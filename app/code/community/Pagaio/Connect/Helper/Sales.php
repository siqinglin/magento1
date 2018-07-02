<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

use Pagaio\ApiClient\Exception\ClientError;

/**
 * Sales Helper
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Helper_Sales extends Mage_Core_Helper_Abstract
{

    /**
     * Manage payment added action
     *
     * In a case a payment is added to the subscription it means:
     * - the payment object is valid (card valid, etc.).
     * - the order is not paid yet: we can't invoice.
     *
     * @see webhook subscription.payment.added
     *
     * @param string $action
     * @param array $payload
     *
     * @return array
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function paymentAdded($action, $payload)
    {
        /** @var Mage_Sales_Model_Order $order */
        $orderId = $payload['data']['attributes']['metadata']['magento_order_id'];
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order || !$order->getId()) {
            return [412, $this->__('Order with subscription ID %s does not exist', $payload['data']['id'])];
        }

        Mage::register('pagaio_current_order', $order);

        // Process the order
        $order->setState(
            Mage_Sales_Model_Order::STATE_PROCESSING,
            true,
            $this->__('Payment added to the subscription.'),
            false
        )->save();
        Mage::dispatchEvent('pagaio_payment_added_order_processing', [
            'order' => $order,
            'webhook_action' => $action,
            'webhook_payload' => $payload,
        ]);

        return [201, 'Order processing.'];
    }

    /**
     * Manage payment success action
     *
     * @see webhook payment.success
     *
     * @param string $action
     * @param array $payload
     *
     * @return array
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function paymentSuccess($action, $payload)
    {
        // If transaction already exists
        $transactionId = $payload['data']['id'];
        $transaction = Mage::getModel('sales/order_payment_transaction')->load($transactionId, 'txn_id');
        if ($transaction->getId()) {
            return [208, $this->__("The transaction has already been processed.")];
        }

        // Fetch the subscription
        $subscriptionId = $payload['data']['relationships']['subscriptions']['data']['id'];

        try {
            $subscription = Mage::getSingleton('pagaio_connect/api_client')->getSubscription()->retrieve($subscriptionId);
        } catch (ClientError $e) {
            return [400, $this->__('Subscription not found')];
        }

        $orderId = $subscription['data']['attributes']['metadata']['magento_order_id'] ?? null;
        if (null === $orderId) {
            return [412, $this->__('Subscription %s is not related to a Magento order.', $subscriptionId)];
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($orderId);

        if (!$order || !$order->getId()) {
            return [412, $this->__('Order with subscription ID %s does not exist', $subscriptionId)];
        }

        Mage::register('pagaio_current_order', $order);

        if ($order->getBaseTotalDue() > 0) { // Just in case…
            Mage::register('pagaio_invoice_first_order', $order);
            $this->_invoiceOrder($order, $payload);
            return [201, 'First order invoiced.'];
        } else {
            Mage::register('pagaio_renew_first_order', $order);
            $this->_renewOrder($order, $payload);
            return [201, 'Create new order.'];
        }
    }

    /**
     * Invoice order for the first time
     *
     * @param $order
     * @param $payload
     *
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _invoiceOrder($order, $payload)
    {
        $transactionId = $payload['data']['id'];
        $order->getPayment()->setTransactionId($transactionId);

        // Create invoice
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
        if (!$invoice->getTotalQty()) {
            Mage::throwException(
                sprintf($this->__('Cannot create an invoice without products for order ID %d'), $order->getId())
            );
        }
        $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
        $invoice->register();

        // Create transaction
        $transactionSave = Mage::getModel('core/resource_transaction')
                               ->addObject($invoice)
                               ->addObject($invoice->getOrder());
        $transactionSave->save();

        // Add payload information in order payment transaction
        $transaction = Mage::getModel('sales/order_payment_transaction')
                           ->setOrderPaymentObject($order->getPayment())
                           ->loadByTxnId($invoice->getTransactionId());

        if ($transaction && $transaction->getId()) {
            $transaction
                ->setAdditionalInformation('payload', $payload)
                ->save();
        }
    }

    /**
     * Renew order and invoice
     *
     * @param $order
     * @param $payload
     */
    protected function _renewOrder($order, $payload)
    {
        // Create new order from the initial one
        $order->setReordered(true);
        $createOrder = Mage::getSingleton('adminhtml/sales_order_create');
        $createOrder->initFromOrder($order);
        $newOrder = $createOrder->createOrder();

        // Save pagaio parent ID
        $newOrder->setPagaioParentOrderId($order->getId());
        $newOrder->getResource()->saveAttribute($newOrder, 'pagaio_parent_order_id');
        $newOrder->setPagaioSubscriptionId($payload['data']['relationships']['subscriptions']['data']['id']);
        $newOrder->getResource()->saveAttribute($newOrder, 'pagaio_subscription_id');

        // Invoice the new order with payload information
        $this->_invoiceOrder($newOrder, $payload);
    }

}
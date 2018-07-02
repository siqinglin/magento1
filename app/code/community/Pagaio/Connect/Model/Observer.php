<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

/**
 * Observer Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Observer
{

    /**
     * Check if cart has contract and limit one contract by cart in quantity one
     *
     * @see event 'sales_quote_collect_totals_after'
     * @param Varien_Event_Observer $observer
     */
    public function checkQuote(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $contractId = Mage::helper('pagaio_connect')->getContractIdFromQuote($quote);

        // Cart is ok if there is no contract
        if (!$contractId) {
            return;
        }

        // Check if cart responding to conditions
        $quoteHasError = false;

        $productsWithContract = 0;

        foreach ($quote->getAllVisibleItems() as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */

            // The contract quantity is not correct
            if ($item->getProduct()->getPagaioContract() === $contractId && (int) $item->getQty() !== 1) {
                $item->setHasError(true)->setMessage(
                    $this->__('You can only have one quantity of the contract.')
                );
                $quoteHasError = true;
                continue;
            }

            if ($item->getProduct()->getPagaioContract()) {
                $productsWithContract++;
            }

            // Another contract in cart
            if ($productsWithContract > 1) {
                $item->setHasError(true)->setMessage(
                    $this->__('You can only have one contract in your cart.')
                );
                $quoteHasError = true;
                continue;
            }

            // Another product in cart
            if ($productsWithContract > 0 && !$item->getProduct()->getPagaioContract()) {
                $item->setHasError(true)->setMessage(
                    $this->__('You cannot add other products if you have a contract in your cart.')
                );
                $quoteHasError = true;
                continue;
            }
        }

        // Add message if quote has error (The user is not able to go to checkout)
        if ($quoteHasError) {
            $quote
                ->setHasError(true)
                ->addMessage(
                    $this->__('You cannot continue your order because you can have only one contract in your cart.')
                );
        }

    }

    /**
     * Enable or not Pagaio method and disable others
     *
     * @see event 'payment_method_is_active'
     *
     * @param Varien_Event_Observer $observer
     */
    public function paymentMethodIsActive(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();

        if (!$quote) {
            return;
        }

        $result = $observer->getEvent()->getResult();
        $methodInstance = $observer->getEvent()->getMethodInstance();
        $contractId = Mage::helper('pagaio_connect')->getContractIdFromQuote($quote);

        // Disable Pagaio if quote has no contract, otherwise keep received is_active value
        if (!$contractId) {
            if ($methodInstance->getCode() === Pagaio_Connect_Model_Payment_Method::PAGAIO_PAYMENT_CODE) {
                $result->isAvailable = false;
            }
            return;
        }

        // Cart has a contract only authorize Pagaio
        if ($methodInstance->getCode() !== Pagaio_Connect_Model_Payment_Method::PAGAIO_PAYMENT_CODE) {
            $result->isAvailable = false;
            return;
        }

        $result->isAvailable = true;
    }


    /**
     * Check if a contract is ordered.
     * It creates the Pagaio customer if needed and the subscription to return the payment URL
     *
     * @see event 'checkout_type_onepage_save_order_after'
     *
     * @param Varien_Event_Observer $observer
     *
     * @return void
     *
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    public function checkIfContractIsOrdered(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getEvent()->getOrder();

        /** @var Mage_Sales_Model_Quote_Payment $quotePayment */
        $quotePayment = $quote->getPayment();

        /** @var Mage_Sales_Model_Order_Payment $orderPayment */
        $orderPayment = $order->getPayment();

        // Get contract ID
        $contractId = Mage::helper('pagaio_connect')->getContractIdFromQuote($quote);

        // Nothing to do if no contract
        if (!$contractId) {
            return;
        }

        // Get Pagaio customer ID
        $pagaioCustomer = $this->_getPagaioCustomer($quote);
        $pagaioCustomerId = $pagaioCustomer['data']['id'];

        // Get chosen payment
        $pagaioPaymentId = $quotePayment->getPagaioId();

        // Create Pagaio subscription
        $subscription = Mage::getSingleton('pagaio_connect/api_client')->getSubscription()->create(
            null,
            $contractId,
            $pagaioPaymentId,
            [],
            $pagaioCustomerId,
            [
                'magento_quote_id' => $quote->getId(),
                'magento_order_id' => $order->getId(),
                'magento_order_increment_id' => $order->getIncrementId(),
            ]
        );

        // TODO XXX Manage errors

        // Keep trace of the subscription's ID
        $subscriptionId = $subscription['data']['id'];
        $order->setPagaioSubscriptionId($subscriptionId);
        $order->getResource()->saveAttribute($order, 'pagaio_subscription_id');

        // Payment information
        $paymentUrl = $subscription['data']['attributes']['payment_url'];

        // Set Redirect payment URL
        $quote->getPayment()->getMethodInstance()->setOrderPlaceRedirectUrl($paymentUrl);
    }

    /**
     * Translation function
     *
     * @param $text
     * @return string
     */
    public function __($text)
    {
        return Mage::helper('pagaio_connect')->__($text);
    }

    /**
     * Get Pagaio customer ID, the customer is created if not exists
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return array
     *
     * @throws Exception
     */
    protected function _getPagaioCustomer($quote)
    {
        /** @var Mage_Customer_Model_Customer $customer */
        $customer = $quote->getCustomer();

        // Return Pagaio customer ID if is set
        if ($pagaioCustomerId = $customer->getPagaioCustomerId()) {
            // TODO XXX Manage errors
            return Mage::getSingleton('pagaio_connect/api_client')->getCustomer()->retrieve($pagaioCustomerId);
        }

        // Create Pagaio customer
        $customerData = [
            'fullname' => $customer->getName(),
            'email' => $customer->getEmail(),
            'metadata' => [
                'magento_id' => $customer->getId()
            ],
        ];
        $pagaioCustomer = Mage::getSingleton('pagaio_connect/api_client')->getCustomer()->create(null, $customerData);

        // TODO XXX Manage errors

        // Save attribute
        $customer->setPagaioCustomerId($pagaioCustomer['data']['id']);
        $customer->getResource()->saveAttribute($customer, 'pagaio_customer_id');

        return $pagaioCustomer;
    }

    /**
     * Prepare payment information to display in backend
     *
     * @see event 'payment_info_block_prepare_specific_information'
     * @param $observer
     */
    public function paymentInfoBlockPrepareSpecificInformation($observer)
    {
        if ($observer->getEvent()->getBlock()->getIsSecureMode()) {
            return;
        }

        $payment = $observer->getEvent()->getPayment();
        $transport = $observer->getEvent()->getTransport();
        $helper = Mage::helper('pagaio_connect');

        foreach ($payment->getAdditionalInformation() as $key => $value) {
            $transport->setData($helper->__('payment_key_' . $key), $helper->__($value));
        }

        // Add Pagaio parent order info if exists
        if ($pagaioParentOrderId = $payment->getOrder()->getPagaioParentOrderId()) {
            $pagaioParentOrder = Mage::getModel('sales/order')
                ->getCollection()
                ->addAttributeToFilter('entity_id', $pagaioParentOrderId)
                ->getFirstItem();

            // Manage case if parent order has been deleted
            if (!$pagaioParentOrder || !$pagaioParentOrder->getId()) {
                $message = $helper->__('Order does not exist anymore');
                $transport->setData($helper->__('payment_key_parent_order_increment_id'), $message);
                $transport->setData($helper->__('payment_key_parent_order_id'), $pagaioParentOrderId);
                return;
            }

            // Add parent order info
            $transport->setData($helper->__('payment_key_parent_order_increment_id'), $pagaioParentOrder->getIncrementId());
            $transport->setData($helper->__('payment_key_parent_order_id'), $pagaioParentOrder->getId());
        }
    }
}
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
 * Observer_Webhook Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Observer_Webhook extends Mage_Core_Model_Abstract
{

    /**
     * Process all webhooks
     */
    public function processWebhook(Varien_Event_Observer $observer)
    {
        switch ($observer->getAction()) {
            case 'subscription.payment.added':
                list($code, $message) = Mage::helper('pagaio_connect/sales')->paymentAdded($observer->getAction(), $observer->getPayload());
                break;
            case 'payment.success':
                list($code, $message) = Mage::helper('pagaio_connect/sales')->paymentSuccess($observer->getAction(), $observer->getPayload());
                break;
        }

        Mage::dispatchEvent('pagaio_webhook_received_' . str_replace('.', '_', $observer->getAction()), [
            'payload' => $observer->getPayload(),
            'request' => $observer->getRequest(),
            'response' => $observer->getResponse(),
        ]);
        
        if (isset($code)) {
            /** @var $response Mage_Core_Controller_Response_Http */
            $response = $observer->getResponse();
            $response->setHttpResponseCode($code);
            if (isset($message) && !empty($message)) {
                $response->appendBody($message . "\n", $observer->getAction());
            }
        }
    }

}
<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

use Pagaio\ApiClient\Exception\PagaioApiClientException;

/**
 * Adminhtml_Pagaio_Webhook Controller
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Adminhtml_Pagaio_WebhookController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Generate webhooks
     */
    public function generateAction()
    {
        $store = Mage::app()->getStore(Mage_Core_Model_Store::DEFAULT_CODE);
        $webhookResource = Mage::getSingleton('pagaio_connect/api_client')->getWebhook();
        $webhooks = Mage::getSingleton('pagaio_connect/resource_webhook')->getAll();

        foreach ($webhooks as $webhookIdentifier => $webhook) {
            if ($webhook['uuid'] === false) {
                // Create the webhook
                try {
                    $result = $webhookResource->create(
                        null,
                        [
                            'endpoint' => $store->getUrl('pagaio_connect/webhook/raw'),
                            'actions' => [$webhook['action']],
                            'label' => 'Magento "' . $store->getName() . '" / ' . $webhook['label'],
                            'metadata' => [
                                'magento' => 1,
                            ],
                        ]
                    );
                    Mage::getSingleton('pagaio_connect/config')->setWebhookConfig(
                        $webhookIdentifier,
                        $result['data']['id'],
                        $result['data']['attributes']['secret']
                    );
                } catch (PagaioApiClientException $e) {
                    Mage::getSingleton('pagaio_connect/logger')->exception($e);
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('pagaio_connect')->__("It seems that an error occurred during the registration of the webhooks.")
                    );
                }
            }
        }

        return $this->_redirectReferer();
    }

    /**
     * Delete a webhook
     */
    public function deleteAction()
    {
        function error($msg = "Webhook not found.") {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('pagaio_connect')->__($msg)
            );
            return $this->_redirectReferer();
        }

        // Get the webhook
        if (!$id = $this->getRequest()->getParam('id')) {
            return error();
        }

        $webhookResource = Mage::getSingleton('pagaio_connect/api_client')->getWebhook();

        try {
            $webhookResource->retrieve($id);
        } catch (PagaioApiClientException $e) {
            if ($e->getCode() === 404) {
                return error();
            }

            Mage::getSingleton('pagaio_connect/logger')->exception($e);
            return error("An error occurred.");
        }

        $webhookResource->delete($id);
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('pagaio_connect')->__("Webhook deleted successfully.")
        );
        return $this->_redirectReferer();
    }

    /**
     * Is allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('pagaio');
    }

}
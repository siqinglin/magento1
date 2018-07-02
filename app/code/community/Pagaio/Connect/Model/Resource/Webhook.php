<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

require_once(__DIR__ . '/../../lib/Pagaio/static_autoload.php');

use Pagaio\ApiClient\Exception\PagaioApiClientException;

/**
 * Resource_Webhook Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Resource_Webhook extends Mage_Core_Model_Abstract
{

    protected $webhooks = [];

    /**
     * @return array
     */
    public function getFlatWebhooks()
    {
        return [
            'subscription_created' => [
                'label' => "Subscription created",
                'action' => 'subscription.created',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'subscription_closed' => [
                'label' => "Subscription closed",
                'action' => 'subscription.closed',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'subscription_payment_added' => [
                'label' => "Payment added to a subscription",
                'action' => 'subscription.payment.added',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'payment_scheduled' => [
                'label' => "Payment scheduled",
                'action' => 'payment.scheduled',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'payment_processing' => [
                'label' => "Payment processing",
                'action' => 'payment.processing',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'payment_waiting' => [
                'label' => "Payment waiting",
                'action' => 'payment.waiting',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'payment_success' => [
                'label' => "Payment success",
                'action' => 'payment.success',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
            'payment_failure' => [
                'label' => "Payment failure",
                'action' => 'payment.failure',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],

//            'contract_created' => [
//                'label' => "Contract created",
//                'action' => 'contract.created',
//                'uuid' => false,
//                'secret' => null,
//                'webhook' => null,
//            ],
            'customer_created' => [
                'label' => "Customer created",
                'action' => 'customer.created',
                'uuid' => false,
                'secret' => null,
                'webhook' => null,
            ],
//            'amendment_created' => [
//                'label' => "Amendment created",
//                'action' => 'amendment.created',
//                'uuid' => false,
//                'secret' => null,
//                'webhook' => null,
//            ],
        ];
    }

    /**
     * Retrieve all webhooks
     *
     * @return array
     */
    public function getAll()
    {
        $webhookResource = Mage::getSingleton('pagaio_connect/api_client')->getWebhook();

        if (empty($this->webhooks)) {
            $this->webhooks = $this->getFlatWebhooks();

            $registeredWebhooks = $webhookResource->retrieveAll();

            $config = Mage::getSingleton('pagaio_connect/config');

            foreach ($registeredWebhooks['data'] as $webhook) {

                if (isset($webhook['attributes']['metadata']['magento'])) {

                    // Loop on the actions
                    foreach ($webhook['attributes']['actions'] as $action) {
                        foreach ($this->webhooks as $wbId => & $wh) {
                            if ($wh['action'] === $action) {
                                $wh['uuid'] = $webhook['id'];
                                $wh['secret'] = $webhook['attributes']['secret'];
                                $wh['webhook'] = $webhook;

                                $webhookUuid = $config->getWebhookUuid($wbId);

                                if ($webhookUuid !== $webhook['id']) {
                                    // Change value in config
                                    $config->setWebhookConfig(
                                        $wbId,
                                        $webhook['id'],
                                        $webhook['attributes']['secret']
                                    );
                                }
                            }
                        }
                    }

                }
            }

            // Clean deleted webhooks
            foreach ($this->webhooks as $webhookIdentifier => & $webhook) {
                if ($webhook['uuid'] === false) {
                    $webhookUuid = $config->getWebhookUuid($webhookIdentifier);
                    if ($webhookUuid) {
                        Mage::app()->getConfig()->deleteConfig(
                            'pagaio/webhook/' . $webhookIdentifier
                        );
                    }
                }
            }
        }
        return $this->webhooks;
    }

}
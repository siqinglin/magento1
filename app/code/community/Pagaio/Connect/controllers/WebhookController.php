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
 * Pagaio Controller
 * @package Pagaio_Connect
 */
class Pagaio_Connect_WebhookController extends Mage_Core_Controller_Front_Action
{

    /**
     * Receive a webhook
     */
    public function rawAction()
    {
        $logger = Mage::getSingleton('pagaio_connect/logger');
        
        // Retrieve the payload
        $payload = json_decode($this->getRequest()->getRawBody(), true);
        
        if ($payload === null) {
            $logger->warning('[WEBHOOK CALL] Payload malformed or empty');
            $logger->info($payload);
            $this->_stop('404 Not Found');
            return;
        }

        if (!isset($payload['action'])) {
            $logger->warning('[WEBHOOK CALL] No action provided');
            $logger->info($payload);
            $this->_stop('404 Not Found');
            return;
        }

        // Keep trace if debug mode is on
        $logger->debug($payload);

        // Check signature
        if (!$signature = $this->checkRequestSignature($this->getRequest(), $payload)) {
            $logger->warning('[WEBHOOK CALL] Bad signature');
            $this->_stop('412 Precondition Failed', 'Bad signature, see logs');
            return;
        }

        $response = $this->getResponse();

        try {
            $actionType = $payload['action'];
            Mage::dispatchEvent('pagaio_webhook_received', [
                'action' => $actionType,
                'payload' => $payload,
                'request' => $this->getRequest(),
                'signature' => $signature,
                'response' => $response,
            ]);

        } catch (Exception $e) {
            $logger->exception($e);
            $response
                ->setHttpResponseCode(400)
                ->appendBody($e->getMessage())
            ;
        }
    }

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param array $payload
     *
     * @return false|string
     */
    private function checkRequestSignature(Mage_Core_Controller_Request_Http $request, $payload)
    {
        $webhooks = Mage::getSingleton('pagaio_connect/resource_webhook')->getFlatWebhooks();
        foreach ($webhooks as $webhookIdentifier => $webhook) {
            if ($webhook['action'] === $payload['action']) {
                $secret = Mage::getSingleton('pagaio_connect/config')->getWebhookSecret($webhookIdentifier);
                if ($secret === null) {
                    Mage::getSingleton('pagaio_connect/logger')->warning(sprintf(
                        "Webhook %s received calls, but it is not configured [no secret found].",
                        $webhookIdentifier
                    ));
                    return false;
                }

                try {
                    $signature = $request->getHeader('X-Pagaio-Signature');
                    Mage::getSingleton('pagaio_connect/logger')->debug(sprintf('Signature: %s', $signature));
                } catch (\Exception $e) {
                    $signature = null;
                }

                $hash = hash_hmac('sha256', $this->getRequest()->getRawBody(), $secret);
                if ($signature === $hash) {
                    return $signature;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param string $httpCode
     * @param null|string $x Details
     */
    protected function _stop($httpCode, $x = null)
    {
        header(sprintf('HTTP/1.1 %s', $httpCode));
        if (null !== $x) {
            header(sprintf('X-Details: %s', $x));
        }
        exit;
    }

}
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

use Pagaio\ApiClient\Exception\PagaioApiClientException as ClientException;
use Pagaio\ApiClient\Exception\ClientError;
use Pagaio\ApiClient\Client;

/**
 * Api_Client Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Api_Client
{
    /**
     * @var \Pagaio\ApiClient\Client
     */
    private $client;

    /**
     * @var Pagaio_Connect_Model_Logger
     */
    private $logger;

    /**
     * Pagaio_Connect_Model_Api_Client constructor
     */
    public function __construct()
    {
        $apiKey = Mage::getSingleton('pagaio_connect/config')->getApiKey();
        $this->client = new Client($apiKey);
        $this->logger = Mage::getSingleton('pagaio_connect/logger');
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Info
     */
    public function getInfo()
    {
        return $this->client->getInfo();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Webhook
     */
    public function getWebhook()
    {
        return $this->client->getWebhook();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Contract
     */
    public function getContract()
    {
        return $this->client->getContract();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Customer
     */
    public function getCustomer()
    {
        return $this->client->getCustomer();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Subscription
     */
    public function getSubscription()
    {
        return $this->client->getSubscription();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Amendment
     */
    public function getAmendment()
    {
        return $this->client->getAmendment();
    }

    /**
     * @return \Pagaio\ApiClient\Resource\Payment
     */
    public function getPayment()
    {
        return $this->client->getPayment();
    }

    /**
     * Return all payment methods
     *
     * @return array
     */
    public function getAllPaymentMethods()
    {
        try {
            $payment = $this->client->getPayment()->retrieveAll();
        } catch (ClientError $e) {
            $payment = ['error' => $e->getCode()];
        }

        return $payment;
    }

}
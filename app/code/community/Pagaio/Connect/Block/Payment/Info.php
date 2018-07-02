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
 * Payment_Info Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Payment_Info extends Mage_Payment_Block_Info
{

    /**
     * @var array
     */
    private $subscription;

    /**
     * @var array
     */
    private $payment;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('pagaio_connect/payment/info.phtml');
    }

    /**
     * @return mixed
     */
    public function getSubscription()
    {
        if (null === $this->subscription) {
            $this->subscription = Mage::getSingleton('pagaio_connect/api_client')->getSubscription()->retrieve(
                $this->getSubscriptionId()
            );
            // TODO XXX Manage errors
        }
        return $this->subscription;
    }

    /**
     * @return string
     */
    public function getSubscriptionId()
    {
        return $this->getInfo()->getOrder()->getPagaioSubscriptionId();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPaymentPageUrl()
    {
        return $this->helper('pagaio_connect/subscription')->getPaymentPageUrl(
            $this->getSubscription()
        );
    }

    public function getPagaioPayment()
    {
        if (null === $this->payment) {
            $this->payment = Mage::getSingleton('pagaio_connect/api_client')->getPayment()->retrieve(
                $this->getSubscription()['data']['relationships']['payments']['data']['id']
            );
            // TODO XXX Manage errors
        }
        return $this->payment;
    }

}
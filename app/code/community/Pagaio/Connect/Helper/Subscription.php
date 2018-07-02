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
 * Subscription Helper
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Helper_Subscription extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieve contract details from subscription
     *
     * @param array $subscription
     *
     * @return void
     * @throws Exception
     */
    public function getContract(array $subscription)
    {
        $this->_checkSubscriptionParameter($subscription);
        foreach ($subscription['included'] as $included) {
            if ($included['type'] === 'contracts') {
                return $included;
            }
        }
    }

    /**
     * @param array $subscription
     *
     * @return mixed
     * @throws Exception
     */
    public function getPaymentPageUrl(array $subscription)
    {
        $this->_checkSubscriptionParameter($subscription);
        return $subscription['data']['attributes']['payment_url'];
    }

    /**
     * @param $subscription
     *
     * @throws Exception
     */
    protected function _checkSubscriptionParameter($subscription)
    {
        if ($subscription['data']['type'] !== 'subscriptions') {
            throw new \Exception('Please use a subscription as first parameter.');
        }
    }

// Monsieur Biz Tag NEW_METHOD

}
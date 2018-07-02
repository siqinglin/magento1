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
 * Customer Helper
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Helper_Customer extends Mage_Core_Helper_Abstract
{

// Monsieur Biz Tag NEW_CONST

// Monsieur Biz Tag NEW_VAR

    /**
     * @param Mage_Customer_Model_Customer $customer
     *
     * @return array
     * @throws Exception
     */
    public function getCustomer(Mage_Customer_Model_Customer $customer)
    {
        if (!$customer->getPagaioCustomerId()) {
            throw new \Exception("This customer has no pagaio_customer_id.");
        }

        return Mage::getSingleton('pagaio_connect/api_client')->getCustomer()->retrieve(
            $customer->getPagaioCustomerId()
        );
    }

// Monsieur Biz Tag NEW_METHOD

}
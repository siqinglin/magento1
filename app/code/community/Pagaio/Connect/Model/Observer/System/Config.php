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
 * Observer_System_Config Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Observer_System_Config extends Mage_Core_Model_Abstract
{

    /**
     * @see event `admin_system_config_changed_section_pagaio`
     */
    public function saveConfig(Varien_Event_Observer $observer)
    {
        $post = Mage::app()->getRequest()->getPost();
        $fields = $post['groups']['general']['fields'];
        
        $websiteId = $observer->getWebsite();
        $storeId = $observer->getStore();

        if (null === $websiteId) {
            $scope = 'default';
            $scopeId = 0;
        } elseif (null === $storeId) {
            $scope = 'websites';
            $scopeId = Mage::app()->getWebsite($websiteId)->getId();
        } else {
            $scope = 'stores';
            $store = Mage::app()->getStore($storeId);
            $scopeId = $store->getId();
        }

        // Get details and save subdomain
        if (isset($fields['active']['inherit']) && $fields['active']['inherit'] === "1") {
            $isActive = isset($store) ? $store->getConfig('pagaio/general/active') : Mage::getStoreConfig('pagaio/general/active');
        } else {
            $isActive = $fields['active']['value'];
        }
        if ($isActive) {
            $me = Mage::getSingleton('pagaio_connect/api_client')->getInfo()->getInfo();
            $subdomain = $me['account']['subdomain'];
            Mage::app()->getConfig()->saveConfig('pagaio/general/subdomain', $subdomain, $scope, $scopeId);
        }
    }

}
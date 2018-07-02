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
 * Config Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Config
{

    /**
     * @return bool
     */
    public function isActive()
    {
        return (bool) Mage::getStoreConfigFlag('pagaio/general/active');
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return (bool) Mage::getStoreConfigFlag('pagaio/general/debug_mode');
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return (string) Mage::getStoreConfig('pagaio/general/api_key');
    }

    /**
     * @param string $webhookIdentifier
     *
     * @return array
     */
    protected function _getWebhookConfig($webhookIdentifier)
    {
        $config = (string) Mage::getStoreConfig('pagaio/webhook/' . $webhookIdentifier);
        $values = explode('|', $config);
        if (count($values) !== 2) {
            return [null, null];
        }
        return $values;
    }

    /**
     * @param string $webhookIdentifier
     * @param string $uuid
     * @param string $secret
     *
     * @return $this
     */
    public function setWebhookConfig($webhookIdentifier, $uuid, $secret)
    {
        $value = sprintf('%s|%s', $uuid, $secret);
        Mage::app()->getConfig()->saveConfig(
            'pagaio/webhook/' . $webhookIdentifier,
            $value
        );

        return $this;
    }

    /**
     * @param string $webhookIdentifier
     *
     * @return string
     */
    public function getWebhookUuid($webhookIdentifier)
    {
        list($uuid, $secret) = $this->_getWebhookConfig($webhookIdentifier);
        return $uuid;
    }

    /**
     * @param string $webhookIdentifier
     *
     * @return string
     */
    public function getWebhookSecret($webhookIdentifier)
    {
        list($uuid, $secret) = $this->_getWebhookConfig($webhookIdentifier);
        return $secret;
    }

}
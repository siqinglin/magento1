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
 * Data Helper
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Is the module active in BO
     *
     * @return bool
     */
    public function isActive()
    {
        $isActive = Mage::getSingleton('pagaio_connect/config')->isActive()
            && strlen(trim(Mage::getSingleton('pagaio_connect/config')->getApiKey())) > 1;

        $transport = new Varien_Object([
            'origin' => [
                'is_active' => $isActive,
            ],
            'is_active' => $isActive
        ]);
        Mage::dispatchEvent('pagaio_connect_is_active', ['transport' => $transport]);

        return $transport->getIsActive();
    }

    /**
     * Transform an API link to an APP link
     *
     * @param $apiLink
     *
     * @return mixed
     */
    public function getAppLink($apiLink)
    {
        return str_replace('https://api.pagaio.com', 'https://app.pagaio.com', $apiLink);
    }

    /**
     * @param string… sprintf format of the URL
     * @return string
     */
    public function getAppUrl()
    {
        return 'https://app.pagaio.com/' . call_user_func_array('sprintf', func_get_args());
    }

    /**
     * Retrieve Contract ID from Quote using items
     *
     * @param Mage_Sales_Model_Quote $quote
     *
     * @return null
     */
    public function getContractIdFromQuote(Mage_Sales_Model_Quote $quote)
    {
        $contractId = null;
        foreach ($quote->getAllVisibleItems() as $item) {
            /** @var Mage_Sales_Model_Quote_Item $item */
            if ($contractId = $item->getProduct()->getPagaioContract()) {
                break;
            }
        }
        return $contractId;
    }

}
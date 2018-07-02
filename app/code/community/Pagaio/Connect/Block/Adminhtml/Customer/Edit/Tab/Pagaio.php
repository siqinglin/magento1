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
 * Adminhtml_Customer_Edit_Tab_Pagaio Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Adminhtml_Customer_Edit_Tab_Pagaio
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function _construct()
    {
        if ($this->helper('pagaio_connect')->isActive()) {
            $this->setTemplate('pagaio_connect/customer.phtml');
        }
        parent::_construct();
    }

    /**
     * Defines after which tab, this tab should be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'account';
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Pagaio');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Pagaio');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return (bool) $customer->getId();
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return !$this->getCustomer()->hasPagaioCustomerId();
    }

    /**
     * @return boolean
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * @return false|array
     */
    public function getPagaioCustomer()
    {
        if (!($customerUuid = $this->getCustomer()->getPagaioCustomerId())) {
            return false;
        }
        $customerResource = Mage::getSingleton('pagaio_connect/api_client')->getCustomer();
        try {
            $customer = $customerResource->retrieve($customerUuid);
        } catch (PagaioApiClientException $e) {
            return false;
        }
        return $customer;
    }

}
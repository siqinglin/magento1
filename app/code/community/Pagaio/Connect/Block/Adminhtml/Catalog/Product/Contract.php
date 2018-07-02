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
 * Adminhtml_Catalog_Product_Contract Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Adminhtml_Catalog_Product_Contract extends Varien_Data_Form_Element_Select
{
    /**
     * Append contract information to the select
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $blockHtml = Mage::app()
            ->getLayout()
            ->createBlock('core/template')
            ->setTemplate('pagaio_connect/contract.phtml')
            ->toHtml()
        ;

        return parent::getAfterElementHtml() . $blockHtml;
    }
}
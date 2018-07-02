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
 * Attribute_Source_Contract Model
 *
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Attribute_Source_Contract extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Retrieve All options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (null == $this->_options) {
            $this->_options[''] = Mage::helper('pagaio_connect')->__('No contract');

            $contractResource = Mage::getSingleton('pagaio_connect/api_client')->getContract();

            try {
                $contracts = $contractResource->retrieveAll();
                foreach ($contracts['data'] as $contract) {
                    $this->_options[$contract['id']] = $contract['attributes']['title'];
                }
            } catch (PagaioApiClientException $e) {
                throw $e;
            }
        }

        return $this->_options;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = [];
        $attributeCode = $this->getAttribute()->getAttributeCode();

        if (Mage::helper('core')->useDbCompatibleMode()) {
            $columns[$attributeCode] = [
                'type' => 'varchar(255)',
                'unsigned' => false,
                'is_null' => true,
                'default' => null,
                'extra' => null,
            ];
        } else {
            $columns[$attributeCode] = [
                'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length' => 255,
                'unsigned' => false,
                'nullable' => true,
                'default' => null,
                'extra' => null,
                'comment' => $attributeCode . ' column',
            ];
        }

        return $columns;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     *
     * @return Varien_Db_Select
     */
    public function getFlatUpdateSelect($store = null)
    {
        return Mage::getResourceModel('eav/entity_attribute')
           ->getFlatUpdateSelect($this->getAttribute(), $store)
        ;
    }
}
<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

try {
    $installer = $this;
    $installer->startSetup();

    // Create product attribute to link contract

    /* @var $setup Mage_Catalog_Model_Resource_Setup */
    $setup = Mage::getResourceModel('catalog/setup', 'core_setup');

    $setup->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'pagaio_contract', array(
        'label'                      => 'Contract',
        'group'                      => 'Pagaio',
        'input'                      => 'select',
        'type'                       => 'varchar',
        'source'                     => 'pagaio_connect/attribute_source_contract',
        'input_renderer'             => 'pagaio_connect/adminhtml_catalog_product_contract',
        'global'                     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'sort_order'                 => '10',
        'visible'                    => true,
        'required'                   => false,
        'comparable'                 => false,
        'filterable'                 => false,
        'is_configurable'            => false,
        'is_html_allowed_on_front'   => false,
        'searchable'                 => false,
        'unique'                     => false,
        'used_for_sort_by'           => false,
        'used_in_product_listing'    => false,
        'user_defined'               => false,
        'visible_on_front'           => false,
        'visible_in_advanced_search' => false,
        'wysiwyg_enabled'            => false,
    ));

    $setup->endSetup();

} catch (Exception $e) {
    // Silence is golden
    //throw $e;
}

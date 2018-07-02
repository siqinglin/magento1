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

    /* @var $setup Mage_Sales_Model_Resource_Setup */
    $setup = Mage::getResourceModel('sales/setup', 'core_setup');

    // Create order column to save Pagaio parent order ID
    $setup->addAttribute(Mage_Sales_Model_Order::ENTITY, 'pagaio_parent_order_id', [
        'type'     => 'int',
        'length'   => 10,
        'unsigned' => true,
    ]);

    $installer->endSetup();

} catch (Exception $e) {
    // Silence is golden
}

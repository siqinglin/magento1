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

    /* @var $setup Mage_Customer_Model_Resource_Setup */
    $setup = Mage::getResourceModel('customer/setup', 'core_setup');

    // Create customer attribute to save Pagaio customer ID
    $setup->addAttribute('customer', 'pagaio_customer_id', array(
        'label'    => 'Pagaio ID',
        'group'    => 'Pagaio',
        'visible'  => true,
        'required' => false,
        'input'    => 'text',
        'type'     => 'varchar',
        'position' => 1,
    ));

    $installer->endSetup();

    $setup->endSetup();

} catch (Exception $e) {
    // Silence is golden
    //throw $e;
}

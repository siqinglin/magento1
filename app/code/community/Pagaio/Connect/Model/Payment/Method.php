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
 * Payment_Method Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Payment_Method extends Mage_Payment_Model_Method_Abstract
{

    const PAGAIO_PAYMENT_CODE = 'pagaio';

    protected $_code = self::PAGAIO_PAYMENT_CODE;
    protected $_formBlockType = 'pagaio_connect/payment_form';
    protected $_infoBlockType = 'pagaio_connect/payment_info';

    protected $_isInitializeNeeded      = true;
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;

    protected $_isGateway                   = true;
    protected $_canAuthorize                = true;
    protected $_canCapture                  = true;
    protected $_canCapturePartial           = false;

}
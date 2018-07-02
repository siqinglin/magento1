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
 * Payment_Form Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Payment_Form extends Mage_Core_Block_Template
{
    /**
     * Set template file for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('pagaio_connect/payment/form.phtml');
    }

    /**
     * Get available payment methods in Pagaio
     *
     * @return string
     */
    public function getPaymentMethods()
    {
        $paymentMethods = Mage::getSingleton('pagaio_connect/api_client')->getAllPaymentMethods(true);
        
        // Error in API call
        if (isset($paymentMethods['error'])) {
            $paymentMethods['error_message'] = sprintf(
                Mage::helper('pagaio_connect')->__('Error during retrieving payment methods (Error code %d)'),
                $paymentMethods['error']
            );
        }

        $transport = new Varien_Object([
            'payment_methods' => $paymentMethods,
        ]);
        Mage::dispatchEvent('pagaio_connect_payment_methods', [
            'transport' => $transport,
        ]);

        return $transport->getPaymentMethods();
    }
}
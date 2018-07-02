<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

require_once(__DIR__ . '/../../../../lib/Pagaio/static_autoload.php');

use Pagaio\ApiClient\Exception\PagaioApiClientException;

/**
 * Adminhtml_System_Config_Info Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Adminhtml_System_Config_Info extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        // If not active, be silent
        if (!Mage::helper('pagaio_connect')->isActive()) {
            return $this->_decorateRowHtml($element, '<td><span id="' . $element->getHtmlId() . '"></span></td>');
        }

        return $this->_decorateRowHtml($element, '<td colspan="4" class="label">' . $this->_getInfo($element) . '</td>');

    }

    /**
     * Retrieve and format current info to display
     *
     * @return string
     */
    protected function _getInfo(Varien_Data_Form_Element_Abstract $element)
    {
        // Get API Info
        $client = Mage::getSingleton('pagaio_connect/api_client');
        $infoResource = $client->getInfo();

        try {
            $information = $infoResource->getInfo();
        } catch (PagaioApiClientException $e) {
            switch ($e->getCode()) {
                case 401: // Unauthorized
                    return $this->getLayout()->createBlock('core/messages')->addError(
                        Mage::helper('pagaio_connect')->__('It seems that the API key you provided is not correct.')
                    )->toHtml();
                    break;
                default:
                    Mage::getSingleton('pagaio_connect/logger')->exception($e);
                    throw $e;
            }
        }

        // Manage mode display
        $currentMode = Mage::helper('pagaio_connect')->__($information['live_mode'] ? 'live' : 'test');
        $colorMode = $information['live_mode'] ? 'green' : 'orange';
        $currentMode = sprintf(
            '<span style="color:%s;font-weight:bold;text-transform:uppercase">%s</span>',
            $colorMode,
            $currentMode
        );

        // Manage date display
        $createdAt = Mage::app()->getLocale()->date($information['account']['created_at']);

        // Generate HTML to display
        $helper = Mage::helper('pagaio_connect');
        return <<<HTML
<ul id="{$element->getHtmlId()}">
    <li style="font-weight:bold;">{$helper->__('Your information')}</li>
    <li>{$helper->__('Mode: %s', $currentMode)}</li>
    <li>{$helper->__('ID: %s', $information['account']['id'])}</li>
    <li>{$helper->__('Subdomain: %s', $information['account']['subdomain'])}</li>
    <li>{$helper->__('Company: %s', $information['account']['company'])}</li>
    <li>{$helper->__('Creation Date: %s', $createdAt)}</li>
</ul>
HTML;
    }
}
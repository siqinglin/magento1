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

use Pagaio\ApiClient\Exception\ClientError;

/**
 * Adminhtml_System_Config_Webhook Block
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Block_Adminhtml_System_Config_Webhook
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Some color for displayed message
     */
    const TEXT_ERROR_COLOR = 'red';

    /**
     * Render config element
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $helper = Mage::helper('pagaio_connect');

        // If not active, be silent
        if (!Mage::helper('pagaio_connect')->isActive()) {
            return $this->_decorateRowHtml(
                $element,
                '<td colspan="4"><span id="' . $element->getHtmlId() . '">'
                . $helper->__('Please set an API key and/or save the configuration.')
                . '</span></td>'
            );
        }

        try {
            $webhooks = Mage::getSingleton('pagaio_connect/resource_webhook')->getAll();
        } catch (ClientError $e) {
            if ($e->getCode() === 401) {
                return $this->_decorateRowHtml(
                    $element,
                    '<td colspan="4"><span id="' . $element->getHtmlId() . '">'
                    . $helper->__('Please set an API key and/or save the configuration.')
                    . '</span></td>'
                );
            }
            Mage::getSingleton('pagaio_connect/logger')->exception($e);
            return '';
        }

        $noWebhookSet = true;
        $allWebhookSet = true;
        $rowsHtml = '';
        
        foreach ($webhooks as $webhook) {
            $deleteLink = $this->getUrl('adminhtml/pagaio_webhook/delete', ['id' => $webhook['uuid']]);
            $rowsHtml .= PHP_EOL
                . '<tr>'
                . '  <td>' . $helper->__($webhook['label']) . '</td>'
                . '  <td>' . ($webhook['uuid'] === false ? '🚫' : '✅') . '</td>'
                . '  <td>' . ($webhook['uuid'] === false ? '' : sprintf('<a href="%s">%s</a>', $deleteLink, $helper->__("Remove"))) . '</td>'
                . '</tr>'
            ;
            if ($webhook['uuid'] !== false) {
                $noWebhookSet = false;
            } else {
                $allWebhookSet = false;
            }
        }

        $messageHtml = '';

        // No webhook set, add button to generate it
        if ($noWebhookSet) {
            $message = $this->__('Please generate and associate webhooks to the platform.');
            $messageHtml .= $this->getLayout()->createBlock('core/messages')
                ->addNotice($message)
                ->toHtml();
        } else {
            $messageHtml .= <<<HTML
<div class="grid">
    <table class="data" cellspacing="0">
        <thead>
            <tr class="headings">
                <th>{$helper->__("Webhook")}</th>
                <th>{$helper->__("Status")}</th>
                <th>{$helper->__("Actions")}</th>
            </tr>
        </thead>
        <tbody>{$rowsHtml}</tbody>
    </table>
</div>
HTML;
        }

        if ($allWebhookSet) {
            $generateButtonHtml = '';
        } else {
            $generateButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
               ->setType('button')
               ->setLabel($this->__('Generate Webhooks'))
               ->setId($element->getHtmlId())
               ->setOnClick(
                   sprintf(
                       "setLocation('%s');",
                       Mage::helper('adminhtml')->getUrl('adminhtml/pagaio_webhook/generate')
                   )
               )
               ->toHtml()
            ;
        }

        return sprintf(
            '<td id="row_' . $element->getHtmlId() . '" colspan="4" class="label"><p>%s</p><p>%s</p></td>',
            $messageHtml,
            $generateButtonHtml
        );

    }
}
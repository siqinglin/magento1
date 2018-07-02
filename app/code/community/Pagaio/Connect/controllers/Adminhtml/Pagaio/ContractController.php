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
 * Adminhtml_Pagaio_Contract Controller
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Adminhtml_Pagaio_ContractController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Get contract's details via ajax request
     */
    public function detailsAction()
    {
        // Request must be ajax
        if (!$this->getRequest()->isAjax()) {
            $this->getResponse()->setHttpResponseCode(404);
            return;
        }

        // Retrieve contract and clauses
        $contractId = $this->getRequest()->getParam('id');
        $contractResource = Mage::getSingleton('pagaio_connect/api_client')->getContract();

        try {
            $contract = $contractResource->retrieve($contractId);
        } catch (PagaioApiClientException $e) {
            $this->getResponse()->setHeader('Content-type', 'application/json');
            $this->getResponse()->setBody(json_encode([
                'error' => true,
                'message' => $e->getMessage(),
            ]));
            return;
        }

        // Format date
        $date = Mage::app()->getLocale()->date($contract['data']['attributes']['created_at']);
        $contract['data']['attributes']['created_at_formatted'] = (string) $date;

        // Return response in JSON
        $this->getResponse()->setHeader('Content-type', 'application/json');
        $this->getResponse()->setBody(json_encode([
            'success' => true,
            'contract' => $contract,
        ]));
    }

    /**
     * Is allowed?
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/products');
    }

}
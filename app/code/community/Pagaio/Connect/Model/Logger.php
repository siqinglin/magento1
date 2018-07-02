<?php
/**
 * This file is part of Pagaio_Connect for Magento.
 *
 * @license MIT
 * @category Pagaio
 * @package Pagaio_Connect
 * @copyright Copyright (c) 2018 Pagaio (https://pagaio.com/)
 */

require_once(__DIR__ . '/../lib/Pagaio/static_autoload.php');

use Pagaio\ApiClient\Exception\PagaioApiClientException as ClientException;

/**
 * Logger Model
 * @package Pagaio_Connect
 */
class Pagaio_Connect_Model_Logger
{

    const PAGAIO_LOG_FILE = 'pagaio.log';

    /**
     * Log exception
     *
     * @param ClientException $e
     */
    public function exception(ClientException $e)
    {
        $this->_log(PHP_EOL . (string) $e, Zend_Log::ERR);
    }

    /**
     * Log information
     *
     * @param mixed $data
     */
    public function info($data)
    {
        $this->_log($data, Zend_Log::INFO);
    }

    /**
     * Log warning
     *
     * @param mixed $data
     */
    public function warning($data)
    {
        $this->_log($data, Zend_Log::WARN);
    }

    /**
     * Log debug
     *
     * @param mixed $data
     */
    public function debug($data)
    {
        $this->_log($data, Zend_Log::DEBUG);
    }

    /**
     * Perform logging
     *
     * @param $data
     * @param int $level
     */
    protected function _log($data, $level = Zend_log::DEBUG)
    {
        $isDebug = Mage::getSingleton('pagaio_connect/config')->isDebug();
        if ($level === Zend_Log::DEBUG && !$isDebug) {
            return;
        }

        Mage::log($data, $level, self::PAGAIO_LOG_FILE, $isDebug);
    }

}
<?php

namespace Pagaio\ApiClient\Exception;

/**
 * Class MissingApiKeyException.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class MissingApiKeyException extends PagaioApiClientException
{
    public function __construct()
    {
        parent::__construct('api key is missing, you must provide one before sending a request');
    }
}

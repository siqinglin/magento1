<?php

namespace Pagaio\ApiClient\Exception;

/**
 * Class MissingExceptionException.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class MissingEndpointException extends PagaioApiClientException
{
    public function __construct()
    {
        parent::__construct('missing endpoint, you must provide one before sending a request');
    }
}

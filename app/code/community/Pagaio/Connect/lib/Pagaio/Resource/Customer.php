<?php

namespace Pagaio\ApiClient\Resource;

/**
 * Class Customer.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Customer extends SimpleResource
{
    public function getResourceName()
    {
        return 'customers';
    }

    public function getTypeName()
    {
        return 'customers';
    }
}

<?php

namespace Pagaio\ApiClient\Resource;

/**
 * Class Contract.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Contract extends SimpleResource
{
    public function getResourceName()
    {
        return 'contracts';
    }

    public function getTypeName()
    {
        return 'contracts';
    }
}

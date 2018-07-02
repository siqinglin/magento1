<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class Info.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Info
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getInfo()
    {
        return $this->connection->get('/me');
    }
}

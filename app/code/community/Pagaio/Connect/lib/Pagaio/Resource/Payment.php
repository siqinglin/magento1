<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class Payment.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Payment
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($id)
    {
        return $this->connection->get("/payments/$id");
    }

    public function retrieveAll()
    {
        return $this->connection->get('/payments');
    }
}

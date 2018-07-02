<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class SubscriptionHistory.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class SubscriptionHistory
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($subscriptionId, $historyId)
    {
        return $this->connection->get("/subscriptions/$subscriptionId/histories/$historyId");
    }
}

<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class Amendment.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Amendment
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($id)
    {
        return $this->connection->get("/amendments/$id");
    }

    public function create($id = null, array $attributes, $subscriptionClauseId)
    {
        $data = [
            'data' => [
                'type' => 'amendments',
                'attributes' => $attributes,
                'relationships' => [
                    'subscription_clauses' => [
                        'data' => [
                            'type' => 'subscription_clauses',
                            'id' => (string) $subscriptionClauseId,
                        ],
                    ],
                ],
            ],
        ];

        if (null !== $id) {
            $data['data']['id'] = (string) $id;
        }

        return $this->connection->post('/amendments', json_encode($data));
    }
}

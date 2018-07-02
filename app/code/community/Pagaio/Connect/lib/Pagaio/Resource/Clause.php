<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class Clause.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Clause
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($id)
    {
        return $this->connection->get("/clauses/$id");
    }

    public function retrieveAll($contractId)
    {
        return $this->connection->get("/contracts/$contractId/relationships/clauses");
    }

    public function create($id = null, array $attributes, $contractId)
    {
        $data = [
            'data' => [
                'type' => 'clauses',
                'attributes' => $attributes,
                'relationships' => [
                    'contracts' => [
                        'data' => [
                            'type' => 'contracts',
                            'id' => (string) $contractId,
                        ],
                    ],
                ],
            ],
        ];

        if (null !== $id) {
            $data['data']['id'] = (string) $id;
        }

        return $this->connection->post('/clauses', json_encode($data));
    }
}

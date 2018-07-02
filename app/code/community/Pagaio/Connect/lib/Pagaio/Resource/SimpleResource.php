<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class SimpleResource.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
abstract class SimpleResource
{
    protected $connection;

    abstract public function getResourceName();

    abstract public function getTypeName();

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($id)
    {
        return $this->connection->get(sprintf('/%s/%s', (string) $this->getResourceName(), $id));
    }

    public function retrieveAll()
    {
        return $this->connection->get(sprintf('/%s', (string) $this->getResourceName()));
    }

    public function create($id = null, array $attributes)
    {
        $data = [
            'data' => [
                'type' => (string) $this->getTypeName(),
                'attributes' => $attributes,
            ],
        ];

        if (null !== $id) {
            $data['data']['id'] = (string) $id;
        }

        return $this->connection->post(sprintf('/%s', (string) $this->getResourceName()), json_encode($data));
    }
}

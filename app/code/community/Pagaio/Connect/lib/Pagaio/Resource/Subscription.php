<?php

namespace Pagaio\ApiClient\Resource;

use Pagaio\ApiClient\Connection;

/**
 * Class Susbcription.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Subscription
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function retrieve($id)
    {
        return $this->connection->get("/subscriptions/$id");
    }

    public function retrieveAll($contractId = null, $customerId = null, $customerEmail = null)
    {
        $query = [];

        if (null !== $contractId) {
            $query['filter'] = ['contract_id' => $contractId];
        }

        if (null !== $customerId) {
            $query['filter'] = ['customer_id' => $customerId];
        }

        if (null !== $customerEmail) {
            $query['filter'] = ['customer_email' => $customerEmail];
        }

        return $this->connection->get('/subscriptions', $query);
    }

    public function close($id)
    {
        return $this->connection->delete("/subscriptions/$id");
    }

    public function reopen($id)
    {
        $data = [
            'data' => [
                'type' => 'subscriptions',
                'id' => $id,
                'attributes' => [
                    'status' => 'ready',
                ],
            ],
        ];
        return $this->connection->patch("/subscriptions/$id", json_encode($data));
    }

    public function create($id = null, $contractId, $paymentId, array $customer = [], $customerId = null, array $metadata = [])
    {
        if (null === $customerId && true === empty($customer)) {
            throw new \InvalidArgumentException('at least customerId or customer parameter must be used');
        }

        if (null !== $customerId && false === empty($customer)) {
            throw new \InvalidArgumentException('use customerId or customer parameter but not both at the same time');
        }

        $data = [
            'data' => [
                'type' => 'subscriptions',
                'attributes' => [],
                'relationships' => [
                    'contracts' => [
                        'data' => [
                            'type' => 'contracts',
                            'id' => (string) $contractId,
                        ],
                    ],
                    'payments' => [
                        'data' => [
                            'type' => 'payments',
                            'id' => (string) $paymentId,
                        ],
                    ],
                ],
            ],
        ];

        if (!empty($customer)) {
            $data['data']['attributes'] = ['customer' => $customer];
        }

        if (!empty($metadata)) {
            $data['data']['attributes']['metadata'] = $metadata;
        }

        if (null !== $customerId) {
            $data['data']['relationships']['customers'] = [
                'data' => [
                    'type' => 'customers',
                    'id' => (string) $customerId,
                ],
            ];
        }

        if (null !== $id) {
            $data['data']['id'] = (string) $id;
        }

        return $this->connection->post('/subscriptions', json_encode($data));
    }
}

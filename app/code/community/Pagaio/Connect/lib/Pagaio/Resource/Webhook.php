<?php

namespace Pagaio\ApiClient\Resource;

/**
 * Class Webhook.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Webhook extends SimpleResource
{
    public function getResourceName()
    {
        return 'webhooks';
    }

    public function getTypeName()
    {
        return 'webhooks';
    }

    public function delete($id)
    {
        return $this->connection->delete(sprintf('/%s/%s', (string) $this->getResourceName(), $id));
    }

    public function update($id, array $attributes)
    {
        $data = [
            'data' => [
                'type' => (string) $this->getTypeName(),
                'id' => (string) $id,
                'attributes' => $attributes,
            ],
        ];

        return $this->connection->patch(
            sprintf('/%s/%s', (string) $this->getResourceName(), (string) $id),
            json_encode($data)
        );
    }
}

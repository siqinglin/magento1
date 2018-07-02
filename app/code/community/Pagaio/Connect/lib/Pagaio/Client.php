<?php

namespace Pagaio\ApiClient;

use Pagaio\ApiClient\Resource\Amendment;
use Pagaio\ApiClient\Resource\Clause;
use Pagaio\ApiClient\Resource\Contract;
use Pagaio\ApiClient\Resource\Customer;
use Pagaio\ApiClient\Resource\Info;
use Pagaio\ApiClient\Resource\Payment;
use Pagaio\ApiClient\Resource\Subscription;
use Pagaio\ApiClient\Resource\SubscriptionHistory;
use Pagaio\ApiClient\Resource\Webhook;

/**
 * Class Client.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Client
{
    private $connection;

    private $customer;
    private $webhook;
    private $contract;
    private $clause;
    private $amendment;
    private $subscription;
    private $history;
    private $payment;
    private $info;

    const ENDPOINT = 'https://api.pagaio.com';

    public function __construct($apiKey = null)
    {
        $this->connection = new Connection(self::ENDPOINT, $apiKey);
        $this->customer = new Customer($this->connection);
        $this->webhook = new Webhook($this->connection);
        $this->contract = new Contract($this->connection);
        $this->clause = new Clause($this->connection);
        $this->amendment = new Amendment($this->connection);
        $this->subscription = new Subscription($this->connection);
        $this->payment = new Payment($this->connection);
        $this->history = new SubscriptionHistory($this->connection);
        $this->info = new Info($this->connection);
    }

    public function setApiKey($apiKey)
    {
        $this->connection->setApiKey($apiKey);
    }

    public function setEndpoint($endpoint)
    {
        $this->connection->setEndpoint($endpoint);
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getWebhook()
    {
        return $this->webhook;
    }

    public function getContract()
    {
        return $this->contract;
    }

    public function getClause()
    {
        return $this->clause;
    }

    public function getAmendment()
    {
        return $this->amendment;
    }

    public function getSubscription()
    {
        return $this->subscription;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function getHistory()
    {
        return $this->history;
    }

    public function getInfo()
    {
        return $this->info;
    }
}

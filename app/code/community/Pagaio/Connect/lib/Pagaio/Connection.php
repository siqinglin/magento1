<?php

namespace Pagaio\ApiClient;

use Pagaio\ApiClient\Exception\ClientError;
use Pagaio\ApiClient\Exception\MissingApiKeyException;
use Pagaio\ApiClient\Exception\MissingEndpointException;
use Pagaio\ApiClient\Exception\NetworkError;
use Pagaio\ApiClient\Exception\ServerError;

/**
 * Class Connection.
 *
 * @author Manuel Raynaud <manu@periodable.com>
 */
class Connection
{
    private $apiKey;
    private $endpoint;
    private $curl;

    private $responseBody;
    private $responseHeaders = [];
    private $responseStatusLine;

    private $defaultHeaders = [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ];

    public function __construct($endpoint = null, $apiKey = null)
    {
        $this->setApiKey($apiKey);
        $this->setEndpoint($endpoint);
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function setEndpoint($endpoint)
    {
        $this->endpoint = rtrim((string) $endpoint, '/');
    }

    private function initializeRequest($url)
    {
        if (null === $this->apiKey) {
            throw new MissingApiKeyException();
        }

        if (true === empty($this->endpoint)) {
            throw new MissingEndpointException();
        }

        $this->responseBody = null;
        $this->responseHeaders = [];

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $this->endpoint.$url);
        curl_setopt($this->curl, CURLOPT_USERAGENT, 'pagaio sdk');
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, [$this, 'parseBody']);
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'parseHeader']);
    }

    private function processResponse()
    {
        if (curl_errno($this->curl)) {
            throw new NetworkError(curl_error($this->curl), curl_errno($this->curl));
        }

        $rawBody = $this->getRawBody();

        $statusCode = (int) curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($statusCode >= 400 && $statusCode <= 499) {
            throw new ClientError($rawBody, $statusCode);
        }

        if ($statusCode >= 500) {
            throw new ServerError($rawBody, $statusCode);
        }
    }

    private function sendRequest(array $headers = [])
    {
        $headers = array_merge($this->defaultHeaders, $headers);
        $headers['Authorization'] = sprintf('Bearer %s', (string) $this->apiKey);

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->buildHeaders($headers));

        curl_exec($this->curl);
    }

    private function buildHeaders(array $headers)
    {
        $requestHeaders = [];

        foreach ($headers as $header => $value) {
            $requestHeaders[] = "$header: $value";
        }

        return $requestHeaders;
    }

    /**
     * @param string $url
     * @param string $body
     *
     * @return array
     */
    public function post($url, $body, array $headers = [])
    {
        $this->initializeRequest($url);

        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, (string) $body);

        $this->sendRequest($headers);
        $this->processResponse();

        return $this->getBody();
    }

    public function get($url, array $query = [], array $headers = [])
    {
        if (false === empty($query)) {
            $url .= '?'.http_build_query($query);
        }

        $this->initializeRequest($url);

        curl_setopt($this->curl, CURLOPT_HTTPGET, true);

        $this->sendRequest($headers);
        $this->processResponse();

        return $this->getBody();
    }

    public function delete($url, array $headers = [])
    {
        $this->initializeRequest($url);

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');

        $this->sendRequest($headers);
        $this->processResponse();

        return $this->getBody();
    }

    public function patch($url, $body, array $headers = [])
    {
        $this->initializeRequest($url);

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, (string) $body);

        $this->sendRequest($headers);
        $this->processResponse();

        return $this->getBody();
    }

    private function parseBody($curl, $body)
    {
        $this->responseBody .= $body;

        return strlen($body);
    }

    private function parseHeader($curl, $headers)
    {
        if (!$this->responseStatusLine && 0 === strpos($headers, 'HTTP/')) {
            $this->responseStatusLine = $headers;
        } else {
            $parts = explode(': ', $headers);
            if (isset($parts[1])) {
                $this->responseHeaders[$parts[0]] = trim($parts[1]);
            }
        }

        return strlen($headers);
    }

    public function getBody()
    {
        return json_decode($this->responseBody, true);
    }

    public function getRawBody()
    {
        return $this->responseBody;
    }

    public function __destruct()
    {
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
    }
}

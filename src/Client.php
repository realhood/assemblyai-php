<?php

namespace Realhood\AssemblyAI;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Realhood\AssemblyAI\Exceptions\ApiException;

class Client
{
    protected $apiKey;
    protected $httpClient;

    public function __construct(string $apiKey, array $config = [])
    {
        $this->apiKey = $apiKey;

        $defaultConfig = [
            'base_uri' => 'https://api.assemblyai.com/v2/',
            'headers' => [
                'Authorization' => $this->apiKey,
                'Content-Type'  => 'application/json',
            ],
        ];

        $this->httpClient = new GuzzleClient(array_merge($defaultConfig, $config));
    }

    public function request(string $method, string $uri, array $options = [])
    {
        try {
            $response = $this->httpClient->request($method, $uri, $options);
            return json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            $this->handleApiError($e);
        }
    }

    protected function handleApiError(RequestException $e)
    {
        $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
        $errorBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';

        $errorData = json_decode($errorBody, true);
        $message = $errorData['error'] ?? $e->getMessage();

        throw new ApiException($message, $statusCode, $e);
    }

    public function uploadFile(string $filePath): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \InvalidArgumentException("File at {$filePath} does not exist or is not readable.");
        }

        try {
            $response = $this->httpClient->request('POST', 'upload', [
                'headers' => [
                    'Transfer-Encoding' => 'chunked',
                    'Content-Type' => 'application/octet-stream',
                ],
                'body' => fopen($filePath, 'rb'),
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['upload_url'];
        } catch (RequestException $e) {
            $this->handleApiError($e);
        }
    }
}

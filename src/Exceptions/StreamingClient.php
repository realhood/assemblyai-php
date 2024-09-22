<?php

namespace Realhood\AssemblyAI;

use Ratchet\Client\WebSocket;
use Ratchet\Client\Connector;
use React\EventLoop\Factory as LoopFactory;
use Realhood\AssemblyAI\Exceptions\ApiException;
use React\Promise\Deferred;
use Psr\Http\Message\ResponseInterface;
use Exception;

class StreamingClient
{
    private $apiKey;
    private $url = 'wss://api.assemblyai.com/v2/realtime/ws?sample_rate=16000';
    private $loop;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
        $this->loop = LoopFactory::create();
    }

    /**
     * Stream audio in real-time via WebSocket
     *
     * @param callable $onData
     * @param callable|null $onError
     * @param callable|null $onClose
     * @return void
     */
    public function startStreaming(callable $onData, callable $onError = null, callable $onClose = null)
    {
        $connector = new Connector($this->loop);
        
        $connector($this->url, [], ['Authorization' => $this->apiKey])
            ->then(function (WebSocket $conn) use ($onData, $onError, $onClose) {
                // Handling messages from server
                $conn->on('message', function ($msg) use ($onData) {
                    $data = json_decode($msg, true);
                    $onData($data);
                });

                // Handling connection errors
                $conn->on('error', function (Exception $e) use ($onError) {
                    if ($onError) {
                        $onError($e);
                    } else {
                        throw new ApiException('WebSocket connection error: ' . $e->getMessage(), 0, $e);
                    }
                });

                // Handling connection closure
                $conn->on('close', function ($code = null, $reason = null) use ($onClose) {
                    if ($onClose) {
                        $onClose($code, $reason);
                    }
                });
            })
            ->otherwise(function (Exception $e) use ($onError) {
                if ($onError) {
                    $onError($e);
                } else {
                    throw new ApiException('WebSocket connection error: ' . $e->getMessage(), 0, $e);
                }
            });

        $this->loop->run();
    }

    /**
     * Send audio data over WebSocket.
     *
     * @param WebSocket $conn
     * @param string $audioData
     * @return void
     */
    public function sendAudio(WebSocket $conn, string $audioData)
    {
        $conn->send($audioData);
    }

    /**
     * Stop the real-time transcription and close the WebSocket connection.
     *
     * @param WebSocket $conn
     * @return void
     */
    public function stopStreaming(WebSocket $conn)
    {
        $conn->close();
        $this->loop->stop();
    }
}

<?php

namespace Realhood\AssemblyAI\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Realhood\AssemblyAI\StreamingClient;
use Phake;

class StreamingClientTest extends TestCase
{
    protected $streamingClient;
    protected $mockWebSocket;

    protected function setUp(): void
    {
        $this->streamingClient = new StreamingClient('test_api_key');
        $this->mockWebSocket = Phake::mock(\Ratchet\Client\WebSocket::class);
    }

    public function testStartStreamingSuccessfulConnection()
    {
        $onData = function ($data) {
            $this->assertNotNull($data);
        };

        $this->streamingClient->startStreaming($onData, function ($e) {
            $this->fail('Error callback should not be triggered');
        });

        // Simulate WebSocket message
        Phake::verify($this->mockWebSocket)->on('message', Phake::anyParameters());
    }

    public function testSendAudioData()
    {
        $audioData = base64_encode('test audio data');

        // Send audio data
        $this->streamingClient->sendAudio($this->mockWebSocket, $audioData);

        // Verify WebSocket send call
        Phake::verify($this->mockWebSocket)->send($audioData);
    }

    public function testStopStreaming()
    {
        // Stop streaming and verify the close method is called
        $this->streamingClient->stopStreaming($this->mockWebSocket);

        Phake::verify($this->mockWebSocket)->close();
    }
}

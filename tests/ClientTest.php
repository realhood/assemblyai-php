<?php

namespace Realhood\AssemblyAI\Tests;

use PHPUnit\Framework\TestCase;
use Realhood\AssemblyAI\Client;

class ClientTest extends TestCase
{
    private $client;
    private $apiKey;

    protected function setUp(): void
    {
        $this->apiKey = getenv('ASSEMBLYAI_API_KEY');

        if (!$this->apiKey) {
            $this->markTestSkipped('No API key available for testing.');
        }

        $this->client = new Client($this->apiKey);
    }

    public function testTranscribe()
    {
        // Assuming you have a way to mock Guzzle responses
        $audioUrl = 'https://github.com/audio-samples/audio-samples.github.io/blob/master/samples/mp3/blizzard_biased/sample-0.mp3';
        $response = $this->client->transcribe($audioUrl);

        $this->assertArrayHasKey('id', $response);
    }

    public function testTranscribeAndSummarize()
    {
        $audioUrl = 'https://github.com/audio-samples/audio-samples.github.io/blob/master/samples/mp3/blizzard_biased/sample-0.mp3';
        $options = ['summary_type' => 'conversational'];
        $response = $this->client->transcribeAndSummarize($audioUrl, $options);

        $this->assertArrayHasKey('id', $response);
    }

    public function testTranscribeWithChapters()
    {
        $audioUrl = 'https://example.com/podcast.mp3';
        $response = $this->client->transcribeWithChapters($audioUrl);

        $this->assertArrayHasKey('id', $response);
    }

    public function testGetTranscript()
    {
        $transcriptId = 'test_transcript_id';
        $response = $this->client->getTranscript($transcriptId);

        // Depending on your mocking, adjust the assertions
        $this->assertArrayHasKey('status', $response);
    }

    public function testUploadFile()
    {
        //$filePath = '/path/to/test/audio.mp3';
        $filePath = '/Users/real/Downloads/sample-0.mp3';
        $uploadUrl = $this->client->uploadFile($filePath);

        $this->assertIsString($uploadUrl);
    }
}

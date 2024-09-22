<?php

namespace Realhood\AssemblyAI;

use Realhood\AssemblyAI\Models\TranscriptModel;
use Realhood\AssemblyAI\Exceptions\ApiException;
use InvalidArgumentException;

class Transcriber
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a new transcription.
     *
     * @param array $options
     * @return Transcript
     * @throws ApiException
     */
    public function transcribe(array $options): Transcript
    {
        $this->validateParams($options);

        $data = $this->client->request('POST', 'transcript', [
            'json' => $options,
        ]);

        $transcriptModel = new TranscriptModel($data);
        return new Transcript($this->client, $transcriptModel);
    }

    /**
     * List all transcripts.
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function listTranscripts(int $limit = 20, int $offset = 0): array
    {
        return $this->client->request('GET', 'transcript', [
            'query' => [
                'limit' => $limit,
                'offset' => $offset,
            ],
        ]);
    }

    /**
     * Upload an audio file and return the upload URL.
     *
     * @param string $filePath
     * @return string
     * @throws InvalidArgumentException
     */
    public function uploadAudio(string $filePath): string
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("File at {$filePath} does not exist or is not readable.");
        }

        return $this->client->uploadFile($filePath);
    }

    /**
     * Validate the transcription parameters before sending the request.
     *
     * @param array $options
     * @throws InvalidArgumentException
     */
    protected function validateParams(array &$options)
    {
        // Ensure that either 'audio_url' or 'audio_data' is provided
        if (empty($options['audio_url']) && empty($options['audio_data'])) {
            throw new InvalidArgumentException('Either "audio_url" or "audio_data" must be provided.');
        }

        if (!empty($options['audio_url']) && !filter_var($options['audio_url'], FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('"audio_url" must be a valid URL.');
        }

        // Check if boolean parameters are valid
        $booleanParams = [
            'punctuate',
            'format_text',
            'dual_channel',
            'speaker_labels',
            'content_safety',
            'iab_categories',
            'entity_detection',
            'sentiment_analysis',
            'auto_highlights',
            'filter_profanity',
            'redact_pii',
            'disfluencies',
            'utterances',
            'summarization',
            'auto_chapters',
        ];

        foreach ($booleanParams as $param) {
            if (isset($options[$param]) && !is_bool($options[$param])) {
                throw new InvalidArgumentException("\"{$param}\" must be a boolean.");
            }
        }

        // Validate 'summary_type' parameter
        if (isset($options['summary_type']) && !in_array($options['summary_type'], ['bullets', 'gist', 'headline', 'conversational'])) {
            throw new InvalidArgumentException('"summary_type" must be one of "bullets", "gist", "headline", or "conversational".');
        }

        // Validate 'boost_param'
        if (isset($options['boost_param']) && !in_array($options['boost_param'], ['low', 'default', 'high'])) {
            throw new InvalidArgumentException('"boost_param" must be one of "low", "default", or "high".');
        }

        // Validate 'word_boost'
        if (isset($options['word_boost']) && !is_array($options['word_boost'])) {
            throw new InvalidArgumentException('"word_boost" must be an array of strings.');
        }

        // Validate 'language_code'
        if (isset($options['language_code']) && !is_string($options['language_code'])) {
            throw new InvalidArgumentException('"language_code" must be a string.');
        }

    }
}

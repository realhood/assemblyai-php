<?php

namespace Realhood\AssemblyAI;

use Realhood\AssemblyAI\Models\TranscriptModel;

class Transcript
{
    protected $client;
    protected $model;

    public function __construct(Client $client, TranscriptModel $model)
    {
        $this->client = $client;
        $this->model = $model;
    }

    /**
     * Refresh the transcript by fetching the latest data from the API.
     */
    public function refresh()
    {
        $data = $this->client->request('GET', "transcript/{$this->model->id}");
        $this->model->update($data);
    }

    /**
     * Get the paragraphs of the transcript.
     *
     * @return array
     */
    public function getParagraphs(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/paragraphs");
    }

    /**
     * Get the words of the transcript.
     *
     * @return array
     */
    public function getWords(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/words");
    }

    /**
     * Get the entities detected in the transcript.
     *
     * @return array
     */
    public function getEntities(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/entities");
    }

    /**
     * Get chapters in the transcript.
     *
     * @return array
     */
    public function getChapters(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/chapters");
    }

    /**
     * Get sentiment analysis results.
     *
     * @return array
     */
    public function getSentimentAnalysis(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/sentiment");
    }

    /**
     * Get content safety labels.
     *
     * @return array
     */
    public function getContentSafetyLabels(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/content_safety");
    }

    /**
     * Get auto highlights.
     *
     * @return array
     */
    public function getAutoHighlights(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/auto_highlights");
    }

    /**
     * Get utterances in the transcript.
     *
     * @return array
     */
    public function getUtterances(): array
    {
        return $this->client->request('GET', "transcript/{$this->model->id}/utterances");
    }

    /**
     * Delete the transcript.
     */
    public function delete()
    {
        $this->client->request('DELETE', "transcript/{$this->model->id}");
    }

    /**
     * Magic method to access model properties directly.
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this->model, $property)) {
            return $this->model->$property;
        }
        throw new \Exception("Property {$property} does not exist on Transcript.");
    }
}

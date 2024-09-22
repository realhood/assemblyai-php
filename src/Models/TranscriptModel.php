<?php

namespace Realhood\AssemblyAI\Models;

class TranscriptModel
{
    public $id;
    public $status;
    public $text;
    public $summary;
    public $confidence;
    public $audio_duration;
    public $language_code;
    public $punctuate;
    public $speaker_labels;
    public $auto_chapters;
    public $utterances;

    // Other attributes that the API returns can be added here

    public function __construct(array $data)
    {
        $this->update($data);
    }

    /**
     * Update the model with the latest data from the API.
     *
     * @param array $data
     */
    public function update(array $data)
    {
        $this->id = $data['id'] ?? $this->id;
        $this->status = $data['status'] ?? $this->status;
        $this->text = $data['text'] ?? $this->text;
        $this->summary = $data['summary'] ?? $this->summary;
        $this->confidence = $data['confidence'] ?? $this->confidence;
        $this->audio_duration = $data['audio_duration'] ?? $this->audio_duration;
        $this->language_code = $data['language_code'] ?? $this->language_code;
        $this->punctuate = $data['punctuate'] ?? $this->punctuate;
        $this->speaker_labels = $data['speaker_labels'] ?? $this->speaker_labels;
        $this->auto_chapters = $data['auto_chapters'] ?? $this->auto_chapters;
        $this->utterances = $data['utterances'] ?? $this->utterances;
    }
}

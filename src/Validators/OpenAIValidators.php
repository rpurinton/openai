<?php

namespace RPurinton\Validators;

use RPurinton\Exceptions\ConfigException;

class OpenAIValidators
{
    /**
     * Get the validated prompt configuration.
     *
     * @return array
     */
    public static function validatePrompt(array $prompt): bool
    {
        $keys = [
            'model' => 'string',
            'temperature' => 'double',
            'top_p' => 'double',
            'frequency_penalty' => 'double',
            'presence_penalty' => 'double',
            'max_tokens' => 'integer',
            'stop' => 'array',
            'messages' => 'array',
            'tools' => 'array',
            'functions' => 'array',
        ];
        foreach ($prompt as $key => $value) {
            if (array_key_exists($key, $keys)) {
                if (gettype($value) !== $keys[$key]) {
                    throw new ConfigException("Invalid type for '$key' in OpenAI config. Expected {$keys[$key]}, got " . gettype($value));
                }
            } else {
                throw new ConfigException("Invalid key '$key' in OpenAI config.");
            }
        }
        return true;
    }
}

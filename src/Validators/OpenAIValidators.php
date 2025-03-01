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
            'store' => 'boolean',
            'metadata' => 'array',
            'logit_bias' => 'array',
            'logprobs' => 'boolean',
            'top_logprobs' => 'integer',
            'n' => 'integer',
            'modalities' => 'array',
            'prediction' => 'array',
            'audio' => 'array',
            'response_format' => 'array',
            'seed' => 'integer',
            'service_tier' => 'string',
            'stream_options' => 'array',
            'parallel_tool_calls' => 'boolean',
            'user' => 'string',
            'function_call' => 'string',
            'functions' => 'array',
            'temperature' => 'double,integer',
            'top_p' => 'double,integer',
            'frequency_penalty' => 'double,integer',
            'presence_penalty' => 'double,integer',
            'max_tokens' => 'integer',
            'max_completion_tokens' => 'integer',
            'stop' => 'array',
            'messages' => 'array',
            'tools' => 'array',
            'response_format' => 'array',
            'reasoning_effort' => 'string',
            'stream' => 'boolean',
            'tool_choice' => 'string,array',
        ];
        foreach ($prompt as $key => $value) {
            if (array_key_exists($key, $keys)) {
                if (strpos($keys[$key], ',') !== false) {
                    $types = explode(',', $keys[$key]);
                    $valid = false;
                    foreach ($types as $type) {
                        if (gettype($value) === $type) {
                            $valid = true;
                            break;
                        }
                    }
                    if (!$valid) {
                        throw new ConfigException("Invalid type for '$key' in OpenAI config. Expected {$keys[$key]}, got " . gettype($value));
                    }
                } else if (gettype($value) !== $keys[$key]) {
                    throw new ConfigException("Invalid type for '$key' in OpenAI config. Expected {$keys[$key]}, got " . gettype($value));
                }
            } else {
                throw new ConfigException("Invalid key '$key' in OpenAI config.");
            }
        }
        return true;
    }
}

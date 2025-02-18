<?php

declare(strict_types=1);

namespace RPurinton;

use TikToken\Encoder;
use OpenAI as OpenAIClient;
use RPurinton\Config;
use RPurinton\Exceptions\ConfigException;
use RPurinton\Exceptions\OpenAIException;

/**
 * Class OpenAI
 *
 * Provides an interface for interacting with the OpenAI API and managing token counts.
 *
 * @package RPurinton
 */
class OpenAI
{
	/**
	 * The OpenAI API client instance.
	 *
	 * @var OpenAIClient
	 */
	public OpenAIClient $ai;

	/**
	 * Encoder instance for counting tokens.
	 *
	 * @var Encoder
	 */
	private Encoder $encoder;

	/**
	 * Prompt Configuration
	 * 
	 * @var array
	 */
	public array $prompt;

	/**
	 * OpenAI constructor.
	 *
	 * Initializes the API client with the provided API key or falling back to the environment variable.
	 *
	 * @param string|null $apiKey The OpenAI API key. If not provided, the OPENAI_API_KEY environment variable is used.
	 *
	 * @throws Exception If no API key is provided.
	 */
	public function __construct(?string $apiKey = null)
	{
		if (empty($apiKey)) {
			$apiKey = getenv('OPENAI_API_KEY') ?: null;
		}

		if (empty($apiKey)) {
			throw new OpenAIException('No OpenAI API key provided.');
		}

		$this->ai = OpenAIClient::client($apiKey);
		$this->encoder = new Encoder();
		$this->prompt =  $this->validatedPrompt($this->getConfig());
	}

	/**
	 * Get the validated prompt configuration.
	 *
	 * @return array
	 */
	public function validatedPrompt(array $prompt): array
	{
		$keys = [
			'model' => 'string',
			'temperature' => 'float',
			'top_p' => 'float',
			'frequency_penalty' => 'float',
			'presence_penalty' => 'float',
			'max_tokens' => 'int',
			'stop' => 'array',
			'messages' => 'array',
			'tools' => 'array',
			'functions' => 'array',
		];
		foreach ($prompt as $key => $value) {
			if (array_key_exists($key, $keys)) {
				if (gettype($value) !== $keys[$key]) {
					throw new ConfigException("Invalid type for $key in OpenAI config. Expected $keys[$key], got " . gettype($value));
				}
			} else {
				throw new ConfigException("Invalid key $key in OpenAI config.");
			}
		}
		return $prompt;
	}

	/**
	 * Get the prompt configuration.
	 *
	 * @return array
	 */
	public function getConfig(): array
	{
		return Config::get('OpenAI', ['model' => 'string']);
	}

	/**
	 * Counts the tokens in the given text using the encoder.
	 *
	 * This method encodes the provided text and returns the count of tokens generated.
	 *
	 * @param string $text The text to analyze.
	 *
	 * @return int The number of tokens in the input text.
	 */
	public function tokenCount(string $text): int
	{
		$tokens = $this->encoder->encode($text);
		return count($tokens);
	}
}

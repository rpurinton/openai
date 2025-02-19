<?php

declare(strict_types=1);

namespace RPurinton;

use OpenAI\Client;
use OpenAI\Responses\Chat\CreateResponse;
use RPurinton\Config;
use RPurinton\Exceptions\OpenAIException;
use RPurinton\Validators\OpenAIValidators;

/**
 * Class OpenAI
 *
 * A wrapper for the OpenAI API client.
 *
 * @package RPurinton
 */
class OpenAI
{
	/**
	 * The OpenAI API client instance.
	 *
	 * @var \OpenAI\Client
	 */
	public Client $ai;

	/**
	 * Prompt Configuration
	 * 
	 * @var array
	 */
	public array $prompt;

	/**
	 * OpenAI constructor.
	 * Initializes the API client with the provided API key or falling back to the environment variable.
	 *
	 * @param string|null $apiKey The OpenAI API key. If not provided, the OPENAI_API_KEY environment variable is used.
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

		$this->ai = \OpenAI::client($apiKey);
		$this->reload();
	}

	/**
	 * Connect to the OpenAI API.
	 *
	 * @param string|null $apiKey The OpenAI API key. If not provided, the OPENAI_API_KEY environment variable is used.
	 * @return OpenAI The OpenAI instance.
	 */
	public static function connect(?string $apiKey = null): OpenAI
	{
		return new self($apiKey);
	}

	/**
	 * Reload the prompt configuration.
	 */
	public function reload(): void
	{
		$this->prompt = Config::get('OpenAI', ['model' => 'string']);
		OpenAIValidators::validatePrompt($this->prompt);
	}

	/**
	 * Ask a question to the OpenAI API.
	 * This method sends the provided text to the OpenAI API and returns the response.
	 *
	 * @param string $text The text to send to the API.
	 * @return string The response from the API.
	 * @throws OpenAIException If an error occurs while interacting with the API.
	 */
	public function ask(string $text): string
	{
		try {
			$this->prompt['message'][] = ['role' => 'user', 'content' => $text];
			$response = $this->create($this->prompt);
			$response = $response->choices[0]->message->content;
			$this->prompt['message'][] = ['role' => 'ai', 'content' => $response];
			return $response;
		} catch (\Exception $e) {
			throw new OpenAIException($e->getMessage());
		}
	}

	/**
	 * Create a chat response from the OpenAI API.
	 *
	 * @param array $prompt The prompt to send to the API.
	 * @return CreateResponse The response from the API.
	 * @throws OpenAIException If an error occurs while interacting with the API.
	 */
	public function create(array $prompt): CreateResponse
	{
		try {
			return $this->ai->chat()->create($prompt);
		} catch (\Exception $e) {
			throw new OpenAIException($e->getMessage());
		}
	}
}

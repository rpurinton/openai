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
	public Client $ai;

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

		$this->ai = \OpenAI::client($apiKey);
		$this->prompt =  $this->getConfig();
		OpenAIValidators::validatePrompt($this->prompt);
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
	 * Ask a question to the OpenAI API.
	 *
	 * This method sends the provided text to the OpenAI API and returns the response.
	 *
	 * @param string $text The text to send to the API.
	 *
	 * @return string The response from the API.
	 *
	 * @throws OpenAIException If an error occurs while interacting with the API.
	 */
	public function ask(string $text): string
	{
		try {
			$prompt = array_merge($this->prompt, ['messages' => [['role' => 'user', 'content' => $text]]]);
			$response = $this->ai->chat()->create($prompt);
			return $response->choices[0]->message->content;
		} catch (\Exception $e) {
			throw new OpenAIException($e->getMessage());
		}
	}

	/**
	 * Create a chat response from the OpenAI API.
	 *
	 * This method sends the provided prompt to the OpenAI API and returns the response.
	 *
	 * @param array $prompt The prompt to send to the API.
	 *
	 * @return CreateResponse The response from the API.
	 *
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

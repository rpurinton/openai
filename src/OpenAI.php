<?php

declare(strict_types=1);

namespace RPurinton;

use TikToken\Encoder;

class OpenAI
{
	public \OpenAI $ai;
	private Encoder $encoder;

	/**
	 * Constructor.
	 *
	 * @param string $apiKey The OpenAI API key.
	 */
	public function __construct(string $apiKey)
	{
		$this->ai = \OpenAI::client($apiKey);
		$this->encoder = new Encoder();
	}

	/**
	 * Counts the tokens for a given text using the encoder.
	 *
	 * @param string $text
	 * @return int
	 */
	public function token_count(string $text): int
	{
		return count($this->encoder->encode($text));
	}
}

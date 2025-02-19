<?php

namespace RPurinton;

use TikToken\Encoder;

class TikToken
{
    /**
     * Encoder instance for counting tokens.
     *
     * @var Encoder
     */
    private Encoder $encoder;

    /**
     * TikToken constructor.
     *
     * Initializes the Encoder instance.
     */
    public function __construct()
    {
        $this->encoder = new Encoder();
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
    public function count(string $text): int
    {
        return count($this->encoder->encode($text));
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

    public static function staticCount(string $text): int
    {
        return count((new Encoder)->encode($text));
    }
}

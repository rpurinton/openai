#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

use RPurinton\OpenAI;

$ai = OpenAI::connect(getenv('OPENAI_KEY_TEST'));
echo $ai->ask("What is the capital of the United States?") . PHP_EOL;
// Response: The capital of the United States is Washington, D.C.

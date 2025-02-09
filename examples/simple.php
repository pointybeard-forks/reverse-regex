<?php

declare(strict_types=1);

use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser;
use pointybeard\ReverseRegex\Random\MersenneRandom;

// require composer
require '../vendor/autoload.php';

// parse the regex
$lexer = new Lexer('[a-z]{10}');
$parser = new Parser($lexer, new Scope, new Scope);
$generator = $parser->parse()->getResult();

// run the generator
$random = new MersenneRandom(777);

for ($i = 50; $i > 0; $i--) {
    $result = '';
    $generator->generate($result, $random);

    echo $result;
    echo PHP_EOL;
}

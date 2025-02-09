<?php

declare(strict_types=1);

namespace pointybeard\ReverseRegex\Generator;

/**
 *  Conext interface for Generator.
 *
 *  @author Lewis Dyer <getintouch@icomefromthenet.com>
 *
 *  @since 0.0.1
 */
interface ContextInterface
{
    /**
     *  Generate a text string appending to result arguments.
     *
     * @param  string  $result
     */
    public function generate(&$result, GeneratorInterface $generator): string;
}

<?php

declare(strict_types=1);

namespace pointybeard\ReverseRegex\Random;

use pointybeard\ReverseRegex\Exception as ReverseRegexException;

/**
 *  Simple Random.
 *
 *  @see http://www.sitepoint.com/php-random-number-generator/
 *
 *  @author Craig Buckler
 */
class SimpleRandom implements GeneratorInterface
{
    /**
     * @var int the seed value to use
     */
    protected $seed = 0;

    /**
     * @var int the max
     */
    protected $max;

    /**
     * @var int the min
     */
    protected $min;

    /**
     *  Constructor.
     *
     * @param  int  $seed
     * @return void
     */
    public function __construct($seed = null)
    {
        if ($seed === null || $seed === 0) {
            // # 6 - Propagate the call to mt_rand() by assigning it to $seed
            $seed = mt_rand();
        }

        $this->seed($seed);
    }

    public function max($value = null)
    {
        if ($value === null && $this->max === null) {
            $max = 2147483647;
        } elseif ($value === null) {
            $max = $this->max;
        } else {
            $max = $this->max = $value;
        }

        return $max;
    }

    public function min($value = null)
    {
        if ($value === null && $this->max === null) {
            $min = 0;
        } elseif ($value === null) {
            $min = $this->min;
        } else {
            $min = $this->min = $value;
        }

        return $min;
    }

    /**
     *  Set the seed to use for generator.
     *
     * @param  int  $seed  the seed to use
     */
    public function seed($seed = null)
    {
        if ($seed === null) {
            $seed = 0;
        }

        return $this->seed = abs(intval($seed)) % 9999999 + 1;
    }

    /**
     *  Generate a random numer.
     *
     * @param  int  $max
     * @param  int  $max  2,796,203 largest possible max
     */
    public function generate($min = 0, $max = null): int
    {
        if ($max === null) {
            $max = 2796203;
        }

        if ($max > 2796203) {
            throw new ReverseRegexException('Max param has exceeded the maxium 2796203');
        }

        if ($this->seed == 0) {
            $this->seed(mt_rand());
        }

        $this->seed = ($this->seed * 125) % 2796203;

        return $this->seed % ($max - $min + 1) + $min;
    }
}
/* End of File */

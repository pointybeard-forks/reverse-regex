<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser;
use pointybeard\ReverseRegex\Random\MersenneRandom;

class ParserTest extends Basic
{
    public function test_parser_example_a()
    {
        $lexer = new Lexer('ex1{5,5}');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);
        $generator->generate($result, $random);

        $this->assertEquals('ex11111', $result);
    }

    public function test_parser_example_b()
    {
        // $lexer = new Lexer('\(0[23478]\)-[0-9]{4} [0-9]{4}');

        $lexer = new Lexer('(\(0[23478]\)){4}');

        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);
        $generator->generate($result, $random);

        $this->assertEquals('(02)(04)(02)(08)', $result);
    }

    public function test_example_c()
    {
        $lexer = new Lexer('509[0-9][A-K]');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);

        $generator->generate($result, $random);
        $this->assertEquals('5090J', $result);
    }

    public function test_example_d()
    {
        $lexer = new Lexer('\d\d\d');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);

        $generator->generate($result, $random);
        $this->assertMatchesRegularExpression('/\d\d\d/', $result);
    }

    public function test_example_e()
    {
        $lexer = new Lexer('\d\d\d([a-zA-Z])\w.');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(10034343);

        $generator->generate($result, $random);
        $this->assertMatchesRegularExpression('/\d\d\d([a-zA-Z])\w./', $result);
    }

    public function test_parser_example_phone_number()
    {
        $lexer = new Lexer('\(0[23478]\)[0-9]{4}-[0-9]{4}');

        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);
        $generator->generate($result, $random);

        $this->assertEquals('(02)2595-1288', $result);
    }

    public function test_parser_example_post_code()
    {
        // Australian Post Codes.

        // ACT: 0200-0299 and 2600-2639.
        // NSW: 1000-1999, 2000-2599 and 2640-2914.
        // NT:  0900-0999 and 0800-0899.
        // QLD: 9000-9999 and 4000-4999.
        // SA:  5000-5999.
        // TAS: 7800-7999 and 7000-7499.
        // VIC: 8000-8999 and 3000-3999.
        // WA: 6800-6999 and 6000-6799

        $lexer = new Lexer('(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $random = new MersenneRandom(10789);

        for ($i = 100; $i > 0; $i--) {
            $result = '';
            $generator->generate($result, $random);
            $this->assertMatchesRegularExpression('/^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$/', $result);
        }

        // Generate Postcode for ACT only

        $lexer = new Lexer('02[0-9]{2}|26[0-3][0-9]');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $result = '';
        $random = new MersenneRandom(100);

        $generator->generate($result, $random);
        $this->assertEquals('0225', $result);

        $result = '';
        $generator->generate($result, $random);
        $this->assertEquals('2631', $result);

        $result = '';
        $generator->generate($result, $random);
        $this->assertEquals('0288', $result);

        $result = '';
        $generator->generate($result, $random);
        $this->assertEquals('0243', $result);

        $result = '';
        $generator->generate($result, $random);
        $this->assertEquals('0284', $result);

        $result = '';
        $generator->generate($result, $random);
        $this->assertEquals('0210', $result);
    }

    public function test_hellow_world()
    {
        $lexer = new Lexer('Hello|World|Is|Good');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $random = new MersenneRandom(10789);

        for ($i = 10; $i > 0; $i--) {
            $result = '';
            $generator->generate($result, $random);
            $this->assertMatchesRegularExpression('/^Hello|World|Is|Good$/', $result);
        }
    }

    public function test_limiting_quantifer()
    {
        $lexer = new Lexer('(Hello){5,9}');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $random = new MersenneRandom(10789);

        for ($i = 10; $i > 0; $i--) {
            $result = '';
            $generator->generate($result, $random);
            $this->assertMatchesRegularExpression('/(Hello){5,9}/', $result);
        }

        $lexer = new Lexer('(Hello)?');
        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();

        $random = new MersenneRandom(107559);

        $result = '';
        $generator->generate($result, $random);
        $this->assertMatchesRegularExpression('/(Hello)?/', $result);
    }

    public function test_parser_lexer_error()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage("Error found STARTING at position 3 after `\(0[` with msg Negated Character Set ranges not supported at this time");

        $lexer = new Lexer('\(0[^23478]\)[0-9]{4}-[0-9]{4}');

        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);
        $generator = $parser->parse()->getResult();
    }

    public function test_parser_lexer_error_b()
    {
        $lexer = new Lexer('\(0[23478]\)[9-4]{4}-[0-9]{4}');

        $container = new Scope;
        $head = new Scope;
        $parser = new Parser($lexer, $container, $head);

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage("Error found STARTING at position 16 after `\(0[23478]\)[9-4]` with msg Character class range 9 - 4 is out of order");

        $generator = $parser->parse()->getResult();
    }
}

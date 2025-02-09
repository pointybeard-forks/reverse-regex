<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Generator\LiteralScope;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser\CharacterClass;

class CharacterClassTest extends Basic
{
    public function test_normalize_no_unicode()
    {
        $lexer = new Lexer('[a-mnop]');
        $scope = new Scope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $result = $parser->normalize($scope, $scope, $lexer);

        $this->assertEquals('[a-mnop]', $result);
    }

    public function test_normalize_with_unicode_value()
    {
        $lexer = new Lexer('[\X{00ff}nop]');
        $scope = new Scope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $result = $parser->normalize($scope, $scope, $lexer);

        $this->assertEquals('[\\ÿnop]', $result);
    }

    public function test_normalize_with_unicode_range()
    {
        $lexer = new Lexer('[\X{00FF}-\X{00FF}mnop]');
        $scope = new Scope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $result = $parser->normalize($scope, $scope, $lexer);

        $this->assertEquals('[\\ÿ-\\ÿmnop]', $result);
    }

    public function test_fill_range_ascii()
    {
        $start = '!';
        $end = '&';
        $range = '!"#$%&';
        $scope = new LiteralScope;
        $parser = new CharacterClass;

        $parser->fillRange($scope, $start, $end);

        $this->assertEquals($range, implode('', $scope->getLiterals()->toArray()));
    }

    public function test_fill_range_unicode()
    {
        $start = 'Ꭰ';
        $end = 'Ꭵ';
        $range = 'ᎠᎡᎢᎣᎤᎥ';
        $scope = new LiteralScope;
        $parser = new CharacterClass;

        $parser->fillRange($scope, $start, $end);

        $this->assertEquals($range, implode('', $scope->getLiterals()->toArray()));
    }

    public function test_fill_range_outof_order()
    {
        $start = 'z';
        $end = 'a';
        $scope = new LiteralScope;
        $parser = new CharacterClass;

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Character class range z - a is out of order');

        $parser->fillRange($scope, $start, $end);
    }

    public function test_parse_no_ranges()
    {
        $lexer = new Lexer('[amnop]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->toArray();

        $this->assertEquals(['a', 'm', 'n', 'o', 'p'], array_values($values));
    }

    public function test_parse_no_unicode_shorts()
    {
        $lexer = new Lexer('[a-k]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->toArray();

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'], array_values($values));
    }

    public function test_parse_no_unicode_shorts_multi_range()
    {
        $lexer = new Lexer('[a-k-n]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->toArray();

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n'], array_values($values));
    }

    public function test_parse_unicode_short()
    {
        $lexer = new Lexer('[\X{0061}-\X{006B}]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->toArray();

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'], array_values($values));
    }

    public function test_parse_hex_short()
    {
        $lexer = new Lexer('[\x61-\x6B]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->toArray();

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k'], array_values($values));
    }

    public function test_parse_hex_short_multirange()
    {
        $lexer = new Lexer('[z\x61-\x6B-\x6E]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);

        $values = $head->getLiterals()->getValues();
        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'z'], $values);
    }

    public function test_parse_hex_short_brace_error()
    {
        $lexer = new Lexer('[\x{61}-\x6B-\x6E]');
        $scope = new Scope;
        $head = new LiteralScope;
        $parser = new CharacterClass;

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Braces not supported here');

        $lexer->moveNext();
        $parser->parse($head, $scope, $lexer);
    }
}

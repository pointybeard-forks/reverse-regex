<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Generator\LiteralScope;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser\Unicode;

class UnicodeTest extends Basic
{
    public function test_unsupported_short_property()
    {
        $lexer = new Lexer('\p');
        $scope = new Scope;
        $parser = new Unicode;

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Property \p (Unicode Property) not supported use \x to specify unicode character or range');

        $parser->parse($scope, $scope, $lexer);
    }

    public function test_error_no_opening_brace()
    {
        $lexer = new Lexer('\Xaaaaa');
        $scope = new Scope;
        $parser = new Unicode;

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Expecting character { after \X none found');

        $parser->parse($scope, $scope, $lexer);
    }

    public function test_error_nested()
    {
        $lexer = new Lexer('\X{aa{aa}');
        $scope = new Scope;
        $parser = new Unicode;

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Nesting hex value ranges is not allowed');

        $parser->parse($scope, $scope, $lexer);
    }

    public function test_error_unclosed()
    {
        $lexer = new Lexer('\X{aaaa');
        $scope = new Scope;
        $parser = new Unicode;

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Closing quantifier token `}` not found');

        $parser->parse($scope, $scope, $lexer);
    }

    public function test_error_empty_token()
    {
        $lexer = new Lexer('\X{}');
        $scope = new Scope;
        $parser = new Unicode;

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('No hex number found inside the range');

        $parser->parse($scope, $scope, $lexer);
    }

    public function tests_example_a()
    {
        $lexer = new Lexer('\X{FA24}');
        $scope = new Scope;
        $parser = new Unicode;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        $this->assertEquals('﨤', $result[0]);
    }

    public function test_short_error_when_braces()
    {
        $lexer = new Lexer('\x{64');
        $scope = new Scope;
        $parser = new Unicode;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Braces not supported here');

        $parser->parse($head, $scope, $lexer);
    }

    public function test_short_x()
    {
        $lexer = new Lexer('\x64');
        $scope = new Scope;
        $parser = new Unicode;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        $this->assertEquals('d', $result[0]);
    }
}

<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Generator\LiteralScope;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser\Short;

class ShortTest extends Basic
{
    public function test_digit()
    {
        $lexer = new Lexer('\d');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertMatchesRegularExpression('/\d/', $value);
        }
    }

    public function test_not_digit()
    {
        $lexer = new Lexer('\D');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertMatchesRegularExpression('/\D/', $value);
        }
    }

    public function test_whitespace()
    {
        $lexer = new Lexer('\s');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertTrue(! empty($value));
        }
    }

    public function test_non_whitespace()
    {
        $lexer = new Lexer('\S');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertTrue(! empty($value));
        }
    }

    public function test_word()
    {
        $lexer = new Lexer('\w');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertMatchesRegularExpression('/\w/', $value);
        }
    }

    public function test_non_word()
    {
        $lexer = new Lexer('\W');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();
        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        foreach ($result as $value) {
            $this->assertMatchesRegularExpression('/\W/', $value);
        }
    }

    public function test_dot_range()
    {
        $lexer = new Lexer('.');
        $scope = new Scope;
        $parser = new Short;
        $head = new LiteralScope('lit1', $scope);

        $lexer->moveNext();

        $parser->parse($head, $scope, $lexer);

        $result = $head->getLiterals();

        // match 0..127 char in ASSCI Chart
        $this->assertCount(128, $result);
    }
}

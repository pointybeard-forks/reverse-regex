<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Lexer;
use pointybeard\ReverseRegex\Parser\Quantifier;

class QuantifierParserTest extends Basic
{
    public function test_quantifier_parser_pattern_a()
    {
        $pattern = '{1,5}';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurances());
        $this->assertEquals(5, $scope->getMaxOccurances());
    }

    public function test_quantifer_single_value()
    {
        $pattern = '{5}';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(5, $scope->getMinOccurances());
        $this->assertEquals(5, $scope->getMaxOccurances());
    }

    public function test_quantifer_spaces_included()
    {
        $pattern = '{ 1 , 5 }';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurances());
        $this->assertEquals(5, $scope->getMaxOccurances());
    }

    public function test_failer_alpha_caracters()
    {
        $pattern = '{ 1 , 5a }';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Quantifier expects and integer compitable string');

        $qual->parse($scope, $scope, $lexer);
    }

    public function test_failer_missing_maximum_caracters()
    {
        $pattern = '{ 1 ,}';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Quantifier expects and integer compitable string');

        $qual->parse($scope, $scope, $lexer);
    }

    public function test_failer_missing_minimum_caracters()
    {
        $pattern = '{,1}';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Quantifier expects and integer compitable string');

        $qual->parse($scope, $scope, $lexer);
    }

    public function test_missing_closure_character()
    {
        $pattern = '{1,1';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Closing quantifier token `}` not found');

        $qual->parse($scope, $scope, $lexer);
    }

    public function test_nesting_quantifiers()
    {
        $pattern = '{1,1{1,1}';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Nesting Quantifiers is not allowed');

        $qual->parse($scope, $scope, $lexer);
    }

    public function test_star_quantifier()
    {
        $pattern = 'az*';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();

        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(0, $scope->getMinOccurances());
        $this->assertEquals(PHP_INT_MAX, $scope->getMaxOccurances());
    }

    public function test_cross_quantifier()
    {
        $pattern = 'az+';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(1, $scope->getMinOccurances());
        $this->assertEquals(PHP_INT_MAX, $scope->getMaxOccurances());
    }

    public function test_question_quantifier()
    {
        $pattern = 'az?';
        $lexer = new Lexer($pattern);
        $scope = new Scope;
        $qual = new Quantifier;

        $lexer->moveNext();
        $lexer->moveNext();
        $lexer->moveNext();
        $qual->parse($scope, $scope, $lexer);

        $this->assertEquals(0, $scope->getMinOccurances());
        $this->assertEquals(1, $scope->getMaxOccurances());
    }
}

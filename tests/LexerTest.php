<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Lexer;

class LexerTest extends Basic
{
    public function test_inherits_doctrine_lexer()
    {
        $lexer = new Lexer('[a-z]');
        $this->assertInstanceOf('\Doctrine\Common\Lexer\AbstractLexer', $lexer);
    }

    public function test_lexer_pattern_a()
    {
        $lexer = new Lexer('[a-z]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('a', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('-', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_RANGE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('z', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);

        // $lexer->moveNext();
        // $this->assertEquals(null,$lexer->lookahead['value']);
    }

    public function test_lexer_pattern_b()
    {
        $lexer = new Lexer('\[a-z\]');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('a', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('-', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('z', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();

        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        // $lexer->moveNext();
        // $this->assertEquals(null,$lexer->lookahead['value']);
    }

    public function test_lexer_pattern_c()
    {
        $lexer = new Lexer('[1-9]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('1', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('-', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_RANGE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('9', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);

        // $lexer->moveNext();
        // $this->assertEquals(null,$lexer->lookahead['value']);
    }

    public function test_lexer_pattern_d()
    {
        $lexer = new Lexer('[1-9\x{56}]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('1', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('-', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_RANGE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('9', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('x', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_X, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('5', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('6', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);

        // $lexer->moveNext();
        // $this->assertEquals(null,$lexer->lookahead['value']);
    }

    public function test_lexer_pattern_e()
    {
        $lexer = new Lexer('([^1-8\[]){0,9}*?+');

        $lexer->moveNext();
        $this->assertEquals('(', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('^', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_NEGATED, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('1', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('-', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_RANGE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('8', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(')', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_CLOSE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('0', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(',', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('9', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_CLOSE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('*', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_STAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('?', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_QUESTION, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('+', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_PLUS, $lexer->lookahead['type']);

        // $lexer->moveNext();
        // $this->assertEquals(null,$lexer->lookahead['value']);
    }

    public function test_parrent_short_codes()
    {
        $lexer = new Lexer('\W');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('W', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_NOT_W, $lexer->lookahead['type']);

        $lexer = new Lexer('\w');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('w', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_W, $lexer->lookahead['type']);

        $lexer = new Lexer('\S');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('S', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_NOT_S, $lexer->lookahead['type']);

        $lexer = new Lexer('\s');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('s', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_S, $lexer->lookahead['type']);

        $lexer = new Lexer('\D');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('D', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_NOT_D, $lexer->lookahead['type']);

        $lexer = new Lexer('\d');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('d', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_D, $lexer->lookahead['type']);
    }

    public function test_lexer_pattern_f()
    {
        $lexer = new Lexer('[\']');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        // in the above expression using php metasequence \' to escape a single quote
        // the reg only see the expression ['] and NOT [\']
        $lexer->moveNext();
        $this->assertEquals("'", $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_lexer_escaped_blackslash()
    {
        $lexer = new Lexer('\\\\');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);
    }

    public function test_lexer_brackets()
    {
        $lexer = new Lexer('[\p{}]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('p', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_P, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);

        $lexer = new Lexer('\p{}');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('p', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_P, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_QUANTIFIER_CLOSE, $lexer->lookahead['type']);
    }

    public function test_alternation()
    {
        $lexer = new Lexer('A|a');

        $lexer->moveNext();
        $this->assertEquals('A', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('|', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_CHOICE_BAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('a', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        // no alternation in char classes

        $lexer = new Lexer('[A|a]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('A', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('|', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('a', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_dot_character()
    {
        $lexer = new Lexer('.');

        $lexer->moveNext();
        $this->assertEquals('.', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_DOT, $lexer->lookahead['type']);

        $lexer = new Lexer('\.');

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('.', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        // normal char in a char class
        $lexer = new Lexer('[.]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('.', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_lexer_pattern_g()
    {
        $lexer = new Lexer('abcd&\*\(\)');

        $lexer->moveNext();
        $this->assertEquals('a', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('b', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('c', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('d', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('&', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('*', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('(', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(')', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);
    }

    public function test_unicode_propertyin_character_class()
    {
        $lexer = new Lexer('[np\p{L}]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('n', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('p', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('p', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_P, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('L', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_unicode_reference_propertyin_character_class()
    {
        $lexer = new Lexer('[no\X{00FF}]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('n', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('o', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('X', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SHORT_UNICODE_X, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('{', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('0', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('0', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_NUMERIC, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('F', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('F', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('}', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_carret_and_dollar()
    {
        $lexer = new Lexer('^$');

        $lexer->moveNext();
        $this->assertEquals('^', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_START_CARET, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('$', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_END_DOLLAR, $lexer->lookahead['type']);

        $lexer = new Lexer('[\^$]');

        $lexer->moveNext();
        $this->assertEquals('[', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('\\', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_ESCAPE_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('^', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('$', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_LITERAL_CHAR, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(']', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_SET_CLOSE, $lexer->lookahead['type']);
    }

    public function test_lexer_pattern_h_group_nesting()
    {
        $lexer = new Lexer('(())');

        $lexer->moveNext();
        $this->assertEquals('(', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals('(', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_OPEN, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(')', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_CLOSE, $lexer->lookahead['type']);

        $lexer->moveNext();
        $this->assertEquals(')', $lexer->lookahead['value']);
        $this->assertEquals(Lexer::T_GROUP_CLOSE, $lexer->lookahead['type']);
    }

    public function test_group_nesting_error_still_open()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Opening group char "(" has no matching closing character');

        $lexer = new Lexer('(()');
    }

    public function test_group_nesting_error_closed_not_opened()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Closing group char "(" has no matching opening character');

        $lexer = new Lexer('())');
    }

    public function test_char_set_nesting_error()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage("Can't have a second character class while first remains open");

        $lexer = new Lexer('[[]]');
    }

    public function test_char_set_open_error()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage("Can't close a character class while none is open");

        $lexer = new Lexer(']');
    }

    public function test_char_set_open_not_closed()
    {
        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('Character Class that been closed');

        $lexer = new Lexer('[');
    }
}

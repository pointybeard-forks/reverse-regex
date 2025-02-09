<?php

declare(strict_types=1);

namespace Tests;

use pointybeard\ReverseRegex\Exception as RegexException;
use pointybeard\ReverseRegex\Generator\LiteralScope;
use pointybeard\ReverseRegex\Generator\Scope;
use pointybeard\ReverseRegex\Random\MersenneRandom;

class ScopeTest extends Basic
{
    public function test_scope_implements_repeat_interface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\RepeatInterface', $scope);
    }

    public function test_scope_implements_context_interface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\ContextInterface', $scope);
    }

    public function test_scope_extends_node()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\Node', $scope);
    }

    public function test_scope_implements_alternate_interface()
    {
        $scope = new Scope('scope1');
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\AlternateInterface', $scope);
    }

    public function test_alternate_interface()
    {
        $scope = new Scope('scope1');
        $this->assertFalse($scope->usingAlternatingStrategy());

        $scope->useAlternatingStrategy(true);
        $this->assertTrue($scope->usingAlternatingStrategy());
    }

    public function test_repeat_interface()
    {
        $scope = new Scope('scope1');

        $scope->setMaxOccurances(10);
        $scope->setMinOccurances(5);

        $this->assertEquals(10, $scope->getMaxOccurances());
        $this->assertEquals(5, $scope->getMinOccurances());
        $this->assertEquals(5, $scope->getOccuranceRange());
    }

    public function test_attach_child()
    {
        $scope = new Scope('scope1');
        $scope2 = new Scope('scope2');

        $scope->attach($scope2)->rewind();
        $this->assertEquals($scope2, $scope->current());
    }

    public function test_repeat_quota()
    {
        $gen = new MersenneRandom(703);

        $scope = new Scope('scope1');
        $scope->setMinOccurances(1);
        $scope->setMaxOccurances(6);

        $this->assertEquals(3, $scope->calculateRepeatQuota($gen));
    }

    public function test_generate_error_not_children()
    {
        $gen = new MersenneRandom(700);

        $scope = new Scope('scope1');
        $scope->setMinOccurances(1);
        $scope->setMaxOccurances(6);

        $result = '';

        $this->expectException(RegexException::class);
        $this->expectExceptionMessage('No child scopes to call must be atleast 1');

        $scope->generate($result, $gen);
    }

    public function test_generate()
    {
        $gen = new MersenneRandom(700);
        $result = '';

        $scope = new Scope('scope1');
        $scope->setMinOccurances(6);
        $scope->setMaxOccurances(6);

        $child = $this->getMockBuilder('pointybeard\ReverseRegex\Generator\Scope')->setMethods(['generate'])->getMock();

        $child->expects($this->exactly(6))
            ->method('generate')
            ->with($this->isType('string'), $this->equalTo($gen))
            ->will($this->returnCallback(function (&$sResult) {
                return $sResult .= 'a';
            }));

        $scope->attach($child);

        $result = $scope->generate($result, $gen);

        $this->assertEquals('aaaaaa', $result);
    }

    public function test_get_node()
    {
        $scope = new Scope('scope1');

        for ($i = 1; $i <= 6; $i++) {
            $scope->attach(new Scope('label_'.$i));
        }

        $other_scope = $scope->get(6);
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_6', $other_scope->getLabel());

        $other_scope = $scope->get(1);
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_1', $other_scope->getLabel());

        $other_scope = $scope->get(3);
        $this->assertInstanceOf('pointybeard\ReverseRegex\Generator\Scope', $other_scope);
        $this->assertEquals('label_3', $other_scope->getLabel());

        $other_scope = $scope->get(0);
        $this->assertEquals(null, $other_scope);
    }

    public function test_generate_with_alternating_strategy()
    {
        $scope = new Scope('scope1');
        $gen = new MersenneRandom(700);
        $result = '';

        $scope->setMinOccurances(7);
        $scope->setMaxOccurances(7);

        for ($i = 1; $i <= 6; $i++) {
            $lit = new LiteralScope('label_'.$i);
            $lit->addLiteral($i);
            $scope->attach($lit);
            $lit = null;
        }

        $scope->useAlternatingStrategy();
        $scope->generate($result, $gen);
        $this->assertMatchesRegularExpression('/[1-6]{7}/', $result);
    }
}

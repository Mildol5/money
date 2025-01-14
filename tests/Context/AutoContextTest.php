<?php

declare(strict_types=1);

namespace Brick\Money\Tests\Context;

use Brick\Math\Exception\RoundingNecessaryException;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CashContext;
use Brick\Money\Context\AutoContext;
use Brick\Money\Currency;
use Brick\Money\Tests\AbstractTestCase;

use Brick\Math\BigNumber;

/**
 * Tests for class AutoContext.
 */
class AutoContextTest extends AbstractTestCase
{
    /**
     * @dataProvider providerApplyTo
     */
    public function testApplyTo(string $amount, string $currency, RoundingMode $roundingMode, string $expected) : void
    {
        $amount = BigNumber::of($amount);
        $currency = Currency::of($currency);

        $context = new AutoContext();

        if ($this->isExceptionClass($expected)) {
            $this->expectException($expected);
        }

        $actual = $context->applyTo($amount, $currency, $roundingMode);

        if (! $this->isExceptionClass($expected)) {
            $this->assertBigDecimalIs($expected, $actual);
        }
    }

    public static function providerApplyTo() : array
    {
        return [
            ['1', 'USD', RoundingMode::UNNECESSARY, '1'],
            ['1.23', 'JPY', RoundingMode::UNNECESSARY, '1.23'],
            ['123/5000', 'EUR', RoundingMode::UNNECESSARY, '0.0246'],
            ['5/7', 'EUR', RoundingMode::UNNECESSARY, RoundingNecessaryException::class],
            ['5/7', 'EUR', RoundingMode::DOWN, \InvalidArgumentException::class]
        ];
    }

    public function testGetStep() : void
    {
        $context = new CashContext(5);
        self::assertSame(5, $context->getStep());
    }
}

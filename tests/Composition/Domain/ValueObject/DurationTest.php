<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\ValueObject;

use App\Composition\Domain\ValueObject\Duration;
use PHPUnit\Framework\TestCase;

final class DurationTest extends TestCase
{
    public function testBeats(): void
    {
        $this->assertSame(4.0, Duration::WHOLE->beats());
        $this->assertSame(2.0, Duration::HALF->beats());
        $this->assertSame(1.0, Duration::QUARTER->beats());
        $this->assertSame(0.5, Duration::EIGHTH->beats());
    }

    public function testValues(): void
    {
        $this->assertSame('whole', Duration::WHOLE->value);
        $this->assertSame('half', Duration::HALF->value);
        $this->assertSame('quarter', Duration::QUARTER->value);
        $this->assertSame('eighth', Duration::EIGHTH->value);
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Exercise\Domain\ValueObject;

use App\Exercise\Domain\ValueObject\Species;
use PHPUnit\Framework\TestCase;

final class SpeciesTest extends TestCase
{
    public function testAllSpeciesValues(): void
    {
        $this->assertSame('first', Species::FIRST->value);
        $this->assertSame('second', Species::SECOND->value);
        $this->assertSame('third', Species::THIRD->value);
        $this->assertSame('fourth', Species::FOURTH->value);
        $this->assertSame('fifth', Species::FIFTH->value);
    }

    public function testDescriptions(): void
    {
        $this->assertSame('Note against note', Species::FIRST->description());
        $this->assertSame('Florid counterpoint', Species::FIFTH->description());
    }
}

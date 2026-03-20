<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\ValueObject;

use App\Composition\Domain\ValueObject\Pitch;
use PHPUnit\Framework\TestCase;

final class PitchTest extends TestCase
{
    public function testAllPitchValues(): void
    {
        $this->assertSame('C', Pitch::C->value);
        $this->assertSame('D', Pitch::D->value);
        $this->assertSame('E', Pitch::E->value);
        $this->assertSame('F', Pitch::F->value);
        $this->assertSame('G', Pitch::G->value);
        $this->assertSame('A', Pitch::A->value);
        $this->assertSame('B', Pitch::B->value);
    }

    public function testSemitonesFromC(): void
    {
        $this->assertSame(0, Pitch::C->semitonesFromC());
        $this->assertSame(2, Pitch::D->semitonesFromC());
        $this->assertSame(4, Pitch::E->semitonesFromC());
        $this->assertSame(5, Pitch::F->semitonesFromC());
        $this->assertSame(7, Pitch::G->semitonesFromC());
        $this->assertSame(9, Pitch::A->semitonesFromC());
        $this->assertSame(11, Pitch::B->semitonesFromC());
    }

    public function testDiatonicIndex(): void
    {
        $this->assertSame(0, Pitch::C->diatonicIndex());
        $this->assertSame(1, Pitch::D->diatonicIndex());
        $this->assertSame(6, Pitch::B->diatonicIndex());
    }
}

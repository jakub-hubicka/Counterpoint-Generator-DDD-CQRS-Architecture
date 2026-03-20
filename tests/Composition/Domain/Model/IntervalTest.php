<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\Model;

use App\Composition\Domain\Model\Interval;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use PHPUnit\Framework\TestCase;

final class IntervalTest extends TestCase
{
    public function testUnison(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::C, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('unison', $interval->name());
        $this->assertSame(0, $interval->diatonicSteps());
        $this->assertTrue($interval->isPerfect());
        $this->assertTrue($interval->isConsonant());
        $this->assertFalse($interval->isDissonant());
        $this->assertFalse($interval->isLeap());
    }

    public function testSecond(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::D, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('second', $interval->name());
        $this->assertTrue($interval->isStep());
        $this->assertTrue($interval->isDissonant());
    }

    public function testThird(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::E, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('third', $interval->name());
        $this->assertTrue($interval->isConsonant());
        $this->assertTrue($interval->isLeap());
    }

    public function testFifth(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::G, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('fifth', $interval->name());
        $this->assertTrue($interval->isPerfect());
        $this->assertTrue($interval->isConsonant());
    }

    public function testOctave(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::C, 5, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('octave', $interval->name());
        $this->assertTrue($interval->isPerfect());
    }

    public function testSeventh(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::B, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('seventh', $interval->name());
        $this->assertTrue($interval->isDissonant());
        $this->assertTrue($interval->isLargerThanSixth());
    }

    public function testSixth(): void
    {
        $a = new Note(Pitch::C, 4, Duration::QUARTER);
        $b = new Note(Pitch::A, 4, Duration::QUARTER);
        $interval = new Interval($a, $b);

        $this->assertSame('sixth', $interval->name());
        $this->assertTrue($interval->isConsonant());
        $this->assertFalse($interval->isLargerThanSixth());
    }
}

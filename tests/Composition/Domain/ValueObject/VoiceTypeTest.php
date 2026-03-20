<?php

declare(strict_types=1);

namespace App\Tests\Composition\Domain\ValueObject;

use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;

final class VoiceTypeTest extends TestCase
{
    public function testSopranoRange(): void
    {
        $type = VoiceType::SOPRANO;
        $this->assertTrue($type->isInRange(new Note(Pitch::C, 4, Duration::QUARTER)));
        $this->assertTrue($type->isInRange(new Note(Pitch::A, 5, Duration::QUARTER)));
        $this->assertFalse($type->isInRange(new Note(Pitch::C, 3, Duration::QUARTER)));
    }

    public function testBassRange(): void
    {
        $type = VoiceType::BASS;
        $this->assertTrue($type->isInRange(new Note(Pitch::E, 2, Duration::QUARTER)));
        $this->assertTrue($type->isInRange(new Note(Pitch::E, 4, Duration::QUARTER)));
        $this->assertFalse($type->isInRange(new Note(Pitch::C, 5, Duration::QUARTER)));
    }

    public function testAltoRange(): void
    {
        $type = VoiceType::ALTO;
        $this->assertTrue($type->isInRange(new Note(Pitch::A, 3, Duration::QUARTER)));
        $this->assertFalse($type->isInRange(new Note(Pitch::E, 5, Duration::QUARTER)));
    }

    public function testTenorRange(): void
    {
        $type = VoiceType::TENOR;
        $this->assertTrue($type->isInRange(new Note(Pitch::C, 3, Duration::QUARTER)));
        $this->assertTrue($type->isInRange(new Note(Pitch::A, 4, Duration::QUARTER)));
        $this->assertFalse($type->isInRange(new Note(Pitch::C, 5, Duration::QUARTER)));
    }
}

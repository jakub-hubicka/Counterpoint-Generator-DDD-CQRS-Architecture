<?php

declare(strict_types=1);

namespace App\Composition\Domain\ValueObject;

enum VoiceType: string
{
    case SOPRANO = 'soprano';
    case ALTO = 'alto';
    case TENOR = 'tenor';
    case BASS = 'bass';

    public function minPitch(): Note
    {
        return match ($this) {
            self::SOPRANO => new Note(Pitch::C, 4, Duration::WHOLE),
            self::ALTO => new Note(Pitch::F, 3, Duration::WHOLE),
            self::TENOR => new Note(Pitch::C, 3, Duration::WHOLE),
            self::BASS => new Note(Pitch::E, 2, Duration::WHOLE),
        };
    }

    public function maxPitch(): Note
    {
        return match ($this) {
            self::SOPRANO => new Note(Pitch::A, 5, Duration::WHOLE),
            self::ALTO => new Note(Pitch::D, 5, Duration::WHOLE),
            self::TENOR => new Note(Pitch::A, 4, Duration::WHOLE),
            self::BASS => new Note(Pitch::E, 4, Duration::WHOLE),
        };
    }

    public function isInRange(Note $note): bool
    {
        $min = $this->minPitch();
        $max = $this->maxPitch();

        return $note->midiNumber() >= $min->midiNumber()
            && $note->midiNumber() <= $max->midiNumber();
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Composition\Application\Command;

use App\Composition\Application\Command\WriteNoteCommand;
use App\Composition\Application\Command\WriteNoteHandler;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Uid\Uuid;

final class WriteNoteHandlerTest extends TestCase
{
    public function testWriteNoteAddsNoteToVoice(): void
    {
        $voice = new Voice(Uuid::v7(), 'comp-1', VoiceType::SOPRANO);

        $repository = new class($voice) implements VoiceRepositoryInterface {
            public function __construct(private Voice $voice)
            {
            }

            public function save(Voice $voice): void
            {
            }

            public function findById(string $id): ?Voice
            {
                return $this->voice;
            }

            public function findByCompositionId(string $compositionId): array
            {
                return [$this->voice];
            }
        };

        $dispatcher = new EventDispatcher();
        $handler = new WriteNoteHandler($repository, $dispatcher);

        $command = new WriteNoteCommand(
            voiceId: $voice->id(),
            pitch: 'C',
            octave: 5,
            duration: 'quarter',
        );

        $handler($command);

        $this->assertSame(1, $voice->melody()->count());
    }

    public function testWriteNoteToNonExistentVoiceThrows(): void
    {
        $repository = new class implements VoiceRepositoryInterface {
            public function save(Voice $voice): void
            {
            }

            public function findById(string $id): ?Voice
            {
                return null;
            }

            public function findByCompositionId(string $compositionId): array
            {
                return [];
            }
        };

        $dispatcher = new EventDispatcher();
        $handler = new WriteNoteHandler($repository, $dispatcher);

        $command = new WriteNoteCommand(
            voiceId: 'nonexistent',
            pitch: 'C',
            octave: 5,
            duration: 'quarter',
        );

        $this->expectException(\InvalidArgumentException::class);
        $handler($command);
    }
}

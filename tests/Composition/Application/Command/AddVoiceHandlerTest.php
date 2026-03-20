<?php

declare(strict_types=1);

namespace App\Tests\Composition\Application\Command;

use App\Composition\Application\Command\AddVoiceCommand;
use App\Composition\Application\Command\AddVoiceHandler;
use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;
use App\Composition\Domain\ValueObject\VoiceType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class AddVoiceHandlerTest extends TestCase
{
    public function testAddVoiceCreatesAndPersistsVoice(): void
    {
        $repository = new class implements VoiceRepositoryInterface {
            public ?Voice $saved = null;

            public function save(Voice $voice): void
            {
                $this->saved = $voice;
            }

            public function findById(string $id): ?Voice
            {
                return $this->saved;
            }

            public function findByCompositionId(string $compositionId): array
            {
                return $this->saved ? [$this->saved] : [];
            }
        };

        $dispatcher = new EventDispatcher();
        $handler = new AddVoiceHandler($repository, $dispatcher);

        $command = new AddVoiceCommand(
            compositionId: 'comp-1',
            voiceType: 'soprano',
        );

        $voice = $handler($command);

        $this->assertNotNull($repository->saved);
        $this->assertSame('comp-1', $voice->compositionId());
        $this->assertSame(VoiceType::SOPRANO, $voice->voiceType());
    }
}

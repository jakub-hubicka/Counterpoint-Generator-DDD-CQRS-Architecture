<?php

declare(strict_types=1);

namespace App\Composition\Application\Command;

use App\Composition\Domain\Repository\VoiceRepositoryInterface;
use App\Composition\Domain\ValueObject\Duration;
use App\Composition\Domain\ValueObject\Note;
use App\Composition\Domain\ValueObject\Pitch;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class WriteNoteHandler
{
    public function __construct(
        private VoiceRepositoryInterface $voiceRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(WriteNoteCommand $command): void
    {
        $voice = $this->voiceRepository->findById($command->voiceId);

        if ($voice === null) {
            throw new \InvalidArgumentException(
                sprintf('Voice with id %s not found', $command->voiceId)
            );
        }

        $note = new Note(
            Pitch::from($command->pitch),
            $command->octave,
            Duration::from($command->duration),
        );

        $voice->addNote($note);
        $this->voiceRepository->save($voice);

        foreach ($voice->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}

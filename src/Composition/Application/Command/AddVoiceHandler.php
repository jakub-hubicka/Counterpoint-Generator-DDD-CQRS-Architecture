<?php

declare(strict_types=1);

namespace App\Composition\Application\Command;

use App\Composition\Domain\Model\Voice;
use App\Composition\Domain\Repository\VoiceRepositoryInterface;
use App\Composition\Domain\ValueObject\VoiceType;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final readonly class AddVoiceHandler
{
    public function __construct(
        private VoiceRepositoryInterface $voiceRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(AddVoiceCommand $command): Voice
    {
        $voiceType = VoiceType::from($command->voiceType);
        $voice = new Voice(Uuid::v7(), $command->compositionId, $voiceType);

        $this->voiceRepository->save($voice);

        foreach ($voice->pullDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $voice;
    }
}

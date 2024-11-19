<?php

declare(strict_types=1);

namespace App\Common\Service\Core;

use App\Common\Service\Core\Events\PreFlushEvent;

trait EventsTrait
{
    private array $recordedPreFlushEvents = [];
    private array $recordedPostFlushEvents = [];

    public function recordEvent(object $event): void
    {
        if ($event instanceof PreFlushEvent) {
            $this->recordedPreFlushEvents[] = $event;
        } else {
            // PostFlush is fallback case
            $this->recordedPostFlushEvents[] = $event;
        }
    }

    public function releaseAllEvents(): array
    {
        $events = [
            ...$this->recordedPreFlushEvents,
            ...$this->recordedPostFlushEvents,
        ];

        $this->recordedPreFlushEvents = [];
        $this->recordedPostFlushEvents = [];

        return $events;
    }

    public function releasePreFlushEvents(): array
    {
        $events = $this->recordedPreFlushEvents;

        $this->recordedPreFlushEvents = [];

        return $events;
    }

    public function releasePostFlushEvents(): array
    {
        $events = $this->recordedPostFlushEvents;

        $this->recordedPostFlushEvents = [];

        return $events;
    }
}

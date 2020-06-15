<?php

declare(strict_types=1);


namespace V1\StorageEngine\Event;

trait BaseEvent
{
    protected array $EventPool = [];

    public function On(string $event, \Closure $closure): bool
    {
        if(array_key_exists($event, $this->EventPool))
        {
            throw new \Exception("$event was registered");
        }
        $this->EventPool[$event] = $closure;
        return true;
    }

    public function Off(string $event): bool
    {
        if(!array_key_exists($event, $this->EventPool))
        {
            throw new \Exception("$event not be registered");
        }
        unset($this->EventPool[$event]);
        return true;
    }

    public function Trigger(string $event, array $params = []): void
    {
        if(!array_key_exists($event, $this->EventPool))
        {
            throw new \Exception("$event not be registered");
        }
        $closure = $this->EventPool[$event];
        $handler = new EventHandler();
        $handler->Event = $event;
        $handler->Target = $this;
        $closure($params, $handler);
    }
}
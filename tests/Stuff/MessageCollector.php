<?php

namespace Kavw\Psr15Middleware\Tests\Stuff;

final class MessageCollector
{
    /**
     * @var string[]
     */
    private array $messages = [];

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * @return string[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}

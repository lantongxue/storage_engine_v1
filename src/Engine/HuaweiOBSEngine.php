<?php

declare(strict_types=1);

namespace V1\StorageEngine\Engine;


use V1\StorageEngine\Entity\StreamBuffer;

class HuaweiOBSEngine extends BaseEngine
{

    public function ReadAsText(): string
    {
        // TODO: Implement ReadAsText() method.
    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {
        // TODO: Implement ReadAsStreamBuffer() method.
    }

    public function WriteText(string $content): int
    {
        // TODO: Implement WriteText() method.
    }

    public function WriteStream(StreamBuffer $buffer): int
    {
        // TODO: Implement WriteStream() method.
    }

    public function AppendText(string $content): int
    {
        // TODO: Implement AppendText() method.
    }

    public function AppendStream(StreamBuffer $buffer): int
    {
        // TODO: Implement AppendStream() method.
    }

    public function CopyTo(string $target): bool
    {
        // TODO: Implement CopyTo() method.
    }

    public function MoveTo(string $target): bool
    {
        // TODO: Implement MoveTo() method.
    }

    public function Delete(): bool
    {
        // TODO: Implement Delete() method.
    }
}
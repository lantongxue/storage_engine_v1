<?php


namespace V1\StorageEngine\Engine;


use Qcloud\Cos\Client;
use V1\StorageEngine\Entity\StreamBuffer;

class COSEngine extends BaseEngine
{
    public Client $Client;

    protected string $Bucket;

    protected function init()
    {
        $this->Bucket = $this->options['bucket'];
        unset($this->options['bucket']);
        $this->Client = new Client($this->options);
    }

    public function ReadAsText(): string
    {

    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {

    }

    public function WriteText(string $content): int
    {

    }

    public function WriteStream(StreamBuffer $buffer): int
    {

    }

    public function AppendText(string $content): int
    {

    }

    public function AppendStream(StreamBuffer $buffer): int
    {

    }

    public function CopyTo(string $target): bool
    {

    }

    public function MoveTo(string $target): bool
    {

    }

    public function Delete(): bool
    {

    }
}
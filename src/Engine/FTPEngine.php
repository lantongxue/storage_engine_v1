<?php


namespace V1\StorageEngine\Engine;


use V1\StorageEngine\Entity\StreamBuffer;

class FTPEngine extends BaseEngine
{
    protected object $ftpHandle;

    protected function init()
    {
        $ssl = $this->options['ssl'] ?? false;
        $timeout = $this->options['timeout'] ?? 90;
        $port = $this->options['port'] ?? 21;
        if($ssl)
        {
            $this->ftpHandle = ftp_ssl_connect($this->options['host'], $port, $timeout);
        }
        else
        {
            $this->ftpHandle = ftp_connect($this->options['host'], $port, $timeout);
        }
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
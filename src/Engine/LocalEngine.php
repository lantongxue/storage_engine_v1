<?php

declare(strict_types=1);

namespace V1\StorageEngine\Engine;

use V1\StorageEngine\Entity\FileInfo;
use V1\StorageEngine\Entity\StreamBuffer;

/**
 * 本地存储
 * Class LocalEngine
 * @package V1\StorageEngine\Engine
 */
class LocalEngine extends BaseEngine
{
    private function check_file_dir(string $file) : string
    {
        $dir = pathinfo($file, PATHINFO_DIRNAME);
        if(!file_exists($dir))
        {
            mkdir($dir, 0777, true);
        }
        return $file;
    }

    public function ReadAsText(): string
    {
        return file_get_contents($this->FileInfo->FullName);
    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {
        return StreamBuffer::FromFile($this->FileInfo);
    }

    public function WriteText(string $content): int
    {
        $bytes = file_put_contents($this->check_file_dir($this->FileInfo->FullName), $content);
        $bytes = $bytes === false ? 0 : $bytes;
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($content);
        return $bytes;
    }

    public function WriteStream(StreamBuffer $buffer): int
    {
        $fp = fopen($this->check_file_dir($this->FileInfo->FullName), 'wb');
        $bytes = fwrite($fp, $buffer->ToString());
        fclose($fp);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($buffer);
        return $bytes;
    }

    public function AppendText(string $content): int
    {
        $bytes = file_put_contents($this->check_file_dir($this->FileInfo->FullName), $content, FILE_APPEND);
        $bytes = $bytes === false ? 0 : $bytes;
        $newBytes = $this->FileInfo->Length + $bytes;
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $newBytes]);
        unset($content);
        return $bytes;
    }

    public function AppendStream(StreamBuffer $buffer): int
    {
        $fp = fopen($this->check_file_dir($this->FileInfo->FullName), 'ab');
        $bytes = fwrite($fp, $buffer->ToString());
        fclose($fp);
        $newBytes = $this->FileInfo->Length + $bytes;
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $newBytes]);
        unset($buffer);
        return $bytes;
    }

    public function CopyTo(string $target): bool
    {
        return copy($this->FileInfo->FullName, $this->check_file_dir($this->Root.'/'.$target));
    }

    public function MoveTo(string $target): bool
    {
        $result = $this->CopyTo($target) && $this->Delete();
        $this->FileInfo->Trigger(FileInfo::EVENT_MOVED, ['target' => $target, 'root' => $this->Root]);
        return $result;
    }

    public function Delete(): bool
    {
        return unlink($this->FileInfo->FullName);
    }
}
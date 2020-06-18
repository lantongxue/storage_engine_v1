<?php


namespace V1\StorageEngine\Engine;


use OSS\OssClient;
use V1\StorageEngine\Entity\FileInfo;
use V1\StorageEngine\Entity\StreamBuffer;
use V1\StorageEngine\Util\PathUtil;

class AliyunOSSEngine extends BaseEngine
{
    protected OssClient $ossClient;

    protected string $Bucket;

    protected function init()
    {
        $this->Bucket = $this->options['bucket'];
        $this->ossClient = new OssClient($this->options['accessKeyId'], $this->options['accessKeySecret'], $this->options['endPoint']);
    }

    public function AddFile(FileInfo $fileInfo): BaseEngine
    {
        parent::AddFile($fileInfo);
        $this->FileInfo->FullName = PathUtil::explodeKey($this->FileInfo->FullName);
        return $this;
    }

    protected function DownloadFile() : string
    {
        // 创建一个空白的临时文件到系统临时目录
        $localPath = tempnam(sys_get_temp_dir(), '_SE');
        try {
            $this->ossClient->getObject($this->Bucket, $this->FileInfo->FullName, [
                OssClient::OSS_FILE_DOWNLOAD => $localPath
            ]);
            return $localPath;
        }
        catch (\Exception $exception)
        {
            unlink($localPath); // 删除创建的空白临时文件
            throw $exception;
        }
    }

    public function ReadAsText(): string
    {
        $temp = $this->DownloadFile();
        $text = file_get_contents($temp);
        unlink($temp);
        return $text;
    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {
        $temp = $this->DownloadFile();
        $streamBuffer = StreamBuffer::FromFile(new FileInfo($temp, true));
        unlink($temp);
        return $streamBuffer;
    }

    public function WriteText(string $content): int
    {
        $this->ossClient->putObject($this->Bucket, $this->FileInfo->FullName, $content);
        $bytes = strlen($content);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($content);
        return $bytes;
    }

    public function WriteStream(StreamBuffer $buffer): int
    {
        $binaryText = $buffer->ToString();
        $this->ossClient->putObject($this->Bucket, $this->FileInfo->FullName, $binaryText);
        $bytes = strlen($binaryText);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($buffer);
        unset($binaryText);
        return $bytes;
    }

    public function AppendText(string $content): int
    {
        try {
            $content = $this->ReadAsText().$content;
            $this->ossClient->putObject($this->Bucket, $this->FileInfo->FullName, $content);
            $bytes = strlen($content);
            $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
            unset($content);
            return $bytes;
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    public function AppendStream(StreamBuffer $buffer): int
    {
        try {
            $streamBuffer = $this->ReadAsStreamBuffer();
            $streamBuffer->AppendHex($buffer->HexBuffer);
            $binaryText = $streamBuffer->ToString();
            $this->ossClient->putObject($this->Bucket, $this->FileInfo->FullName, $binaryText);
            $bytes = strlen($binaryText);
            $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
            unset($buffer);
            unset($streamBuffer);
            unset($binaryText);
            return $bytes;
        }
        catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    public function CopyTo(string $target): bool
    {
        try
        {
            $this->ossClient->copyObject($this->Bucket, $this->FileInfo->FullName, $this->Bucket, PathUtil::explodeKey($this->Root.'/'.$target));
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }

    public function MoveTo(string $target): bool
    {
        $result = $this->CopyTo($target) && $this->Delete();
        $this->FileInfo->Trigger(FileInfo::EVENT_MOVED, ['target' => $target, 'root' => $this->Root]);
        return $result;
    }

    public function Delete(): bool
    {
        try
        {
            $this->ossClient->deleteObject($this->Bucket, $this->FileInfo->FullName);
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}
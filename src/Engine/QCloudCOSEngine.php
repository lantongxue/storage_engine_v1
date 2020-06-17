<?php


namespace V1\StorageEngine\Engine;


use Qcloud\Cos\Client;
use V1\StorageEngine\Entity\StreamBuffer;
use V1\StorageEngine\Entity\FileInfo;

/**
 * 腾讯云COS
 * Class COSEngine
 * @package V1\StorageEngine\Engine
 */
class QCloudCOSEngine extends BaseEngine
{
    public Client $Client;

    protected string $Bucket;

    protected string $Region;

    protected function init()
    {
        $this->Bucket = $this->options['bucket'];
        $this->Region = $this->options['region'];
        unset($this->options['bucket']);
        $this->Client = new Client($this->options);
    }

    public function AddFile(FileInfo $fileInfo): BaseEngine
    {
        parent::AddFile($fileInfo);
        $this->FileInfo->FullName = Client::explodeKey($this->FileInfo->FullName);
        return $this;
    }

    protected function DownloadFile() : string
    {
        // 创建一个空白的临时文件到系统临时目录
        $localPath = tempnam(sys_get_temp_dir(), '_SE');
        try {
            $this->Client->getObject([
                'Bucket' => $this->Bucket,
                'Key' => $this->FileInfo->FullName,
                'SaveAs' => $localPath
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
        $this->Client->putObject([
            'Bucket' => $this->Bucket,
            'Key' => $this->FileInfo->FullName,
            'Body' => $content
        ]);
        $bytes = strlen($content);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        return $bytes;
    }

    public function WriteStream(StreamBuffer $buffer): int
    {
        $binaryText = $buffer->ToString();
        $this->Client->putObject([
            'Bucket' => $this->Bucket,
            'Key' => $this->FileInfo->FullName,
            'Body' => $binaryText
        ]);
        $bytes = strlen($binaryText);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($buffer);
        unset($binaryText);
        return $bytes;
    }

    public function AppendText(string $content): int
    {
        try
        {
            $text = $this->ReadAsText().$content;
        }
        catch (\Exception $exception)
        {
            $text = $content;
        }
        $this->Client->putObject([
            'Bucket' => $this->Bucket,
            'Key' => $this->FileInfo->FullName,
            'Body' => $text
        ]);
        $bytes = strlen($text);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        return $bytes;
    }

    public function AppendStream(StreamBuffer $buffer): int
    {
        try
        {
            $steamBuffer = $this->ReadAsStreamBuffer();
            $steamBuffer->AppendHex($buffer->HexBuffer);
        }
        catch (\Exception $exception)
        {
            $steamBuffer = $buffer;
        }
        $binaryText = $steamBuffer->ToString();
        $this->Client->putObject([
            'Bucket' => $this->Bucket,
            'Key' => $this->FileInfo->FullName,
            'Body' => $binaryText
        ]);
        $bytes = strlen($binaryText);
        $this->FileInfo->Trigger(FileInfo::EVENT_WRITE, ['bytes' => $bytes]);
        unset($steamBuffer);
        unset($buffer);
        unset($binaryText);
        return $bytes;
    }

    public function CopyTo(string $target): bool
    {
        try
        {
            $this->Client->copy($this->Bucket, $target, [
                'Region' => $this->Region,
                'Bucket' => $this->Bucket,
                'Key' => $this->FileInfo->FullName,
            ]);
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
            $this->Client->deleteObject([
                'Bucket' => $this->Bucket,
                'Key' => $this->FileInfo->FullName
            ]);
            return true;
        }
        catch (\Exception $exception)
        {
            return false;
        }
    }
}
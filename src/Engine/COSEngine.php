<?php


namespace V1\StorageEngine\Engine;


use Qcloud\Cos\Client;
use V1\StorageEngine\Entity\StreamBuffer;
use V1\StorageEngine\Entity\FileInfo;

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

    protected function DownloadFile()
    {
        // 创建一个空白的临时文件到系统临时目录
        $localPath = tempnam(sys_get_temp_dir(), '_SE_COS_TEMP_');
        try {
            $result = $this->Client->getObject([
                'Bucket' => $this->Bucket,
                'Key' => $this->FileInfo->FullName,
                'SaveAs' => $localPath
            ]);
            print_r($result);
        }
        catch (\Exception $exception)
        {
            unlink($localPath); // 删除创建的空白临时文件
            throw $exception;
        }
    }

    public function ReadAsText(): string
    {

    }

    public function ReadAsStreamBuffer(): StreamBuffer
    {
        $this->DownloadFile();
        return StreamBuffer::FromFile(new FileInfo('22'));
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
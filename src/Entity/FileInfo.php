<?php

declare(strict_types=1);

namespace V1\StorageEngine\Entity;

use V1\StorageEngine\Event\BaseEvent;
use V1\StorageEngine\Event\EventHandler;

class FileInfo
{
    use BaseEvent;

    /**
     * 文件名字（不带扩展名字）
     * @var mixed|string
     */
    public string $Name;

    /**
     * 文件完整路径
     * @var false|string
     */
    public string $FullName;

    /**
     * 文件类型（后缀）
     * @var mixed|string
     */
    public string $Extend;

    /**
     * 文件大小（字节）
     * @var false|int
     */
    public int $Length;

    /**
     * 文件
     * @var string
     */
    public string $File;

    const EVENT_WRITE = 'FileInfo::OnWrite';

    const EVENT_MOVED = 'FileInfo::OnMoved';

    public function __construct(string $file, bool $autoInitialization = false)
    {
        $this->File = $file;
        if($autoInitialization)
        {
            $this->init($file);
        }
        $this->On(self::EVENT_WRITE, $this->OnWrite());
        $this->On(self::EVENT_MOVED, $this->OnMoved());
    }

    /**
     * 获取当前文件特征
     * @return string MD5
     */
    public function Identity() : string
    {
        return md5_file($this->FullName);
    }

    public function init(string $root = '') : void
    {
        $file = $root.'/'.$this->File;
        $info = pathinfo($file);
        $this->Name = $info['filename'];
        $this->Extend = $info['extension'];

        if(!file_exists($file))
        {
            $this->Length = 0;
            $this->FullName = $file;
        }
        else
        {
            $this->Length = filesize($file);
            $this->FullName = realpath($file);
        }
    }

    public function OnWrite()
    {
        return function (array $params, EventHandler $event) {
            $this->Length = $params['bytes'];
        };
    }

    public function OnMoved()
    {
        return function (array $params, EventHandler $event) {
            $this->init($params['target']);
        };
    }

    public function __destruct()
    {
        $this->Off(self::EVENT_WRITE);
        $this->Off(self::EVENT_MOVED);
    }
}
<?php

declare(strict_types=1);

namespace V1\StorageEngine\Entity;

use V1\StorageEngine\Event\BaseEvent;
use V1\StorageEngine\Event\EventHandler;

class FileInfo
{
    use BaseEvent;

    /**
     * 文件名字
     * @var string
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

    /**
     * 路径
     * @var string
     */
    public string $Path;

    /**
     * 文件名（不带扩展名字）
     * @var string
     */
    public string $BaseName;

    /**
     * 是否存在
     * @var bool
     */
    public bool $Exists = false;

    const EVENT_WRITE = 'FileInfo::OnWrite';

    const EVENT_MOVED = 'FileInfo::OnMoved';

    public function __construct(string $file, bool $autoInitialization = false)
    {
        $this->File = $file;
        if($autoInitialization)
        {
            /**
             * 自动初始化的文件一定是一个存在且可以访问的文件
             */
            $this->init();
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
        if($root === '')
        {
            $file = $this->File;
        }
        else
        {
            $file = $root.'/'.$this->File;
        }
        $info = pathinfo($file);
        $this->Path = $info['dirname'];
        $this->Name = $info['basename'];
        $this->BaseName = $info['filename'];
        $this->Extend = $info['extension'];
        $this->Exists = file_exists($file);
        if(!$this->Exists)
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
            $this->Exists = true;
        };
    }

    public function OnMoved()
    {
        return function (array $params, EventHandler $event) {
            $this->File = $params['target'];
            $this->init($params['root']);
        };
    }

    public function __destruct()
    {
        $this->Off(self::EVENT_WRITE);
        $this->Off(self::EVENT_MOVED);
    }
}
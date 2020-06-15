<?php

declare(strict_types=1);

namespace V1\StorageEngine\Engine;


use V1\StorageEngine\Entity\FileInfo;
use V1\StorageEngine\Interfaces\IEngine;

abstract class BaseEngine implements IEngine
{

    public FileInfo $FileInfo;

    protected array $options;

    /**
     * 存储引擎根路径
     * @required
     * @var mixed|string
     */
    protected string $Root;

    public function __construct($options = [])
    {
        $this->options = $options;

        $this->Root = $this->options['root'];
        unset($this->options['root']);

        $this->init();
    }

    protected function init()
    {

    }

    public function AddFile(FileInfo $fileInfo) : BaseEngine
    {
        $fileInfo->init($this->Root);
        $this->FileInfo = $fileInfo;
        return $this;
    }
}
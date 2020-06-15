<?php

declare(strict_types=1);

namespace V1\StorageEngine;


use V1\StorageEngine\Engine\BaseEngine;
use V1\StorageEngine\Entity\FileInfo;

class StorageEngine
{
    /**
     * 已实例化的引擎池
     * [
     *      'Engine::class' => EngineObject
     * ]
     * @var array
     */
    private array $EnginePool = [];

    /**
     * 当前所使引擎
     * @var BaseEngine
     */
    public BaseEngine $Engine;

    public function __construct(string $engine, array $options = [])
    {
        $this->SwitchEngine($engine, $options);
    }

    /**
     * 实例化引擎对象
     * @param string $engine
     * @param array $options
     * @return BaseEngine
     */
    public static function Make(string $engine, array $options = []) : BaseEngine
    {
        return new $engine($options);
    }

    /**
     * 切换引擎
     * @param string $engine
     * @param array $options
     * @param bool $inherit
     * @return $this
     */
    public function SwitchEngine(string $engine, array $options = [], bool $inherit = true) : StorageEngine
    {
        /**
         * @var BaseEngine $oldEngine
         */
        $oldEngine = $this->Engine ?? null;

        if(array_key_exists($engine, $this->EnginePool))
        {
            $this->Engine = $this->EnginePool[$engine];
        }
        else
        {
            $this->Engine = self::Make($engine, $options);
            $this->EnginePool[$engine] = $this->Engine;
        }

        // 把上一个引擎的部分数据继承下去
        if($inherit && $oldEngine instanceof BaseEngine)
        {
            $this->Engine->FileInfo = $oldEngine->FileInfo;
        }

        return $this;
    }

    /**
     * 添加单个文件
     * @param FileInfo $fileInfo
     * @return $this
     */
    public function AddFile(FileInfo $fileInfo) : StorageEngine
    {
        $this->Engine->AddFile($fileInfo);
        return $this;
    }
}
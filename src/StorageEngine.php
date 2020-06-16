<?php

declare(strict_types=1);

namespace V1\StorageEngine;


use V1\StorageEngine\Engine\BaseEngine;
use V1\StorageEngine\Entity\FileInfo;

/**
 * Class StorageEngine
 * @package V1\StorageEngine
 * @method string ReadAsText()
 * @method \V1\StorageEngine\Entity\StreamBuffer ReadAsStreamBuffer()
 * @method int WriteText(string $content)
 * @method int WriteStream(\V1\StorageEngine\Entity\StreamBuffer $buffer)
 * @method int AppendText(string $content)
 * @method int AppendStream(\V1\StorageEngine\Entity\StreamBuffer $buffer)
 * @method bool CopyTo(string $target)
 * @method bool MoveTo(string $target)
 * @method bool Delete()
 */
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

    public function __call($name, $arguments)
    {
        if(method_exists($this->Engine, $name))
        {
            return call_user_func_array([$this->Engine, $name], $arguments);
        }
        else
        {
            throw new \Exception("Call undefined method {$name}", 500);
        }
    }
}
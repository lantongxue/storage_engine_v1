<?php

declare(strict_types=1);

namespace V1\StorageEngine;


use V1\StorageEngine\Engine\BaseEngine;
use V1\StorageEngine\Engine\FTPEngine;
use V1\StorageEngine\Engine\LocalEngine;
use V1\StorageEngine\Engine\QCloudCOSEngine;
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
    private array $EngineObjectPool = [];

    private static array $EnginePool = [
        'LocalEngine' => LocalEngine::class,
        'QCloudCOSEngine' => QCloudCOSEngine::class,
        'FTPEngine' => FTPEngine::class
    ];

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
        $engineObject = new $engine($options);
        if(!$engineObject instanceof BaseEngine)
        {
            throw new \Exception("$engine dose not inherit BaseEngine");
        }
        return $engineObject;
    }

    /**
     * 注册引擎
     * @param string $name
     * @param string $engine 引擎 class
     */
    public static function RegEngine(string $name, string $engine)
    {
        if(!array_key_exists($name, self::$EnginePool))
        {
            self::$EnginePool[$name] = $engine;
        }
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

        if(array_key_exists($engine, $this->EngineObjectPool))
        {
            $this->Engine = $this->EngineObjectPool[$engine];
        }
        else
        {
            $engineClass = self::$EnginePool[$engine] ?? false;
            if($engineClass === false)
            {
                throw new \Exception("$engine unregistered");
            }
            $this->Engine = self::Make($engineClass, $options);
            $this->EngineObjectPool[$engine] = $this->Engine;
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
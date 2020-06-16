<?php

declare(strict_types=1);

namespace V1\StorageEngine\Entity;


class StreamBuffer
{
    /**
     * 16进制流
     * @var array
     */
    public array $HexBuffer = [];

    /**
     * 10进制流
     * @var array
     */
    public array $DecBuffer = [];

    public function __construct(array $DecBuffer = [], array $HexBuffer = [])
    {
        if($DecBuffer !== [] && $HexBuffer !== [])
        {
            $decBuffer = $this->ConvertToDecBuffer($HexBuffer);
            $diff = array_diff($DecBuffer, $decBuffer);
            if(!empty($diff))
            {
                throw new \Exception('$DecBuffer & $HexBuffer data must be equality');
            }
            $this->DecBuffer = $DecBuffer;
            $this->HexBuffer = $HexBuffer;
        }
        if($DecBuffer !== [] && $HexBuffer === [])
        {
            $this->DecBuffer = $DecBuffer;
            $this->HexBuffer = $this->ConvertToHexBuffer($DecBuffer);
        }

        if($DecBuffer === [] && $HexBuffer !== [])
        {
            $this->HexBuffer = $HexBuffer;
            $this->DecBuffer = $this->ConvertToDecBuffer($HexBuffer);
        }

        unset($DecBuffer);
        unset($HexBuffer);
    }

    public static function FromFile(FileInfo $fileInfo) : StreamBuffer
    {
        $object = new self;
        if($fileInfo->Length === 0)
        {
            return $object;
        }
        $fp = fopen($fileInfo->FullName, 'rb');
        $object->BinaryText = fread($fp, $fileInfo->Length);
        fclose($fp);

        $object->DecBuffer = unpack('C*', $object->BinaryText);
        $object->HexBuffer = $object->ConvertToHexBuffer($object->DecBuffer);
        return $object;
    }

    public function ConvertToHexBuffer(array $decBuffer) : array
    {
        $hexBuffer = [];
        foreach ($decBuffer as $dec)
        {
            $hexBuffer[] = str_pad(dechex($dec), 2, '0', STR_PAD_LEFT); // 向左补0
        }
        return $hexBuffer;
    }

    public function ConvertToDecBuffer(array $hexBuffer) : array
    {
        $decBuffer = [];
        foreach ($hexBuffer as $hex)
        {
            $decBuffer[] = hexdec($hex);
        }
        return $decBuffer;
    }

    public function WriteDec(array $decBuffer) : int
    {
        $this->DecBuffer = $decBuffer;
        $this->HexBuffer = $this->ConvertToHexBuffer($decBuffer);
        $bytes = count($decBuffer);
        unset($decBuffer);
        return $bytes;
    }

    public function WriteHex(array $hexBuffer) : int
    {
        $this->HexBuffer = $hexBuffer;
        $this->DecBuffer = $this->ConvertToDecBuffer($hexBuffer);
        $bytes = count($hexBuffer);
        unset($hexBuffer);
        return $bytes;
    }

    public function AppendDec(array $decBuffer) : int
    {
        array_push($this->DecBuffer, ...$decBuffer);
        $this->HexBuffer = $this->ConvertToHexBuffer($this->DecBuffer);
        $bytes = count($decBuffer);
        unset($decBuffer);
        return $bytes;
    }

    public function AppendHex(array $hexBuffer) : int
    {
        array_push($this->HexBuffer, ...$hexBuffer);
        $this->DecBuffer = $this->ConvertToDecBuffer($this->HexBuffer);
        $bytes = count($hexBuffer);
        unset($hexBuffer);
        return $bytes;
    }

    public function ToString() : string
    {
        $binaryText = '';
        foreach ($this->HexBuffer as $hex)
        {
            $binaryText .= hex2bin($hex);
        }
        return $binaryText;
    }

    public function __destruct()
    {
        unset($this->HexBuffer);
        unset($this->DecBuffer);
    }
}
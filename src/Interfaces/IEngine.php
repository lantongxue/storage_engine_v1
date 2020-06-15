<?php

declare(strict_types=1);

namespace V1\StorageEngine\Interfaces;


use V1\StorageEngine\Entity\StreamBuffer;

interface IEngine
{
    public function ReadAsText() : string ;

    public function ReadAsStreamBuffer() : StreamBuffer ;

    public function WriteText(string $content) : int ;

    public function WriteStream(StreamBuffer $buffer) : int ;

    public function AppendText(string $content) : int ;

    public function AppendStream(StreamBuffer $buffer) : int ;

    public function CopyTo(string $target) : bool ;

    public function MoveTo(string $target) : bool ;

    public function Delete() : bool ;
}
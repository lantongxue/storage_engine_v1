<?php


namespace V1\StorageEngine\Interfaces;


interface IEvent
{
    public function On(string $event, \Closure $closure) : bool ;

    public function Off(string $event) : bool ;

    public function Trigger(string $event, array $params = []) : void ;
}
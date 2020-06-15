<?php
declare(strict_types=1);

$m1 = memory_get_usage();

require_once dirname(__FILE__, 2).'/vendor/autoload.php';

// LocalEngine demo
$engine = new \V1\StorageEngine\StorageEngine(
    \V1\StorageEngine\Engine\LocalEngine::class,
    $options = [
        'root' => dirname(__FILE__)
    ]
);

$fileInfo = new \V1\StorageEngine\Entity\FileInfo('test.txt');
$engine->AddFile($fileInfo);
$engine->Engine->ReadAsStreamBuffer();
$engine->Engine->WriteText('=>');
$engine->Engine->AppendStream(new \V1\StorageEngine\Entity\StreamBuffer([], ['74', '76']));
$engine->Engine->AppendText('你好');
print_r($fileInfo);

// COSEngine demo
//$engine = new \V1\StorageEngine\StorageEngine(
//    \V1\StorageEngine\Engine\COSEngine::class,
//    $options = [
//        'region' => 'ap-guangzhou',
//        'schema' => 'https',
//        'bucket' => 'sanyi-hubang-gd-1259744590', // required
//        'root' => '/v1/', // required
//        'credentials' => [
//            'appId' => 123456,
//            'secretId'  => 'secretId',
//            'secretKey' => 'secretKey'
//        ]
//    ]
//);
//$fileInfo = new \V1\StorageEngine\Entity\FileInfo('test2.txt');
//$engine->AddFile($fileInfo);
//$engine->Engine->ReadAsStreamBuffer();
//print_r($fileInfo);

$m2 = memory_get_usage();
echo "before memory usage $m1 bytes \n";
echo "after memory usage $m2 bytes \n";
<?php
declare(strict_types=1);

$m1 = memory_get_usage();

require_once dirname(__FILE__, 2).'/vendor/autoload.php';

// LocalEngine demo
// $engine = new \V1\StorageEngine\StorageEngine(
//     \V1\StorageEngine\Engine\LocalEngine::class,
//     $options = [
//         'root' => dirname(__FILE__) // required
//     ]
// );

// $fileInfo = new \V1\StorageEngine\Entity\FileInfo('test.txt', true);
// $engine->AddFile($fileInfo);
// $engine->ReadAsStreamBuffer();
// $engine->WriteText('=>');
// $engine->AppendStream(new \V1\StorageEngine\Entity\StreamBuffer([], ['74', '76']));
// $engine->AppendText('你好');
// print_r($fileInfo);

// COSEngine demo
//$engine = new \V1\StorageEngine\StorageEngine(
//   \V1\StorageEngine\Engine\COSEngine::class,
//   $options = [
//       'region' => '$region',
//       'schema' => 'https',
//       'bucket' => '$bucket', // required
//       'root' => '/', // required
//       'credentials' => [
//           'appId' => 123456,
//           'secretId'  => '$secretId',
//           'secretKey' => '$secretKey'
//       ]
//   ]
//);
//$fileInfo = new \V1\StorageEngine\Entity\FileInfo('test3.jpg');
//$engine->AddFile($fileInfo);
//$engine->ReadAsStreamBuffer();
//$engine->WriteText('nihao22');
//$engine->AppendStream(\V1\StorageEngine\Entity\StreamBuffer::FromFile(new \V1\StorageEngine\Entity\FileInfo('demo.jpg', true)));
//$engine->MoveTo('/aa.jpg');
//print_r($fileInfo);

// FTPEngine demo
$engine = new \V1\StorageEngine\StorageEngine(
    \V1\StorageEngine\Engine\FTPEngine::class,
    $options = [
        'root' => '/', // required
        'host' => '127.0.0.1',
        'port' => 21,
        'user' => '',
        'password' => '',
        'passive' => false, // 被动模式
        'ssl' => false, // ftps
        'timeout' => 90,
    ]
);
$fileInfo = new \V1\StorageEngine\Entity\FileInfo('test3.jpg');
$engine->AddFile($fileInfo);

$m2 = memory_get_usage();
echo "before memory usage $m1 bytes \n";
echo "after memory usage $m2 bytes \n";
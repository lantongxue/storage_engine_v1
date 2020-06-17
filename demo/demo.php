<?php
declare(strict_types=1);

$m1 = memory_get_usage();

require_once dirname(__FILE__, 2).'/vendor/autoload.php';

// LocalEngine demo
//$engine = new \V1\StorageEngine\StorageEngine(
// 'LocalEngine',
// $options = [
//     'root' => dirname(__FILE__) // required
// ]
//);
//
//$fileInfo = new \V1\StorageEngine\Entity\FileInfo('aa/bb/test.txt', true);
//$engine->AddFile($fileInfo);
//$engine->WriteText('=>');
//$engine->AppendStream(new \V1\StorageEngine\Entity\StreamBuffer([], ['74', '76']));
//$engine->AppendText('你好');
//$engine->CopyTo('cc/aa.txt');
//$engine->ReadAsStreamBuffer();

// COSEngine demo
//$engine = new \V1\StorageEngine\StorageEngine(
//   'QCloudCOSEngine',
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
//$engine = new \V1\StorageEngine\StorageEngine(
//    'FTPEngine',
//    $options = [
//        'root' => '/123/456', // required
//        'host' => '127.0.0.1', // required
//        'port' => 21,
//        'user' => 'anonymous', // if user empty then will trying anonymous ftp connection
//        'password' => '',
//        'passive' => false, // passive mode
//        'ssl' => false, // ftps
//        'timeout' => 90,
//    ]
//);
//$fileInfo = new \V1\StorageEngine\Entity\FileInfo('555/666/666.jpg');
//$engine->AddFile($fileInfo);
//$engine->WriteStream(\V1\StorageEngine\Entity\StreamBuffer::FromFile(new \V1\StorageEngine\Entity\FileInfo('demo.jpg', true)));
//
//$fileInfo = new \V1\StorageEngine\Entity\FileInfo('555/777/666.txt');
//$engine->AddFile($fileInfo);
//$engine->WriteText('hello');
//$engine->AppendText('555555555');
//$engine->CopyTo('bb/aa.jpg'); // copy /123/456/555/777/666.txt to /123/456/bb/aa.jpg

$m2 = memory_get_usage();
echo "before memory usage $m1 bytes \n";
echo "after memory usage $m2 bytes \n";
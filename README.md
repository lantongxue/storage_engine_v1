# V1存储引擎 #About

一款集成：阿里云OSS、腾讯云COS、FTP、本地、七牛KODO、华为云OBS、又拍云USS、UCloud、京东云OSS、网易云NOS、Amazon S3、Azure Blobs 等云商的对象存储SDK。
帮助开发者快速适配各类云存储，同时可以基于此构建一套完整、高可用的独立存储系统。

# 一些优点 #Features

1. 纯面向对象
2. 针对常驻内存做了优化（例如在`swoole`下使用）
3. 接口友好
4. 使用了最新的PHP语法，更加规范
5. 扩展或者适配其他云商真的超级方便

> 仅支持`PHP 7.4.0`及以上版本

# 安装 #Install
使用`composer`快速安装
```shell script
composer require v1/storage_engine
```

# 文档 #Document
先看demo里面的用法

# 已知问题以及解决办法 #Questions
**问题一：中文乱码，如何解决？**
>答：目前中文乱码问题仅在使用FTP引擎时有出现，解决办法就是提前将中文转换为GBK编码。

# 引擎适配情况 #Project Planning

- [x] 本地存储 Local
- [x] 阿里云 OSS
- [x] 腾讯云 COS
- [x] 华为云 OBS
- [ ] 七牛云 KODO
- [ ] 又拍云 USS
- [ ] 京东云 OSS
- [ ] 网易云 NOS
- [ ] UCloud UFile
- [ ] Amazon S3
- [ ] Azure Blobs
- [x] FTP Server

# 开源协议 #License
MIT
[![License](https://img.shields.io/github/license/yonna-framework/log.svg)](https://packagist.org/packages/yonna/log)
[![Repo Size](https://img.shields.io/github/repo-size/yonna-framework/log.svg)](https://packagist.org/packages/yonna/log)
[![Downloads](https://img.shields.io/packagist/dm/yonna/log.svg)](https://packagist.org/packages/yonna/log)
[![Version](https://img.shields.io/github/release/yonna-framework/log.svg)](https://packagist.org/packages/yonna/log)
[![Php](https://img.shields.io/packagist/php-v/yonna/log.svg)](https://packagist.org/packages/yonna/log)

## Yonna 日志库

```
日志包含文件、mongo、mysql三种类型
其中文件日志core库默认已经包含一部分官方日志
```

## 

#### 如何安装

##### 可以通过composer安装：`composer require yonna/log`

##### 可以通过git下载：`git clone https://github.com/yonna-framework/log.git`

> Yonna demo：[GOTO yonna](https://github.com/yonna-framework/yonna)

### Example

```php
<?php
    
    use Yonna\Log\File;
    
    // 记录日志的「目录」
    // 你可以在scope中的$request->cargo内获取到app的根
    $root = '/your_log_dir';
    
    $log = (new File($root));
    
    // 记录 Throwable 的日志
    $log->throwable($e);
    
    // 记录常规日志
    $msg = 'iam log';
    $data = [
        'a' => 1,    
        'b' => 2,    
        'c' => 3,    
    ];
    $log->error($msg,$data);
    $log->warning($msg,$data);
    $log->info($msg,$data);
    
?>
```

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=YNNLC9V28YDPN)

## Pure Ajax Upload And for Laravel 5 (Support jQuery-File-Upload, FileApi, Plupload)

[![StyleCI](https://styleci.io/repos/48772854/shield?style=flat)](https://styleci.io/repos/48772854)
[![Build Status](https://travis-ci.org/recca0120/upload.svg)](https://travis-ci.org/recca0120/upload)
[![Total Downloads](https://poser.pugx.org/recca0120/upload/d/total.svg)](https://packagist.org/packages/recca0120/upload)
[![Latest Stable Version](https://poser.pugx.org/recca0120/upload/v/stable.svg)](https://packagist.org/packages/recca0120/upload)
[![Latest Unstable Version](https://poser.pugx.org/recca0120/upload/v/unstable.svg)](https://packagist.org/packages/recca0120/upload)
[![License](https://poser.pugx.org/recca0120/upload/license.svg)](https://packagist.org/packages/recca0120/upload)
[![Monthly Downloads](https://poser.pugx.org/recca0120/upload/d/monthly)](https://packagist.org/packages/recca0120/upload)
[![Daily Downloads](https://poser.pugx.org/recca0120/upload/d/daily)](https://packagist.org/packages/recca0120/upload)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/recca0120/upload/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/recca0120/upload/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/recca0120/upload/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/recca0120/upload/?branch=master)

## Features

- Support Chunks [jQuery-File-Upload](https://github.com/blueimp/jQuery-File-Upload) $driver = 'fileapi';
- Support Chunks [Dropzone](https://fineuploader.com/) $driver = 'dropzone';
- Support Chunks [FileApi](http://mailru.github.io/FileAPI/) $driver = 'fileapi';
- Support Chunks [Fine Uploader](https://fineuploader.com/) $driver = 'fine-uploader';
- Support Chunks [Plupload](https://www.plupload.com/) $driver = 'plupload';
- Support Chunks [Filepond](https://github.com/pqina/filepond) $driver = 'filepond'

## Installing

To get the latest version of Laravel Exceptions, simply require the project using [Composer](https://getcomposer.org):

```bash
composer require recca0120/upload
```

## Laravel

publish

```php
artisan vendor:publish --provider="Recca0120\Upload\UploadServiceProvider"
```

## How to use

Controller

```php

use Illuminate\Http\JsonResponse;
use Recca0120\Upload\UploadManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadController extends Controller
{
    public function upload(UploadManager $manager)
    {
        $driver = 'plupload'; // or 'fileapi'
        $inputName = 'file'; // $_FILES index;

        return $manager->driver($driver)->receive($inputName);

        // or
        return $manager
            ->driver($driver)
            ->receive($inputName, function (UploadedFile $uploadedFile, $path, $domain, $api) {
                $clientOriginalName = $uploadedFile->getClientOriginalName();
                $clientOriginalExtension = strtolower($uploadedFile->getClientOriginalExtension());
                $basename = pathinfo($uploadedFile->getBasename(), PATHINFO_FILENAME);
                $filename = md5($basename).'.'.$clientOriginalExtension;
                $mimeType = $uploadedFile->getMimeType();
                $size = $uploadedFile->getSize();
                $uploadedFile->move($path, $filename);

                return new JsonResponse([
                    'name' => pathinfo($clientOriginalName, PATHINFO_FILENAME).'.'.$clientOriginalExtension,
                    'tmp_name' => $path.$filename,
                    'type' => $mimeType,
                    'size' => $size,
                    'url' => $domain.$path.$filename,
                ]);
            });
    }
}
```

## Factory

```php
use Recca0120\Upload\Receiver;
use Illuminate\Http\JsonResponse;

require __DIR__.'/vendor/autoload.php';

$config = [
    'chunks' => 'path_to_chunks',
    'storage' => 'path_to_storage',
    'domain' => 'http://app.dev/',
    'path' => 'web_path'
];

$inputName = 'file';
$storagePath = 'relative path';
$api = 'fileapi'; // or plupload

Receiver::factory($config, $api)->receive($inputName)->send();
```

## Standalone

```php

use Recca0120\Upload\Receiver;
use Recca0120\Upload\FileAPI;
use Recca0120\Upload\Plupload;
use Illuminate\Http\JsonResponse;

require __DIR__.'/vendor/autoload.php';

$config = [
    'chunks' => 'path_to_chunks',
    'storage' => 'path_to_storage',
    'domain' => 'http://app.dev/',
    'path' => 'web_path'
];

$inputName = 'file';
$storagePath = 'relative path';

// if use Plupload, new Recca0120\Upload\Plupload
$receiver = new Receiver(new FileAPI($config));
// save to $config['base_path'].'/'.$storagePath;
$receiver->receive($inputName)->send();
```

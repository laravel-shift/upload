<?php

namespace Recca0120\Upload\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Recca0120\Upload\Filesystem;
use ReflectionClass;
use ReflectionException;

abstract class TestCase extends BaseTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var Request
     */
    protected $request;

    protected $uploadedFile;

    protected $root;

    protected $config;

    protected $files;

    protected $uuid;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuid = uniqid('upload-', true);

        $this->root = vfsStream::setup('root', null, [
            'chunks' => [],
            'storage' => [],
        ]);

        $this->config = [
            'chunks' => $this->root->url().'/chunks',
            'storage' => $this->root->url().'/storage',
        ];

        $this->uploadedFile = UploadedFile::fake()->image('test.png');

        $this->request = Request::createFromGlobals();
        $this->request->files->replace(['foo' => $this->uploadedFile]);

        $this->files = new Filesystem();
    }

    /**
     * @throws ReflectionException
     */
    protected function chunkUpload(int $chunks, ?callable $callback): void
    {
        $content = $this->uploadedFile->getContent();
        $size = $this->uploadedFile->getSize();
        $remainder = $size % $chunks;
        $chunkSize = ($size - $remainder) / $chunks;
        $totalCount = (int) ceil($size / $chunkSize);

        $offset = 0;
        for ($i = 0; $i < $chunks; $i++) {
            $this->setRequestContent(substr($content, $offset, $chunkSize));
            if (is_callable($callback)) {
                $callback($offset, $chunkSize, $i, $totalCount);
            }
            $offset = (($i + 1) * $chunkSize);
        }
        if ($remainder > 0) {
            $this->setRequestContent(substr($content, $offset, $remainder));
            if (is_callable($callback)) {
                $callback($offset, $remainder, $i, $totalCount);
            }
        }
        $this->setRequestContent($content);
    }

    /**
     * @throws ReflectionException
     */
    protected function setRequestContent(string $content): void
    {
        $reflectedClass = new ReflectionClass($this->request);
        $reflection = $reflectedClass->getProperty('content');
        $reflection->setAccessible(true);
        $reflection->setValue($this->request, $content);
        file_put_contents($this->uploadedFile->getRealPath(), $content);
    }
}

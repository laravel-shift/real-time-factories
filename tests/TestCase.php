<?php

namespace Tests;

use Blueprint\RealTimeFactoriesServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function fixture(string $path)
    {
        return file_get_contents(__DIR__.'/'.'fixtures'.'/'.ltrim($path, '/'));
    }

    public function stub(string $path)
    {
        return file_get_contents(__DIR__.'/../'.'stubs'.'/'.ltrim($path, '/'));
    }

    protected function getPackageProviders($app)
    {
        return [
            RealTimeFactoriesServiceProvider::class,
        ];
    }
}

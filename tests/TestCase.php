<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Before;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    #[Before]
    public function ensureSupportedTestingDriver(): void
    {
        if (env('DB_CONNECTION') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Extension pdo_sqlite belum aktif di environment ini, sehingga test database tidak bisa dijalankan.');
        }
    }
}

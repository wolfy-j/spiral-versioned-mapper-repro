<?php

declare(strict_types=1);

namespace Tests;

use Spiral\Console\Console;

/**
 * @mixin BaseTest
 */
trait WithMigrations
{
    /**
     * Setup user and account.
     */
    protected function setUpMigrations()
    {
        /** @var Console $console */
        $console = $this->app->get(Console::class);
        $console->run('migrate:init');
        $console->run('migrate');
//        $console->run('cycle');
    }
}

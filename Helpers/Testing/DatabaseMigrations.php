<?php

namespace Modules\Core\Helpers\Testing;

use Illuminate\Contracts\Console\Kernel;

trait DatabaseMigrations
{
    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */
    public function runDatabaseMigrations()
    {
        $this->artisan('migrate');
        $this->artisan('module:migrate');

        $this->app[Kernel::class]->setArtisan(null);

        $this->beforeApplicationDestroyed(function () {
            $this->artisan('module:migrate-rollback');
            $this->artisan('migrate:rollback');
        });
    }
}

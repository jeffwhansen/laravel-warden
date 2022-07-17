<?php

namespace Jeffwhansen\Warden\Commands;

use Illuminate\Console\Command;

class WardenCommand extends Command
{
    public $signature = 'laravel-warden';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

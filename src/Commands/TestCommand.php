<?php

namespace JamesGifford\LaravelArchitecture\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'jg:test';
    protected $description = 'Test that the James Gifford Laravel Architecture package is installed correctly.';

    public function handle(): int
    {
        $this->info('James Gifford Laravel Architecture package is wired in and responding.');

        return self::SUCCESS;
    }
}

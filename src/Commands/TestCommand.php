<?php

namespace JamesGifford\LaravelArchitecture\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'laravel-architecture:test';
    protected $description = 'Test that the Laravel Architecture package by James Gifford is installed correctly.';

    public function handle(): int
    {
        $this->info('Laravel Architecture package by James Gifford is wired in and responding.');

        return self::SUCCESS;
    }
}

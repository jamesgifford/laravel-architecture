<?php

namespace JamesGifford\LaravelArchitecture\Commands\MakeControllerUnit;

use JamesGifford\LaravelArchitecture\Scaffolds\CreateControllerUnit\CreateControllerUnitScaffold;
use JamesGifford\LaravelArchitecture\Support\Commands\Command;

class MakeControllerUnitCommand extends Command
{
    // TODO: Make type optional
    // TODO: Enforce set values for type (page, data, fragment)
    protected $signature = 'make:unit:controller {name : The base name of the unit (e.g. CreateWebsite)} {type? : The type of controller (page, data, fragment}';
    protected $description = 'Create a Controller Unit';

    public function __construct(
        protected CreateControllerUnitScaffold $scaffold,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $response = ($this->scaffold)(
            name: $this->argument('name'),
            type: $this->argument('type'), // TODO: Establish default
        );

        $this->info(sprintf('Controller unit [%s] created at: %s', $response->unitName, $response->unitPath));

        if (! $response->createdDirector) {
            $this->warn('  - Controller already existed');
        }

        if (! $response->createdRequest) {
            $this->warn('  - Request already existed');
        }

        if (! $response->createdResponse) {
            $this->warn('  - Response already existed');
        }

        return self::SUCCESS;
    }
}

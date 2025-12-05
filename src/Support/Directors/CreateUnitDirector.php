<?php

namespace JamesGifford\LaravelArchitecture\Support\Directors;

use Illuminate\Filesystem\Filesystem;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;
use RuntimeException;

/**
 * Base Director for "create a unit" operations.
 *
 * @template-extends Director<RequestTransferInterface, ResponseTransferInterface>
 */
abstract class CreateUnitDirector extends Director
{
    public function __construct(
        protected Filesystem $files,
        protected string $rootNamespace,
    ) {
    }

    /**
     * Helper to write the main class file.
     */
    protected function writeDirectorClass(
        string $unitPath,
        string $namespace,
        string $class,
        string $stubName,
        array $extra = [],
    ): void {
        $filePath = sprintf('%s/%s.php', $unitPath, $class);

        if ($this->files->exists($filePath)) {
            return;
        }

        $stubFile = sprintf('%s/%s', 'unit', $stubName);
        $stub = $this->getStub($stubFile);

        $replacements = array_merge([
            '{{ namespace }}'     => $namespace,
            '{{ rootNamespace }}' => rtrim($this->rootNamespace, '\\'),
            '{{ class }}'         => $class,
        ], $extra);

        $content = $this->replacePlaceholders($stub, $replacements);

        $this->ensureDirectory($unitPath);

        $this->files->put($filePath, $content);
    }

    protected function writeRequestClass(
        string $unitPath,
        string $namespace,
        string $class,
        string $stubName,
    ): void {
        $filePath = sprintf('%s/%s.php', $unitPath, $class);

        if ($this->files->exists($filePath)) {
            return;
        }

        $stubFile = sprintf('%s/%s', 'unit', $stubName);
        $stub = $this->getStub($stubFile);

        $content = $this->replacePlaceholders($stub, [
            '{{ namespace }}' => $namespace,
            '{{ class }}' => $class,
        ]);

        $this->ensureDirectory($unitPath);

        $this->files->put($filePath, $content);
    }

    protected function writeResponseClass(
        string $unitPath,
        string $namespace,
        string $class,
        string $stubName,
    ): void {
        $filePath = sprintf('%s/%s.php', $unitPath, $class);

        if ($this->files->exists($filePath)) {
            return;
        }

        $stubFile = sprintf('%s/%s', 'unit', $stubName);
        $stub = $this->getStub($stubFile);

        $content = $this->replacePlaceholders($stub, [
            '{{ namespace }}' => $namespace,
            '{{ class }}'  => $class,
        ]);

        $this->ensureDirectory($unitPath);

        $this->files->put($filePath, $content);
    }

    protected function ensureDirectory(string $unitPath): void
    {
        if (! $this->files->exists($unitPath)) {
            $this->files->makeDirectory($unitPath, 0755, true);
        }
    }

    protected function getStub(string $name): string
    {
        $stubPath = __DIR__.'/../../../../stubs/'. $name . '.stub';

        if (! $this->files->exists($stubPath)) {
            throw new RuntimeException(sprintf('Stub not found: %s', $stubPath));
        }

        return $this->files->get($stubPath);
    }

    protected function replacePlaceholders(string $stub, array $replacements): string
    {
        return str_replace(array_keys($replacements), array_values($replacements), $stub);
    }
}

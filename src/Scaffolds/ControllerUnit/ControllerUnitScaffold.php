<?php

namespace JamesGifford\LaravelArchitecture\Scaffolds\ControllerUnit;

use Illuminate\Support\Str;
use JamesGifford\LaravelArchitecture\Support\Directors\CreateUnitDirectorAbstract;
use JamesGifford\LaravelArchitecture\Support\Transfers\RequestTransferInterface;
use JamesGifford\LaravelArchitecture\Support\Transfers\ResponseTransferInterface;

/**
 * Director that creates a Controller Unit on disk.
 *
 * @template-extends CreateUnitDirectorAbstract<ControllerUnitRequest, ControllerUnitResponse>
 */
class ControllerUnitScaffold extends CreateUnitDirectorAbstract
{
    /**
     * @param  ControllerUnitRequest $request
     * @return ControllerUnitResponse
     */
    protected function handle(RequestTransferInterface $request): ResponseTransferInterface
    {
        $unitType = Str::ucfirst($request->type);
        $unitName = Str::studly($request->name);

        $unitPath = app_path(sprintf('Http/Controllers/Web/%s/%s', $unitType, $unitName)); // TODO: Use constants/config for the path
        $namespace = sprintf('%s\\Http\\Controllers\\Web\\%s\\%s',
            rtrim($this->rootNamespace, '\\'),
            $unitType,
            $unitName,
        ); // TODO: Use constants/config for the namespace

        // TODO: Use constants/config for the file names
        $directorClass = $unitName . 'Controller';
        $requestClass = $unitName . 'Request';
        $responseClass = $unitName . 'Response';

        $createdDirector = false;
        $createdRequest = false;
        $createdResponse = false;

        if (! $this->files->exists($unitPath)) {
            $this->ensureDirectory($unitPath);
        }

        if (! $this->files->exists(sprintf('%s/%s.php', $unitPath, $directorClass))) {
            $this->writeDirectorClass(
                unitPath: $unitPath,
                namespace: $namespace,
                class: $directorClass,
                stubName: 'controller',
                extra: [
                    '{{ unitName }}' => $unitName,
                ],
            );

            $createdDirector = true;
        }

        if (! $this->files->exists(sprintf('%s/%s.php', $unitPath, $requestClass))) {
            $this->writeRequestClass(
                unitPath: $unitPath,
                namespace: $namespace,
                class: $requestClass,
                stubName: 'request',
            );

            $createdRequest = true;
        }

        if (! $this->files->exists(sprintf('%s/%s.php', $unitPath, $responseClass))) {
            $this->writeResponseClass(
                unitPath: $unitPath,
                namespace: $namespace,
                class: $responseClass,
                stubName: 'response',
            );

            $createdResponse = true;
        }

        return ControllerUnitResponse::build(
            unitName: $unitName,
            unitPath: $unitPath,
            createdDirector: $createdDirector,
            createdRequest: $createdRequest,
            createdResponse: $createdResponse,
        );
    }
}

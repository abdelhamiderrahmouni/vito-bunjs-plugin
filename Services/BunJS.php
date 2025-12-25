<?php

namespace App\Vito\Plugins\Abdelhamiderrahmouni\VitoBunJsPlugin\Services;

use App\Exceptions\SSHError;
use App\Services\AbstractService;
use Closure;
use Illuminate\Validation\Rule;

class BunJS extends AbstractService
{
    public static function id(): string
    {
        return 'bunjs';
    }

    public static function type(): string
    {
        return 'bunjs';
    }

    public function unit(): string
    {
        return '';
    }

    public function creationRules(array $input): array
    {
        return [
            'type' => [
                function (string $attribute, mixed $value, Closure $fail): void {
                    $exists = $this->service->server->service('bunjs');
                    if ($exists) {
                        $fail('You already have Bun installed on the server.');
                    }
                },
            ],
            'version' => [
                'required',
                Rule::in(config('service.services.bunjs.versions')),
                Rule::unique('services', 'version')
                    ->where('type', 'bunjs')
                    ->where('server_id', $this->service->server_id),
            ],
        ];
    }

    public function deletionRules(): array
    {
        return [
            'service' => [
                function (string $attribute, mixed $value, Closure $fail): void {
                    $hasSite = $this->service->server->sites()
                        ->where('type', 'bunjs')
                        ->exists();
                    if ($hasSite) {
                        $fail('Some sites are using Bun.');
                    }
                },
            ],
        ];
    }

    /**
     * @throws SSHError
     */
    public function install(): void
    {
        $server = $this->service->server;

        $server->ssh()->exec(
            view('vitodeploy-bunjs::install-bun', [
                'version' => $this->service->version,
            ]),
            'install-bun-'.$this->service->version
        );

        event('service.installed', $this->service);

        $this->service->server->os()->cleanup();
    }

    /**
     * @throws SSHError
     */
    public function uninstall(): void
    {
        $this->service->server->ssh()->exec(
            view('vitodeploy-bunjs::uninstall-bun'),
            'uninstall-bun'
        );

        event('service.uninstalled', $this->service);

        $this->service->server->os()->cleanup();
    }

    public function version(): string
    {
        $version = $this->service->server->ssh()->exec(
            'bun --version'
        );

        return trim($version);
    }
}

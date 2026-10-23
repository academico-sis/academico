<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;
use Spatie\Multitenancy\Contracts\IsTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

/**
 * Each tenant keeps its files in its own folder, storage/<storage_folder>/app/private and app/public.
 * The tenant of a single-tenant install (no storage folder) keeps the configured folders.
 */
class SwitchTenantFilesystemsTask implements SwitchTenantTask
{
    /**
     * @var array<string, string>
     */
    private array $configuredRoots;

    public function __construct()
    {
        $this->configuredRoots = [
            'local' => config('filesystems.disks.local.root'),
            'public' => config('filesystems.disks.public.root'),
        ];
    }

    public function makeCurrent(IsTenant $tenant): void
    {
        $folder = $tenant instanceof Tenant ? $tenant->storage_folder : null;

        $this->useRoots($folder ? [
            'local' => storage_path("{$folder}/app/private"),
            'public' => storage_path("{$folder}/app/public"),
        ] : $this->configuredRoots);
    }

    public function forgetCurrent(): void
    {
        $this->useRoots($this->configuredRoots);
    }

    /**
     * @param  array<string, string>  $roots
     */
    private function useRoots(array $roots): void
    {
        config([
            'filesystems.disks.local.root' => $roots['local'],
            'filesystems.disks.public.root' => $roots['public'],
            'filesystems.disks.public.url' => rtrim((string) config('app.url'), '/').'/storage',
        ]);

        Storage::forgetDisk(['local', 'public']);
    }
}

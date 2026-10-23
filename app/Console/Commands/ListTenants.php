<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Console\Command;

class ListTenants extends Command
{
    protected $signature = 'academico:tenant:list';

    protected $description = 'List the institutions with their database, files folder and domains';

    public function handle(): int
    {
        $this->table(['ID', 'Name', 'Database', 'Files', 'Domains (primary first)'], Tenant::query()->with('domains')->orderBy('id')->get()->map(fn (Tenant $tenant): array => [
            $tenant->id,
            $tenant->name,
            $tenant->database,
            $tenant->storage_folder ? storage_path($tenant->storage_folder) : storage_path('app'),
            $tenant->domains->sortByDesc('is_primary')->map(fn (TenantDomain $domain): string => $domain->domain)->implode(', '),
        ])->all());

        return Command::SUCCESS;
    }
}

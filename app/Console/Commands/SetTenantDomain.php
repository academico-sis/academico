<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SetTenantDomain extends Command
{
    protected $signature = 'academico:tenant:domain
        {tenant : ID or name of the institution}
        {domain : Host name, such as school.example.org}
        {--primary : Make it the primary domain, used in links and emails}
        {--remove : Remove the domain}';

    protected $description = 'Add, make primary or remove a domain of an institution';

    public function handle(): int
    {
        $tenant = Tenant::query()->whereIn('id', [$this->argument('tenant')])->orWhere('name', $this->argument('tenant'))->first();
        $domain = Str::lower(trim((string) $this->argument('domain')));

        if (! $tenant) {
            $this->error("No institution matches {$this->argument('tenant')}.");

            return Command::FAILURE;
        }

        $existing = $tenant->domains()->where('domain', $domain)->first();

        if ($this->option('remove')) {
            if (! $existing) {
                $this->error("{$domain} is not a domain of {$tenant->name}.");

                return Command::FAILURE;
            }

            $existing->delete();
            $this->info("{$domain} no longer leads to {$tenant->name}.");

            return Command::SUCCESS;
        }

        if (! $existing) {
            $validator = Validator::make(['domain' => $domain], [
                'domain' => ['max:253', 'regex:/^([a-z0-9]([a-z0-9-]*[a-z0-9])?\.)+[a-z0-9]([a-z0-9-]*[a-z0-9])?$/', Rule::unique(TenantDomain::class, 'domain')],
            ]);

            if ($validator->fails()) {
                $this->error($validator->errors()->first());

                return Command::FAILURE;
            }

            $existing = $tenant->domains()->create(['domain' => $domain]);
        }

        if ($this->option('primary') || $tenant->domains()->count() === 1) {
            DB::connection($tenant->getConnectionName())->transaction(function () use ($tenant, $existing): void {
                // The domain itself is left out: a model already primary would not save true again
                $tenant->domains()->whereKeyNot($existing->getKey())->update(['is_primary' => false]);
                $existing->update(['is_primary' => true]);
            });
        }

        $this->info("{$domain} leads to {$tenant->name}.");

        return Command::SUCCESS;
    }
}

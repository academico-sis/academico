<?php

namespace App\Traits;

use App\Models\Tenant;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * For commands that work on the data of an institution: they run once for each tenant, or for the tenants
 * given with --tenant (ID or name), each one current in turn. A failure is reported and the command goes
 * on with the next tenant. The command signature declares the option:
 *
 *     {--tenant=* : Only run for these tenants (ID or name)}
 */
trait RunsForEachTenant
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $names = (array) $this->option('tenant');

        $tenants = Tenant::query()
            ->when($names !== [], fn ($query) => $query->whereIn('id', $names)->orWhereIn('name', $names))
            ->orderBy('id')
            ->get();

        if ($tenants->isEmpty()) {
            $this->error($names === [] ? 'There is no tenant yet.' : 'No tenant matches '.implode(', ', $names).'.');

            return self::FAILURE;
        }

        $status = self::SUCCESS;

        foreach ($tenants as $tenant) {
            try {
                $status = max($status, (int) $tenant->execute(fn () => $this->laravel->call([$this, 'handle'])));
            } catch (Throwable $exception) {
                report($exception);
                $this->error("Tenant {$tenant->id} ({$tenant->name}): {$exception->getMessage()}");
                $status = self::FAILURE;
            }
        }

        return $status;
    }
}

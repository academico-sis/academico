<?php

use App\Models\Tenant;
use App\Services\Tenancy\ApplyTenantSettingsTask;
use App\Services\Tenancy\DomainTenantFinder;
use App\Services\Tenancy\SwitchTenantDatabaseTask;
use App\Services\Tenancy\SwitchTenantFilesystemsTask;
use App\Services\Tenancy\SwitchTenantPermissionCacheTask;
use App\Services\Tenancy\SwitchTenantUrlTask;
use App\Services\Tenancy\TagErrorReportsTask;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\CallQueuedClosure;
use Spatie\Multitenancy\Actions\ForgetCurrentTenantAction;
use Spatie\Multitenancy\Actions\MakeQueueTenantAwareAction;
use Spatie\Multitenancy\Actions\MakeTenantCurrentAction;
use Spatie\Multitenancy\Actions\MigrateTenantAction;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Spatie\Multitenancy\Jobs\TenantAware;
use Spatie\Multitenancy\Tasks\PrefixCacheTask;

return [

    /*
    |--------------------------------------------------------------------------
    | Landlord database
    |--------------------------------------------------------------------------
    |
    | The database holding the tenants and their domains. When it is empty,
    | the landlord tables live in the application database: the install has a
    | single tenant, which is used whatever the host name. When it is set,
    | each tenant has its own database and is found from the request host.
    |
    */

    'landlord_database' => env('LANDLORD_DB_DATABASE'),

    /*
     * Tenant used for hosts that match no domain, in a multi-tenant install.
     */
    'default_tenant' => env('TENANCY_DEFAULT_TENANT'),

    'tenant_finder' => DomainTenantFinder::class,

    /*
     * Behind Traefik, its HTTP provider can read the routers of every tenant
     * domain from /tenancy/traefik, sending the token as a bearer token. The
     * endpoint is disabled while the token is empty.
     */
    'traefik' => [
        'token' => env('TRAEFIK_PROVIDER_TOKEN'),
        'service_url' => env('TRAEFIK_SERVICE_URL', 'http://academico:80'),
        'cert_resolver' => env('TRAEFIK_CERT_RESOLVER', 'letsencrypt'),
        'https_entrypoint' => env('TRAEFIK_HTTPS_ENTRYPOINT', 'websecure'),
        'http_entrypoint' => env('TRAEFIK_HTTP_ENTRYPOINT', 'web'),
    ],

    'tenant_artisan_search_fields' => [
        'id',
        'name',
    ],

    /*
     * Run in this order when a tenant becomes current: the URL comes before
     * the filesystems (the public disk URL uses it), the settings come last.
     */
    'switch_tenant_tasks' => [
        SwitchTenantDatabaseTask::class,
        PrefixCacheTask::class,
        SwitchTenantPermissionCacheTask::class,
        SwitchTenantUrlTask::class,
        SwitchTenantFilesystemsTask::class,
        TagErrorReportsTask::class,
        ApplyTenantSettingsTask::class,
    ],

    'tenant_model' => Tenant::class,

    'queues_are_tenant_aware_by_default' => true,

    /*
     * The tenant database is the default connection, whose database is
     * switched. The landlord connection is a copy of the default one that
     * uses the landlord database (see TenancyServiceProvider).
     */
    'tenant_database_connection_name' => null,

    'landlord_database_connection_name' => env('LANDLORD_DB_CONNECTION', 'landlord'),

    'current_tenant_context_key' => 'tenantId',

    'current_tenant_container_key' => 'currentTenant',

    'shared_routes_cache' => false,

    'actions' => [
        'make_tenant_current_action' => MakeTenantCurrentAction::class,
        'forget_current_tenant_action' => ForgetCurrentTenantAction::class,
        'make_queue_tenant_aware_action' => MakeQueueTenantAwareAction::class,
        'migrate_tenant' => MigrateTenantAction::class,
    ],

    'queueable_to_job' => [
        SendQueuedMailable::class => 'mailable',
        SendQueuedNotifications::class => 'notification',
        CallQueuedClosure::class => 'closure',
        CallQueuedListener::class => 'class',
        BroadcastEvent::class => 'event',
    ],

    'tenant_aware_interface' => TenantAware::class,

    'not_tenant_aware_interface' => NotTenantAware::class,

    'tenant_aware_jobs' => [],

    'not_tenant_aware_jobs' => [],

];

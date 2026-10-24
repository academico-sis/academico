<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Sentry\Event;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Tests\InteractsWithTenants;
use Tests\TestCase;

class TraefikConfigTest extends TestCase
{
    use InteractsWithTenants;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'multitenancy.traefik.token' => 'secret-token',
            'multitenancy.traefik.service_url' => 'http://academico-web:80',
        ]);
    }

    public function test_the_endpoint_is_disabled_without_token(): void
    {
        config(['multitenancy.traefik.token' => null]);

        $this->get('/tenancy/traefik')->assertNotFound();
        $this->withToken('')->get('/tenancy/traefik')->assertNotFound();
    }

    public function test_the_endpoint_needs_the_token(): void
    {
        $this->get('/tenancy/traefik')->assertNotFound();
        $this->withToken('wrong-token')->get('/tenancy/traefik')->assertNotFound();
    }

    public function test_each_institution_gets_a_router_for_its_domains(): void
    {
        $this->useSeveralTenants();
        $first = $this->tenantWithSettings('first');
        $first->domains()->create(['domain' => 'first.example.org', 'is_primary' => true]);
        $first->domains()->create(['domain' => 'www.first.example.org']);
        $second = $this->tenantWithSettings('second');
        $second->domains()->create(['domain' => 'second.example.org', 'is_primary' => true]);
        $this->tenantWithSettings('without-domain');

        $response = $this->withToken('secret-token')->get('/tenancy/traefik')->assertSuccessful();

        $response->assertJsonPath("http.routers.academico-{$first->id}", [
            'rule' => 'Host(`first.example.org`) || Host(`www.first.example.org`)',
            'entryPoints' => ['websecure'],
            'service' => 'academico',
            'tls' => ['certResolver' => 'letsencrypt'],
        ]);
        $response->assertJsonPath("http.routers.academico-{$first->id}-http.middlewares", ['academico-https']);
        $response->assertJsonPath("http.routers.academico-{$second->id}.rule", 'Host(`second.example.org`)');
        $response->assertJsonPath('http.services.academico.loadBalancer.servers.0.url', 'http://academico-web:80');
        $this->assertStringNotContainsString('\\/', $response->getContent(), 'Traefik reads the body as YAML, where \\/ is not a valid escape.');
        $response->assertJsonPath('http.middlewares.academico-https.redirectScheme.scheme', 'https');
        $this->assertCount(4, $response->json('http.routers'));
    }

    public function test_host_names_that_could_change_the_rule_are_left_out(): void
    {
        $tenant = Tenant::sole();
        $tenant->domains()->delete();
        $tenant->domains()->create(['domain' => 'school.example.org']);
        DB::table('tenant_domains')->insert(['tenant_id' => $tenant->id, 'domain' => 'evil.example.org`) || PathPrefix(`/']);

        $rule = $this->withToken('secret-token')->get('/tenancy/traefik')->json("http.routers.academico-{$tenant->id}.rule");

        $this->assertSame('Host(`school.example.org`)', $rule);
    }

    public function test_error_reports_carry_the_tenant(): void
    {
        $this->assertSame((string) Tenant::sole()->id, $this->sentryTags()['tenant'] ?? null);

        Tenant::forgetCurrent();

        $this->assertArrayNotHasKey('tenant', $this->sentryTags());
    }

    /**
     * @return array<string, string>
     */
    private function sentryTags(): array
    {
        $tags = [];
        SentrySdk::getCurrentHub()->configureScope(function (Scope $scope) use (&$tags): void {
            $tags = $scope->applyToEvent(Event::createEvent())?->getTags() ?? [];
        });

        return $tags;
    }
}

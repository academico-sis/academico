<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantDomain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Dynamic configuration for Traefik's HTTP provider: a router for the domains of each institution, with an
 * HTTPS certificate from the configured resolver, and a redirection from HTTP. A new institution or domain
 * is served once Traefik polls this endpoint again, without changing the Traefik configuration.
 *
 * Only answers requests carrying the configured token (Authorization: Bearer <token>).
 */
class TraefikConfigController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $config = config('multitenancy.traefik');

        abort_if(blank($config['token']) || ! hash_equals($config['token'], (string) $request->bearerToken()), 404);

        $routers = [];

        foreach (Tenant::query()->with('domains')->orderBy('id')->get() as $tenant) {
            $rule = $tenant->domains
                ->pluck('domain')
                ->filter(fn (string $domain): bool => (bool) preg_match(TenantDomain::PATTERN, $domain))
                ->map(fn (string $domain): string => "Host(`{$domain}`)")
                ->implode(' || ');

            if ($rule === '') {
                continue;
            }

            $routers["academico-{$tenant->id}"] = [
                'rule' => $rule,
                'entryPoints' => [$config['https_entrypoint']],
                'service' => 'academico',
                'tls' => ['certResolver' => $config['cert_resolver']],
            ];
            $routers["academico-{$tenant->id}-http"] = [
                'rule' => $rule,
                'entryPoints' => [$config['http_entrypoint']],
                'service' => 'academico',
                'middlewares' => ['academico-https'],
            ];
        }

        // Traefik reads the body as YAML, which does not accept the escaped slashes of JSON
        return response()->json(['http' => [
            'routers' => (object) $routers,
            'middlewares' => ['academico-https' => ['redirectScheme' => ['scheme' => 'https', 'permanent' => true]]],
            'services' => ['academico' => ['loadBalancer' => ['servers' => [['url' => $config['service_url']]]]]],
        ]], options: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

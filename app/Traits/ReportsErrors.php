<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Sentry\State\Scope;

trait ReportsErrors
{
    protected function reportError(\Throwable $e, string $context, array $extra = []): void
    {
        Log::error("[{$context}] {$e->getMessage()}", array_merge([
            'exception' => $e::class,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], $extra));

        if (function_exists('\Sentry\withScope')) {
            \Sentry\withScope(function (Scope $scope) use ($e, $context, $extra): void {
                $scope->setTag('context', $context);
                foreach ($extra as $key => $value) {
                    $scope->setExtra($key, $value);
                }
                \Sentry\captureException($e);
            });
        }
    }
}

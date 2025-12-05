<?php

namespace App\Http\Middleware;

use App\Models\ProductKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProductKeyIsValid
{
    protected array $exceptRoutes = [
        'product-key.*',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldBypass($request)) {
            return $next($request);
        }

        if ($this->hasValidProductKey()) {
            return $next($request);
        }

        return redirect()->route('product-key.index')->with('error', 'Please activate your product key to continue.');
    }

    protected function shouldBypass(Request $request): bool
    {
        if ($request->route()?->getName() && $request->routeIs(...$this->exceptRoutes)) {
            return true;
        }

        if ($request->is('storage/*') || $request->is('assets/*')) {
            return true;
        }

        return false;
    }

    protected function hasValidProductKey(): bool
    {
        $value = config('app.product_key');

        if (blank($value)) {
            return false;
        }

        return ProductKey::where('product_key', $value)
            ->where('is_active', true)
            ->exists();
    }
}


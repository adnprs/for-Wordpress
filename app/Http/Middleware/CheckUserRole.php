<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * Supported route middleware usage examples:
     * - role:admin
     * - role:editor
     * - role:operator
     * - role:review   (admin/editor)
     * - role:push     (admin/editor)
     * - role:generate (admin/editor/operator)
     */
    public function handle(Request $request, Closure $next, string ...$requirements): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401, 'Unauthenticated.');
        }

        $userRole = $user->role;

        if ($userRole === 'admin') {
            return $next($request);
        }

        if ($requirements === []) {
            return $next($request);
        }

        foreach ($requirements as $requirement) {
            if ($this->isAllowed($userRole, $requirement)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to perform this action.');
    }

    protected function isAllowed(string $userRole, string $requirement): bool
    {
        $requirement = strtolower(trim($requirement));

        $capabilityMap = [
            'review' => ['editor'],
            'push' => ['editor'],
            'generate' => ['editor', 'operator'],
        ];

        if (isset($capabilityMap[$requirement])) {
            return in_array($userRole, $capabilityMap[$requirement], true);
        }

        $hierarchy = [
            'operator' => 1,
            'editor' => 2,
            'admin' => 3,
        ];

        if (! isset($hierarchy[$userRole], $hierarchy[$requirement])) {
            return false;
        }

        return $hierarchy[$userRole] >= $hierarchy[$requirement];
    }
}

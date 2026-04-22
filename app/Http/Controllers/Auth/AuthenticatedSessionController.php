<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Redirect based on user role
        return redirect()->route($this->getDashboardRoute(Auth::user()));
    }

    /**
     * Get the appropriate dashboard route based on user role
     */
    private function getDashboardRoute($user): string
    {
        if ($user->isSuperAdmin()) {
            return 'superadmin.dashboard';
        } elseif ($user->isFinanceOfficer()) {
            return 'finance.dashboard';
        } elseif ($user->isAdmin()) {
            return 'admin.dashboard';
        } elseif ($user->isExamOfficer()) {
            return 'exam.dashboard';
        } elseif ($user->isProprietor()) {
            return 'proprietor.dashboard';
        } else {
            // Default for staff and any other roles
            return 'staff.dashboard';
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

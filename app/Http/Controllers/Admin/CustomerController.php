<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('role', UserRole::CUSTOMER)
            ->withCount('orders')
            ->latest()
            ->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user): View
    {
        abort_unless($user->isCustomer(), 404);

        $user->load(['orders' => fn ($query) => $query->latest()->with('items')]);

        return view('admin.customers.show', ['customer' => $user]);
    }
}

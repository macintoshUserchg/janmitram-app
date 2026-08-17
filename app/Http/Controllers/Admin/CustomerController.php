<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\ShopPasswordResetRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    use SortableIndex;

    public function index(Request $request)
    {
        $allowedColumns = ['id', 'name', 'phone', 'email', 'gender', 'date_of_birth', 'created_at'];
        [$sort, $direction] = $this->resolveSort($allowedColumns, 'id', 'desc');

        $query = User::role(Roles::CUSTOMER->value)->with('media');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $this->applySort($query, $sort, $direction, $allowedColumns)
            ->paginate($this->resolvePerPage(20))
            ->withQueryString();

        return view('admin.customer.index', compact('customers', 'sort', 'direction'));
    }

    public function create()
    {
        return view('admin.customer.create');
    }

    public function store(RegistrationRequest $request)
    {
        // Create a new user
        $user = UserRepository::registerNewUser($request);

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        return to_route('admin.customer.index')->withSuccess(__('Created successfully'));
    }

    public function edit(User $user)
    {
        return view('admin.customer.edit', compact('user'));
    }

    public function update(User $user, UserRequest $request)
    {
        UserRepository::updateByRequest($request, $user);

        return to_route('admin.customer.index')->withSuccess(__('Updated successfully'));
    }

    public function destroy(User $user)
    {
        if ($user->orders()->exists()) {
            return back()->withError(__('Cannot delete customer with order history'));
        }

        if ($user->profile_photo_path) {
            Storage::delete($user->profile_photo_path);
        }

        $user->delete();

        return back()->withSuccess(__('Deleted successfully'));
    }

    public function resetPassword(User $user, ShopPasswordResetRequest $request)
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->withSuccess(__('Password reset successfully'));
    }
}

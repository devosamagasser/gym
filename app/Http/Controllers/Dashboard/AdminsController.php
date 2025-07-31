<?php
namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;

class AdminsController extends Controller
{
    public function index()
    {
        $admins = Admin::paginate();

        return ApiResponse::success($admins);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admins,email',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'sometimes|in:super_admin,admin',
                'is_active' => 'sometimes|boolean',
            ]);

            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => $data['role'] ?? 'admin',
                'is_active' => $data['is_active'] ?? true,
            ]);

            $admin->assignRole($admin->role);

            return ApiResponse::created($admin);
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function show(Admin $admin)
    {
        return ApiResponse::success($admin);
    }

    public function update(Request $request, Admin $admin)
    {
        try {
            $data = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:admins,email,' . $admin->id,
                'password' => 'sometimes|required|string|min:8|confirmed',
                'role' => 'sometimes|in:super_admin,admin',
                'is_active' => 'sometimes|boolean',
            ]);

            $admin->update($data);

            if (array_key_exists('role', $data)) {
                $admin->syncRoles([$data['role']]);
            }

            return ApiResponse::updated($admin);
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            $admin->delete();

            return ApiResponse::deleted();
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }
}

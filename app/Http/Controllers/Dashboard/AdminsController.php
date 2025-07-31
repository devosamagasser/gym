<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Admins\AdminStoreRequest;
use App\Http\Requests\Dashboard\Admins\AdminUpdateRequest;

class AdminsController extends Controller
{
    public function index()
    {
        $limit = request()->query('limit', 10);
        $admins = Admin::filter(request()->all())->paginate($limit);
        return ApiResponse::success($admins);
    }

    public function store(AdminStoreRequest $request)
    {
        try {
            $data = $request->validated();
            $admin = Admin::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => '12345678',
            ])->assignRole('admin');

            return ApiResponse::created($admin);
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function show(Admin $admin)
    {
        return ApiResponse::success($admin);
    }

    public function update(AdminUpdateRequest $request, Admin $admin)
    {
        try {
            $data = $request->validated();
            if (!isset($data['password'])) {
                unset($data['password']);
            }
            $admin->update($data);

            return ApiResponse::updated($admin);
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage() ?: 'An error occurred while processing, please try again.');
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

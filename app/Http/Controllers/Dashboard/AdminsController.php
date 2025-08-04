<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Admin;
use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Dashboard\Admins\AdminStoreRequest;
use App\Http\Requests\Dashboard\Admins\AdminUpdateRequest;

class AdminsController extends Controller
{
    public function index()
    {
        if (! auth()->user()->hasRole('super_admin')) {
            return ApiResponse::forbidden();
        }
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

    public function show(string $id)
    {
        try{
            $admin = Admin::findOrFail($id);
            return ApiResponse::success($admin);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }

    public function update(AdminUpdateRequest $request, string $id)
    {
        try {
            $admin = Admin::findOrFail($id);
            $data = $request->validated();
            if (!isset($data['password'])) {
                unset($data['password']);
            }
            $admin->update($data);

            return ApiResponse::updated($admin);
        }catch (ModelNotFoundException $e) {
            return ApiResponse::notFound('Admin not found.');
        } catch (\Exception $e) {
            return ApiResponse::serverError($e->getMessage() ?: 'An error occurred while processing, please try again.');
        }
    }

    public function destroy(string $id)
    {
        try {
            if (! auth()->user()->hasRole('super_admin')) {
                return ApiResponse::forbidden();
            }
            $admin = Admin::findOrFail($id);
            $admin->delete();
            return ApiResponse::deleted();
        } catch (\Exception $e) {
            return ApiResponse::serverError('An error occurred while processing, please try again.');
        }
    }
}

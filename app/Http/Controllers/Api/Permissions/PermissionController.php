<?php

namespace App\Http\Controllers\API\Permissions;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Routing\Controller;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['store']]);
        $this->middleware('permission:update permission', ['only' => ['update']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }


    public function index(): JsonResponse
    {
        $user = Auth::user();
        if ($user->isActive  == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }

        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $permissions = Permission::all();
            return response()->json($permissions);
        }
        return response()->json(
            ['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '],
            401,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function show(Permission $permission): JsonResponse
    {
        return response()->json($permission);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);
        $user = Auth::user();

        if ($user->isActive == 0) {
            return response()->json(
                ['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'],
                401,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }

        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $permission = Permission::create([
                'name' => $request->name
            ]);
            return response()->json([
                'message' => 'Permission created successfully',
                'permission' => $permission
            ], 201);
        }
        return response()->json(
            ['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '],
            401,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update(Request $request, Permission $permission): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name,' . $permission->id
        ]);
        $user = Auth::user();
        if ($user->isActive == 0) {
            return response()->json(
                ['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'],
                401,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $permission->update([
                'name' => $request->name
            ]);

            return response()->json(
                [
                    'message' => 'Permission updated successfully',
                    'permission' => $permission
                ],
                201,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }
        return response()->json(
            ['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '],
            401,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function destroy(Permission $permission): JsonResponse
    {
        $user = Auth::user();
        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }

        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $permission->delete();


            return response()->json(
                [
                    'message' => 'Permission deleted successfully'
                ],
                201,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }
        return response()->json(
            ['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '],
            401,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }
}

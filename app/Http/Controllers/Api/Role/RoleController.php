<?php

namespace App\Http\Controllers\API\Role;

use App\Helpers\ApiResponseHelper;
use Illuminate\Routing\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['store', 'addPermissionToRole', 'givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['update']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index(): JsonResponse
    {
        $user = Auth::user();
        // $user = User::where('id', auth()->id())->first();

        // Specify the guard explicitly when checking roles
        if (
            $user->hasRole('super-admin', 'api') ||
            $user->hasRole('admin', 'api') ||
            $user->hasRole('staff', 'api')
        ) {
            $roles = Role::where('guard_name', 'api')->get();

            return ApiResponseHelper::success($roles);
        }

        return ApiResponseHelper::error('Unauthorized', 403);
    }

    public function show(Role $role): JsonResponse
    {
        return response()->json(
            [$role],
            200,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name'
        ]);
        $user = Auth::user();
        //  $user = User::where('id', auth()->id())->first();
        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }

        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $role = Role::create([
                'name' => $request->name
            ]);
            return response()->json(
                [
                    'message' => 'Role created successfully',
                    'role' => $role
                ],
                201,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }
        return response()->json(['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '], 401);
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id
        ]);
        $user = Auth::user();
        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $role->update([
                'name' => $request->name
            ]);


            return response()->json(
                [
                    'message' => 'Role updated successfully',
                    'role' => $role
                ],
                201,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }
        return response()->json(['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '], 401);
    }


    public function destroy(Role $role): JsonResponse
    {
        $user = Auth::user();
        //  $user = User::where('id', auth()->id())->first();
        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $role->delete();

            return response()->json([
                'message' => 'Role deleted successfully'
            ]);
        }
        return response()->json(['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '], 401);
    }


    public function addPermissionToRole(Role $role): JsonResponse
    {
        $user = Auth::user();
        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }
        if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
            $permissions = Permission::all();
            $rolePermissions = DB::table('role_has_permissions')
                ->where('role_id', $role->id)
                ->pluck('permission_id')
                ->all();

            return response()->json([
                'role' => $role,
                'permissions' => $permissions,
                'rolePermissions' => $rolePermissions
            ]);
        }
        return response()->json(['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '], 401);
    }

    public function givePermissionToRole(Request $request, Role $role): JsonResponse
    {
        $request->validate([
            'permission' => 'required|array'
        ]);

        $user = Auth::user();

        if ($user->isActive == 0) {
            return response()->json(['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'], 401);
        }

        if ($user->hasRole('super-admin', 'api') || $user->hasRole('admin', 'api') || $user->hasRole('staff', 'api')) {
            $permissions = Permission::whereIn('name', $request->permission)
                ->where('guard_name', $role->guard_name)
                ->get();

            $role->syncPermissions($permissions);

            return response()->json(
                [
                    'message' => 'Permissions added to role'
                ],
                201,
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            );
        }

        return response()->json(['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '], 401);
    }
}

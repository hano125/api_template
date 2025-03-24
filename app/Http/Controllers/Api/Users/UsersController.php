<?php

namespace App\Http\Controllers\API\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view user', ['only' => ['index']]);
        $this->middleware('permission:create user', ['only' => ['store']]);
        $this->middleware('permission:update user', ['only' => ['update']]);
        $this->middleware('permission:delete user', ['only' => ['destroy']]);
    }

    public function index(Request $request): JsonResponse
    {
        // Get the 'type' parameter from the request body
        $type = $request->input('type', null); // Default is null if not provided

        // Determine the 'isActive' value based on the 'type' parameter
        $users = User::select('id', 'name', 'email', 'isActive')
            ->when($type === 'active', function ($query) {
                return $query->where('isActive', true);  // Filter active users
            })
            ->when($type === 'blocked', function ($query) {
                return $query->where('isActive', false);  // Filter inactive users
            })
            ->with('roles:name')->get();

        $users->transform(function ($user) {
            $user->role_name = $user->roles->first()->name; // Get the first role name
            unset($user->roles); // Remove the roles array
            return $user;
        });

        return response()->json(
            [
                'success' => true,
                'message' => 'كل المستخدمين',
                'data' => $users
            ],
            200,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(
            [$user],
            200,
            ['Content-Type' => "application/json; charset=UTF-8"],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:20',
            'role_name' => 'required',
            // 'isActive' => 'required|'

        ]);
        try {
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
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    // 'isActive'=>$request->isActive
                ]);

                $user->syncRoles($request->role_name);



                $data = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role_name' => $user->roles->first()->name
                ];


                return response()->json(
                    [
                        'success' => true,
                        'message' => "تم خلق الحساب بنجاح",
                        'data' => $data
                    ],
                    201,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE

                );
            }
            return response()->json(['message:the user not auth']);
        } catch (\Exception $e) {
            // Return an error response if something went wrong
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => false
            ], 500); // 500 Internal Server Error
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $authUser = Auth::user();
            if ($authUser->isActive == 0) {
                return response()->json(
                    ['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'],
                    401,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            }
            if (!$authUser->hasRole(['super-admin', 'admin'])) {
                return response()->json(
                    ['message' => 'ليس لديك صلاحية لإجراء هذه العملية'],
                    403,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            }
            $user = User::findOrFail($id);
            // Validate incoming data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $id,
                //'password' => 'nullable|string|min:8|max:20',
                'role_name' => 'required|string', // Single role as string
            ]);
            // Hash the password if provided
            if ($request->has('password')) {
                $validatedData['password'] = Hash::make($request->password);
                // Update user with validated data
                $user->update($validatedData);

                // Assign the new role (assuming it's a single role and not an array)
                $user->syncRoles([]); // Remove all current roles
                $user->assignRole($validatedData['role_name']); // Assign the single new role

                // Log action (optional)


                return response()->json(
                    [
                        'success' => true,
                        'message' => 'تم تعديل الحساب بنجاح',
                        'data' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role_name' => $validatedData['role_name'], // Assuming role_name is part of request
                        ]
                    ],
                    201,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            } else {
                $user->update($validatedData);
                $user->syncRoles([]); // Remove all current roles
                $user->assignRole($validatedData['role_name']); // Assign the single new role

                return response()->json(
                    [
                        'success' => true,
                        'message' => 'تم تعديل الحساب بنجاح',
                        'data' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'role_name' => $validatedData['role_name'], // Assuming role_name is part of request
                        ]
                    ],
                    201,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'data' => false
            ], 500); // 500 Internal Server Error
        }
    }

    public function destroy($id): JsonResponse
    {
        // Find user by ID or return a 404 response

        // Handle potential exceptions or issues
        try {
            $user = Auth::user();
            if ($user->isActive == 0) {
                return response()->json(
                    ['message' => 'لا تملك صلاحية الدخول الى هذا الموقع '],
                    401,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            }


            if ($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
                return response()->json([
                    'success' => false,
                    'data' => null,
                    'message:the user not auth'
                ]);
            }
            $user = User::findOrFail($id);

            $user->delete(); // Use delete() instead of destroy()


            return response()->json([
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function userSummary()
    {
        try {
            $user = Auth::user();
            if ($user->isActive == 0) {
                return response()->json(
                    ['message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'],
                    401,
                    ['Content-Type' => "application/json; charset=UTF-8"],
                    JSON_UNESCAPED_UNICODE
                );
            }

            if (!$user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('staff')) {
                return response()->json(['message:the user not auth']);
            }

            // Assuming you have a User model and a status column or method
            $activeUsersCount = User::where('isActive', 1)->count();
            $blockedUsersCount = User::where('isActive', 0)->count();

            // Create the response array
            $response = [
                'success' => true,
                'message' => 'ملخص بيانات المستخدم',  // Arabic for 'User Data Summary'
                'data' => [
                    [
                        'active' => (string) $activeUsersCount,  // Convert count to string
                    ],
                    [
                        'blocked' => (string) $blockedUsersCount,  // Convert count to string
                    ],
                ],
                ['Content-Type' => "application/json; charset=UTF-8"],
                JSON_UNESCAPED_UNICODE
            ];

            // Return the response as JSON
            return response()->json($response);
        } catch (\Exception $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function blockOrUnblock(Request $request, $id)
    {
        $authUser = Auth::user();

        // Get the isActive value from the request body
        $isActive = $request->input('isActive');

        // Validate the isActive input
        if (!in_array($isActive, [0, 1])) {
            return response()->json([
                'message' => 'قيمة نشطة غير صالحة، يجب أن تكون إما 0 (حظر) أو 1 (إلغاء الحظر)'
            ], 400, ['Content-Type' => "application/json; charset=UTF-8"], JSON_UNESCAPED_UNICODE);
        }

        // Check if the authenticated user's account is active
        if ($authUser->isActive == 0) {
            return response()->json([
                'message' => 'الحساب غير مفعل الرجاء التواصل مع الادارة'
            ], 401, ['Content-Type' => "application/json; charset=UTF-8"], JSON_UNESCAPED_UNICODE);
        }

        // Check if the authenticated user has the necessary roles

        if ($authUser->hasRole(['super-admin', 'admin'])) {
            // Find the user by ID
            $user = User::findOrFail($id);

            // Update isActive status based on input
            $user->update(['isActive' => $isActive]);

            // Determine the message based on the action
            $message = $isActive == 1 ? 'تم إلغاء حظر المستخدم بنجاح' : 'تم حظر المستخدم بنجاح';


            // Return success response
            return response()->json([
                'success' => true,
                'data' => null,
                'message' => $message
            ], 200, ['Content-Type' => "application/json; charset=UTF-8"], JSON_UNESCAPED_UNICODE);
        }

        // If the user does not have permission
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => 'غير مصرح لك بتنفيذ هذا الإجراء'
        ], 403, ['Content-Type' => "application/json; charset=UTF-8"], JSON_UNESCAPED_UNICODE);
    }
}

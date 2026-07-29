<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Admin\UserRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
            'role' => ['sometimes', 'string', Rule::in(User::SUPPORTED_ROLES)],
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $data = UserRepository::getList(
                (int) $request->input('page', 1),
                (int) $request->input('limit', 10),
                trim((string) $request->input('search', '')),
                $request->input('role')
            );

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone_number' => 'required|string|max:255',
            'role_name' => ['required', 'string', Rule::in(User::SUPPORTED_ROLES)],
            'password' => 'required|string|min:8',
            'confirmed_password' => 'required|string|same:password',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $userId = DB::transaction(fn () => UserRepository::create([
                'name' => trim($request->name),
                'email' => mb_strtolower(trim($request->email)),
                'phone_number' => trim($request->phone_number),
                'role_name' => $request->role_name,
                'password' => Hash::make($request->password),
            ]));

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => UserRepository::getDetail($userId),
            ], 201);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function show(int $userId)
    {
        $user = UserRepository::getDetail($userId);

        if (! $user) {
            return $this->notFound();
        }

        try {
            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => [
                    ...((array) $user),
                    'related_records' => UserRepository::getRelatedRecordCounts($userId),
                ],
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function update(Request $request, int $userId)
    {
        $user = UserRepository::getDetail($userId);

        if (! $user) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone_number' => 'required|string|max:255',
            'role_name' => ['required', 'string', Rule::in(User::SUPPORTED_ROLES)],
            'is_active' => 'required|boolean',
            'password' => 'sometimes|string|min:8',
            'confirmed_password' => 'required_with:password|string|same:password',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $admin = $request->get('auth_user');
        if ($admin->id === $userId && (! $request->boolean('is_active') || $request->role_name !== User::ROLE_ADMIN)) {
            return response()->json([
                'code' => 409,
                'message' => 'You cannot deactivate or remove the Admin role from your own account',
                'errors' => null,
            ], 409);
        }

        try {
            $data = [
                'name' => trim($request->name),
                'email' => mb_strtolower(trim($request->email)),
                'phone_number' => trim($request->phone_number),
                'role_name' => $request->role_name,
                'is_active' => $request->boolean('is_active'),
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            DB::transaction(fn () => UserRepository::update($userId, $data));

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => UserRepository::getDetail($userId),
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function destroy(Request $request, int $userId)
    {
        $user = UserRepository::getDetail($userId);

        if (! $user) {
            return $this->notFound();
        }

        if ($request->get('auth_user')->id === $userId) {
            return response()->json([
                'code' => 409,
                'message' => 'You cannot deactivate your own account',
                'errors' => null,
            ], 409);
        }

        try {
            $relatedRecords = UserRepository::getRelatedRecordCounts($userId);
            DB::transaction(fn () => UserRepository::deactivate($userId));

            return response()->json([
                'code' => 200,
                'message' => 'User account deactivated successfully',
                'data' => [
                    'id' => $userId,
                    'is_active' => false,
                    'related_records_preserved' => array_sum($relatedRecords) > 0,
                ],
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    private function validationError($validator)
    {
        return response()->json([
            'code' => 422,
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 422);
    }

    private function notFound()
    {
        return response()->json([
            'code' => 404,
            'message' => 'User not found',
            'errors' => null,
        ], 404);
    }

    private function serverError(\Throwable $exception)
    {
        report($exception);

        return response()->json([
            'code' => 500,
            'message' => 'Internal Server Error',
            'errors' => null,
        ], 500);
    }
}

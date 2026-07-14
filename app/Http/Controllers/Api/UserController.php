<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreUserRequest;
use App\Http\Requests\Api\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\JejakmuSyncService;
use App\Support\RoleMapper;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 10), 100);

        $users = User::query()
            ->when($request->filled('role'), function ($query) use ($request) {
                $query->where('role', RoleMapper::toDatabase($request->input('role')));
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');
                $query->where(function ($builder) use ($search) {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage);

        $payload = $users->toArray();
        $payload['data'] = UserResource::collection($users->items())->resolve();

        return $this->success($payload);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return $this->success(new UserResource($user), 201);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(new UserResource($user));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $attributes = $request->userAttributes();

        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $user->update($attributes);

        return $this->success(new UserResource($user->fresh()));
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->is($user)) {
            return $this->error('Anda tidak dapat menghapus akun yang sedang digunakan.', 422);
        }

        $user->delete();

        return $this->success(['message' => 'User berhasil dihapus.']);
    }

    public function syncFromJejakmu(JejakmuSyncService $syncService): JsonResponse
    {
        $result = $syncService->sync();

        return $this->success($result);
    }
}

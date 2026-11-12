<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        return view('roles.index', [
            'roles' => Role::query()->withCount('users')->with('permissions')->orderBy('is_system', 'desc')->orderBy('name')->paginate($perPage, ['*'], 'roles_page')->withQueryString(),
            'permissions' => Permission::query()->orderBy('group')->orderBy('name')->paginate($perPage, ['*'], 'permissions_page')->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('roles.create', ['permissions' => $this->assignablePermissions()]);
    }

    public function store(StoreRoleRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $permissions = Permission::query()->whereKey($request->validated('permissions', []))->get();
        abort_unless($permissions->every(fn (Permission $permission): bool => $request->user()->hasPermissionTo($permission->name)), 403);

        $role = DB::transaction(function () use ($request, $permissions, $activityLogger): Role {
            $role = Role::query()->create([
                'name' => $request->validated('name'),
                'slug' => Str::slug($request->validated('name')).'-'.Str::lower(Str::random(6)),
                'description' => $request->validated('description'),
                'is_system' => false,
            ]);
            $role->permissions()->sync($permissions->modelKeys());
            $activityLogger->log('role.created', $role);

            return $role;
        });

        return redirect()->route('roles.index')->with('success', __('admin.messages.role_created'));
    }

    public function edit(Role $role): View
    {
        abort_if($role->is_system, 403);

        return view('roles.edit', [
            'role' => $role->load('permissions'),
            'permissions' => $this->assignablePermissions(),
        ]);
    }

    public function update(StoreRoleRequest $request, Role $role, ActivityLogger $activityLogger): RedirectResponse
    {
        abort_if($role->is_system, 403);
        $permissions = Permission::query()->whereKey($request->validated('permissions', []))->get();
        abort_unless($permissions->every(fn (Permission $permission): bool => $request->user()->hasPermissionTo($permission->name)), 403);

        DB::transaction(function () use ($request, $role, $permissions, $activityLogger): void {
            $role->update([
                'name' => $request->validated('name'),
                'slug' => $role->slug,
                'description' => $request->validated('description'),
            ]);
            $role->permissions()->sync($permissions->modelKeys());
            $activityLogger->log('role.updated', $role);
        });

        return redirect()->route('roles.index')->with('success', __('admin.messages.role_updated'));
    }

    public function destroy(Role $role, ActivityLogger $activityLogger): RedirectResponse
    {
        abort_if($role->is_system || $role->users()->exists(), 403);
        DB::transaction(function () use ($role, $activityLogger): void {
            $activityLogger->log('role.deleted', $role);
            $role->delete();
        });

        return redirect()->route('roles.index')->with('success', __('admin.messages.role_deleted'));
    }

    private function assignablePermissions(): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::query()->orderBy('group')->orderBy('name')->get()
            ->filter(fn (Permission $permission): bool => request()->user()->hasPermissionTo($permission->name))
            ->values();
    }
}

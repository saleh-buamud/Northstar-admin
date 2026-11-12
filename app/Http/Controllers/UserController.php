<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = $request->integer('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;

        $users = User::query()->with('roles')
            ->when($request->filled('q'), fn ($query) => $query->where(function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->string('q')->value().'%')
                    ->orWhere('email', 'like', '%'.$request->string('q')->value().'%')
                    ->orWhere('phone_number', 'like', '%'.$request->string('q')->value().'%');
            }))
            ->when($request->input('status') === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->input('status') === 'inactive', fn ($query) => $query->where('is_active', false))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return view('users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('users.create', ['roles' => $this->assignableRoles(request()->user())]);
    }

    public function store(StoreUserRequest $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $role = Role::query()->findOrFail($request->integer('role_id'));
        abort_unless($this->canAssignRole($request->user(), $role), 403);

        $user = DB::transaction(function () use ($request, $role, $activityLogger): User {
            $user = User::query()->create($request->safe()->except('role_id') + ['is_active' => true, 'locale' => 'en']);
            $user->roles()->attach($role);
            $activityLogger->log('user.created', $user);

            return $user;
        });

        return redirect()->route('users.show', $user)->with('success', __('admin.messages.user_created'));
    }

    public function show(User $user): View
    {
        return view('users.show', ['user' => $user->load('roles')]);
    }

    public function edit(User $user): View
    {
        $this->authorize('update', $user);

        return view('users.edit', [
            'user' => $user->load('roles'),
            'roles' => $this->assignableRoles(request()->user()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorize('update', $user);
        $role = Role::query()->findOrFail($request->integer('role_id'));
        abort_unless($this->canAssignRole($request->user(), $role), 403);

        DB::transaction(function () use ($request, $user, $role, $activityLogger): void {
            $attributes = $request->safe()->except(['role_id', 'password']);

            if ($request->filled('password')) {
                $attributes['password'] = $request->input('password');
            }

            $user->update($attributes);
            $user->roles()->sync([$role->id]);
            $activityLogger->log('user.updated', $user);
        });

        return redirect()->route('users.show', $user)->with('success', __('admin.messages.user_updated'));
    }

    public function destroy(User $user, ActivityLogger $activityLogger): RedirectResponse
    {
        $this->authorize('delete', $user);
        DB::transaction(function () use ($user, $activityLogger): void {
            $activityLogger->log('user.deleted', $user);
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', __('admin.messages.user_deleted'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Role>
     */
    private function assignableRoles(User $actor): \Illuminate\Database\Eloquent\Collection
    {
        return Role::query()->with('permissions')
            ->where('slug', '!=', 'super-admin')
            ->get()
            ->filter(fn (Role $role): bool => $this->canAssignRole($actor, $role))
            ->values();
    }

    private function canAssignRole(User $actor, Role $role): bool
    {
        return $role->slug !== 'super-admin'
            && ($actor->hasRole('super-admin') || $role->permissions->every(
                fn ($permission): bool => $actor->hasPermissionTo($permission->name),
            ));
    }
}

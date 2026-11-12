<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    public function store(Request $request, ActivityLogger $activityLogger): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9]*(?:\.[a-z][a-z0-9]*)+$/', 'unique:permissions,name'],
            'group' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($validated, $activityLogger): void {
            $permission = Permission::query()->create($validated + ['is_system' => false]);
            $activityLogger->log('permission.created', $permission);
        });

        return redirect()->route('roles.index')->with('success', __('admin.messages.permission_created'));
    }

    public function destroy(Permission $permission, ActivityLogger $activityLogger): RedirectResponse
    {
        abort_if($permission->is_system, 403);
        DB::transaction(function () use ($permission, $activityLogger): void {
            $activityLogger->log('permission.deleted', $permission);
            $permission->delete();
        });

        return redirect()->route('roles.index')->with('success', __('admin.messages.permission_deleted'));
    }
}

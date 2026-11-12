<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

class PermissionCatalog
{
    /**
     * @return array<string, array{group: string, description: string}>
     */
    public function defaults(): array
    {
        return [
            'dashboard.view' => ['group' => 'dashboard', 'description' => 'View dashboard'],
            'products.view' => ['group' => 'products', 'description' => 'View products'],
            'products.create' => ['group' => 'products', 'description' => 'Create products'],
            'products.edit' => ['group' => 'products', 'description' => 'Edit products'],
            'products.delete' => ['group' => 'products', 'description' => 'Delete products'],
            'users.view' => ['group' => 'users', 'description' => 'View users'],
            'users.create' => ['group' => 'users', 'description' => 'Create users'],
            'users.edit' => ['group' => 'users', 'description' => 'Edit users'],
            'users.delete' => ['group' => 'users', 'description' => 'Delete users'],
            'roles.manage' => ['group' => 'access', 'description' => 'Manage roles'],
            'permissions.manage' => ['group' => 'access', 'description' => 'Manage permissions'],
        ];
    }

    /**
     * @return array<string, Role>
     */
    public function seedDefaults(): array
    {
        $permissions = [];

        foreach ($this->defaults() as $name => $attributes) {
            $permissions[$name] = Permission::query()->updateOrCreate(
                ['name' => $name],
                [...$attributes, 'is_system' => true],
            );
        }

        $definitions = [
            'super-admin' => ['name' => 'Super Admin', 'permissions' => array_keys($permissions)],
            'admin' => ['name' => 'Admin', 'permissions' => array_keys($permissions)],
            'manager' => ['name' => 'Manager', 'permissions' => ['dashboard.view', 'products.view', 'products.create', 'products.edit', 'users.view']],
            'editor' => ['name' => 'Editor', 'permissions' => ['dashboard.view', 'products.view', 'products.create', 'products.edit']],
            'user' => ['name' => 'User', 'permissions' => ['dashboard.view', 'products.view']],
        ];
        $roles = [];

        foreach ($definitions as $slug => $definition) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $definition['name'], 'is_system' => true],
            );
            $role->permissions()->sync(collect($definition['permissions'])->map(
                fn (string $permission): int => $permissions[$permission]->id,
            ));
            $roles[$slug] = $role;
        }

        return $roles;
    }
}

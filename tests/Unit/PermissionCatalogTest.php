<?php

use App\Services\PermissionCatalog;

it('defines the default dashboard and management permissions', function () {
    $permissions = array_keys((new PermissionCatalog())->defaults());

    expect($permissions)->toContain(
        'dashboard.view',
        'products.view',
        'products.create',
        'products.edit',
        'products.delete',
        'users.view',
        'users.create',
        'users.edit',
        'users.delete',
        'roles.manage',
        'permissions.manage',
    );
}); 
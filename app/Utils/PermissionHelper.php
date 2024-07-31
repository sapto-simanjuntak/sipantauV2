<?php

namespace App\Utils;

class PermissionHelper
{
    const SPECIAL_PERMISSIONS = [
        'superuser' => 'Bypass all permissions, hanya untuk administrator',
    ];

    const ACTIONS = [
        'View', 'Create', 'Edit', 'Delete'
    ];

    const PERMISSIONS = [
        'master' => [
            'Data'
        ],
        'settings' => [
            'User',
            'Role',
            'Permission',
        ],
    ];
}

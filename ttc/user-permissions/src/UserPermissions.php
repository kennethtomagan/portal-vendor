<?php

namespace Ttc\UserPermissions;

use Laravel\Nova\ResourceTool;

class UserPermissions extends ResourceTool
{
    
    public function __construct()
    {
        parent::__construct();
        $this->permissionResource();
    }

    public function permissionResource()
    {
        $config = config('permission_resource');

        return $this->withMeta(['permissionResource' => $config['resources']]);
    }


    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'User Permissions';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'UserPermissions';
    }
}

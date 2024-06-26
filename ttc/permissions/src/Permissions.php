<?php

namespace Ttc\Permissions;

use Laravel\Nova\ResourceTool;

class Permissions extends ResourceTool
{

    public function __construct()
    {
        parent::__construct();
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
        return 'Permissions';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'permissions';
    }

}

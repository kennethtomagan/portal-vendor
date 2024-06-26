<?php

namespace Ttc\ProductVariants;

use Laravel\Nova\ResourceTool;

class ProductVariants extends ResourceTool
{
    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Product Variants';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'product-variants';
    }

    public function isCentral()
    {
        return $this->withMeta(['isCentral' => !tenancy()->initialized]); 
    }
}

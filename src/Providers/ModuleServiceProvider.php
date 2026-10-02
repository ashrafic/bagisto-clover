<?php

namespace Webkul\Clover\Providers;

use Webkul\Clover\Models\CloverCheckoutSession;
use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        CloverCheckoutSession::class,
    ];
}

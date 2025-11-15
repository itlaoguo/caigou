<?php

namespace Modules\Order\Providers;

use Catch\CatchAdmin;
use Catch\Providers\CatchModuleServiceProvider;

class OrderServiceProvider extends CatchModuleServiceProvider
{
    /**
     * route path
     *
     * @return string
     */
    public function moduleName(): string
    {
        // TODO: Implement path() method.
        return 'Order';
    }
}

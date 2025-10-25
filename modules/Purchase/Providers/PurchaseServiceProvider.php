<?php

namespace Modules\Purchase\Providers;

use Catch\CatchAdmin;
use Catch\Providers\CatchModuleServiceProvider;

class PurchaseServiceProvider extends CatchModuleServiceProvider
{
    /**
     * route path
     *
     * @return string
     */
    public function moduleName(): string
    {
        // TODO: Implement path() method.
        return 'purchase';
    }
}

<?php

declare(strict_types=1);

namespace Modules\Order\Models;

use Catch\Traits\DB\BaseOperate;
use Catch\Traits\DB\ScopeTrait;
use Catch\Traits\DB\Trans;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $order_id
 * @property $order_amount
 * @property $order_status
 * @property $logistics_status
 * @property $order_closed_reason
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
*/
class Order extends Model
{
    use BaseOperate, Trans, ScopeTrait;

    protected $table = 'order';

    protected $fillable = [ 'order_id', 'order_amount', 'order_status', 'logistics_status', 'order_closed_reason', 'creator_id', 'created_at', 'updated_at' ];

    /**
     * @var array
     */
    protected array $fields = ['order_id','order_amount','order_status','logistics_status','created_at'];

    /**
     * @var array
     */
    protected array $form = ['order_id','order_amount','order_status','logistics_status'];

    /**
     * @var array
     */
    public array $searchable = [
        
    ];

    protected bool $isPaginate = true;
}

<?php

declare(strict_types=1);

namespace Modules\Purchase\Models;


//use Catch\Base\CatchModel as Model;
use Catch\Traits\DB\BaseOperate;
use Catch\Traits\DB\ScopeTrait;
use Catch\Traits\DB\Trans;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $number
 * @property $name
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class Purchase extends Model
{

    use BaseOperate, Trans, ScopeTrait;

    protected $table = 'purchase';

    protected $fillable = [ 'id', 'name', 'description','file', 'status', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','name','description','file', 'status','created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['name','description','file','status'];

    /**
     * @var array
     */
    public array $searchable = [
        'name' => 'like',
    ];

    protected bool $isPaginate = true;

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'deleted_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function orders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_id');
    }

    // 定义状态为1的订单关系
    public function successOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_id')->where('status', 1);
    }

    public function failOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'purchase_id')->where('status', 2);
    }


}

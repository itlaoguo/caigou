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

    protected $fillable = [ 'id', 'name', 'description','file', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','name','file', 'created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['name','description','file'];

    /**
     * @var array
     */
    public array $searchable = [
        'name' => 'like',
    ];

    protected bool $isPaginate = true;

}

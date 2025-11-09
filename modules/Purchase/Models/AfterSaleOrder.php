<?php

declare(strict_types=1);

namespace Modules\Purchase\Models;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Linkedmall\V20230930\Linkedmall;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\ListPurchaserShopsRequest;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\SplitPurchaseOrderRequest;
use AlibabaCloud\Tea\Tea;
use AlibabaCloud\Tea\Utils\Utils;

use Catch\Base\CatchModel as Model;

/**
 * @property $id
 * @property $number
 * @property $name
 * @property $creator_id
 * @property $created_at
 * @property $updated_at
 * @property $deleted_at
*/
class AfterSaleOrder extends Model
{


    protected $table = 'purchase_order';

    protected $fillable = [ 'id', 'number', 'name', 'creator_id', 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * @var array
     */
    protected array $fields = ['id','number','name','created_at','updated_at'];

    /**
     * @var array
     */
    protected array $form = ['number','name'];

    /**
     * @var array
     */
    public array $searchable = [

    ];

    protected bool $isPaginate = true;


}

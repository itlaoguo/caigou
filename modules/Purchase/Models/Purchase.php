<?php

declare(strict_types=1);

namespace Modules\Purchase\Models;

use AlibabaCloud\SDK\Linkedmall\V20230930\Models\AddressInfo;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\OrderRenderProductDTO;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\PurchaseOrderRenderQuery;
use Catch\Exceptions\FailedException;
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
use function PHPUnit\Framework\isArray;

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

    ];

    protected bool $isPaginate = true;

}

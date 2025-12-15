<?php

declare(strict_types=1);

namespace Modules\Purchase\Models;

use AlibabaCloud\SDK\Linkedmall\V20230930\Models\AddressInfo;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\CreatePurchaseOrderRequest;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\OrderRenderProductDTO;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\ProductDTO;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\PurchaseOrderCreateCmd;
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
use AlibabaCloud\Tea\Exception\TeaUnableRetryError;

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
class PurchaseOrder extends Model
{


    protected $table = 'purchase_order';

    protected $fillable = [
        'id',
        'purchase_id',
        'product_id',
        'product_title',
        'sku_id',
        'sku_title',
        'price',
        'product_pic_url',
        'purchaser_id',
        'quantity',
        'can_sell',
        'address_detail',
        'receiver' ,
        'receiver_phone',
        'status'
    ];

    /**
     * @var array
     */
    protected array $fields = [
        'purchase_id',
        'product_id',
        'product_title',
        'sku_id',
        'sku_title',
        'price',
        'product_pic_url',
        'purchaser_id',
        'quantity',
        'can_sell',
        'address_detail',
        'receiver' ,
        'receiver_phone',
        'status'
    ];

    /**
     * @var array
     */
    protected array $form = [
        'product_id',
        'product_title',
        'sku_id',
        'sku_title',
        'price',
        'product_pic_url',
        'purchaser_id',
        'quantity',
        'can_sell',
        'address_detail',
        'receiver' ,
        'receiver_phone'
    ];

    /**
     * @var array
     */
    public array $searchable = [
        'purchase_id'=>'=',
        'product_id'=>'=',
        'product_title'=>'like',
        'sku_id'=>'=',
        'sku_title'=>'like',
    ];

    protected bool $isPaginate = true;

    private object $client;
    private object $runtime;

    public function __construct()
    {

        $config = new Config([
            "accessKeyId" => env('LINKMALL_ACCESS_KEY_ID'),
            "accessKeySecret" => env('LINKMALL_ACCESS_KEY_SECRET')
        ]);
        $config->endpoint = "linkedmall-distributor.cn-zhangjiakou.aliyuncs.com";

        $this->client = new Linkedmall($config);

        $this->runtime = new RuntimeOptions([
            "maxIdleConns" => 3,
            "connectTimeout" => 10000,
            "readTimeout" => 10000,
            "ignoreSSL" => true  // 忽略SSL证书验证，解决SSL证书错误
        ]);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    /**
     * 获取采购店铺信息
     * @return array
     */
    public function getPurchaserShops($paramters){

        $listPurchaserShopsRequest = new ListPurchaserShopsRequest([
            "pageNumber" => $paramters['page'],
            "pageSize" => $paramters['pageSize']??10,
        ]);

        $headers = [];
        try {
            // 调用API并返回结果
            $response = $this->client->listPurchaserShopsWithOptions($listPurchaserShopsRequest, $headers, $this->runtime);

            // 将响应对象转换为数组，以便Laravel可以正确序列化为JSON
            // ListPurchaserShopsResponse继承自Model类，具有toMap()方法
            $data =Tea::merge($response->body);
            $shopList = [];

            if(isArray($data['shopList']) && !empty($data['shopList'])) {

                $_shopList = $data['shopList'];
                foreach ($_shopList as $shop) {
                    if($shop['status'] === 'WORKING') {
                        $shopList[] = $shop;
                    }
                }
            }

            return $shopList;

        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }

            // 安全地访问错误数据，避免数组访问null错误
            $errorData = [
                'message' => $error->message ?? '未知错误',
                'code' => $error->code ?? '未知错误码'
            ];

            // 只有当error->data存在且包含Recommend时才访问
            if (isset($error->data) && is_array($error->data) && isset($error->data["Recommend"])) {
                $errorData['recommend'] = $error->data["Recommend"];
            }

            // 返回错误信息而不是直接var_dump
            throw new \RuntimeException('LinkedMall API调用失败: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }

    }

    public function getPurchaserShop($paramters){

        $purchaseId = $paramters['purchaserId'];

        try {
            // 复制代码运行请自行打印 API 的返回值
            $response = $this->client->getPurchaserShopWithOptions($purchaseId, [], $this->runtime);
            $data =Tea::merge($response->body);
            return $data;

        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }

            // 安全地访问错误数据，避免数组访问null错误
            $errorData = [
                'message' => $error->message ?? '未知错误',
                'code' => $error->code ?? '未知错误码'
            ];

            // 只有当error->data存在且包含Recommend时才访问
            if (isset($error->data) && is_array($error->data) && isset($error->data["Recommend"])) {
                $errorData['recommend'] = $error->data["Recommend"];
            }

            // 返回错误信息而不是直接var_dump
            throw new \RuntimeException('LinkedMall API调用失败: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 渲染并拆单
     * @return mixed
     */
    public function renderAndSplitPurchaseOrder($parameters){

        $purchaseOrderRenderQueryProductListOrderRenderProductDTO0 = new OrderRenderProductDTO([
            "quantity" => $parameters[5],
            "purchaserId" => "PID2200006482",
            "productId" => $parameters[1],
            "skuId" => $parameters[3],
        ]);

        $purchaseOrderRenderQueryAddressInfo = new AddressInfo([
            "addressDetail" => $parameters[8],
            "receiverPhone" => $parameters[7],
            "receiver" => $parameters[6],
        ]);

        $purchaseOrderRenderQuery = new PurchaseOrderRenderQuery([
            "buyerId" => $parameters[9]??"20251101",
            "deliveryAddress" => $purchaseOrderRenderQueryAddressInfo,
            "productList" => [
                $purchaseOrderRenderQueryProductListOrderRenderProductDTO0
            ]
        ]);

        $splitPurchaseOrderRequest = new SplitPurchaseOrderRequest([
            "body" => $purchaseOrderRenderQuery
        ]);

        $headers = [];

        try {

            // 复制代码运行请自行打印 API 的返回值
            $response = $this->client->splitPurchaseOrderWithOptions($splitPurchaseOrderRequest, $headers, $this->runtime);
            $data =Tea::merge($response->body);
            return $data;
        }
        catch (Exception $error) {

            $errorData = [
                'message' => $error->getMessage() ?? '未知错误',
                'code' => $error->getCode() ?? '未知错误码'
            ];

            if ($error instanceof TeaError) {
               $errorInfo = $error->getErrorInfo();
                $errorData = [
                    'message' => $errorInfo['data']['errorMessage'],
                    'code' => $errorInfo['data']['errorCode'],
                ];
            }

            throw new FailedException(json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * 创建采购单
     * @param $paramters
     * @return mixed
     */
    public function createPurchaseOrder($paramters){

        $purchaseOrderCreateCmdProductListProductDTO0 = new ProductDTO([
            "purchaserId" => "PID2200006482",
            "quantity" => $paramters['quantity'],
            "productId" => $paramters['product_id'],
            "skuId" => $paramters['sku_id'],
        ]);
        $purchaseOrderCreateCmdAddressInfo = new AddressInfo([
            "receiver" => $paramters['receiver'],
            "receiverPhone" =>  $paramters['receiver_phone'],
            "addressDetail" => $paramters['address_detail'],
        ]);
        $purchaseOrderCreateCmd = new PurchaseOrderCreateCmd([
            "outerPurchaseOrderId" => date('Ymd',time()).$paramters['id'],
            "buyerId" => $paramters['buyer_id']??"9527",
            "deliveryAddress" => $purchaseOrderCreateCmdAddressInfo,
            "productList" => [
                $purchaseOrderCreateCmdProductListProductDTO0
            ]
        ]);

        $createPurchaseOrderRequest = new CreatePurchaseOrderRequest([
            "body" => $purchaseOrderCreateCmd
        ]);
        $headers = [];
        try {
            // 复制代码运行请自行打印 API 的返回值
            $response = $this->client->createPurchaseOrderWithOptions($createPurchaseOrderRequest, $headers, $this->runtime);
            return Tea::merge($response->body);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }

            $errorData = [
                'message' => $error->message ?? '未知错误',
                'code' => $error->code ?? '未知错误码'
            ];

            throw new FailedException('LinkedMall API调用失败: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }

    }

    /**
     * @param $parameters
     * @return mixed
     */
    public function getPurchaseOrders($parameters){

        $queryOrdersRequest = new QueryOrdersRequest([]);
        $headers = [];
        try {
            // 复制代码运行请自行打印 API 的返回值
            $this->client->queryOrdersWithOptions($queryOrdersRequest, $headers, $this->runtime);
        }
        catch (Exception $error) {
            if (!($error instanceof TeaError)) {
                $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
            }

            $errorData = [
                'message' => $error->message ?? '未知错误',
                'code' => $error->code ?? '未知错误码'
            ];

            throw new \RuntimeException('LinkedMall API调用失败: ' . json_encode($errorData, JSON_UNESCAPED_UNICODE));
        }



    }
}

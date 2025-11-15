<?php

namespace Modules\Purchase\Models;

use AlibabaCloud\SDK\Linkedmall\V20230930\Linkedmall;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\ListPurchaserShopsRequest;
use AlibabaCloud\SDK\Linkedmall\V20230930\Models\SplitPurchaseOrderRequest;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Tea;
use AlibabaCloud\Tea\Utils\Utils;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Darabonba\OpenApi\Models\Config;

class LinkMall
{


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
            $data = Utils::toJSONString(Tea::merge($response->body));
            var_dump($data);exit();

            // 将响应对象转换为数组，以便Laravel可以正确序列化为JSON
            // ListPurchaserShopsResponse继承自Model类，具有toMap()方法
            $data = $response->toMap();
            $shopList = [];

            if($data['statusCode'] === 200) {

                $_shopList = $data['body']['shopList'];
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

    /**
     * 渲染并拆单
     * @return void
     */
    public function renderAndSplitPurchaseOrder($parameters){

        $splitPurchaseOrderRequest = SplitPurchaseOrderRequest::fromMap([
            'body' => [
                'buyerId'=> $parameters,
                'deliveryAddress'=> [

                ],
                'productList'=> '',
                'extInfo'=> [],
            ]
        ]);

        $headers = [];

        try {
            // 复制代码运行请自行打印 API 的返回值
            $response = $this->client->splitPurchaseOrderWithOptions($splitPurchaseOrderRequest, $headers, $this->runtime);

            $data = $response->toMap();
            var_dump($data);
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

    /**
     * 查询订单
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

    /**
     * 拆单并渲染采购单
     * @param $parameters
     * @return mixed
     */
    public function splitPurchaseOrder($parameters){

    }

    /**
     * 创建采购单
     * @param $parameters
     * @return mixed
     */
    public function createPurchaseOrder($parameters){

    }

    /**
     * 获取采购单状态
     * @param $parameters
     * @return mixed
     */
    public function getPurchaseOrderStatus($purchaseOrderId){

        try {
            // 调用API并返回结果
            $response = $this->client->getPurchaseOrderStatus($purchaseOrderId);
            $data = Utils::toJSONString(Tea::merge($response->body));
            var_dump($data);exit();

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


    public function renderRefundOrder($parameters){

    }
}

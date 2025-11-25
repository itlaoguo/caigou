<?php
declare(strict_types=1);

namespace Modules\Purchase\Http\Controllers;

use Catch\Base\CatchController as Controller;
use Catch\Exceptions\FailedException;
use Modules\Permissions\Enums\MenuType;
use Modules\Purchase\Models\Purchase;
use Modules\Purchase\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class PurchaseOrderController extends Controller
{
    public function __construct(
        protected readonly Purchase $purchaseModel,
        protected readonly PurchaseOrder $model
    ){}

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request): mixed
    {

//        $result = Purchase::withCount([
//            'orders as order_count', // 采购单数量
//            'successOrders as success_order_count' // 状态为1的采购单数量
//        ])
//            ->select([
//                'id',
//                'name',
//                'created_at',
//                DB::raw('(SELECT SUM(CAST(quantity AS UNSIGNED)) FROM ymp_purchase_order WHERE ymp_purchase_order.purchase_id = ymp_purchase.id) as product_quantity')
//            ])
//            ->whereNull('deleted_at')
//            ->get()
//            ->map(function ($purchase) {
//                return [
//                    '采购名称' => $purchase->name,
//                    '开始日期' => $purchase->created_at->format('Y-m-d'),
//                    '采购单数量' => $purchase->order_count,
//                    '采购产品数量' => $purchase->product_quantity ?: 0,
//                    '成功采购单数量' => $purchase->success_order_count
//                ];
//            });
//        $result = $this->purchaseModel->getList();

        // $offset = ($request->page - 1) * $request->limit;

        // $results = $this->purchaseModel->select('purchase.*')
        //     ->selectRaw('(SELECT COUNT(*) FROM ymp_purchase_order ypo WHERE ypo.purchase_id = ymp_purchase.id) AS total_task')
        //     ->selectRaw('(SELECT COUNT(*) FROM ymp_purchase_order ypo WHERE ypo.purchase_id = ymp_purchase.id AND status = 1) AS total_success_task')
        //     ->selectRaw('(SELECT COUNT(*) FROM ymp_purchase_order ypo WHERE ypo.purchase_id = ymp_purchase.id AND status = 2) AS total_fail_task')
        //     ->selectRaw('(SELECT SUM(quantity) FROM ymp_purchase_order ypo WHERE ypo.purchase_id = ymp_purchase.id) AS total_product')
        //     ->whereNull('deleted_at')
        //     ->getList();

        $results = $this->purchaseModel->setBeforeGetList(function ($query){
            $query->withCount([
                'orders as total_task', // 采购单数量
                'successOrders as total_success_task',  // 状态为1的采购单数量
                'failOrders as total_fail_task' // 状态为2的采购单数量
            ])
            ->withSum('orders as total_product', 'quantity');
            return $query;
        })->getList();


        return $results;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {

            $purchaseId = $this->purchaseModel->storeBy($request->only(['name','description','file']));
            if (!empty($request->input('products'))) {

                foreach ($request->input('products') as $product) {

                    $product['purchase_id'] = $purchaseId;
                    $this->model->storeBy($product);
                }

            }

        }catch (FailedException $e){

            throw new FailedException($e->getMessage());
        }


    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function render(Request $request)
    {

        return $this->model->renderAndSplitPurchaseOrder($request->all());

    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {


        return $this->model->firstBy($id);
    }

    public function lines($id){

        return $this->model->setBeforeGetList(function($query) use ($id){
            $query->where(['purchase_id', $id]);
        })->getList();
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update($id, Request $request)
    {
        return $this->model->updateBy($id, $request->all());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        return $this->model->deleteBy($id);
    }

    /**
     * 获取采购店铺列表
     * @return mixed
     */
    public function shops(Request $request){

        return $this->model->getPurchaserShops($request);
    }

    /**
     * 获取采购店铺列表
     * @return mixed
     */
    public function shop(Request $request){


        return $this->model->getPurchaserShop($request);
    }

    /**
     * @return mixed
     */
    public function renderAndSplitPurchaseOrder(Request $request){

        $file = $request->input('path');
        $dataList = $this->parseExcel($file);

        if(!is_array($dataList) || empty($dataList)){
            throw new FailedException('未获得产品数据');
        }

        $list = [];

        foreach($dataList as $item){

            $result = $this->model->renderAndSplitPurchaseOrder($item);

            if(intval($result['canSell']) === 1){

                $list[] = array(
                    'productId'=>$result['orderList'][0]['productList'][0]['productId'],
                    'productTitle'=>$result['orderList'][0]['productList'][0]['productTitle'],
                    'skuId'=>$result['orderList'][0]['productList'][0]['skuId'],
                    'skuTitle'=>$result['orderList'][0]['productList'][0]['skuTitle'],
                    'price'=>$result['orderList'][0]['productList'][0]['price'],
                    'productPicUrl'=>$result['orderList'][0]['productList'][0]['productPicUrl'],
                    'purchaserId'=>$result['orderList'][0]['productList'][0]['purchaserId'],
                    'quantity'=>$result['orderList'][0]['productList'][0]['quantity'],
                    'canSell'=>$result['orderList'][0]['productList'][0]['canSell']?1:0,
                    'addressDetail'=>$result['addressList'][0]['addressDetail'],
                    'receiver'=>$result['addressList'][0]['receiver'],
                    'receiverPhone'=>$result['addressList'][0]['receiverPhone']
                );

            }else{

                $list[] = array(
                    'productId'=>$result['unsellableOrderList'][0]['productList'][0]['productId'],
                    'productTitle'=>$result['unsellableOrderList'][0]['productList'][0]['productTitle'],
                    'skuId'=>$result['unsellableOrderList'][0]['productList'][0]['skuId'],
                    'skuTitle'=>$result['unsellableOrderList'][0]['productList'][0]['skuTitle'],
                    'price'=>$result['unsellableOrderList'][0]['productList'][0]['price'],
                    'productPicUrl'=>$result['unsellableOrderList'][0]['productList'][0]['productPicUrl'],
                    'purchaserId'=>$result['unsellableOrderList'][0]['productList'][0]['purchaserId'],
                    'quantity'=>$result['unsellableOrderList'][0]['productList'][0]['quantity'],
                    'canSell'=>$result['unsellableOrderList'][0]['productList'][0]['canSell']?1:0,
                    'addressDetail'=>$result['addressList'][0]['addressDetail'],
                    'receiver'=>$result['addressList'][0]['receiver'],
                    'receiverPhone'=>$result['addressList'][0]['receiverPhone']
                );
            }

        }

        return $list;

    }

    public function createPurchaseOrder(Request $request){


    }

    /**
     * 解析Excel文件，跳过第一行，返回数组
     * 针对大数据量文件进行优化，使用流式读取和逐行处理
     *
     * @param Request $request
     * @return array
     */
    public function parseExcel($file)
    {
//        $file = base_path() . DIRECTORY_SEPARATOR . $file;
        $file = public_path() . DIRECTORY_SEPARATOR.'uploads\file\2025-11-17\20254VmmBZ3IX41763335428.xlsx';

        // 检查文件是否存在
        if (!file_exists($file)) {
            throw new FailedException('文件不存在');
        }

        try {
            // 使用PhpSpreadsheet进行读取，优化内存使用
            $reader = IOFactory::createReader('Xlsx');

            // 设置只读模式，减少内存占用（不读取格式信息，只读数据）
            $reader->setReadDataOnly(true);

            // 不读取空单元格，进一步减少内存占用
            $reader->setReadEmptyCells(false);

            // 加载文件
            $spreadsheet = $reader->load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            // 获取最高行和列，用于确定数据范围
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

            // 存储数据的数组
            $data = [];

            // 从第二行开始读取（跳过第一行表头），逐行处理以优化内存
            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = [];
                $isEmptyRow = true;

                // 读取该行的所有列
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
                    $rowData[] = $cellValue;

                    // 检查是否有非空值
                    if ($cellValue !== null && $cellValue !== '') {
                        $isEmptyRow = false;
                    }
                }

                // 只添加非空行
                if (!$isEmptyRow) {
                    $data[] = $rowData;
                }

                // 每处理1000行清理一次内存（可选优化）
                if ($row % 1000 === 0) {
                    gc_collect_cycles();
                }
            }

            // 释放内存
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $worksheet, $reader);

            // 强制垃圾回收
            gc_collect_cycles();

            return $data;

        } catch (\Exception $e) {

            throw new FailedException('解析Excel文件失败');

        }
    }

}

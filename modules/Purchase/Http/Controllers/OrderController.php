<?php
declare(strict_types=1);

namespace Modules\Purchase\Http\Controllers;

use Catch\Base\CatchController as Controller;
use Modules\Purchase\Models\PurchaseOrder;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function __construct(
        protected readonly PurchaseOrder $model
    ){}

    /**
     * @return mixed
     */
    public function index(): mixed
    {
        return $this->model->getList();
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        return $this->model->getPurchaserShops();//获取shopid

        //return $this->model->storeBy($request->all());
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

        return $this->model->renderAndSplitPurchaseOrder($request);
    }

}

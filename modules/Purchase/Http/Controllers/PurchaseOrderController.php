<?php
declare(strict_types=1);

namespace Modules\Purchase\Http\Controllers;

use Catch\Base\CatchController as Controller;
use Modules\Purchase\Models\PurchaseOrder;
use Illuminate\Http\Request;


class PurchaseOrderController extends Controller
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
        return $this->model->post();

        //return $this->model->storeBy($request->all());
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
}

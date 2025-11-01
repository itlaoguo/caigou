<?php

namespace Modules\Purchase\Http\Requests;

use Illuminate\Foundation\Http\FormRequest as Request;

class PurchaseOrderRequest extends Request
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'number' => 'required',
'name' => 'required',

        ];
    }


    /**
     *
     * @return array
     */
    public function messages(): array
    {
        return [];
    }
}

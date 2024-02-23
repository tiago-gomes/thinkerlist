<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchScheduleRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'perPage' => 'sometimes|integer|min:1',
            'page' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|max:255',
            'is_custom' => 'sometimes|boolean',
            'is_recurring' => 'sometimes|boolean',
        ];
    }
}

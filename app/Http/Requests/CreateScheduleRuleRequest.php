<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'user_id' => 'required|exists:users,id',

            'is_recurring' => 'required|boolean',
            'recurring_type' => 'required_if:is_recurring,true|integer|between:1,4',
            'recurring_type_duration' => 'required_with:recurring_type|integer',

            'recurring_duration_start_date' => 'required_if:is_recurring,true|date',
            'recurring_start_time' => 'required_if:is_recurring,true|date_format:H:i',
            'recurring_end_time' => 'required_if:is_recurring,true|date_format:H:i|after:recurring_start_time',
            'recurring_duration_minutes' => 'required_if:is_recurring,true|integer|between:30,60',
            'recurring_interval_minutes' => 'required_if:is_recurring,true|integer|between:5,10',

            'recurring_has_weekdays' => 'required_if:is_recurring,true|boolean',
            'recurring_weekdays' => 'required|array|between:1,7',
            'recurring_weekdays.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',

            'recurring_has_lunch_time' => 'required_if:is_recurring,true|boolean',
            'recurring_start_lunch_time' => 'required_if:recurring_has_lunch_time,true|date_format:H:i',
            'recurring_end_lunch_time' => 'required_if:recurring_has_lunch_time,true|date_format:H:i|after:recurring_start_lunch_time',

            'recurring_ignore_weekends' => 'required_if:is_recurring,true|boolean',

            'is_custom' => 'required|boolean',
            'custom_date_times' => 'required_if:is_custom,true|json',
        ];
    }
}

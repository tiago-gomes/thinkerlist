<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'part_id' => 'required|integer|exists:parts,id',
            'episode_id' => 'required|integer|exists:episodes,id', // Ensure episode_id is required, an integer, and exists in the episodes table
            'position' => 'required|integer|min:1', // Ensure position is required, an integer, and greater than or equal to 1
            'new_position' => 'required|integer|min:1',
        ];
    }
}

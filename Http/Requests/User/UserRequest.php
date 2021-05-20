<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'pagination'             => 'boolean',
            'filter'                 => 'array',
            'filter.name'            => 'min:3|string|max:255',
            'filter.email'           => 'min:3|string|max:255',
            'filter.phone'           => 'min:3|string|max:255',
            'filter.date'            => ['date', 'date_format:Y-m-d'],
            'filter.date_start'      => ['date', 'date_format:Y-m-d'],
            'filter.date_end'        => ['date', 'date_format:Y-m-d'],
            'filter.companies'       => 'array',
            'filter.companies.*'     => 'distinct|exists:companies,id',
            'filter.search_fields'   => 'array',
            'filter.search_fields.*' => 'distinct|string|min:3',
            'filter.keywords'        => 'min:3|string|max:255',
            'sort'                   => 'array',
            'sort.*'                 => [
                'required',
                Rule::in(['asc', 'desc']),
            ],
        ];
        $filter = $this->request->get('filter') ?? [];
        if (array_key_exists('date_start', $filter) && array_key_exists('date_end', $filter)) {
            $rules['filter.date_start'][] = 'before_or_equal:filter.date_end';
        }

        return $rules;
    }
}

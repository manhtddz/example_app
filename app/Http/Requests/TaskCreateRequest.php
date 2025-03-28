<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskCreateRequest extends FormRequest
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
            'project_id' => ['required', 'integer', Rule::exists('projects', 'id')],
            "name" => [
                'required',
                'max:128',
                Rule::unique('tasks', 'name')->where(function ($query) {
                    return $query
                        ->where('project_id', $this->input('project_id'))
                        ->whereNot('del_flag', IS_DELETED);
                }),
            ],
            'task_status' => ['required', 'in:1,2', 'integer'],
        ];
    }
}

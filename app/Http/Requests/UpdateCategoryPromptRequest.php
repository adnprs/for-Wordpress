<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryPromptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $prompt = $this->route('prompt');
        $appId = $this->input('app_id', $prompt->app_id);

        return [
            'app_id' => ['sometimes', 'exists:apps,id'],
            'category_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('category_prompts', 'category_name')
                    ->where('app_id', $appId)
                    ->ignore($prompt->id),
            ],
            'prompt_template' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.unique' => 'This site already has a category with that name.',
        ];
    }
}

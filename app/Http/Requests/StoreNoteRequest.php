<?php

namespace App\Http\Requests;

use App\Models\Poste;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

#[FailOnUnknownFields]
class StoreNoteRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $budget = $this->budget;

        return [
            'price' => ['required', 'integer:strict'],
            'label' => ['nullable', 'string', 'max:250'],
            'year' => ['required', 'integer', 'min:1900', 'max:2100'],
            'month' => ['required', 'integer', 'between:0,11'],
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function (Builder $query) use ($budget) {
                    $query->where('budget_id', $budget->id);
                }),
            ],
            'poste_id' => [
                'nullable',
                'integer',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $catId = $validator->validated()['category_id'] ?? null;
                $posId = $validator->validated()['poste_id'] ?? null;

                if (empty($catId)) {
                    $validator->errors()->add('category_id', 'Category is required');

                    return;
                }

                $postes = Poste::where('category_id', $catId)->get();

                if (empty($posId) && $postes->isNotEmpty()) {
                    $validator->errors()->add('poste_id', 'Poste is required');
                }

                if (! empty($posId) && $postes->doesntContain('id', $posId)) {
                    $validator->errors()->add('poste_id', 'Poste is not in category');
                }
            },
        ];
    }
}

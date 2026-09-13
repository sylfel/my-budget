<?php

namespace App\Http\Requests;

use App\Models\Poste;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\Attributes\FailOnUnknownFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

#[FailOnUnknownFields]
class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return $this->user()->can('update', $this->note);
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
            'price' => ['sometimes', 'required', 'integer:strict'],
            'label' => ['sometimes', 'nullable', 'string', 'max:250'],
            'category_id' => [
                'sometimes',
                'required',
                Rule::exists('categories', 'id')->where(function (Builder $query) use ($budget) {
                    $query->where('budget_id', $budget->id);
                })],
            'poste_id' => [
                'sometimes',
                'nullable',
                'integer',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $needCheck = Arr::hasAny($validator->validated(), ['poste_id', 'category_id']);
                if (! $needCheck) {
                    return;
                }
                $cat_id = Arr::get($validator->validated(), 'category_id', $this->note->category_id);
                $pos_id = Arr::get($validator->validated(), 'poste_id', $this->note->poste_id);

                $postes = Poste::where('category_id', $cat_id)->get();

                if (empty($pos_id) && $postes->isNotEmpty()) {
                    $validator->errors()->add(
                        'poste_id',
                        'Poste is required'
                    );
                }

                if (! empty($pos_id) && $postes->doesntContain($pos_id)) {
                    $validator->errors()->add(
                        'poste_id',
                        'Poste is not in category'
                    );
                }

            },
        ];
    }
}

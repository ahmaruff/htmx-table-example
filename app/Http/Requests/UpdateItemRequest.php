<?php

namespace App\Http\Requests;

use Ahmaruff\HtmxTable\HtmxTable;
use App\Models\Item;
use App\Services\ItemDatatableService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateItemRequest extends FormRequest
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
        return [
            'sku' => ['required', 'string', 'max:255', Rule::unique('items', 'sku')->ignore($this->route('item'))],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', Rule::in(Item::CATEGORIES)],
            'brand' => ['required', Rule::in(Item::BRANDS)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['required', Rule::in(Item::UNITS)],
            'status' => ['required', Rule::in(Item::STATUSES)],
            'is_active' => ['required', 'boolean'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'manufactured_at' => ['nullable', 'date'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if (! HtmxTable::isFragmentRequest($this)) {
            parent::failedValidation($validator);
        }

        if ($this->hasSession()) {
            $this->session()->flashInput($this->except(['_token', '_method']));
        }

        throw new HttpResponseException(
            response(
                view('items.partials.edit-row', [
                    'item' => $this->route('item'),
                    'columns' => app(ItemDatatableService::class)->columns(),
                ])->withErrors($validator),
                422
            )
        );
    }
}

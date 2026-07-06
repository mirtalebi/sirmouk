<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
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
            'entry_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:draft,posted',
            'items' => 'required|array|min:2',
            'items.*.account_id' => 'required|integer|exists:accounts,id',
            'items.*.customer_id' => 'nullable|integer|exists:users,id',
            'items.*.debit' => 'required|integer|min:0',
            'items.*.credit' => 'required|integer|min:0',
            'items.*.description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'entry_date.required' => 'تاریخ سند الزامی است.',
            'entry_date.date' => 'تاریخ سند باید یک تاریخ معتبر باشد.',
            'status.required' => 'وضعیت سند الزامی است.',
            'status.in' => 'وضعیت سند باید "draft" یا "posted" باشد.',
            'items.required' => 'سند باید حداقل دو سطر داشته باشد.',
            'items.min' => 'سند باید حداقل دو سطر داشته باشد.',
            'items.*.account_id.required' => 'حساب برای هر سطر الزامی است.',
            'items.*.account_id.exists' => 'حساب انتخاب شده معتبر نیست.',
            'items.*.customer_id.exists' => 'مشتری انتخاب شده معتبر نیست.',
            'items.*.debit.required' => 'مبلغ بدهکار الزامی است.',
            'items.*.debit.integer' => 'مبلغ بدهکار باید عدد صحیح باشد.',
            'items.*.credit.required' => 'مبلغ بستانکار الزامی است.',
            'items.*.credit.integer' => 'مبلغ بستانکار باید عدد صحیح باشد.',
        ];
    }

    /**
     * Validate the request after data is validated (custom validation).
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Check if totals are balanced
        $items = $this->input('items', []);
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($items as $item) {
            $totalDebit += (int) ($item['debit'] ?? 0);
            $totalCredit += (int) ($item['credit'] ?? 0);
        }

        if ($totalDebit !== $totalCredit) {
            $validator->errors()->add('balance', 'جمع بدهکار و بستانکار برابر نیستند! سند باید متوازن (تراز) باشد.');
        }

        parent::failedValidation($validator);
    }
}


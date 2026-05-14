<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchServerCopyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // No authentication required
    }

    public function rules(): array
    {
        return [
            'nid' => ['required', 'string', 'regex:/^\d{10}$|^\d{17}$/'],
            'dob' => ['required', 'string', 'date_format:Y-m-d'],
        ];
    }

    public function messages(): array
    {
        return [
            'nid.required' => 'NID নম্বর দিন।',
            'nid.regex'    => 'NID অবশ্যই ১০ বা ১৭ সংখ্যার হতে হবে। ১৩ অঙ্কের NID হলে জন্মসাল যোগ করে ১৭ অঙ্কের করুন।',
            'dob.required' => 'জন্ম তারিখ দিন।',
            'dob.date_format' => 'জন্ম তারিখ YYYY-MM-DD ফরম্যাটে দিন।',
        ];
    }

    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): never
    {
        throw new \Illuminate\Http\Exceptions\HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422)
        );
    }
}

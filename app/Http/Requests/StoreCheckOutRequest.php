<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * NFR-08: Semua input divalidasi di backend.
 * FR-12: Mencari transaksi aktif berdasarkan nomor tiket atau plat.
 */
class StoreCheckOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['required', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.required' => 'Nomor tiket atau plat kendaraan wajib diisi.',
            'search.max'      => 'Input pencarian terlalu panjang.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * NFR-08: Semua input divalidasi di backend.
 * FR-07: Input nomor plat
 * FR-08: Memilih jenis kendaraan
 * FR-09: Memilih area parkir
 */
class StoreCheckInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'license_plate'   => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{0,3}$/i',
            ],
            'vehicle_type'    => ['required', 'in:motorcycle,car'],
            'parking_area_id' => ['required', 'integer', 'exists:parking_areas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'license_plate.required'      => 'Nomor plat kendaraan wajib diisi.',
            'license_plate.regex'         => 'Format nomor plat tidak valid (contoh: B 1234 ABC).',
            'license_plate.max'           => 'Nomor plat maksimal 15 karakter.',
            'vehicle_type.required'       => 'Jenis kendaraan wajib dipilih.',
            'vehicle_type.in'             => 'Jenis kendaraan harus motor atau mobil.',
            'parking_area_id.required'    => 'Area parkir wajib dipilih.',
            'parking_area_id.exists'      => 'Area parkir tidak ditemukan.',
        ];
    }
}

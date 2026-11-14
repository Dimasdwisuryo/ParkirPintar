<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * NFR-08: Semua input divalidasi di backend.
 * FR-01: Menambahkan area parkir
 * FR-05: Mengatur kapasitas motor dan mobil
 */
class StoreParkingAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => ['required', 'string', 'max:100', 'unique:parking_areas,name'],
            'location'            => ['nullable', 'string', 'max:255'],
            'capacity_motorcycle' => ['required', 'integer', 'min:0', 'max:9999'],
            'capacity_car'        => ['required', 'integer', 'min:0', 'max:9999'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                => 'Nama area parkir wajib diisi.',
            'name.unique'                  => 'Nama area parkir sudah digunakan.',
            'name.max'                     => 'Nama area parkir maksimal 100 karakter.',
            'capacity_motorcycle.required' => 'Kapasitas motor wajib diisi.',
            'capacity_motorcycle.min'      => 'Kapasitas motor tidak boleh negatif.',
            'capacity_car.required'        => 'Kapasitas mobil wajib diisi.',
            'capacity_car.min'             => 'Kapasitas mobil tidak boleh negatif.',
        ];
    }
}

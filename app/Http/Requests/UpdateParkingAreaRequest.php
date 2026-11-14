<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * NFR-08: Semua input divalidasi di backend.
 * FR-03: Mengubah informasi area
 * FR-05: Kapasitas tidak boleh dikurangi di bawah jumlah kendaraan terparkir.
 */
class UpdateParkingAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $areaId = $this->route('parking_area')?->id ?? $this->route('id');

        return [
            'name'                => ['required', 'string', 'max:100', "unique:parking_areas,name,{$areaId}"],
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
            'capacity_motorcycle.min'      => 'Kapasitas motor tidak boleh negatif.',
            'capacity_car.min'             => 'Kapasitas mobil tidak boleh negatif.',
        ];
    }
}

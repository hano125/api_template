<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class insertDataRequest extends FormRequest
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
            "certificate_name" => "required",
            'certificate_flag' => "nullable",
            "deg_address_name" => "required|string",
            "deg_type_name" => "required|string",
            "deg_id" => "nullbale",
            "main_id" => "nullbale",
            'vacancy_file' => "required",
            'majles_file' => "required",
            'malia_file' => "required",
            'file_back' => "required",
            'minstry' => "nullable",
            'tshkeel' => "required",
            "vacancy_deg_type" => "required",
            'vacancy_deg_address' => "required",
            'mkun_name' => "required",
            'required_deg_type' => "required",
            'required_deg_address' => "required",
            //'certif' => "required",
            'book_num' => "required",
            'book_date' => "required",
            'newly_deg_type' => "required",
            'newly_deg_address' => "nullable",
            'complate_flag' => "nullable",
            'deflg' => "nullable",
            'num_back' => "required",
            'chek' => "required",
        ];
    }
}

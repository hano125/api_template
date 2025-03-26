<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\insertDataRequest;
use App\Models\certificate;
use App\Models\deg_addresse;
use App\Models\deg_type;
use App\Models\est_file;
use App\Models\est_main;
use App\Models\mkun;
use Illuminate\Support\Facades\Auth;

class storeAllController extends Controller
{
    public function store(insertDataRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = Auth::user();

            DB::transaction(function () use ($request, $user) {

                $certificate = certificate::updateOrCreate(
                    [
                        'certificate_name' => $request->certificate_name, // Use a unique field instead of 'id'
                    ],
                    [
                        'certificate_flag' => $request->certificate_flag,
                    ]
                );

                //   dd($certificate);

                $mukn = mkun::firstOrCreate(
                    [
                        'mkun_name' => $request->mkun_name, // Use a unique field instead of 'id'
                    ],
                    [
                        'mkun_flag' => $request->mkun_flag,
                    ]
                );

                // dd($mukn);

                $deg_addresse = deg_addresse::firstOrCreate(
                    [
                        "id" => $request->id,
                    ],
                    [
                        'deg_address_name' => $request->deg_address_name,
                        'deg_id' => $request->deg_id ?? null,
                    ]

                );

                //dd($deg_addresse);

                $deg_type = deg_type::firstOrCreate(
                    [
                        "id" => $request->id,
                    ],
                    [
                        'deg_type_name' => $request->deg_type_name,
                        'deg_flag' => $request->deg_flag,
                    ]
                );
                // dd($deg_type);



                $est_main = est_main::create(

                    [
                        "user_id" => $user->id,
                        "minstry" => $user->minstry ?? null,
                        'tshkeel' => $request->tshkeel ?? null,
                        'vacancy_deg_type' => $request->vacancy_deg_type,
                        'vacancy_deg_address' => $request->vacancy_deg_address,
                        'vacancy_deg_date' => $request->vacancy_deg_date,
                        "mkun" => $mukn->mkun_name,
                        'certif' => $certificate->certificate_name,
                        'book_num' => $request->book_num,
                        'book_date' => $request->book_date,
                        'newly_deg_type' => $request->newly_deg_type,
                        'newly_deg_addresse' => $request->newly_deg_addresse,
                        'complate_flag' => $request->complate_flag,
                        'delflg' => $request->delflg,
                        'num_back' => $request->num_back,
                        'chek' => $request->chek,
                        'required_deg_type' => $request->required_deg_type,
                        'required_deg_address' => $request->required_deg_address,

                    ]

                );

                // dd($est_main);

                $est_file = est_file::updateOrCreate(
                    [
                        "id" => $request->id,
                    ],
                    [
                        'main_id' => $est_main->id,
                        'vacancy_file' => $request->vacancy_file,
                        'majles_file' => $request->majles_file,
                        'malia_file' => $request->malia_file,
                        'file_back' => $request->file_back,
                    ]
                );
            });
            return response()->json([
                'status' => 'success',
                'message' => 'Data inserted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

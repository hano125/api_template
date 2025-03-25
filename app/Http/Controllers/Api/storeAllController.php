<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\insertDataRequest;
use App\Models\certificate;
use App\Models\deg_type;
use Illuminate\Support\Facades\Auth;

class storeAllController extends Controller
{
    public function store(insertDataRequest $request)
    {
        $validated = $request->validated();

        try {
            $user = Auth::user();

            DB::transaction(function () use ($request) {
                deg_type::create([
                    'name' => $request->name,
                    'flag' => $request->flag
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

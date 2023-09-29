<?php

namespace App\Http\Controllers\Api;

use App\Models\Survey;
use App\Libraries\ResponseBase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;

class SurveyController extends Controller
{
    public function store(SurveyRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $survey = Survey::where('user_id', $user->id)->first();
        if ($survey)
            return ResponseBase::error('Anda sudah mengisi survey!', 422);

        try {
            $survey = new Survey();
            $survey->user_id = $user->id;
            $survey->age = $request->age;
            $survey->skin_type = $request->skin_type;
            $survey->skin_problem = $request->skin_problem;
            $survey->save();

            return ResponseBase::success("Berhasil menambahkan data survey!", $survey->load('user'));
        } catch (\Exception $e) {
            Log::error('Gagal menambahkan data survey: ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data survey!', 409);
        }
    }
}

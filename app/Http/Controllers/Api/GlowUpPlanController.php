<?php

namespace App\Http\Controllers\Api;

use App\Models\GlowUpPlan;
use Illuminate\Support\Str;
use App\Libraries\ResponseBase;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\GlowUpPlanRequest;

class GlowUpPlanController extends Controller
{
    public function index(GlowUpPlanRequest $request)
    {
        $pageNumber = $request->input('page', 1);
        $dataAmount = $request->input('limit', 10);

        $glowUpPlans = GlowUpPlan::paginate($dataAmount, ['*'], 'page', $pageNumber);

        return ResponseBase::success("Berhasil menerima data glowUpPlan", $glowUpPlans);
    }

    public function create(GlowUpPlanRequest $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        try {
            DB::beginTransaction();
            $glowUpPlan = new GlowUpPlan();
            $glowUpPlan->user_id = $user->id;

            $image = $request->img;
            $fileNameOriginal = $image->getClientOriginalName();
            $fileName = Str::slug(basename($fileNameOriginal, '.' . $image->getClientOriginalExtension()) . '-' . time());
            $fileNameExtension = $fileName  . '.' . $image->getClientOriginalExtension();
            $path = Storage::disk('public')->putFileAs('products', $image, $fileNameExtension);

            if (!$path)
                return ResponseBase::error("Terjadi kesalahan upload gambar Glow Up Plan", 409);

            $glowUpPlan->img = $path;
            $glowUpPlan->save();

            DB::commit();
            return ResponseBase::success("Berhasil menambahkan data glowUpPlan!", $glowUpPlan);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menambahkan data glowUpPlan: ' . $e->getMessage());
            return ResponseBase::error('Gagal menambahkan data glowUpPlan!', 409);
        }
    }

    public function show($id)
    {
        $glowUpPlan = GlowUpPlan::findOrFail($id);
        return ResponseBase::success("Berhasil menerima data glowUpPlan", $glowUpPlan);
    }

    public function delete($id)
    {
        $glowUpPlan = GlowUpPlan::findOrFail($id);

        try {
            DB::beginTransaction();

            if (config('app.env') === 'local') {
                // Jika aplikasi berjalan di localhost (mode pengembangan)
                $pathOld = str_replace(asset('storage/'), '', $glowUpPlan->img);
            } else {
                // Jika aplikasi berjalan di lingkungan selain localhost (mode produksi)
                $pathOld = str_replace(asset('public/storage/'), '', $glowUpPlan->img);
            }

            Storage::disk('public')->delete($pathOld);

            $glowUpPlan->delete();

            DB::commit();
            return ResponseBase::success('Berhasil menghapus data glowUpPlan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus data glowUpPlan: ' . $e->getMessage());
            return ResponseBase::error('Gagal menghapus data glowUpPlan!', 409);
        }
    }
}

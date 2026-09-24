<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReferralResource;
use App\Models\Master;
use App\Models\Referral;
use App\Models\ReferralEarning;
use App\Services\Referral\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function attach(
        Request         $request,
        ReferralService $referralService,
    ): JsonResponse
    {
        $code = $request->input('code');
        $masterId = (int)$request->header('X-Master-Id');
        $master = $this->getMasterById($masterId);
        $referral = $referralService->registerReferral($master, $code);

        return response()->json($referral, 201);
    }

    public function my(Request $request): JsonResponse
    {
        $masterId = (int)$request->header('X-Master-Id');
        $master = $this->getMasterById($masterId);
        $referrals = $master->referrals;

        return response()->json(ReferralResource::collection($referrals));
    }

    public function earnings(Request $request): JsonResponse
    {
        $masterId = (int)$request->header('X-Master-Id');

        $totalPending = ReferralEarning::where('referrer_master_id', $masterId)
            ->where('status', ReferralEarning::STATUS_PENDING)
            ->sum('amount');
        $totalPaid = ReferralEarning::where('referrer_master_id', $masterId)
            ->where('status', ReferralEarning::STATUS_PAID)
            ->sum('amount');
        $totalAccrued = ReferralEarning::where('referrer_master_id', $masterId)
            ->sum('amount');
        $totalReferrals = Referral::where('referrer_master_id', $masterId)
            ->where('status', Referral::STATUS_REWARDED)
            ->count();

        return response()->json([
            'total_accrued' => (int)$totalAccrued,
            'total_pending' => (int)$totalPending,
            'total_paid' => (int)$totalPaid,
            'total_referrals' => $totalReferrals,
        ]);
    }

    private function getMasterById(int $id): Master
    {
        return Master::find($id);
    }
}

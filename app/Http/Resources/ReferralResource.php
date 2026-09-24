<?php

namespace App\Http\Resources;

use App\Models\Referral;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Referral
 */
class ReferralResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->referredMaster->name,
            'created_at' => $this->created_at,
            'rewarded' => $this->status === Referral::STATUS_REWARDED,
            'payment' => $this->referralEarning->amount ?? 0,
        ];
    }
}

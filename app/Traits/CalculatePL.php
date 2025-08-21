<?php

namespace App\Traits;

trait CalculatePL
{
    public function calculateProfitAndLoss($t_amt, $cross_amt, $claim, $cross_claim)
    {
        // dd($claim);

        return ($t_amt + $cross_amt) - $claim * 100 - $cross_claim * 100;
    }
}

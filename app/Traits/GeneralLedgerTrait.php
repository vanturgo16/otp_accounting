<?php

namespace App\Traits;
use App\Models\GeneralLedger;
use App\Models\MstAccountCodes;

trait GeneralLedgerTrait {
    public function storeGeneralLedger($ref_number, $date_transaction, $id_account_code, $transaction, $amount, $source)
    {
        return GeneralLedger::create([
            'ref_number' => $ref_number,
            'date_transaction' => $date_transaction,
            'id_account_code' => $id_account_code,
            'transaction' => $transaction,
            'amount' => $amount,
            'source' => $source,
        ]);
    }

    public function updateBalanceAccount($id_account_code, $amount, $type)
    {
        // Get First Balance in Account
        $account = MstAccountCodes::where('id', $id_account_code)->first();
        $accountAmount = floatval($account->balance);
        $accountType = $account->balance_type; 
        
        // Initiate Incoming
        $incomingAmount = floatval($amount);
        // Reverse Type
        $incomingType = ($type == "D") ? "K" : "D";

        if ($accountType === "D" && $incomingType === "K") {
            $result = $accountAmount - $incomingAmount;
            if ($result >= 0) {
                $accountAmount = $result;
                $accountType = "D";
            } else {
                $accountAmount = abs($result);
                $accountType = "K";
            }
        } elseif ($accountType === "K" && $incomingType === "D") {
            $result = $accountAmount - $incomingAmount;
            if ($result > 0) {
                $accountAmount = $result;
                $accountType = "K";
            } else {
                $accountAmount = abs($result);
                $accountType = "D";
            }
        } elseif ($accountType === $incomingType) {
            $accountAmount += $incomingAmount;
        }
        
        // Format account amount to 3 decimal places
        $accountAmount = number_format($accountAmount, 3, '.', '');

        // Return Update Account Balance
        return MstAccountCodes::where('id', $id_account_code)
            ->update([
                'balance' => $accountAmount,
                'balance_type' => $accountType,
                'is_used' => 1,
            ]);
    }
}
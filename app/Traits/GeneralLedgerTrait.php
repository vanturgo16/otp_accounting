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
        $account = MstAccountCodes::findOrFail($id_account_code);

        $balance = (float) $account->balance;
        $balanceType = $account->balance_type;
        $incoming = (float) $amount;
        $incomingType = $type;

        // if same type, just add
        if ($balanceType === $incomingType) {
            $balance += $incoming;
        } else {
            // different type → subtract
            $result = $balance - $incoming;

            if ($result > 0) {
                $balance = $result; // keep original type (it "wins")
            } elseif ($result < 0) {
                $balance = abs($result);
                $balanceType = $incomingType; // incoming "wins"
            } else {
                $balance = 0;
                $balanceType = "D"; // default when zero (or set to null if you prefer)
            }
        }

        // round to 3 decimals
        $balance = round($balance, 3);

        return $account->update([
            'balance' => $balance,
            'balance_type' => $balanceType,
            'is_used' => 1,
        ]);
    }
}
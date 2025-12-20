<?php

namespace App\Traits;
use DateTime;
use App\Models\GeneralLedger;
use App\Models\MstAccountCodes;
use App\Models\DeliveryNote;

trait GeneralLedgerTrait {
    public function storeGeneralLedger($id_ref, $ref_number, $date_transaction, $id_account_code, $transaction, $amount, $note, $source, $ref_source)
    {
        return GeneralLedger::create([
            'id_ref' => $id_ref,
            'ref_number' => $ref_number,
            'date_transaction' => $date_transaction,
            'id_account_code' => $id_account_code,
            'transaction' => $transaction,
            'amount' => $amount,
            'note' => $note,
            'source' => $source,
            'ref_source' => $ref_source,
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
    
    public function getCustomerFromDN($id)
    {
        return DeliveryNote::select(
                'delivery_notes.dn_number', 
                'delivery_notes.id_master_customers',
                'master_customers.name as customer_name',
                'master_customers.tax_number',
                'master_currencies.currency_code',
                'master_salesmen.name as salesman_name',
                'master_customer_addresses.*',
                'master_customer_addresses.email as email_customer',
                'master_customer_addresses.telephone as phone_customer',
                'master_customer_addresses.fax as fax_customer',
                'master_provinces.province',
                'master_countries.country',
                'sales_orders.reference_number as po_number'
            )
            ->leftjoin('master_customers', 'delivery_notes.id_master_customers', 'master_customers.id')
            ->leftjoin('master_currencies', 'master_customers.id_master_currencies', 'master_currencies.id')
            // ->leftjoin('master_salesmen', 'delivery_notes.id_master_salesman', 'master_salesmen.id')
            ->leftjoin('sales_orders', 'delivery_notes.id_sales_orders', 'sales_orders.id')
            ->leftjoin('master_salesmen', 'sales_orders.id_master_salesmen', 'master_salesmen.id')
            ->leftjoin('master_customer_addresses', 'master_customers.id', 'master_customer_addresses.id_master_customers')
            ->leftjoin('master_provinces', 'master_customer_addresses.id_master_provinces', 'master_provinces.id')
            ->leftjoin('master_countries', 'master_customer_addresses.id_master_countries', 'master_countries.id')
            ->whereIn('master_customer_addresses.type_address', ['Same As (Invoice, Shipping)', 'Invoice'])
            ->where('delivery_notes.id', $id)
            ->first();
    }

    

    public function formatDateToIndonesian($date)
    {
        $dateTime = new DateTime($date);
        $formattedDate = $dateTime->format('d F Y');
        $indonesianMonths = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];
        return str_replace(array_keys($indonesianMonths), array_values($indonesianMonths), $formattedDate);
    }
    public function formatDateToEnglish($date)
    {
        return (new DateTime($date))->format('d F Y');
    }
    public function terbilang($number) 
    {
        $number = abs($number);
        $words = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $temp = "";
        if ($number < 12) {
            $temp = " " . $words[$number];
        } else if ($number < 20) {
            $temp = $this->terbilang($number - 10) . " Belas ";
        } else if ($number < 100) {
            $temp = $this->terbilang(intval($number / 10)) . " Puluh " . $this->terbilang($number % 10);
        } else if ($number < 200) {
            $temp = " Seratus " . $this->terbilang($number - 100);
        } else if ($number < 1000) {
            $temp = $this->terbilang(intval($number / 100)) . " Ratus " . $this->terbilang($number % 100);
        } else if ($number < 2000) {
            $temp = " Seribu " . $this->terbilang($number - 1000);
        } else if ($number < 1000000) {
            $temp = $this->terbilang(intval($number / 1000)) . " Ribu " . $this->terbilang($number % 1000);
        } else if ($number < 1000000000) {
            $temp = $this->terbilang(intval($number / 1000000)) . " Juta " . $this->terbilang($number % 1000000);
        } else if ($number < 1000000000000) {
            $temp = $this->terbilang(intval($number / 1000000000)) . " Milyar " . $this->terbilang(fmod($number, 1000000000));
        } else if ($number < 1000000000000000) {
            $temp = $this->terbilang(intval($number / 1000000000000)) . " Trilyun " . $this->terbilang(fmod($number, 1000000000000));
        }
        return trim($temp);
    }
    public function terbilangWithDecimal($number)
    {
        if (strpos($number, '.') !== false) {
            [$intPart, $decPart] = explode('.', (string)$number, 2);
            $result = $this->terbilang((int)$intPart) . " Koma";
            // Read Each Digits
            $decWords = [];
            $words = array("Nol", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
            foreach (str_split($decPart) as $digit) {
                $decWords[] = $words[$digit];
            }
            $result .= " " . implode(" ", $decWords);
            return trim($result);
        } else {
            return $this->terbilang((int)$number);
        }
    }

    function normalizeOpeningBalance($value)
    {
        $value = str_replace(['.', ','], ['', '.'], $value);
        return (float) number_format((float) $value, 2, '.', '');
    }

    function normalizePrice($value)
    {
        $value = str_replace(['.', ','], ['', '.'], $value);
        return (float) number_format((float) $value, 2, '.', '');
    }

    function decimal3($value) {
        return number_format((float) $value, 3, '.', '');
    }
    
    public function getManualType() {
        return [
            'Bukti Kas Keluar',
            'Bukti Kas Masuk',
            'Bukti Bank Keluar',
            'Bukti Bank Masuk',
        ];
    }
}
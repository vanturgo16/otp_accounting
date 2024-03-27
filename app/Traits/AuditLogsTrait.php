<?php

namespace App\Traits;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Browser;

trait AuditLogsTrait {
    public function auditLogs($username,$ipAddress,$location,$access_from,$activity)
    {
        $insert_auditLog=AuditLog::create([
            'username' => $username,
            'ip_address' => $ipAddress,
            'location' => $location,
            'access_from' => $access_from,
            'activity' => $activity,
        ]);
    }

    public function auditLogsShort($activity)
    {
        $username = auth()->user()->email; 
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $location = '0';
        $access_from = Browser::browserName();

        $insert_auditLog=AuditLog::create([
            'username' => $username,
            'ip_address' => $ipAddress,
            'location' => $location,
            'access_from' => $access_from,
            'activity' => $activity,
        ]);
    }
}
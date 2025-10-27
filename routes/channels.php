<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('qr-login.{sessionId}', function ($sessionId) {
    return true;
});
Broadcast::channel('App.User.{id}', function ($user, $id) {
     return (int)$user->id === (int)$id;
});

Broadcast::channel('Dashboard.{companyId}', function ($user, $companyId) {
    $inCompany = (int) $user->company_id === (int) $companyId;
    $isMasterDept = optional($user->departement)->name === 'Master';
    return $inCompany && ( (int)$user->role_id === 0 || $isMasterDept );
});

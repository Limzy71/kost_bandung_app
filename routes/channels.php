<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.moderations', function () {
    return Auth::check() && Auth::user()->role === 'admin';
});

Broadcast::channel('admin.inbox', function () {
    return Auth::check() && Auth::user()->role === 'admin';
});

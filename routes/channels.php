<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('admin.moderations', function ($user) {
    return $user && $user->role === 'admin';
});

Broadcast::channel('admin.inbox', function ($user) {
    return $user && $user->role === 'admin';
});

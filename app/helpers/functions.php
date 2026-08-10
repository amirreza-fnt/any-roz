<?php

function message($type, $message): void
{
    session()->flash('message', [
        'type' => $type,
        'message' => $message,
    ]);
}

function admin_home_url(): string
{
    $admin = auth('admin')->user();
    if (! $admin) {
        return route('admin.login');
    }

    return \App\Support\AdminAccess::firstAccessibleUrl($admin) ?? route('admin.login');
}

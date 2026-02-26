<?php

namespace People\Providers;

use Illuminate\Support\Facades\Vite;

class RegisterAssets implements Provider
{
    public function __construct()
    {
        add_action('admin_head', [$this, 'enqueueEditorAssets']);
    }

    public function register()
    {
        //
    }

    public function enqueueEditorAssets()
    {
        
    }
}

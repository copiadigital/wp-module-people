<?php

namespace People\Providers;

class RegisterAssets implements Provider
{
    public function __construct()
    {
        add_action('init', [$this, 'enqueue']);
    }

    public function register()
    {
        //
    }

    public function enqueue()
    {

    }
}

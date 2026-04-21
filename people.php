<?php

$boot = new People\Providers\PeopleServiceProvider;
$boot->register();

add_action('init', [$boot, 'boot']);

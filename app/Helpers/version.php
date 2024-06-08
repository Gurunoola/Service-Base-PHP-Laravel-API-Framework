<?php

if (! function_exists('app_version')) {
    function app_version() {
        return json_decode(file_get_contents(base_path('composer.json')))->version;
    }
}
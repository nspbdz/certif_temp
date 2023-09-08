<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Validator::extend('max_html', function ($attribute, $value, $parameters, $validator) {
            $maxCharacters = (int) $parameters[0];
            $strippedValue = strip_tags($value);
            $characterCount = mb_strlen(html_entity_decode(preg_replace('/\s+|&nbsp;/', '', $strippedValue)));

            return $characterCount <= $maxCharacters;
        });

        Validator::replacer('max_html', function ($message, $attribute, $rule, $parameters) {
            return str_replace(':max_html', $parameters[0], $message);
        });
    }
}

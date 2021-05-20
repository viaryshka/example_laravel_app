<?php

namespace App\Helpers;

class LanguagesHelper
{
    public function checkLang($lang)
    {
        $languages = config('brikk.languages');
        if (in_array($lang, $languages)) {
            app()->setLocale($lang);
        }
    }

    public function getListLanguages()
    {
        return config('brikk.languages');
    }

    public function getDefaultLanguage()
    {
        return config('app.locale');
    }

    public function getStripeLangCode($lang)
    {
        $languages = [
            'en' => 'en-UK',
            'fr' => 'fr-FR',
            'nl' => 'nl-NL',
        ];
        if (array_key_exists($lang, $languages)) {
            return $languages[$lang];
        }

        return 'en-UK';
    }
}

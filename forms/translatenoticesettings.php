<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/TranslateNotice/lib/MicrosoftTranslator.php';

class TranslateNoticeSettingsForm extends Form
{
    function __construct($out=null)
    {
        parent::__construct($out);
    }

    function id()
    {
        return 'translate_notice_settings';
    }

    function formClass()
    {
        return 'form_settings';
    }


    function action()
    {
        return common_local_url('translatenoticesettings');
    }

    function formData()
    {
        $user = common_current_user();

        $targetLanguage = Translate_notice::getTargetLanguage($user); // Get language from db. Will be null if never set.

        // Get auth info
        $client_id = common_config('translatenotice', 'client_id');
        $client_secret = common_config('translatenotice', 'client_secret');

        // Get supported languages
        $translator = new MicrosoftTranslator($client_id, $client_secret);
        $languageCodes = $translator->getLanguagesForTranslate();
        $languageNames = $translator->getLanguageNames($languageCodes);
        $languages = array();

        // Build list of <options>s
        for ($i = 0; $i < count($languageNames); $i += 1) {
            $languageCode = $languageCodes[$i]->__toString();

            $languages[$languageCode] = $languageNames[$i];
        }

        // Start outputing HTML
        $this->out->elementStart('fieldset');

        $this->out->elementStart('ul', 'form_data');

        // Supported language list
        $this->li();
        $this->out->dropdown(
            'language',                 // id
            'Translate notices into',   // label
            $languages,                 // content
            null,                       // instructions
            true,                       // 1st <option> is blank
            $targetLanguage             // selected <option> on pageload
        );
        $this->unli();

        $this->elementEnd('ul');
        $this->out->elementEnd('fieldset');
    }

    function formActions()
    {
        $this->out->submit('translate-notice-settings-submit', _m('BUTTON', 'Save'), 'submit', 'submit');
    }
}

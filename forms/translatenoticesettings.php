<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/TranslateNotice/lib/YandexTranslator.php';

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

        // Get language from db. Will be null if never set.
        $targetLanguage = Translate_notice::getTargetLanguage($user);

        // Get auth info
        $api_key= common_config('translatenotice', 'api_key');

        // Get supported languages
        $translator = new YandexTranslator($api_key);
        $languages = $translator->getSupportedLanguages();

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

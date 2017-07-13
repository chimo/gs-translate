<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/TranslateNotice/lib/YandexTranslator.php';

class TranslateNoticeSettingsAction extends SettingsAction
{
    function title()
    {
        return _m('Translate Notice Settings');
    }

    protected function doPost()
    {
        Translate_notice::saveTargetLanguage(common_current_user(), $this->trimmed('language'));

        return _('Settings saved.');
    }

    function showContent()
    {
        $form = new TranslateNoticeSettingsForm($this);
        $form->show();
    }
}

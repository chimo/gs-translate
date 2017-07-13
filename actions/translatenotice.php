<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/TranslateNotice/lib/YandexTranslator.php';

class TranslatenoticeAction extends Action
{
    function handle()
    {
        GNUsocial::setApi(true);

        $user = common_current_user();

        if (!common_logged_in()) { // Make sure we're logged in
            $this->clientError(_('Not logged in.'));

            return;
        } else if (!common_is_real_login()) { // Make _really_ sure we're logged in...
            common_set_returnto($this->selfUrl());

            if (Event::handle('RedirectToLogin', array($this, $user))) {
                common_redirect(common_local_url('login'), 303);
            }
        }

        // Get plugin settings
        $plugins = GNUsocial::getActivePlugins();
        $transl_attrs = $plugins['TranslateNotice'];

        // Get API key
        $api_key = $transl_attrs['api_key'];

        // Params
        $text = $this->trimmed('text');
        $target_language = Translate_notice::getTargetLanguage($user);

        $translator = new YandexTranslator($api_key);
        $results = $translator->translate($text, $target_language);

        header('Content-Type: application/json; charset=utf-8');

        print $results;
    }

    function isReadOnly($args) {
          return true;
    }
}

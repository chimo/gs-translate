<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/TranslateNotice/lib/BingTranslator.php';

class TranslateNoticeAction extends Action
{
    private $language;
    private $urlPath;

    function prepare($args)
    {
        parent::prepare($args);

        if (!common_logged_in()) { // Make sure we're logged in
            $this->clientError(_('Not logged in.'));
            return;
        } else if (!common_is_real_login()) { // Make _really_ sure we're logged in...
            common_set_returnto($this->selfUrl());
            $user = common_current_user();
            if (Event::handle('RedirectToLogin', array($this, $user))) {
                common_redirect(common_local_url('login'), 303);
            }
        } else { // k, I think by now we're logged in. For realz.
            $this->user = common_current_user();
        }

        return true;
    }

    function handle($args) {
        parent::handle($args);

        // User submitted form, get the selected language
        if (isset($args['language'])) {
            $this->languageCode = $args['language'];
        }

        // URL to use in <form> action
        $this->urlPath = $args['p'];

        // Print page content
        $this->showPage();
    }

    function title() {
        return _m('Translate Notice');
    }

    function showContent() {
        $user = common_current_user();

        // We have form-submitted language in this request.
        if (isset($this->languageCode)) {
            Translate_notice::saveTargetLanguage($user, $this->languageCode); // Save it to the db
            $targetLanguage = $this->languageCode; // Language that should be pre-selected in the dropdown
        } else { // No new language submitted
            $targetLanguage = Translate_notice::getTargetLanguage($user); // Get language from db. Will be null if never set.
        }

        // Get auth info
        $client_id = common_config('translatenotice', 'client_id');
        $client_secret = common_config('translatenotice', 'client_secret');

        // Get supported languages
        $translator = new BingTranslator($client_id, $client_secret);
        $languageCodes = $translator->getLanguagesForTranslate();
        $languages = $translator->getLanguageNames($languageCodes);

        // <form>
        $this->elementStart('form', array('action' => common_local_url('translatenotice'), 'method' => 'POST'));

        // <label>
        $this->element('label', array('for' => 'gs-trn-languages'), 'Translate notices into:'); // TODO: TRANS

        // <select>
        $this->elementStart('select', array('id' => 'gs-trn-languages', 'name' => 'language'));
        for ($i = 0; $i < count($languages); $i += 1) {
            $currLanguageCode = $languageCodes[$i]->__toString();
            $attributes = array('value' => $currLanguageCode);

            // If this language is the user's target language, have it selected in the dropdown
            if ($targetLanguage === $currLanguageCode) {
                $attributes['selected'] = 'selected';
            }

            $this->element('option', $attributes, $languages[$i]);
        }
        $this->elementEnd('select');

        // Submit button
        $this->element('input', array('type' => 'submit'));

        // </form>
        $this->elementEnd('form');
    }

    function isReadOnly($args) {
        if (isset($args['language'])) {
            return false;
        } else {
            return true;
        }
    }
}

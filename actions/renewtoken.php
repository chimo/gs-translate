<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/plugins/TranslateNotice/lib/BingTranslator.php';

class RenewtokenAction extends Action
{
    function handle($args)
    {
        GNUsocial::setApi(true);

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

        // Get auth info
        $client_id = common_config('translatenotice', 'client_id');
        $client_secret = common_config('translatenotice', 'client_secret');

        $translator = new BingTranslator($client_id, $client_secret);
        $accessToken = $translator->getAccessToken();

        header('Content-Type: application/json; charset=utf-8');
        print json_encode($accessToken);
    }

    function isReadOnly($args) {
          return true;
    }
}

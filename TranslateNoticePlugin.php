<?php

if (!defined('GNUSOCIAL')) {
    exit(1);
}

require_once INSTALLDIR . '/local/plugins/TranslateNotice/lib/YandexTranslator.php';

class TranslateNoticePlugin extends Plugin
{
    const VERSION = '0.1.0';

    private $translator;

    function onRouterInitialized($m) {
        $m->connect(
            'settings/translatenotice', array(
                    'action' => 'translatenoticesettings'
                )
            );

        $m->connect(
            'main/translatenotice/translatenotice', array(
                    'action' => 'translatenotice'
                )
            );

        return true;
    }

    function onEndShowNoticeOptionItems($item) {
        if (!common_logged_in()) {
            return;
        }

        $item->out->element('a', array('href' =>  '#', 'class' => 'gs-translate'), 'Translate this notice');

        return true;
    }

    function onEndAccountSettingsNav($action) {
        $action->elementStart('li');
        $action->element('a', array('href' => common_local_url('translatenoticesettings')), 'Translate Notices');
        $action->elementEnd('li');

        return true;
    }

    function onEndShowScripts($action) {
        if (common_logged_in()) {
            $action->script($this->path('js/gs-translate.js'));
        }

        return true;
    }

    function onEndShowStyles($action) {
        if (common_logged_in()) {
            $action->cssLink($this->path('css/gs-translate.css'));
        }

        return true;
    }

    function onCheckSchema() {
        $schema = Schema::get();
        $schema->ensureTable('translate_notice', Translate_notice::schemaDef());

        return true;
    }

    function onPluginVersion(array &$versions)
    {
        $versions[] = array('name' => 'TranslateNotice',
                            'version' => self::VERSION,
                            'author' => 'chimo',
                            'homepage' => 'https://github.com/chimo/gs-translate',
                            'description' =>
                            // TRANS: Plugin description.
                            _m('Translate notices.'));
        return true;
    }
}

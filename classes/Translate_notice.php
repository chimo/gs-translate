<?php

if (!defined('STATUSNET')) {
    exit(1);
}

class Translate_notice extends Managed_DataObject {
    public $__table = 'translate_notice'; // table name
    public $user_id; // int(11) not_null
    public $target_lang; // varchar(255) -- probably overkill...

    public static function schemaDef() {
        return array(
            'fields' => array(
                'user_id' => array(
                    'type' => 'int',
                    'not null' => true
                ),
                'target_lang' => array(
                    'type' => 'varchar',
                    'length' => 255
                ),
            ),
            'primary key' => array('user_id'),
        );
    }

    public static function getByUserId($userid) {
        return self::getKV('user_id', $userid);
    }

    public static function getTargetLanguage($user) {
        $tn = self::getByUserId($user->id);

        if (empty($tn)) {
            return null;
        } else {
            return $tn->target_lang;
        }
    }

    public static function saveTargetLanguage($user, $language) {
        $tn = new Translate_notice();
        $tn->user_id = $user->id;
        $tn->target_lang = $language;

        // User never chose a language; insert().
        if (empty(self::getByUserId($user->id))) {
            $tn->insert();
        } else { // User is updating language; update().
            $tn->update();
        }
    }
}

GNU social Translate Notice
===========================

Install
---------

1. Navigate to your /local/plugins directory (create it if it doesn't exist)
2. `git clone https://github.com/chimo/gs-translate.git TranslateNotice`

Configure
---------

1. Get an API key from Yandex: https://tech.yandex.com/keys/get/?service=trnsl
2. Add the following to your GNU social config.php (make sure to replace the 'api_key' value with yours):

```
addPlugin('TranslateNotice', array(
    'api_key'     => 'YOUR_API_KEY'
));
```

Usage
---------

Users can choose which language they want notices to be translated in by going
in the "Translate Notices" section of their Settings.

When logged in, users will see a globe icon under each notice:  
![Timeline icons](https://static.chromic.org/repos/gs-translate/gs-translate-button.png)

Clicking on it should show the notice in the language they selected above:  
![Translation results](https://static.chromic.org/repos/gs-translate/gs-translate-result.png)

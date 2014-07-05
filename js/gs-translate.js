(function () {
    'use strict';

    var token = window.gsTranslate.accessToken,
        translate;


    translate = function (text) {
        var appId        = 'Bearer ',    // FIXME legacy: http://msdn.microsoft.com/en-us/library/hh454950.aspx
            to           = 'en',
            callbackName = 'gsTranslate.callback',
            encode       = encodeURIComponent,
            script;

        script = document.createElement('script');

        script.src = 'http://api.microsofttranslator.com/V2/Ajax.svc/Translate' +
            '?appId=' + encode(appId) + encode(token) +
            '&to=' + encode(to) +
            '&text=' + encode(text) +
            '&oncomplete=' + encode(callbackName);

        document.body.appendChild(script);
    };

    $(document).on('click', '.gs-translate', function (e) {
        e.preventDefault();

        var $this = $(this),
            text = $this.closest('.notice').find('.e-content').first().text();

        translate(text);
    });


    window.gsTranslate.callback = function (response) {
//        console.log(response);
        alert(response);
    };
}());

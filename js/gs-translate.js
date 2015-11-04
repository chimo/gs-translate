/*global _peopletagAC: false*/
( function( window, $ ) {
    "use strict";

    var gsTranslate = window.gsTranslate,
        encode = encodeURIComponent,
        translate,
        installdir = _peopletagAC.replace( "/main/peopletagautocomplete", "" ),
        $dialog = $( "<div class='gs-translate-dialog'>" ).dialog( { autoOpen: false } )/*.appendTo( "body" )*/;

    translate = function( text ) {
        var appId        = "Bearer ",    /* FIXME legacy: http://msdn.microsoft.com/en-us/library/hh454950.aspx */
            to           = gsTranslate.targetLanguage || "en",
            callbackName = "gsTranslate.callback",
            script;

        script = document.createElement( "script" );

        script.src = "//api.microsofttranslator.com/V2/Ajax.svc/Translate" +
            "?appId=" + encode( appId ) + encode( gsTranslate.accessToken.token ) +
            "&to=" + encode( to ) +
            "&text=" + encode( text ) +
            "&oncomplete=" + encode( callbackName );

        document.body.appendChild( script );
    };

    $( document ).on( "click", ".gs-translate", function( e ) {
        e.preventDefault();

        var $this = $( this ),
            text = $this.closest( ".notice" ).find( ".e-content" ).first().text();

        if ( gsTranslate.accessToken.expires < Date.now() ) {
            /* TODO: Error handling */
            $.getJSON( installdir + "/main/translatenotice/renewtoken", function( data ) {
                gsTranslate.accessToken.token = data.token;
                gsTranslate.accessToken.expires = data.expires;

                translate( text );
            } );
        } else {
            translate( text );
        }
    } );

    gsTranslate.callback = function( response ) {
        $dialog
            .closest( ".ui-dialog" )
            .find( ".ui-dialog-title" ).html(
            "<a style='text-decoration: none;' href='http://aka.ms/MicrosoftTranslatorAttribution'>" +
                "Translated by " +
                "<img style='vertical-align: middle;' src='" + installdir +
                    "/plugins/TranslateNotice/extlib/MicrosoftTranslator/MSFT_logo_rgb_C-Gray_D.png' alt='Microsoft Translator'>" +
            "</a>"
        );

        $dialog.text( response ).dialog( "open" );
    };
}( window, jQuery ) );

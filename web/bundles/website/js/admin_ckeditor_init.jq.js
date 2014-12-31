$(document).ready(function(){
    // Slideshow dialog - consider: http://docs.ckeditor.com/#!/api/CKEDITOR.dialog.definition.html
    // AnoSlide lightweight Jquery - http://www.anowave.com/ultra-lightweight-responsive-carousel/
    CKEDITOR.plugins.addExternal('clipboard', '/bundles/website/js/ckeditor/plugins/clipboard/', 'plugin.js');
    CKEDITOR.plugins.addExternal('savebtn', '/bundles/website/js/ckeditor/plugins/savebtn/', 'plugin.js');
    CKEDITOR.plugins.addExternal('dialog', '/bundles/website/js/ckeditor/plugins/dialog/', 'plugin.js');
    CKEDITOR.plugins.addExternal('dialogui', '/bundles/website/js/ckeditor/plugins/dialogui/', 'plugin.js');
    CKEDITOR.plugins.addExternal('lineutils', '/bundles/website/js/ckeditor/plugins/lineutils/', 'plugin.js');
    CKEDITOR.plugins.addExternal('widget', '/bundles/website/js/ckeditor/plugins/widget/', 'plugin.js');
    CKEDITOR.plugins.addExternal('oembed', '/bundles/website/js/ckeditor/plugins/oembed/', 'plugin.js');
    CKEDITOR.disableAutoInline = true;
    CKEDITOR.stylesSet.add( 'my_styles', [
        { name: 'Blok titel', element: 'span', attributes: { 'class' : 'h3' } },
        { name: 'Fremhaevet', element: 'span', attributes: { 'class': 'fremhaevet' } },
        { name: 'Understreget', element: 'span', attributes: { 'class': 'understreget' } },
        { name: 'SlideShow', element: 'ul', attributes: {'class': 'carousel'}}
    ] );

    $('.ck_editable').each(function(){
        CKEDITOR.inline($(this).attr('id'),{
            language: 'da',
            extraPlugins : 'savebtn,oembed',
            saveSubmitURL: '{{ path('websitestatic_page_saveslot') }}',
            stylesSet : 'my_styles',
            toolbar: [{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ]},'|', { name: 'media', items: ['Image','oembed'] },'/', { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] }, { name: 'styles', items: [ 'Styles', 'RemoveFormat']}, '/', { name: 'lists', items: ['NumberedList', 'BulletedList', 'Blockquote'] }, { name: 'savebtn', items: ['savebtn']}],
            filebrowserUploadUrl : '/bundles/website/js/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files&currentFolder=/',
            filebrowserImageUploadUrl : '/bundles/website/js/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images&currentFolder=/',
            filebrowserFlashUploadUrl : '/bundles/website/js/ckeditor/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
        } );});
    // CKFinder integration: http://docs.cksource.com/CKFinder_2.x/Developers_Guide/PHP/CKEditor_Integration
    //CKFinder.setupCKEditor( null, '/bundles/website/js/ckeditor/plugins/ckfinder' );
});
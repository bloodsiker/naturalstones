/*jslint todo: true*/
/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.add('imageWatermark', {
        lang: ['en', 'ru', 'uk']
    });

    CKEDITOR.on('dialogDefinition', function (event) {
        var dialogName = event.data.name,
            dialogDefinition = event.data.definition,
            uploadTab = dialogDefinition.getContents('Upload'),
            editor = event.editor;

        if (dialogName == 'image' && uploadTab)
        {
            var watermarks = [[editor.lang.imageWatermark.empty, ''], [editor.lang.imageWatermark.vogue, '_vogue'], [editor.lang.imageWatermark.buy, '_buy']],
                onShowOldImplementation = dialogDefinition.onShow;

            uploadTab.elements.splice(uploadTab.elements.length - 1, 0, {
                type : 'select',
                id : 'watermark',
                label : editor.lang.imageWatermark.title,
                items: watermarks,
                default: ''
            });

            dialogDefinition.onShow = function(e) {
                onShowOldImplementation.apply(this, e);
                var dialog = this,
                    uploadButton = uploadTab.get('uploadButton');

                uploadButton['filebrowser']['onSelect'] = function(fileUrl, errorMessage) {
                    var selectWatermark = dialog.getValueOf('Upload', 'watermark'),
                        oldUploadFilterParam = getUrlParam(uploadTab.get('upload').action, 'filter'),
                        targetElement = this.filebrowser.target || null;

                    if(selectWatermark && targetElement) {
                        var newFileUrl = fileUrl.replace(oldUploadFilterParam, oldUploadFilterParam + selectWatermark),
                            target = targetElement.split(':');

                        dialog.getContentElement(target[0], target[1]).setValue(newFileUrl);
                        dialog.selectPage(target[0]);

                        return false;
                    }
                }
            };
        }
    });

    function getUrlParam( url, paramName ) {
        var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' );
        var match = url.match( reParam );
        return ( match && match.length > 1 ) ? match[1] : null;
    }
}());

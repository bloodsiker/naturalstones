/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('instagram', function (editor) {
        return {
            title: editor.lang.shortcode.instagram.dialog_title,
            minWidth: 400,
            minHeight: 200,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.instagram.tab_basic_label,
                    elements: [
                        {
                            id: 'code_instagram',
                            type: 'textarea',
                            label: editor.lang.shortcode.instagram.code_instagram,
                            rows: 10,
                            inputStyle: 'height: 100%; white-space: pre-wrap',
                            validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.shortcode.instagram.err_text_empty),
                            setup: function (widget) {
                                this.setValue(widget.data.code_instagram);
                            },
                            commit: function (widget) {
                                var insertCode = this.getValue();
                                // INSTAGRAM
                                insertCode = insertCode.replace(/<a\W*href=".*instagram\.com.*">.*<\/a>/g, 'INSTAGRAM');
                                // TWITTER
                                insertCode = insertCode.replace(/<p\W*lang=".*"\W*dir=".*">.*<\/p>/g, '');
                                insertCode = insertCode.replace(/(?!<blockquote\W+class="twitter-tweet"\W*[^>]+>)&(.*?)(?=<a\W*href=".*twitter\.com.*">)/g, 'TWITTER');
                                widget.setData('code_instagram', insertCode);
                            }
                        },
                    ]
                },
            ]
        };
    });
}());

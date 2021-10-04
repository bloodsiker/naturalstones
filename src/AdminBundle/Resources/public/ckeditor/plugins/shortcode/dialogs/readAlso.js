/**
 * readAlso dialog
 */

/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('readAlsoDialog', function (editor) {
        return {
            title: editor.lang.shortcode.readAlso.dialog_title,
            width: 210,
            height: 80,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.readAlso.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            width: ['100%'],
                            children: [
                                {
                                    id: 'placeholder-selector',
                                    type: 'select',
                                    label: editor.lang.shortcode.readAlso.select_placeholder,
                                    validate: CKEDITOR.dialog.validate.regex(/^content_[0-9]+$/, editor.lang.shortcode.readAlso.err_text_empty),
                                    inputStyle: 'height: 100%; white-space: pre-wrap;',
                                    items: [
                                        [ editor.lang.shortcode.readAlso.select_content_1, 'content_1' ],
                                        [ editor.lang.shortcode.readAlso.select_content_2, 'content_2' ],
                                        [ editor.lang.shortcode.readAlso.select_content_3, 'content_3' ],
                                        [ editor.lang.shortcode.readAlso.select_content_4, 'content_4' ],
                                        [ editor.lang.shortcode.readAlso.select_content_5, 'content_5' ]
                                    ], 'default': editor.lang.shortcode.readAlso.select_content_1,
                                    setup: function (widget) {
                                        this.setValue(widget.data.holder);
                                    },
                                    commit: function (widget) {
                                        widget.setData('holder', this.getValue());
                                    }
                                }
                            ]
                        }
                    ]
                }
            ]
        };
    });
}());

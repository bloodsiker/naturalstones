/**
 * Quote dialog
 */

/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('quoteDialog', function (editor) {
        return {
            title: editor.lang.quote.dialog_title,
            minWidth: 400,
            minHeight: 200,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.quote.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['100%'],
                            children: [
                                {
                                    id: 'quote-text',
                                    type: 'textarea',
                                    label: editor.lang.quote.text,
                                    rows: 5,
                                    inputStyle: 'height: 100%; white-space: pre-wrap',
                                    validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.quote.err_text_empty),
                                    setup: function (widget) {
                                        this.setValue(widget.data.text);
                                    },
                                    commit: function (widget) {
                                        widget.setData('text', this.getValue());
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

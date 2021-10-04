/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('adaptiveIframe', function (editor) {
        return {
            title: editor.lang.shortcode.adaptiveIframe.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.adaptiveIframe.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'src',
                                    type: 'text',
                                    label: editor.lang.shortcode.adaptiveIframe.src,
                                    validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.shortcode.adaptiveIframe.err_id_empty),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                            ]
                        }
                    ]
                }
            ]
        };
    });
}());

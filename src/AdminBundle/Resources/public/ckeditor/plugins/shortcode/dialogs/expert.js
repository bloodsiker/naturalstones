/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('expert', function (editor) {
        return {
            title: editor.lang.shortcode.expert.dialog_title,
            minWidth: 400,
            minHeight: 200,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.expert.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'number',
                                    min: 1,
                                    label: editor.lang.shortcode.expert.id,
                                    validate: CKEDITOR.dialog.validate.functions(
                                        CKEDITOR.dialog.validate.notEmpty(),
                                        CKEDITOR.dialog.validate.integer(),
                                        editor.lang.shortcode.expert.err_id_empty
                                    ),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                    id: 'expert_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.expert.expert_select_label,
                                    title: editor.lang.shortcode.expert.expert_select_title,
                                    style: 'margin-top: 17px;',
                                    filebrowser: {
                                        action: 'Browse',
                                        url: editor.config.filebrowserExpertListUrl,
                                        target: 'basic-tab:id'
                                    }
                                }
                            ]
                        },
                        {
                            id: 'text',
                            type: 'textarea',
                            label: editor.lang.shortcode.expert.text,
                            rows: 10,
                            inputStyle: 'height: 100%; white-space: pre-wrap',
                            validate: CKEDITOR.dialog.validate.notEmpty(editor.lang.shortcode.expert.err_text_empty),
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
        };
    });
}());

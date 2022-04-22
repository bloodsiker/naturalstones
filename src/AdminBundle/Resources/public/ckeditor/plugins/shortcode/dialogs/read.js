/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('read', function (editor) {
        return {
            title: editor.lang.shortcode.read.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.read.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'number',
                                    min: 1,
                                    label: editor.lang.shortcode.read.id,
                                    validate: CKEDITOR.dialog.validate.functions(
                                        CKEDITOR.dialog.validate.notEmpty(),
                                        CKEDITOR.dialog.validate.integer(),
                                        editor.lang.shortcode.read.err_id_empty
                                    ),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                    id: 'article_select',
                                    type: 'button',
                                    label: editor.lang.shortcode.read.select_label,
                                    title: editor.lang.shortcode.read.select_title,
                                    style: 'margin-top: 17px;',
                                    hidden: true,
                                    filebrowser: {
                                        action: 'Browse',
                                        url: editor.config.filebrowserArticleListUrl,
                                        target: 'basic-tab:id'
                                    }
                                }
                            ]
                        },
                        {
                            id: 'title',
                            type: 'text',
                            label: editor.lang.shortcode.read.alternate_title,
                            setup: function (widget) {
                                this.setValue(widget.data.title.trim());
                            },
                            commit: function (widget) {
                                widget.setData('title', this.getValue() ? this.getValue() : ' ');
                            }
                        }
                    ]
                }
            ]
        };
    });
}());

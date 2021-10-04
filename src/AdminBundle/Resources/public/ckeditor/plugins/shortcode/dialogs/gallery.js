/*global CKEDITOR*/
/*jslint todo: true*/

(function () {
    'use strict';

    CKEDITOR.dialog.add('gallery', function (editor) {
        return {
            title: editor.lang.shortcode.gallery.dialog_title,
            minWidth: 400,
            minHeight: 95,
            contents: [
                {
                    id: 'basic-tab',
                    label: editor.lang.shortcode.gallery.tab_basic_label,
                    elements: [
                        {
                            type: 'hbox',
                            widths: ['90%', '10%'],
                            children: [
                                {
                                    id: 'id',
                                    type: 'text',
                                    label: editor.lang.shortcode.gallery.id,
                                    validate: CKEDITOR.dialog.validate.regex(/^[0-9]+$/, editor.lang.shortcode.gallery.wrong_format),
                                    setup: function (widget) {
                                        this.setValue(widget.data.id);
                                    },
                                    commit: function (widget) {
                                        widget.setData('id', this.getValue());
                                    }
                                },
                                {
                                  type: 'button',
                                  id: 'gallery_select',
                                  filebrowser: {
                                    action: 'Browse',
                                    target: 'basic-tab:id',
                                    url: editor.config.filebrowserBrowseUrl
                                  },
                                  style: 'margin-top: 17px;',
                                  hidden: true,
                                  label: editor.lang.shortcode.gallery.select_label,
                                  title: editor.lang.shortcode.gallery.select_title
                                }
                            ]
                        }
                    ]
                }
            ]
        };
    });
}());

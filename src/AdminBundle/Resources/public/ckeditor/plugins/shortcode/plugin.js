/*global CKEDITOR*/
/*jslint regexp: true*/

(function () {
    'use strict';

    CKEDITOR.plugins.add('shortcode', {
        requires: 'widget',
        icons: 'quotesc,gallerysc,quizsc,adaptiveIframesc,readAlso',
        lang: ['en', 'ru', 'uk'],
        init: function (editor) {
            CKEDITOR.dialog.add('quote', this.path + 'dialogs/quote.js');
            CKEDITOR.dialog.add('gallery', this.path + 'dialogs/gallery.js');
            CKEDITOR.dialog.add('quiz', this.path + 'dialogs/quiz.js');
            CKEDITOR.dialog.add('adaptiveIframe', this.path + 'dialogs/adaptiveIframe.js');
            CKEDITOR.dialog.add('readAlsoDialog', this.path + 'dialogs/readAlso.js');

            editor.addContentsCss(this.path + 'styles/style.css');

            editor.widgets.add('QuoteSC', {
                button: editor.lang.shortcode.quote.button_title,
                dialog: 'quote',
                template:
                    '<code class="quote">' +
                        '[QUOTE text=<span class="quote__text"></span>; image=<span class="quote__img"></span>;]' +
                    '</code>',
                allowedContent: 'code(!quote); span(!quote__text); span(!quote__img)',
                requiredContent: 'code(quote)',
                init: function () {
                    var text = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('quote__text');
                        }),
                        image = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('quote__img');
                        }),
                        id = this.element.getAttribute('id') || 'quote_' + Math.round(Math.random() * 10000);

                    if (text) {
                        this.setData('text', text.getText());
                    }

                    if (image) {
                        this.setData('image', image.getText());
                    }

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);
                    }

                    if (this.data.text) {
                        this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('quote__text');
                        }).setText(this.data.text);
                    }

                    if (this.data.image) {
                        this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('quote__img');
                        }).setText(this.data.image);
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[QUOTE id=' + (this.data.id || '') + '; text=' + (this.data.text || '') + '; image=' + (this.data.image || '') + ';]');
                }
            });

            editor.widgets.add('GallerySC', {
                button: editor.lang.shortcode.gallery.button_title,
                dialog: 'gallery',
                template:
                    '<code class="gallery">' +
                        '[GALLERY id=<span class="gallery__id"></span>;]' +
                    '</code>',
                allowedContent: 'code(!gallery); span(!gallery__id)',
                requiredContent: 'code(gallery)',
                init: function () {
                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('gallery__id');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[GALLERY id=' + this.data.id + ';]');
                }
            });

            editor.widgets.add('QuizSC', {
                button: editor.lang.shortcode.quiz.button_title,
                dialog: 'quiz',
                template:
                '<code class="quiz">' +
                '[QUIZ id=<span class="quiz__id"></span>;]' +
                '</code>',
                allowedContent: 'code(!quiz); span(!quiz__id)',
                requiredContent: 'code(quiz)',
                init: function () {
                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('quiz__id');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[QUIZ id=' + this.data.id + ';]');
                }
            });

            editor.widgets.add('AdaptiveIframeSC', {
                button: editor.lang.shortcode.adaptiveIframe.button_title,
                dialog: 'adaptiveIframe',
                template:
                '<div class="adaptiveIframe embed_16x9">' +
                '<iframe class="adaptiveIframe__id" width="" height="" src="" frameborder="0" allowfullscreen></iframe>' +
                '</div>',
                allowedContent: 'div(!adaptiveIframe); iframe(!adaptiveIframe__id)',
                requiredContent: 'div(adaptiveIframe)',
                init: function () {
                    var src = this.element.getAttribute('src');
                    if (src) {
                        this.setAttribute('src', src);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'iframe' && child.hasClass('adaptiveIframe__id');
                    });
                    if (this.data.id) {
                        if (this.data.id.indexOf("<iframe") >= 0) {
                            if (this.element) {
                                this.element.setHtml(this.data.id);
                            }
                        } else {
                            this.element.setAttribute('attr-src', this.data.id);
                            if (idElement) {
                                idElement.setAttribute('src', this.data.id);
                            }
                        }
                    }
                },
            });

            editor.widgets.add('ReadAlso', {
                button: editor.lang.shortcode.readAlso.button_title,
                dialog: 'readAlsoDialog',
                template:
                    '<code class="holder">' +
                        '[READ_ALSO holder=<span class="holder__id"></span>;]' +
                    '</code>',
                allowedContent: 'code(!holder); span(!holder__id)',
                requiredContent: 'code(holder)',
                init: function () {
                    var holder = this.element.getAttribute('holder');

                    if (holder) {
                        this.setData('holder', holder);
                    }

                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('holder__id');
                    });

                    if (this.data.holder) {
                        this.element.setAttribute('holder', this.data.holder);

                        if (idElement) {
                            idElement.setText(this.data.holder);
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[READ_ALSO holder=' + this.data.holder + ';]');
                }
            });
        },
        afterInit: function (editor) {
            var quoteRegexp = /\[QUOTE id=(.+?); text=(.*?); image=(.*?);\]/g,
                galleryRegexp = /\[GALLERY id=(.+?);\]/g,
                readAlsoReqexp = /\[READ_ALSO holder=content_(.+?);\]/g,
                quizRegexp = /\[QUIZ id=(.+?);\]/g;

            editor.dataProcessor.dataFilter.addRules({
                text: function (text, node) {
                    var dtd = node.parent && CKEDITOR.dtd[node.parent.name];

                    if (dtd && !dtd.span) {
                        return;
                    }

                    return text.replace(quoteRegexp, function (match, id, text, image) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'quote',
                                'id': id
                            });

                        if (match) {
                            outerElement.setHtml('[QUOTE text=<span class="quote__text">' + text + '</span>; image=<span class="quote__img">' + image + '</span>;]');
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'QuoteSC');

                        return widgetWrapper.getOuterHtml();
                    });
                }
            });

            editor.dataProcessor.dataFilter.addRules({
                text: function (text, node) {
                    var dtd = node.parent && CKEDITOR.dtd[node.parent.name];

                    if (dtd && !dtd.span) {
                        return;
                    }

                    return text.replace(galleryRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'gallery',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml('[GALLERY id=<span class="gallery__id">' + id + '</span>;]');
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'GallerySC');

                        return widgetWrapper.getOuterHtml();
                    });
                }
            });

            editor.dataProcessor.dataFilter.addRules({
                text: function (text, node) {
                    var dtd = node.parent && CKEDITOR.dtd[node.parent.name];

                    if (dtd && !dtd.span) {
                        return;
                    }

                    return text.replace(readAlsoReqexp, function (match, holder) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'holder',
                                'holder': 'content_'+holder
                            });

                        if (match && holder) {
                            outerElement.setHtml('[READ_ALSO id=<span class="holder__id">' + holder + '</span>;]');
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'ReadAlso');

                        return widgetWrapper.getOuterHtml();
                    });
                }
            });

            editor.dataProcessor.dataFilter.addRules({
                text: function (text, node) {
                    var dtd = node.parent && CKEDITOR.dtd[node.parent.name];

                    if (dtd && !dtd.span) {
                        return;
                    }

                    return text.replace(quizRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'quiz',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml('[QUIZ id=<span class="quiz__id">' + id + '</span>;]');
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'QuizSC');

                        return widgetWrapper.getOuterHtml();
                    });
                }
            });
        }
    });
}());

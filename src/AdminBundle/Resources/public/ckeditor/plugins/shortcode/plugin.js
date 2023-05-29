/*global CKEDITOR*/
/*jslint this: true*/
/*jslint multivar: true*/

(function () {
    'use strict';

    CKEDITOR.plugins.add('shortcode', {
        requires: 'widget,numericinput',
        icons: 'textsc,gallerysc,videosc,imagesc,expertsc,readsc,audiosc,filesc,instagramsc,onlinesc,quizsc,championshipsc',
        lang: ['en', 'ru', 'uk'],
        init: function (editor) {
            CKEDITOR.dialog.add('text', this.path + 'dialogs/text.js');
            CKEDITOR.dialog.add('gallery', this.path + 'dialogs/gallery.js');
            CKEDITOR.dialog.add('quiz', this.path + 'dialogs/quiz.js');
            CKEDITOR.dialog.add('video', this.path + 'dialogs/video.js');
            CKEDITOR.dialog.add('imageSC', this.path + 'dialogs/image.js');
            CKEDITOR.dialog.add('expert', this.path + 'dialogs/expert.js');
            CKEDITOR.dialog.add('read', this.path + 'dialogs/read.js');
            CKEDITOR.dialog.add('file', this.path + 'dialogs/file.js');
            CKEDITOR.dialog.add('audio', this.path + 'dialogs/audio.js');
            CKEDITOR.dialog.add('instagram', this.path + 'dialogs/instagram.js');
            CKEDITOR.dialog.add('championship', this.path + 'dialogs/championship.js');

            editor.addContentsCss(this.path + 'styles/style.css');

            var langCode = editor.name.indexOf('_translations_uk_') === -1 ? 'ru' : 'uk';

            editor.widgets.add('TextSC', {
                button: editor.lang.shortcode.text.button_title,
                dialog: 'text',
                template:
                    '<code class="text">' +
                    '[TEXT id=<span class="text__id"></span>;]' +
                    '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!text); span(!text__id); span(!preview); iframe;',
                requiredContent: 'code(text)',
                init: function () {
                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('text__id');
                        }),
                        previewElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                        });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="600" height="380" frameborder="0" src="'+editor.config.filebrowserTextPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[TEXT id=' + this.data.id + ';]');
                }
            });

            editor.widgets.add('ReadSC', {
                button: editor.lang.shortcode.read.button_title,
                dialog: 'read',
                template:
                    '<code class="read">' +
                    '[READ id=<span class="read__id"></span>; title=<span class="read__title"></span>;]' +
                    '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!read); span(!read__id); span(!read__title); span(!preview); iframe;',
                requiredContent: 'code(read)',
                init: function () {
                    var title = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('read__title');
                        }),
                        id = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('read__id');
                        });

                    if (title) {
                        this.setData('title', title.getText());
                    }

                    if (id) {
                        this.setData('id', id.getText());
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('read__id');
                        }),
                        title = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('read__title');
                        }),
                        previewElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                        });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (title) {
                            title.setText(this.data.title);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="110" frameborder="0" src="'+editor.config.filebrowserArticlePreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[READ id=' + (this.data.id || '') + '; title=' + (this.data.title || '') + ';]');
                }
            });

            editor.widgets.add('ImageSC', {
                button: editor.lang.shortcode.image.button_title,
                dialog: 'imageSC',
                template:
                    '<code class="image">' +
                    '[IMAGE id=<span class="image__id"></span>; title=<span class="image__title"></span>;<span class="image__blank"></span>]' +
                    '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!image); span(!image__id); span(!image__title); span(!preview); iframe;',
                requiredContent: 'code(image)',
                init: function () {
                    var title = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('image__title');
                        }),
                        id = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('image__id');
                        });

                    if (title) {
                        this.setData('title', title.getText());
                    }
                    if (id) {
                        this.setData('id', id.getText());
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('image__id');
                        }),
                        title = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('image__title');
                        }),
                        previewElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                        });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }
                        if (title) {
                            title.setText(this.data.title);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="300" frameborder="0" src="'+editor.config.filebrowserImagePreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[IMAGE id=' + this.data.id + '; title=' + (this.data.title || '') + ';]');
                }
            });

            editor.widgets.add('FileSC', {
                button: editor.lang.shortcode.file.button_title,
                dialog: 'file',
                template:
                    '<code class="file">' +
                        '[FILE id=<span class="file__id"></span>;]' +
                        '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!file); span(!file__id); span(!preview); iframe;',
                requiredContent: 'code(file)',
                init: function () {
                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('file__id');
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="60" frameborder="0" src="'+editor.config.filebrowserFilePreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[FILE id=' + (this.data.id || '') + ';]');
                }
            });

            editor.widgets.add('AudioSC', {
                button: editor.lang.shortcode.audio.button_title,
                dialog: 'audio',
                template:
                    '<code class="audio">' +
                        '[AUDIO id=<span class="audio__id"></span>;]' +
                        '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!audio); span(!audio__id); span(!preview); iframe;',
                requiredContent: 'code(audio)',
                init: function () {
                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('audio__id');
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="110" frameborder="0" src="'+editor.config.filebrowserAudioPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[AUDIO id=' + (this.data.id || '') + ';]');
                }
            });

            editor.widgets.add('VideoSC', {
                button: editor.lang.shortcode.video.button_title,
                dialog: 'video',
                template:
                    '<code class="video">' +
                        '[VIDEO id=<span class="video__id"></span>; title=<span class="video__title"></span>]' +
                        '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!video); span(!video__title); span(!video__id); span(!preview); iframe;',
                requiredContent: 'code(video)',
                init: function () {
                    var title = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('video__title');
                    });

                    if (title) {
                        this.setData('title', title.getText());
                    }

                    var id = this.element.getAttribute('id');

                    if (id) {
                        this.setData('id', id);
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('video__id');
                    }),
                    title = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('video__title');
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (title) {
                            title.setText(this.data.title);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="430" frameborder="0" src="'+editor.config.filebrowserVideoPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[VIDEO id=' + (this.data.id || '') + '; title=' + (this.data.title || '') + ']');
                }
            });

            editor.widgets.add('GallerySC', {
                button: editor.lang.shortcode.gallery.button_title,
                dialog: 'gallery',
                template:
                    '<code class="gallery">' +
                        '[GALLERY id=<span class="gallery__id"></span>;]' +
                        '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!gallery); span(!gallery__id); span(!preview); iframe;',
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
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="380" frameborder="0" src="'+editor.config.filebrowserGalleryPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
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
                    '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!quiz); span(!quiz__id); span(!preview); iframe;',
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
                        }),
                        previewElement = this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                        });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="180" frameborder="0" src="'+editor.config.filebrowserQuizPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[QUIZ id=' + this.data.id + ';]');
                }
            });

            editor.widgets.add('ChampionshipSC', {
                button: editor.lang.shortcode.championship.button_title,
                dialog: 'championship',
                template:
                    '<code class="championship">' +
                    '[CHAMPIONSHIP id=<span class="championship__id"></span>; tour=<span class="championship__tour"></span>; stage=<span class="championship__stage"></span>]' +
                    '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!championship); span(!championship__id); span(!championship__tour); span(!championship__stage); span(!preview); iframe;',
                requiredContent: 'code(championship)',
                init: function () {
                    var id = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__id');
                    });
                    if (id) {
                        this.setData('id', id.getText());
                    }

                    var tour = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__tour');
                    });
                    if (tour) {
                        this.setData('tour', tour.getText());
                    }

                    var stage = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__stage');
                    });
                    if (stage) {
                        this.setData('stage', stage.getText());
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__id');
                    }),
                    tour = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__tour');
                    }),
                    stage = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('championship__stage');
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }
                        if (tour) {
                            tour.setText(this.data.tour);
                        }
                        if (stage) {
                            stage.setText(this.data.stage);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="180" frameborder="0" src="'+editor.config.filebrowserChampionshipPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[CHAMPIONSHIP id=' + this.data.id + '; tour=' + (this.data.tour || '') + '; stage=' + (this.data.stage || '') + ']');
                }
            });


            editor.widgets.add('ExpertSC', {
                button: editor.lang.shortcode.expert.button_title,
                dialog: 'expert',
                template:
                    '<code class="expert">' +
                        '[EXPERT id=<span class="expert__id"></span>; text=<span class="expert__text"></span>;]' +
                        '<span class="preview"></span>' +
                    '</code>',
                allowedContent: 'code(!expert); span(!expert__text); span(!expert__id); span(!preview); iframe;',
                requiredContent: 'code(expert)',
                init: function () {
                    var text = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('expert__text');
                    }),
                    id = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('expert__id');
                    });

                    if (text) {
                        this.setData('text', text.getText());
                    }

                    if (id) {
                        this.setData('id', id.getText());
                    }
                },
                data: function () {
                    var idElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('expert__id');
                    }),
                    textElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('expert__text');
                    }),
                    previewElement = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('preview');
                    });

                    if (this.data.id) {
                        this.element.setAttribute('id', this.data.id);

                        if (idElement) {
                            idElement.setText(this.data.id);
                        }

                        if (textElement) {
                            textElement.setText(this.data.text);
                        }

                        if (previewElement) {
                            previewElement.setHtml(
                                '<iframe scrolling="no" width="500" height="130" frameborder="0" src="'+editor.config.filebrowserExpertPreviewUrl + '?id='+this.data.id+'&langCode='+langCode+'"></iframe>'
                            );
                        }
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('[EXPERT id=' + (this.data.id || '') + '; text=' + (this.data.text || '') + ';]');
                }
            });

            editor.widgets.add('InstagramSC', {
                button: editor.lang.shortcode.instagram.button_title,
                dialog: 'instagram',
                template:
                    '<p class="instagram">' +
                    '<span class="code__instagram"></span>' +
                    '</p>',
                allowedContent: 'p(!instagram); span(!code__instagram);',
                requiredContent: 'p(instagram)',
                init: function () {
                    var code_instagram = this.element.getFirst(function (child) {
                        return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('code__instagram');
                    });

                    if (code_instagram) {
                        this.setData('code_instagram', code_instagram.getText());
                    }
                },
                data: function () {
                    if (this.data.code_instagram) {
                        this.element.getFirst(function (child) {
                            return child.type === CKEDITOR.NODE_ELEMENT && child.getName() === 'span' && child.hasClass('code__instagram');
                        }).setText(this.data.code_instagram);
                    }
                },
                downcast: function () {
                    return new CKEDITOR.htmlParser.text('<span class="code__instagram">' + (this.data.code_instagram || '') + '</span>');
                }
            });
        },
        afterInit: function (editor) {
            var langCode = editor.name.indexOf('_translations_uk_') === -1 ? 'ru' : 'uk',
                onlineRegexp = /\[ONLINE\ start=(.+?); finish=(.+?);\]/g,
                textRegexp = /\[TEXT\ id=(.+?);\]/g,
                readRegexp = /\[READ\ id=(.+?);\ title=(.*?);\]/g,
                imageRegexp = /\[IMAGE\ id=(.+?);\ title=(.*?);\]/g,
                galleryRegexp = /\[GALLERY\ id=(.+?);\]/g,
                quizRegexp = /\[QUIZ\ id=(.+?);\]/g,
                championshipRegexp = /\[CHAMPIONSHIP\ id=(.+?); tour=(.*?); stage=(.*?)\]/g,
                fileRegexp = /\[FILE\ id=(.+?);\]/g,
                audioRegexp = /\[AUDIO\ id=(.+?);\]/g,
                videoRegexp = /\[VIDEO\ id=(.+?);\ title=(.*?)\]/g,
                expertRegexp = /\[EXPERT\ id=(.+?);\ text=(.*?);\]/g,
                instagramRegexp = /\<span class='code__instagram'>code_instagram=(.+?);\<\/span>/g;

            editor.dataProcessor.dataFilter.addRules({
                text: function (text, node) {
                    var dtd = node.parent && CKEDITOR.dtd[node.parent.name];

                    if (dtd && !dtd.span) {
                        return;
                    }

                    return text.replace(textRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'text',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[TEXT id=<span class="text__id">' + id + '</span>;]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="600" height="130" frameborder="0" src="'+editor.config.filebrowserTextPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'TextSC');

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

                    return text.replace(readRegexp, function (match, id, title) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'read',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[READ id=<span class="read__id">' + id + '</span>; title=<span class="read__title">' + (title ? title : ' ') + '</span>;]' +
                                '<span class="preview">' +
                                '<iframe scrolling="no" width="500" height="110" frameborder="0" src="'+editor.config.filebrowserArticlePreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'ReadSC');

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

                    return text.replace(imageRegexp, function (match, id, title) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'image',
                                'id': id
                            });

                        if (match && id > 0) {
                            outerElement.setHtml(
                                '[IMAGE id=<span class="image__id">' + id + '</span>; title=<span class="image__title">' + (title ? title : ' ') + '</span>]' +
                                '<span class="preview">' +
                                '<iframe scrolling="no" width="500" height="300" frameborder="0" src="'+editor.config.filebrowserImagePreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'ImageSC');

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

                    return text.replace(onlineRegexp, function (match, start, finish) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'online',
                                'start': start,
                                'finish': finish
                            });

                        if (match && start && finish) {
                            outerElement.setHtml('[ONLINE start=<span class="online__start">' + start + '</span>; finish=<span class="online__finish">' + finish + '</span>;]');
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'OnlineSC');

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
                            outerElement.setHtml(
                                '[GALLERY id=<span class="gallery__id">' + id + '</span>;]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="500" height="380" frameborder="0" src="'+editor.config.filebrowserGalleryPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
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

                    return text.replace(quizRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'quiz',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[QUIZ id=<span class="quiz__id">' + id + '</span>;]' +
                                '<span class="preview">' +
                                '<iframe scrolling="no" width="500" height="180" frameborder="0" src="'+editor.config.filebrowserQuizPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'QuizSC');

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

                    return text.replace(championshipRegexp, function (match, id, tour, stage) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'championship',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[CHAMPIONSHIP id=<span class="championship__id">' + id + '</span>; tour=<span class="championship__tour">' + (tour ? tour : '') + '</span>; stage=<span class="championship__stage">' + (stage ? stage : '') + '</span>]' +
                                '<span class="preview">' +
                                '<iframe scrolling="no" width="500" height="180" frameborder="0" src="'+editor.config.filebrowserChampionshipPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'ChampionshipSC');

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

                    return text.replace(videoRegexp, function (match, id, title) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'video',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[VIDEO id=<span class="video__id">' + id + '</span>; title=<span class="video__title">' + (title ? title : ' ') + '</span>]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="500" height="430" frameborder="0" src="'+editor.config.filebrowserVideoPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'VideoSC');

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

                    return text.replace(fileRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'file',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[FILE id=<span class="file__id">' + id + '</span>;]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="500" height="60" frameborder="0" src="'+editor.config.filebrowserFilePreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'FileSC');

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

                    return text.replace(audioRegexp, function (match, id) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'audio',
                                'id': id
                            });

                        if (match && id) {
                            outerElement.setHtml(
                                '[AUDIO id=<span class="audio__id">' + id + '</span>;]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="500" height="110" frameborder="0" src="'+editor.config.filebrowserAudioPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'AudioSC');

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

                    return text.replace(expertRegexp, function (match, id, text) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'expert',
                                'id': id
                            });

                        if (match) {
                            outerElement.setHtml(
                                '[EXPERT id=<span class="expert__id">' + id + '</span>; text=<span class="expert__text">' + text + '</span>;]' +
                                '<span class="preview">' +
                                    '<iframe scrolling="no" width="500" height="130" frameborder="0" src="'+editor.config.filebrowserExpertPreviewUrl + '?id='+id+'&langCode='+langCode+'"></iframe>' +
                                '</span>'
                            );
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'ExpertSC');

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

                    return text.replace(instagramRegexp, function (match, code_instagram) {
                        var widgetWrapper,
                            outerElement = new CKEDITOR.htmlParser.element('code', {
                                'class': 'instagram'
                            });

                        if (match) {
                            outerElement.setHtml(code_instagram);
                        }

                        widgetWrapper = editor.widgets.wrapElement(outerElement, 'InstagramSC');

                        return widgetWrapper.getOuterHtml();
                    });
                }
            });
        }
    });
}());

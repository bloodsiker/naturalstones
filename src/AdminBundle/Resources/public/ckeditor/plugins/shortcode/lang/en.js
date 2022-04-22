/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'en', {
        text: {
            button_title: 'Вставить код шаблона',
            dialog_title: 'Шаблон',
            tab_basic_label: 'Основные параметры',
            id: 'ID шаблона',
            select_label: 'Выбрать',
            select_title: 'Выбрать шаблон',
            err_id_empty: 'ID шаблона не может быть пустым.'
        },
        gallery: {
            button_title: 'Insert Gallery shortcode',
            dialog_title: 'Gallery',
            tab_basic_label: 'Basic settings',
            id: 'Gallery ID',
            select_label: 'Select',
            select_title: 'Select gallery from repository',
            err_id_empty: 'Gallery ID cannot be empty.'
        },
        quiz: {
            button_title: 'Insert Quiz shortcode',
            dialog_title: 'Quiz',
            tab_basic_label: 'Basic settings',
            id: 'Quiz ID',
            select_label: 'Select',
            select_title: 'Select quiz from repository',
            err_id_empty: 'Quiz ID cannot be empty.'
        },
        championship: {
            button_title: 'Insert championship shortcode',
            dialog_title: 'Championship',
            tab_basic_label: 'Basic settings',
            id: 'Championship ID',
            select_label: 'Select',
            select_title: 'Select championship from repository',
            err_id_empty: 'Championship ID cannot be empty.'
        },
        video: {
            button_title: 'Embed Video',
            dialog_title: 'Video',
            tab_basic_label: 'Basic settings',
            id: 'Video ID',
            err_id_empty: 'Video ID cannot be empty.',
            select_label: 'Select',
            select_title: 'Select Video from repository'
        },
        file: {
            button_title: 'Insert File shortcode',
            dialog_title: 'File',
            tab_basic_label: 'Basic settings',
            id: 'File ID',
            select_label: 'Select',
            select_title: 'Select file from repository',
            err_id_empty: 'File ID cannot be empty.'
        },
        audio: {
            button_title: 'Insert Audio shortcode',
            dialog_title: 'Audio',
            tab_basic_label: 'Basic settings',
            id: 'Audio ID',
            select_label: 'Select',
            select_title: 'Select audio from repository',
            err_id_empty: 'Audio ID cannot be empty.'
        },
        image: {
            button_title: 'Insert Image shortcode',
            dialog_title: 'Image',
            tab_basic_label: 'Basic settings',
            id: 'Image ID',
            title: 'Description',
            blank: 'Open image in new window',
            select_label: 'Select',
            select_title: 'Select image from repository',
            err_id_empty: 'Image ID cannot be empty.'
        },
        expert: {
            button_title: 'Insert Expert Quote shortcode',
            dialog_title: 'Expert',
            id: 'Expert ID',
            text: 'Quote',
            err_id_empty: 'Expert ID cannot be empty.',
            err_text_empty: 'Expert quote cannot be empty.',
            expert_select_label: 'Select',
            expert_select_title: 'Select expert from repository'
        },
        instagram: {
            button_title: 'Insert code Instagram, Twitter',
            dialog_title: 'Instagram, Twitter',
            tab_basic_label: 'Basic settings',
            code_instagram: 'Insert code *blockquote*',
            err_text_empty: 'Insert code cannot be empty.',
        },
        read: {
            button_title: 'Insert Read/View also content',
            dialog_title: 'Block Read/View',
            tab_basic_label: 'Basic settings',
            id: 'Article ID',
            alternate_title: 'Alternate title',
            err_id_empty: 'Article ID cannot be empty.',
            select_label: 'Select',
            select_title: 'Select Article from repository',
            select_read: 'Read also',
            select_view: 'View also'
        }
    });
}());

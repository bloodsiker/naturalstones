/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'ru', {
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
            button_title: 'Вставить код галереи',
            dialog_title: 'Галерея',
            tab_basic_label: 'Основные параметры',
            id: 'ID галереи',
            select_label: 'Выбрать',
            select_title: 'Выбрать галерею',
            err_id_empty: 'ID галереи не может быть пустым.'
        },
        quiz: {
            button_title: 'Вставить код опроса',
            dialog_title: 'Опрос',
            tab_basic_label: 'Основные параметры',
            id: 'ID опроса',
            select_label: 'Выбрать',
            select_title: 'Выбрать опрос',
            err_id_empty: 'ID опроса не может быть пустым.'
        },
        championship: {
            button_title: 'Вставить код чемпионата',
            dialog_title: 'Чемпионат',
            tab_basic_label: 'Основные параметры',
            id: 'ID чемпионата',
            select_label: 'Выбрать',
            select_title: 'Выбрать чемпионат',
            err_id_empty: 'ID чемпионата не может быть пустым.',
            tour: 'Тур/группа',
            stage: 'Стадия'
        },
        video: {
            button_title: 'Вставить видео',
            dialog_title: 'Видео',
            tab_basic_label: 'Основные параметры',
            id: 'ID видео',
            title: 'Описание видео',
            err_id_empty: 'ID видео не может быть пустым.',
            select_label: 'Выбрать',
            select_title: 'Выбрать видео'
        },
        image: {
            button_title: 'Вставить код изображения',
            dialog_title: 'Изображение',
            tab_basic_label: 'Основные параметры',
            id: 'ID изображения',
            title: 'Описание фото',
            blank: 'Открывать изображение в новом окне',
            select_label: 'Выбрать',
            select_title: 'Выбрать изображение',
            err_id_empty: 'ID изображения не может быть пустым.'
        },
        file: {
            button_title: 'Вставить код файла',
            dialog_title: 'Файл',
            tab_basic_label: 'Основные параметры',
            id: 'ID файла',
            select_label: 'Выбрать',
            select_title: 'Выбрать файл',
            err_id_empty: 'ID файла не может быть пустым.'
        },
        audio: {
            button_title: 'Вставить код аудио',
            dialog_title: 'Аудио',
            tab_basic_label: 'Основные параметры',
            id: 'ID аудио',
            select_label: 'Выбрать',
            select_title: 'Выбрать аудио',
            err_id_empty: 'ID аудио не может быть пустым.'
        },
        expert: {
            button_title: 'Вставить цитату эксперта',
            dialog_title: 'Эксперт',
            id: 'ID эксперта',
            text: 'Цитата',
            err_id_empty: 'ID эксперта не может быть пустым.',
            err_text_empty: 'Цитата эксперта не может быть пустой.',
            expert_select_label: 'Выбрать',
            expert_select_title: 'Выбрать эксперта'
        },
        instagram: {
            button_title: 'Вставить код Instagram, Twitter',
            dialog_title: 'Instagram, Twitter',
            tab_basic_label: 'Основные параметры',
            code_instagram: 'Код вставки *blockquote*',
            err_text_empty: 'Код вставки не может быть пустым.',
        },
        read: {
            button_title: 'Вставить блок Читай также',
            dialog_title: 'Блок "Читай также"',
            tab_basic_label: 'Основные параметры',
            id: 'ID статьи',
            alternate_title: 'Альтернативный заголовок',
            err_id_empty: 'ID статьи не может быть пустым.',
            select_label: 'Выбрать',
            select_title: 'Выбрать статью',
            select_read: 'Читай также',
            select_view: 'Cмотри также'
        }
    });
}());

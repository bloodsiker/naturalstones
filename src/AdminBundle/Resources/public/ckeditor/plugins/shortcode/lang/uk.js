/*global CKEDITOR*/

(function () {
    'use strict';

    CKEDITOR.plugins.setLang('shortcode', 'uk', {
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
            button_title: 'Вставити код галереї',
            dialog_title: 'Галерея',
            tab_basic_label: 'Основні параметри',
            id: 'ID галереї',
            select_label: 'Обрати',
            select_title: 'Обрати галерею',
            err_id_empty: 'ID галереї не може бути порожнім.'
        },
        quiz: {
            button_title: 'Вставити код опитування',
            dialog_title: 'Опитування',
            tab_basic_label: 'Основні параметри',
            id: 'ID опитування',
            select_label: 'Обрати',
            select_title: 'Обрати опитування',
            err_id_empty: 'ID опитування не може бути порожнім.'
        },
        championship: {
            button_title: 'Вставити таблицю чемпіонату',
            dialog_title: 'Чемпіонат',
            tab_basic_label: 'Основні параметри',
            id: 'ID чемпіонату',
            select_label: 'Обрати',
            select_title: 'Обрати чемпіонат',
            err_id_empty: 'ID чемпіонату не може бути порожнім.',
            tour: 'Тур/група',
            stage: 'Cтадія'
        },
        video: {
            button_title: 'Вставити відео',
            dialog_title: 'Відео',
            tab_basic_label: 'Основні параметри',
            id: 'ID відео',
            title: 'Опис відео',
            err_id_empty: 'ID відео не може бути порожнім.',
            select_label: 'Обрати',
            select_title: 'Обрати відео'
        },
        image: {
            button_title: 'Вставити код зображення',
            dialog_title: 'Зображення',
            tab_basic_label: 'Основні параметри',
            id: 'ID зображення',
            title: 'Опис фото',
            blank: 'Відкривати зображення в новому вікні',
            select_label: 'Обрати',
            select_title: 'Обрати зображення',
            err_id_empty: 'ID зображення не може бути порожнім.'
        },
        file: {
            button_title: 'Вставити код файлу',
            dialog_title: 'Файл',
            tab_basic_label: 'Основні параметри',
            id: 'ID файлу',
            select_label: 'Обрати',
            select_title: 'Обрати файл',
            err_id_empty: 'ID файлу не може бути порожнім.'
        },
        audio: {
            button_title: 'Вставити код аудіо',
            dialog_title: 'Аудіо',
            tab_basic_label: 'Основні параметри',
            id: 'ID аудіо',
            select_label: 'Обрати',
            select_title: 'Обрати аудіо',
            err_id_empty: 'ID аудіо не може бути порожнім.'
        },
        expert: {
            button_title: 'Вставити цитату експерта',
            dialog_title: 'Експерт',
            id: 'ID',
            text: 'Цитата',
            err_id_empty: 'ID експерта не може бути порожнім.',
            err_text_empty: 'Цитата експерта не може бути порожньою.',
            expert_select_label: 'Обрати',
            expert_select_title: 'Обрати експерта'
        },
        instagram: {
            button_title: 'Вставити код Instagram, Twitter',
            dialog_title: 'Instagram, Twitter',
            tab_basic_label: 'Основні параметри',
            code_instagram: 'Код вставки *blockquote*',
            err_text_empty: 'Код вставки не може бути пустим.',
        },
        read: {
            button_title: 'Вставити блок Читай також',
            dialog_title: 'Блок "Читати також"',
            tab_basic_label: 'Основні параметри',
            id: 'ID статті',
            alternate_title: 'Альтернативний заголовок',
            err_id_empty: 'ID статті не може бути порожнім.',
            select_label: 'Вибрати',
            select_title: 'Вибрати статтю',
            select_read: 'Читай також',
            select_view: 'Дивись також'
        }
    });
}());

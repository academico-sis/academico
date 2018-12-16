<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backpack Crud Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used by the CRUD interface.
    | You are free to change them to anything
    | you want to customize your views to better match your application.
    |
    */

    // Forms
    'save_action_save_and_new'                => 'Сохранить и создать',
    'save_action_save_and_edit'               => 'Сохранить и продолжить редактирование',
    'save_action_save_and_back'               => 'Сохранить и выйти',
    'save_action_changed_notification'        => 'Действие после сохранения было изменено',

    // Create form
    'add'                                     => 'Добавить',
    'back_to_all'                             => 'Вернуться к списку',
    'cancel'                                  => 'Отменить',
    'add_a_new'                               => 'Добавить новый(ую)',

    // Edit form
    'edit'                                    => 'Редактировать',
    'save'                                    => 'Сохранить',

    // Revisions
    'revisions'                               => 'Версии',
    'no_revisions'                            => 'Версий не найдено',
    'created_this'                            => 'создал(а) это',
    'changed_the'                             => 'изменил(а)',
    'restore_this_value'                      => 'Восстановить это значение',
    'from'                                    => 'с',
    'to'                                      => 'по',
    'undo'                                    => 'Шаг назад',
    'revision_restored'                       => 'Версия успешно восстановлена',

    // CRUD table view
    'all'                                     => 'Все ',
    'in_the_database'                         => 'в базе данных',
    'list'                                    => 'Список',
    'actions'                                 => 'Действия',
    'preview'                                 => 'Предпросмотр',
    'delete'                                  => 'Удалить',
    'admin'                                   => 'Админ',
    'details_row'                             => 'Это строка сведений. Измените, пожалуйста',
    'details_row_loading_error'               => 'Произошла ошибка при загрузке сведений. Повторите операцию.',

    // Confirmation messages and bubbles
    'delete_confirm'                          => 'Вы уверены. что хотите удалить эту запись?',
    'delete_confirmation_title'               => 'Запись удалена',
    'delete_confirmation_message'             => 'Запись была успешно удалена',
    'delete_confirmation_not_title'           => 'НЕ удалено',
    'delete_confirmation_not_message'         => 'Произошла ошибка. Запись не была удалена',
    'delete_confirmation_not_deleted_title'   => 'Не удалено',
    'delete_confirmation_not_deleted_message' => 'Ничего не произошло. Ваша запись сохранена/',

    // DataTables translation
    'emptyTable'                              => 'В таблице нет доступных данных',
    'info'                                    => 'Показано _START_ до _END_ из _TOTAL_ совпадений',
    'infoEmpty'                               => 'Показано 0 до 0 из 0 совпадений',
    'infoFiltered'                            => '(отфильтровано из _MAX_ совпадений)',
    'infoPostFix'                             => '',
    'thousands'                               => ',',
    'lengthMenu'                              => '_MENU_ записей на странице',
    'loadingRecords'                          => 'Загрузка...',
    'processing'                              => 'Обработка...',
    'search'                                  => 'Поиск: ',
    'zeroRecords'                             => 'Совпадений не найдено',
    'paginate'                                => [
        'first'    => 'Первая',
        'last'     => 'Последняя',
        'next'     => 'Следующая',
        'previous' => 'Предыдущая',
    ],
    'aria'                                    => [
        'sortAscending'  => ': нажмите для сортировки по возрастанию',
        'sortDescending' => ': нажмите для сортировки по убыванию',
    ],

    // global crud - errors
    'unauthorized_access'                     => 'У Вас нет необходимых прав для просмотра этой страницы.',
    'please_fix'                              => 'Пожалуйста, исправьте следующие ошибки:',

    // global crud - success / error notification bubbles
    'insert_success'                          => 'Запись была успешно добавлена.',
    'update_success'                          => 'Запись была успешно изменена.',

    // CRUD reorder view
    'reorder'                                 => 'Изменить порядок',
    'reorder_text'                            => 'Используйте drag&drop для изменения порядка.',
    'reorder_success_title'                   => 'Готово',
    'reorder_success_message'                 => 'Порядок был сохранен.',
    'reorder_error_title'                     => 'Ошибка',
    'reorder_error_message'                   => 'Порядок не был сохранен.',

    // CRUD yes/no
    'yes'                                     => 'Да',
    'no'                                      => 'Нет',

    // CRUD filters navbar view
    'filters'                                 => 'Фильтры',
    'toggle_filters'                          => 'Переключить фильтры',
    'remove_filters'                          => 'Очистить фильтры',

    // Fields
    'browse_uploads'                          => 'Загрузить файлы',
    'clear'                                   => 'Очистить',
    'page_link'                               => 'Ссылка на страницу',
    'page_link_placeholder'                   => 'http://example.com/your-desired-page',
    'internal_link'                           => 'Внутренняя ссылка',
    'internal_link_placeholder'               => 'Внутренний путь. Например: \'admin/page\' (без кавычек) для \':url\'',
    'external_link'                           => 'Внешняя ссылка',
    'choose_file'                             => 'Выбрать файл',

    //Table field
    'table_cant_add'                          => 'Не удалось добавить новую :entity',
    'table_max_reached'                       => 'Максимальное количество из :max достигнуто',

    // File manager
    'file_manager'                            => 'Файловый менеджер',
];

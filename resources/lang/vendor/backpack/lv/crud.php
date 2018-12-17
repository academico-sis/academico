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
    'save_action_save_and_new' => 'Saglabāt un jauns ieraksts',
    'save_action_save_and_edit' => 'Saglabāt un rediģēt šo ierakstu',
    'save_action_save_and_back' => 'Saglabāt un atpakaļ',
    'save_action_changed_notification' => 'Uzvedība pēc noklusējuma pēc izmaiņu saglabāšanas.',

    // Create form
    'add' => 'Jauns',
    'back_to_all' => 'Atpakaļ pie Visiem ',
    'cancel' => 'Atcelt',
    'add_a_new' => 'Jauns ',

    // Edit form
    'edit' => 'Rediģēt',
    'save' => 'Saglabāt',

    // Revisions
    'revisions' => 'Revīzijas',
    'no_revisions' => 'Revīzijas nav atrastas',
    'created_this' => 'izveidoja',
    'changed_the' => 'izmainīja',
    'restore_this_value' => 'Atjaunot vērtību',
    'from' => 'no',
    'to' => 'līdz',
    'undo' => 'Atgriezt atpakaļ',
    'revision_restored' => 'Revīzija veiksmīgi atjaunota',
    'guest_user' => 'Lietotājs: viesis',

    // Translatable models
    'edit_translations' => 'LABOT TULKOJUMUS',
    'language' => 'Valoda',

    // CRUD table view
    'all' => 'Visi ',
    'in_the_database' => 'datubāzē',
    'list' => 'Saraksts',
    'actions' => 'Darbības',
    'preview' => 'Priekšapskats',
    'delete' => 'Dzēst',
    'admin' => 'Admin',
    'details_row' => 'Šī ir detaļu rinda.',
    'details_row_loading_error' => 'Notika kļuda lādējot detaļas. Lūdzu atkārtojiet.',

    // Confirmation messages and bubbles
    'delete_confirm' => 'Vai esat pārliecināti, ka gribat izdzēst šo ierakstu?',
    'delete_confirmation_title' => 'Ieraksts Izdzēsts',
    'delete_confirmation_message' => 'Elements tika izdzēsts veiksmīgi.',
    'delete_confirmation_not_title' => 'NAV izdzēsts',
    'delete_confirmation_not_message' => 'Notika kļūda. Ieraksts var būt neizdzēsts.',
    'delete_confirmation_not_deleted_title' => 'Nav izdzēsts',
    'delete_confirmation_not_deleted_message' => 'Nekas nenotika. Ieraksts ir drošībā.',

    'ajax_error_title' => 'Kļūda',
    'ajax_error_text' => 'Kļūda ielādējot saturu. Lūdzu pārlādē lapu.',

    // DataTables translation
    'emptyTable' => 'Dati nav pieejami',
    'info' => 'Rādot no _START_ līdz _END_ no kopumā _TOTAL_ ierakstiem',
    'infoEmpty' => 'Rādot 0 no 0 no kopumā 0 ierakstiem',
    'infoFiltered' => '(filtrēts no _MAX_ kopējiem ierakstiem)',
    'infoPostFix' => '',
    'thousands' => ',',
    'lengthMenu' => '_MENU_ ieraksti uz lapu',
    'loadingRecords' => 'Ielādē...',
    'processing' => 'Apstrādā...',
    'search' => 'Meklēšana: ',
    'zeroRecords' => 'Peimēroti ieraksti nav atrasti',
    'paginate' => [
        'first' => 'Pirmā',
        'last' => 'Pēdējā',
        'next' => 'Nākošā',
        'previous' => 'Iepriekšējā',
    ],
    'aria' => [
        'sortAscending' => ': aktivizējiet lai šķirot augoši',
        'sortDescending' => ': aktivizējiet lai šķirot dilstoši',
    ],
    'export' => [
        'copy' => 'Kopēt',
        'excel' => 'Excel',
        'csv' => 'CSV',
        'pdf' => 'PDF',
        'print' => 'Drukāt',
        'column_visibility' => 'Kolonnu redzamība',
    ],

    // global crud - errors
    'unauthorized_access' => 'Neautorizēta pieeja - jums nav nepieciešamo tiesību lai apskatītu šo lapu.',
    'please_fix' => 'Lūdzu izlabojiet sekojošas kļūdas:',

    // global crud - success / error notification bubbles
    'insert_success' => 'Ieraksts tika veiksmīgi pievienots.',
    'update_success' => 'Ieraksts tika veiksmīgi modificēts.',

    // CRUD reorder view
    'reorder' => 'Pārkārtot',
    'reorder_text' => 'Izmantojiet drag&drop lai pārkārtotu.',
    'reorder_success_title' => 'Gatavs',
    'reorder_success_message' => 'Secība tika saglabāta.',
    'reorder_error_title' => 'Kļuda',
    'reorder_error_message' => 'Secība netika saglabāta.',

    // CRUD yes/no
    'yes' => 'Jā',
    'no' => 'Nē',

    // CRUD filters navbar view
    'filters' => 'Filtri',
    'toggle_filters' => 'Pārslēgt filtrus',
    'remove_filters' => 'Noņemt filtrus',

    // Fields
    'browse_uploads' => 'Pārlūkot failus',
    'select_all' => 'Atlasīt visu',
    'select_files' => 'Izvēlies failus',
    'select_file' => 'Izvēlies failu',
    'clear' => 'Notīrīt',
    'page_link' => 'Lapas saite',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => 'Iekšējā saite',
    'internal_link_placeholder' => 'Iekšējās saites vārds. Piemēram: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'Ārējā saite',
    'choose_file' => 'Izvēlaties failu',

    //Table field
    'table_cant_add' => 'Nevar pievienot jaunu :entity',
    'table_max_reached' => 'Sasniegts maksimālais skaits no :max',

    // File manager
    'file_manager' => 'Failu Pārlūks',
];

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
    'save_action_save_and_new' => 'Salva ed aggiungi un nuovo elemento',
    'save_action_save_and_edit' => 'Salva e modifica questo elemento',
    'save_action_save_and_back' => 'Salva e torna indietro',
    'save_action_changed_notification' => 'Azione predefinita cambiata',

    // Create form
    'add'                 => 'Aggiungi',
    'back_to_all'         => 'Torna a tutti i ',
    'cancel'              => 'Annulla',
    'add_a_new'           => 'Aggiungi un nuovo ',

    // Edit form
    'edit'                 => 'Modifica',
    'save'                 => 'Salva',

    // Revisions
    'revisions'            => 'Revisioni',
    'no_revisions'         => 'Nessuna revisione trovata',
    'created_this'         => 'ha creato questo',
    'changed_the'          => 'ha cambiato il',
    'restore_this_value'   => 'ripristinare questo valore',
    'from'                 => 'da',
    'to'                   => 'a',
    'undo'                 => 'Annulla',
    'revision_restored'    => 'Revisione ripristinata con successo',
    'guest_user'           => 'Un Ospite',

    // Translatable models
    'edit_translations' => 'MODIFICA TRADUZIONI',
    'language'          => 'Lingua',

    // CRUD table view
    'all'                       => 'Tutti i ',
    'in_the_database'           => 'nel database',
    'list'                      => 'Lista',
    'actions'                   => 'Azioni',
    'preview'                   => 'Anteprima',
    'delete'                    => 'Elimina',
    'admin'                     => 'Amministrazione',
    'details_row'               => 'Questa è la riga dei dettagli. Modificala a tuo piacimento.',
    'details_row_loading_error' => "C'è stato un errore caricando i dettagli. Riprova.",

    // Confirmation messages and bubbles
    'delete_confirm'                              => 'Sei sicuro di eliminare questo elemento?',
    'delete_confirmation_title'                   => 'Elemento eliminato',
    'delete_confirmation_message'                 => "L'elemento è stato eliminato con successo.",
    'delete_confirmation_not_title'               => 'NON eliminato',
    'delete_confirmation_not_message'             => "C'è stato un errore. L'elemento potrebbe non essere stato eliminato.",
    'delete_confirmation_not_deleted_title'       => 'Non eliminato',
    'delete_confirmation_not_deleted_message'     => "Non è successo niente. L'elemento è al sicuro.",

    'ajax_error_title' => 'Errore',
    'ajax_error_text'  => 'Errore durante il caricamento della pagina. Per favore ricarica la pagina.',

    // DataTables translation
    'emptyTable'     => 'Nessun record da visualizzare',
    'info'           => 'Visualizzando da _START_ a _END_ record di _TOTAL_',
    'infoEmpty'      => 'Visualizzando da 0 a 0 record di 0',
    'infoFiltered'   => '(filtrati da _MAX_ record totali)',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => '_MENU_ record per pagina',
    'loadingRecords' => 'Caricamento...',
    'processing'     => 'Elaborazione...',
    'search'         => 'Cerca: ',
    'zeroRecords'    => 'Nessun record corrispondente',
    'paginate'       => [
        'first'    => 'Primo',
        'last'     => 'Ultimo',
        'next'     => 'Prossimo',
        'previous' => 'Precedente',
    ],
    'aria' => [
        'sortAscending'  => ': attiva per ordinare la colonna ascendentemente',
        'sortDescending' => ': attiva per ordinare la colonna discendentemente',
    ],
    'export' => [
        'copy'              => 'Copia',
        'excel'             => 'Excel',
        'csv'               => 'CSV',
        'pdf'               => 'PDF',
        'print'             => 'Stampa',
        'column_visibility' => 'Visibilità colonne',
    ],

    // global crud - errors
    'unauthorized_access' => 'Accesso non autorizzato - non hai i permessi necessari per vedere questa pagina.',
    'please_fix' => 'Per favore correggi i seguenti errori:',

    // global crud - success / error notification bubbles
    'insert_success' => "L'elemento è stato aggiunto correttamente.",
    'update_success' => "L'elemento è stato aggiornato correttamente.",

    // CRUD reorder view
    'reorder'                      => 'Riordina',
    'reorder_text'                 => 'Seleziona e trascina per riordinare.',
    'reorder_success_title'        => 'Fatto',
    'reorder_success_message'      => 'Il tuo ordinamento è stato salvato.',
    'reorder_error_title'          => 'Errore',
    'reorder_error_message'        => 'Il tuo ordinamento non è stato salvato.',

    // CRUD yes/no
    'yes' => 'Sì',
    'no' => 'No',

    // CRUD filters navbar view
    'filters' => 'Filtri',
    'toggle_filters' => 'Attiva/disattiva filtri',
    'remove_filters' => 'Rimuovi filtri',

    // Fields
    'browse_uploads' => 'Sfoglia file caricati',
    'select_all' => 'Seleziona tutti',
    'select_files' => 'Seleziona i files',
    'select_file' => 'Seleziona un file',
    'clear' => 'Pulisci',
    'page_link' => 'Link Pagina',
    'page_link_placeholder' => 'http://esempio.com/pagina-desiderata',
    'internal_link' => 'Link Interno',
    'internal_link_placeholder' => 'Slug interno. Es: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => 'Link Esterno',
    'choose_file' => 'Scegli file',

    //Table field
    'table_cant_add' => 'Impossibile aggiungere una nuova :entity',
    'table_max_reached' => 'Numero massimo di :max raggiunto',

    // File manager
    'file_manager' => 'File Manager',
];

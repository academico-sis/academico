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
    'save_action_save_and_new' => 'Opslaan en nieuw item toevoegen',
    'save_action_save_and_edit' => 'Opslaan en item bewerken',
    'save_action_save_and_back' => 'Opslaan en terug',
    'save_action_changed_notification' => 'Standaard gedrag na opslaan is aangepast.',

    // Create form
    'add'                 => 'Toevoegen',
    'back_to_all'         => 'Terug naar alle items ',
    'cancel'              => 'Annuleren',
    'add_a_new'           => 'Nieuwe toevoegen ',

    // Edit form
    'edit'                 => 'Bewerken',
    'save'                 => 'Opslaan',

    // Revisions
    'revisions'            => 'Revisies',
    'no_revisions'         => 'Geen revisies gevonden',
    'created_this'          => 'heeft dit aangemaakt',
    'changed_the'          => 'heeft aangepast',
    'restore_this_value'   => 'Herstel deze waarde',
    'from'                 => 'van',
    'to'                   => 'naar',
    'undo'                 => 'Ongedaan maken',
    'revision_restored'    => 'Revisie succesvol hersteld',

    // Translatable models
    'edit_translations' => 'VERTALING WIJZIGEN',
    'language'          => 'Taal',

    // CRUD table view
    'all'                       => 'Alles ',
    'in_the_database'           => 'in de database',
    'list'                      => 'Lijst',
    'actions'                   => 'Acties',
    'preview'                   => 'Voorbeeld',
    'delete'                    => 'Verwijderen ',
    'admin'                     => 'Admin',
    'details_row'               => 'Dit is de detail rij. Bewerk als gewenst.',
    'details_row_loading_error' => 'Er is een fout opgetreden bij het laden van de details. Probeer het opnieuw.',

        // Confirmation messages and bubbles
        'delete_confirm'                              => 'Weet je zeker dat je dit item wilt verwijderen?',
        'delete_confirmation_title'                   => 'Item verwijderd',
        'delete_confirmation_message'                 => 'Het item is succesvol verwijderd.',
        'delete_confirmation_not_title'               => 'NIET verwijderd',
        'delete_confirmation_not_message'             => 'Er is een fout opgetreden. Het item is misschien niet verwijderd.',
        'delete_confirmation_not_deleted_title'       => 'Niet verwijderd',
        'delete_confirmation_not_deleted_message'     => 'Er is niks gebeurd. Je item is veilig.',

        // DataTables translation
        'emptyTable'     => 'Geen data beschikbaar in de tabel',
        'info'           => 'Toon _START_ tot _END_ van _TOTAL_ items',
        'infoEmpty'      => 'Toon 0 tot 0 van 0 items',
        'infoFiltered'   => '(gefilterd van _MAX_ totale items)',
        'infoPostFix'    => '',
        'thousands'      => ',',
        'lengthMenu'     => '_MENU_ items per pagina',
        'loadingRecords' => 'Laden...',
        'processing'     => 'Verwerken...',
        'search'         => 'Zoeken: ',
        'zeroRecords'    => 'Geen overeenkomend item gevonden',
        'paginate'       => [
            'first'    => 'Eerste',
            'last'     => 'Laatste',
            'next'     => 'Volgende',
            'previous' => 'Vorige',
        ],
        'aria' => [
            'sortAscending'  => ': activeer om kolom oplopend te sorteren',
            'sortDescending' => ': activeer om kolom aflopend te sorteren',
        ],

    // global crud - errors
        'unauthorized_access' => 'Geen toegang - je hebt niet de benodigde rechten om deze pagina te bekijken.',
        'please_fix' => 'Los de volgende fouten op:',

    // global crud - success / error notification bubbles
        'insert_success' => 'Het item is succesvol toegevoegd.',
        'update_success' => 'Het item is succesvol bewerkt.',

    // CRUD reorder view
        'reorder'                      => 'Rangschik',
        'reorder_text'                 => 'Gebruik drag&drop om te rangschikken.',
        'reorder_success_title'        => 'Klaar',
        'reorder_success_message'      => 'De rangschikking is opgeslagen.',
        'reorder_error_title'          => 'Fout',
        'reorder_error_message'        => 'De rangschikking is niet opgeslagen.',

    // CRUD yes/no
        'yes' => 'Ja',
        'no' => 'Nee',

    // Fields
        'browse_uploads' => 'Blader uploads',
        'select_files' => 'Selecteer bestanden',
        'select_file' => 'Selecteer bestand',
        'clear' => 'Wissen',
        'page_link' => 'Pagina link',
        'page_link_placeholder' => 'http://example.com/your-desired-page',
        'internal_link' => 'Interne link',
        'internal_link_placeholder' => 'Interne slug. B.v.: \'admin/page\' (geen quotes) voor \':url\'',
        'external_link' => 'Externe link',
        'choose_file' => 'Kies bestand',

    //Table field
        'table_cant_add' => 'Kan nieuwe :entity niet toevoegen',
        'table_max_reached' => 'Maximale grootte van :max bereikt',

    // File manager
    'file_manager' => 'Bestandsbeheer',
];

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
    'save_action_save_and_new' => 'Guardar e adicionar item',
    'save_action_save_and_edit' => 'Guardar e editar este item',
    'save_action_save_and_back' => 'Guardar e voltar',
    'save_action_changed_notification' => 'Comportamento predefinido depois de gravar.',

    // Create form
    'add'                 => 'Adicionar',
    'back_to_all'         => 'Voltar a todos ',
    'cancel'              => 'Cancelar',
    'add_a_new'           => 'Adicionar novo ',

    // Edit form
    'edit'                 => 'Editar',
    'save'                 => 'Gravar',

    // Revisions
    'revisions'            => 'Revisões',
    'no_revisions'         => 'Não foram encontradas revisões',
    'created_this'         => 'criou isto',
    'changed_the'          => 'alterou isto',
    'restore_this_value'   => 'Restaurou este valor',
    'from'                 => 'de',
    'to'                   => 'para',
    'undo'                 => 'Desfazer',
    'revision_restored'    => 'Revisão restaurada com sucesso',
    'guest_user'           => 'Convidado',

    // Translatable models
    'edit_translations' => 'EDITAR TRADUÇÕES',
    'language'          => 'Idioma',

    // CRUD table view
    'all'                       => 'Todos ',
    'in_the_database'           => 'na base de dados',
    'list'                      => 'Lista',
    'actions'                   => 'Ações',
    'preview'                   => 'Visualizar',
    'delete'                    => 'Apagar',
    'admin'                     => 'Administrar',
    'details_row'               => 'Isto é a linha de detalhes. Modifique conforme quiser.',
    'details_row_loading_error' => 'Houve um erro ao carregar os detalhes. Por favor tente novamente.',

    // Confirmation messages and bubbles
    'delete_confirm'                              => 'Tem a certeza que quer apagar este item?',
    'delete_confirmation_title'                   => 'Item apagado',
    'delete_confirmation_message'                 => 'O item foi apagado com sucesso.',
    'delete_confirmation_not_title'               => 'NÃO apagado',
    'delete_confirmation_not_message'             => 'Houve um erro. O item pode não ter sido apagado.',
    'delete_confirmation_not_deleted_title'       => 'Não apagado',
    'delete_confirmation_not_deleted_message'     => 'Está tudo bem!. O item não foi apagado.',

     // DataTables translation
    'emptyTable'     => 'Sem dados disponíveis na tabela',
    'info'           => 'A mostrar _START_ a _END_ de _TOTAL_ registos',
    'infoEmpty'      => 'A mostrar 0 a 0 de 0 registos',
    'infoFiltered'   => '(filtrado de _MAX_ registos no total)',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => '_MENU_ registos por página',
    'loadingRecords' => 'A carregar...',
    'processing'     => 'A processar...',
    'search'         => 'Procurar: ',
    'zeroRecords'    => 'Nenhum registo encontrado',
    'paginate'       => [
        'first'    => 'Primeiro',
        'last'     => 'Último',
        'next'     => 'Seguinte',
        'previous' => 'Anterior',
    ],
    'aria' => [
        'sortAscending'  => ': activar para colocar por ordem ascendente',
        'sortDescending' => ': activar para colocar por ordem descendente',
    ],

    // global crud - errors
    'unauthorized_access' => 'Acesso não autorizado - não tem as permissões necessárias para ver esta página.',
    'please_fix' => 'Por favor corrija os seguintes erros:',

    // global crud - success / error notification bubbles
    'insert_success' => 'O item foi adicionado com sucesso.',
    'update_success' => 'O item foi modificado com sucesso.',

    // CRUD reorder view
    'reorder'                      => 'Reordenar',
    'reorder_text'                 => 'Use \'arrastar e soltar\' para ordenar.',
    'reorder_success_title'        => 'Feito',
    'reorder_success_message'      => 'A ordenação foi gravada.',
    'reorder_error_title'          => 'Erro',
    'reorder_error_message'        => 'A ordenação não foi gravada.',

    // CRUD yes/no
    'yes' => 'Sim',
    'no' => 'Não',

    // CRUD filters navbar view
    'filters' => 'Filtros',
    'toggle_filters' => 'Alternar filtros',
    'remove_filters' => 'Remover filtros',

    // Fields
    'browse_uploads' => 'Procurar uploads',
    'clear' => 'Limpar',
    'page_link' => 'Page link',
    'page_link_placeholder' => 'http://example.com/a-sua-pagina',
    'internal_link' => 'Link interno',
    'internal_link_placeholder' => 'Slug interno. Ex: \'admin/page\' (sem aspas) para \':url\'',
    'external_link' => 'Link externo',
    'choose_file' => 'Escolher ficheiro',

    //Table field
    'table_cant_add' => 'Não é possível adicionar :entity',
    'table_max_reached' => 'O número máximo de :max foi atingido',

    // File manager
    'file_manager' => 'Gestor de ficheiros',
];

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
    'save_action_save_and_new' => 'Salvar e criar novo item',
    'save_action_save_and_edit' => 'Salvar e editar item',
    'save_action_save_and_back' => 'Salvar e voltar',
    'save_action_changed_notification' => 'Comportamento padrão após salvar foi alterado.',

    // Create form
    'add'                 => 'Adicionar',
    'back_to_all'         => 'Voltar para todos ',
    'cancel'              => 'Cancelar',
    'add_a_new'           => 'Adicionar ',

    // Edit form
    'edit'                 => 'Editar',
    'save'                 => 'Salvar',

    // Revisions
    'revisions'            => 'Revisões',
    'no_revisions'         => 'Nenhuma revisão encontrada',
    'created_this'         => 'criou este',
    'changed_the'          => 'alterou o',
    'restore_this_value'   => 'Restaurar esse valor',
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
    'in_the_database'           => 'no banco de dados',
    'list'                      => 'Lista',
    'actions'                   => 'Ações',
    'preview'                   => 'Visualizar',
    'delete'                    => 'Excluir',
    'admin'                     => 'Admin',
    'details_row'               => 'Esta são os detalhes do registro. Faça as modificações necessárias.',
    'details_row_loading_error' => 'Ocorreu um erro durante o carregamento dos detalhes. Por favor, tente novamente.',

        // Confirmation messages and bubbles
        'delete_confirm'                              => 'Tem certeza que deseja excluir este item?',
        'delete_confirmation_title'                   => 'Item excluído',
        'delete_confirmation_message'                 => 'Item excluído com sucesso.',
        'delete_confirmation_not_title'               => 'Item não excluído',
        'delete_confirmation_not_message'             => 'Ocorreu um erro. O item pode não ter sido excluído.',
        'delete_confirmation_not_deleted_title'       => 'Item não excluído',
        'delete_confirmation_not_deleted_message'     => 'Nada aconteceu. Seu item está seguro.',

        'ajax_error_title' => 'Erro',
        'ajax_error_text'  => 'Erro ao carregar. Por favor, atualize a página.',

        // DataTables translation
        'emptyTable'     => 'Nenhum dado cadastrado na tabela',
        'info'           => 'Exibindo _START_ a _END_ de _TOTAL_ registros',
        'infoEmpty'      => 'Exibindo 0 a 0 de 0 registros',
        'infoFiltered'   => '(filtrados de _MAX_ registros)',
        'infoPostFix'    => '',
        'thousands'      => ',',
        'lengthMenu'     => '_MENU_ registros por página',
        'loadingRecords' => 'Carregando...',
        'processing'     => 'Processando...',
        'search'         => 'Pesquisar: ',
        'zeroRecords'    => 'Nenhum registro encontrado',
        'paginate'       => [
            'first'    => 'Primeira',
            'last'     => 'Última',
            'next'     => 'Próxima',
            'previous' => 'Anterior',
        ],
        'aria' => [
            'sortAscending'  => ': clique para ordenar de forma ascendente',
            'sortDescending' => ': clique para ordenar de forma descendente',
        ],
        'export' => [
            'copy'              => 'Copiar',
            'excel'             => 'Excel',
            'csv'               => 'CSV',
            'pdf'               => 'PDF',
            'print'             => 'Imprimir',
            'column_visibility' => 'Visibilidade da coluna',
        ],

    // global crud - errors
        'unauthorized_access' => 'Acesso negado - você não possui a permissão necessária para acessar esta página.',
        'please_fix' => 'Por favor, corrija os seguintes erros:',

    // global crud - success / error notification bubbles
        'insert_success' => 'Item cadastrado com sucesso.',
        'update_success' => 'Item atualizado com sucesso.',

    // CRUD reorder view
        'reorder'                      => 'Reordenar',
        'reorder_text'                 => 'Use arrastar-e-soltar para reordenar.',
        'reorder_success_title'        => 'Pronto',
        'reorder_success_message'      => 'Sua ordenação foi salva.',
        'reorder_error_title'          => 'Erro',
        'reorder_error_message'        => 'Sua ordenação não foi salva.',

    // CRUD yes/no
        'yes' => 'Sim',
        'no' => 'Não',

    // CRUD filters navbar view
        'filters' => 'Filtros',
        'toggle_filters' => 'Alternar filtros',
        'remove_filters' => 'Remover filtros',

    // Fields
        'browse_uploads' => 'Pesquisar uploads',
        'clear' => 'Limpar',
        'page_link' => 'URL da Página',
        'page_link_placeholder' => 'http://exemplo.com',
        'internal_link' => 'Link Interno',
        'internal_link_placeholder' => 'Endereço interno. Ex: \'admin/pagina\' (sem aspas) para \':url\'',
        'external_link' => 'Link Externo',
        'choose_file' => 'Escolher arquivo',
        'select_all' => 'Selecionar todos',
        'select_files' => 'Selecionar todos os arquivos',
        'select_file' => 'Selecionar arquivo',

    //Table field
        'table_cant_add' => 'Não foi possível adicionar um(a) novo(a) :entity',
        'table_max_reached' => 'Limite de :max alcançado',

    // File manager
    'file_manager' => 'Gerenciador de Arquivos',
];

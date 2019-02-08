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
    'save_action_save_and_new' => 'Guardar y crear nuevo',
    'save_action_save_and_edit' => 'Guardar y continuar editando',
    'save_action_save_and_back' => 'Guardar y regresar',
    'save_action_changed_notification' => 'La acción por defecto del botón guardar ha sido modificada.',

    // Create form
    'add'                 => 'Añadir',
    'back_to_all'         => 'Volver al listado de',
    'cancel'              => 'Cancelar',
    'add_a_new'           => 'Añadir ',

    // Edit form
    'edit'                 => 'Editar',
    'save'                 => 'Guardar',

    // Revisions
    'revisions'            => 'Las revisiones',
    'no_revisions'         => 'No hay revisiones encontradas',
    'created_this'         => 'creado este',
    'changed_the'          => 'cambiado el',
    'restore_this_value'   => 'restaurar este valor',
    'from'                 => 'de',
    'to'                   => 'a',
    'undo'                 => 'Deshacer',
    'revision_restored'    => 'Revisión restaurada correctamente',

    // Translatable models
    'edit_translations' => 'EDITAR TRADUCCIONES',
    'language'          => 'Idioma',

    // CRUD table view
    'all'                       => 'Todos los registros de ',
    'in_the_database'           => 'en la base de datos',
    'list'                      => 'Listar',
    'actions'                   => 'Acciones',
    'preview'                   => 'Vista previa',
    'delete'                    => 'Eliminar',
    'admin'                     => 'Admin',
    'details_row'               => 'Esta es la fila de detalles. Modificar a su gusto.',
    'details_row_loading_error' => 'Se ha producido un error al cargar los datos. Por favor, intente de nuevo.',

    // Confirmation messages and bubbles
    'delete_confirm'                              => '¿Está seguro que desea eliminar este elemento?',
    'delete_confirmation_title'                   => 'Elemento eliminado',
    'delete_confirmation_message'                 => 'El elemento ha sido eliminado de manera correcta.',
    'delete_confirmation_not_title'               => 'No se pudo eliminar',
    'delete_confirmation_not_message'             => 'Ha ocurrido un error. Puede que el elemento no haya sido eliminado.',
    'delete_confirmation_not_deleted_title'       => 'No se pudo eliminar',
    'delete_confirmation_not_deleted_message'     => 'No ha ocurrido nada. Su elemento está seguro.',

    // DataTables translation
    'emptyTable'     => 'No hay datos disponibles en la tabla',
    'info'           => 'Mostrando registros _START_ a _END_ de un total de _TOTAL_ registros',
    'infoEmpty'      => 'Mostrando 0 registros',
    'infoFiltered'   => '(filtrando de _MAX_ registros totales)',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => '_MENU_ elementos por página',
    'loadingRecords' => 'Cargando...',
    'processing'     => 'Procesando...',
    'search'         => 'Buscar: ',
    'zeroRecords'    => 'No se encontraron elementos',
    'paginate'       => [
        'first'    => 'Primero',
        'last'     => 'Último',
        'next'     => 'Siguiente',
        'previous' => 'Anterior',
    ],
    'aria' => [
        'sortAscending'  => ': activar para ordenar ascendentemente',
        'sortDescending' => ': activar para ordenar descendentemente',
    ],

    'export' => [
        'copy'              => 'Copiar',
        'excel'             => 'Excel',
        'csv'               => 'CSV',
        'pdf'               => 'PDF',
        'print'             => 'Imprimir',
        'column_visibility' => 'Visibilidad de columnas',
    ],

    // global crud - errors
    'unauthorized_access' => 'Acceso denegado - usted no tiene los permisos necesarios para ver esta página.',
    'please_fix' => 'Por favor corrija los siguientes errores:',

    // global crud - success / error notification bubbles
    'insert_success' => 'El elemento ha sido añadido de manera correcta.',
    'update_success' => 'El elemento ha sido modificado de manera correcta.',

    // CRUD reorder view
    'reorder'                      => 'Reordenar',
    'reorder_text'                 => 'Arrastrar y soltar para reordenar.',
    'reorder_success_title'        => 'Hecho',
    'reorder_success_message'      => 'El orden ha sido guardado.',
    'reorder_error_title'          => 'Error',
    'reorder_error_message'        => 'El orden no se ha guardado.',

    // CRUD yes/no
    'yes' => 'Sí',
    'no' => 'No',

    // Fields
    'browse_uploads' => 'Subir archivos',
    'clear' => 'Limpiar',
    'page_link' => 'Enlace',
    'page_link_placeholder' => 'http://example.com/su-pagina',
    'internal_link' => 'Enlace interno',
    'internal_link_placeholder' => 'Slug interno. Ejplo: \'admin/page\' (sin comillas) para \':url\'',
    'external_link' => 'Enlace externo',

    // Filtering
    'filters'           =>  'Filtros',
    'remove_filters'    =>  'Remover filtros',
    'toggle_filters'    =>  'Alternar filtros',

];

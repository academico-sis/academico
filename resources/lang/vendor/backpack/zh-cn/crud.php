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

    // Create form
    'add'                 => '新增',
    'back_to_all'         => '回到所有 ',
    'cancel'              => '取消',
    'add_a_new'           => '新增一个 ',

    // Create form - advanced options
    'after_saving'            => '新增后',
    'go_to_the_table_view'    => '回到总表',
    'let_me_add_another_item' => '新增另一个记录',
    'edit_the_new_item'       => '编辑这个记录',

    // Edit form
    'edit'                 => '编辑',
    'save'                 => '存储',

    // Revisions
    'revisions'            => '修顶版本',
    'no_revisions'         => '找不到修订版本',
    'created_this'         => '建立的',
    'changed_the'          => '更改了',
    'restore_this_value'   => '还原这个值',
    'from'                 => '由',
    'to'                   => '到',
    'undo'                 => '撤销',
    'revision_restored'    => '成功恢复修订版本',

    // CRUD table view
    'all'                       => '全部 ',
    'in_the_database'           => '',
    'list'                      => '列表',
    'actions'                   => '操作',
    'preview'                   => '预览',
    'delete'                    => '删除',
    'admin'                     => '管理員',
    'details_row'               => '这是详情列。你可以在这里做出编辑。',
    'details_row_loading_error' => '加载详情时出错。请重试。',

    // Confirmation messages and bubbles
    'delete_confirm'                              => '你确定要删除这条记录吗？',
    'delete_confirmation_title'                   => '记录已被删除',
    'delete_confirmation_message'                 => '这条记录已被成功删除。',
    'delete_confirmation_not_title'               => '记录未被删除',
    'delete_confirmation_not_message'             => '尝试删除时发生错误。这项记录或未被成功删除。',
    'delete_confirmation_not_deleted_title'       => '记录未被删除',
    'delete_confirmation_not_deleted_message'     => '沒有任何事情发生过。你的这条记录纹丝未动。',

    // DataTables translation
    'emptyTable'     => '数据库中没有相关记录',
    'info'           => '正在显示 _TOTAL_ 个记录中的 _START_ 至 _END_ 项',
    'infoEmpty'      => '正在显示 0 个记录中的 0 至 0 项',
    'infoFiltered'   => '(自 _TOTAL_ 个记录中筛选出来的记录)',
    'infoPostFix'    => '',
    'thousands'      => ',',
    'lengthMenu'     => '每页 _MENU_ 条记录',
    'loadingRecords' => '加载中...',
    'processing'     => '处理中...',
    'search'         => '搜索: ',
    'zeroRecords'    => '找不到相关记录',
    'paginate'       => [
        'first'    => '首页',
        'last'     => '尾页',
        'next'     => '下一页',
        'previous' => '上一页',
    ],
    'aria' => [
        'sortAscending'  => ': 增序排列',
        'sortDescending' => ': 降序排列',
    ],

    // global crud - errors
    'unauthorized_access' => '您沒有权限浏览此页面。',
    'please_fix' => '请修正以下错误：',

    // global crud - success / error notification bubbles
    'insert_success' => '插入成功。',
    'update_success' => '更新成功。',

    // CRUD reorder view
    'reorder'                      => '重新排序',
    'reorder_text'                 => '请以拖放 (drag and drop) 的放式重新排序。',
    'reorder_success_title'        => '完成',
    'reorder_success_message'      => '你的排序已被儲存。',
    'reorder_error_title'          => '错误',
    'reorder_error_message'        => '你的排序尚未被儲存。',

    // CRUD yes/no
    'yes' => 'Yes',
    'no' => 'No',

    // Fields
    'browse_uploads' => '查看已上传的文档',
    'clear' => '清除',
    'page_link' => '页面链接',
    'page_link_placeholder' => 'http://example.com/your-desired-page',
    'internal_link' => '内部链接',
    'internal_link_placeholder' => '内部链接，例如: \'admin/page\' (no quotes) for \':url\'',
    'external_link' => '外部链接',
    'choose_file' => '选择文件',

    //Table field
    'table_cant_add' => '不能再增加 :entity',
    'table_max_reached' => '已达到 :max 条记录的上限',

];

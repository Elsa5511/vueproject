<?php

namespace App\DataTables\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class ChatsDataTable extends DataTable {

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {
        return datatables($query)
                        ->addColumn('action', 'admin.chats.table-actions')
                        ->setRowId('{{$id}}')
                        ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request) {
        /* return \Cypretex\Chat\Models\Conversation::select('converstaions.*, customer.name as customer, provider.name as provider')
          ->join('mc_conversation_user'); */
        return DB::select(DB::raw("SELECT conversations.*, customer.name as customer_name, provider.name as provider_name FROM mc_conversations as conversations "
                                . "LEFT JOIN (SELECT conversation_id as c_c_id, users.* FROM mc_conversation_user JOIN users ON(users.id=mc_conversation_user.user_id) WHERE users.role='USER') as customer ON(c_c_id=conversations.id) "
                                . "LEFT JOIN (SELECT conversation_id as p_c_id, users.* FROM mc_conversation_user JOIN users ON(users.id=mc_conversation_user.user_id) WHERE users.role='PROVIDER') as provider ON(p_c_id=conversations.id)"));
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html() {
        return $this->builder()
                        ->columns($this->getColumns())
                        ->minifiedAjax()
                        ->addAction(['width' => '80px'])
                        ->parameters($this->getBuilderParameters());
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns() {
        return [
            'id',
            'customer_name' => ['title' => 'Cliente', 'name' => 'customer_name'],
            'provider_name' => ['title' => 'Modelo', 'name' => 'provider_name'],
            'updated_at' => ['title' => 'Ultimo mensaje'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {
        return 'Admin/Chats_' . date('YmdHis');
    }

}

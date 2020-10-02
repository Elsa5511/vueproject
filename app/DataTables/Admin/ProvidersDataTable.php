<?php

namespace App\DataTables\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class ProvidersDataTable extends DataTable {

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {
        return datatables($query)
                        ->addColumn('action', 'admin.providers.table-actions')
                        /* ->editColumn('is_verified', function ($model) {
                          return "<span class='status status-{$model->is_verified}'>" . (($model->is_verified) ? 'YES' : 'NO') . "</span>";
                          }) */
                        ->editColumn('status', function ($model) {
                            return "<span class='status status-" . strtolower($model->status) . "'>" . $model->status . "</span>";
                        })
                        ->editColumn('identity_verified', function ($model) {
                            return "<span class='status status-" . (($model->identity_verified) ? 'active' : 'pending') . "'>" . (($model->identity_verified) ? 'Verificada' : 'Pendiente') . "</span>";
                        })
                        ->setRowId('{{$id}}')
                        ->rawColumns(['status', 'identity_verified', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Request $request) {

        $where = ['role' => 'PROVIDER', 'status' => 'active', 'identity_verified' => true];

        if ($request->get('status') != '') {
            if ($request->input('status') == 'all') {
                unset($where['status']);
            } else {
                $where['status'] = $request->get('status');
            }
        }

        $users = User::select('users.*')->with(['country'])
                ->where($where);
        if ($request->input('status') == 'pending') {
            $users->orWhere(function($query) {
                $query->where(['role' => 'PROVIDER', 'identity_verified' => false]);
            });
        }
        return $users;
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
            'country.name' => ['title' => 'Pais'],
            'country.phonecode' => ['title' => 'Codigo'],
            'phone' => ['title' => 'Telefono'],
            'name' => ['title' => 'Nombre'],
            'role' => ['title' => 'Rol'],
            'created_at' => ['title' => 'F. Registro'],
            'status' => ['title' => 'Estado', 'name' => 'status'],
            'identity_verified' => ['title' => 'Identidad', 'name' => 'identity_verified'],
                //'is_verified' => ['title' => 'Verified?', 'name' => 'is_verified'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {
        return 'Admin/Users_' . date('YmdHis');
    }

}

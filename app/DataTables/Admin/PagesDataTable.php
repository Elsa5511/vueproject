<?php

namespace App\DataTables\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class PagesDataTable extends DataTable {

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {
        return datatables($query)
                        ->addColumn('action', 'admin.pages.table-actions')                        
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
        /* $query = $model->newQuery();
          $query->where(['role' => 'USER']);
          $query->with(['profile'])->select('id', 'email', 'status', 'created_at', 'updated_at');
          $posts = User::with('profile')->select('users.*', 'user.profile.first_name');
          return $posts; */



        $where = ['published' => 1];

        if ($request->get('status') != '') {
            if ($request->input('status') == 'all') {
                unset($where['published']);
            } elseif($request->input('status')=='unpublished') {
                $where['published'] = 0;
            }
        }

        $users = Page::select('*')
                ->where($where);
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
            'title' => ['title' => 'Titulo'],
            'slug' => ['title' => 'Slug'],
            'published' => ['title' => 'Publicada?', 'name' => 'published'],
                //'is_verified' => ['title' => 'Verified?', 'name' => 'is_verified'],
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {
        return 'Admin/Pages_' . date('YmdHis');
    }

}

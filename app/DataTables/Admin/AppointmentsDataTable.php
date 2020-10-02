<?php

namespace App\DataTables\Admin;

use App\Models\User;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Yajra\DataTables\Services\DataTable;

class AppointmentsDataTable extends DataTable {

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query) {
        return datatables($query)
                        ->addColumn('action', 'admin.appointments.table-actions')
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


        $where = [];
        if ($this->user) {
            if ($this->user->role == 'USER') {
                $where['appointments.customer_id'] = $this->user->id;
            } else if ($this->user->role == 'PROVIDER') {
                $where['appointments.provider_id'] = $this->user->id;
            }
        }




        $records = Appointment::select('appointments.*', 'customers.name as customer_name', 'providers.name as provider_name')
                ->join('users as customers', 'customers.id', '=', 'appointments.customer_id')
                ->join('users as providers', 'providers.id', '=', 'appointments.provider_id')
                ->where($where);

        switch ($request->get('status')) {
            case 'active':
                $records->where(function($q) {
                    return $q->where(['status_name' => 'ON_THE_WAY'])->orWhere(['status_name' => 'IN_PROGRESS'])->orWhere(['status_name' => 'AWAITING_ACCEPTANCE']);
                });
                break;
            case 'pending':
                $records->where(function($q) {
                    return $q->where(['status_name' => 'PENDING']);
                });
                break;
            case 'finalized':
                $records->where(function($q) {
                    return $q->where(['finished' => 1]);
                });
                break;
        }
        return $records;
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


        $columns = [
            'customer_name' => ['title' => 'Cliente', 'name' => 'customer_name'],
            'provider_name' => ['title' => 'Modelo', 'name' => 'provider_name'],
            'date_from' => ['title' => 'F. Desde'],
            'date_to' => ['title' => 'F. Hasta'],
            'status_name' => ['title' => 'Estado'],
        ];

        if ($this->user) {
            if ($this->user->role == 'USER') {
                unset($columns['customer_name']);
            } else if ($this->user->role == 'PROVIDER') {
                unset($columns['provider_name']);
            }
        }

        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename() {
        return 'Admin/Appointments_' . date('YmdHis');
    }

}

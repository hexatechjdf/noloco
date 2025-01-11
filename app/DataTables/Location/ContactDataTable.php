<?php

namespace App\DataTables\Location;

use App\Models\Contact;
use Yajra\DataTables\Html\Column;
use Illuminate\Support\Str;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\DB;

class ContactDataTable extends DataTable
{

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($query) {
                return view('location.contacts.action',get_defined_vars());
            })
            ->rawColumns(['action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = Contact::query();
        $model->where('user_id', auth()->id())->with('user');

        $model->withCount([
            'payoffs as pending_payoffs_count' => function($query) {
                $query->where('status', 'pending');
            },
            'payoffs as received_payoffs_count' => function($query) {
                $query->where('status', 'received');
            },
            'payoffs as total_payoffs_count'
        ]);

        $model->orderBy('created_at','desc');

        return $this->applyScopes($model);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('<"row align-items-center"<"col-md-2" l><"col-md-6" B><"col-md-4"f>><"table-responsive my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">')
            ->parameters([
                "buttons" => [
                    'excel',
                ],
                "processing" => true,
                "autoWidth" => false,
                'initComplete' => "function () {
                            $('.dt-buttons').addClass('btn-group btn-group-sm')
                            this.api().columns().every(function (colIndex) {

                            });


                        }",
                'drawCallback' => "function () {
                        }"
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            ['data' => 'name', 'name' => 'name', 'title' => 'Name', 'orderable' =>false ,'searchable' => true],
            ['data' => 'email', 'name' => 'email', 'title' => 'Email', 'orderable' =>false ,'searchable' => true],
            ['data' => 'crm_contact_id', 'name' => 'crm_contact_id', 'title' => 'Contact Id', 'orderable' =>false ,'searchable' => true],
            ['data' => 'pending_payoffs_count', 'name' => 'pending_payoffs_count', 'title' => 'Pending Payoffs', 'orderable' =>false ,'searchable' => false],
            ['data' => 'received_payoffs_count', 'name' => 'received_payoffs_count', 'title' => 'Received Payoffs', 'orderable' =>false ,'searchable' => false],
            ['data' => 'total_payoffs_count', 'name' => 'total_payoffs_count', 'title' => 'Total Payoffs', 'orderable' =>false ,'searchable' => false],
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->orderable(false)
                ->width(20)
                ->addClass('text-center hide-search'),
        ];
    }


      /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename(): string
    {
        return 'Export_' . date('YmdHis');
    }

   /**
    * Get filename for export.
    *
    * @return string
    */
    protected function sheetName() : string
    {
        return "Yearly Report";
    }
}

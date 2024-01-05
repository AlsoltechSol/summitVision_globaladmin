<?php

namespace App\Exports;

use App\Models\Company;
use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection,WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $usr = \Auth::user();
        if ($usr->can('Manage Order') || $usr->type == 'super admin') {
            $orders  = Order::select(
                [
                    'orders.*',
                    'companies.name as user_name',
                ]
            );
            if (!empty($this->request->start_date) && $this->request->start_date != 'null') {
                $orders->where('orders.created_at', '>=', $this->request->start_date);
            }

            if (!empty($this->request->end_date) && $this->request->end_date != 'null') {
                $orders->where('orders.created_at', '<=', $this->request->end_date);
            }

            if (!empty($this->request->payment_status) && $this->request->payment_status != 'null') {
                $orders->where('orders.payment_status', $this->request->payment_status);
            }


            $orders->join('companies', 'orders.company_id', '=', 'companies.id')->orderBy('orders.created_at', 'DESC');

            $orders = $orders->get();
            // dd($orders);
            foreach ($orders as &$order) {
                $company = Company::find($order['company_id']);
                $order['name'] = $order['user_name'];
                $order['email'] = $company->email;

                $arr = ['user_name', 'updated_at', 'user_id', 'receipt'];

                foreach ($arr as $key) {
                    unset($order[$key]);
                }
            }

            return $orders;
        }
    }

    public function headings(): array
    {
        return [
            "SN ID",
            "COMPANY ID",
            "ORDER ID",
            "NAME",
            "EMAIL",
            "CARD NUMBER",
            "CARD EXP MONTH",
            "CARD EXP YEAR",
            "PLAN NAME",
            "PLAN ID",
            "PRICE",
            "PRICE CURRENCY",
            "TXN ID",
            "PAYMENT STATUS",
            "PAYMENT TYPE",
            "ORDER CREATED AT",
        ];
    }
}

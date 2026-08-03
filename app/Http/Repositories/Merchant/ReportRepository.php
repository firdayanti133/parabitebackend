<?php

namespace App\Http\Repositories\Merchant;

use Illuminate\Support\Facades\DB;

class ReportRepository
{
    public static function checkDailyIncome($merchant_id)
    {
        $query = DB::table('user_orders')
            ->where('merchant_id', $merchant_id)
            ->whereDate('created_at', now()->toDateString())
            ->where('is_paid', 1)
            ->sum('bill');

        return intval($query);
    }

    public static function getListDailyOrder($merchant_id, $page, $limit)
    {
        $query = DB::table('user_orders')
            ->where('merchant_id', $merchant_id)
            ->where('status', '3')
            ->where('is_paid', 1)
            ->whereDate('created_at', now()->toDateString())
            ->offset(($page - 1) * $limit)
            ->limit($limit)
            ->get();

        $data = $query->map(function ($item, $index) use ($page, $limit) {
            $orderNumber = ($page - 1) * $limit + $index + 1;

            return [
                'id' => $item->id,
                'queue_number' => $item->queue_number,
                'user_id' => $item->user_id,
                'merchant_id' => $item->merchant_id,
                'location_id' => $item->location_id,
                'order_number' => $orderNumber,
                'bill' => $item->bill,
                'type' => $item->type,
                'payment_method' => $item->payment_method,
                'status' => $item->status,
                'schedule' => $item->schedule,
                'is_preorder' => $item->is_preorder,
                'created_at' => $item->created_at,
            ];
        });

        return $data;
    }

    public static function getListWeeklyOrder($merchant_id, $start_date, $end_date)
    {
        $query = DB::table('user_orders')
            ->where('merchant_id', $merchant_id)
            ->where('status', '3')
            ->where('is_paid', 1)
            ->whereDate('created_at', '>=', $start_date)
            ->whereDate('created_at', '<=', $end_date)
            ->selectRaw('DATE(created_at) as date, SUM(bill) as total_income, COUNT(id) as total_order')
            ->groupByRaw('DATE(created_at)')
            ->get();

        $totalIncome = $query->sum('total_income');
        $totalOrder = $query->sum('total_order');

        $data = [
            'data' => $query,
            'total_income' => $totalIncome,
            'total_order' => $totalOrder,

        ];

        return $data;
    }
}

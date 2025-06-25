<?php

namespace App\Http\Repositories\Merchant;

use Illuminate\Support\Facades\DB;

class ReportRepository {
    public static function checkDailyIncome($merchant_id) {
        $query = DB::table('user_orders')
        ->where('merchant_id', $merchant_id)
        ->where(DB::raw('TO_DAYS(created_at)'), DB::raw('TO_DAYS(NOW())'))
        ->where('status', '3')
        ->sum('bill');

        return intval($query);
    }

    public static function getListDailyOrder($merchant_id, $page, $limit) {
        $query = DB::table('user_orders')
        ->where('merchant_id', $merchant_id)
        ->where('status', '3')
         ->where(DB::raw('TO_DAYS(created_at)'), DB::raw('TO_DAYS(NOW())'))
        ->offset(($page - 1) * $limit)
        ->limit($limit)
        ->get();

        $data = $query->map(function ($item, $index) use ($page, $limit) {
            $orderNumber = ($page - 1) * $limit + $index + 1;
            return [
                'id' => $item->id,
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

    public static function getListWeeklyOrder($merchant_id, $start_date, $end_date) {
        $query = DB::table('user_orders')
            ->where('merchant_id', $merchant_id)
            ->where('status', '3')
            ->whereBetween(DB::raw('TO_DAYS(created_at)'), [DB::raw('TO_DAYS("' . $start_date . '")'), DB::raw('TO_DAYS("' . $end_date . '")')])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(bill) as total_income'), DB::raw('COUNT(id) as total_order'))
            ->groupBy(DB::raw('DATE(created_at)'))
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
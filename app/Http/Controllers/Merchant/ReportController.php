<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Http\Repositories\Merchant\ReportRepository;

class ReportController extends Controller
{
    public function getListDailyIncome(Request $request) {
        $merchant = $request->get('auth_user');
        $validateId = Validator::make(['merchant_id' => $merchant->id], [
            'merchant_id' => 'exists:users,id',
        ]);

        $validator = Validator::make($request->all(), [
            'page' => 'numeric',
            'limit' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $page = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }

        try {
            $report = ReportRepository::getListDailyOrder($merchant->id, $page, $limit);
            $income = ReportRepository::checkDailyIncome($merchant->id);

            $data = [
                'report' => $report,
                'income' => $income,
            ];

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }

    public function getWeeklyReport(Request $request) {
        $merchant = $request->get('auth_user');
        $validateId = Validator::make(['merchant_id' => $merchant->id], [
            'merchant_id' => 'exists:users,id',
        ]);

        if ($validateId->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validateId->errors()
            ], 422);
        }
        
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $report = ReportRepository::getListWeeklyOrder($merchant->id, $request->start_date, $request->end_date);

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $report
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Internal Server Error',
                'errors' => $e
            ], 500);
        }
    }
}

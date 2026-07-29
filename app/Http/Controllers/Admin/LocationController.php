<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Admin\LocationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:100',
            'search' => 'sometimes|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $data = LocationRepository::getList(
                (int) $request->input('page', 1),
                (int) $request->input('limit', 10),
                trim((string) $request->input('search', ''))
            );

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => $data,
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $name = trim($request->name);
        if ($name === '') {
            return $this->nameValidationError('The location name field is required.');
        }

        if (LocationRepository::nameExists($name)) {
            return $this->nameValidationError('The location name has already been taken.');
        }

        try {
            $locationId = DB::transaction(fn () => LocationRepository::create($name));

            return response()->json([
                'code' => 201,
                'message' => 'Success',
                'data' => LocationRepository::getDetail($locationId),
            ], 201);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function show(int $locationId)
    {
        $location = LocationRepository::getDetail($locationId);

        if (! $location) {
            return $this->notFound();
        }

        return response()->json([
            'code' => 200,
            'message' => 'Success',
            'data' => $location,
        ]);
    }

    public function update(Request $request, int $locationId)
    {
        if (! LocationRepository::getDetail($locationId)) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $name = trim($request->name);
        if ($name === '') {
            return $this->nameValidationError('The location name field is required.');
        }

        if (LocationRepository::nameExists($name, $locationId)) {
            return $this->nameValidationError('The location name has already been taken.');
        }

        try {
            DB::transaction(fn () => LocationRepository::update($locationId, $name));

            return response()->json([
                'code' => 200,
                'message' => 'Success',
                'data' => LocationRepository::getDetail($locationId),
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    public function destroy(int $locationId)
    {
        if (! LocationRepository::getDetail($locationId)) {
            return $this->notFound();
        }

        try {
            $orderCount = LocationRepository::countOrders($locationId);
            if ($orderCount > 0) {
                return response()->json([
                    'code' => 409,
                    'message' => 'Location cannot be deleted because it is used by existing orders',
                    'errors' => [
                        'orders' => $orderCount,
                    ],
                ], 409);
            }

            DB::transaction(fn () => LocationRepository::delete($locationId));

            return response()->json([
                'code' => 200,
                'message' => 'Location deleted successfully',
                'data' => null,
            ]);
        } catch (\Throwable $exception) {
            return $this->serverError($exception);
        }
    }

    private function validationError($validator)
    {
        return response()->json([
            'code' => 422,
            'message' => 'Validation Error',
            'errors' => $validator->errors(),
        ], 422);
    }

    private function nameValidationError(string $message)
    {
        return response()->json([
            'code' => 422,
            'message' => 'Validation Error',
            'errors' => [
                'name' => [$message],
            ],
        ], 422);
    }

    private function notFound()
    {
        return response()->json([
            'code' => 404,
            'message' => 'Location not found',
            'errors' => null,
        ], 404);
    }

    private function serverError(\Throwable $exception)
    {
        report($exception);

        return response()->json([
            'code' => 500,
            'message' => 'Internal Server Error',
            'errors' => null,
        ], 500);
    }
}

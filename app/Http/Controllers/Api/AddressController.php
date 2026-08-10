<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $addresses = $request->user()
            ->addresses()
            ->where('is_active', true)
            ->latest()
            ->get();

        return response()->json([
            'data' => AddressResource::collection($addresses),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'province_id' => ['required', 'exists:provinces,id'],
            'province_name' => ['nullable', 'string', 'max:255'],
            'city_id' => ['required', 'exists:cities,id'],
            'city_name' => ['nullable', 'string', 'max:255'],
            'full_address' => ['required', 'string', 'max:1000'],
            'postal_code' => ['required', 'string', 'max:10'],
            'mobile' => ['required', 'string', 'max:11'],
            'is_default' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $data['user_id'] = $request->user()->id;

        if (!empty($data['is_default'])) {
            $request->user()->addresses()->update(['is_default' => false]);
        }

        $address = $request->user()->addresses()->create($data);

        return response()->json([
            'message' => 'آدرس با موفقیت اضافه شد',
            'data' => new AddressResource($address),
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'province_id' => ['sometimes', 'exists:provinces,id'],
            'province_name' => ['nullable', 'string', 'max:255'],
            'city_id' => ['sometimes', 'exists:cities,id'],
            'city_name' => ['nullable', 'string', 'max:255'],
            'full_address' => ['sometimes', 'string', 'max:1000'],
            'postal_code' => ['sometimes', 'string', 'max:10'],
            'mobile' => ['sometimes', 'string', 'max:11'],
            'is_default' => ['boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['is_default'])) {
            $request->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json([
            'message' => 'آدرس با موفقیت به‌روزرسانی شد',
            'data' => new AddressResource($address->fresh()),
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);
        $address->update(['is_active' => false]);

        return response()->json(['message' => 'آدرس با موفقیت حذف شد']);
    }

    public function setDefault(Request $request, $id): JsonResponse
    {
        $address = $request->user()->addresses()->findOrFail($id);

        $request->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return response()->json([
            'message' => 'آدرس پیش‌فرض با موفقیت تنظیم شد',
            'data' => new AddressResource($address->fresh()),
        ]);
    }
}

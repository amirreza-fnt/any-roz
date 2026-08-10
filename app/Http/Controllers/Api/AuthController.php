<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private const OTP_TTL = 300;

    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', 'max:20', 'regex:/^09[0-9]{9}$/'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mobile = $request->mobile;
        $otp = (string) random_int(100000, 999999);
        $attemptsKey = "otp_attempts_{$mobile}";

        $attempts = (int) Cache::get($attemptsKey, 0);
        if ($attempts >= 5) {
            return response()->json([
                'message' => 'تعداد درخواست‌های مجاز را رد کرده‌اید. لطفاً ۱۰ دقیقه بعد تلاش کنید.',
            ], 429);
        }

        Cache::put("otp_{$mobile}", $otp, self::OTP_TTL);
        Cache::put($attemptsKey, $attempts + 1, 600);

        if (config('app.debug')) {
            return response()->json([
                'message' => 'کد تأیید برای شما ارسال شد',
                'otp' => $otp,
            ]);
        }

        // TODO: Send SMS via gateway
        // SmsService::send($mobile, "کد تأیید آنی‌رز: $otp");

        return response()->json([
            'message' => 'کد تأیید برای شما ارسال شد',
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'mobile' => ['required', 'string', 'max:20'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $mobile = $request->mobile;
        $cachedOtp = Cache::get("otp_{$mobile}");

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json([
                'message' => 'کد تأیید نامعتبر یا منقضی شده است',
            ], 422);
        }

        Cache::forget("otp_{$mobile}");
        Cache::forget("otp_attempts_{$mobile}");

        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            $user = User::create([
                'name' => $mobile,
                'email' => "{$mobile}@aniroz.ir",
                'mobile' => $mobile,
                'password' => Hash::make(str()->random(32)),
                'is_active' => true,
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'حساب کاربری شما غیرفعال شده است'], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'ورود با موفقیت انجام شد',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'خروج با موفقیت انجام شد']);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'is_active' => $user->is_active,
            'created_at' => $user->created_at,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['sometimes', 'confirmed', Password::defaults()],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'پروفایل با موفقیت به‌روزرسانی شد',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
        ]);
    }
}

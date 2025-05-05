<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log; 
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; 


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string',
            'bag_id' => 'required|string|unique:users,bag_id',
            'password' => 'required|string|min:6',
            'image' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $user = User::create([
            'name' => $request->owner_name,
            'email' => $request->owner_email,
            'phone' => $request->owner_phone,
            'bag_id' => $request->bag_id,
            'password' => Hash::make($request->password),
            'image' => $imagePath,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'bag_id' => 'required|string',
            'password' => 'required|string',
        ]);

        Log::info('Login Request:', $request->all());

        $credentials = $request->only('bag_id', 'password');

        // محاولة تسجيل الدخول
        if (!$token = JWTAuth::attempt(['bag_id' => $credentials['bag_id'], 'password' => $credentials['password']])) {
            // في حالة فشل المصادقة، إرجاع رسالة توضح السبب
            return response()->json(['error' => 'Invalid credentials. Please check your bag_id and password.'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user(),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'phone' => 'required|string', 
            'new_password' => 'required|string|min:6',
        ]);
    
        $user = User::where('email', $request->email)
                    ->where('phone', $request->phone) 
                    ->first();
    
        if (!$user) {
            return response()->json(['error' => 'User not found or phone number is incorrect'], 404);
        }
    
        // تحديث كلمة المرور
        $user->password = Hash::make($request->new_password);
        $user->save();
    
        return response()->json(['message' => 'Password updated successfully']);
    }
    
    public function sendResetOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $otp = rand(100000, 999999);

    DB::table('password_resets')->where('email', $request->email)->delete();

    DB::table('password_resets')->insert([
        'email' => $request->email,
        'otp' => $otp,
        'created_at' => Carbon::now(),
    ]);

    Mail::raw("رمز إعادة تعيين كلمة المرور هو: $otp", function ($message) use ($request) {
        $message->to($request->email)
                ->subject('إعادة تعيين كلمة المرور');
    });

    return response()->json(['message' => 'OTP has been sent to your email']);
}
public function confirmResetOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
        'new_password' => 'required|string|min:6',
    ]);

    $record = DB::table('password_resets')
    ->where('email', $request->email)
    ->where('otp', $request->otp)
    ->first();

if (!$record) {
    return response()->json(['error' => 'OTP is invalid'], 400);
}

$otpCreatedAt = Carbon::parse($record->created_at);
if ($otpCreatedAt->diffInMinutes(Carbon::now()) > 10) {
    return response()->json(['error' => 'OTP has expired'], 400);
}


    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->new_password);
    $user->save();

    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password has been updated successfully']);
}

}

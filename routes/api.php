<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\BagController;
use App\Http\Controllers\NotificationController;

// 📌 تسجيل مستخدم جديد
Route::post('/register', [AuthController::class, 'register']);

// 📌 تسجيل الدخول
Route::post('/login', [AuthController::class, 'login']);

// 📌 إرسال OTP لإعادة تعيين كلمة المرور
Route::post('/send-reset-otp', [AuthController::class, 'sendResetOtp']);

// 📌 تأكيد OTP وتعيين كلمة مرور جديدة
Route::post('/confirm-reset-otp', [AuthController::class, 'confirmResetOtp']);

// 📌 تسجيل الدخول باستخدام Google أو Facebook (OAuth)
Route::get('auth/redirect/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('auth/callback/{provider}', [SocialAuthController::class, 'callback']);

// 📌 عرض بيانات الشنطة (dashboard)
Route::middleware('auth:api')->get('/bag/dashboard', [BagController::class, 'dashboard']);

// 📌 تحديث بيانات الشنطة
Route::middleware('auth:api')->post('/bag/update', [BagController::class, 'updateBagData']);

// 📌 جلب كل الإشعارات الخاصة بالمستخدم
Route::middleware('auth:api')->get('/notifications', [NotificationController::class, 'getNotifications']);

// 📌 إرسال إشعار جديد إلى المستخدم
Route::middleware('auth:api')->post('/notifications', [NotificationController::class, 'sendNotification']);

// 📌 تعليم إشعار كمقروء
Route::middleware('auth:api')->put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);

// 📌 البحث عن الشنطة باستخدام الـ bag_id
Route::middleware('auth:api')->post('/search-bag', [BagController::class, 'searchByBagId']);

// 📌 جلب بيانات المستخدم الحالي بناءً على JWT Token
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

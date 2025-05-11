<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $request->email;

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User with this email does not exist.',
            ], 404);
        }

        // Generate a unique token
        $token = Str::random(60);

        // Set reset token and expiry date on user
        $user->reset_token = $token;
        $user->reset_token_expires_at = Carbon::now()->addHours(2); // Token expires in 2 hours
        $user->save();

        // Send email with a reset link
        $resetLink = url('/auth/reset-password/' . $token); // Создайте маршрут reset-password

        try {
            Mail::send('emails.reset_password', ['resetLink' => $resetLink, 'name' => $user->name], function ($message) use ($email, $user) {
                $message->to($email)
                    ->subject('Reset Your Password')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            return response()->json([
                'message' => 'A password reset link has been sent to your email address.',
            ], 200);

        } catch (\Exception $e) {
            // Log the error

            return response()->json([
                'message' => 'Failed to send password reset email.',
                'error' => $e->getMessage(), // Optional: Return specific error message
            ], 500);
        }
    }
}

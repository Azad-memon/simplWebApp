<?php

namespace App\Helpers;

class MessageHelper
{
    public static function defaultValidationMessages($language = 'EN')
    {
        // Multilingual error messages
        $messages = [
            'EN' => [
                'first_name.required' => 'Please enter first name.',
                'first_name.max' => 'The first name may not be greater than 255 characters.',

                'last_name.required' => 'Please enter last name.',
                'last_name.max' => 'The name may not be greater than 255 characters.',

                'email.required' => 'Email address is required.',
                'email.email' => 'Please provide a valid email address.',
                'email.max' => 'The email may not be greater than 255 characters.',
                'email.unique' => 'This email is already registered.',

                'phone.required' => 'Phone number is required.',
                'phone.max' => 'The phone number may not be greater than 15 characters.',
                'phone.unique' => 'This phone number is already registered.',

                'password.required' => 'Please provide a password.',
                'password.min' => 'The password must be at least 6 characters long.',

                'c_password.same' => 'The password does not match.',
                'c_password.required' => 'Please confirm your password.',

                'otp.required' => 'Please enter otp code.',


            ],
            'UR' => [
                'first_name.required' => 'براہ کرم پہلا نام درج کریں۔',
                'first_name.max' => 'پہلا نام 255 حروف سے زیادہ نہیں ہو سکتا۔',

                'last_name.required' => 'براہ کرم آخری نام درج کریں۔',
                'last_name.max' => 'نام 255 حروف سے زیادہ نہیں ہو سکتا۔',

                'email.required' => 'ای میل ایڈریس درج کرنا ضروری ہے۔',
                'email.email' => 'براہ کرم درست ای میل ایڈریس فراہم کریں۔',
                'email.max' => 'ای میل 255 حروف سے زیادہ نہیں ہو سکتی۔',
                'email.unique' => 'یہ ای میل پہلے سے رجسٹرڈ ہے۔',

                'phone.required' => 'فون نمبر درج کرنا ضروری ہے۔',
                'phone.max' => 'فون نمبر 15 حروف سے زیادہ نہیں ہو سکتا۔',
                'phone.unique' => 'یہ فون نمبر پہلے سے رجسٹرڈ ہے۔',

                'password.required' => 'براہ کرم پاس ورڈ درج کریں۔',
                'password.min' => 'پاس ورڈ کم از کم 6 حروف پر مشتمل ہونا چاہیے۔',

                'c_password.same' => 'پاس ورڈ مماثل نہیں ہے۔',
                'c_password.required' => 'براہ کرم پاس ورڈ کی تصدیق کریں۔',

                'otp.required' => 'Please enter otp code.',
                "current_password"=> 'موجودہ پاس ورڈ درست نہیں ہے',

            ],

        ];


        $defaultMessages = $messages['EN'];
        $selectedMessages = $messages[$language] ?? [];

        $finalMessages = [];
        foreach ($defaultMessages as $key => $value) {

            $finalMessages[$key] = $selectedMessages[$key] ?? $value;
        }

        return $finalMessages;
    }
    public static function defaultSuccessMessages($language = 'EN', $fields = [])
    {
        // Multilingual success messages
        $messages = [

            'EN' => [
                'register.success' => 'User registered successfully.',
                'register.verify' => 'Please verify the account.',
                'login.success' => 'Login successful.',
                'logout.success' => 'You have been logged out.',
                'login.error' => 'Invalid credentials or inactive customer.',
                'logout.error' => 'You are not logged in.',
                'validation.error' => 'Required fields.',
                'otp.success' => 'OTP is sent successfully to your device.',
                'otp.verify' => 'User verified successfully.',
                'otp.invalid' => 'Please enter valid otp.',
                'otp' => 'OTP',
                'user.invalid' => 'User does not exit.',
                'error' => 'Error',
                'error.unknown' => 'Unknown error occured try again later.',
                'error.otp' => 'Please enter valid otp.',
                "error.otp.invalid" => 'Please enter valid otp.',
                "reset-password.success" => 'Password reset successfully.',
                "current_password"=> 'Current password is incorrect.',
             ],
            'UR' => [
                'register.success' => 'صارف کو کامیابی سے رجسٹر کر لیا گیا۔',
                'register.verify' => 'براہ کرم اکاؤنٹ کی تصدیق کریں۔',
                'login.success' => 'لاگ اِن کامیاب رہا۔',
                'logout.success' => 'آپ کامیابی سے لاگ آؤٹ ہو چکے ہیں۔',
                'login.error' => 'غلط اسناد یا غیر فعال صارف۔',
                'logout.error' => 'آپ لاگ آؤٹ نہیں ہیں۔',
                'validation.error' => 'درکار فیلڈز درکار ہیں۔',
                'otp.success' => 'OTP is sent successfully to your device.',
                'otp.verify' => 'User verified successfully.',
                'otp.invalid' => 'Please enter valid otp.',
                'otp' => 'OTP',
                'user.invalid' => 'User does not exit.',
                'error' => 'Error',
                'error.unknown' => 'Unknown error occured try again later.',
                "current_password"=> 'موجودہ پاس ورڈ درست نہیں ہے',
            ],
        ];


        $defaultMessages = $messages['EN'];
        $selectedMessages = $messages[$language] ?? [];

        $finalMessages = [];

        foreach ($fields as $field) {
            // Fallback to English if translation is missing
            $finalMessages[$field] = $selectedMessages[$field] ?? $defaultMessages[$field] ?? '';
        }

        return $finalMessages;
    }



    public static function formatErrors($e)
    {
        //dd($e);
       $errors = [];

    if ($e instanceof \Illuminate\Validation\ValidationException) {
        $validator = $e->validator;

        foreach ($validator->errors()->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errors[] = [
                    'field' => $field,
                    'message' => $message,
                ];
            }
        }
    } else {
        $errors[] = [
            'field' => null,
          //  'message' => method_exists($e, 'getMessage') ? $e->getMessage() : 'An error occurred.',
           'message' => 'An error occurred.',
        ];
    }

    return $errors;
    }
    public static function formatMessages($messages = [])
    {
        $formatedMessages = [];

        foreach ($messages as $message) {
            $formatedMessages[] = $message;
        }


        return $formatedMessages;
    }
    public static function whatsAppMessageTemplate($requestType = "register", $otpCode)
    {
        if ($requestType == "register")
            $message = "Welcome to {{ env('APP_NAME') }} App, Your OTP code is: {{$otpCode}}. For Security do not share this code";
        else if ($requestType == "delete")
            $message = "Warning: This may delete your account forever, if you really want to continue then Your OTP code is: {{$otpCode}}";
        else if ($requestType == "old-number-change")
            $message = "To verify your identity, use this OTP: {{$otpCode}}. If you didn't request this, ignore the message.";
        else if ($requestType == "new-number-request")
            $message = "To verify your identity for the new number update, use this OTP: {{$otpCode}}. If you did not request this change, please ignore this message.";


        return $message;
    }
}

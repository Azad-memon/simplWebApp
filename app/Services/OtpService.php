<?php

namespace App\Services;

use App\Models\ModelOtp;
use App\Models\ModelRequestNewNumber;

use App\Services\TwilioService;
use App\Helpers\MessageHelper;
class OtpService
{
    protected $twilio;


    public function formatE164($number, $countryCode = '92')
    {
        $number = preg_replace('/[^0-9]/', '', $number); // remove any non-digits
        if (strpos($number, '0') === 0) {
            $number = substr($number, 1); // remove leading 0
        }
        return "+$countryCode$number";
    }
    public function generateAndSaveOtp($modelType, $modelId, $request = "register", $numberType = "")
    {
       //return("asdad");
        if (empty($modelType) || empty($modelId)) {
            throw new \InvalidArgumentException('Model type or model ID cannot be empty.');
        }

        try {
            $existingOtp = ModelOtp::where('otpable_type', $modelType)
                ->where('otpable_id', $modelId)
                ->first();

            if ($existingOtp) {
                $existingOtp->delete();
            }

            $otpCode = rand(100000, 999999);

            ModelOtp::create([
                'otpable_id' => $modelId,
                'otpable_type' => $modelType,
                'otp_code' => $otpCode,
            ]);


            $rawPhone = $this->getRecipientNumber($modelType, $modelId, $numberType);
            $recipientNumber = $rawPhone; // e.g. 0304605XXXX
            $recipientNumber = $this->formatE164($rawPhone); // becomes +92304605XXXX

            if (!$recipientNumber) {
                throw new \RuntimeException('Recipient phone number not found.');
            }


            $message = MessageHelper::whatsAppMessageTemplate($request, $otpCode);
            $this->twilio = new TwilioService();

            try {
                $this->twilio->sendWhatsAppMessage($recipientNumber, $message);
                $delivery = 'whatsapp';
            } catch (\Exception $e) {
                \Log::warning('WhatsApp message failed: ' . $e->getMessage());

                try {
                    $this->twilio->sendSmsMessage($recipientNumber, $message);
                    $delivery = 'sms';
                } catch (\Exception $ex) {
                    \Log::error('SMS message also failed: ' . $ex->getMessage());
                    $delivery = 'none';
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to generate and save OTP: ' . $e->getMessage(), 0, $e);
        }
    }



    protected function getRecipientNumber($modelType, $modelId, $requestType = "")
    {
        if (!empty($requestType)) {
            $modelInstance = ModelRequestNewNumber::where("customer_id", $modelId)->first();
            return $modelInstance ? $modelInstance->number : null;
        }

        $modelInstance = $modelType::find($modelId);

        return $modelInstance ? $modelInstance->phone : null;
    }

    public function verifyOtp($data)
    {
        if (empty($data['otp_code']) || empty($data['otpable_id'])) {
            $missingFields = [];
            if (empty($data['otp_code'])) {
                $missingFields[] = 'otp_code';
            }
            if (empty($data['otpable_id'])) {
                $missingFields[] = 'otpable_id';
            }
            if (empty($data['otpable_type'])) {
                $missingFields[] = 'otpable_type';
            }
            return [
                'status' => 'error',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields),
            ];
        }

        $conditions = [
            ['otp_code', '=', $data['otp_code']],
            ['otpable_id', '=', $data['otpable_id']],
            ['otpable_type', '=', $data['otpable_type']],
        ];
        $find = ModelOtp::where($conditions)->first();
        if ($find) {
            $otpData = $find->toArray();
            $find->delete();
            return $otpData;
        } else {
            return null;
        }
    }


}

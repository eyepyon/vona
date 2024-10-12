<?php

namespace app\Console\Commands;

use App\Console\Command;
use App\Console\SMSRequest;
use App\Console\VerificationWorkflow;
use App\Console\Vonage;

class VerifyV2 extends Command
{
    protected $signature = 'verify:v2';

    public function handle()
    {
        $toPhoneNumber = '+818048563939';// 送信先の電話番号';
        $toEmail = 'eyepyon@gmail.com'; // 送信先のメールアドレス
        $brand = 'B3R5ZW4';

        // 認証開始
        $request = new SMSRequest($toPhoneNumber, $brand);
        $request->setLength(10);    // PIN コードの桁数を指定
        $request->setTimeout(60);   // PIN コードの有効期限を指定
        $emailWorkflow = new VerificationWorkflow(VerificationWorkflow::WORKFLOW_EMAIL, $toEmail);
        $request->addWorkflow($emailWorkflow);
        $voiceWorkflow = new VerificationWorkflow(VerificationWorkflow::WORKFLOW_VOICE, $toPhoneNumber);
        $request->addWorkflow($voiceWorkflow);
        $response = Vonage::verify2()->startVerification($request);

        $isAuthenticated = false;
        while (true) {
            $code = $this->ask('Please enter the verification code (Enter "no" to exit)');
            if ($code === 'no') {
                try {
                    // ワークフローのキャンセル
                    Vonage::verify2()->cancelRequest($response['request_id']);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                break;
            }
            // PIN コードのチェック
            try {
                $result = Vonage::verify2()->check($response['request_id'], $code);
                if ($result === true) {
                    $isAuthenticated = true;
                    break;
                }
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
        // 結果メッセージの表示
        if ($isAuthenticated) {
            $this->info('Successfully authenticated');
        } else {
            $this->error('Authentication has failed');
        }
    }
}

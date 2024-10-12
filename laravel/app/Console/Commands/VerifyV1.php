<?php

namespace app\Console\Commands;

use App\Console\Command;
use App\Console\Vonage;

class VerifyV1 extends Command
{
    protected $signature = 'verify:v1';

    public function handle()
    {
        $to = '+818048563939';// 送信先の電話番号
        $from = "+18135920110 ";//Sender ID
        $brand = 'Brand Name';

        // 認証開始
        $workflowId = \Vonage\Verify\Request::WORKFLOW_SMS_TTS_TSS;
        $request = new \Vonage\Verify\Request($to, $brand, $workflowId);
        $request->setCodeLength(\Vonage\Verify\Request::PIN_LENGTH_6);
        $request->setLocale('ja-jp');
        $request->setSenderId($from);
        $response = Vonage::verify()->start($request);

        $failedCount = 0;
        while ($failedCount < 3) {
            $code = $this->ask('Please enter the verification code');
            try {
                // PIN コードのチェック
                $result = Vonage::verify()->check($response->getRequestId(), $code);
                $responseData = $result->getResponseData();
                if ($responseData['status'] === "0") {
                    break;
                }
            } catch (\Vonage\Client\Exception\Exception $e) {
                $failedCount++;
                $this->error($e->getMessage());
            }
        }
        // 結果メッセージの表示
        if ($failedCount < 3) {
            $this->info('Successfully authenticated');
        } else {
            $this->error('Authentication has failed');
        }
    }


}

<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessThirdPartyResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;
    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $client = new Client(['verify' => false]);
            $client->request('POST', 'https://wibip.free.beeceptor.com/order', [
                'form_params' => [
                    "Order_ID" => $this->data['OrderId'],
                    "Customer_Name" => $this->data['customer_name'],
                    "Order_Value" => $this->data['order_value'],
                    "Order_Date" => $this->data['created_at'],
                    "Order_Status" => $this->data['status'],
                    "Process_ID" => $this->data['process_id']
                ]
            ]);

        } catch (\Exception $e) {
            Log::info($e);
        }
    }
}

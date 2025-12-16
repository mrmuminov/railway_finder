<?php

class RailwayService
{
    private string $date;
    private string $depStationCode;
    private string $arvStationCode;
    private float $maxCost;
    private string $botToken;
    private string $chatId;
    private int $interval;

    public function __construct(
        string $date,
        string $depStationCode,
        string $arvStationCode,
        float $maxCost,
        string $botToken,
        string $chatId,
        int $interval
    ) {
        $this->date = $date;
        $this->depStationCode = $depStationCode;
        $this->arvStationCode = $arvStationCode;
        $this->maxCost = $maxCost;
        $this->botToken = $botToken;
        $this->chatId = $chatId;
        $this->interval = $interval;
    }

    private function makeRequest(): ?array
    {
        $url = 'https://eticket.railway.uz/api/v3/handbook/trains/list';
        $headers = [
            'Accept: application/json',
            'Accept-Encoding: gzip, deflate, br, zstd',
            'Accept-Language: uz',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'Content-Type: application/json',
            'Cookie: __stripe_mid=e1f01afa-c761-4b14-bd37-b59c6e3b3fb08ce5d1; G_ENABLED_IDPS=google; XSRF-TOKEN=d0d1aae8-4de1-49c6-965b-5b444c66a6bd; __stripe_sid=ed68b0fe-c1f4-487f-8f5e-4a574b84c6a32580fe',
            'Origin: https://eticket.railway.uz',
            'X-XSRF-TOKEN: d0d1aae8-4de1-49c6-965b-5b444c66a6bd',
        ];

        $data = [
            "directions" => [
                "forward" => [
                    "date" => $this->date,
                    "depStationCode" => $this->depStationCode,
                    "arvStationCode" => $this->arvStationCode
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            error_log('cURL Error: ' . curl_error($ch));
            return null;
        }
        curl_close($ch);

        return json_decode($response, true);
    }

    private function sendMessage(string $text): void
    {
        $telegramUrl = "https://api.telegram.org/bot" . $this->botToken . "/sendMessage?chat_id=" . $this->chatId . "&text=" . urlencode($text);
        file_get_contents($telegramUrl);
    }

    public function run(): void
    {
        while (true) {
            print_r("Next loop at: " . date("Y-m-d H:i:s") . "\n");
            $response = $this->makeRequest();

            if ($response && isset($response['data']['directions']['forward']['trains'])) {
                foreach ($response['data']['directions']['forward']['trains'] as $train) {
                    $text = "RAILWAY\n\n";
                    $isExists = false;
                    foreach ($train['cars'] as $car) {
                        foreach ($car['tariffs'] as $tariff) {
                            if ($tariff['tariff'] <= $this->maxCost) {
                                $isExists = true;
                                $text .= $car['freeSeats'] . " ta joy | " . $train['type']  . ' - ' . $train['number'] . ' - ' . $train['departureDate'] . "\n";
                            }
                        }
                    }
                    if ($isExists) {
                        $this->sendMessage($text);
                    }
                }
            } else {
                error_log("Failed to retrieve or parse train data.");
            }
            sleep($this->interval);
        }
    }
}
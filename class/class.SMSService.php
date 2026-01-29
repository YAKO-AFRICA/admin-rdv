<?php


class SMSService
{

    var $details = null;

    var $lib_activer  = null;
    var $color_activer = null;

    var $lib_typ_membre  = null;

    var $clientId = 'xjxWRml44RnoZ5dvMFIfQl3e18rGA7tv';
    var $clientSecret = 'wQkC6YbNdM5AMXig';
    var $sender = '225123456789';
    var $tokenUrl = 'https://api.orange.com/oauth/v3/token';
    var $smsUrl = 'https://api.orange.com/smsmessaging/v1/outbound';




    // Informations de configuration
    // Fonction pour obtenir le token d'accès Orange
    private  function getAccessToken()
    {
        try {
            $headers = [
                'Authorization: Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
            ];

            $postFields = [
                'grant_type' => 'client_credentials',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->tokenUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));

            $response = curl_exec($ch);
            curl_close($ch);

            if ($response === false) {
                throw new Exception('Erreur lors de la récupération du token : ' . curl_error($ch));
            }

            $data = json_decode($response, true);
            if (isset($data['access_token'])) {
                return $data['access_token'];
            } else {
                throw new Exception('Token non trouvé dans la réponse.');
            }
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }


    // Fonction pour envoyer un OTP via Infobip
    public function sendOtpInfobip($phoneNumber, $message, $from = "YAKO AFRICA")
    {
        //echo PHP_EOL." SMS a " . $phoneNumber . " " . $message . PHP_EOL;
        $url = "https://wp2e3q.api.infobip.com/sms/2/text/advanced";
        $apiKey = "ca9b1e97d87d27dc425b2d598aa83c46-cbbd83f5-f0af-49ae-9bc0-02ba090ecac3";

        $headers = [
            'Authorization: App ' . $apiKey,
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        $body = [
            "messages" => [
                [
                    "from" => $from,
                    "destinations" => [["to" => $phoneNumber]],
                    "text" => "$message"
                ]
            ]
        ];

        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_POST, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        // $response = curl_exec($ch);
        // curl_close($ch);

        // @file_put_contents(Config::LogDirectory . 'sms-notif-ynov.log', date('Y-m-d H:i:s') . '|' . $phoneNumber . '|' . $from . '| ' . $message . '| result = ' . json_encode($response) . ': ' . PHP_EOL, FILE_APPEND);

        // if ($response === false) {
        //     return ['error' => 'Erreur lors de l\'envoi du SMS via Infobip : ' . curl_error($ch)];
        // }



        // return json_decode($response, true);
    }

    // Fonction pour envoyer un OTP via l'API Orange
    public function sendOtpOrangeAPI($phoneNumber, $message, $sender = '0000')
    {
        // Obtenir le token d'accès
        $accessToken = $this->getAccessToken($this->clientId, $this->clientSecret, $this->tokenUrl);

        if (isset($accessToken['error'])) {
            return $accessToken; // Retourner l'erreur
        }

        $smsUrl = "{$this->smsUrl}/tel%3A%2B" . urlencode($sender) . "/requests";

        $body = [
            'outboundSMSMessageRequest' => [
                'address' => "tel:+{$phoneNumber}",
                'senderAddress' => "tel:+{$sender}",
                'outboundSMSTextMessage' => [
                    'message' => $message,
                ],
            ],
        ];

        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $smsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

        $response = curl_exec($ch);
        curl_close($ch);

        @file_put_contents(Config::LogDirectory . 'sms-notif-ynov.log', date('Y-m-d H:i:s') . '|' . $phoneNumber . '|' . $sender . '| ' . $message . '| result = ' . json_encode($response) . ': ' . PHP_EOL, FILE_APPEND);

        if ($response === false) {
            return ['error' => 'Erreur lors de l\'envoi du SMS via Orange API : ' . curl_error($ch)];
        }



        return json_decode($response, true);
    }
}



/*

// Exemple d'appel des fonctions
$phoneNumber = '2250758407197';
$otp = '123456';

// Envoyer via Infobip
$infobipResponse = sendOtpInfobip($phoneNumber, $otp);
print_r($infobipResponse);

// Envoyer via l'API Orange
$orangeResponse = sendOtpOrangeAPI($phoneNumber, $otp, $sender, $tokenUrl, $smsUrl);
print_r($orangeResponse);

*/
<?php
/*

$to = 'bogavanessa@gmail.com'; // L'adresse du destinataire
$subject = 'Sujet de l\'email'; // Sujet de l'email
$message = 'Ceci est le corps de l\'email.'; // Contenu de l'email
$headers = 'From: expediteur@example.com' . "\r\n" .
    'Reply-To: expediteur@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion(); // En-têtes pour spécifier l'expéditeur

// Envoi de l'email
if (mail($to, $subject, $message, $headers)) {
    echo "Email envoyé avec succès.";
} else {
    echo "Échec de l'envoi de l'email.";
}
*/

/*$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://wp2e3q.api.infobip.com/whatsapp/1/message/location',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>'{"from":"441134960000","to":"2250758407197","messageId":"a28dd97c-1ffb-4fcf-99f1-0b557ed381da",
    "content":{"latitude":44.9526862,"longitude":13.8545217},"callbackData":"Callback data","notifyUrl":"https://www.example.com/whatsapp"}',
    CURLOPT_HTTPHEADER => array(
        'Authorization: {authorization}',
        'Content-Type: application/json',
        'Accept: application/json'
    ),
));

3f0ea06f5c6d158efd851298007decce-23a07fa5-2725-4818-834f-065c1e26bd44

$response = curl_exec($curl);

curl_close($curl);
echo $response;*/




// URL de l'API Infobip pour l'envoi de SMS (advanced endpoint)
$url = "https://wp2e3q.api.infobip.com/sms/2/text/advanced";

// Clé API Infobip
$cleApi = "3f0ea06f5c6d158efd851298007decce-23a07fa5-2725-4818-834f-065c1e26bd44";  // Remplace cette clé par la tienne

// En-têtes HTTP pour l'authentification et le type de contenu
$headers = [
    'Authorization' => "App $cleApi",
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
];

// Corps de la requête avec les informations du message
$body = [
    "messages" => [
        [
            "from" => "YAKO AFRICA",  // Expéditeur (peut être un numéro ou un texte comme "YAKO AFRICA")
            "destinations" => [
                ["to" => "+2250758407197"],  // Numéro du destinataire (au format international)
            ],
            "text" => "BONJOUR, ceci est un test de l'API Infobip."  // Contenu du message
        ]
    ]
];

try {
    // Initialisation de cURL
    $ch = curl_init();

    // Configuration de la requête cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
    curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 2);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));  // Envoi du corps en JSON
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 60000);  // Timeout de la requête (en millisecondes)
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  // Entêtes pour l'authentification et le contenu JSON

    // Exécution de la requête
    $data = curl_exec($ch);

    // Vérification des erreurs cURL
    if ($data === false) {
        echo 'Erreur cURL : ' . curl_error($ch);
    } else {
        // Décodage de la réponse JSON de l'API
        $data1 = json_decode($data, true);  // On utilise 'true' pour obtenir un tableau associatif

        // Vérification du statut de la réponse
        if (isset($data1['messages'][0]['status']['groupName']) && $data1['messages'][0]['status']['groupName'] == 'Sent') {
            echo "Le message a été envoyé avec succès !\n";
        } else {
            echo "Erreur d'envoi : " . print_r($data1, true);
        }
    }

    // Fermeture de la session cURL
    curl_close($ch);
} catch (Exception $e) {
    // Gestion des exceptions
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
/**/

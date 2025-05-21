<?php
require '../vendor/autoload.php';
header('Content-Type: application/json');

\Stripe\Stripe::setApiKey('sk_stripe'); // Reemplaza con tu clave secreta

$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? 'Cliente desconocido';

// Define el monto (puede venir del frontend también si quieres)
$amount = 5000; // S/50.00

try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'pen',
        'payment_method_types' => ['card'],
        'description' => 'Pago desde formulario personalizado',
        'metadata' => [
            'cliente' => $name
        ]
    ]);

    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

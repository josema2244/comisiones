<?php
// Punto de entrada de la API. Se ejecuta con:
//   php -S localhost:8000 backend/index.php
header('Content-Type: application/json; charset=utf-8');

require __DIR__ . '/db.php';
require __DIR__ . '/comisiones.php';

function responder($datos, $codigo = 200)
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    exit;
}

function fechaValida($texto)
{
    $partes = explode('-', (string) $texto);

    return count($partes) === 3
        && checkdate((int) $partes[1], (int) $partes[2], (int) $partes[0])
        && preg_match('/^\d{4}-\d{2}-\d{2}$/', $texto) === 1;
}

$ruta   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodo = $_SERVER['REQUEST_METHOD'];

try {
    // GET /api/comisiones?fechaInicio=2026-09-01&fechaFin=2026-09-30
    if ($ruta === '/api/comisiones' && $metodo === 'GET') {
        $inicio = $_GET['fechaInicio'] ?? '';
        $fin    = $_GET['fechaFin'] ?? '';

        if (!fechaValida($inicio) || !fechaValida($fin)) {
            responder(['error' => 'Fechas inválidas. Usa el formato AAAA-MM-DD'], 422);
        }

        if ($inicio > $fin) {
            responder(['error' => 'La fecha de inicio no puede ser mayor que la fecha final'], 422);
        }

        responder(calcularComisiones($inicio, $fin));
    }

    // GET /api/reglas
    if ($ruta === '/api/reglas' && $metodo === 'GET') {
        $reglas = consultar('SELECT Porcentaje, MontoMinimo FROM Regla ORDER BY MontoMinimo');

        responder(array_map(fn($r) => [
            'porcentaje'  => (float) $r['Porcentaje'],
            'montoMinimo' => (float) $r['MontoMinimo'],
        ], $reglas));
    }

    responder(['error' => 'Ruta no encontrada'], 404);
} catch (Exception $e) {
    responder(['error' => $e->getMessage()], 500);
}
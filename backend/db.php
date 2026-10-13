<?php
const SERVIDOR = 'MSI';
const BASE     = 'comisiones';

function conectar()
{
    static $conexion = null;

    if ($conexion === null) {
        $archivo = getenv('DB_FILE');

        if ($archivo) {
            $conexion = new PDO('sqlite:' . $archivo);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } else {
            $conexion = sqlsrv_connect(SERVIDOR, [
                'Database'               => BASE,
                'CharacterSet'           => 'UTF-8',
                'TrustServerCertificate' => true,
            ]);

            if ($conexion === false) {
                throw new Exception('No se pudo conectar a SQL Server');
            }
        }
    }

    return $conexion;
}

function consultar($sql, $parametros = [])
{
    $conexion = conectar();

    if ($conexion instanceof PDO) {
        $consulta = $conexion->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    $consulta = sqlsrv_query($conexion, $sql, $parametros);

    if ($consulta === false) {
        throw new Exception('Error al ejecutar la consulta');
    }

    $filas = [];
    while ($fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
        $filas[] = $fila;
    }

    return $filas;
}
<?php
$archivo = $argv[1] ?? (__DIR__ . '/comisiones.db');

$carpeta = dirname($archivo);
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0755, true);
}

if (file_exists($archivo)) {
    unlink($archivo);
}

$db = new PDO('sqlite:' . $archivo);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$db->exec("
    CREATE TABLE Regla (
        Id          INTEGER PRIMARY KEY AUTOINCREMENT,
        Porcentaje  REAL NOT NULL,
        MontoMinimo REAL NOT NULL
    );

    CREATE TABLE Vendedor (
        Id     INTEGER PRIMARY KEY AUTOINCREMENT,
        Nombre TEXT NOT NULL
    );

    CREATE TABLE Venta (
        Id         INTEGER PRIMARY KEY AUTOINCREMENT,
        VendedorId INTEGER NOT NULL,
        FechaVenta TEXT NOT NULL,
        Monto      REAL NOT NULL,
        FOREIGN KEY (VendedorId) REFERENCES Vendedor(Id)
    );

    INSERT INTO Regla (Porcentaje, MontoMinimo) VALUES
    (6, 0), (8, 500), (10, 800), (15, 1000);

    INSERT INTO Vendedor (Nombre) VALUES
    ('Ana Pérez'), ('Luis Gómez'), ('María Torres'), ('Carlos Ruiz');

    INSERT INTO Venta (VendedorId, FechaVenta, Monto) VALUES
    (1, '2026-09-03', 200), (1, '2026-09-18', 250),
    (2, '2026-09-05', 300), (2, '2026-09-21', 350),
    (3, '2026-09-08', 400), (3, '2026-09-25', 500),
    (4, '2026-09-10', 600), (4, '2026-09-30', 700),
    (1, '2026-08-15', 300), (2, '2026-08-20', 100),
    (3, '2026-08-10', 1000), (4, '2026-08-05', 50);
");

echo "Base SQLite creada en $archivo\n";
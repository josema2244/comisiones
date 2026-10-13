# Cálculo de Comisiones

Aplicación web que calcula la comisión de cada vendedor según lo que vendió en un rango de fechas. Es una tarea de Ingeniería Web (UDLA).

Versión publicada: https://comisiones-jmww.onrender.com

La página gratuita se duerme cuando nadie la usa, así que la primera carga puede tardar cerca de un minuto.

## Índice

- [Descripción](#descripción)
- [Reglas de comisión](#reglas-de-comisión)
- [Tecnologías](#tecnologías)
- [Cómo ejecutarlo en mi computadora](#cómo-ejecutarlo-en-mi-computadora)
- [Versión publicada](#versión-publicada)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Autor](#autor)

## Descripción

El usuario elige una fecha de inicio y una fecha final. El sistema suma las ventas de cada vendedor en ese rango, busca la regla de comisión que le corresponde según el total y calcula cuánto debe cobrar. El resultado se muestra en una tabla con el nombre del vendedor, el total vendido, el porcentaje y la comisión, y al final el total de comisiones.

## Reglas de comisión

El porcentaje depende del total vendido en el rango:

| Total vendido desde | Porcentaje |
|---|---|
| $0 | 6% |
| $500 | 8% |
| $800 | 10% |
| $1000 | 15% |

Por ejemplo, un vendedor que suma $650 está en el tramo de $500 y recibe el 8%, es decir $52.

## Tecnologías

- Angular 19 para la pantalla
- PHP 8 para la API
- SQL Server (SSMS) como base de datos en mi computadora
- SQLite como base de datos en la versión publicada
- Docker y Render para publicarlo


## Versión publicada

La versión de Render usa un Dockerfile que compila Angular y lo sirve junto con la API de PHP en el mismo contenedor.

Como la aplicación solo lee datos, en esa versión usé SQLite en lugar de SQL Server. El archivo se crea al construir la imagen con el script `backend/crear_sqlite.php`, que tiene los mismos datos que `database.sql`. El mismo código de `backend/db.php` elige la base según el entorno: usa SQLite si existe la variable `DB_FILE` y SQL Server si no existe.

## Estructura del proyecto

```
comisiones/
├── backend/
│   ├── index.php          rutas de la API
│   ├── comisiones.php     cálculo de las comisiones
│   ├── db.php             conexión a la base de datos
│   └── crear_sqlite.php   crea la base SQLite para la versión publicada
├── frontend/              aplicación Angular
├── database.sql           script de SQL Server
├── Dockerfile
└── README.md
```

La API tiene dos rutas:

- `GET /api/comisiones?fechaInicio=2026-09-01&fechaFin=2026-09-30` devuelve las comisiones.
- `GET /api/reglas` devuelve las reglas de comisión.

## Autor

José Jáuregui, estudiante de Ingeniería de Software en la Universidad de las Américas.
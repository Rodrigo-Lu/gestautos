<?php

/**
 * Parametros del negocio. Se editan aca y no dentro del codigo.
 */
return [
    'negocio' => [
        'nombre'    => env('GESTAUTOS_NOMBRE', 'JP Automotores'),
        'ruc'       => env('GESTAUTOS_RUC', '80012345-6'),
        'direccion' => env('GESTAUTOS_DIRECCION', 'San Lorenzo, Departamento Central, Paraguay'),
        'telefono'  => env('GESTAUTOS_TELEFONO', '(021) 000-000'),
        'email'     => env('GESTAUTOS_EMAIL', 'contacto@jpautomotores.com.py'),
    ],

    'financiamiento' => [
        // Tasa de interes mensual por defecto en el formulario de venta.
        'tasa_mensual_default'   => (float) env('GESTAUTOS_TASA_MENSUAL', 3.5),
        // Mora diaria sobre el saldo de la cuota atrasada.
        'tasa_mora_diaria'       => (float) env('GESTAUTOS_TASA_MORA', 0.15),
        // Plazos que ofrece la casa.
        'plazos'                 => [6, 12, 18, 24, 36, 48],
        // Anticipo minimo sugerido, en porcentaje del precio.
        'anticipo_minimo_pct'    => 20,
    ],

    'alertas' => [
        // Cuantos dias antes del vencimiento se avisa.
        'dias_previo_aviso' => (int) env('GESTAUTOS_DIAS_AVISO', 5),
    ],

    'catalogo' => [
        'por_pagina'      => 12,
        'fotos_por_vehiculo' => 10,
    ],
];

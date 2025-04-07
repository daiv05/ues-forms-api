@props(['slot'])
<!-- resources/views/components/container.blade.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} - UES</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header img {
            height: 80px;
            /* Ajustar según el tamaño que desees */
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin: 0 0 20px;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.5;
        }

        a {
            color: #771D1D;
            text-decoration: none;
        }

        .text-red-ues {
            color: #771D1D;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            /* Sombra para el cuadro */
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            color: #d32f2f;
            /* Rojo de la UES */
            border-bottom: 2px solid #ddd;
            /* Línea abajo de los encabezados */
            text-transform: uppercase;
            /* Todo en mayúsculas para mayor formalidad */
        }

        td {
            font-weight: normal;
            color: #555;
            border-bottom: 1px solid #ddd;
            /* Línea abajo de las celdas */
        }

        td:first-child {
            font-weight: bold;
            /* Negrita solo para las celdas de la izquierda */
        }

        /* Fila alternada para mayor legibilidad */
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: #555;
        }

        .footer a {
            color: #d32f2f;
            /* Rojo de la UES */
            margin: 0 10px;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .container-estado {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .estado-badge {
            border-radius: 9999px;
            width: auto;
            padding-left: 16px;
            padding-right: 16px;
            padding-top: 6px;
            padding-bottom: 6px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 0.875rem;
        }

        .color-completado {
            background-color: #3F6212;
            color: #fbfff6;
        }

        .color-finalizado {
            background-color: #d1fae5;
            color: #065f46;
        }

        .color-incompleto {
            background-color: #ffedd5;
            color: #9a3412;
        }

        .estado-text {
            font-weight: 500;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-primary {
            background-color: #9a3412;
            border: none;
            border-radius: 4px;
            color: white !important;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
        }

        .flex-div {
            display: flex;
            justify-content: center;
            justify-self: center;
            justify-items: center;
        }
    </style>
</head>

<body>
    {{ $slot }}
</body>

</html>

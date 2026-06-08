<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->serial_number }}</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        svg {
            max-width: 100vw;
            max-height: 100vh;
            width: 100%;
            height: auto;
            aspect-ratio: 1120/792;
            display: block;
        }
        @media print {
            @page {
                size: landscape;
                margin: 0;
            }
            body, html {
                width: 100%;
                height: 100%;
            }
            svg {
                width: 100%;
                height: 100%;
                max-width: none;
                max-height: none;
            }
        }
    </style>
</head>
<body>
    {!! $svgContent !!}

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Give it a tiny delay to ensure fonts and SVG render properly
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>

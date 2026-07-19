<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Print Semua Barcode Produk</title>

    <style>

        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f3f3;
        }

        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;

            padding: 12px 20px;

            border: none;
            border-radius: 8px;

            background: #292522;
            color: white;

            font-weight: bold;

            cursor: pointer;

            z-index: 999;
        }

        .sheet {
            width: 190mm;
            min-height: 277mm;

            margin: 20px auto;

            padding: 3mm;

            background: white;

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            grid-template-rows: repeat(8, 1fr);

            gap: 3mm;

            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: auto;
        }

        .barcode-item {
            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 3mm;

            overflow: hidden;

            border: 1px dashed #999;
        }

        .product-name {
            max-width: 100%;

            margin-bottom: 2mm;

            font-size: 10px;

            font-weight: bold;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }

        .sku {
            margin-bottom: 2mm;

            font-size: 8px;

            color: #555;
        }

        .barcode {
            width: 100%;

            max-width: 48mm;

            height: 15mm;
        }

        .barcode-number {
            margin-top: 1mm;

            font-size: 8px;

            letter-spacing: 1px;
        }

        @media print {

            body {
                background: white;
            }

            .print-button {
                display: none;
            }

            .sheet {
                margin: 0;
            }

        }

    </style>

</head>

<body>

    <button
        class="print-button"
        onclick="window.print()"
    >
        Cetak Semua Barcode
    </button>


    @foreach ($products->chunk(24) as $chunk)

        <div class="sheet">

            @foreach ($chunk as $item)

                <div class="barcode-item">

                    <div class="product-name">
                        {{ $item['product']->name }}
                    </div>

                    <div class="sku">
                        SKU: {{ $item['product']->sku }}
                    </div>

                    <div class="barcode">
                        {!! $item['barcode'] !!}
                    </div>

                    <div class="barcode-number">
                        {{ $item['barcode_number'] }}
                    </div>

                </div>

            @endforeach

        </div>

    @endforeach

</body>

</html>
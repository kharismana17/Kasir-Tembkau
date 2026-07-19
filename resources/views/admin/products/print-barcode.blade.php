<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Print Barcode Produk</title>

    <style>

        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f3f3;
        }


        /* =====================================================
           BUTTON
        ===================================================== */

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


        /* =====================================================
           A4 SHEET
        ===================================================== */

        .sheet {

            width: 194mm;
            height: 281mm;

            margin: 20px auto;

            padding: 2mm;

            background: white;

            display: grid;

            grid-template-columns: repeat(3, 1fr);

            grid-template-rows: repeat(8, 1fr);

            gap: 2mm;

            page-break-after: always;
        }

        .sheet:last-child {
            page-break-after: auto;
        }


        /* =====================================================
           BARCODE ITEM
        ===================================================== */

        .barcode-item {

            min-width: 0;
            min-height: 0;

            border: 1px dashed #999;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            text-align: center;

            padding: 2mm;

            overflow: hidden;
        }


        /* =====================================================
           PRODUCT NAME
        ===================================================== */

        .product-name {

            width: 100%;

            font-size: 9px;

            font-weight: bold;

            line-height: 1.2;

            margin-bottom: 1mm;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;
        }


        /* =====================================================
           SKU
        ===================================================== */

        .sku {

            font-size: 7px;

            color: #555;

            margin-bottom: 1mm;
        }


        /* =====================================================
           BARCODE
        ===================================================== */

        .barcode {
            width: 48mm;
            height: 13mm;

            display: flex;

            align-items: center;
            justify-content: center;

            overflow: hidden;
        }

        .barcode svg {
            width: 100%;
            height: 100%;
        }


        /* =====================================================
           BARCODE NUMBER
        ===================================================== */

        .barcode-number {

            font-size: 7px;

            letter-spacing: 0.8px;

            margin-top: 1mm;
        }


        /* =====================================================
           PRINT
        ===================================================== */

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
        type="button"
        class="print-button"
        onclick="window.print()"
    >
        Cetak Barcode
    </button>


    @foreach ($products->chunk(24) as $chunk)

        <div class="sheet">

            @foreach ($chunk as $item)

                @php
                    $product = $item['product'];
                    $barcode = $item['barcode'];
                @endphp


                <div class="barcode-item">


                    <div class="product-name">

                        {{ $product->name }}

                    </div>


                    <div class="sku">

                        SKU: {{ $product->sku }}

                    </div>


                    <div class="barcode">
                        {!! $barcode !!}
                    </div>


                    <div class="barcode-number">

                        {{ $product->barcode }}

                    </div>


                </div>

            @endforeach

        </div>

    @endforeach


</body>

</html>
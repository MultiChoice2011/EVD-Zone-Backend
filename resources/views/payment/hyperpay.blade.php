<!DOCTYPE html>
<html>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<head>
    <title>Order Gift Done</title>
    <style>
        body {background-color:#f6f6f5;}
        /* default value, applies to all devices */
        .input,
        .button {
            height: 44px;
            width: 100%;
        }
        @media (min-width: 480px) {
            /* this rule applies only to devices with a minimum screen width of 480px */
            .button {
                width: 50%;
            }
        }
    </style>
    <script src="{{ config('services.hyperpay.base_url') }}v1/paymentWidgets.js?checkoutId={{ $id }}"></script>
    <script type="text/javascript">
        var wpwlOptions = {
          paymentTarget: "_top",
          style:"card"
        }
    </script>
</head>

<body>
    <div style="height: 150px"></div>
    <form action="{{ route($routeName, ['entityId' => $entityId, 'orderId' => $orderId]) }}" class="paymentWidgets" data-brands="{{ $paymentBrand }}" method="POST"></form>

    <script type="text/javascript">
        var wpwlOptions = {
          paymentTarget: "_top",
          style:"card"
        }
    </script>

</body>

</html>

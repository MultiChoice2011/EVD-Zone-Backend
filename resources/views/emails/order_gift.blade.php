<!DOCTYPE html>
<html>

<head>
    <title>Order Gift Done</title>
</head>

<body>
    <p>From: {{ $customerData['name'] }}</p>
    <br/>
    <p>To: {{ $orderGift['recipient_name'] }}</p>
    <br/>
    <p>Desc: {{ $orderGift['description'] ?? 'N/A' }}</p>
    <br/>
    <p>
        {{--  <img src="{{ $message->embed($orderGift['image']) }}" alt="test">  --}}
        <img src="{{ $orderGift['image'] ?? null }}" alt="test">
    </p>
    <br/>
    <p>Your Serials</p>
    <ul>
        @foreach ($productSerials as $productSerial)
            <li>
                <strong>Serial:</strong> {{ $productSerial['serial'] }} <br>
                <strong>Scratching:</strong> {{ $productSerial['scratching'] }}
            </li>
            <br/>
        @endforeach
    </ul>
</body>

</html>

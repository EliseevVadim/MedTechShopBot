<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Mail</title>
</head>
<body>
    <h1 style="text-align: center">{{$details['title']}}</h1>
    <p>
        <b>{{$details['date']}} </b>был осуществлен следующий заказ:
    </p>
    <p>
        <b>Получатель: </b>{{$details['customers_name']}}
    </p>
    <p>
        <b>Товар: </b>{{$details['service_name']}}
    </p>
    <p>
        <b>Цена за единицу товара: </b>{{$details['price']}}
    </p>
    <p>
        <b>Скидка: </b>{{$details['discount']}}
    </p>
    <p>
        <b>Число единиц товара: </b>{{$details['quantity']}}
    </p>
    <p>
        <b>Итоговая стоимость: </b>{{$details['sum']}}
    </p>
    <p>
        <b>Email заказчика: </b> {{$details['user_mail']}}
    </p>
</body>
</html>

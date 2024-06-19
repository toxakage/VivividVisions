<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Light Up | Каталог</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="assets/scripts/jquery.js" type="text/javascript"></script>
    <script src="https://api-maps.yandex.ru/2.1?apikey=f04f9759-ab7f-4efd-8a16-b0fc5fb613e1&lang=ru_RU"
        type="text/javascript"> </script>
    <script src="assets/scripts/script.js" type="text/javascript"></script>
</head>

<?php
require_once __DIR__ . '/assets/php/boot.php';

$user = null;

$stmt = pdo()->prepare("SELECT * FROM `orders`;");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = pdo()->prepare("SELECT * FROM `liveinfo`;");
$stmt->execute();
$liveinfo = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = pdo()->prepare("SELECT * FROM `products`;");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (check_auth()) {
    $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `login` = :login");
    $stmt->execute([':login' => $_SESSION['login']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = pdo()->prepare("SELECT * FROM `orders` WHERE `login` = :id;");
    $stmt->execute([':id' => $user['login']]);
    $selforders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>


<header class="header">
    <div class="container">
        <div class="header__inner">
            <a href="index.php" class="header__logo">VividVisions</a>

            <?php if ($user) { ?>

                <nav class="nav">
                    <a class="nav__link" href="index.php">Главная</a>
                    <a class="nav__link" href="#">Каталог</a>
                    <a class="nav__link" href="#" data-bs-toggle="modal" data-bs-target="#ordersModal">Корзина</a>
                    <div class="dropdown">
                        <a class="nav__link" href="#" id="ProfiledropdownMenuLink" data-bs-toggle="dropdown"
                            aria-expanded="false">Профиль</a>

                        <ul class="dropdown-menu" aria-labelledby="ProfiledropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><?= htmlspecialchars($user['login']) ?></a></li>
                            <?php if ($user['rank'] > 0) { ?>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminModal">Админ
                                        панель</a></li>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="#">Баланс: <?= htmlspecialchars($user['balance']) ?> BYN</a>
                                </li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#updateBalanceModal">Пополнить баланс</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                        data-bs-target="#SuccessOrdersModal">Мои заказы</a></li>
                            <?php } ?>
                            <li><a id="logoutForm" class="dropdown-item" role="button">Выйти</a></li>
                        </ul>
                    </div>
                </nav>

            <?php } else { ?>

                <header class="header">
                    <div class="container">
                        <div class="header__inner">
                            <a href="index.php" class="header__logo">VividVisions</a>

                            <nav class="nav">
                                <a class="nav__link" href="index.php">Главная</a>
                                <a class="nav__link" href="#">Каталог</a>
                                <a class="nav__link" href="#" data-bs-toggle="modal" data-bs-target="#authModal">Корзина</a>
                                <a class="nav__link" href="#" data-bs-toggle="modal" data-bs-target="#authModal">Войти</a>
                            </nav>
                        </div>
                    </div>
                </header>

            <?php } ?>
        </div>
    </div>
</header>

<body>
    <!-- Модальное окно -->
    <div class="modal fade" id="authModal" tabindex="-1" aria-labelledby="authModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="authModalLabel">Авторизация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <div id="login">
                        <form id="authform">
                            <fieldset>
                                <p><label for="email">Email:</label></p>
                                <p><input name="username" type="email" id="email" value="Email"
                                        onBlur="if(this.value=='')this.value='Email'"
                                        onFocus="if(this.value=='Email')this.value=''"></p>

                                <p><label for="password">Пароль:</label></p>
                                <p><input name="password" type="password" id="password" value="Пароль"
                                        onBlur="if(this.value=='')this.value='Пароль'"
                                        onFocus="if(this.value=='Пароль')this.value=''"></p>

                                <p><input type="submit" value="ВОЙТИ"></p>
                                <p><a class="toreg" href="#" data-bs-toggle="modal" data-bs-target="#regModal">У меня
                                        нет аккаунта</a></p>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Модальное окно -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">Заголовок</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <div class="post__header">
                        <p class="post__photo">
                            <img src="" alt="">
                        </p>
                    </div>
                    <div class="post__content">
                        <div class="post__text">
                        </div>
                    </div>
                    <div class="post__sum">
                        <span></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id='addorder' class="btn btn-primary" data-bs-target="#productModal" data-bs-toggle="modal"
                        data-bs-dismiss="modal">Добавить в корзину</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Модальное окно -->
    <div class="modal fade" id="regModal" tabindex="-1" aria-labelledby="regModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="regModalLabel">Регистрация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                </div>
                <div class="modal-body">
                    <div id="login">
                        <form id="regform">
                            <fieldset>
                                <p><label for="email">Email:</label></p>
                                <p><input type="email" id="email" name="username" value="Email"
                                        onBlur="if(this.value=='')this.value='Email'"
                                        onFocus="if(this.value=='Email')this.value=''"></p>

                                <p><label for="password">Пароль:</label></p>
                                <p><input type="password" id="password" name="password" value="Пароль"
                                        onBlur="if(this.value=='')this.value='Пароль'"
                                        onFocus="if(this.value=='Пароль')this.value=''"></p>
                                <p><label for="password2">Повторите пароль:</label></p>
                                <p><input type="password" id="password2" name="password2" value="Пароль"
                                        onBlur="if(this.value=='')this.value='Пароль'"
                                        onFocus="if(this.value=='Пароль')this.value=''"></p>

                                <p style="text-align: center;"><input type="submit" value="ЗАРЕГИСТРИРОВАТЬСЯ"></p>
                                <p><a class="toreg" href="#" data-bs-toggle="modal" data-bs-target="#authModal">Я уже
                                        зарегистрирован</a></p>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($user) { ?>
        <!-- Модальное окно -->
        <div class="modal fade" id="ordersModal" tabindex="-1" aria-labelledby="ordersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ordersModalLabel">Корзина</h5>
                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php

                        $count = 0;

                        foreach ($selforders as $selforder) {

                            if ($selforder['status'] == 'Неоплачен') {
                                $count++;
                                $login = $user['login'];
                                $orderid = $selforder['id'];
                                $id = $selforder['product'];
                                foreach ($products as $product) {
                                    if ($product['id'] == $selforder['product']) {
                                        $title = $product['title'];
                                    }
                                }
                                $price = $selforder['price'];

                                echo "<div class='orderBox' style='overflow:hidden; white-space: nowrap;'>
                                        <img style='display:inline-block' height='55px' width='55px' src='assets/img/product_$id.png' alt=''>
                                        <div style='display:inline-block; font-size: 15px;'>
                                            $title -
                                        </div>
                                        <div style='display:inline-block; font-size: 15px;'>
                                            <b> $price руб</b>
                                        </div>
                                        <button type='button' class='btn-close' data-bs-id='$orderid' data-bs-target='#ordersModal' data-bs-toggle='modal' data-bs-dismiss='modal'></button>
                                        </div>";
                            }
                        }
                        if ($count == 0) {
                            echo '<div class="footer__text" style="text-align: center;">В данный момент корзина пуста.</div>';
                        }
                        ?>
                    </div>
                    <?php if ($count > 0) { ?>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#submitOrdersModal">К
                                оплате</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- Модальное окно -->
        <div class="modal fade" id="submitOrdersModal" tabindex="-1" aria-labelledby="submitOrdersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="submitOrdersModalLabel">Оформление заказа</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <div id="login">
                            <form id="OrdersForm">
                                <fieldset>
                                    <p><label for="adres">Адрес:</label></p>
                                    <p><input type="text" id="adres" name="adres" value="Адрес"
                                            onBlur="if(this.value=='')this.value='Адрес'"
                                            onFocus="if(this.value=='Адрес')this.value=''"></p>
                                    <p><label for="number">Номер телефона:</label></p>
                                    <p><input type="tel" id="number" name="number" value="Номер телефона"
                                            onBlur="if(this.value=='')this.value='Номер телефона'"
                                            onFocus="if(this.value=='Номер телефона')this.value=''"></p>
                                    <br>
                                    <p style='font-size: 16px; text-align: center;'>Сумма к оформления заказа:
                                        <?php $money = 0;
                                        foreach ($selforders as $selforder) {
                                            if ($selforder['status'] == 'Неоплачен') {
                                                $money = $money + $selforder['price'];
                                            }
                                        }
                                        echo $money; ?>
                                        руб</p>
                                    <p style="text-align: center;"><input name='ordersSumbit' id="ordersSumbit"
                                            data-bs-balance="<?= htmlspecialchars($user['balance']) ?>"
                                            data-bs-login="<?= htmlspecialchars($user['login']) ?>"
                                            data-bs-orders="<?php foreach ($selforders as $selforder) {
                                                if ($selforder['status'] == 'Неоплачен') {
                                                    echo $selforder['id'] . ",";
                                                }
                                            } ?>"
                                            data-bs-money="<?= htmlspecialchars($money) ?>" type="submit" value="Оформить заказ">
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Модальное окно -->
        <div class="modal fade" id="updateBalanceModal" tabindex="-1" aria-labelledby="updateBalanceModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateBalanceModalLabel">Пополнить баланс</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <div id="login">
                            <form id="BalanceForm">
                                <fieldset style="text-align: center;">
                                    <p><label for="sum">Сумма к пополнению(BYN):</label></p>
                                    <p><input style="text-align:center; border: 1px solid #555;" type="number" id="sum"
                                            name="sum" value="Сумма" onBlur="if(this.value=='')this.value='Сумма'"
                                            onFocus="if(this.value=='Сумма')this.value=''"></p>
                                    <br>
                                    <p style="text-align: center;"><input name='balanceSumbit' id="balanceSumbit"
                                            data-bs-login="<?= htmlspecialchars($user['login']) ?>" type="submit"
                                            value="Пополнить"></p>
                                    <p style='font-size: 12px; text-align: center;'>* Тут должна быть подключена касса
                                        оплаты, представим, что здесь условно Free Касса *</p>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="SuccessOrdersModal" tabindex="-1" aria-labelledby="SuccessOrdersModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="SuccessOrdersModalLabel">Мои заказы</h5>
                        <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php

                        $count = 0;

                        foreach ($selforders as $selforder) {

                            if ($selforder['status'] != 'Неоплачен') {
                                $count++;
                                $id = $selforder['product'];
                                foreach ($products as $product) {
                                    if ($product['id'] == $selforder['product']) {
                                        $title = $product['title'];
                                    }
                                }
                                $status = $selforder['status'];

                                echo "<div class='orderBox' style='overflow:hidden; white-space: nowrap;'>
                                        <img style='display:inline-block' height='55px' width='55px' src='assets/img/product_$id.png' alt=''>
                                        <div style='display:inline-block; font-size: 15px;'>
                                            $title -
                                        </div>
                                        <div style='display:inline-block; font-size: 15px;'>
                                            <b> $status</b>
                                        </div>";
                                if ($selforder['status'] == 'Оплачен') {
                                    echo "<p style='font-size: 11px; margin-left: 5px;'>* Ожидайте звонок от модераторов для подтверждения и отправки товара.</p>";
                                } else {
                                    echo "<p style='font-size: 11px; margin-left: 5px;'>* Товар в ближайшее время будет отправлен вам почтой или курьером.</p>";
                                }
                                echo "</div>";
                            }
                        }
                        if ($count == 0) {
                            echo '<div class="footer__text" style="text-align: center;">В данный момент корзина пуста.</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($user['rank'] > 0) { ?>

            <!-- Модальное окно -->
            <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="adminModalLabel"></h5>
                            <button type="button" class="btn-close" data-bs-toggle="modal" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <?php

                            if (count($products) > 0) {
                                foreach ($products as $product) {

                                    $id = $product['id'];
                                    $title = $product['title'];
                                    $price = $product['price'];


                                    echo "<div class='orderBox' style='overflow:hidden; white-space: nowrap;'>
                                            <img style='display:inline-block' height='55px' width='55px' src='assets/img/product_$id.png' alt=''>
                                            <div style='display:inline-block; font-size: 15px;'>
                                                $title -
                                            </div>
                                            <div style='display:inline-block; font-size: 15px;'>
                                                <b> $price руб</b>
                                            </div>
                                            <button type='button' class='btn-close' data-bs-id='$id' data-bs-toggle='modal' data-bs-dismiss='modal'></button>
                                            </div>";
                                }
                            } else {
                                echo '<div class="footer__text" style="text-align: center;">в данный момент каталог товаров пуст.</div>';
                            }
                            ?>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">Добавить
                                товар</button>
                            <button class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#orderModerationModal">Модерация заказов</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Модальное окно -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Добавить товар</h5>
                            <button type="button" class="btn-close" aria-label="Закрыть" data-bs-toggle="modal"
                                data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="login">
                                <form id="AddProductForm">
                                    <fieldset style="text-align: center;">
                                        <p><label for="image">Изображение товара:</label></p>
                                        <p><input style="text-align:center; border: 1px solid #555;" type="file"
                                                accept="image/*" id="image" name="image"></p>
                                        <p><label for="title">Название товара:</label></p>
                                        <p><input style="text-align: center;" type="text" id="title" name="title"
                                                value="Название" onBlur="if(this.value=='')this.value='Название'"
                                                onFocus="if(this.value=='Название')this.value=''"></p>
                                        <p><label for="desc">Описание товара:</label></p>
                                        <p><input style="text-align: center;" type="text" id="desc" name="desc" value="Описание"
                                                onBlur="if(this.value=='')this.value='Описание'"
                                                onFocus="if(this.value=='Описание')this.value=''"></p>
                                        <p><label for="price">Цена товара(BYN):</label></p>
                                        <p><input style="text-align:center; border: 1px solid #555;" type="number" id="price"
                                                name="price" value="Цена" onBlur="if(this.value=='')this.value='Цена'"
                                                onFocus="if(this.value=='Цена')this.value=''"></p>
                                        <br>
                                        <p style="text-align: center;"><input name='balanceSumbit' id="addproductSumbit"
                                                type="submit" value="Подтвердить"></p>
                                        <p><a class="toreg" href="#" data-bs-toggle="modal"
                                                data-bs-target="#adminModal">Вернуться назад</a></p>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="orderModerationModal" tabindex="-1" aria-labelledby="orderModerationModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered ">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderModerationModalLabel">Модерация заказов</h5>
                            <button type="button" class="btn-close" data-bs-toggle="modal"
                                data-bs-target="#adminModal"></button>
                        </div>
                        <div class="modal-body">
                            <?php

                            $count = 0;

                            foreach ($orders as $order) {

                                if ($order['status'] != 'Неоплачен') {
                                    $count++;
                                    $id = $order['product'];
                                    $orderid = $order['id'];
                                    $status = $order['status'];
                                    $adres = $order['adress'];
                                    $phone = $order['phone'];
                                    $login = $order['login'];
                                    $price = $order['price'];
                                    foreach ($products as $product) {
                                        if ($product['id'] == $order['product']) {
                                            $title = $product['title'];
                                        }
                                    }

                                    if ($order['status'] == 'Оплачен') {
                                        echo "<div class='orderBox' style='overflow:hidden; white-space: nowrap;'>
                                                <img style='display:inline-block' height='55px' width='55px' src='assets/img/product_$id.png' alt=''>
                                                <div style='display:inline-block; font-size: 15px;'>
                                                    $title -
                                                </div>
                                                <div style='display:inline-block; font-size: 15px;'>
                                                    <b> $status</b>
                                                </div>
                                                <p style='font-size: 11px; margin-left: 5px;'>Адрес - $adres </p>
                                                <p style='font-size: 11px; margin-left: 5px;'>Мобильный телефон - $phone </p>
                                                <p>
                                                    <button type='button' class='btn btn-primary btn-sm' data-bs-id='$orderid' data-bs-login='$login'>Подтвердить</button>
                                                    <button type='button' class='btn btn-secondary btn-sm' data-bs-id='$orderid' data-bs-login='$login' data-bs-price='$price'>Отклонить</button>
                                                </p>
                                                </div>";
                                    } else {
                                        echo "<div class='orderBox' style='overflow:hidden; white-space: nowrap;'>
                                                <img style='display:inline-block' height='55px' width='55px' src='assets/img/product_$id.png' alt=''>
                                                <div style='display:inline-block; font-size: 15px;'>
                                                    $title -
                                                </div>
                                                <div style='display:inline-block; font-size: 15px;'>
                                                    <b> $status</b>
                                                </div>
                                                </div>";
                                    }
                                }
                            }
                            if ($count == 0) {
                                echo '<div class="footer__text" style="text-align: center;">В данный момент список заказов пуст.</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>

    <?php } ?>
    <section class="section">
        <div id="productcontainer" class="container">
            <div class="section__header">
                <h2 class="section__title">Каталог</h2>
            </div>
            <?php

            if (count($products) > 0) {
                foreach ($products as $product) {

                    $login = $user['login'];
                    $id = $product['id'];
                    $title = $product['title'];
                    $desc = $product['description'];
                    $price = $product['price'];

                    echo "<a class='jpost' href='#' data-bs-login='$login' data-bs-id='$id' data-bs-title='$title' data-bs-desc='$desc' data-bs-price='$price' data-bs-toggle='modal' data-bs-target='#productModal'>
                                <div class='post__item'>
                                    <div class='post__header'>
                                        <p class='post__photo'>
                                            <img style='width: 85%; height: 85%;' src='assets/img/product_$id.png' alt=''>
                                        </p>
                                    </div>
                                    <div class='post__content'>
                                        <div class='post__title'>
                                            $title
                                        </div>
                                        <div class='post__text'>
                                            $desc
                                        </div>
                                    </div>
                                    <div class='post__sum'>
                                        <span>$price руб</span>
                                    </div>
                                </div>
                            </a>";
                }
            } else {

                echo '<div class="footer__text">Приносим свои извинения. К сожалению, в данный момент каталог пуст.</div>';
            }
            ?>
        </div>
    </section>
</body>

</html>
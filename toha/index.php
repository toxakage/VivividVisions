<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VividVisions | Главная</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/scripts/jquery.js" type="text/javascript"></script>
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

$stmt = pdo()->prepare("SELECT * FROM `reviews`;");
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    <a class="nav__link" href="#">Главная</a>
                    <a class="nav__link" href="catalog.php">Каталог</a>
                    <a class="nav__link" href="#" data-bs-toggle="modal" data-bs-target="#ordersModal">Корзина</a>
                    <div class="dropdown">
                        <a class="nav__link" href="#" id="ProfiledropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">Профиль</a>

                        <ul class="dropdown-menu" aria-labelledby="ProfiledropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><?= htmlspecialchars($user['login']) ?></a></li>
                            <?php if ($user['rank'] > 0) { ?>
                                <li><a class="dropdown-item" href="admin.php">Админ панель</a></li>
                            <?php } else { ?>

                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#SuccessOrdersModal">Мои заказы</a></li>
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
                                <a class="nav__link" href="#">Главная</a>
                                <a class="nav__link" href="catalog.php">Каталог</a>
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
    <!-- Модальное окно -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered ">
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
                    <button <?php if($user) { echo "id='addorder' data-bs-target='#productModal' data-bs-toggle='modal'"; } else { echo "data-bs-toggle='modal' data-bs-target='#authModal'"; }?> class="btn btn-secondary">Добавить в корзину</button>
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

                            if ($selforder['status'] == 'Не заказан') {
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
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#submitOrdersModal">Оформить заказ</button>
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
                                    <p style='font-size: 16px; text-align: center;'>Сумма к офомлению заказа:
                                        <?php $money = 0;
                                        foreach ($selforders as $selforder) {
                                            if ($selforder['status'] == 'Не заказан') {
                                                $money = $money + $selforder['price'];
                                            }
                                        }
                                        echo $money; ?>
                                        руб</p>
                                    <p style="text-align: center;"><input name='ordersSumbit' id="ordersSumbit"
                                            data-bs-login="<?= htmlspecialchars($user['login']) ?>"
                                            data-bs-orders="<?php foreach ($selforders as $selforder) {
                                                if ($selforder['status'] == 'Не заказан') {
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
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reviewModalLabel">Отзывы</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        <div id="login">
                            <form id="reviewForm">
                                <fieldset style="text-align: center;">
                                    <p><label for="fio">Ваша фамилия и имя:</label></p>
                                    <p><input style="text-align: center;" type="text" id="fio" name="fio" value="Фамилия и имя"
                                            onBlur="if(this.value=='')this.value='Фамилия и имя'"
                                            onFocus="if(this.value=='Фамилия и имя')this.value=''"></p>
                                    <p><label for="txt">Текст отзыва:</label></p>
                                    <p><input style="text-align: center;" type="text" id="txt" name="txt" value="Текст"
                                            onBlur="if(this.value=='')this.value='Текст'"
                                            onFocus="if(this.value=='Текст')this.value=''"></p>
                                    <br>
                                    <p><label for="stars">Выберите кол-во звезд</label></p>
                                    <p>
                                        <select id="stars" name="stars" style="width: 180px; text-align:center; border: 1px solid #555;">
                                            <option disabled selected value='none'>Кол-во звезд</option>
                                            <option value='1'>&#9733;</option>
                                            <option value='2'>&#9733;&#9733;</option>
                                            <option value='3'>&#9733;&#9733;&#9733;</option>
                                            <option value='4'>&#9733;&#9733;&#9733;&#9733;</option>
                                            <option value='5'>&#9733;&#9733;&#9733;&#9733;&#9733;</option>
                                        </select>
                                    </p>
                                    <p style="text-align: center;"><input name='reviewSumbit' id="reviewSumbit" type="submit" value="Оставить отзыв">
                                    </p>
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

                            if ($selforder['status'] != 'Не заказан') {
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
                                if ($selforder['status'] == 'Заказан') {
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

    <?php } ?>
    <section class="section">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">Художественная галерея</h2>
                <div class="section__text">
                <p class="gallery-description">Добро пожаловать в нашу уютную художественную галерею, где каждая картина воплощает в себе частичку искусства и эмоций. Мы стремимся создать пространство, где вы сможете найти вдохновение для своего дома или офиса, обогатить его уникальными произведениями искусства.</p>
                <p class="gallery-offer">Наша коллекция включает в себя картины от известных польских и других европейских художников, которые привносят в ваш интерьер атмосферу красоты, гармонии и творчества. Независимо от вашего вкуса или предпочтений, у нас вы обязательно найдете что-то, что вас вдохновит и украсит ваше пространство.</p>
                <p class="online-order">Мы гордимся тем, что предлагаем нашим клиентам возможность заказать картину онлайн, чтобы каждый мог легко и удобно обогатить свой дом или рабочее место уникальным произведением искусства. Наша команда всегда готова помочь вам подобрать именно то, что подчеркнет ваш стиль и индивидуальность.</p>
            </div>
        </div>
        <div class="about">
            <div class="about__item">
                <div class="slider">
                    <div class="slides">
                        <div class="slide"><img src="assets/img/1.jpg" alt="Image 1"></div>
                        <div class="slide"><img src="assets/img/2.jpg" alt="Image 2"></div>
                        <div class="slide"><img src="assets/img/3.jpg" alt="Image 3"></div>
                    </div>
                    <button class="prev" onclick="moveSlides(-1)">&#10094;</button>
                    <button class="next" onclick="moveSlides(1)">&#10095;</button>
                </div>
            </div>
        </div>
    </section>

    <section class="section--catalog" id="catalog">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">Каталог</h2>
            </div>
            <?php if (count($products)>0) { ?>

                <div class="row">
                    <div class="col">
                        <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[0]['id'])?>' data-bs-title='<?=htmlspecialchars($products[0]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[0]['description'])?>' data-bs-price='<?=htmlspecialchars($products[0]['price'])?>'>
                            <div class="post__item">
                                <div class="post__header">
                                    <p class="post__photo">
                                        <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[0]['id'])?>.png" alt="">
                                    </p>
                                </div>
                                <div class="post__content">
                                    <div class="post__title">
                                        <?=htmlspecialchars($products[0]['title'])?>
                                    </div>
                                    <div class="post__text">
                                        <?=htmlspecialchars($products[0]['description'])?>
                                    </div>
                                </div>
                                <div class="post__sum">
                                    <span><?=htmlspecialchars($products[0]['price'])?> руб</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <?php if (count($products)>1) { ?>
                            <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[1]['id'])?>' data-bs-title='<?=htmlspecialchars($products[1]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[1]['description'])?>' data-bs-price='<?=htmlspecialchars($products[1]['price'])?>'>
                                <div class="post__item">
                                    <div class="post__header">
                                        <p class="post__photo">
                                            <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[1]['id'])?>.png" alt="">
                                        </p>
                                    </div>
                                    <div class="post__content">
                                        <div class="post__title">
                                            <?=htmlspecialchars($products[1]['title'])?>
                                        </div>
                                        <div class="post__text">
                                            <?=htmlspecialchars($products[1]['description'])?>
                                        </div>
                                    </div>
                                    <div class="post__sum">
                                        <span><?=htmlspecialchars($products[1]['price'])?> руб</span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?php } if (count($products)>2) { ?>

                <div class="row">
                    <div class="col">
                        <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[2]['id'])?>' data-bs-title='<?=htmlspecialchars($products[2]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[2]['description'])?>' data-bs-price='<?=htmlspecialchars($products[2]['price'])?>'>
                            <div class="post__item">
                                <div class="post__header">
                                    <p class="post__photo">
                                        <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[2]['id'])?>.png" alt="">
                                    </p>
                                </div>
                                <div class="post__content">
                                    <div class="post__title">
                                        <?=htmlspecialchars($products[2]['title'])?>
                                    </div>
                                    <div class="post__text">
                                        <?=htmlspecialchars($products[2]['description'])?>
                                    </div>
                                </div>
                                <div class="post__sum">
                                    <span><?=htmlspecialchars($products[2]['price'])?> руб</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <?php if (count($products)>3) { ?>
                            <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[3]['id'])?>' data-bs-title='<?=htmlspecialchars($products[3]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[3]['description'])?>' data-bs-price='<?=htmlspecialchars($products[3]['price'])?>'>
                                <div class="post__item">
                                    <div class="post__header">
                                        <p class="post__photo">
                                            <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[3]['id'])?>.png" alt="">
                                        </p>
                                    </div>
                                    <div class="post__content">
                                        <div class="post__title">
                                            <?=htmlspecialchars($products[3]['title'])?>
                                        </div>
                                        <div class="post__text">
                                            <?=htmlspecialchars($products[3]['description'])?>
                                        </div>
                                    </div>
                                    <div class="post__sum">
                                        <span><?=htmlspecialchars($products[3]['price'])?> руб</span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?php } if (count($products)>4) { ?>

                <div class="row">
                    <div class="col">
                        <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[4]['id'])?>' data-bs-title='<?=htmlspecialchars($products[4]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[4]['description'])?>' data-bs-price='<?=htmlspecialchars($products[4]['price'])?>'>
                            <div class="post__item">
                                <div class="post__header">
                                    <p class="post__photo">
                                        <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[4]['id'])?>.png" alt="">
                                    </p>
                                </div>
                                <div class="post__content">
                                    <div class="post__title">
                                        <?=htmlspecialchars($products[4]['title'])?>
                                    </div>
                                    <div class="post__text">
                                        <?=htmlspecialchars($products[4]['description'])?>
                                    </div>
                                </div>
                                <div class="post__sum">
                                    <span><?=htmlspecialchars($products[4]['price'])?> руб</span>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col">
                        <?php if (count($products)>5) { ?>
                            <a class="jpost" href="#" data-bs-toggle='modal' data-bs-target='#productModal' data-bs-login='<?=htmlspecialchars($user['login'])?>' data-bs-id='<?=htmlspecialchars($products[5]['id'])?>' data-bs-title='<?=htmlspecialchars($products[5]['title'])?>' data-bs-desc='<?=htmlspecialchars($products[5]['description'])?>' data-bs-price='<?=htmlspecialchars($products[5]['price'])?>'>
                                <div class="post__item">
                                    <div class="post__header">
                                        <p class="post__photo">
                                            <img style='display:inline-block' height='380px' width='450px' src="assets/img/product_<?=htmlspecialchars($products[5]['id'])?>.png" alt="">
                                        </p>
                                    </div>
                                    <div class="post__content">
                                        <div class="post__title">
                                            <?=htmlspecialchars($products[5]['title'])?>
                                        </div>
                                        <div class="post__text">
                                            <?=htmlspecialchars($products[5]['description'])?>
                                        </div>
                                    </div>
                                    <div class="post__sum">
                                        <span><?=htmlspecialchars($products[5]['price'])?> руб</span>
                                    </div>
                                </div>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?php } if(count($products)<=0) { ?>

                    <div class="footer__text">Приносим свои извинения. К сожалению, в данный момент каталог пуст.</div>

                <?php } if(count($products)>6) {?>
                    <a class="btncatalog" href="catalog.php">Посмотреть еще</a>
            <?php } ?>
        </div>
    </section>

    <section class="section--catalog" id="catalog">
        <div class="container">
            <div class="section__header">
                <h2 class="section__title">Отзывы</h2>
            </div>
            <?php 

                if(count($reviews)>0) {
                    foreach($reviews as $review) {
                        $name = $review['name'];
                        $date = $review['date'];
                        $stars = $review['stars'];
                        $text = $review['text'];

                        echo "<div class='rpost'>
                        <div class='row'>
                            <div class='col-3'> 
                                <div class='r_title'>
                                    $name
                                </div>
                                <div class='r_sum'>
                                    $date
                                </div>
                                <div class='r_stars'>
                                    <span>";
                        for ($i = 0; $i < $stars; $i++) { echo '&#9733;'; }
                        
                        echo "</span>
                                    </div>
                                </div>
                                <div class='col-9'>
                                    <div class='r_text'>
                                        $text
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                } else {
                    echo '<div class="footer__text">К сожалению, в данный момент отзывов нет. Вы можете оставить свой и он будет первым.</div>';
                }
            
            ?>
            <a class="btncatalog" style="margin-top: 15px;" <?php if($user) { echo "data-bs-toggle='modal' data-bs-target='#reviewModal'"; } else { echo "data-bs-toggle='modal' data-bs-target='#authModal'"; } ?> href="#">Оставить отзыв</a>
        </div>
    </section>

    <footer class="footer">
        <section class="section--about">
            <div class="container--about">
                <h2 class="section--about__title">О нас</h2>
                <p class="section--about__description">
                    Наша художественная галерея – это место, где искусство оживает. Мы представляем работы как известных художников, так и талантливых новичков. 
                    Наши выставки обновляются ежемесячно, предлагая посетителям уникальные впечатления каждый раз.
                </p>
                <p class="section--about__contact-info">

                    Телефон: +7 (123) 456-78-90<br>
                    Email: info@artgallery.com
                </p>
            </div>
        </section>
    </footer>

</body>

</html>
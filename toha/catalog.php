<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VividVisions | Каталог</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="assets/scripts/jquery.js" type="text/javascript"></script>
    <script src="assets/scripts/script.js" type="text/javascript"></script>
</head>

    <?php
        require_once __DIR__.'/assets/php/boot.php';

        $user = null;

        $stmt = pdo()->prepare("SELECT * FROM `orders`;");
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = pdo()->prepare("SELECT * FROM `products`;");
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        parse_str($_SERVER['QUERY_STRING'], $get);

        if(isset($get['search'])) {
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category AND (`title` LIKE :search OR `description` LIKE :search OR `price` LIKE :search);");
            $stmt->execute([':category' => 'picture', ':search' => '%' . $get['search'] . '%']);
            $pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category AND (`title` LIKE :search OR `description` LIKE :search OR `price` LIKE :search);");
            $stmt->execute([':category' => 'statue', ':search' => '%' . $get['search'] . '%']);
            $statues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif (isset($get['price'])) {
            $min = explode("-", $get['price'])[0];
            $max = explode("-", $get['price'])[1];
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category AND `price` >= :min AND `price` <= :max;");
            $stmt->execute([':category' => 'picture', ':min' => $min, ':max' => $max]);
            $pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category AND `price` >= :min AND `price` <= :max;");
            $stmt->execute([':category' => 'statue', ':min' => $min, ':max' => $max]);
            $statues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category;");
            $stmt->execute([':category' => 'picture']);
            $pictures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = pdo()->prepare("SELECT * FROM `products` WHERE `category` = :category;");
            $stmt->execute([':category' => 'statue']);
            $statues = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

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
                                    <p><input name="username" type="email" id="email" value="Email" onBlur="if(this.value=='')this.value='Email'" onFocus="if(this.value=='Email')this.value=''"></p>

                                    <p><label for="password">Пароль:</label></p>
                                    <p><input name="password" type="password" id="password" value="Пароль" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value=''"></p> 

                                    <p><input type="submit" value="ВОЙТИ"></p>
                                    <p><a class="toreg" href="#" data-bs-toggle="modal" data-bs-target="#regModal">У меня нет аккаунта</a></p>
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
                    <button <?php if($user) { echo "id='addorder' data-bs-target='#productModal' data-bs-toggle='modal'"; } else { echo "data-bs-toggle='modal' data-bs-target='#authModal'"; }?> class="btn btn-secondary">Добавить в корзину</button>
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
                                    <p><input type="email" id="email" name="username" value="Email" onBlur="if(this.value=='')this.value='Email'" onFocus="if(this.value=='Email')this.value=''"></p>

                                    <p><label for="password">Пароль:</label></p>
                                    <p><input type="password" id="password" name="password" value="Пароль" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value=''"></p> 
                                    <p><label for="password2">Повторите пароль:</label></p>
                                    <p><input type="password" id="password2" name="password2" value="Пароль" onBlur="if(this.value=='')this.value='Пароль'" onFocus="if(this.value=='Пароль')this.value=''"></p> 

                                    <p style="text-align: center;"><input type="submit" value="ЗАРЕГИСТРИРОВАТЬСЯ"></p>
                                    <p><a class="toreg" href="#" data-bs-toggle="modal" data-bs-target="#authModal">Я уже зарегистрирован</a></p>
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
            <div id="productcontainer" class="container">
                <div class="section__header">
                    <h2 class="section__title">Каталог</h2>
                    <div class="input-group mb-3" style="margin-left: auto; margin-right: auto; width: 400px;">
                        <input id='searchtext' type="text" class="form-control" placeholder="Введите запрос" aria-label="Введите запрос" aria-describedby="searchbtn">
                        <button class="btn btn-secondary" type="button" id="searchbtn">Поиск</button>
                    </div>
                    <?php if(isset($get['search'])) { ?>
                        <h5>Найдено по запросу - <?=htmlspecialchars($get['search'])?> <button id='searchdismiss' type='button' class='btn-close'></button></h5>
                    <?php } ?>
                </div>
                <p ><label for="min">Фильтр по цене(BYN):</label></p>
                <p>
                    <label for="min" style='margin-right: 10px;'>От</label><input style="text-align:center; border: 1px solid #555;" type="number" id="min" name="min" value="От" onBlur="if(this.value=='')this.value='От'" onFocus="if(this.value=='От')this.value=''">
                </p>
                <p>
                    <label for="max" style='margin-right: 10px;'>До</label><input style="text-align:center; border: 1px solid #555;" type="number" id="max" name="max" value="До" onBlur="if(this.value=='')this.value='До'" onFocus="if(this.value=='До')this.value=''">
                </p>
                <button type='button' style='text-align: start; margin-bottom: 30px;' class='btn btn-secondary btn-sm' id="filterbtn">Применить фильтр</button>
                <div сlass="btn-group btn-group-lg" role="group" aria-label="Large button group">
                    <input type="radio" class="btn-check" name="btnradio" id="picturebtn" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="picturebtn">Картины</label>

                    <input type="radio" class="btn-check" name="btnradio" id="statuebtn" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="statuebtn">Статуэтки</label>
                </div>
                <br>
                <div id='pictures' style='display: block;'>
                    <?php 

                        if(count($pictures)>0) {

                            for ($i = 0; $i < count($pictures); $i+=2) {

                                if($user) {
                                    $login = $user['login'];
                                }
                                $id = $pictures[$i]['id'];
                                $title = $pictures[$i]['title'];
                                $desc = $pictures[$i]['description'];
                                $price = $pictures[$i]['price'];

                                echo "<div class='row'>
                                    <div class='col'>
                                        <a class='jpost' href='#' data-bs-toggle='modal' data-bs-target='#productModal'";
                                if($user) {
                                    echo "data-bs-login='$login'"; 
                                }
                                echo "data-bs-id='$id' data-bs-title='$title' data-bs-desc='$desc' data-bs-price='$price'>
                                            <div class='post__item'>
                                                <div class='post__header'>
                                                    <p class='post__photo'>
                                                        <img style='display:inline-block' height='380px' width='450px' src='assets/img/product_$id.png' alt=''>
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
                                        </a>
                                    </div>";
                                if($i + 1 < count($pictures)) {
                                    $sid = $pictures[$i+1]['id'];
                                    $stitle = $pictures[$i+1]['title'];
                                    $sdesc = $pictures[$i+1]['description'];
                                    $sprice = $pictures[$i+1]['price'];

                                    echo "<div class='col'>
                                            <a class='jpost' href='#' data-bs-toggle='modal' data-bs-target='#productModal'";
                                    if($user) {
                                        echo "data-bs-login='$login'"; 
                                    }
                                    echo "data-bs-id='$sid' data-bs-title='$stitle' data-bs-desc='$sdesc' data-bs-price='$sprice'>
                                                <div class='post__item'>
                                                    <div class='post__header'>
                                                        <p class='post__photo'>
                                                            <img style='display:inline-block' height='380px' width='450px' src='assets/img/product_$sid.png' alt=''>
                                                        </p>
                                                    </div>
                                                    <div class='post__content'>
                                                        <div class='post__title'>
                                                            $stitle
                                                        </div>
                                                        <div class='post__text'>
                                                            $sdesc
                                                        </div>
                                                    </div>
                                                    <div class='post__sum'>
                                                        <span>$sprice руб</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>";

                                } else {
                                    echo "<div class='col'></div></div>";
                                }
                            }

                        } else {

                            echo '<div class="footer__text">Приносим свои извинения. К сожалению, в данный момент каталог пуст.</div>';
                        }
                    ?>
                </div>
                <div id='statues' style='display: none;'>
                    <?php 

                        if(count($statues)>0) {

                            for ($i = 0; $i < count($statues); $i+=2) {

                                if($user) {
                                    $login = $user['login'];
                                }
                                $id = $statues[$i]['id'];
                                $title = $statues[$i]['title'];
                                $desc = $statues[$i]['description'];
                                $price = $statues[$i]['price'];

                                echo "<div class='row'>
                                    <div class='col'>
                                        <a class='jpost' href='#' data-bs-toggle='modal' data-bs-target='#productModal'";
                                if($user) {
                                    echo "data-bs-login='$login'"; 
                                }
                                echo "data-bs-id='$id' data-bs-title='$title' data-bs-desc='$desc' data-bs-price='$price'>
                                            <div class='post__item'>
                                                <div class='post__header'>
                                                    <p class='post__photo'>
                                                        <img style='display:inline-block' height='380px' width='450px' src='assets/img/product_$id.png' alt=''>
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
                                        </a>
                                    </div>";
                                if($i + 1 < count($statues)) {
                                    $sid = $statues[$i+1]['id'];
                                    $stitle = $statues[$i+1]['title'];
                                    $sdesc = $statues[$i+1]['description'];
                                    $sprice = $statues[$i+1]['price'];

                                    echo "<div class='col'>
                                            <a class='jpost' href='#' data-bs-toggle='modal' data-bs-target='#productModal'";
                                    if($user) {
                                        echo "data-bs-login='$login'"; 
                                    }
                                    echo "data-bs-id='$sid' data-bs-title='$stitle' data-bs-desc='$sdesc' data-bs-price='$sprice'>
                                                <div class='post__item'>
                                                    <div class='post__header'>
                                                        <p class='post__photo'>
                                                            <img style='display:inline-block' height='380px' width='450px' src='assets/img/product_$sid.png' alt=''>
                                                        </p>
                                                    </div>
                                                    <div class='post__content'>
                                                        <div class='post__title'>
                                                            $stitle
                                                        </div>
                                                        <div class='post__text'>
                                                            $sdesc
                                                        </div>
                                                    </div>
                                                    <div class='post__sum'>
                                                        <span>$sprice руб</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>";

                                } else {
                                    echo "<div class='col'></div></div>";
                                }
                            }

                        } else {

                            echo '<div class="footer__text">Приносим свои извинения. К сожалению, в данный момент каталог пуст.</div>';
                        }
                    ?>
                </div>
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
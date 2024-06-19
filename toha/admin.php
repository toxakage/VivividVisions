<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="assets/css/style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>VividVisions | Админ-панель</title>
        <script src="assets/scripts/jquery.js" type="text/javascript"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="assets/scripts/admscripts.js" type="text/javascript"></script>
        <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/sweetalert2@7.12.15/dist/sweetalert2.min.css'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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

        $stmt = pdo()->prepare("SELECT * FROM `reviews`;");
        $stmt->execute();
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (check_auth()) {
            $stmt = pdo()->prepare("SELECT * FROM `users` WHERE `login` = :login");
            $stmt->execute([':login' => $_SESSION['login']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    ?>


    <header class="header">
        <div class="container">
        <div class="header__inner">
            <a href="index.php" class="header__logo">VividVisions</a>

            <?php if ($user) { ?>

                <nav class="nav">
                    <a class="nav__link" href="index.php">Главная</a>
                    <a class="nav__link" href="catalog.php">Каталог</a>
                    <a class="nav__link" href="index.php#products">Корзина</a>
                    <div class="dropdown">
                        <a class="nav__link" href="#" id="ProfiledropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">Профиль</a>

                        <ul class="dropdown-menu" aria-labelledby="ProfiledropdownMenuLink">
                            <li><a class="dropdown-item" href="#"><?= htmlspecialchars($user['login']) ?></a></li>
                            <?php if ($user['rank'] > 0) { ?>
                                <li><a class="dropdown-item" href="admin.php">Админ панель</a></li>
                            <?php } else { ?>
                                <li><a class="dropdown-item" href="index.php#orders">Мои заказы</a></li>
                            <?php } ?>
                            <li><a class="dropdown-item" href="index.php#logout">Выйти</a></li>
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
                                <a class="nav__link" href="catalog.php">Каталог</a>
                                <a class="nav__link" href="index.php#auth">Корзина</a>
                                <a class="nav__link" href="index.php#auth">Войти</a>
                            </nav>
                        </div>
                    </div>
                </header>

            <?php } ?>

        </div>
        </div>
    </header>

    <body>
        <?php if($user) { if($user['rank']>0) {?>
            <!-- Модальное окно -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addProductModalLabel">Добавить товар</h5>
                            <button type="button" class="btn-close" aria-label="Закрыть" data-bs-toggle="modal" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="login">
                                <form id="AddProductForm">
                                    <fieldset style="text-align: center;">
                                        <p><label for="image">Изображение товара:</label></p>
                                        <p><input style="text-align:center; border: 1px solid #555;" type="file" accept="image/*" id="image" name="image"></p>
                                        <p><label for="title">Название товара:</label></p>
                                        <p><input style="text-align: center;" type="text" id="title" name="title" value="Название" onBlur="if(this.value=='')this.value='Название'" onFocus="if(this.value=='Название')this.value=''"></p>
                                        <p><label for="desc">Описание товара:</label></p>
                                        <p><input style="text-align: center;" type="text" id="desc" name="desc" value="Описание" onBlur="if(this.value=='')this.value='Описание'" onFocus="if(this.value=='Описание')this.value=''"></p>
                                        <p><label for="price">Цена товара(BYN):</label></p>
                                        <p><input style="width: 180px; text-align:center; border: 1px solid #555;" type="number" id="price" name="price" value="Цена" onBlur="if(this.value=='')this.value='Цена'" onFocus="if(this.value=='Цена')this.value=''"></p>
                                        <p><label for="category">Категория товара</label></p>
                                        <p>
                                            <select id="category" name="category" style="width: 180px; text-align:center; border: 1px solid #555;">
                                                <option disabled selected value='none'>Выберите категорию</option>
                                                <option value='picture'>Картина</option>
                                                <option value='statue'>Статуэтка</option>
                                            </select>
                                        </p>
                                        <br>
                                        <p style="text-align: center;"><input name='balanceSumbit' id="addproductSumbit" type="submit" value="Подтвердить"></p>
                                        <p><a class="toreg" href="#" data-bs-toggle="modal" data-bs-target="#adminModal">Вернуться назад</a></p>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <section class="section" style='text-align: center;'>
                <div сlass="btn-group btn-group-lg" role="group" aria-label="Large button group">
                    <input type="radio" class="btn-check" name="btnradio" id="ordersbtn" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="ordersbtn">Заказы</label>

                    <input type="radio" class="btn-check" name="btnradio" id="productsbtn" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="productsbtn">Товары</label>

                    <input type="radio" class="btn-check" name="btnradio" id="reviewsbtn" autocomplete="off">
                    <label class="btn btn-outline-secondary" for="reviewsbtn">Отзывы</label>
                </div>
                <br>
                <div id='orders'>
                    <?php

                        $count = 0;

                        foreach($orders as $order) {

                            if($order['status']!='Не заказан') {
                                $count++;
                                $id = $order['product'];
                                $orderid = $order['id'];
                                $status = $order['status'];
                                $adres = $order['adress'];
                                $phone = $order['phone'];
                                $login = $order['login'];
                                $price = $order['price'];
                                foreach($products as $product) {
                                    if($product['id']==$order['product']) {
                                        $title = $product['title'];
                                    }
                                }

                                if($order['status']=='Заказан') {
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
                                        <button type='button' class='btn btn-secondary btn-sm' data-bs-id='$orderid' data-bs-login='$login'>Отклонить</button>
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
                        if($count==0) {
                            echo '<div class="footer__text" style="text-align: center;">В данный момент список заказов пуст.</div>';
                        }
                    ?>
                </div>
                <div id='products' style='display: none;'>
                    <?php

                        if(count($products)>0) {
                            foreach($products as $product) {

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
                        echo '<br><button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addProductModal">Добавить товар</button>';
                    ?>
                </div>
                <div id='reviews' style='display: none;'>
                    <?php 

                        if(count($reviews)>0) {
                            foreach($reviews as $review) {
                                $rid = $review['id'];
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
                                        <div class='col-4'>
                                            <div class='r_text'>
                                                $text
                                            </div>
                                        </div>
                                        <div class='col-3'> 
                                            <p>
                                                <button type='button' style='text-align: start;' class='btn btn-secondary btn-sm' data-bs-id='$rid'>Удалить</button>
                                            </p>
                                        </div>
                                    </div>
                                </div>";
                            }
                        } else {
                            echo '<div class="footer__text">К сожалению, в данный момент отзывов нет. Вы можете оставить свой и он будет первым.</div>';
                        }

                    ?>
                </div>
            </section>
        <?php } else {?>
            <div class="intro">
                <div class="container">
                    <div class="intro__inner">
                        <h1 class="intro__title">Нет доступа!</h1>
                    </div>
                </div>
            </div>
        <?php }} else {?>
            <div class="intro">
                <div class="container">
                    <div class="intro__inner">
                        <h1 class="intro__title">Нет доступа!</h1>
                    </div>
                </div>
            </div>
        <?php }?>

    </body>
</html>
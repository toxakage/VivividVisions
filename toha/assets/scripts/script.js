$(document).ready(function(){

    var statues = document.getElementById('statues')
    var pictures = document.getElementById('pictures')

    var picturebtn = document.getElementById('picturebtn')
    if(picturebtn) {
        picturebtn.addEventListener("click",function() {
            pictures.style.display = 'block'
            statues.style.display = 'none'
            window.location.hash = '#pictures'
        });
    }
    var statuebtn = document.getElementById('statuebtn')
    if(statuebtn) {
        statuebtn.addEventListener("click",function() {
            pictures.style.display = 'none'
            statues.style.display = 'block'
            window.location.hash = '#statues'
        });
    
    }

    const currentUrl = window.location;

    if(currentUrl.pathname.search('index') >= 0) {
        if(currentUrl.hash=='#orders') {
            currentUrl.hash=''
            $('#SuccessOrdersModal').modal('show')
        } else if(currentUrl.hash=='#auth') {
            currentUrl.hash=''
            $('#authModal').modal('show')
        } else if(currentUrl.hash=='#products') {
            currentUrl.hash=''
            $('#ordersModal').modal('show')
        } else if(currentUrl.hash=='#logout') {
            currentUrl.hash=''
            $.ajax({
                url: '/assets/php/do_logout.php',
                method: 'post',
                dataType: 'html',
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы вышли из аккаунта!",
                        icon: "success"
                    }).then((result) => {
                        if(result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        }
    } else if(currentUrl.pathname.search('catalog') >= 0) {
        if(currentUrl.hash=='#statues') {
            statuebtn.setAttribute("checked","")
            pictures.style.display = 'none'
            statues.style.display = 'block'
        } else {
            picturebtn.setAttribute("checked","")
            pictures.style.display = 'block'
            statues.style.display = 'none'
        }
    }

    var min = document.getElementById('min')
    var max = document.getElementById('max')
    var filterbtn = document.getElementById('filterbtn')
    if(filterbtn) {
        filterbtn.addEventListener("click", function () {
            if (min.value != 'От' && max.value !='До') {
                if(min.value<max.value) {
                    currentUrl.search = 'price=' + min.value + '-' + max.value
                } else {
                    Swal.fire({
                        title: "Ошибка",
                        text: 'Минимальное не может быть больше или эквивалентно максимальному!',
                        icon: "error"
                    });
                }
            } else {
                Swal.fire({
                    title: "Ошибка",
                    text: 'Заполните все поля, чтобы продолжить!',
                    icon: "error"
                });
            }
        });
    }

    var searchtext = document.getElementById('searchtext')
    var searchbtn = document.getElementById('searchbtn')
    if(searchbtn) {
        searchbtn.addEventListener("click", function () {
            if (searchtext.value != '') {
                currentUrl.search = 'search=' + searchtext.value
            } else {
                Swal.fire({
                    title: "Ошибка",
                    text: 'Введите запрос!',
                    icon: "error"
                });
            }
        });
    }

    var searchdismiss = document.getElementById('searchdismiss')
    if(searchdismiss) {
        searchdismiss.addEventListener("click", function () {
            window.location.search = ''
        });
    }

    $('#regform').on('submit', function() {
        var email = $(this).find('input[name="username"]').val();
        var password = $(this).find('input[name="password"]').val();
        var paswordagain = $(this).find('input[name="password2"]').val();
        if(password.length>5 && paswordagain.length>5) {
            $.ajax({
                url: '/assets/php/do_register.php',
                method: 'post',
                dataType: 'html',
                data: {'username': email,'password': password,'password2': paswordagain},
                success: function(data){
                if(data=='0') {
                    Swal.fire({
                        title: "Ошибка",
                        text: "Пароли не совпадают!",
                        icon: "error"
                        });
                } else if(data=='1') {
                    Swal.fire({
                        title: "Ошибка",
                        text: "Аккаунт с таким адрессом электронной почты уже зарегистрирован!",
                        icon: "error"
                        });
                } else {
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы успешно зарегистрировались!",
                        icon: "success"
                        });
                }
                }
            });
        } else {
            Swal.fire({
                title: "Ошибка",
                text: "Длина пароля должна быть не менее чем 6 символов!",
                icon: "error"
                });
        }
        return false;
    });
    $('#authform').on('submit', function() {
        var email = $(this).find('input[name="username"]').val();
        var password = $(this).find('input[name="password"]').val();
        if(password.length>5) {
            $.ajax({
                url: '/assets/php/do_login.php',
                method: 'post',
                dataType: 'html',
                data: {'username': email,'password': password},
                success: function(data){
                if(data=='0') {
                    Swal.fire({
                        title: "Ошибка",
                        text: "Аккаунт с таким адрессом электронной почты не зарегистрирован!",
                        icon: "error"
                        });
                } else if(data=='1') {
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы успешно авторизовались!",
                        icon: "success"
                    }).then((result) => {
                        if(result.isConfirmed) {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Ошибка",
                        text: "Неверный пароль!",
                        icon: "error"
                        });
                }
                }
            });
        } else {
            Swal.fire({
                title: "Ошибка",
                text: "Длина пароля должна быть не менее чем 6 символов!",
                icon: "error"
                });
        }
        return false;
    });
    var productModal = document.getElementById('productModal')
    productModal.addEventListener('show.bs.modal', function (event) {
        var modalTitle = productModal.querySelector('.modal-title')
        var modalBodyImg = productModal.querySelector('.post__photo img')
        var modalBodyDesc = productModal.querySelector('.post__text')
        var modalBodySum = productModal.querySelector('.post__sum span')

        var button = event.relatedTarget
        var login = button.getAttribute('data-bs-login')
        var id = button.getAttribute('data-bs-id')
        var title = button.getAttribute('data-bs-title')
        var desc = button.getAttribute('data-bs-desc')
        var price = button.getAttribute('data-bs-price')

        modalTitle.textContent = title
        modalBodyImg.setAttribute('src','assets/img/product_' + id + '.png')
        modalBodyDesc.textContent = 'Описание: ' + desc
        modalBodySum.textContent = 'Цена за 1шт: ' + price + 'руб'

        var addorder = productModal.querySelector('#addorder')
        if(addorder) {
            addorder.addEventListener("click",function() {
                $.ajax({
                    url: './assets/php/add_order.php',
                    method: 'post',
                    dataType: 'html',
                    data: {'login': login,'product': id,'price': price},
                    success: function(data){
                        if(data=='BAD') {
                            Swal.fire({
                                title: "Ошибка",
                                text: "Данный товар уже есть в корзине!",
                                icon: "error"
                            });
                        } else if(data=='GOOD') {
                            Swal.fire({
                                title: "Успешно",
                                text: "Товар успешно добавлен в корзину!",
                                icon: "success"
                            }).then((result) => {
                                if(result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        }
                    }
                });
            })   
        }
        
    })

    $('#logoutForm').click(function() {
        $.ajax({
            url: '/assets/php/do_logout.php',
            method: 'post',
            dataType: 'html',
            success: function(){
                Swal.fire({
                    title: "Успешно",
                    text: "Вы вышли из аккаунта!",
                    icon: "success"
                }).then((result) => {
                    if(result.isConfirmed) {
                        location.reload();
                    }
                });
            }
        });
        return false;
    });

    $('#OrdersForm').on('submit', function () {
        var adres = $(this).find('input[name="adres"]').val();
        var phone = $(this).find('input[name="number"]').val();
        var orderButton = document.querySelector('#ordersSumbit');	
        var login = orderButton.getAttribute('data-bs-login');
        var price = orderButton.getAttribute('data-bs-money');
        var ids = orderButton.getAttribute('data-bs-orders')
        if (adres != 'Адрес' && phone != 'Номер телефона') {
            $.ajax({
                url: '/assets/php/move_orders.php',
                method: 'post',
                dataType: 'html',
                data: { 'adres': adres, 'phone': phone, 'login': login, 'money': price, 'ids': ids },
                success: function () {
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы успешно оформили заказ. Ожидайте ответ модератора.",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                title: "Ошибка",
                text: 'Похоже, что вы не заполнили все поля. Введите ваш адрес и номер телефона.',
                icon: "error"
            });
        }
        return false;
    });

    $('#reviewForm').on('submit', function () {
        var name = $(this).find('input[name="fio"]').val();
        var text = $(this).find('input[name="txt"]').val();
        var stars = document.getElementById('stars')	
        var now = new Date();
        var year = now.getFullYear()
        var month = now.getMonth()+1
        var day = now.getDate()
        if (name != 'Фамилия и имя' && text != 'Текст' && stars.value!='none') {
            $.ajax({
                url: '/assets/php/add_review.php',
                method: 'post',
                dataType: 'html',
                data: { 'name': name, 'text': text, 'stars': stars.value, 'date': year + '.' + month + '.' + day},
                success: function () {
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы успешно оставили отзыв. Спасибо, нам важно ваше мнение!",
                        icon: "success"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                title: "Ошибка",
                text: 'Похоже, что вы не заполнили все поля. Введите ваше имя и фамилию, текст отзыва и выберите кол-во звезд.',
                icon: "error"
            });
        }
        return false;
    });

    var orderModal = document.getElementById('ordersModal')
    var orderModalBody = orderModal.querySelector(".modal-body")
    var deleteOrders = orderModalBody.querySelectorAll(".btn-close")
    deleteOrders.forEach((deleteOrder) => {
        deleteOrder.addEventListener("click",function(){

            var orderid = deleteOrder.getAttribute('data-bs-id')

            $.ajax({
                url: '/assets/php/delete_order.php',
                method: 'post',
                dataType: 'html',
                data: {'orderid': orderid,'admin': 'none'},
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы удалили товар из корзины!",
                        icon: "success"
                    }).then((result) => {
                        if(result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
            });
        })
    });

});
 // Слайдер на главной странице
let currentIndex = 0;

function showSlides(index) {
    const slides = document.querySelectorAll('.slide');
    if (index >= slides.length) {
        currentIndex = 0;
    }
    if (index < 0) {
        currentIndex = slides.length - 1;
    }
    slides.forEach((slide, i) => {
        slide.style.transform = `translateX(${-100 * currentIndex}%)`;
    });
}

function moveSlides(n) {
    showSlides(currentIndex += n);
}

document.addEventListener('DOMContentLoaded', () => {
    showSlides(currentIndex);
});
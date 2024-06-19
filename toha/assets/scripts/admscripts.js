$(document).ready(function(){

    var files;

    $('#image').on('change', function(){
        files = this.files;
    });

    $('#AddProductForm').on('submit', function() {
        var title = $(this).find('input[name="title"]').val()
        var desc = $(this).find('input[name="desc"]').val()
        var price = $(this).find('input[name="price"]').val()
        var category = document.getElementById('category')

        if(title!='Название' && desc!='Описание' && price!='Цена' && category.value!='none') {
            if(typeof files != 'undefined') {

                $.ajax({
                    url: '/assets/php/add_product.php',
                    method: 'post',
                    dataType: 'html',
                    data: {'title': title,'desc': desc,'price': price,'category': category.value},
                    success: function( data ){
                        
                        if(parseInt(data)>=0) {
                            var id = parseInt(data);

                            var data = new FormData();

                            $.each( files, function( key, value ){
                                data.append( key, value );
                            });
            
                            data.append( 'my_file_upload', 1 );
                            data.append( 'id',id);
            
                            // AJAX запрос
                            $.ajax({
                                url         : '/assets/php/uploader.php',
                                type        : 'POST', // важно!
                                data        : data,
                                cache       : false,
                                dataType    : 'html',
                                processData : false,
                                contentType : false,
                                success     : function( respond ){
                                    Swal.fire({
                                        title: "Успешно",
                                        text: "Вы добавили новый товар в каталог.",
                                        icon: "success"
                                        }).then((result) => {
                                            if(result.isConfirmed) {
                                                location.reload();
                                            }
                                        });
                                }
            
                            });
                        } else {
                            Swal.fire({
                                title: "Ошибка",
                                text: 'Ошибка загрузки изображения на сервер. Попробуйте еще раз.',
                                icon: "error"
                                });
                        }
                    }
                });

            } else {
                Swal.fire({
                    title: "Ошибка",
                    text: 'Похоже, что вы не выбрали изображение товара.',
                    icon: "error"
                    });
            }
        } else {
            Swal.fire({
                title: "Ошибка",
                text: 'Похоже, что вы не заполнили все поля. Введите название, описание и цену товара.',
                icon: "error"
                });
        }
        return false;
    });
    var orderModerationModal = document.getElementById('orders')
    var dissmissOrders = orderModerationModal.querySelectorAll(".btn-secondary")
    var acceptOrders = orderModerationModal.querySelectorAll(".btn-primary")
    acceptOrders.forEach((acceptOrder) => {
        acceptOrder.addEventListener("click",function(){

            var id = acceptOrder.getAttribute('data-bs-id')
            var login = acceptOrder.getAttribute('data-bs-login')

            $.ajax({
                url: '/assets/php/accept_orders.php',
                method: 'post',
                dataType: 'html',
                data: {'id': id,'login': login},
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы одобрили заказ.",
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
    dissmissOrders.forEach((dissmissOrder) => {
        dissmissOrder.addEventListener("click",function(){

            var id = dissmissOrder.getAttribute('data-bs-id')
            var price = dissmissOrder.getAttribute('data-bs-price')
            var login = dissmissOrder.getAttribute('data-bs-login')

            $.ajax({
                url: '/assets/php/delete_order.php',
                method: 'post',
                dataType: 'html',
                data: {'orderid': id,'login': login,'admin': 'true'},
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы отклонили заказ.",
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
    var products = document.getElementById('products')
    var deleteProducts = products.querySelectorAll(".btn-close")
    deleteProducts.forEach((deleteProduct) => {
        deleteProduct.addEventListener("click",function(){

            var id = deleteProduct.getAttribute('data-bs-id')

            $.ajax({
                url: '/assets/php/delete_product.php',
                method: 'post',
                dataType: 'html',
                data: {'id': id},
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы удалили товар из каталога!",
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
    var reviews = document.getElementById('reviews')
    var deleteReviews = reviews.querySelectorAll(".btn-secondary")
    deleteReviews.forEach((deleteReview) => {
        deleteReview.addEventListener("click",function(){

            var id = deleteReview.getAttribute('data-bs-id')

            $.ajax({
                url: '/assets/php/delete_review.php',
                method: 'post',
                dataType: 'html',
                data: {'id': id},
                success: function(){
                    Swal.fire({
                        title: "Успешно",
                        text: "Вы удалили отзыв.",
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
    var reviewsbtn = document.getElementById('reviewsbtn')
    reviewsbtn.addEventListener("click",function() {
        orderModerationModal.style.display = 'none'
        products.style.display = 'none'
        reviews.style.display = 'block'
        window.location.hash = '#reviews'
    });
    var orderbtn = document.getElementById('ordersbtn')
    orderbtn.addEventListener("click",function() {
        window.location.hash = '#orders'
        orderModerationModal.style.display = 'block'
        reviews.style.display = 'none'
        products.style.display = 'none'
    });
    var productbtn = document.getElementById('productsbtn')
    productbtn.addEventListener("click",function() {
        orderModerationModal.style.display = 'none'
        products.style.display = 'block'
        reviews.style.display = 'none'
        window.location.hash = '#products'
    });

    const currentUrl = window.location.hash;

    if(currentUrl=='#products') {
        productbtn.setAttribute("checked","")
        orderModerationModal.style.display = 'none'
        reviews.style.display = 'none'
        products.style.display = 'block'
    } else if(currentUrl=='#reviews') {
        reviewsbtn.setAttribute("checked","")
        orderModerationModal.style.display = 'none'
        reviews.style.display = 'block'
        products.style.display = 'none'
    } else {
        orderbtn.setAttribute("checked","")
        orderModerationModal.style.display = 'block'
        reviews.style.display = 'none'
        products.style.display = 'none'
    }
});
$(function() {
	$('.updateAll').click(function(event) {
        
        //xu ly class updateAllForm hiện ra
        $('.updateAllForm').addClass('updateAllFormhienra');

        //xy ly class cho .momo hiện ra
        $('.momo').addClass('momodira');

    });

    //jquery cho nut close
    // Khi click vào nút thoát hoặc phần background mờ thì phần đăng nhập - tạo tài khoản sẽ biến mất
    $('.nutdong, .momo').click(function(event) {
        
        //xu ly class updateAllForm biến mất
        $('.updateAllForm').removeClass('updateAllFormhienra');

        //xy ly class cho .momo biến mất
        $('.momo').removeClass('momodira');
    });

})
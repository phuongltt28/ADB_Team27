<?php
	// Vui lòng thêm database trước khi thực hiện chương trình
	use MongoDB\Client;

	require_once "vendor/autoload.php";

	// khoi tao class Client
	$conn = new Client("mongodb://127.0.0.1:27017");

	// $db = $conn->qlKhoGao;
	$db = $conn->selectDatabase('qlKhoGao');

	$db->Kho->insertMany([
		[
			'tenKho'=>'Kho 1',
			'diaChi'=>'12 Lê Lợi, TPHCM',
		],
		[
			'tenKho'=>'Kho 2',
			'diaChi'=>'220 Đinh Tiên Hoàng, TPHCM',
		]
	]);

	$db->NhaCungCap->insertMany([
		[
			'tenNCC'=>'Vinamic Organic Farm',
			'diaChi'=>'22A Nguyễn Văn Trỗi, P17, Q.Phú Nhuận, TPHCM',
		],
		[
			'tenNCC'=>'Quý Thu Rice Q&T',
			'diaChi'=>'99 Nguyễn Văn Nghi, Gó Vấp, TP HCM',
		]
	]);

	$db->LoaiGao->insertMany([
		[
			'tenLoai'=>'Gạo dẻo',
		],
		[
			'tenLoai'=>'Gạo nở',
		],
		[
			'tenLoai'=>'Gạo xốp',
		],
	]);
	echo "Thêm database thành công, vui lòng kiểm tra lại";
?>
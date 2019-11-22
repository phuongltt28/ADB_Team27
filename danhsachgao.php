<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Danh sách gạo</title>
<link rel="stylesheet" href="mycss/danhsachgao.css">
<!-- Latest jQuery form server -->
<script src="https://code.jquery.com/jquery.min.js"></script>

<script src="myjs/danhsachgao.js"></script>	
<script src="myjs/jquery-3.3.1.js"></script>

</head>
<body>
	<h1>DANH SÁCH GẠO</h1>
	<?php
		include "php-chung.php";
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		use MongoDB\Client;

		require_once "vendor/autoload.php";

		// khoi tao class Client
		$conn = new Client("mongodb://127.0.0.1:27017");

		$db = $conn->selectDatabase('qlKhoGao');
	?>
	
	<table align="center" width="80%">
		<tr>
			<th>TÊN GẠO</th>
			<th>LOẠI GẠO</th>
			<th>NHÀ CUNG CẤP</th>
			<th>NGÀY NHẬP</th>
			<th>KHO</th>
			<th>TÌNH TRẠNG</th>
			<th>THAO TÁC</th>
		</tr>
		<?php  
			$all = $db->Gao->find();
			foreach ($all as $g) {
				$id = $g->_id;
				$tenGao = $g->tenGao;
				$loaiGao = $db->LoaiGao->findOne(['_id'=>new MongoDB\BSON\ObjectId($g->maLoai)], ['projection'=>['tenLoai'=>1]])->tenLoai;
				$ncc = $db->NhaCungCap->findOne(['_id'=>new MongoDB\BSON\ObjectId($g->maNCC)], ['projection'=>['tenNCC'=>1]])->tenNCC;
				$ngayNhap = $g->ngayNhap;
				$makho = $db->ChiTietGaoKho->findOne(['tenGao'=>$tenGao], ['projection'=>['maKho'=>'1', '_id'=>0]]);
				$kho = $db->Kho->findOne(['_id'=>new MongoDB\BSON\ObjectId($makho->maKho)], ['projection'=>['tenKho'=>'1', '_id'=>0]])->tenKho;
				$tinhtrang = ($g->soLuong > 0) ? "Còn hàng" : "Hết hàng";
		?>
				<tr>
					<td><?php echo $tenGao ?></td>
					<td><?php echo $loaiGao ?></td>
					<td><?php echo $ncc ?></td>
					<td><?php echo $ngayNhap ?></td>
					<td><?php echo $kho ?></td>
					<td><?php echo $tinhtrang ?></td>
					<td><a href="thongtingao.php?id=<?php echo $id ?>" class="btnXem">Xem</a></td>
				</tr>
				
		<?php
			}

		?>
			
		</tr>
		
	</table>
	<div>
		<a href="themgao.php" class="btnThem">Thêm gạo</a><a href="timkiem.php" class="btnTimKiem">Tìm kiếm gạo</a>
		<button class="updateAll">Cập nhật đơn giá theo loại gạo</button>
	</div>
	<div class="momo"></div> 
    <div class="updateAllForm">
        <div class="updateForm" align="center">
        	<h1>Cập nhật đơn giá theo loại gạo</h1>
            <form class="fupdate" method="post">
                <table class="update-table" align="center">
                    <tr>
						<td>Tên loại: </td>
						<td>
							<select name="cb_maloaiUpdate" width="70">
							<?php  
								$collectionLoaiUpdate = $db->selectCollection('LoaiGao')->find()->toArray();
								foreach ($collectionLoaiUpdate as $lgUd) {
									echo "<option value='$lgUd->_id'>$lgUd->tenLoai</option>";
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Đơn giá: </td>
						<td><input type="text" pattern="[^0|\D]\d{4,}$" required="" title="Vui lòng nhập vào đơn giá" placeholder = "Nhập đơn giá gạo. Đơn giá tối thiểu 10000"  name="dongiaUp" id="dongia" size="70" value=""></td>
					</tr>
                    <tr>
                        <td class="btnField" colspan="2" align="center"><input type="submit" class="btnCapNhat" name="updateAll" id="updateAll" value="Cập nhật tất cả"><div class="nutdong" style="display: inline-block;">THOÁT</div></td>
                        <?php  
                        	if (isset($_POST['updateAll'])) {
                        		$donGiaUp = $_POST['dongiaUp'];
                        		$loaiGaoUp = $_POST['cb_maloaiUpdate'];

                        		try {
                        			$db->Gao->updateMany(['maLoai'=>new MongoDB\BSON\ObjectId($loaiGaoUp)], ['$set'=>['donGia'=>$donGiaUp]]);
                        			XuatThongBao("Cập nhật đơn giá mới thành công");
                        		}
                        		catch (MongoCursorException $e) {
                        			XuatThongBao("Không thể cập nhật đơn giá gạo loại này");
                        		}
                        		
                        	}
                        ?>
                    </tr>
                    
                </table>
            </form>
        </div><!-- End-signin area -->
	
</body>
</html>
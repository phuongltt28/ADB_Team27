<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Thêm gạo mới</title>
<link rel="stylesheet" href="mycss/themgao.css">  

</head>
<body>
	<h1>THÊM GẠO</h1>
	<a href="danhsachgao.php" class="back">Danh sách gạo</a>
	<?php
		include "php-chung.php";
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		use MongoDB\Client;

		require_once "vendor/autoload.php";

		// khoi tao class Client
		$conn = new Client("mongodb://127.0.0.1:27017");

		$db = $conn->selectDatabase('qlKhoGao');
		if (isset($_POST['them'])) {
			$ten = $_POST['tengao'];
			$checkGao = $db->Gao->findOne(['tenGao'=>$ten]);
			if (!empty($checkGao)) {
				XuatThongBao("Đã có gạo tên " . $ten);
			}else {
				$loai = $_POST['cb_maloai'];
				$ncc = $_POST['cb_ncc'];
				$dongia = $_POST['dongia'];
				$soluong = $_POST['soluong'];
				$donggoi = $_POST['donggoi'];
				$trongluong = $_POST['trongluong'];
				$donvi = $_POST['donvi'];
				$dkbq = $_POST['dkbq'];
				$nsx = date_format(new DateTime($_POST['nsx']), "Y-m-d");
				$sohansd = $_POST['sohansd'];
				$chuhansd = $_POST['nam-thanghsd'];
				$today = date_create();
		        $ngaynhap = date_format($today, 'Y-m-d');
				$makho = $_POST['makho'];
				$nhh = new DateTime();
				if ($chuhansd == 'month') {
					$chuhansd = ' tháng';
					$nhh = date("Y-m-d", strtotime("".date_format(new DateTime($nsx), "Y-m-d"). "+ " . $sohansd ." month"));
					
				}
				else {
					$chuhansd = ' năm';
					$nhh = date("Y-m-d", strtotime("".date_format(new DateTime($nsx), "Y-m-d"). "+ " . $sohansd ." year"));
				}
				$hansd = $sohansd . "$chuhansd";
				
				try{
					try {
						$db->ChiTietGaoKho->insertOne([
							'tenGao'=>$ten,
							'maKho'=>new MongoDB\BSON\ObjectId($makho),
							'soLuong'=>$soluong,
							'ngayNhap'=>$ngaynhap,
						]);
					}
					catch (MongoCursorException $e) {
						XuatThongBao("Gạo không thể thêm vào kho");
					}

					$db->Gao->insertOne([
						'maLoai'=>new MongoDB\BSON\ObjectId($loai),
						'tenGao'=>$ten,
						'donGia'=>$dongia,
						'soLuong'=>$soluong,
						'dieuKienBaoQuan'=>$dkbq,
						'hanSd'=>$hansd,
						'ngayNhap'=>$ngaynhap,
						'NSX'=>$nsx,
						'NHH'=>$nhh,
						'maNCC'=>new MongoDB\BSON\ObjectId($ncc),
						'dongGoi'=>$donggoi,
						'trongLuong'=>$trongluong,
						'donVi'=>$donvi
					]);
					XuatThongBao("Thêm thành công");
					moveTo("danhsachgao.php");
				} catch(MongoCursorException $e) {
					XuatThongBao("Thêm thất bại");
				}
			}
		}
	?>
	<form method="post">
		<table align="center" width="50%">
			<tr>
				<td>Tên gạo: </td>
				<td><input type="text" required="" title="Vui lòng điền đầy đủ thông tin"  name="tengao" id="tengao" size="70" value=""></td>
			</tr>

			<tr>
				<td>Tên loại: </td>
				<td>
					<select name="cb_maloai" width="70">
					<?php  
						try {
							$collectionLoai = $db->selectCollection('LoaiGao')->find()->toArray();
							foreach ($collectionLoai as $lg) {
								echo "<option value='$lg->_id'>$lg->tenLoai</option>";
							}
						}
						catch(MongoCursorException $e){
							echo "Chưa có loại gạo";
						}
						
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Nhà cung cấp: </td>
				<td>
					<select name="cb_ncc" width="70">
					<?php 
						try {
						 	$collectionNCC = $db->selectCollection('NhaCungCap')->find()->toArray();
							foreach ($collectionNCC as $ncc) {
								echo "<option value='$ncc->_id'>$ncc->tenNCC</option>";
							}
						} catch (MongoCursorException $e) {
							echo "Chưa có nhà cung cấp";	
						} 
						
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Đơn giá: </td>
				<td><input type="text" pattern="[^0|\D]\d{4,}$" required="" title="Vui lòng nhập vào đơn giá" placeholder = "Nhập đơn giá gạo. Đơn giá tối thiểu 10000"  name="dongia" id="dongia" size="70" value=""></td>
			</tr>
			<tr>
				<td>Số lượng: </td>
				<td><input type="text" pattern="[^0|\D]\d*$" required="" title="Vui lòng nhập vào số lượng" placeholder = "Nhập số lượng gạo. Số lượng tối thiểu là 1"  name="soluong" id="soluong" size="70" value="" ></td>
			</tr>
			
			<tr>
				<td>Đóng gói: </td>
				<td>
					<select name="donggoi">
						<option selected value = "Hộp">Hộp</option>
						<option value = "Bao">Bao</option>
						<option value = "Túi">Túi</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Trọng lượng: </td>
				<td>
					<select name="trongluong">
						<option selected value = 1>1</option>
						<option value = 2>2</option>
						<option value = 5>5</option>
						<option value = 10>10</option>
					</select>
					<input type="text" readonly name="donvi" id="donvi" size="70" value="kg" >
				</td>
			</tr>
			
			<tr>
				<td>Điều kiện bảo quản: </td>
				<td><input type="text" title="Vui lòng điền đầy đủ thông tin"  name="dkbq" id="dkbq" size="70" value="" ></td>
			</tr>
			<tr>
				<td>Ngày sản xuât: </td>
				<td><input type="date" required="" title="Vui lòng điền đầy đủ thông tin"  name="nsx" id="nsx" size="70" value=""></td>
			</tr>
			<tr>
				<td>Hạn sử dụng: </td>
				<td><input type="number" min=1 max=12 name="sohansd" id="sohansd" size="70" value='' required="" title="Vui lòng điền đầy đủ thông tin" >
					<select name = nam-thanghsd>
						<option value="month">Tháng</option>
						<option value="year">Năm</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>Mã kho: </td>
				<td>
					<select name="makho" width="70">
						<?php  
							try {
								$collectionKho = $db->selectCollection('Kho')->find()->toArray();
								foreach ($collectionKho as $k) {
									echo "<option value='$k->_id'>$k->tenKho</option>";
								}
							} catch (MongoCursorException $e) {
								echo "Chưa có kho";
							}
								
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'><input class="them" name="them" type = "submit" value="Thêm gạo mới"></td>
			</tr>
			
		</table>
	</form>

	
	
</body>
</html>
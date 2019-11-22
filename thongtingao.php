<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Thông tin gạo</title>
<link rel="stylesheet" href="mycss/themgao.css">  
<!-- <link rel="stylesheet" href="mycss/thongtingao.css">   -->
<script type="text/javascript">
	function confirmBox(msg) {
		return confirm(msg);
	}
</script>
</head>
<body>
	<h1>THÔNG TIN GẠO</h1>
	<?php
		include "php-chung.php";
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		use MongoDB\Client;

		require_once "vendor/autoload.php";

		// khoi tao class Client
		$conn = new Client("mongodb://127.0.0.1:27017");

		$db = $conn->selectDatabase('qlKhoGao');
		$tenGao = $loai = $ma_ncc = $ngayNhap = $makho = $donGia = $soLuong = $dongGoi = $trongLuong = $dkbq = $nsx = $nhh = "";
		$hansd = array("","");
		if (!isset($_GET['id'])) {
			$id = "";
		} else {
			$id = $_GET['id'];
			$gao = $db->Gao->findOne(['_id'=>new MongoDB\BSON\ObjectId($id)]);
			
			$tenGao = $gao->tenGao;
			$loai = $gao->maLoai;
			$ma_ncc = $gao->maNCC;
			$ngayNhap = $gao->ngayNhap;
			$makho = $db->ChiTietGaoKho->findOne(['tenGao'=>$tenGao], ['projection'=>['maKho'=>'1', '_id'=>0]])->maKho;
			$donGia = $gao->donGia;
			$soLuong = $gao->soLuong;
			$dongGoi = $gao->dongGoi;
			$trongLuong = $gao->trongLuong;
			$dkbq = $gao->dieuKienBaoQuan;
			$nsx = $gao->NSX;
			$nhh = $gao->NHH;
			$hansd = explode(" ", $gao->hanSd);
		}
		
		if (isset($_POST['action'])) {
			$action = $_POST['action'];
			if ($action == "Xóa gạo") {
				try {
					$db->Gao->deleteOne(['_id'=>new MongoDB\BSON\ObjectId($id)]);
					$db->ChiTietGaoKho->deleteOne(['tenGao'=>$tenGao]);
					XuatThongBao("Xóa gạo thành công");
					moveTo('danhsachgao.php');
				}
				catch (MongoCursorException $e) {
					XuatThongBao("Không thể xóa gạo");
				}
				
			}
			else {
				$old = $db->Gao->findOne(['_id'=>new MongoDB\BSON\ObjectId($id)], ['projection'=>['tenGao'=>'1', '_id'=>0]])->tenGao;
				$ten = $_POST['tengao'];
				if ($ten != $old) {
					$checkGao = $db->Gao->findOne(['tenGao'=>$ten]);
				}else {
					$checkGao = null;
				}
				
				if (!empty($checkGao)) {
					XuatThongBao("Đã có gạo tên " . $ten);
				}
				else {
					$loai = $_POST['cb_maloai'];
					$ncc = $_POST['cb_ncc'];
					$dongia = $_POST['dongia'];
					$soluong = $_POST['soluong'];
					$donggoi = $_POST['donggoi'];
					$trongluong = $_POST['trongluong'];
					$donvi = $_POST['donvi'];
					$dkbq = $_POST['dkbq'];
					$makho = $_POST['makho'];
					
					try {
						try{
							$db->Gao->updateOne(
								['_id'=>new MongoDB\BSON\ObjectId($id)],
								['$set'=>
									[
									'maLoai'=>new MongoDB\BSON\ObjectId($loai),
									'tenGao'=>$ten,
									'donGia'=>$dongia,
									'soLuong'=>$soluong,
									'dieuKienBaoQuan'=>$dkbq,
									'maNCC'=>new MongoDB\BSON\ObjectId($ncc),
									'dongGoi'=>$donggoi,
									'trongLuong'=>$trongluong,
									'donVi'=>$donvi]
								]
							);
						}
						catch(MongoCursorException $e) {
							XuatThongBao("lỗi");
						}
						

						$db->ChiTietGaoKho->updateOne(
							['tenGao'=>$old],
							['$set'=>
								[
								'soLuong'=>$soluong,
								'maKho'=>new MongoDB\BSON\ObjectId($makho),
								'tenGao'=>$ten]
							]
						);
						XuatThongBao("Sửa thông tin gạo thành công");
						moveTo('thongtingao.php?id=' . $id);
					}
					catch(MongoCursorException $e) {
						XuatThongBao("Sửa thông tin gạo thất bại");
					}
					
				}
				
			}
		}
		
	?>
	<a href="danhsachgao.php" class="back">Danh sách gạo</a>
	<form method="post">
		<table align="center" width="50%">
			<tr>
				<td>Tên gạo: </td>
				<td><input type="text" required="" title="Vui lòng điền đầy đủ thông tin"  name="tengao" id="tengao" size="70" value="<?php echo $tenGao ?>"></td>
			</tr>

			<tr>
				<td>Tên loại: </td>
				<td>
					<select name="cb_maloai" width="70">
					<?php  
						$collectionLoai = $db->selectCollection('LoaiGao')->find()->toArray();
						foreach ($collectionLoai as $lg) {
							if ($lg->_id == $loai) {
								echo "<option value='$lg->_id' selected>$lg->tenLoai</option>";
							}
							else echo "<option value='$lg->_id'>$lg->tenLoai</option>";
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
						$collectionNCC = $db->selectCollection('NhaCungCap')->find()->toArray();
						foreach ($collectionNCC as $ncc) {
							if ($ncc->_id == $ma_ncc) echo "<option value='$ncc->_id' selected>$ncc->tenNCC</option>";
							else echo "<option value='$ncc->_id'>$ncc->tenNCC</option>";
						}
					?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Đơn giá: </td>
				<td><input type="text" pattern="[^0|\D]\d{4,}$" required="" title="Vui lòng nhập vào đơn giá" placeholder = "Nhập đơn giá gạo. Đơn giá tối thiểu 10000"  name="dongia" id="dongia" size="70" value="<?php echo $donGia ?>"></td>
			</tr>
			<tr>
				<td>Số lượng: </td>
				<td><input type="text" pattern="[^0|\D]\d*$" required="" title="Vui lòng nhập vào số lượng" placeholder = "Nhập số lượng gạo. Số lượng tối thiểu là 1"  name="soluong" id="soluong" size="70" value="<?php echo $soLuong ?>" ></td>
			</tr>
			
			<tr>
				<td>Đóng gói: </td>
				<td>
					<select name="donggoi">
						<option value = "Hộp" <?php echo ($dongGoi == "Hộp") ? "selected" : "" ?>>Hộp</option>
						<option value = "Bao" <?php echo ($dongGoi == "Bao") ? "selected" : "" ?>>Bao</option>
						<option value = "Túi" <?php echo ($dongGoi == "Túi") ? "selected" : "" ?>>Túi</option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td>Trọng lượng: </td>
				<td>
					<select name="trongluong">
						<option value = 1 <?php echo ($trongLuong == 1) ? "selected" : "" ?>>1</option>
						<option value = 2 <?php echo ($trongLuong == 2) ? "selected" : "" ?>>2</option>
						<option value = 5 <?php echo ($trongLuong == 5) ? "selected" : "" ?>>5</option>
						<option value = 10 <?php echo ($trongLuong == 10) ? "selected" : "" ?>>10</option>
					</select>
					<input type="text" readonly name="donvi" id="donvi" size="70" value="kg" >
				</td>
			</tr>
			
			<tr>
				<td>Điều kiện bảo quản: </td>
				<td><input type="text" title="Vui lòng điền đầy đủ thông tin"  name="dkbq" id="dkbq" size="70" value="<?php echo $dkbq ?>" ></td>
			</tr>
			<tr>
				<td>Ngày sản xuât: </td>
				<td><input type="date" readonly title="Vui lòng điền đầy đủ thông tin"  name="nsx" id="nsx" size="70" value="<?php echo $nsx ?>"></td>
			</tr>
			<tr>
				<td>Hạn sử dụng: </td>
				<td><input type="number" min=1 max=12 name="sohansd" id="sohansd" size="70" value='<?php echo $hansd[0] ?>' readonly title="Vui lòng điền đầy đủ thông tin" >
					<select name = nam-thanghsd readonly>
						<option value="month" readonly <?php echo ($hansd[1] == "tháng") ? "selected" : "" ?>>Tháng</option>
						<option value="year" readonly <?php echo ($hansd[1] == "năm") ? "selected" : "" ?>>Năm</option>
					</select>
				</td>
			</tr>

			<tr>
				<td>Ngày hết hạn: </td>
				<td><input type="date" readonly name="nhh" id="nhh" size="70" value="<?php echo $nhh ?>"></td>
			</tr>

			<tr>
				<td>Ngày nhập: </td>
				<td><input type="date" readonly name="ngaynhap" id="ngaynhap" size="70" value="<?php echo $ngayNhap ?>"></td>
			</tr>

			<tr>
				<td>Mã kho: </td>
				<td>
					<select name="makho" width="70">
						<?php  
							$collectionKho = $db->selectCollection('Kho')->find()->toArray();
							foreach ($collectionKho as $k) {
								if ($k->_id == $makho) echo "<option value='$k->_id' selected>$k->tenKho</option>";
								else echo "<option value='$k->_id'>$k->tenKho</option>";
							}
						?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td colspan="2"><input type = 'submit' class="sua" name="action" value="Sửa thông tin gạo"><input type = 'submit' onclick="return confirmBox('Bạn có muốn xóa <?php echo $tenGao ?>')" class="xoa" name="action" value="Xóa gạo"></td>
			</tr>
			
		</table>
	</form>
	
	
</body>
</html>
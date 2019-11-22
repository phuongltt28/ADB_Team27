<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tìm kiếm gạo</title>
<link rel="stylesheet" href="mycss/themgao.css">
<link rel="stylesheet" href="mycss/timkiem.css">
<script type="text/javascript">
	function checkRD(){
		if (document.getElementById('search_by_ten_gao').checked) {
			document.getElementById('fieldKho').style.display="none";
			document.getElementById('fieldNCC').style.display="none";
			document.getElementById('fieldLoaiGao').style.display="none";
			document.getElementById('fieldTenGao').style.display="";
		}
		else if (document.getElementById('search_by_loai_gao').checked) {
			document.getElementById('fieldKho').style.display="none";
			document.getElementById('fieldNCC').style.display="none";
			document.getElementById('fieldLoaiGao').style.display="";
			document.getElementById('fieldTenGao').style.display="none";
		}
		else if (document.getElementById('search_by_ncc').checked) {
			document.getElementById('fieldKho').style.display="none";
			document.getElementById('fieldNCC').style.display="";
			document.getElementById('fieldLoaiGao').style.display="none";
			document.getElementById('fieldTenGao').style.display="none";
		}
		else {
			document.getElementById('fieldKho').style.display="";
			document.getElementById('fieldNCC').style.display="none";
			document.getElementById('fieldLoaiGao').style.display="none";
			document.getElementById('fieldTenGao').style.display="none";
		}
	}
	
</script>
</head>
<body>
	<a href="danhsachgao.php" class="back">Danh sách gạo</a>
	<h1>TÌM KIẾM GẠO</h1>
	<?php
		include "php-chung.php";
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		use MongoDB\Client;

		require_once "vendor/autoload.php";

		// khoi tao class Client
		$conn = new Client("mongodb://127.0.0.1:27017");

		$db = $conn->selectDatabase('qlKhoGao');
		$search_by = $loai = $tengao = $ncc = $makho = $ten = "";
	?>
	<form method="post">
		<table align="center" width="50%">
			<tr>
				<td>Tìm kiếm theo:</td>
				<td>
					<input type="radio" name="search_by" id="search_by_ten_gao" checked onchange="checkRD();" value="tengao">Tên gạo
					<input type="radio" name="search_by" id="search_by_loai_gao" onchange="checkRD();" value="loaigao">Loại gạo
					<input type="radio" name="search_by" id="search_by_ncc" onchange="checkRD();" value="ncc">Nhà cung cấp
					<input type="radio" name="search_by" id="search_by_kho" onchange="checkRD();" value="kho">Kho
				</td>
			</tr>
			<tr id="fieldTenGao">
				<td>Tên gạo: </td>
				<td><input type="text" name="tengao" id="tengao" size="70" value=""></td>
			</tr>

			<tr id="fieldLoaiGao" style="display: none">
				<td>Tên loại: </td>
				<td>
					<select name="cb_maloai" width="70">
					<?php  
						$collectionLoai = $db->selectCollection('LoaiGao')->find()->toArray();
						foreach ($collectionLoai as $lg) {
							echo "<option value='$lg->_id'>$lg->tenLoai</option>";
						}
					?>
					</select>
				</td>
			</tr>
			
			<tr id="fieldNCC" style="display: none">
				<td>Nhà cung cấp: </td>
				<td>
					<select name="cb_ncc" width="70">
					<?php  
						$collectionNCC = $db->selectCollection('NhaCungCap')->find()->toArray();
						foreach ($collectionNCC as $mancc) {
							echo "<option value='$mancc->_id'>$mancc->tenNCC</option>";
						}
					?>
					</select>
				</td>
			</tr>

			<tr id="fieldKho" style="display: none">
				<td>Mã kho: </td>
				<td>
					<select name="makho" width="70">
						<?php  
							$collectionKho = $db->selectCollection('Kho')->find()->toArray();
							foreach ($collectionKho as $k) {
								echo "<option value='$k->_id'>$k->tenKho</option>";
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td colspan='2'><input class="them" name="timkiem" type = "submit" value="Tìm kiếm"></td>
			</tr>
			
		</table>
	</form>
	<h1>KẾT QUẢ TÌM KIẾM</h1>
	<table id="tb_result">
		<tr>
			<th>TÊN GẠO</th>
			<th>LOẠI GẠO</th>
			<th>NHÀ CUNG CẤP</th>
			<th>KHO</th>
			<th>THAO TÁC</th>
		</tr>
		<?php  

			if (isset($_POST['timkiem'])) {
				$search_by = $_POST['search_by'];
				$ten = $_POST['tengao'];
				$loai = $_POST['cb_maloai'];
				$ncc = $_POST['cb_ncc'];
				$makho = $_POST['makho'];
					try{
						
							if ($search_by == "loaigao" && $loai != "") {
								$tk = $db->Gao->find(["maLoai"=>new MongoDB\BSON\ObjectId($loai)]);
							}
							else if ($search_by == "tengao" && $ten != "") {
								$tk = $db->Gao->find(["tenGao"=>['$regex'=>$ten, '$options'=>'i']]);		
							}
							else if ($search_by == "ncc" && $ncc != "") {
								$tk = $db->Gao->find(["maNCC"=>new MongoDB\BSON\ObjectId($ncc)]);	
							}
							else if ($search_by == "kho" && $makho != "") {
								$arrTenGao = $db->ChiTietGaoKho->find(["maKho"=>new MongoDB\BSON\ObjectId($makho)], ['projection'=>['tenGao'=>'1', '_id'=>0]]);
								$tk = array();
								foreach ($arrTenGao as $ar) {
									$c = $db->Gao->find(["tenGao"=>['$regex'=>$ar->tenGao, '$options'=>'i']])->toArray();
									$tk = array_merge($tk, $c);
								}
								
							}
						
							foreach ($tk as $g) {
								$id = $g->_id;
								$tenGao = $g->tenGao;
								$loaiGao = $db->LoaiGao->findOne(['_id'=>new MongoDB\BSON\ObjectId($g->maLoai)], ['projection'=>['tenLoai'=>1]])->tenLoai;
								$ncc = $db->NhaCungCap->findOne(['_id'=>new MongoDB\BSON\ObjectId($g->maNCC)], ['projection'=>['tenNCC'=>1]])->tenNCC;
								$makho = $db->ChiTietGaoKho->findOne(['tenGao'=>$tenGao], ['projection'=>['maKho'=>'1', '_id'=>0]]);
								$kho = $db->Kho->findOne(['_id'=>new MongoDB\BSON\ObjectId($makho->maKho)], ['projection'=>['tenKho'=>'1', '_id'=>0]])->tenKho;
				?>
								<tr>
									<td><?php echo $tenGao ?></td>
									<td><?php echo $loaiGao ?></td>
									<td><?php echo $ncc ?></td>
									<td><?php echo $kho ?></td>
									<td><a href="thongtingao.php?id=<?php echo $id ?>" class="btnXem">Xem</a></td>
								</tr>
				<?php
							}
						
					} catch(MongoCursorException $e) {
						echo "Lỗi tìm kiếm";
					}
				
			}
		
		?>
	</table>
	
	
</body>
</html>
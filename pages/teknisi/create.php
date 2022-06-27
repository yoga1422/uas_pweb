<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:25
 *
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(400);
    $reply['error'] = 'POST method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data POST
 */
$id_teknisi = $_POST['id_teknisi'] ?? '';
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nomor_hp = $_POST['nomor_hp'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? date('Y-m-d');
$lama_bekerja = $_POST['lama_bekerja']?? '';
$jenis_kelamin = $_POST['jenis_kelamin'] ?? 0;

/**
 * Validation empty fields
 */
$isValidated = true;

if(empty($nama_lengkap)){
    $reply['error'] = 'Nama Lengkap harus diisi';
    $isValidated = false;
}
if(empty($alamat)){
    $reply['error'] = 'Alamat harus di isi';
    $isValidated = false;
}
if(empty($nomor_hp)){
    $reply['error'] = 'Nomor HP harus di isi';
    $isValidated = false;
}
if(empty($tanggal_lahir)){
    $reply['error'] = 'Tanggal Lahir harus di isi';
    $isValidated = false;
}
if(empty($lama_bekerja)){
    $reply['error'] = 'Lama Bekerja harus di isi';
    $isValidated = false;
}
if(empty($jenis_kelamin)){
    $reply['error'] = 'Jenis Kelamin harus di isi';
    $isValidated = false;
}
/*
 * Jika filter gagal
 */
if(!$isValidated){
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO teknisi (id_teknisi, nama_lengkap, alamat, nomor_hp, tanggal_lahir, lama_bekerja, jenis_kelamin) 
VALUES (:id_teknisi, :nama_lengkap, :alamat, :nomor_hp, :tanggal_lahir, :lama_bekerja, :jenis_kelamin)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_teknisi", $id_teknisi);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":tanggal_lahir", $tanggal_lahir);
    $statement->bindValue(":lama_bekerja", $lama_bekerja);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamin);
    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/*
 * Get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT * FROM teknisi WHERE id_teknisi = :id_teknisi";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_teknisi', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get jenis kelamin
 */
$stmJenisKelamin = $connection->prepare("SELECT * FROM jenis_kelamin where id = :id");
$stmJenisKelamin->bindValue(':id', $result['jenis_kelamin']);
$stmJenisKelamin->execute();
$resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat jenis kelamin 'Tidak diketahui'
 */
$jenis_kelamin = [
    'id' => $result['jenis_kelamin'],
    'jenis' => 'Tidak diketahui'
];
if ($resultJenisKelamin) {
    $jenis_kelamin = [
        'id' => $resultJenisKelamin['id'],
        'jenis' => $resultJenisKelamin['jenis']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'id_teknisi' => $result['id_teknisi'],
    'nama_lengkap' => $result['nama_lengkap'],
    'alamat' => $result['alamat'],
    'nomor_hp' => $result['nomor_hp'],
    'tanggal_lahir' => $result['tanggal_lahir'],
    'lama_bekerja' => $result['lama_bekerja'],
    'jenis_kelamin' => $jenis_kelamin,
     'created_at' => $result['created_at']
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);
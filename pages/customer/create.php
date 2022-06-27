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
$id_customer = $_POST['id_customer'] ?? '';
$nama_lengkap = $_POST['nama_lengkap'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$nomor_hp = $_POST['nomor_hp'] ?? '';
$tanggal_lahir = $_POST['tanggal_lahir'] ?? date('Y-m-d');
$jenis_kelamin = $_POST['jenis_kelamin'] ?? 0;
$teknisi = $_POST['teknisi'] ?? '';
$service = $_POST['service'] ?? '';
$tanggal_service = $_POST['tanggal_service'] ?? date('Y-m-d');

/**
 * Validation empty fields
 */
$isValidated = true;
if(empty($id_customer)){
    $reply['error'] = 'ID Customer harus di isi';
    $isValidated = false;
}
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
if(empty($jenis_kelamin)){
    $reply['error'] = 'Jenis Kelamin harus di isi';
    $isValidated = false;
}
if(empty($teknisi)){
    $reply['error'] = 'Teknisi harus di isi';
    $isValidated = false;
}
if(empty($service)){
    $reply['error'] = 'Service harus di isi';
    $isValidated = false;
}
if(empty($tanggal_service)){
    $reply['error'] = 'Tanggal Service harus di isi';
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
    $query = "INSERT INTO customer (id_customer, nama_lengkap, alamat, nomor_hp, tanggal_lahir, jenis_kelamin, teknisi, service, tanggal_service) 
VALUES (:id_customer, :nama_lengkap, :alamat, :nomor_hp, :tanggal_lahir, :jenis_kelamin, :teknisi, :service, :tanggal_service)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_customer", $id_customer);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":tanggal_lahir", $tanggal_lahir);
    $statement->bindValue(":jenis_kelamin", $jenis_kelamin);
    $statement->bindValue(":teknisi", $teknisi);
    $statement->bindValue(":service", $service);
    $statement->bindValue(":tanggal_service", $tanggal_service);
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
$getResult = "SELECT * FROM customer WHERE id_customer = :id_customer";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_customer', $id_customer);
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
 * Get teknisi
 */
$stmTeknisi = $connection->prepare("SELECT * FROM teknisi where id_teknisi = :id_teknisi");
$stmTeknisi->bindValue(':id_teknisi', $result['teknisi']);
$stmTeknisi->execute();
$resultTeknisi = $stmTeknisi->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat teknisi 'Tidak diketahui'
 */
$teknisi = [
    'id_teknisi' => $result['teknisi'],
    'nama_lengkap' => 'Tidak diketahui'
];
if ($resultTeknisi) {
    $teknisi = [
        'id_teknisi' => $resultTeknisi['id_teknisi'],
        'nama_lengkap' => $resultTeknisi['nama_lengkap']
    ];
}
/*
 * Get service
 */
$stmService = $connection->prepare("SELECT * FROM service where kode = :kode");
$stmService->bindValue(':kode', $result['service']);
$stmService->execute();
$resultService = $stmService->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat service 'Tidak diketahui'
 */
$service = [
    'kode' => $result['service'],
    'nama_lengkap' => 'Tidak diketahui'
];
if ($resultService) {
    $service = [
        'kode' => $resultService['kode'],
        'nama_service' => $resultService['nama_service']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'id_customer' => $result['id_customer'],
    'nama_lengkap' => $result['nama_lengkap'],
    'alamat' => $result['alamat'],
    'nomor_hp' => $result['nomor_hp'],
    'tanggal_lahir' => $result['tanggal_lahir'],
    'jenis_kelamin' => $jenis_kelamin,
    'teknisi' => $teknisi,
    'service' => $service,
    'tanggal_service' => $result['tanggal_service'],
    'createad_at' => $result['created_at']
];

/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);
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
$kode = $_POST['kode'] ?? '';
$nama_service = $_POST['nama_service'] ?? '';
$garansi = $_POST['garansi'] ?? '';
$harga_service = $_POST['harga_service'] ?? '';
$keterangan = $_POST['keterangan'] ?? '';
$supplier = $_POST['supplier']?? '';

/**
 * Validation int value
 */
$kodeFilter = filter_var($kode, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;

if(empty($nama_service)){
    $reply['error'] = 'Nama Service harus diisi';
    $isValidated = false;
}
if(empty($garansi)){
    $reply['error'] = 'Garansi harus di isi';
    $isValidated = false;
}
if(empty($harga_service)){
    $reply['error'] = 'Harga Service harus di isi';
    $isValidated = false;
}
if(empty($keterangan)){
    $reply['error'] = 'Keterangan harus di isi';
    $isValidated = false;
}
if(empty($supplier)){
    $reply['error'] = 'Supplier harus di isi';
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
    $query = "INSERT INTO service (kode, nama_service, garansi, harga_service, keterangan, supplier) 
VALUES (:kode, :nama_service, :garansi, :harga_service, :keterangan, :supplier)";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":kode", $kode);
    $statement->bindValue(":nama_service", $nama_service);
    $statement->bindValue(":garansi", $garansi);
    $statement->bindValue(":harga_service", $harga_service);
    $statement->bindValue(":keterangan", $keterangan);
    $statement->bindValue(":supplier", $supplier);
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
$getResult = "SELECT * FROM service WHERE kode = :kode";
$stm = $connection->prepare($getResult);
$stm->bindValue(':kode', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);

/*
 * Get supplier
 */
$stmSupplier = $connection->prepare("SELECT * FROM supplier where id = :id");
$stmSupplier->bindValue(':id', $result['supplier']);
$stmSupplier->execute();
$resultSupplier = $stmSupplier->fetch(PDO::FETCH_ASSOC);
/*
 * Defulat supplier 'Tidak diketahui'
 */
$supplier = [
    'id' => $result['supplier'],
    'nama_supplier' => 'Tidak diketahui'
];
if ($resultSupplier) {
    $supplier = [
        'id' => $resultSupplier['id'],
        'nama_supplier' => $result['nama_supplier']
    ];
}

/*
 * Transform result
 */
$dataFinal = [
    'kode' => $result['kode'],
    'nama_service' => $result['nama_service'],
    'garansi' => $result['garansi'],
    'harga_service' => $result['harga_service'],
    'keterangan' => $result['keterangan'],
    'supplier' => $supplier,
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
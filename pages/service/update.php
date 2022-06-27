<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 02/06/2022
 * Time: 20:07
 * @var $connection PDO
 */

/*
 * Validate http method
 */
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$kode = $formData['kode'] ?? '';
$nama_service = $formData['nama_service'] ?? '';
$garansi = $formData['garansi'] ?? '';
$harga_service = $formData['harga_service'] ?? '';
$keterangan = $formData['keterangan'] ?? '';
$supplier = $formData['supplier'] ?? '';

/**
 * Validation int value
 */
$kodeFilter = filter_var($kode, FILTER_VALIDATE_INT);

/**
 * Validation empty fields
 */
$isValidated = true;
if($kodeFilter === false){
    $reply['error'] = "kode harus format INT";
    $isValidated = false;
}
if(empty($kode)){
    $reply['error'] = 'kode harus di isi';
    $isValidated = false;
}
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM service where kode = :kode";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode', $kodeFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan kode'.$kodeFilter;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE service SET nama_service = :nama_service, garansi = :garansi, harga_service = :harga_service, keterangan = :keterangan, supplier = :supplier
WHERE kode = :kode";
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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM service where kode = :kode");
$stmSelect->bindValue(':kode', $kodeFilter);
$stmSelect->execute();
$dataService = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data supplier berdasarkan kolom supplier
 */
$dataFinal = [];
if($dataService) {
    $stmSupplier = $connection->prepare("select * from supplier where id = :id");
    $stmSupplier->bindValue(':id', $dataService['supplier']);
    $stmSupplier->execute();
    $resultSupplier = $stmSupplier->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat supplier 'Tidak diketahui'
     */
    $supplier = [
        'id' => $dataService['supplier'],
        'nama_supplier' => 'Tidak diketahui'
    ];
    if ($resultSupplier) {
        $supplier = [
            'id' => $resultSupplier['id'],
            'nama_supplier' => $resultSupplier['supplier']
        ];
    }

    /*
     * Transoform hasil query dari table service dan supplier
     * Gabungkan data berdasarkan kolom id supplier
     * Jika id supplier tidak ditemukan, default "tidak diketahui'
     *
     */

            $dataFinal = [
                'kode' => $dataService['kode'],
                'nama_service' => $dataService['nama_service'],
                'harga_service' => $dataService['harga_service'],
                'garansi' => $dataService['garansi'],
                'keterangan' => $dataService['keterangan'],
                'supplier' => $supplier,
                'created_at' => $dataService['created_at']
            ];
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);
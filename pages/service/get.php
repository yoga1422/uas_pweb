<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 09/06/2022
 * Time: 16:19
 * @var $connection PDO
 */

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'GET'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

$dataFinal = [];
$kode = $_GET['kode'] ?? '';

if(empty($kode)){
    $reply['error'] = 'kode tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM service where kode = :kode";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode', $kode);
    $statement->execute();
    $dataService = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin berdasarkan kolom jenis_kelamin
     */
    if($dataService) {
        $stmSupplier = $connection->prepare("select * from supplier where id = :id");
        $stmSupplier->bindValue(':id', $dataService['supplier']);
        $stmSupplier->execute();
        $resultSupplier = $stmSupplier->fetch(PDO::FETCH_ASSOC);
        /*
         * Default supplier 'Tidak diketahui'
         */
        $supplier = [
            'id' => $dataService['supplier'],
            'nama_supplier' => 'Tidak diketahui'
        ];
        if ($resultSupplier) {
            $supplier = [
                'id' => $resultSupplier['id'],
                'nama_supplier' => $resultSupplier['nama_supplier']
            ];
        }

        /*
         * Transoform hasil query dari table service dan supplier
         * Gabungkan data berdasarkan kolom id supplier
         * Jika id supplier tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'kode' => $dataService['kode'],
            'nama_service' => $dataService['nama_service'],
            'garansi' => $dataService['garansi'],
            'harga_service' => $dataService['harga_service'],
            'keterangan' => $dataService['keterangan'],
            'supplier' => $supplier,
            'createad_at' => $dataService['created_at']
        ];
    }

}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Show response
 */
if(!$dataFinal){
    $reply['error'] = 'Data tidak ditemukan kode '.$kode;
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Otherwise show data
 */
$reply['status'] = true;
$reply['data'] = $dataFinal;
header('Content-Type: application/json');
echo json_encode($reply);
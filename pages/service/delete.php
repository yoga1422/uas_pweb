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
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$kode = $res['kode'] ?? '';
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
    $reply['error'] = 'kode harus diisi';
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
 *
 * Cek apakah ID Teknisi tersedia
 */
try{
    $queryCheck = "SELECT * FROM service where kode = :kode";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode', $kode);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan kdoe '.$kode;
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
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM service where kode = :kode";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':kode', $kode);
    $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
$reply['status'] = true;
echo json_encode($reply);
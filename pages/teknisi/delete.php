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
$id_teknisi = $res['id_teknisi'] ?? '';
/**
 * Validation int value
 */
$id_teknisiFilter = filter_var($id_teknisi, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($id_teknisiFilter === false){
    $reply['error'] = "ID Teknisi harus format INT";
    $isValidated = false;
}
if(empty($id_teknisi)){
    $reply['error'] = 'ID Teknisi harus diisi';
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
    $queryCheck = "SELECT * FROM teknisi where id_teknisi = :id_teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_teknisi', $id_teknisi);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Teknisi '.$id_teknisi;
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
    $queryCheck = "DELETE FROM teknisi where id_teknisi = :id_teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_teknisi', $id_teknisi);
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
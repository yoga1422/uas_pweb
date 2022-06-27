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
$id_teknisi = $_GET['id_teknisi'] ?? '';

if(empty($id_teknisi)){
    $reply['error'] = 'ID Teniksi tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM teknisi where id_teknisi = :id_teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_teknisi', $id_teknisi);
    $statement->execute();
    $dataTeknisi = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin berdasarkan kolom jenis_kelamin
     */
    if($dataTeknisi) {
        $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
        $stmJenisKelamin->bindValue(':id', $dataTeknisi['jenis_kelamin']);
        $stmJenisKelamin->execute();
        $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $dataTeknisi['jenis_kelamin'],
            'jenis' => 'Tidak diketahui'
        ];
        if ($resultJenisKelamin) {
            $jenis_kelamin = [
                'id' => $resultJenisKelamin['id'],
                'jenis' => $resultJenisKelamin['jenis']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan jenis_kelamin
         * Gabungkan data berdasarkan kolom id jenis_kelamin
         * Jika id jenis kelamin tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_teknisi' => $dataTeknisi['id_teknisi'],
            'nama_lengkap' => $dataTeknisi['nama_lengkap'],
            'alamat' => $dataTeknisi['alamat'],
            'nomor_hp' => $dataTeknisi['nomor_hp'],
            'tanggal_lahir' => $dataTeknisi['tanggal_lahir'],
            'lama_bekerja' => $dataTeknisi['lama_bekerja'],
            'jenis_kelamin' => $jenis_kelamin,
            'createad_at' => $dataTeknisi['created_at']
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
    $reply['error'] = 'Data tidak ditemukan ID Teknisi '.$id_teknisi;
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
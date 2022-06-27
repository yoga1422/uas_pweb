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
$id_customer = $_GET['id_customer'] ?? '';

if(empty($id_customer)){
    $reply['error'] = 'ID Customer tidak boleh kosong';
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

try{
    $queryCheck = "SELECT * FROM customer where id_customer = :id_customer";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_customer', $id_customer);
    $statement->execute();
    $dataCustomer = $statement->fetch(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin berdasarkan kolom jenis_kelamin
     */
    if($dataCustomer) {
        $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
        $stmJenisKelamin->bindValue(':id', $dataCustomer['jenis_kelamin']);
        $stmJenisKelamin->execute();
        $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $dataCustomer['jenis_kelamin'],
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
            'id_customer' => $dataCustomer['id_customer'],
            'nama_lengkap' => $dataCustomer['nama_lengkap'],
            'alamat' => $dataCustomer['alamat'],
            'nomor_hp' => $dataCustomer['nomor_hp'],
            'tanggal_lahir' => $dataCustomer['tanggal_lahir'],
            'jenis_kelamin' => $jenis_kelamin,
            'tanggal_service' => $dataCustomer['tanggal_service'],
            'createad_at' => $dataCustomer['created_at']
        ];
    }
    /*
     * Ambil data teknisi berdasarkan kolom teknisi
     */
    if($dataCustomer) {
        $stmTeknisi = $connection->prepare("select * from teknisi where id_teknisi = :id_teknisi");
        $stmTeknisi->bindValue(':id_teknisi', $dataCustomer['teknisi']);
        $stmTeknisi->execute();
        $resultTeknisi = $stmTeknisi->fetch(PDO::FETCH_ASSOC);
        /*
         * Default teknisi 'Tidak diketahui'
         */
        $teknisi = [
            'id_teknisi' => $dataCustomer['teknisi'],
            'nama_lengkap' => 'Tidak diketahui'
        ];
        if ($resultTeknisi) {
            $teknisi = [
                'id_teknisi' => $resultTeknisi['id_teknisi'],
                'nama_lengkap' => $resultTeknisi['nama_lengkap']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan teknisi
         * Gabungkan data berdasarkan kolom id teknisi
         * Jika id teknisi tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_customer' => $dataCustomer['id_customer'],
            'nama_lengkap' => $dataCustomer['nama_lengkap'],
            'alamat' => $dataCustomer['alamat'],
            'nomor_hp' => $dataCustomer['nomor_hp'],
            'tanggal_lahir' => $dataCustomer['tanggal_lahir'],
            'jenis_kelamin' => $jenis_kelamin,
            'teknisi' => $teknisi,
            'tanggal_service' => $dataCustomer['tanggal_service'],
            'createad_at' => $dataCustomer['created_at']
        ];
    }
    /*
 * Ambil data service berdasarkan kolom service
 */
    if($dataCustomer) {
        $stmService = $connection->prepare("select * from service where kode = :kode");
        $stmService->bindValue(':kode', $dataCustomer['service']);
        $stmService->execute();
        $resultService = $stmService->fetch(PDO::FETCH_ASSOC);
        /*
         * Default service 'Tidak diketahui'
         */
        $service = [
            'kode' => $dataCustomer['service'],
            'nama_service' => 'Tidak diketahui'
        ];
        if ($resultService) {
            $service = [
                'kode' => $resultService['kode'],
                'nama_service' => $resultService['nama_service']
            ];
        }

        /*
         * Transoform hasil query dari table customer dan teknisi
         * Gabungkan data berdasarkan kolom id teknisi
         * Jika id teknisi tidak ditemukan, default "tidak diketahui'
         */
        $dataFinal = [
            'id_customer' => $dataCustomer['id_customer'],
            'nama_lengkap' => $dataCustomer['nama_lengkap'],
            'alamat' => $dataCustomer['alamat'],
            'nomor_hp' => $dataCustomer['nomor_hp'],
            'tanggal_lahir' => $dataCustomer['tanggal_lahir'],
            'jenis_kelamin' => $jenis_kelamin,
            'teknisi' => $teknisi,
            'service' => $service,
            'tanggal_service' => $dataCustomer['tanggal_service'],
            'createad_at' => $dataCustomer['created_at']
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
    $reply['error'] = 'Data tidak ditemukan ID Customer '.$id_customer;
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
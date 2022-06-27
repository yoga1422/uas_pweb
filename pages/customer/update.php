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

$id_customer = $formData['id_customer'] ?? '';
$nama_lengkap = $formData['nama_lengkap'] ?? '';
$alamat = $formData['alamat'] ?? '';
$nomor_hp = $formData['nomor_hp'] ?? '';
$tanggal_lahir = $formData['tanggal_lahir'] ?? date('Y-m-d');
$idjenis_kelamin = $formData['jenis_kelamin'] ?? 0;
$idteknisi = $formData['teknisi'] ?? '';
$idservice = $formData['service'] ?? '';
$tanggal_service = $formData['tanggal_service'] ?? date('Y-m-d');

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
if(empty($idjenis_kelamin)){
    $reply['error'] = 'Jenis Kelamin harus di isi';
    $isValidated = false;
}
if(empty($idteknisi)){
    $reply['error'] = 'Teknisi harus di isi';
    $isValidated = false;
}
if(empty($idservice)){
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM customer where id_customer = :id_customer";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_customer', $id_customer);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Customer '.$id_customer;
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
    $query = "UPDATE customer SET nama_lengkap = :nama_lengkap, alamat = :alamat, nomor_hp = :nomor_hp, tanggal_lahir = :tanggal_lahir, jenis_kelamin = :jenis_kelamin, teknisi = :teknisi, service = :service, tanggal_service = :tanggal_service
WHERE id_customer = :id_customer";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */  $statement->bindValue(":id_customer", $id_customer);
    $statement->bindValue(":nama_lengkap", $nama_lengkap);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":nomor_hp", $nomor_hp);
    $statement->bindValue(":tanggal_lahir", $tanggal_lahir);
    $statement->bindValue(":jenis_kelamin", $idjenis_kelamin);
    $statement->bindValue(":teknisi", $idteknisi);
    $statement->bindValue(":service", $idservice);
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
 * Get data
 */
$stmSelect = $connection->prepare("SELECT * FROM customer where id_customer = :id_customer");
$stmSelect->bindValue(':id_customer', $id_customer);
$stmSelect->execute();
$dataCustomer = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data jenis kelamin berdasarkan kolom jenis kelamin
 */
$dataFinal = [];
if($dataCustomer) {
    $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
    $stmJenisKelamin->bindValue(':id', $dataCustomer['jenis_kelamin']);
    $stmJenisKelamin->execute();
    $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat jenis kelamin 'Tidak diketahui'
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
     *
     */
    /*
 * Ambil data jenis kelamin berdasarkan kolom jenis kelamin
 */
    $dataFinal = [];
    if ($dataCustomer) {
        $stmTeknisi = $connection->prepare("select * from teknisi where id_teknisi = :id_teknisi");
        $stmTeknisi->bindValue(':id_teknisi', $dataCustomer['teknisi']);
        $stmTeknisi->execute();
        $resultTeknisi = $stmTeknisi->fetch(PDO::FETCH_ASSOC);
        /*
         * Defulat teknisi 'Tidak diketahui'
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
         * Gabungkan data berdasarkan kolom id_teknisi teknisi
         * Jika id_teknisi teknisi tidak ditemukan, default "tidak diketahui'
         */

        /*
* Ambil data service berdasarkan kolom service
*/
        $dataFinal = [];
        if ($dataCustomer) {
            $stmService = $connection->prepare("select * from service where kode = :kode");
            $stmService->bindValue(':kode', $dataCustomer['service']);
            $stmService->execute();
            $resultService = $stmService->fetch(PDO::FETCH_ASSOC);
            /*
             * Defulat service 'Tidak diketahui'
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
             * Transoform hasil query dari table customer dan service
             * Gabungkan data berdasarkan kolom kode service
             * Jika kode service tidak ditemukan, default "tidak diketahui'
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
    }
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);
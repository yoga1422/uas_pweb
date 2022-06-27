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

$id_teknisi = $formData['id_teknisi'] ?? '';
$nama_lengkap = $formData['nama_lengkap'] ?? '';
$alamat = $formData['alamat'] ?? '';
$nomor_hp = $formData['nomor_hp'] ?? '';
$tanggal_lahir = $formData['tanggal_lahir'] ?? date('Y-m-d');
$lama_bekerja = $formData['lama_bekerja'] ?? '';
$idjenis_kelamin = $formData['jenis_kelamin'] ?? 0;

/**
 * Validation int value
 */
$id_teknisiFilter = filter_var($id_teknisi, FILTER_VALIDATE_INT);
/**
 * Validation empty fields
 */
$isValidated = true;
if($id_teknisiFilter === false){
    $reply['error'] = "id_teknisi harus format INT";
    $isValidated = false;
}
if(empty($id_teknisi)){
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
if(empty($lama_bekerja)){
    $reply['error'] = 'Lama Bekerja harus di isi';
    $isValidated = false;
}
if(empty($idjenis_kelamin)){
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
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */
try{
    $queryCheck = "SELECT * FROM teknisi where id_teknisi = :id_teknisi";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_teknisi', $id_teknisiFilter);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan ID Teknisi '.$id_teknisiFilter;
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
    $query = "UPDATE teknisi SET nama_lengkap = :nama_lengkap, alamat = :alamat, nomor_hp = :nomor_hp, tanggal_lahir = :tanggal_lahir, lama_bekerja = :lama_bekerja, jenis_kelamin = :jenis_kelamin
WHERE id_teknisi = :id_teknisi";
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
    $statement->bindValue(":jenis_kelamin", $idjenis_kelamin);
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
$stmSelect = $connection->prepare("SELECT * FROM teknisi where id_teknisi = :id_teknisi");
$stmSelect->bindValue(':id_teknisi', $id_teknisiFilter);
$stmSelect->execute();
$dataTeniksi = $stmSelect->fetch(PDO::FETCH_ASSOC);

/*
 * Ambil data jenis kelamin berdasarkan kolom jenis kelamin
 */
$dataFinal = [];
if($dataTeniksi) {
    $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin where id = :id");
    $stmJenisKelamin->bindValue(':id', $dataTeniksi['jenis_kelamin']);
    $stmJenisKelamin->execute();
    $resultJenisKelamin = $stmJenisKelamin->fetch(PDO::FETCH_ASSOC);
    /*
     * Defulat jenis kelamin 'Tidak diketahui'
     */
    $jenis_kelamin = [
        'id' => $dataTeniksi['jenis_kelamin'],
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

            $dataFinal = [
                'id_teknisi' => $dataTeniksi['id_teknisi'],
                'nama_lengkap' => $dataTeniksi['nama_lengkap'],
                'alamat' => $dataTeniksi['alamat'],
                'nomor_hp' => $dataTeniksi['nomor_hp'],
                'tanggal_lahir' => $dataTeniksi['tanggal_lahir'],
                'lama_bekerja' => $dataTeniksi['lama_bekerja'],
                'jenis_kelamin' => $jenis_kelamin,
                'created_at' => $dataTeniksi['created_at']
            ];
}

/**
 * Show output to client
 */
$reply['data'] = $dataFinal;
$reply['status'] = $isOk;
header('Content-Type: application/json');
echo json_encode($reply);
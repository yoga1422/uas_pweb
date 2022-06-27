<?php
include '../../config/koneksi.php';
/**
 * Created by Pizaini <pizaini@uin-suska.ac.id>
 * Date: 31/05/2022
 * Time: 15:22
 * @var $connection PDO
 */
try{
    /**
     * Prepare query customer limit 50 rows
     */
    $statement = $connection->prepare("select * from teknisi order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsTeknisi = $statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data jenis kelamin
     */
    $stmJenisKelamin = $connection->prepare("select * from jenis_kelamin");
    $isOk = $stmJenisKelamin->execute();
    $resultJenisKelamin = $stmJenisKelamin->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table customer dan jenis_kelamin
     * Gabungkan data berdasarkan kolom id jenis_kelamin
     * Jika id jenis_kelamin tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsJenisKelamin = array_column($resultJenisKelamin, 'id');
    foreach ($resultsTeknisi as $teknisi) {
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $teknisi['jenis_kelamin'],
            'jenis' => 'Tidak diketahui'
        ];
        /*
         * Cari jenis kelamin berd id
         */
        $findByIdJenisKelamin = array_search($teknisi['jenis_kelamin'], $idsJenisKelamin);

        /*
         * Jika id ditemukan
         */
        if ($findByIdJenisKelamin !== false) {
            $findDataJenisKelamin = $resultJenisKelamin[$findByIdJenisKelamin];
            $jenis_kelamin = [
                'id' => $findDataJenisKelamin['id'],
                'jenis' => $findDataJenisKelamin['jenis']
            ];
        }

                $finalResults[] = [
                    'id_teknisi' => $teknisi['id_teknisi'],
                    'nama_lengkap' => $teknisi['nama_lengkap'],
                    'alamat' => $teknisi['alamat'],
                    'nomor_hp' => $teknisi['nomor_hp'],
                    'tanggal_lahir' => $teknisi['tanggal_lahir'],
                    'lama_bekerja' => $teknisi['lama_bekerja'],
                    'jenis_kelamin' => $jenis_kelamin,
                    'created_at' => $teknisi['created_at']
                ];
            }

    $reply['data'] = $finalResults;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
$reply['status'] = true;
echo json_encode($reply);
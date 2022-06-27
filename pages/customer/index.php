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
    $statement = $connection->prepare("select * from customer order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsCustomer = $statement->fetchAll(PDO::FETCH_ASSOC);

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
    foreach ($resultsCustomer as $customer) {
        /*
         * Default jenis kelamin 'Tidak diketahui'
         */
        $jenis_kelamin = [
            'id' => $customer['jenis_kelamin'],
            'jenis' => 'Tidak diketahui'
        ];
        /*
         * Cari jenis kelamin berd id
         */
        $findByIdJenisKelamin = array_search($customer['jenis_kelamin'], $idsJenisKelamin);

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
        /*
            * Ambil data teknisi
            */
        $stmTeknisi = $connection->prepare("select * from teknisi");
        $isOk = $stmTeknisi->execute();
        $resultTeknisi = $stmTeknisi->fetchAll(PDO::FETCH_ASSOC);

        /*
         * Transoform hasil query dari table customer dan teknisi
         * Gabungkan data berdasarkan kolom id_teknisi teknisi
         * Jika id_teknisi teknisi tidak ditemukan, default "tidak diketahui'
         */
        $finalResults = [];
        $idsTeknisi = array_column($resultTeknisi, 'id_teknisi');
        foreach ($resultsCustomer as $customer) {
            /*
             * Default teknisi 'Tidak diketahui'
             */
            $teknisi = [
                'id_teknisi' => $customer['teknisi'],
                'nama_lengkap' => 'Tidak diketahui'
            ];
            /*
             * Cari teknisi berd id_teknisi
             */
            $findByIdTeknisi = array_search($customer['teknisi'], $idsTeknisi);

            /*
             * Jika id_teknisi ditemukan
             */
            if ($findByIdTeknisi !== false) {
                $findDataTeknisi = $resultTeknisi[$findByIdTeknisi];
                $teknisi = [
                    'id_teknisi' => $findDataTeknisi['id_teknisi'],
                    'nama_lengkap' => $findDataTeknisi['nama_lengkap']
                ];
            }
            /*
         * Ambil data teknisi
         */
            $stmService = $connection->prepare("select * from service");
            $isOk = $stmService->execute();
            $resultService = $stmService->fetchAll(PDO::FETCH_ASSOC);

            /*
             * Transoform hasil query dari table customer dan service
             * Gabungkan data berdasarkan kolom kode service
             * Jika kode kode tidak ditemukan, default "tidak diketahui'
             */
            $finalResults = [];
            $idsService = array_column($resultService, 'kode');
            foreach ($resultsCustomer as $customer) {
                /*
                 * Default service 'Tidak diketahui'
                 */
                $service = [
                    'kode' => $customer['service'],
                    'nama_service' => 'Tidak diketahui'
                ];
                /*
                 * Cari service berd kode
                 */
                $findByIdService = array_search($customer['service'], $idsService);

                /*
                 * Jika kode ditemukan
                 */
                if ($findByIdService !== false) {
                    $findDataService = $resultService[$findByIdService];
                    $service = [
                        'kode' => $findDataService['kode'],
                        'nama_service' => $findDataService['nama_service']
                    ];
                }
                $finalResults[] = [
                    'id_customer' => $customer['id_customer'],
                    'nama_lengkap' => $customer['nama_lengkap'],
                    'alamat' => $customer['alamat'],
                    'nomor_hp' => $customer['nomor_hp'],
                    'tanggal_lahir' => $customer['tanggal_lahir'],
                    'jenis_kelamin' => $jenis_kelamin,
                    'teknisi' => $teknisi,
                    'service' => $service,
                    'tanggal_service' => $customer['tanggal_service'],
                    'created_at' => $customer['created_at']
                ];
            }
        }
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
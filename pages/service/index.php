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
    $statement = $connection->prepare("select * from service order by created_at desc limit 50");
    $isOk = $statement->execute();
    $resultsService =$statement->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Ambil data supplier
     */
    $stmSupplier = $connection->prepare("select * from supplier");
    $isOk = $stmSupplier->execute();
    $resultSupplier = $stmSupplier->fetchAll(PDO::FETCH_ASSOC);

    /*
     * Transoform hasil query dari table service dan supplier
     * Gabungkan data berdasarkan kolom id supplier
     * Jika id supplier tidak ditemukan, default "tidak diketahui'
     */
    $finalResults = [];
    $idsSupplier = array_column($resultSupplier, 'id');
    foreach ($resultsService as $service) {
        /*
         * Default supplier 'Tidak diketahui'
         */
        $supplier = [
            'id' => $service['supplier'],
            'nama_supplier' => 'Tidak diketahui'
        ];
        /*
         * Cari supplier berd id
         */
        $findByIdSupplier = array_search($service['supplier'], $idsSupplier);

        /*
         * Jika id ditemukan
         */
        if ($findByIdSupplier !== false) {
            $findDataSupplier = $resultSupplier[$findByIdSupplier];
            $supplier = [
                'id' => $findDataSupplier['id'],
                'nama_supplier' => $findDataSupplier['nama_supplier']
            ];
        }

                $finalResults[] = [
                    'kode' => $service['kode'],
                    'nama_service' => $service['nama_service'],
                    'garansi' => $service['garansi'],
                    'harga_service' => $service['harga_service'],
                    'keterangan' => $service['keterangan'],
                    'supplier' => $supplier,
                    'created_at' => $service['created_at']
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
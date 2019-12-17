<?php
/**
 * @link https://www.inlislite.perpusnas.go.id/
 * @copyright Copyright (c) 2015 Perpustakaan Nasional Republik Indonesia
 * @license https://www.inlislite.perpusnas.go.id/licences
 */

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;


// Model
use common\models\Memberguesses;
use common\models\VPertumbJmlKunjunganBulanan;
use common\models\JenisAnggota;

// Component


/**
* StatistikPerkembanganPerpustakaanController implements the create actions for Members model.
* @author 
*/

class StatistikPerkembanganPerpustakaanController extends Controller
{
    public $layout = 'base-layout';

	/**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        // $stTahun = VPertumbJmlKunjunganBulanan::find()->select('tahun')->groupBy('tahun')->all();

        // $tahun = 2015;
        $tahun = date("Y");

        $nowMinOnemonth = mktime(0, 0, 0, date("m"), 0, date("Y"));
        $nowMinOneYear = mktime(0, 0, 0, date("m")-12, 1, date("Y"));
        

        $content['tahun'] = $tahun; 
       

        // Untuk statistik pertumbuhan Jumlah Anggota Perjenis
        $PerJmlAnggota = JenisAnggota::find()->select('jenisanggota')->asArray()->all(); 
        foreach($PerJmlAnggota as $jenis)
        {
            // echo $jenis;die;
            for ($i=11; $i > -1; $i--) 
            { 
                if($i==11)
                {
                    $date = mktime(0, 0, 0, date("m")-($i+1), 1, date("Y"));  // waktu saat ini dikurang 1 bulan
                }
                else
                {
                    $date = mktime(0, 0, 0, date("m")-$i, 0, date("Y"));  // waktu saat ini dikurang 1 bulan
                }
                
                $bln = date('m', $date);
                $thn = date('Y', $date);

                $sqlPerJmlAnggota = "SELECT * FROM  v_stat_anggota WHERE jenisanggota = '".$jenis['jenisanggota']."' and tahun = ".$thn." and bulan = ".$bln; 

                $pertumbuhananggotaVal = Yii::$app->db->createCommand($sqlPerJmlAnggota)->queryOne();

                $dataJumlahAnggota[$jenis['jenisanggota']][] = intval($pertumbuhananggotaVal['jumlah']);

            }
        }


        foreach ($dataJumlahAnggota as $key => $value) {
            $totalJenis[] = [
                'name' => $key,
                'data' => $value
            ];
        }

        $content['totalJenis'] = $totalJenis;
        // !Untuk statistik pertumbuhan Jumlah Anggota Perjenis


        // print_r($PerJmlAnggota);die;


        $databulan = array();
        
        for ($i=11; $i > -1; $i--) 
        { 

            if($i==11)
            {
                $date = mktime(0, 0, 0, date("m")-($i+1), 1, date("Y"));  // waktu saat ini dikurang 1 bulan
            }
            else
            {
                $date = mktime(0, 0, 0, date("m")-$i, 0, date("Y"));  // waktu saat ini dikurang 1 bulan
            }
            
            $bln = date('m', $date);
            $thn = date('Y', $date);
            
            //echo '<br/>'.date('Y-m-d', $date);                
            // echo $bln;
            // echo '-'.$thn.'<br/>';


            //Untuk Statistik Jumlah Kunjungan
            // echo 'tahun'.$thn.'bulan'.$bln.'<br>';
            $nonanggota = VPertumbJmlKunjunganBulanan::find()->where(['kriteria'=>'NONANGGOTA','tahun'=>$thn,'bulan'=>$bln])->one();
            $valNonAnggota[] = intval($nonanggota['jumlah']);

            $anggota = VPertumbJmlKunjunganBulanan::find()->where(['kriteria'=>'ANGGOTA','tahun'=>$thn,'bulan'=>$bln])->one();
            $valAnggota[] = intval($anggota['jumlah']);

            $rombongan = VPertumbJmlKunjunganBulanan::find()->where(['kriteria'=>'ROMBONGAN','tahun'=>$thn,'bulan'=>$bln])->one();
            $valRombongan[] = intval($rombongan['jumlah']);

            $catbulan[] = date('M', $date) .' - '. $thn;




            //Untuk Statistik Jumlah Koleksi
            $sqlPerJmlKoleksi = "SELECT * FROM v_stat_jumlah_koleksi WHERE tahun = ".$thn." and bulan = ".$bln;
            $KoleksiVal = Yii::$app->db->createCommand($sqlPerJmlKoleksi)->queryOne(); 
            // $pertumbuhanKoleksi[] = intval($KoleksiVal['jumlah']);
            
            $koleksiJudul[] = intval($KoleksiVal['jumlah_judul']);
            $koleksiEksemplar[] = intval($KoleksiVal['jumlah_eksemplar']);
            $koleksiDigital[] = intval($KoleksiVal['jumlah_dijital']);


            // //Untuk Statistik Jumlah Koleksi dipinjam
            // // ** Eksemplar
            $sqlKoleksiDipinjamEksemplar = " SELECT 
                YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) AS `tahun`,
                MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) AS `bulan`,
                COUNT(DISTINCT `colit`.`Collection_id`) AS `jumlah_eksemplar`
                FROM
                `collectionloanitems` `colit`
                WHERE
                (`colit`.`CreateDate` BETWEEN ('".date('Y-m-d', $nowMinOneYear)."') AND ('".date('Y-m-d', $nowMinOnemonth)."'))
                AND YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) = ".$thn."
                AND MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) = ".$bln."
                GROUP BY YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) , MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d'))
            "; 
            // echo "<br/>";
            $koleksiDipinjamEksemplarVal = Yii::$app->db->createCommand($sqlKoleksiDipinjamEksemplar)->queryOne();
            $koleksiDipinjamEksemplar[] = intval($koleksiDipinjamEksemplarVal['jumlah_eksemplar']);


            // ** Judul
            // $sqlKoleksiDipinjamJudul = "SELECT * FROM  v_stat_koleksi_dipinjam_judul WHERE tahun = ".$thn." and bulan = ".$bln; 
            $sqlKoleksiDipinjamJudul = " SELECT 
                YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) AS `tahun`,
                MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) AS `bulan`,
                COUNT(DISTINCT `cole`.`Catalog_id`) AS `jumlah_judul`
                FROM
                `collectionloanitems` `colit`
                INNER JOIN `collections` `cole` ON `colit`.`Collection_id` = `cole`.`ID`
                WHERE
                (`colit`.`CreateDate` BETWEEN ('".date('Y-m-d', $nowMinOneYear)."') AND ('".date('Y-m-d', $nowMinOnemonth)."'))
                AND YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) =  ".$thn."
                AND MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) =  ".$bln."
                GROUP BY YEAR(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d')) , MONTH(STR_TO_DATE(`colit`.`CreateDate`, '%Y-%m-%d'))
                "; 
            $koleksiDipinjamJudulVal = Yii::$app->db->createCommand($sqlKoleksiDipinjamJudul)->queryOne();
            $koleksiDipinjamJudul[] = intval($koleksiDipinjamJudulVal['jumlah_judul']);






            // //Untuk Statistik Jumlah Koleksi DIbaca Ditempat
            // // ** Eksemplar
            $sqlKoleksiDibacaEksemplar = "  SELECT 
                    YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) AS `tahun`,
                    MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) AS `bulan`,
                    COUNT(DISTINCT `baca`.`collection_id`) AS `jumlah_eksemplar`
                    FROM
                    `bacaditempat` `baca`
                    WHERE
                      (`baca`.`CreateDate` BETWEEN ('".date('Y-m-d', $nowMinOneYear)."') AND ('".date('Y-m-d', $nowMinOnemonth)."'))
                     AND YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) = ".$thn."
                     AND MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) = ".$bln."
                    GROUP BY YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) , MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d'))
            "; 
            // echo "<br/>";
            $koleksiDibacaEksemplarVal = Yii::$app->db->createCommand($sqlKoleksiDibacaEksemplar)->queryOne();
            $koleksiDibacaEksemplar[] = intval($koleksiDibacaEksemplarVal['jumlah_eksemplar']);



            // ** Judul
            $sqlKoleksiDibacaJudul = "  SELECT 
                YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) AS `tahun`,
                MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) AS `bulan`,
                COUNT(DISTINCT `cole`.`Catalog_id`) AS `jumlah_judul`
                FROM
                `bacaditempat` `baca`
                INNER JOIN `collections` `cole` ON `baca`.`collection_id` = `cole`.`ID`
                WHERE
                  (`baca`.`CreateDate` BETWEEN ('".date('Y-m-d', $nowMinOneYear)."') AND ('".date('Y-m-d', $nowMinOnemonth)."'))
                 AND YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) = ".$thn."
                 AND MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) = ".$bln."
                GROUP BY YEAR(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d')) , MONTH(STR_TO_DATE(`baca`.`CreateDate`, '%Y-%m-%d'))
                "; 
            $koleksiDibacaJudulVal = Yii::$app->db->createCommand($sqlKoleksiDibacaJudul)->queryOne();
            $koleksiDibacaJudul[] = intval($koleksiDibacaJudulVal['jumlah_judul']);







        }//die;
        
        // Category bulanan
        $content['catbulan'] = $catbulan; 
        $content['rangeTahun'] = $catbulan[0].' s/d '.$catbulan[11];

        //Untuk Statistik Jumlah Kunjungan
        $content['valNonAnggota'] = $valNonAnggota; 
        $content['valAnggota'] = $valAnggota;        
        $content['valRombongan'] = $valRombongan; 
        // Untuk Statistik Jumlah Anggota
        $content['pertumbuhananggota'] = $pertumbuhananggota; 

        //Untuk Statistik Jumlah Koleksi
        $content['jumlahKoleksi'] =  array(
            [
                'name'=> yii::t('app', 'Judul'),
                'data' => array_values($koleksiJudul)
            ],
            [
                'name'=> yii::t('app', 'Eksemplar'),
                'data' => array_values($koleksiEksemplar)
            ],
            [
                'name'=> yii::t('app', 'Konten Digital'),
                'data' => array_values($koleksiDigital)
            ]
        ); 






        //Untuk Statistik Jumlah Koleksi Dipinjam
        $content['jumlahKoleksiDipinjam'] =  array(
            [
            'name'=> yii::t('app', 'Judul'),
            'data' => array_values($koleksiDipinjamJudul)
            ],
            [
            'name'=> yii::t('app', 'Eksemplar'),
            'data' => array_values($koleksiDipinjamEksemplar)
            ],
            // [
            // 'name'=>'Konten Digital',
            // 'data' => array_values($koleksiDigital)
            // ]
        ); 

        //Untuk Statistik Jumlah Koleksi Dibaca
        $content['jumlahKoleksiDibaca'] =  array(
            [
            'name'=> yii::t('app', 'Judul'),
            'data' => array_values($koleksiDibacaJudul)
            ],
            [
            'name'=> yii::t('app', 'Eksemplar'),
            'data' => array_values($koleksiDibacaEksemplar)
            ],
            // [
            // 'name'=>'Konten Digital',
            // 'data' => array_values($koleksiDigital)
            // ]
        ); 
            // 'data' => [123,321,123,312,123,312,31,23,123,123,123]

        //Untuk Statistik Range Umur
        // $sqlRangeUmur = "SELECT * FROM v_stat_rangeumur_kunj WHERE tahun = ".$tahun;
        $sqlRangeUmur = "call get_stat_range_umur('".date('Y-m-d', $nowMinOneYear)."', '".date('Y-m-d', $nowMinOnemonth)."');";
        $rangeUmur = Yii::$app->db->createCommand($sqlRangeUmur)->queryAll(); 

        // echo $sqlRangeUmur;die;

        $isiUmur = array();
        foreach ($rangeUmur as $rangeUmur) {
            array_push($isiUmur,['name' => $rangeUmur['Keterangan'],'y'=> intval($rangeUmur['Jumlah']) ]);
        }
        $content['rangeUmur'] = $isiUmur; 
        
        // //Untuk Statistik Jumlah Koleksi
       
        // Untuk Statistik Jenis Pendidikan
        $sqlJenisPendidikan = "call get_stat_jenis_pendidikan('".date('Y-m-d', $nowMinOneYear)."', '".date('Y-m-d', $nowMinOnemonth)."');";
        // $sqlJenisPendidikan = "SELECT * FROM  v_stat_jenis_pendidikan WHERE tahun = ".$tahun;
        $jenisPendidikan = Yii::$app->db->createCommand($sqlJenisPendidikan)->queryAll(); 
        $isiPendidikan = array();
        foreach ($jenisPendidikan as $jenisPendidikan) {
            $isiPendidikan[] = [$jenisPendidikan['Keterangan'], intval($jenisPendidikan['Jumlah']) ];
        }
        // $isiPendidikan[] = ['name' => 'Proprietary or Undetectable','y' => 0.2, 'dataLabels' => ['enabled'=>false]];
        $content['jenisPendidikan'] = $isiPendidikan; 


        
        //Untuk Statistik Koleksi Dipinjam
      

        //Untuk Statistik Kelas Subject
        // $sqlKelasSubject = "SELECT * FROM v_stat_kelas_subjek WHERE tahun = ".$tahun;
        $sqlKelasSubject = "call get_stat_kelas_subject_koleksi('".date('Y-m-d', $nowMinOneYear)."', '".date('Y-m-d', $nowMinOnemonth)."');";
        $kelasSubject = Yii::$app->db->createCommand($sqlKelasSubject)->queryAll(); 
       
        $isiKelas = array();
        foreach ($kelasSubject as $kelasSubject) {
            array_push($isiKelas,['name' => $kelasSubject['Name'],'y'=> intval($kelasSubject['CountEksemplar']) ]);
        }
        $content['kelasSubject'] = $isiKelas; 



        //Untuk Statistik Kelas Subject Koleksi Dipinjam
        // $sqlKelasSubject = "SELECT * FROM v_stat_kelas_subjek WHERE tahun = ".$tahun;
        $sqlKelasSubjectKolDipinjam = "call getStatKlasSubjekKoleksiDipinjam('".date('Y-m-d', $nowMinOneYear)."', '".date('Y-m-d', $nowMinOnemonth)."');";
        $kelasSubjectKolDipinjam = Yii::$app->db->createCommand($sqlKelasSubjectKolDipinjam)->queryAll(); 
       
        $isiKelasKolDipinjam = array();
        foreach ($kelasSubjectKolDipinjam as $kelasSubjectKolDipinjam) {
            array_push($isiKelasKolDipinjam,['name' => $kelasSubjectKolDipinjam['NAME'],'y'=> intval($kelasSubjectKolDipinjam['CountEksemplar']) ]);
        }
        // print_r($kelasSubjectKolDipinjam);die;
        $content['kelasSubjectKolDipinjam'] = $isiKelasKolDipinjam; 




        //Untuk Statistik Kelas Subject Koleksi Dibaca
        // $sqlKelasSubject = "SELECT * FROM v_stat_kelas_subjek WHERE tahun = ".$tahun;
        $sqlKelasSubjectKolDibaca = "call getStatKlasSubjekKoleksiDibaca('".date('Y-m-d', $nowMinOneYear)."', '".date('Y-m-d', $nowMinOnemonth)."');";
        $kelasSubjectKolDibaca = Yii::$app->db->createCommand($sqlKelasSubjectKolDibaca)->queryAll(); 
       
        $isiKelasKolDibaca = array();
        foreach ($kelasSubjectKolDibaca as $kelasSubjectKolDibaca) {
            array_push($isiKelasKolDibaca,['name' => $kelasSubjectKolDibaca['NAME'],'y'=> intval($kelasSubjectKolDibaca['CountEksemplar']) ]);
        }
        // print_r($kelasSubjectKolDibaca);die;
        $content['kelasSubjectKolDibaca'] = $isiKelasKolDibaca; 


         


         // print_r($koleksiJudul);
        
        return $this->render('index', $content);
       
    }




   protected function findModel($id)
    {
        if (($model = Memberguesses::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }



}

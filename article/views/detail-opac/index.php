<?php
use yii\helpers\Url; 
use common\components\OpacHelpers;
use common\components\DirectoryHelpers;
use yii\widgets\Pjax;
use kartik\grid\GridView;
use common\models\CollectionSearchKardeks;
$ids=$catalogID;
$pencarian_url=Yii::$app->urlManager->createAbsoluteUrl('pencarian-sederhana');
$base=Yii::$app->homeUrl;
$pengarang=array_values(array_filter(preg_split("/((--)|[|])/", $detailOpac[0]['PENGARANG'])));
$catatan=array_values(array_filter(preg_split("/((--)|[|])/", $detailOpac[0]['CATATAN'])));
$subyek=preg_split("/([|])/", $detailOpac[0]['SUBJEK']);
$penerbitan=preg_split("/([|])/", $detailOpac[0]['PENERBITAN']);
$ISBN=preg_split("/([|])/", $detailOpac[0]['ISBN']);
$ISMN=preg_split("/([|])/", $detailOpac[0]['ISMN']);
$ISSN=preg_split("/([|])/", $detailOpac[0]['ISSN']);
$KONTEN=preg_split("/([|])/", $detailOpac[0]['KONTEN']);
$MEDIA=preg_split("/([|])/", $detailOpac[0]['MEDIA']);
$PENYIMPANAN_MEDIA=preg_split("/([|])/", $detailOpac[0]['PENYIMPANAN_MEDIA']);
$LOKASI_AKSES_ONLINE=preg_split("/([|])/", $detailOpac[0]['LOKASI_AKSES_ONLINE']);
$ABSTRAK=preg_split("/([|])/", $detailOpac[0]['ABSTRAK']);
$DESKRIPSI_FISIK=preg_split("/([|])/", $detailOpac[0]['DESKRIPSI_FISIK']);
$PERNYATAAN_SERI=preg_split("/([|])/", $detailOpac[0]['PERNYATAAN_SERI']);
$EDISI=preg_split("/([|])/", $detailOpac[0]['EDISI']);
$LOKASI_AKSES_ONLINE=preg_split("/([|])/", $detailOpac[0]['LOKASI_AKSES_ONLINE']);
$INFORMASI_TEKNIS=preg_split("/([|])/", $detailOpac[0]['INFORMASI_TEKNIS']);


$isBooking= Yii::$app->config->get('IsBookingActivated');
$this->registerJS('
$(document).ready(function() {
    $(\'#detail\').DataTable(
        {
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": true
        });
} );


    ');
$urlcover;
if($detailOpac[0]['CoverURL'])
{
     if(file_exists(Yii::getAlias('@uploaded_files/sampul_koleksi/original/'.DirectoryHelpers::GetDirWorksheet($detailOpac[0]['Worksheet_id']).'/'.$detailOpac[0]['CoverURL'])))
    {
        $urlcover= '../uploaded_files/sampul_koleksi/original/'.DirectoryHelpers::GetDirWorksheet($detailOpac[0]['Worksheet_id']).'/'.$detailOpac[0]['CoverURL'];
    }
    else {
        $urlcover= '../uploaded_files/sampul_koleksi/original/Monograf/tdkada.gif';
    }

}else{
    $urlcover= '../uploaded_files/sampul_koleksi/original/Monograf/tdkada.gif';
}
?>   

<script type="text/javascript">

  function keranjang(id) {        
        //var id = $("#catalogID").val();
        $.ajax({
          type     :"POST",
          cache    : false,
          url  : "?action=keranjang&catID="+id,
          success  : function(response) {
            $("#keranjang").html(response);
      }
        });


      }

  function booking(CiD,id) {        
        //var id = $("#catalogID").val();
        $.ajax({
          type     :"POST",
          cache    : false,
          url  : "?action=boooking&colID="+id+"id="+CiD,
          success  : function(response) {
            //location.reload();
            $("#alert").html(response);

            //$("#detail"+CiD).html(response);
            //collection(CiD);
            //search(CiD);
            //$("#collapsecollection"+CiD).collapse('show');
          }
        });


      }
  function logDownload(id) {
    $.ajax({
        type: "POST",
        cache: false,
        url: "?action=logDownload&ID=" + id,
    });


  }

    </script>
    <div class="modal fade bs-example-modal-md" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="gridSystemModalLabel">Cite This</h4>
        </div>
        <div class="modal-body">
         <b> APA Citation </b>  <br>
         <?= $cite['APA']?> <br>
       <!--  <b> Chicago Style Citation </b>  <br>
         <?/*= $cite['APA']*/?> <br>
         <b> MLA Citation </b>  <br>
         <?/*= $cite['APA']*/?> <br> -->
      <div id="keranjang">

      </div>

       </div>
       <div class="modal-footer">
        <p align="center" style="color:grey">Peringatan: citasi ini tidak selalu 100% akurat!</p>

      </div>
    </div>
  </div>
</div>
<section class="content">
  <div class="box box-default">
    <div class="box-body" style="padding:20px 0">
      <div class="breadcrumb">
        <ol class="breadcrumb">
          <li><a href="<?=$base; ?>">Home</a></li>
          <li><a >Detail Result</a></li>
         
          <?php
         /* $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Collections'), 'url' => ['index']];
          $this->params['breadcrumbs'][] = $this->$detailOpac[0]['Title'];
      */    ?>

        </ol>
      </div>

      <div class="row">
        <div class="col-md-9">
          <div class="row">
            <div class="col-md-2"><br><img src="<?= $urlcover ?>" style="width:97px ; height:144px"></div>
            <div class="col-md-10"><center>
              <a href="" data-toggle="modal" data-target=".bs-example-modal-md"  ><i class="glyphicon glyphicon-cog"></i> Cite This</a>&nbsp; &nbsp; &nbsp; &nbsp; 
              <a href="javascript:void(0)" onclick="keranjang(<?= $ids; ?>)" ><i class="glyphicon glyphicon-shopping-cart"></i> Tampung</a>&nbsp; &nbsp; &nbsp; &nbsp; 
              <a href="" ><i class="glyphicon glyphicon-new-window"></i> Export Record</a>
            </center>
            <table class="table table-striped">
            <?php 

              if($detailOpac[0]['JUDUL']!='' || $detailOpac[0]['JUDUL']!= NULL)
              echo"
              <tr>
               <td>Judul</td>
                <td style=\"color:red\">".$detailOpac[0]['JUDUL']."</td>              
              </tr>
              ";
              if($detailOpac[0]['JUDUL_SERAGAM']!='' || $detailOpac[0]['JUDUL_SERAGAM']!= NULL)
              echo"
              <tr>
                <td>Judul Seragam</td>
                <td>".$detailOpac[0]['JUDUL_SERAGAM']."</td>
              </tr>
                ";

              if($detailOpac[0]['PENGARANG']!='' || $detailOpac[0]['PENGARANG'] != NULL){
                      echo"
                        <tr>
                        <td>Pengarang</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($pengarang);$x++){
                      
                       echo "<a href=\"pencarian-sederhana?action=pencarianSederhana&ruas=Pengarang&bahan=Semua Jenis Bahan&katakunci=".$pengarang[$x]."\"> ".$pengarang[$x]." </a> </br>";
                      

                      }
                      echo"
                      </td>
                      </tr>
                      ";
                }
              if($detailOpac[0]['EDISI'] != '' || $detailOpac[0]['EDISI'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>EDISI</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($EDISI);$x++){
                      
                       echo $EDISI[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }
              if($detailOpac[0]['PERNYATAAN_SERI'] != '' || $detailOpac[0]['PERNYATAAN_SERI'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Pernyataan Seri</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($PERNYATAAN_SERI);$x++){
                      
                       echo $PERNYATAAN_SERI[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }
              if($detailOpac[0]['PENERBITAN'] != '' || $detailOpac[0]['PENERBITAN'] != NULL)  
                    echo"
                          <tr>
                          <td>Penerbitan</td>
                          <td>
                    ";
                    for($x=0;$x<sizeof($penerbitan);$x++){

                        echo $penerbitan[$x]." </br> ";
                    }
                     echo "
                        </td>
                      </tr>";
              if($detailOpac[0]['DESKRIPSI_FISIK'] != '' || $detailOpac[0]['DESKRIPSI_FISIK'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Deskripsi Fisik</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($DESKRIPSI_FISIK);$x++){
                      
                       echo $DESKRIPSI_FISIK[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }
              if($detailOpac[0]['KONTEN'] != '' || $detailOpac[0]['KONTEN'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Konten</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($KONTEN);$x++){
                      
                       echo $KONTEN[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }
              if($detailOpac[0]['MEDIA'] != '' || $detailOpac[0]['MEDIA'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Media</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($MEDIA);$x++){
                      
                       echo $MEDIA[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }
              if($detailOpac[0]['PENYIMPANAN_MEDIA'] != '' || $detailOpac[0]['PENYIMPANAN_MEDIA'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Penyimpan Media</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($PENYIMPANAN_MEDIA);$x++){
                      
                       echo $PENYIMPANAN_MEDIA[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }              
              if($detailOpac[0]['INFORMASI_TEKNIS'] != '' || $detailOpac[0]['INFORMASI_TEKNIS'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Informasi Teknis</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($INFORMASI_TEKNIS);$x++){
                      
                       echo $INFORMASI_TEKNIS[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    } 
              if($detailOpac[0]['ISBN'] != '' || $detailOpac[0]['ISBN'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>ISBN</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($ISBN);$x++){
                      
                       echo $ISBN[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    } 
              if($detailOpac[0]['ISSN'] != '' || $detailOpac[0]['ISSN'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>ISSN</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($ISSN);$x++){
                      
                       echo $ISSN[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    } 
              if($detailOpac[0]['ISMN'] != '' || $detailOpac[0]['ISMN'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>ISMN</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($ISMN);$x++){
                      
                       echo $ISMN[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    } 
              if($detailOpac[0]['SUBJEK']!='' || $detailOpac[0]['SUBJEK'] != NULL){

                      echo"
                        <tr>
                        <td>Subjek</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($subyek);$x++){
                      
                        echo"
                        <a href=\"pencarian-sederhana?action=pencarianSederhana&ruas=Subyek&bahan=Semua Jenis Bahan&katakunci=".$subyek[$x]."\"> ".$subyek[$x]." </a> </br>
                        ";
                      }
                        echo "
                        </td>
                      </tr>";
                }
              if($detailOpac[0]['ABSTRAK'] != '' || $detailOpac[0]['ABSTRAK'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Abstrak</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($ABSTRAK);$x++){
                      
                       echo $ABSTRAK[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    } 
              if($detailOpac[0]['CATATAN'] != '' || $detailOpac[0]['CATATAN'] != NULL)  {
                    echo"
                          <tr>
                          <td>Catatan</td>
                          <td>
                    ";
                    for($x=0;$x<sizeof($catatan);$x++){

                        echo $catatan[$x]." </br> ";
                    }
                     echo "
                        </td>
                      </tr>";
              }
              if($detailOpac[0]['BAHASA'] != '' || $detailOpac[0]['BAHASA'] != NULL)  
              echo"
              <tr>
                <td>Bahasa</td>
                <td>".$detailOpac[0]['BAHASA']."</td>
              </tr>              
              ";
              if($detailOpac[0]['BENTUK_KARYA'] != '' || $detailOpac[0]['BENTUK_KARYA'] != NULL)  
              echo"
              <tr>
                <td>Bentuk Karya</td>
                <td>".$detailOpac[0]['BENTUK_KARYA']."</td>
              </tr>
              ";
              if($detailOpac[0]['TARGET_PEMBACA'] != '' || $detailOpac[0]['TARGET_PEMBACA'] != NULL)  
              echo"
              <tr>
                <td>Target Pembaca</td>
                <td>".$detailOpac[0]['TARGET_PEMBACA']."</td>
              </tr>";
              if($detailOpac[0]['LOKASI_AKSES_ONLINE'] != '' || $detailOpac[0]['LOKASI_AKSES_ONLINE'] != NULL)  
                  {
                     echo"
                        <tr>
                        <td>Lokasi Akses Online</td>
                        <td>
                      ";
                      for($x=0;$x<sizeof($LOKASI_AKSES_ONLINE);$x++){
                      
                       echo $LOKASI_AKSES_ONLINE[$x]." </br>";
                      
                      }
                      echo"
                      </td>
                      </tr>
                      ";


                    }  ?>
            </table>
            
            <br>
          </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">                       
          <div class="col-md-12"><span class="nav-tabs-content">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab_11" data-toggle="tab">Eksemplar</a></li>
               <li><a href="#tab_12" data-toggle="tab" title="Tampilkan Konten Digital">Konten Digital</a></li>
              <li><a href="#tab_22" data-toggle="tab" title="Tampilkan Metadata MARC">MARC</a></li>
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                  Unduh Katalog <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=MARC21">Format MARC Unicode/UTF-8</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=MARCXML">Format MARC XML</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=MODS">Format MODS</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=DC_RDF">Format Dublin Core (RDF)</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=DC_OAI">Format Dublin Core (OAI)</a></li>
                  <li role="presentation"><a role="menuitem" tabindex="-1" href="detail-opac/download?id=<?=Yii::$app->request->get('id')?>&amp;type=DC_SRW">Format Dublin Core (SRW)</a></li>
                </ul>
              </li>



            </ul>
          </span>
          <div class="tab-content">
            <div class="tab-pane-content active" id="tab_11">
            <?php if ($detailOpac[0]['Worksheet_id']==4) {
             ?>
              <div class="quarantined-collections-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php Pjax::begin(['id' => 'myGridview']);
    echo GridView::widget([
        'id' => 'myGrid',
        'pjax' => true,
        'dataProvider' => $dataProviderSerial,
        'toolbar' => [
            ['content' =>
                \common\components\PageSize::widget(
                    [
                        'template' => '{label} <div class="col-sm-6" style="width:175px">{list}</div>',
                        'label' => Yii::t('app', 'Showing :'),
                        'labelOptions' => [
                            'class' => 'col-sm-4 control-label',
                            'style' => [
                                'width' => '75px',
                                'margin' => '0px',
                                'padding' => '0px',
                            ]

                        ],
                        'sizes' => Yii::$app->params['pageSize'],
                        'options' => [
                            'id' => 'aa',
                            'class' => 'form-control'
                        ]
                    ]
                )

            ],
            //'{pager}',
            //'{toggleData}',
            //'{export}',
        ],
        //'filterSelector' => 'select[name="per-page"]',
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index) {
                    $searchModel = new CollectionSearchKardeks;
                    $params['CatalogId'] = $model->Catalog_id;
                    $params['EdisiSerial'] = $model->EDISISERIAL;
                    //echo"<pre>"; print_r($params); echo"</pre>"; die;

                    $dataProvider = $searchModel->search4($params);

                    return Yii::$app->controller->renderPartial('_subEksemplar', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);

                }
            ],
            ['class' => 'yii\grid\SerialColumn'],
            //'Edisi_id', 
            'EDISISERIAL',
            'TANGGAL_TERBIT_EDISI_SERIAL',
            'Eksemplar',

        ],
        //'summary'=>'',
        'responsive' => true,
        'containerOptions' => ['style' => 'font-size:13px'],
        'hover' => true,
        'condensed' => true,
        'options' => ['font-size' => '12px'],
        /*        'panel' => [
                    'heading'=>'<h3 class="panel-title"><i class="glyphicon glyphicon-th-list"></i> '.Html::encode($this->title).' </h3>',
                    'type'=>'info',
                    'after'=>Html::a('<i class="glyphicon glyphicon-repeat"></i> '.Yii::t('app','Reset List'), ['index'], ['class' => 'btn btn-info']),

                   'showFooter'=>true,
                   'pager' => true,
                ],*/
    ]);
    Pjax::end(); ?>

</div>

             <?php 
            } else {
             ?>
             <div id="detail<?= $catalogID;?>">
              <div class="table-responsive">
              <table  id="detail" class="table table-striped table-bordered" cellspacing="0">
              <thead>
                  <tr>                        
                        <th >No Barcode</th>
                        <th >No. Panggil</th>
                        <th >Akses</th>
                        <th >Lokasi</th>
                        <th >Ketersediaan</th>
                  
                  </tr>
                  </tr>
              </thead>
             
              <tbody>
                  <?php 
                  for($i=0;$i<sizeof($collectionDetail);$i++){
                  $dateNow = new \DateTime("now");
                  if($collectionDetail[$i]['BookingMemberID'] == $noAnggota && $collectionDetail[$i]['BookingExpiredDate'] > $dateNow->format("Y-m-d H:i:sO") ){
                    $collectionDetail[$i]['ketersediaan'] = "Sudah Dipesan";

                    } else
                    if($collectionDetail[$i]['BookingExpiredDate'] > $dateNow->format("Y-m-d H:i:s") ){
                    $collectionDetail[$i]['ketersediaan'] = "Sudah Dipesan  Sampai ".$collectionDetail[$i]['BookingExpiredDate'];

                    }
                    


                    echo"    <tr>
                      <td>"
                          .$collectionDetail[$i]['NomorBarcode'].
                      "</td>
                      <td>"
                          .$collectionDetail[$i]['CallNumber'].
                      "</td>
                      <td> "
                          .$collectionDetail[$i]['akses']."
                          
                      </td>
                      <td>"
                          .$collectionDetail[$i]['namaperpus']." - ".$collectionDetail[$i]['lokasi'].
                      "</td>
                      <td>"
                          .OpacHelpers::maskedStatus($collectionDetail[$i]['ketersediaan']);
           
                        if($isBooking=='1' && $collectionDetail[$i]['ketersediaan'] == "Tersedia" && ($collectionDetail[$i]['akses'] == "Dapat dipinjam" || $collectionDetail[$i]['akses'] == "Tersedia" )){
                        if (!isset($noAnggota)) {
                            $booking = "
                                  <br>
                                   <a href=\"javascript:void(0)\" class=\"btn btn-success btn-xs navbar-btn\" onclick='tampilLogin()'>pesan</a>
                                ";                    
                        } else
                            $booking = "
                                    <form>
                                    <input type=\"button\" onclick=\"booking(" . $collectionDetail[$i]['Catalog_id'] . "," . $collectionDetail[$i]['id'] . ")\" class=\"btn btn-success btn-xs navbar-btn\" value=\"pesan\">

                                    </form>         
                      ";
                       }else {
                                  $booking = "";
                              }
                      echo $booking."
      
                       </td>
                        ";
                  echo"
                      
                  </tr>";

                  }

                  ?> 



              </tbody>
              </table>

              </div>
              </div>
             <?php 
            }
             ?>
            

          </div>
           <div class="tab-pane-content" id="tab_22">
           <?php
            if($marcOpac!=""){
            echo"<table class=\"table table-bordered\">
            <tr>
            <td>
            Tag
            </td>
            <td>
            Ind1
            </td>
            <td>
            Ind2
            </td>
            <td>
            Isi
            </td>
            </tr>
            ";
              for($i=0; $i < sizeof($marcOpac); $i++){
            echo"<tr>
            <td>
            ".$marcOpac[$i]['Tag']."
            </td>
            
             <td>
            ".$marcOpac[$i]['Indicator1']."
            </td>

             <td>
            ".$marcOpac[$i]['Indicator2']."
            </td>

             <td>
            ".$marcOpac[$i]['Value']."
            </td>


            </tr>
            ";
              }
            }
            echo"</table>";
           ?>

           </div>
            <div class="tab-pane-content " id="tab_12">
                       <?php
            if($KontenDigital[0]['Catalog_id']!=""){
            echo"<table id=\"detail\" class=\"table table-bordered\">
            <tr>
            <td>
            No
            </td>
            <td>
            Nama File
            </td>
            <td>
            Nama File Format Flash
            </td>
            <td>
            Format File
            </td>
            <td>
            Action
            </td>
            </tr>
            ";
              for($i=0; $i < sizeof($KontenDigital); $i++){
            echo"<tr>
            <td>
            ".($i+1)."
            </td>
            ";
            ?>
               <?php if($KontenDigital[$i]['FileFlash']!='' && $KontenDigital[$i]['FileFlash'] != NULL)
            {  
                $fakePath = DirectoryHelpers::GetTemporaryFolder($KontenDigital[$i]['ID'],2);
                if(!isset($noAnggota) && $KontenDigital[$i]['IsPublish']===2){
                    $kata="<a href=\"javascript:void(0)\" onclick=\"tampilLogin()\" >Baca Online</a>";
                } else {
                $kata="<a href=\"../uploaded_files".$fakePath."\" onclick=\"logDownload(".$KontenDigital[$i]['ID'].")\" target=\"_blank\" >Baca Online</a>";
                }
            }
            else{
                
                $fakePath = DirectoryHelpers::GetTemporaryFolder($KontenDigital[$i]['ID'],1);
                if((!isset($noAnggota)) && $KontenDigital[$i]['IsPublish']==2){
                $kata="<a href=\"javascript:void(0)\" onclick=\"tampilLogin()\" >Download</a>";
                } else{
                $kata="<a  href=\"../uploaded_files".$fakePath."\" onclick=\"logDownload(".$KontenDigital[$i]['ID'].")\"target=\"_blank\" >Download</a>";           
                }
            }
           
           
            
            
             echo"
              <td>
             ".$KontenDigital[$i]['FileURL']."
             </td>
             <td>
             ".$KontenDigital[$i]['FileFlash']."
             </td>
             <td>
             ".$KontenDigital[$i]['FormatFile']."
             </td>
             <td>
            ".$kata."
            </td>



            </tr>
            ";
              }
            }
            echo"</table>";
           ?>


          </div>
          <div class="tab-pane-content" id="tab_33">Content Unduh katalog</div>           
        </div>
      </div>
    </div>
    <div class="row">&nbsp;</div>
  </div>
  <?php if (sizeof($similiarTitle) !=0) {
    # code...
   ?>
  <div class="col-md-3">

        <span style="margin-bottom:13px"><strong>Karya Terkait :</strong></span>
        <div class="list-group facet" id="side-panel-authorStr">

           <div id="side-collapse-authorStr" class="collapse in">

          <?php
          $divHiddenBuka='<div class="facedHidden" >';
          $divHiddenTutup=(sizeof($similiarTitle)>5 ? '</div>' : '');

              for($i=0;$i<sizeof($similiarTitle);$i++){
              if($i==5){echo$divHiddenBuka;}
              echo"
          
              <a style=\"padding: 8px 40px 8px 8px;\" class=\"list-group-item \" href=\"?id=".$similiarTitle[$i]['ID']."\" > ".$similiarTitle[$i]['Title']."</a>
          
              ";
              }

              echo$divHiddenTutup;
              if(sizeof($similiarTitle)>5){
              echo"<a  href=\"#\" style=\"padding: 8px 40px 8px 8px;\" class=\"list-group-item faced\"  >Show More</a>";
              }
              ?>

        </div>

          </div>


  </div>
  <?php }?>
</div>

</div>
</div>
<div class="row">&nbsp;</div>                    
          </section><!-- /.content -->



          <?php 
          $this->registerJS('

            $(\'.facedHidden\').hide();

            // Make sure all the elements with a class of "clickme" are visible and bound
            // with a click event to toggle the "box" state
            $(\'.faced\').each(function() {
                $(this).show(0).on(\'click\', function(e) {
                    // This is only needed if your using an anchor to target the "box" elements
                    e.preventDefault();
                    
                    // Find the next "box" element in the DOM
                    $(this).prev(\'.facedHidden\').slideToggle(\'fast\');
                    if ( $(this).text() == "Show More") {
                $(this).text("Show Less")

                } else
                {
                $(this).text("Show More");
                }   


                });
            });
            ');

          ?>
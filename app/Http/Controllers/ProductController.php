<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ProductController extends Controller
{

    private $conn = null;
    public function __construct(){
        $access = env("ACCESS_FILE");
        if(file_exists($access)){
        try{  $this->conn  = new \PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};charset=UTF-8; DBQ=".$access."; Uid=; Pwd=;");
            }catch(\PDOException $e){ die($e->getMessage()); }
        }else{ die("$access no es un origen de datos valido."); }
    }

    public function familiarizacion(){//metodo para replicar las familiarizaciones en mysql
        
        DB::statement("SET SQL_SAFE_UPDATES = 0;");//se desactiva safe update
        DB::statement("SET FOREIGN_KEY_CHECKS = 0;");//se desactivan las llaves foraneas
        DB::statement("truncate table product_variants;");//se vacia la tabla de familiarizaciones
        DB::statement("SET SQL_SAFE_UPDATES = 1;");//se activan las llaves foraneas
        DB::statement("SET FOREIGN_KEY_CHECKS = 1;");//se activa safe update
        try{
            $select = "SELECT * FROM F_EAN";//query para ver las familiarizaciones
            $exec = $this->conn->prepare($select);
            $exec -> execute();
            $art=$exec->fetchall(\PDO::FETCH_ASSOC);//se executa
        }catch (\PDOException $e){ die($e->getMessage());}
                foreach($art as $artic){
                    $pro = DB::table('products')->where('code',$artic["ARTEAN"])->value('id');// se busca el id de el producto que se va a familiarizar             
                    $product = [//se crea el arreglo de insercion
                        "barcode"=>$artic["EANEAN"],
                        "stock"=>0,
                        "_product"=>$pro
                    ];
                    $ins = DB::table('product_variants')->insert($product);//se inserta el arreglo 
                }
                return response()->json("Familiarizaciones creadas correctamente");
    }

    public function productInsert(){
        $produ = "SELECT
        CODART AS CODE,
        CCOART AS NAME,
        DESART AS DESCRIPTION,
        DEEART AS LABEL,
        REFART AS REFERENCE,
        UPPART AS PIECES,
        FAMART AS FAMILIA,
        CP1ART AS CATEGORIA,
        PHAART AS PROVEEDOR,
        FORMAT(FALART,'YYYY-mm-dd')&' '&'00:00:00' AS CREATED,
        FORMAT(FUMART,'YYYY-mm-dd')&' '&'00:00:00' AS UPDATED,
        PCOART AS COSTO,
        EANART AS BARCODE
        FROM F_ART WHERE CODART IN ('167812','167813','167815','167828','167829','167830','167831','170416','170417','170575','170577','174791','174792','174793','174794','174816','174817','174818','175259','175361','C-0026','C-1 ','C-1176','C-4','C-5056','C-5062','C-5566','C-8026','HA64304-W','HA64305-3','HA64426-9','HA64535-3','HA64575-3','HA64577-3','HA65402-9','L01','LL22BPE01-1','LL22BPE01-2','LL22BPE01-3','LL22BPE02-2','LL22BPE02-3','LL22BPE02-4','LL22BPE03-3','LL22BPE03-4','LL22BPE04-1','LL22BPE04-2','LL22BPE04-3','LL22BPE04-4','LL22BPE05-1','LL22BPE05-2','LL22BPE05-3','LL22BPE06-2','LL22BPE07-1','LL22BPE07-2','LL22BPE07-3','LL22BPE07-4','LL22KBE01-1','LL22KBE01-2','YB-5')
        ";
        $exec = $this->conn->prepare($produ);
        $exec -> execute();
        $fact=$exec->fetchall(\PDO::FETCH_ASSOC);
        $colsTab = array_keys($fact[0]);

        foreach($fact as $product){
            foreach($colsTab as $col){ $product[$col] = utf8_encode($product[$col]); }
            $caty = DB::connection('puebla')->table('product_categories as PC')// SE BUSCA LA CATEGORIA DE EL PRODUCTO EN MYSQL
                    ->join('product_categories as PF', 'PF.id', '=','PC.root')
                    ->where('PC.alias', $product['CATEGORIA'])
                    ->where('PF.alias', $product['FAMILIA'])
                    ->value('PC.id'); 

            $category = $caty == null ? 404 : $caty;
            $provider = $product['PROVEEDOR'] == '' ? : 5; 

           $articulos = [
                "code"=>$product['CODE'],
                "name"=>$product['NAME'],
                "description"=>$product['DESCRIPTION'],
                "label"=>$product['LABEL'],
                "reference"=>$product['REFERENCE'],
                "stock"=>0,
                "pieces"=>$product['PIECES'],
                "weight"=>null,
                "_category"=>$category,
                "_status"=>1,
                "_unit"=>3,
                "_provider"=>$provider,
                "created_at"=>$product['CREATED'],
                "updated_at"=>$product['UPDATED'],
                "cost"=>$product['COSTO'],
                "barcode"=>$product['BARCODE'],
                "large"=>null,
                "dimensions"=>null,
                "refillable"=>null,
                "imgcover"=>null
           ];

           $insert = DB::connection('puebla')->table('products')->insert($articulos);
        }

        
        return response()->json("lesto");
    }

    public function replyStock(){
        $workpoint = env("WKP");//SE OBTIENE LA SUCURSAL
        $select = //SE CREA EL QUERY PARA REALIZAR LA BUSQUEDA DE STOCK
            "SELECT F_ART.CODART AS CODIGO,
            SUM(IIF(F_STO.ALMSTO = 'GEN', F_STO.ACTSTO , 0)) AS GENSTOCK, 
            SUM(IIF(F_STO.ALMSTO = 'DES', F_STO.ACTSTO , 0)) AS DESSTOCK, 
            SUM(IIF(F_STO.ALMSTO = 'EXH', F_STO.ACTSTO , 0)) AS EXHSTOCK, 
            SUM(IIF(F_STO.ALMSTO = 'FDT', F_STO.ACTSTO , 0)) AS FDTSTOCK, 
            SUM(IIF(F_STO.ALMSTO = 'GEN', F_STO.ACTSTO , 0)  + IIF(F_STO.ALMSTO = 'EXH', F_STO.ACTSTO , 0) ) AS STOCK 
            FROM F_ART  
            INNER JOIN F_STO ON F_STO.ARTSTO = F_ART.CODART  
            WHERE F_STO.ACTSTO <> 0 GROUP BY F_ART.CODART
            ORDER BY F_ART.CODART ASC ";
        $exec = $this->conn->prepare($select);
        $exec ->execute();
        $art=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($art as $rt){
            $produ [] =$rt["CODIGO"] ;
            $sto []  = [//SE CREA EL ARREGLO PARA LA ACTUALIZACION DE STOCKS
                "product"=>$rt["CODIGO"],
                "stock"=>intval($rt["STOCK"]),
                "gen"=>intval($rt["GENSTOCK"]),
                "exh"=>intval($rt["EXHSTOCK"]),
                "des"=>intval($rt["DESSTOCK"]),
                "fdt"=>intval($rt["FDTSTOCK"]) 
            ];
        }

        $stomspub = DB::connection('puebla')->table('product_stock AS PS')->join('products AS P','P.id','PS._product')->whereIn('P.code',$produ)->where('_workpoint',$workpoint)->select('P.code','PS.*')->orderByRaw('P.code ASC')->get();
        foreach($stomspub as $pub){
            $road [] = [
                "product"=>$pub->code,
                "stock"=>$pub->stock,
                "gen"=>$pub->gen,
                "exh"=>$pub->exh,
                "des"=>$pub->des,
                "fdt"=>$pub->fdt,
            ];
        }

        $out = array_udiff($sto,$road, function($a,$b){
            if($a == $b){
                return  0;
                
            }else{
                return ($a > $b) ? 1 : -1;
            }
        });

        if($out){
            foreach($out as $updpub){
                $updpr = [
                    "PS.stock"=>$updpub['stock'],
                    "PS.gen"=>$updpub['gen'],
                    "PS.exh"=>$updpub['exh'],
                    "PS.des"=>$updpub['des'],
                    "PS.fdt"=>$updpub['fdt'],
                ];
            
                $update =  DB::connection('puebla')
                ->table('product_stock AS PS')
                ->join('products AS P','P.id','PS._product')
                ->where('PS._workpoint',$workpoint)
                ->where('P.code',$updpub['product'])
                ->update($updpr);
                $modd [] = $updpub['product'];
            }
        }else{$modd = [];}

        $stomscdmx = DB::table('product_stock AS PS')
            ->join('products AS P','P.id','PS._product')
            ->whereIn('P.code',$produ)
            ->where('_workpoint',$workpoint)
            ->select('P.code','PS.*')
            ->orderByRaw('P.code ASC')->get();
        foreach($stomscdmx as $cdmx){
            $cdx []  = [
                "product"=>$cdmx->code,
                "stock"=>$cdmx->stock,
                "gen"=>$cdmx->gen,
                "exh"=>$cdmx->exh,
                "des"=>$cdmx->des,
                "fdt"=>$cdmx->fdt,
            ];
        }

        $outw = array_udiff($sto,$cdx, function($a,$b){
            if($a == $b){
                return  0;
                
            }else{
                return ($a > $b) ? 1 : -1;
            }
        });

        if($outw){
            foreach($outw as $updcdm){
                $updprcdmx = [
                    "PS.stock"=>$updcdm['stock'],
                    "PS.gen"=>$updcdm['gen'],
                    "PS.exh"=>$updcdm['exh'],
                    "PS.des"=>$updcdm['des'],
                    "PS.fdt"=>$updcdm['fdt'],
                ];
                
                $updatecdmx =  DB::table('product_stock AS PS')
                    ->join('products AS P','P.id','PS._product')
                    ->where('PS._workpoint',$workpoint)
                    ->where('P.code',$updcdm['product'])
                    ->update($updprcdmx);
                $mocd [] = $updcdm['product'];
            }
        }else{$mocd = [];}

        return response()->json(["articulos pub"=>count($modd),
                                    "articulos cdmx"=>count($mocd)]);
    }
    
    public function replyCeller(){
        $exist = [];
        $created = [];
        $secticdmx = [];
        $workpoint = env('WKP');
        $cellers = DB::connection('puebla')->table('celler')->where('_workpoint',$workpoint)->get();
        foreach($cellers as $celler){
            $celcd = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$celler->name)->value('name');
            if($celcd){
                $exist[]="el almacen ".$celcd." ya existe";
            }else{
                $ins = DB::table('celler')->insert(['name'=>$celler->name,'_workpoint'=>$workpoint,'_type'=>$celler->_type]);
                $created[]="el almacen ".$celler->name." se creo correctamente";
            }
        }
        $sections = DB::connection('puebla')->table('celler_section AS CS')->join('celler AS C','C.id','CS._celler')->where('C._workpoint',$workpoint)->select('CS.*','C.name as celler')->orderByRaw('CS.name ASC')->get();
        foreach($sections as $section){
            $idcd = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$section->celler)->value('id');
            $sectipub []   = [
                'name'=>$section->name,
                'alias'=>$section->alias,
                'path'=>$section->path,
                'root'=>$section->root,
                'deep'=>$section->deep,
                'details'=>json_encode([]),
                '_celler'=>$idcd,
                'deleted_at'=>$section->deleted_at
            ]; 
            
            
        }
       
        $sectionscdmx = DB::table('celler_section AS CS')->join('celler AS C','C.id','CS._celler')->where('C._workpoint',$workpoint)->select('CS.*')->orderByRaw('CS.name ASC')->get();
        
            foreach($sectionscdmx as $sectioncdmx){
                $secticdmx [] = [
                    "name"=>$sectioncdmx->name,
                    "alias"=>$sectioncdmx->alias,
                    "path"=>$sectioncdmx->path,
                    "root"=>$sectioncdmx->root,
                    "deep"=>$sectioncdmx->deep,
                    "details"=>json_encode([]),
                    "_celler"=>$sectioncdmx->_celler,
                    "deleted_at"=>$sectioncdmx->deleted_at
                ]; 
            }

            $out = array_udiff($sectipub,$secticdmx, function($a,$b){
                if($a == $b){
                    return  0;
                    
                }else{
                    return ($a > $b) ? 1 : -1;
                }
            });
    
            if($out){
                foreach($out as $updpub){
                    $idceller = DB::table('celler_section')
                    ->where('name',$updpub['name'])
                    ->where('alias',$updpub['alias'])
                    ->where('path',$updpub['path'])
                    ->where('root',$updpub['root'])
                    ->where('deep',$updpub['deep'])
                    ->where('_celler',$updpub['_celler'])
                    ->value('id');
                    if($idceller){
                        // $updpr = DB::table('celler_section')->where('id',$idceller)->update(['deleted_at'=>$updpub['deleted_at']]);
                    }else{
                    $updpr  =
                        [
                            "name"=>$updpub['name'],
                            "alias"=>$updpub['alias'],
                            "path"=>$updpub['path'],
                            "root"=>$updpub['root'],
                            "deep"=>$updpub['deep'],
                            "details"=>$updpub['details'],
                            "_celler"=>$updpub['_celler'],
                            "deleted_at"=>$updpub['deleted_at']
                        ]; 
                        // $insert =DB::table('celler_section')->insert($updpr);
                    }
                }
            }else{$updpr = [];}

            $plocations = DB::connection('puebla')
            ->table('product_location AS PL')
            ->join('products AS P','P.id','PL._product')
            ->join('celler_section AS CS','CS.id','PL._location')
            ->join('celler AS C','C.id','CS._celler')
            ->where('C._workpoint',$workpoint)
            ->select('P.code as codigo','C.name AS alm','CS.*')->get();
            foreach($plocations as $plocation){
                $product = DB::table('products')->where('code',$plocation->codigo)->value('id');
                $celler = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$plocation->alm)->value('id');
                    $idceller = DB::table('celler_section')
                    ->where('name',$plocation->name)
                    ->where('alias',$plocation->alias)
                    ->where('path',$plocation->path)
                    ->where('_celler',$celler)
                    ->value('id');
     
                $prepare []  = [
                    "_location"=>$idceller,
                    "_product"=>$product,
                ];
          
            }

            sort($prepare,4);

            foreach($prepare as $in){
                $insert []=$in;
            }

            $clocations = DB::table('product_location AS PL')
            ->join('celler_section AS CS','CS.id','PL._location')
            ->join('celler AS C','C.id','CS._celler')
            ->where('C._workpoint',$workpoint)
            ->select('PL.*')
            ->orderByRaw('PL._location ASC')
            ->orderByRaw('PL._product ASC')->get();
            foreach($clocations as $clocation){
                $mscs [] = [
                    "_location"=>$clocation->_location,
                    "_product"=>$clocation->_product
                ];
            }

            $prduc = array_udiff($insert,$mscs, function($a,$b){
                if($a == $b){
                    return  0;
                    
                }else{
                    return ($a > $b) ? 1 : -1;
                }
            });

            if($prduc){
            foreach($prduc as $loc){
                $insdiff  = [
                    "_location"=>$loc['_location'],
                    "_product"=>$loc['_product'],
                ];
            $diff = DB::table('product_location')->insert($insdiff);
                    
            }
            }else{$insdiff = [];}

            $delpro = array_udiff($mscs,$insert, function($a,$b){
                if($a == $b){
                    return  0;
                    
                }else{
                    return ($a > $b) ? 1 : -1;
                }
            });

            if($delpro){
            foreach($delpro as $delloc){

            $deldiff = DB::table('product_location')->where('_location',$delloc['_location'])->where('_product',$delloc['_product'])->delete();
                    
            }
            }else{$deldiff = [];}





        $res =[
            "celler"=>[
            "fail"=>$exist,
            "goal"=>$created],
            "celler_section"=>[
                "puebla"=>$sectipub,
                "cdmx"=>$secticdmx,
                "diferencia"=>$updpr
            ],
            "product_location"=>[
                "insertados"=>$insdiff,
                "eliminados"=>$deldiff,
            ]

        ];

        return response()->json($res);
    }
    public function minmax(){
        $workpoint = env('WKP');
        $minmaxpub = DB::connection('puebla')
        ->table('product_stock AS PS')
        ->join('products AS P','P.id','PS._product')
        ->where('P._status','!=',4)
        ->where('PS.min','!=',0)
        ->where('PS.max','!=',0)
        ->where('PS._workpoint',$workpoint)
        ->select('P.code as codigo','PS.min as minimo','PS.max as maximo')
        ->orderByRaw('P.code ASC')
        ->get();
        foreach($minmaxpub as $mmp){
            $updae [] = [
                "product"=>$mmp->codigo, 
                "min"=>$mmp->minimo,
                "max"=>$mmp->maximo
            ];
        }
        $minmaxcdmx = DB::table('product_stock AS PS')
        ->join('products AS P','P.id','PS._product')
        ->where('P._status','!=',4)
        ->where('PS.min','!=',0)
        ->where('PS.max','!=',0)
        ->where('PS._workpoint',$workpoint)
        ->select('P.code as codigo','PS.min as minimo','PS.max as maximo')
        ->orderByRaw('P.code ASC')
        ->get();
        foreach($minmaxcdmx as $mmc){
            $update [] = [
                "product"=>$mmc->codigo,
                "min"=>$mmc->minimo,
                "max"=>$mmc->maximo
            ];

        }

        $prduc = array_udiff($updae,$update, function($a,$b){
            if($a == $b){
                return  0;
                
            }else{
                return ($a > $b) ? 1 : -1;
            }
        });

        if($prduc){
        foreach($prduc as $loc){
            $insdiff = [
                "PS.min"=>$loc['min'],
                "PS.max"=>$loc['max'],
            ];
            $upda = DB::table('product_stock AS PS')->join('products AS P','P.id','PS._product')->where('PS._workpoint',$workpoint)->where('P.code',$loc['product'])->update($insdiff);
        }
        }else{$insdiff = [];}


        return response()->json(["DIFERENCIA"=>$insdiff]);
    }

    public function compareprices(){
        $date = now()->format('Y-m-d');

        $productspub = "SELECT DISTINCT CODART AS CODIGO FROM F_ART INNER JOIN F_STO ON F_STO.ARTSTO = F_ART.CODART WHERE ACTSTO <> 0 AND ALMSTO NOT IN ('DES','FDT')";
        $exec = $this->conn->prepare($productspub);
        $exec -> execute();
        $fact=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($fact as $profac){
            $asu[]= $profac['CODIGO'];
        }
        

        $products = DB::table('prices_product AS PP')
        ->join('products AS P','P.id','PP._product')
        ->join('product_categories AS PC','PC.id','P._category')
        // ->whereDate('P.updated_at',$date)
        ->where('P._status','!=',4)
        ->whereIn('P.code',$asu)
        ->select('P.code as codigo','P.cost as costo','PP.*')
        ->selectraw('GETSECTION(PC.id) as seccion')
        ->orderBy('P.code','asc')
        ->get();
        $margen = 1.05;
        foreach($products as $product){
            if($product->seccion == "Mochila"){
                $costo = $product->costo;
                $centro = $product->CENTRO;
                $especial = $product->ESPECIAL;
                $caja = $product->CAJA;
                $docena = $product->DOCENA;
                $mayoreo = $product->MAYOREO;
            }else{
                $costo = round($product->costo * $margen,0);
                $centro = round($product->CENTRO * $margen,0);
                $especial = round($product->ESPECIAL * $margen,0);
                $caja = round($product->CAJA * $margen,0);
                $docena = round($product->DOCENA * $margen,0);
                $mayoreo = round($product->MAYOREO * $margen,0);
            }
            if($mayoreo == $centro){
                $menudeo = $caja;
            }elseif(($mayoreo >= 0) && ($mayoreo <= 49)){
                $menudeo = $mayoreo + 5;
            }elseif(($mayoreo >= 50) && ($mayoreo <= 99)){
                $menudeo = $mayoreo + 10;
            }elseif(($mayoreo >= 100) && ($mayoreo <= 499)){
                $menudeo = $mayoreo + 20;
            }elseif(($mayoreo >= 500) && ($mayoreo <= 999)){
                $menudeo = $mayoreo + 50;
            }elseif($mayoreo >= 1000){
                $menudeo =  $mayoreo + 100; 
            }

            $res[]=[
                "codigo"=>$product->codigo,
                // "costo"=>$costo,
                "centro"=>$centro,
                "especial"=>$especial,
                "caja"=>$caja,
                "docena"=>$docena,
                "mayoreo"=>$mayoreo,
                "menudeo"=>$menudeo,
            ]; 
        }

        $pricepub = "SELECT DISTINCT
        F_LTA.ARTLTA AS CODIGO,
        F_ART.PCOART AS COSTO,
        MAX(iif(F_LTA.TARLTA = 6 , F_LTA.PRELTA ,0 )) AS CENTRO,
        MAX(iif(F_LTA.TARLTA = 5 , F_LTA.PRELTA ,0 )) AS ESPECIAL,
        MAX(iif(F_LTA.TARLTA = 4 , F_LTA.PRELTA ,0 )) AS CAJA,
        MAX(iif(F_LTA.TARLTA = 3 , F_LTA.PRELTA ,0 )) AS DOCENA,
        MAX(iif(F_LTA.TARLTA = 2 , F_LTA.PRELTA ,0 )) AS MAYOREO,
        MAX(iif(F_LTA.TARLTA = 1 , F_LTA.PRELTA ,0 )) AS MENUDEO
        FROM ((F_LTA
        INNER JOIN F_ART ON F_ART.CODART = F_LTA.ARTLTA)
        INNER JOIN F_STO ON F_STO.ARTSTO = F_ART.CODART)
        WHERE ACTSTO <> 0 AND ALMSTO NOT IN ('DES','FDT')
        GROUP BY F_LTA.ARTLTA, F_ART.PCOART 
        ORDER BY F_LTA.ARTLTA ASC";
        $exec = $this->conn->prepare($pricepub);
        $exec -> execute();
        $pub=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($pub as $price){
            $pric[]= [
                "codigo"=>$price['CODIGO'],
                // "costo"=>doubleval($price['COSTO']),
                "centro"=>intval($price['CENTRO']),
                "especial"=>intval($price['ESPECIAL']),
                "caja"=>intval($price['CAJA']),
                "docena"=>intval($price['DOCENA']),
                "mayoreo"=>intval($price['MAYOREO']),
                "menudeo"=>intval($price['MENUDEO']),

            ];
        }

        $prduc = array_udiff($res,$pric, function($a,$b){
            if($a == $b){
                return  0;
                
            }else{
                return ($a > $b) ? 1 : -1;
            }
        });
        if($prduc){
            foreach($prduc as $dife){
                $diff[] = $dife['codigo'];

                $updcen = "UPDATE F_LTA SET PRELTA =".$dife['centro']." WHERE TARLTA = 6 AND ARTLTA = ?";
                $exec = $this->conn->prepare($updcen);
                $exec -> execute([$dife['codigo']]);

                $updes = "UPDATE F_LTA SET PRELTA =".$dife['especial']." WHERE TARLTA = 5 AND ARTLTA = ?";
                $exec = $this->conn->prepare($updes);
                $exec -> execute([$dife['codigo']]);

                $updcaj = "UPDATE F_LTA SET PRELTA =".$dife['caja']." WHERE TARLTA = 4 AND ARTLTA = ?";
                $exec = $this->conn->prepare($updcaj);
                $exec -> execute([$dife['codigo']]);

                $upddoc = "UPDATE F_LTA SET PRELTA =".$dife['docena']." WHERE TARLTA = 3 AND ARTLTA = ?";
                $exec = $this->conn->prepare($upddoc);
                $exec -> execute([$dife['codigo']]);

                $updmay = "UPDATE F_LTA SET PRELTA =".$dife['mayoreo']." WHERE TARLTA = 2 AND ARTLTA = ?";
                $exec = $this->conn->prepare($updmay);
                $exec -> execute([$dife['codigo']]);

                $updmen = "UPDATE F_LTA SET PRELTA =".$dife['menudeo']." WHERE TARLTA = 1 AND ARTLTA = ?";
                $exec = $this->conn->prepare($updmen);
                $exec -> execute([$dife['codigo']]);
            }
        $modifi = DB::table('products AS P')->join('product_categories as PC', 'PC.id','P._category')->whereIn('P.code',$diff)->select('P.code','P.description')->selectraw('GETSECTION(PC.id)')->get();
        foreach($modifi as $mod){
            $actualizados[] = $mod; 
        }

        }else{$diff = []; $actualizados = [];}




        return response()->json(["produtms"=>$res,"productfac"=>$pric,"ARTIDULOS"=>$actualizados]);

        
    }

}

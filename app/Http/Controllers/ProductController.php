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

}

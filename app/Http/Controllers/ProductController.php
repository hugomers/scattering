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

    public function familiarizacion(){
        
        DB::statement("SET SQL_SAFE_UPDATES = 0;");
        DB::statement("SET FOREIGN_KEY_CHECKS = 0;");
        DB::statement("truncate table product_variants;");
        DB::statement("SET SQL_SAFE_UPDATES = 1;");
        DB::statement("SET FOREIGN_KEY_CHECKS = 1;");
        try{
            $select = "SELECT * FROM F_EAN";
            $exec = $this->conn->prepare($select);
            $exec -> execute();
            $art=$exec->fetchall(\PDO::FETCH_ASSOC);
        }catch (\PDOException $e){ die($e->getMessage());}
                foreach($art as $artic){
                    $pro = DB::table('products')->where('code',$artic["ARTEAN"])->value('id');                 
                    $product = [
                        "barcode"=>$artic["EANEAN"],
                        "stock"=>0,
                        "_product"=>$pro
                    ];
                    $ins = DB::table('product_variants')->insert($product);
                }
                return response()->json("Familiarizaciones creadas correctamente");
    }
    public function wor(){
        $workpoint = env("WORKPOINT");
        if($workpoint == 18){
            $select = 
            "SELECT F_ART.CODART AS CODIGO,
             SUM(IIF(F_STO.ALMSTO = 'GEN', F_STO.ACTSTO , 0)) AS GENSTOCK, 
             SUM(IIF(F_STO.ALMSTO = 'DES', F_STO.ACTSTO , 0)) AS DESSTOCK, 
             SUM(IIF(F_STO.ALMSTO = 'EXH', F_STO.ACTSTO , 0)) AS EXHSTOCK, 
             SUM(IIF(F_STO.ALMSTO = 'FDT', F_STO.ACTSTO , 0)) AS FDTSTOCK, 
             SUM(IIF(F_STO.ALMSTO = 'GEN', F_STO.ACTSTO , 0)  + IIF(F_STO.ALMSTO = 'EXH', F_STO.ACTSTO , 0) ) AS STOCK 
             FROM F_ART  
             INNER JOIN F_STO ON F_STO.ARTSTO = F_ART.CODART  
             WHERE F_STO.ACTSTO <> 0 GROUP BY F_ART.CODART ";
            $exec = $this->conn->prepare($select);
            $exec ->execute();
            $art=$exec->fetchall(\PDO::FETCH_ASSOC);
            foreach($art as $rt){
                $produ = DB::table('products')->where('code',$rt["CODIGO"])->VALUE('id');
                $sto = [
                    "stock"=>$rt["STOCK"],
                    "gen"=>$rt["GENSTOCK"],
                    "exh"=>$rt["EXHSTOCK"],
                    "des"=>$rt["DESSTOCK"],
                    "fdt"=>$rt["FDTSTOCK"] 
                ];
                DB::table('product_stock')->where('_workpoint', $workpoint)->where('_product',$produ)->update($sto);
            }

        return response()->json($sto);    
            
        }
    }
}

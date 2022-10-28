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

}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $access = env("ACCESS_FILE");
        if(file_exists($access)){
        try{  $this->conn  = new \PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};charset=UTF-8; DBQ=".$access."; Uid=; Pwd=;");
            }catch(\PDOException $e){ die($e->getMessage()); }
        }else{ die("$access no es un origen de datos valido."); }

        $schedule->call(function (){
            $workpoint = env("WORKPOINT");
            if($workpoint == 1){
            try{
                $articulos = "SELECT CODART, NPUART FROM F_ART";
                $exec = $this->conn->prepare($articulos);
                $exec -> execute();
                $art=$exec->fetchall(\PDO::FETCH_ASSOC);
            }catch (\PDOException $e){ die($e->getMessage());}
                if($art){
                    foreach($art as $artic){
                        $_status = $artic['NPUART'] == 0 ? 1 : 5;
                        DB::table('products')->where('code',$artic['CODART'])->update(['_status' => $_status]);
                        DB::table('product_stock AS PS')->join('products AS P','P.id','=','PS._product')->where('P.code',$artic['CODART'])->update(['PS._status' => $_status]);
                    }
                }
        }
        })->dailyAt('23:00');

        $schedule->call(function (){
            $workpoint = env("WORKPOINT");
            if($workpoint == 1){

        try{
            $select = "SELECT CODART FROM F_ART";
            $exec = $this->conn->prepare($select);
            $exec -> execute();
            $art=$exec->fetchall(\PDO::FETCH_ASSOC);
        }catch (\PDOException $e){ die($e->getMessage());}
        foreach($art as $artic){
            $product[] = $artic["CODART"];
        }
         $upd = DB::table('products')->wherenotin('code',$product)->update(['_status'=>4]);
    }
    })->everySixhours();

    $schedule->call(function (){
        $workpoint = env("WORKPOINT");
            if($workpoint == 1){
        DB::statement("INSERT INTO product_stock SELECT 1 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 1))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 2 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 2))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 3 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 3))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 4 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 4))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 5 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 5))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 6 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 6))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 7 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 7))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 8 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 8))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 9 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 9))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 10 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 10))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 11 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 11))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 12 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 12))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 13 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 13))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 14 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 14))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 15 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 15))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 16 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 16))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 17 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 17))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 18 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 18))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 19 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 19))  IS null GROUP BY P.code;");
        DB::statement("INSERT INTO product_stock SELECT 20 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 20))  IS null GROUP BY P.code;");
            }
    })->everyTwoHours();

    $schedule->call(function (){
        $workpoint = env("WORKPOINT");
        $date = carbon::now()->format('d-m-Y');
        try{
          $whithdrawals = "SELECT * FROM F_RET WHERE FECRET = #".$date."#";
          $exec = $this->conn->prepare($whithdrawals);
          $exec -> execute();
          $wth=$exec->fetchall(\PDO::FETCH_ASSOC);
        }catch (\PDOException $e){ die($e->getMessage());}
        if($wth){
            foreach($wth as $wt){
                $codexist = DB::table('withdrawals')->where('code',$wt['CODRET'])->where('_workpoint',$workpoint)->value('id');
                $provider = $wt['PRORET'] != 0 ? $wt['PRORET'] : 800  ;
                if($codexist){
                    $upd = [
                        "description"=>$wt['CONRET'],
                        "total"=>$wt['IMPRET'],
                        "_provider"=>$provider                       
                    ];    
                    DB::table('withdrawals')->where('id',$codexist)->update($upd);
                }else{
                    $whith  = [
                        "code"=>$wt['CODRET'],
                        "_workpoint"=>$workpoint,
                        "_cash"=>$wt['CAJRET'],
                        "description"=>$wt['CONRET'],
                        "total"=>$wt['IMPRET'],
                        "created_at"=>$wt['FECRET'],
                        "_provider"=>INTVAL($provider)
                    ];
                    DB::table('withdrawals')->insert($whith);
                }
            }
        }      
    })->everyThirtyMinutes();
   
    $schedule->call(function (){
        $workpoint = env("WORKPOINT");
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
        }   
         })->everyThreeMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

    }
}

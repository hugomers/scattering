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

    //     $schedule->call(function (){//funcion para actualizar status de productos existentes
    //         $workpoint = env("WORKPOINT");//muestra el id de la tienda en mysql
    //         if($workpoint == 1){//validamos que sea cedis por que de ahi saldra la informacion no de las tiendas
    //         try{
    //             $articulos = "SELECT CODART, NPUART FROM F_ART";//query solo muestra codigo y stado de articulos
    //             $exec = $this->conn->prepare($articulos);
    //             $exec -> execute();
    //             $art=$exec->fetchall(\PDO::FETCH_ASSOC);
    //         }catch (\PDOException $e){ die($e->getMessage());}
    //             if($art){//si hay articulos 
    //                 foreach($art as $artic){
    //                     $_status = $artic['NPUART'] == 0 ? 1 : 5;//si el status en fsol es 0 cambia a 1 disponible de lo contrario a 5 descatalogado
    //                     DB::table('products')->where('code',$artic['CODART'])->update(['_status' => $_status]);//se cambia el status en la tabla maestra
    //                     DB::table('product_stock AS PS')->join('products AS P','P.id','=','PS._product')->where('P.code',$artic['CODART'])->update(['PS._status' => $_status]);//se cambia el status en la tabla de stocks
    //                 }
    //             }
    //     }
    //     })->dailyAt('23:00');// la tarea se ejecuta todos los dias a la 11 de la noche

    // $schedule->call(function (){//tarea para insertar los stock de productos que no existan
    //     $workpoint = env("WORKPOINT");//obtenemos el id de la sucursal
    //         if($workpoint == 1){//se verifica que sea cedis
    //     DB::statement("INSERT INTO product_stock SELECT 1 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 1))  IS null GROUP BY P.code;");//qyery para insertar articulso en la tabla de stock que no existan
    //     DB::statement("INSERT INTO product_stock SELECT 2 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 2))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 3 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 3))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 4 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 4))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 5 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 5))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 6 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 6))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 7 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 7))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 8 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 8))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 9 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 9))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 10 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 10))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 11 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 11))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 12 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 12))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 13 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 13))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 14 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 14))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 15 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 15))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 16 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 16))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 17 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 17))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 18 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 18))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 19 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 19))  IS null GROUP BY P.code;");
    //     DB::statement("INSERT INTO product_stock SELECT 20 , id , 0,0,0,_status,0,0,0,0 FROM products P  WHERE P._status IN (1,5,6) AND ((SELECT sum(stock) FROM product_stock WHERE P.id = _product AND _workpoint = 20))  IS null GROUP BY P.code;");
    //         }
    // })->everyTwoHours();//se ejecuta cada 2 horas

    // $schedule->call(function (){//se crea tarea para replicar las retiradas
    //     $workpoint = env("WORKPOINT");//se obtiene el numero de sucursal en mysql
    //     $date = carbon::now()->format('d-m-Y');//se obtiene el dia que ocurre
    //     try{
    //       $whithdrawals = "SELECT * FROM F_RET WHERE FECRET = #".$date."#";//se obtienen las retiradas del dia en curso
    //       $exec = $this->conn->prepare($whithdrawals);
    //       $exec -> execute();
    //       $wth=$exec->fetchall(\PDO::FETCH_ASSOC);
    //     }catch (\PDOException $e){ die($e->getMessage());}
    //     if($wth){//se valida si hay retiradas
    //         foreach($wth as $wt){
    //             $codexist = DB::table('withdrawals')->where('code',$wt['CODRET'])->where('_workpoint',$workpoint)->value('id');//se busca el codigo de la retirada en la tienda
    //             $provider = $wt['PRORET'] != 0 ? $wt['PRORET'] : 800  ;//si el proveedor es 0 se cambia a 800
    //             if($codexist){//si el codigo de la retirada yaexiste
    //                 $upd = [//se preparan los campos de actualizacion
    //                     "description"=>$wt['CONRET'],
    //                     "total"=>$wt['IMPRET'],
    //                     "_provider"=>$provider                       
    //                 ];    
    //                 DB::table('withdrawals')->where('id',$codexist)->update($upd);// Y SE ACTUALIZA LA RETIRADA EXISTENTE
    //             }else{// SI NO EXISTE
    //                 $whith  = [// SE PREPARA ARREGLO PARA INSERTAR
    //                     "code"=>$wt['CODRET'],
    //                     "_workpoint"=>$workpoint,
    //                     "_cash"=>$wt['CAJRET'],
    //                     "description"=>$wt['CONRET'],
    //                     "total"=>$wt['IMPRET'],
    //                     "created_at"=>$wt['FECRET'],
    //                     "_provider"=>INTVAL($provider)
    //                 ];
    //                 DB::table('withdrawals')->insert($whith);// SE INSERTA LA RETIRADA
    //             }
    //         }
    //     }      
    // })->everyThirtyMinutes();//SE EJECUTA CADA 30 MIN
   
    $schedule->call(function (){//SE HACE LA REPLICACION DE STOCK EN PUEBLA
        
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

        // return response()->json(["articulos pub"=>count($modd),
        //                             "articulos cdmx"=>count($mocd)]);
        echo "articulos pub ".count($modd)." articulos cdmx ".count($mocd);
    })->everyMinute();//TAREA SE GENERA CADA 3 MIN

    // $schedule->call(function (){//replicacion de ubicaciones
    //         $exist = [];
    //         $created = [];
    //         $secticdmx = [];
    //         $workpoint = env('WKP');
    //         $cellers = DB::connection('puebla')->table('celler')->where('_workpoint',$workpoint)->get();
    //         foreach($cellers as $celler){
    //             $celcd = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$celler->name)->value('name');
    //             if($celcd){
    //                 $exist[]="el almacen ".$celcd." ya existe";
    //             }else{
    //                 $ins = DB::table('celler')->insert(['name'=>$celler->name,'_workpoint'=>$workpoint,'_type'=>$celler->_type]);
    //                 $created[]="el almacen ".$celler->name." se creo correctamente";
    //             }
    //         }
    //         $sections = DB::connection('puebla')->table('celler_section AS CS')->join('celler AS C','C.id','CS._celler')->where('C._workpoint',$workpoint)->select('CS.*','C.name as celler')->orderByRaw('CS.name ASC')->get();
    //         foreach($sections as $section){
    //             $idcd = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$section->celler)->value('id');
    //             $sectipub []   = [
    //                 'name'=>$section->name,
    //                 'alias'=>$section->alias,
    //                 'path'=>$section->path,
    //                 'root'=>$section->root,
    //                 'deep'=>$section->deep,
    //                 'details'=>json_encode([]),
    //                 '_celler'=>$idcd,
    //                 'deleted_at'=>$section->deleted_at
    //             ]; 
                
                
    //         }
    //         $sectionscdmx = DB::table('celler_section AS CS')->join('celler AS C','C.id','CS._celler')->where('C._workpoint',$workpoint)->select('CS.*')->orderByRaw('CS.name ASC')->get();
            
    //             foreach($sectionscdmx as $sectioncdmx){
    //                 $secticdmx [] = [
    //                     "name"=>$sectioncdmx->name,
    //                     "alias"=>$sectioncdmx->alias,
    //                     "path"=>$sectioncdmx->path,
    //                     "root"=>$sectioncdmx->root,
    //                     "deep"=>$sectioncdmx->deep,
    //                     "details"=>json_encode([]),
    //                     "_celler"=>$sectioncdmx->_celler,
    //                     "deleted_at"=>$sectioncdmx->deleted_at
    //                 ]; 
    //             }

    //             $out = array_udiff($sectipub,$secticdmx, function($a,$b){
    //                 if($a == $b){
    //                     return  0;
                        
    //                 }else{
    //                     return ($a > $b) ? 1 : -1;
    //                 }
    //             });

    //             if($out){
    //                 foreach($out as $updpub){
    //                     $idceller = DB::table('celler_section')
    //                     ->where('name',$updpub['name'])
    //                     ->where('alias',$updpub['alias'])
    //                     ->where('path',$updpub['path'])
    //                     ->where('root',$updpub['root'])
    //                     ->where('deep',$updpub['deep'])
    //                     ->where('_celler',$updpub['_celler'])
    //                     ->value('id');
    //                     if($idceller){
    //                         $updpr = DB::table('celler_section')->where('id',$idceller)->update(['deleted_at'=>$updpub['deleted_at']]);
    //                     }else{
    //                     $updpr  =
    //                         [
    //                             "name"=>$updpub['name'],
    //                             "alias"=>$updpub['alias'],
    //                             "path"=>$updpub['path'],
    //                             "root"=>$updpub['root'],
    //                             "deep"=>$updpub['deep'],
    //                             "details"=>$updpub['details'],
    //                             "_celler"=>$updpub['_celler'],
    //                             "deleted_at"=>$updpub['deleted_at']
    //                         ]; 
    //                         $insert =DB::table('celler_section')->insert($updpr);
    //                     }
    //                 }
    //             }else{$updpr = [];}

    //             $plocations = DB::connection('puebla')
    //             ->table('product_location AS PL')
    //             ->join('products AS P','P.id','PL._product')
    //             ->join('celler_section AS CS','CS.id','PL._location')
    //             ->join('celler AS C','C.id','CS._celler')
    //             ->where('C._workpoint',$workpoint)
    //             ->select('P.code as codigo','C.name AS alm','CS.*')->get();
    //             foreach($plocations as $plocation){
    //                 $product = DB::table('products')->where('code',$plocation->codigo)->value('id');
    //                 $celler = DB::table('celler')->where('_workpoint',$workpoint)->where('name',$plocation->alm)->value('id');
    //                     $idceller = DB::table('celler_section')
    //                     ->where('name',$plocation->name)
    //                     ->where('alias',$plocation->alias)
    //                     ->where('path',$plocation->path)
    //                     ->where('root',$plocation->root)
    //                     ->where('deep',$plocation->deep)
    //                     ->where('_celler',$celler)
    //                     ->value('id');
            
    //                 $prepare [] = [
    //                     "_location"=>$idceller,
    //                     "_product"=>$product,
    //                 ];
    //             }
    //             sort($prepare,4);

    //             foreach($prepare as $in){
    //                 $insert []=$in;
    //             }

    //             $clocations = DB::table('product_location AS PL')
    //             ->join('celler_section AS CS','CS.id','PL._location')
    //             ->join('celler AS C','C.id','CS._celler')
    //             ->where('C._workpoint',$workpoint)
    //             ->select('PL.*')
    //             ->orderByRaw('PL._location ASC')
    //             ->orderByRaw('PL._product ASC')->get();
    //             foreach($clocations as $clocation){
    //                 $mscs [] = [
    //                     "_location"=>$clocation->_location,
    //                     "_product"=>$clocation->_product
    //                 ];
    //             }

    //             $prduc = array_udiff($insert,$mscs, function($a,$b){
    //                 if($a == $b){
    //                     return  0;
                        
    //                 }else{
    //                     return ($a > $b) ? 1 : -1;
    //                 }
    //             });

    //             if($prduc){
    //             foreach($prduc as $loc){
    //                 $insdiff  = [
    //                     "_location"=>$loc['_location'],
    //                     "_product"=>$loc['_product'],
    //                 ];
    //             $diff = DB::table('product_location')->insert($insdiff);
                        
    //             }
    //             }else{$insdiff = [];}

    //             $delpro = array_udiff($mscs,$insert, function($a,$b){
    //                 if($a == $b){
    //                     return  0;
                        
    //                 }else{
    //                     return ($a > $b) ? 1 : -1;
    //                 }
    //             });

    //             if($delpro){
    //             foreach($delpro as $delloc){

    //             $ddiff = DB::table('product_location')->where('_location',$delloc['_location'])->where('_product',$delloc['_product'])->delete();
                        
    //             }
    //             }else{$deldiff = [];}





    //         $res =[
    //             "celler"=>[
    //             "fail"=>$exist,
    //             "goal"=>$created],
    //             "celler_section"=>[
    //                 "puebla"=>$sectipub,
    //                 "cdmx"=>$secticdmx,
    //                 "diferencia"=>$updpr
    //             ],
    //             "product_location"=>[
    //                 "insertados"=>$insdiff,
    //                 "eliminados"=>$deldiff,
    //             ]

    //         ];

    //         return response()->json($res);

    // })->everyMinute();

    $schedule->call(function (){//REPLICACION DE MIN Y MAXIMOS
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
    })->everyFifteenMinutes();
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

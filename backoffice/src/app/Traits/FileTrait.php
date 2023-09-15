<?php
namespace App\Traits;



trait FileTrait {

    /***
     *  Auth: Alejandro Sagula
     *  Date: 13.09.2023
     *  Desc: Retorna el url para el acceso a los archivos 
     *    EJ: http://localhost:9000/works/files?file=files/users/14/work-registration/60/script_file-60.pdf
     */
    public function addUrlFile($path){
        return url('/').'/works/files?file='.$path;
    }
}
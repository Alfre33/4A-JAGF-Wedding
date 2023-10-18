<?php 
    class Conexion{
        static public function conectar(){
            $link = new PDO("mysql:host=localhost:3008;dbname=wedding", "soporte", "soporte");
    
            $link->exec("set names utf8");
    
            return $link;
        }
    }
    



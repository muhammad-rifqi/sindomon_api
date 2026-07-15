<?php
$host='127.0.0.1'; $user='root'; $pass='root'; $db='sindomondb'; $secret='secret_key_yang_sangat_panjang_dan_aman';
$pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function be($d){return rtrim(strtr(base64_encode($d),'+/','-_'),'=');}
function jwt_e($p,$s){$h=be(json_encode(['alg'=>'HS256','typ'=>'JWT']));$pl=be(json_encode($p));$sig=be(hash_hmac('sha256',$h.'.'.$pl,$s,true));return $h.'.'.$pl.'.'.$sig;}
$stmt=$pdo->query("SELECT id,username,roles_id FROM tbl_users WHERE username IN ('admin_pusat','operator_jabar','pimpinan_mabes')");
while($r=$stmt->fetch()){$p=['uid'=>(int)$r['id'],'username'=>$r['username'],'role'=>(int)$r['roles_id'],'iat'=>time(),'exp'=>time()+365*24*3600];echo $r['username'].'|'.jwt_e($p,$secret)."\n";}

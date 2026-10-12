<?php

$panjang = "10";
$lebar = "5" ;


echo "Sebuah balok memiliki ukuran". "<br>";
echo "Panjang = ".$panjang . "<br>";
echo "Lebar = ".$lebar. "<br>";
echo "berapa total luasnya?". "<br>"."<br>";
function hitunganLuas($panjang, $lebar) {
    return $panjang * $lebar;
}

$hasil = hitunganLuas($panjang,$lebar);
echo "Luas= " . $hasil ."";
?>
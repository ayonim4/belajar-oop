<?php
// Praktikum PBO Pertemuan 2
// Program Kasir Sederhana dan Perhitungan Diskon

// 1. Inisialisasi variabel
$namaPembeli = "";
$namaBarang = "";
$hargaBarang = 0.0;
$jumlahBarang = 0;
$isMember = false;
$subtotal = 0.0;
$diskon = 0.0;
$totalBayar = 0.0;
$daftarBarang = [];
$sudahDihitung = false;

// 2. Function untuk menghitung diskon
function hitungDiskon($subtotal, $isMember) {
    if ($subtotal >= 500000) {
        $persenDiskon = 15;
    } elseif ($subtotal >= 250000) {
        $persenDiskon = 10;
    } elseif ($subtotal >= 100000) {
        $persenDiskon = 5;
    } else {
        $persenDiskon = 0;
    }

    // Member mendapatkan tambahan diskon 5%
    if ($isMember) {
        $persenDiskon += 5;
    }

    return $subtotal * ($persenDiskon / 100);
}

// Memproses data dari formulir
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $namaPembeli = trim($_POST["nama_pembeli"] ?? "");
    $namaBarang = trim($_POST["nama_barang"] ?? "");
    $hargaBarang = (float) ($_POST["harga"] ?? 0);
    $jumlahBarang = (int) ($_POST["jumlah"] ?? 0);
    $isMember = isset($_POST["member"]);

    // Memastikan input sesuai ketentuan
    if (
        $namaPembeli !== "" &&
        $namaBarang !== "" &&
        $hargaBarang > 0 &&
        $jumlahBarang >= 1 &&
        $jumlahBarang <= 1000 &&
        $hargaBarang <= 1000000000
    ) {
        // Menghitung subtotal dan diskon
        $subtotal = $hargaBarang * $jumlahBarang;
        $diskon = hitungDiskon($subtotal, $isMember);
        $totalBayar = $subtotal - $diskon;

        // 3. Perulangan untuk membuat rincian barang
        for ($i = 1; $i <= $jumlahBarang; $i++) {
            $daftarBarang[] = "Barang ke-" . $i .
                " - " . htmlspecialchars($namaBarang);
        }

        $sudahDihitung = true;
    } else {
        $pesanError = "Periksa kembali data yang dimasukkan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Sederhana</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            padding: 25px;
        }

        .kartu {
            max-width: 480px;
            margin: auto;
            padding: 24px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px #00000015;
        }

        h2 {
            text-align: center;
            color: #263746;
        }

        label {
            display: block;
            margin-top: 14px;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            margin-top: 18px;
            padding: 11px;
            color: white;
            background: #1769aa;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .hasil {
            margin-top: 22px;
            padding: 16px;
            background: #eef6ff;
            border-radius: 6px;
            overflow-wrap: anywhere;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            color: #1769aa;
        }

        .error {
            color: #c62828;
        }
    </style>
</head>

<body>
<div class="kartu">
    <h2>Kasir Sederhana</h2>

    <form method="POST">
        <label for="nama_pembeli">Nama Pembeli</label>
        <input type="text" id="nama_pembeli"
               name="nama_pembeli" required>

        <label for="nama_barang">Nama Barang</label>
        <input type="text" id="nama_barang"
               name="nama_barang" required>

        <label for="harga">Harga Satuan (Rp)</label>
        <input type="number" id="harga" name="harga"
               min="1" max="1000000000" required>

        <label for="jumlah">Jumlah Barang</label>
        <input type="number" id="jumlah" name="jumlah"
               min="1" max="1000" required>

        <label>
            <input type="checkbox" name="member" value="1">
            Saya memiliki kartu member
        </label>

        <button type="submit">Hitung Pembayaran</button>
    </form>

    <?php if (!empty($pesanError)): ?>
        <p class="error">
            <?= htmlspecialchars($pesanError) ?>
        </p>
    <?php endif; ?>

    <?php if ($sudahDihitung): ?>
        <div class="hasil">
            <h3>Rincian Pembayaran</h3>

            <p>Nama pembeli:
                <?= htmlspecialchars($namaPembeli) ?>
            </p>

            <p>Nama barang:
                <?= htmlspecialchars($namaBarang) ?>
            </p>

            <p>Jumlah barang: <?= $jumlahBarang ?></p>

            <p>Status member:
                <?= $isMember ? "Ya" : "Tidak" ?>
            </p>

            <hr>

            <p>Subtotal:
                Rp <?= number_format($subtotal, 0, ',', '.') ?>
            </p>

            <p>Potongan diskon:
                Rp <?= number_format($diskon, 0, ',', '.') ?>
            </p>

            <p class="total">
                Total bayar:
                Rp <?= number_format($totalBayar, 0, ',', '.') ?>
            </p>

            <hr>
            <h4>Rincian Barang</h4>

            <ul>
                <?php foreach ($daftarBarang as $barang): ?>
                    <li><?= $barang ?></li>
                <?php endforeach; ?>
            </ul>

            <p>Transaksi berhasil dihitung.</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
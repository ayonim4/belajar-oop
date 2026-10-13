
<?php
// Menyiapkan variabel
$nim = "";
$nama = "";
$semester = 0;
$nilaiTugas = 0.0;
$nilaiUTS = 0.0;
$nilaiUAS = 0.0;
$nilaiAkhir = 0.0;
$predikat = "";
$status = "";
$sudahDikirim = false;
$error = "";
$daftarNilai = [];

// Function untuk menghitung nilai akhir
function hitungNilaiAkhir(float $tugas, float $uts, float $uas): float
{
    return ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);
}

// Function untuk menentukan predikat
function tentukanPredikat(float $nilai): string
{
    if ($nilai >= 85) {
        return "A";
    } elseif ($nilai >= 75) {
        return "B";
    } elseif ($nilai >= 65) {
        return "C";
    } elseif ($nilai >= 50) {
        return "D";
    }

    return "E";
}

// Mengecek apakah form sudah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sudahDikirim = true;

    $nim = trim($_POST["nim"] ?? "");
    $nama = trim($_POST["nama"] ?? "");
    $semester = (int) ($_POST["semester"] ?? 0);

    $tugasInput = trim($_POST["nilai_tugas"] ?? "");
    $utsInput = trim($_POST["nilai_uts"] ?? "");
    $uasInput = trim($_POST["nilai_uas"] ?? "");

    // Validasi data mahasiswa dan nilai
    if (
        $nim == "" || $nama == "" ||
        $semester < 1 || $semester > 8 ||
        !is_numeric($tugasInput) ||
        !is_numeric($utsInput) ||
        !is_numeric($uasInput) ||
        (float)$tugasInput < 0 || (float)$tugasInput > 100 ||
        (float)$utsInput < 0 || (float)$utsInput > 100 ||
        (float)$uasInput < 0 || (float)$uasInput > 100
    ) {
        $error = "Data tidak valid. Pastikan nilai berada antara 0 sampai 100.";
    } else {
        $nilaiTugas = (float)$tugasInput;
        $nilaiUTS = (float)$utsInput;
        $nilaiUAS = (float)$uasInput;

        // Menyimpan nilai ke dalam array
        $daftarNilai = [
            "Nilai Tugas" => $nilaiTugas,
            "Nilai UTS" => $nilaiUTS,
            "Nilai UAS" => $nilaiUAS
        ];

        // Menghitung nilai akhir
        $nilaiAkhir = hitungNilaiAkhir(
            $nilaiTugas,
            $nilaiUTS,
            $nilaiUAS
        );

        // Menentukan predikat
        $predikat = tentukanPredikat($nilaiAkhir);

        // Menentukan status kelulusan
        $lulus = $nilaiAkhir >= 60;
        $status = $lulus ? "Lulus" : "Belum lulus";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perhitungan Nilai Mahasiswa</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #234f7d;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background-color: #234f7d;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #163652;
        }

        .hasil {
            margin-top: 30px;
            padding: 20px;
            background-color: #eef5fa;
            border-radius: 8px;
        }

        .baris {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #d5e1ea;
        }

        .error {
            margin-top: 20px;
            padding: 12px;
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Input Nilai Mahasiswa</h1>

    <form method="POST">

        <label>NIM</label>
        <input type="text" name="nim"
            value="<?= htmlspecialchars($nim) ?>" required>

        <label>Nama Mahasiswa</label>
        <input type="text" name="nama"
            value="<?= htmlspecialchars($nama) ?>" required>

        <label>Semester</label>
        <select name="semester" required>
            <option value="">-- Pilih Semester --</option>

            <?php for ($i = 1; $i <= 8; $i++): ?>
                <option value="<?= $i ?>"
                    <?= $semester === $i ? "selected" : "" ?>>
                    Semester <?= $i ?>
                </option>
            <?php endfor; ?>

        </select>

        <label>Nilai Tugas (0-100)</label>
        <input type="number" name="nilai_tugas"
            min="0" max="100" step="0.01"
            value="<?= htmlspecialchars((string)($_POST["nilai_tugas"] ?? "")) ?>"
            required>

        <label>Nilai UTS (0-100)</label>
        <input type="number" name="nilai_uts"
            min="0" max="100" step="0.01"
            value="<?= htmlspecialchars((string)($_POST["nilai_uts"] ?? "")) ?>"
            required>

        <label>Nilai UAS (0-100)</label>
        <input type="number" name="nilai_uas"
            min="0" max="100" step="0.01"
            value="<?= htmlspecialchars((string)($_POST["nilai_uas"] ?? "")) ?>"
            required>

        <p>
            Bobot: Tugas 30%, UTS 30%, UAS 40%.
            Nilai akhir minimal 60 dinyatakan lulus.
        </p>

        <button type="submit">Hitung Nilai</button>

    </form>

    <?php if ($sudahDikirim && $error != ""): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php elseif ($sudahDikirim): ?>

        <div class="hasil">

            <h2>Hasil Perhitungan Nilai</h2>

            <p><strong>NIM:</strong>
                <?= htmlspecialchars($nim) ?>
            </p>

            <p><strong>Nama:</strong>
                <?= htmlspecialchars($nama) ?>
            </p>

            <p><strong>Semester:</strong>
                <?= $semester ?>
            </p>

            <?php foreach ($daftarNilai as $komponen => $nilai): ?>
                <div class="baris">
                    <span><?= htmlspecialchars($komponen) ?></span>
                    <strong><?= number_format($nilai, 2, ',', '.') ?></strong>
                </div>
            <?php endforeach; ?>

            <div class="baris">
                <strong>Nilai Akhir</strong>
                <strong><?= number_format($nilaiAkhir, 2, ',', '.') ?></strong>
            </div>

            <div class="baris">
                <strong>Predikat</strong>
                <strong><?= htmlspecialchars($predikat) ?></strong>
            </div>

            <div class="baris">
                <strong>Status</strong>
                <strong><?= htmlspecialchars($status) ?></strong>
            </div>

        </div>

    <?php endif; ?>

</div>
</body>
</html>

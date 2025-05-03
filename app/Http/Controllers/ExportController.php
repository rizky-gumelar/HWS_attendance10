<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\Lembur;
use App\Models\Divisi;
use App\Models\Toko;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportTemplate()
    {
        $user = auth()->user();
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Input Jadwal
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Jadwal');
        $sheet->setCellValue('A1', 'Nama Karyawan');
        $sheet->setCellValue('B1', 'Shift');
        $sheet->setCellValue('C1', 'Tanggal');

        // Sheet 2: Karyawan List
        $karyawanSheet = new Worksheet($spreadsheet, 'KaryawanList');
        $spreadsheet->addSheet($karyawanSheet);

        $query = User::with(['shift']);

        if ($user->role === 'spv') {
            $query->where('divisi_id', $user->divisi_id)->where('role', '!=', 'admin');
        }

        $karyawans = $query->orderBy('nama_karyawan')->pluck('nama_karyawan');
        foreach ($karyawans as $index => $nama) {
            $karyawanSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 3: Shift List
        $shiftSheet = new Worksheet($spreadsheet, 'ShiftList');
        $spreadsheet->addSheet($shiftSheet);

        $shifts = Shift::where('id', '<', 100)->pluck('nama_shift');
        foreach ($shifts as $index => $nama) {
            $shiftSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Kembali ke Sheet Input Jadwal
        $spreadsheet->setActiveSheetIndexByName('Input Jadwal');

        // Tambahkan dropdown untuk A2:A100 (Nama Karyawan)
        for ($row = 2; $row <= 100; $row++) {
            $validationA = $sheet->getCell("A$row")->getDataValidation();
            $validationA->setType(DataValidation::TYPE_LIST);
            $validationA->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationA->setAllowBlank(true);
            $validationA->setShowInputMessage(true);
            $validationA->setShowErrorMessage(true);
            $validationA->setShowDropDown(true);
            $validationA->setFormula1("'KaryawanList'!A$1:A$" . count($karyawans));

            // Tambahkan dropdown untuk B2:B100 (Shift)
            $validationB = $sheet->getCell("B$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);
            $validationB->setShowDropDown(true);
            $validationB->setFormula1("'ShiftList'!A$1:A$" . count($shifts));

            // Format kolom C (tanggal) ke yyyy-mm-dd
            $sheet->getStyle("C$row")
                ->getNumberFormat()
                ->setFormatCode('yyyy-mm-dd');

            // (Opsional) Validasi agar kolom C hanya menerima tanggal
            $dateValidation = $sheet->getCell("C$row")->getDataValidation();
            $dateValidation->setType(DataValidation::TYPE_DATE);
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setShowErrorMessage(true);
            $dateValidation->setPromptTitle('Masukkan Tanggal');
            $dateValidation->setPrompt('Format tanggal: YYYY-MM-DD');
            $dateValidation->setErrorTitle('Format Tidak Valid');
            $dateValidation->setError('Harap masukkan tanggal dengan format YYYY-MM-DD');
        }

        // Sembunyikan sheet daftar
        $spreadsheet->getSheetByName('KaryawanList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
        $spreadsheet->getSheetByName('ShiftList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Export file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_jadwal.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return Response::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function exportTemplateLibur()
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Input Jadwal
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Libur');
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Keterangan');

        // Kembali ke Sheet Input Jadwal
        $spreadsheet->setActiveSheetIndexByName('Input Libur');

        // Tambahkan dropdown untuk A2:A100 (Nama Karyawan)
        for ($row = 2; $row <= 100; $row++) {

            // Format kolom C (tanggal) ke yyyy-mm-dd
            $sheet->getStyle("A$row")
                ->getNumberFormat()
                ->setFormatCode('yyyy-mm-dd');

            // (Opsional) Validasi agar kolom C hanya menerima tanggal
            $dateValidation = $sheet->getCell("A$row")->getDataValidation();
            $dateValidation->setType(DataValidation::TYPE_DATE);
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setShowErrorMessage(true);
            $dateValidation->setPromptTitle('Masukkan Tanggal');
            $dateValidation->setPrompt('Format tanggal: YYYY-MM-DD');
            $dateValidation->setErrorTitle('Format Tidak Valid');
            $dateValidation->setError('Harap masukkan tanggal dengan format YYYY-MM-DD');
        }

        // Export file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_libur.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return Response::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function exportTemplateLembur()
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Input Jadwal
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Jadwal Lembur');
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Tipe Lembur');
        $sheet->setCellValue('D1', 'Biaya');
        $sheet->setCellValue('E1', 'Durasi (jam)');
        $sheet->setCellValue('F1', 'Total');
        $sheet->setCellValue('G1', 'Keterangan Lembur');

        // Sheet 2: Karyawan List
        $karyawanSheet = new Worksheet($spreadsheet, 'KaryawanList');
        $spreadsheet->addSheet($karyawanSheet);

        $karyawans = User::orderBy('nama_karyawan')->pluck('nama_karyawan');
        foreach ($karyawans as $index => $nama) {
            $karyawanSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 3: lembur List
        $lemburSheet = new Worksheet($spreadsheet, 'LemburList');
        $spreadsheet->addSheet($lemburSheet);

        // Header
        $lemburSheet->setCellValue('A1', 'Tipe Lembur');
        $lemburSheet->setCellValue('B1', 'Biaya');

        // Data
        $lemburs = Lembur::select('tipe_lembur', 'biaya')->get();
        if ($lemburs->isNotEmpty()) {
            foreach ($lemburs as $index => $item) {
                $lemburSheet->setCellValue('A' . ($index + 2), $item->tipe_lembur);
                $lemburSheet->setCellValue('B' . ($index + 2), $item->biaya);
            }
            $lemburCount = $lemburs->count();
        } else {
            $lemburSheet->setCellValue('A2', 'Data Belum Ada');
            $lemburSheet->setCellValue('B2', 0);
            $lemburCount = 1;
        }

        // Kembali ke Sheet Input Jadwal
        $spreadsheet->setActiveSheetIndexByName('Input Jadwal Lembur');

        // Tambahkan dropdown untuk A2:A100 (Nama Karyawan)
        for ($row = 2; $row <= 100; $row++) {
            // A: Tanggal (validasi + format)
            $sheet->getStyle("A$row")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $dateValidation = $sheet->getCell("A$row")->getDataValidation();
            $dateValidation->setType(DataValidation::TYPE_DATE);
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setPrompt('Format tanggal: YYYY-MM-DD');
            $dateValidation->setError('Harap masukkan tanggal dengan format YYYY-MM-DD');

            // B: Nama Karyawan
            $validationB = $sheet->getCell("B$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_STOP);
            $validationB->setFormula1("'KaryawanList'!A2:A100"); // Sesuaikan jika jumlah lebih
            $validationB->setShowDropDown(true);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);

            // C: Tipe Lembur (Dropdown)
            $validationC = $sheet->getCell("C$row")->getDataValidation();
            $validationC->setType(DataValidation::TYPE_LIST);
            $validationC->setErrorStyle(DataValidation::STYLE_STOP);
            $validationC->setFormula1("'LemburList'!A2:A" . ($lemburCount + 1));
            $validationC->setShowDropDown(true);
            $validationC->setAllowBlank(true);
            $validationC->setShowInputMessage(true);
            $validationC->setShowErrorMessage(true);

            // D: Biaya (pakai VLOOKUP dari sheet LemburList)
            $sheet->setCellValue("D$row", "=IFERROR(VLOOKUP(C$row, LemburList!A:B, 2, FALSE), \"\")");

            // E: Durasi, user input manual

            // F: Total = Biaya x Durasi
            $sheet->setCellValue("F$row", "=IFERROR(D$row*E$row, \"\")");
        }

        // Sembunyikan sheet daftar
        $spreadsheet->getSheetByName('KaryawanList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
        $spreadsheet->getSheetByName('LemburList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Export file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_jadwal_lembur.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return Response::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }

    public function exportTemplateKaryawan()
    {
        $spreadsheet = new Spreadsheet();

        // Sheet 1: Input Karyawan
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Input Jadwal Karyawan');
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Nama Karyawan');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Password');
        $sheet->setCellValue('E1', 'Toko');
        $sheet->setCellValue('F1', 'Default Shift');
        $sheet->setCellValue('G1', 'Divisi');
        $sheet->setCellValue('H1', 'No HP');
        $sheet->setCellValue('I1', 'Role');
        $sheet->setCellValue('J1', 'Total Cuti');
        $sheet->setCellValue('K1', 'Tanggal Masuk');

        // Sheet 2: Toko List
        $tokoSheet = new Worksheet($spreadsheet, 'TokoList');
        $spreadsheet->addSheet($tokoSheet);

        $tokos = Toko::pluck('nama_toko');
        foreach ($tokos as $index => $nama) {
            $tokoSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 3: Default Shift List
        $defaultShiftSheet = new Worksheet($spreadsheet, 'DefaultShiftList');
        $spreadsheet->addSheet($defaultShiftSheet);

        $defaultShifts = Shift::where('id', '<', 100)->pluck('nama_shift');
        foreach ($defaultShifts as $index => $nama) {
            $defaultShiftSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 4: Divisi List
        $divisiSheet = new Worksheet($spreadsheet, 'DivisiList');
        $spreadsheet->addSheet($divisiSheet);

        $divisis = Divisi::pluck('nama_divisi');
        foreach ($divisis as $index => $nama) {
            $divisiSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 5: Role List
        $roleSheet = new Worksheet($spreadsheet, 'RoleList');
        $spreadsheet->addSheet($roleSheet);

        $roles = ['admin', 'spv', 'karyawan'];
        foreach ($roles as $index => $role) {
            $roleSheet->setCellValue('A' . ($index + 1), $role);
        }

        // Kembali ke Sheet Input Jadwal
        $spreadsheet->setActiveSheetIndexByName('Input Jadwal Karyawan');

        // Tambahkan dropdown untuk A2:A100 (Nama Karyawan)
        for ($row = 2; $row <= 100; $row++) {

            // (Toko)
            $validationB = $sheet->getCell("E$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);
            $validationB->setShowDropDown(true);
            $validationB->setFormula1("'TokoList'!A$1:A$" . count($tokos));

            // (Shift)
            $validationB = $sheet->getCell("F$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);
            $validationB->setShowDropDown(true);
            $validationB->setFormula1("'DefaultShiftList'!A$1:A$" . count($defaultShifts));

            // (Divisi)
            $validationB = $sheet->getCell("G$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);
            $validationB->setShowDropDown(true);
            $validationB->setFormula1("'DivisiList'!A$1:A$" . count($divisis));

            // (Role)
            $validationB = $sheet->getCell("I$row")->getDataValidation();
            $validationB->setType(DataValidation::TYPE_LIST);
            $validationB->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validationB->setAllowBlank(true);
            $validationB->setShowInputMessage(true);
            $validationB->setShowErrorMessage(true);
            $validationB->setShowDropDown(true);
            $validationB->setFormula1("'RoleList'!A$1:A$" . count($roles));

            // K: Tanggal (validasi + format)
            $sheet->getStyle("K$row")->getNumberFormat()->setFormatCode('yyyy-mm-dd');
            $dateValidation = $sheet->getCell("K$row")->getDataValidation();
            $dateValidation->setType(DataValidation::TYPE_DATE);
            $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
            $dateValidation->setAllowBlank(true);
            $dateValidation->setShowInputMessage(true);
            $dateValidation->setPrompt('Format tanggal: YYYY-MM-DD');
            $dateValidation->setError('Harap masukkan tanggal dengan format YYYY-MM-DD');
        }

        // Sembunyikan sheet daftar
        $spreadsheet->getSheetByName('TokoList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
        $spreadsheet->getSheetByName('DefaultShiftList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
        $spreadsheet->getSheetByName('DivisiList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);
        $spreadsheet->getSheetByName('RoleList')->setSheetState(Worksheet::SHEETSTATE_HIDDEN);

        // Export file
        $writer = new Xlsx($spreadsheet);
        $fileName = 'template_import_karyawan.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return Response::download($temp_file, $fileName)->deleteFileAfterSend(true);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportTemplate()
    {
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

        $karyawans = User::where('role', 'karyawan')->pluck('nama_karyawan');
        foreach ($karyawans as $index => $nama) {
            $karyawanSheet->setCellValue('A' . ($index + 1), $nama);
        }

        // Sheet 3: Shift List
        $shiftSheet = new Worksheet($spreadsheet, 'ShiftList');
        $spreadsheet->addSheet($shiftSheet);

        $shifts = Shift::pluck('nama_shift');
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
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\DepartmentModel;
use App\Models\LeaveRequestModel;
use App\Models\SettingModel;
use App\Models\TaskModel;
use App\Models\WorkActivityModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends BaseController
{
    public function index()
    {
        $type = (string) ($this->request->getGet('type') ?: 'attendance');
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));
        $departmentId = (string) ($this->request->getGet('department_id') ?: '');

        $data = $this->fetchReportData($type, $startDate, $endDate, $departmentId);

        return view('admin/reports/index', [
            'pageTitle' => 'Laporan & Analitik',
            'type' => $type,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'departmentId' => $departmentId,
            'departments' => (new DepartmentModel())->orderBy('name', 'ASC')->findAll(),
            'reportTitle' => $data['title'],
            'headers' => $data['headers'],
            'rows' => $data['rows'],
            'summary' => $data['summary'],
            'chartLabels' => $data['chartLabels'],
            'chartValues' => $data['chartValues'],
        ]);
    }

    public function exportPdf()
    {
        $type = (string) ($this->request->getGet('type') ?: 'attendance');
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));
        $departmentId = (string) ($this->request->getGet('department_id') ?: '');

        $data = $this->fetchReportData($type, $startDate, $endDate, $departmentId);
        $settings = (new SettingModel())->getAllAsKeyValue();

        $html = view('admin/reports/pdf_template', [
            'companyName' => $settings['company_name'] ?? 'PT Worklog Indonesia',
            'companyAddress' => $settings['company_address'] ?? '',
            'reportTitle' => $data['title'],
            'startDate' => $startDate,
            'endDate' => $endDate,
            'headers' => $data['headers'],
            'rows' => $data['rows'],
            'summary' => $data['summary'],
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = "WORKLOG_{$type}_{$startDate}_sd_{$endDate}.pdf";
        log_activity('EXPORT_PDF', 'reports', "Mengunduh laporan PDF: {$data['title']} ({$startDate} s/d {$endDate})");

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    public function exportExcel()
    {
        $type = (string) ($this->request->getGet('type') ?: 'attendance');
        $startDate = (string) ($this->request->getGet('start_date') ?: date('Y-m-01'));
        $endDate = (string) ($this->request->getGet('end_date') ?: date('Y-m-d'));
        $departmentId = (string) ($this->request->getGet('department_id') ?: '');

        $data = $this->fetchReportData($type, $startDate, $endDate, $departmentId);
        $settings = (new SettingModel())->getAllAsKeyValue();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan WORKLOG');

        // Judul & Periode
        $sheet->setCellValue('A1', ($settings['company_name'] ?? 'PT Worklog Indonesia') . ' — ' . $data['title']);
        $sheet->setCellValue('A2', 'Periode: ' . date('d M Y', strtotime($startDate)) . ' s/d ' . date('d M Y', strtotime($endDate)));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);
        $sheet->getStyle('A2')->getFont()->setSize(10);

        // Header Tabel (Baris 4)
        $colIndex = 1;
        foreach ($data['headers'] as $header) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '4', $header);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
            $colIndex++;
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($data['headers']));
        $sheet->getStyle("A4:{$lastCol}4")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        // Isi Data (Mulai Baris 5)
        $rowNum = 5;
        foreach ($data['rows'] as $row) {
            $c = 1;
            foreach ($row as $cellValue) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->setCellValue($colLetter . $rowNum, $cellValue);
                $c++;
            }
            $rowNum++;
        }

        $filename = "WORKLOG_{$type}_{$startDate}_sd_{$endDate}.xlsx";
        log_activity('EXPORT_EXCEL', 'reports', "Mengunduh laporan Excel: {$data['title']} ({$startDate} s/d {$endDate})");

        ob_start();
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($excelOutput);
    }

    private function fetchReportData(string $type, string $startDate, string $endDate, string $departmentId): array
    {
        switch ($type) {
            case 'activities':
                $builder = (new WorkActivityModel())
                    ->select('work_activities.*, employees.employee_code, employees.full_name, departments.name as department_name')
                    ->join('employees', 'employees.id = work_activities.employee_id')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->where('work_activities.activity_date >=', $startDate)
                    ->where('work_activities.activity_date <=', $endDate)
                    ->orderBy('work_activities.activity_date', 'DESC');

                if ($departmentId !== '') {
                    $builder->where('employees.department_id', $departmentId);
                }

                $records = $builder->findAll();
                $rows = [];
                $counts = ['approved' => 0, 'pending' => 0, 'rejected' => 0, 'draft' => 0];

                foreach ($records as $r) {
                    $counts[$r['approval_status']] = ($counts[$r['approval_status']] ?? 0) + 1;
                    $rows[] = [
                        date('d M Y', strtotime($r['activity_date'])),
                        $r['employee_code'],
                        $r['full_name'],
                        $r['department_name'] ?? '-',
                        $r['title'],
                        substr($r['start_time'], 0, 5) . ' - ' . substr($r['end_time'], 0, 5),
                        $r['progress'] . '%',
                        ucfirst($r['approval_status']),
                    ];
                }

                return [
                    'title' => 'Laporan Aktivitas Karyawan',
                    'headers' => ['Tanggal', 'Kode', 'Nama Karyawan', 'Departemen', 'Judul Aktivitas', 'Jam', 'Progres', 'Status ACC'],
                    'rows' => $rows,
                    'summary' => [
                        'Total Aktivitas' => count($records),
                        'Disetujui' => $counts['approved'],
                        'Menunggu ACC' => $counts['pending'],
                        'Ditolak' => $counts['rejected'],
                    ],
                    'chartLabels' => ['Disetujui', 'Menunggu', 'Ditolak', 'Draft'],
                    'chartValues' => [$counts['approved'], $counts['pending'], $counts['rejected'], $counts['draft']],
                ];

            case 'tasks':
                $builder = (new TaskModel())
                    ->select('tasks.*, employees.employee_code, employees.full_name, departments.name as department_name')
                    ->join('employees', 'employees.id = tasks.assigned_to')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->where('tasks.deadline >=', $startDate)
                    ->where('tasks.deadline <=', $endDate)
                    ->orderBy('tasks.deadline', 'ASC');

                if ($departmentId !== '') {
                    $builder->where('employees.department_id', $departmentId);
                }

                $records = $builder->findAll();
                $rows = [];
                $counts = ['todo' => 0, 'in_progress' => 0, 'review' => 0, 'completed' => 0];

                foreach ($records as $r) {
                    $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;
                    $rows[] = [
                        $r['title'],
                        $r['full_name'],
                        $r['department_name'] ?? '-',
                        ucfirst($r['priority']),
                        date('d M Y', strtotime($r['deadline'])),
                        $r['progress'] . '%',
                        ucfirst(str_replace('_', ' ', $r['status'])),
                    ];
                }

                return [
                    'title' => 'Laporan Progres & Tugas Karyawan',
                    'headers' => ['Judul Tugas', 'PIC', 'Departemen', 'Prioritas', 'Deadline', 'Progres', 'Status'],
                    'rows' => $rows,
                    'summary' => [
                        'Total Tugas' => count($records),
                        'Selesai' => $counts['completed'],
                        'Proses' => $counts['in_progress'] + $counts['review'],
                        'To Do' => $counts['todo'],
                    ],
                    'chartLabels' => ['To Do', 'Proses', 'Review', 'Selesai'],
                    'chartValues' => [$counts['todo'], $counts['in_progress'], $counts['review'], $counts['completed']],
                ];

            case 'leaves':
                $builder = (new LeaveRequestModel())
                    ->select('leave_requests.*, employees.employee_code, employees.full_name, departments.name as department_name')
                    ->join('employees', 'employees.id = leave_requests.employee_id')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->where('leave_requests.start_date >=', $startDate)
                    ->where('leave_requests.start_date <=', $endDate)
                    ->orderBy('leave_requests.start_date', 'DESC');

                if ($departmentId !== '') {
                    $builder->where('employees.department_id', $departmentId);
                }

                $records = $builder->findAll();
                $rows = [];
                $counts = ['izin' => 0, 'sakit' => 0, 'cuti' => 0];
                $approvedCount = 0;

                foreach ($records as $r) {
                    $counts[$r['type']] = ($counts[$r['type']] ?? 0) + 1;
                    if ($r['status'] === 'approved') {
                        $approvedCount++;
                    }
                    $rows[] = [
                        $r['employee_code'],
                        $r['full_name'],
                        $r['department_name'] ?? '-',
                        ucfirst($r['type']),
                        date('d M Y', strtotime($r['start_date'])) . ' - ' . date('d M Y', strtotime($r['end_date'])),
                        $r['reason'],
                        ucfirst($r['status']),
                    ];
                }

                return [
                    'title' => 'Laporan Pengajuan Izin & Cuti',
                    'headers' => ['Kode', 'Nama Karyawan', 'Departemen', 'Jenis', 'Periode', 'Alasan', 'Status'],
                    'rows' => $rows,
                    'summary' => [
                        'Total Pengajuan' => count($records),
                        'Disetujui' => $approvedCount,
                        'Sakit' => $counts['sakit'],
                        'Izin / Cuti' => $counts['izin'] + $counts['cuti'],
                    ],
                    'chartLabels' => ['Izin', 'Sakit', 'Cuti'],
                    'chartValues' => [$counts['izin'], $counts['sakit'], $counts['cuti']],
                ];

            case 'attendance':
            default:
                $builder = (new AttendanceModel())
                    ->select('attendance.*, employees.employee_code, employees.full_name, departments.name as department_name')
                    ->join('employees', 'employees.id = attendance.employee_id')
                    ->join('departments', 'departments.id = employees.department_id', 'left')
                    ->where('attendance.date >=', $startDate)
                    ->where('attendance.date <=', $endDate)
                    ->orderBy('attendance.date', 'DESC');

                if ($departmentId !== '') {
                    $builder->where('employees.department_id', $departmentId);
                }

                $records = $builder->findAll();
                $rows = [];
                $counts = ['present' => 0, 'late' => 0, 'absent' => 0, 'leave' => 0];
                $totalMinutes = 0;

                foreach ($records as $r) {
                    $counts[$r['status']] = ($counts[$r['status']] ?? 0) + 1;
                    $totalMinutes += (int) $r['work_duration_minutes'];
                    $rows[] = [
                        date('d M Y', strtotime($r['date'])),
                        $r['employee_code'],
                        $r['full_name'],
                        $r['department_name'] ?? '-',
                        $r['check_in'] ? date('H:i', strtotime($r['check_in'])) : '-',
                        $r['check_out'] ? date('H:i', strtotime($r['check_out'])) : '-',
                        round(((int) $r['work_duration_minutes']) / 60, 1) . ' jam',
                        ucfirst($r['status']),
                    ];
                }

                return [
                    'title' => 'Laporan Kehadiran & Keterlambatan',
                    'headers' => ['Tanggal', 'Kode', 'Nama Karyawan', 'Departemen', 'Check-in', 'Check-out', 'Durasi', 'Status'],
                    'rows' => $rows,
                    'summary' => [
                        'Total Catatan' => count($records),
                        'Hadir Tepat' => $counts['present'],
                        'Terlambat' => $counts['late'],
                        'Total Jam' => round($totalMinutes / 60, 1) . ' Jam',
                    ],
                    'chartLabels' => ['Hadir', 'Terlambat', 'Izin/Cuti', 'Tidak Hadir'],
                    'chartValues' => [$counts['present'], $counts['late'], $counts['leave'], $counts['absent']],
                ];
        }
    }
}
<?php

namespace App\Exports;

use App\Models\CrawlRun;
use App\Models\Cv;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Collection;

class TrainingDataExport
{
    protected $crawlRun;
    protected $excel;

    public function __construct(CrawlRun $crawlRun)
    {
        $this->crawlRun = $crawlRun;
        $this->excel = app('excel'); // Lấy instance Excel từ container
    }

    /**
     * Trả về mảng dữ liệu để export
     */
    public function getData()
    {
        $data = [];

        // Header
        $data[] = [
            'STT',
            'CV ID',
            'Nội dung CV (text)',
            'Mức lương',
            'Kinh nghiệm',
            'Địa điểm',
            'Matching Score (%)',
        ];

        $cvUsed = $this->crawlRun->cv_used ?? [];
        $cvId = $cvUsed['cv_id'] ?? null;
        $cvText = 'Nội dung CV không khả dụng';

        if ($cvId) {
            $cv = Cv::find($cvId);
            if ($cv && $cv->text_content) {
                $cvText = $cv->text_content;
            }
        }

        $details = $this->crawlRun->detail ?? [];
        $results = $this->crawlRun->result ?? [];

        // Map score theo URL
        $scoreMap = [];
        foreach ($results as $result) {
            if (isset($result['url'])) {
                $scoreMap[$result['url']] = $result['Matching Score (%)'] ?? '0';
            }
        }

        $index = 1;
        foreach ($details as $job) {
            $jobUrl = $job['url'] ?? '';
            $score = $scoreMap[$jobUrl] ?? '0';

            $salary = 'Thoả thuận';
            if (!empty($job['salary'])) {
                if (isset($job['salary']['max'])) {
                    $salary = 'Đến ' . $job['salary']['max'] . ' triệu';
                } elseif (is_array($job['salary'])) {
                    $salary = implode(', ', $job['salary']);
                }
            }

            $experience = 'Không yêu cầu';
            if (isset($job['experience']['min_years'])) {
                $min = $job['experience']['min_years'];
                $experience = is_numeric($min) ? $min . ' năm' : $min;
            }

            $data[] = [
                $index++,
                $cvId ?? '',
                $cvText,
                $salary,
                $experience,
                $job['location'] ?? '',
                $score,
            ];
        }

        return $data;
    }

    /**
     * Phương thức export - được gọi bởi Excel::download()
     */
    public function download($filename)
    {
        return $this->excel->create($filename, function($excel) {
            $excel->sheet('Training Data', function($sheet) {
                $sheet->fromArray($this->getData(), null, 'A1', true);
            });
        })->download('xlsx');
    }
}
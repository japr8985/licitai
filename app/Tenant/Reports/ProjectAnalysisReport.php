<?php

namespace App\Tenant\Reports;

use TCPDF;

class ProjectAnalysisReport extends TCPDF
{
    protected $project;
    protected $data;
    // Colores corporativos
    const COLOR_NAVY = [10, 25, 47]; // #0a192f
    const COLOR_TEAL = [13, 242, 242]; // #0df2f2
    const COLOR_RED = [239, 68, 68]; // #ef4444
    const COLOR_GRAY = [100, 116, 139];
    /**
     * Create a new class instance.
     */
    public function __construct($project)
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $this->project = $project;
        $this->data = $project->ai_analysis_data;

        // Configuración Inicial
        $this->SetCreator('LicitAI Neural Engine');
        $this->SetAuthor('LicitAI');
        $this->SetTitle('Project Analysis Report - ' . $project->title);
        $this->SetMargins(15, 35, 15); // Márgenes (Left, Top, Right)
        $this->SetAutoPageBreak(TRUE, 25);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }
    // Cabecera Personalizada
    public function Header()
    {
        // Logo (Texto simulado o imagen)
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->Cell(0, 10, 'LicitAI', 0, 1, 'L');
        
        // Línea decorativa Teal
        $this->SetLineStyle(['width' => 0.5, 'color' => self::COLOR_TEAL]);
        $this->Line(15, 20, 195, 20);

        // Subtítulo del proyecto
        $this->SetFont('helvetica', 'I', 9);
        $this->SetTextColorArray(self::COLOR_GRAY);
        $this->SetXY(15, 22);
        $this->Cell(0, 10, 'ANALYSIS REF: ' . strtoupper($this->project->id), 0, 0, 'L');
        $this->Cell(0, 10, 'GENERATED: ' . date('Y-m-d H:i'), 0, 0, 'R');
    }
    // Pie de Página
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColorArray(self::COLOR_GRAY);
        $this->Cell(0, 10, 'CONFIDENTIAL - LicitAI Intelligence Report | Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }
    // Método principal para construir el contenido
    public function build()
    {
        $this->AddPage();

        // 1. Título e Info Principal
        $this->SetFont('helvetica', 'B', 18);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->MultiCell(0, 10, $this->project->title, 0, 'L');
        
        $this->Ln(5);
        $this->SetFont('helvetica', '', 11);
        $this->SetTextColorArray(self::COLOR_GRAY);
        $this->Cell(0, 10, 'ENTITY: ' . $this->project->issuing_entity, 0, 1);
        $this->Cell(0, 10, 'BUDGET DETECTED: ' . ($this->data['meta_data']['detected_budget'] ?? 'N/A'), 0, 1);

        $this->Ln(10);

        // 2. Score y Veredicto (Caja Destacada)
        $this->renderScoreBox();

        $this->Ln(10);

        // 3. Executive Summary
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->Cell(0, 10, 'EXECUTIVE SUMMARY', 0, 1);
        
        $this->SetFont('courier', '', 10); // Monospace para vibe "Terminal"
        $this->SetFillColor(245, 248, 250); // Fondo gris muy claro
        $this->MultiCell(0, 0, $this->data['analysis_core']['executive_summary'], 0, 'L', 1, 1, '', '', true, 0, true, true, 0);

        $this->Ln(10);

        // 4. Riesgos (Red Flags)
        $this->renderRisks();

        $this->Ln(10);

        // 5. Métricas (Tabla)
        $this->renderMetrics();

        // Salida del PDF
        return $this->Output('licitai_report.pdf', 'S'); // 'S' devuelve el string (blob)
    }
    private function renderScoreBox()
    {
        $score = $this->project->ai_score;
        $verdict = $this->project->ai_verdict ?? 'PENDING';
        
        // Colores dinámicos
        $scoreColor = ($score >= 80) ? '#10b981' : (($score >= 50) ? '#f59e0b' : '#ef4444');

        $html = <<<EOD
        <table cellspacing="0" cellpadding="10" border="1" style="border-color: #e2e8f0;">
            <tr>
                <td width="30%" align="center" style="background-color: {$scoreColor}; color: white;">
                    <br><span style="font-size: 30px; font-weight: bold;">{$score}</span><br>
                    <span style="font-size: 10px;">/ 100</span>
                </td>
                <td width="70%" style="background-color: #f8fafc;">
                    <span style="font-size: 10px; color: #64748b; font-weight: bold;">VERDICT</span><br>
                    <span style="font-size: 16px; font-weight: bold; color: #0f172a;">{$verdict}</span><br><br>
                    <span style="font-size: 9px; color: #64748b;">AI Confidence: HIGH</span>
                </td>
            </tr>
        </table>
        EOD;

        $this->writeHTML($html, true, false, true, false, '');
    }
    private function renderRisks()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->Cell(0, 10, 'CRITICAL RISK FACTORS', 0, 1);

        $risks = $this->data['margin_protection']['financial_red_flags'] ?? [];

        if (empty($risks)) {
            $this->SetFont('helvetica', 'I', 10);
            $this->Cell(0, 10, 'No critical risks detected.', 0, 1);
            return;
        }

        $html = '<ul style="list-style-type: none;">';
        foreach ($risks as $risk) {
            $html .= '<li style="color: #ef4444; font-weight: bold; padding-bottom: 5px;">⚠️ <span style="color: #334155; font-weight: normal;">' . $risk . '</span></li><br>';
        }
        $html .= '</ul>';

        $this->writeHTML($html, true, false, true, false, '');
    }
    private function renderMetrics()
    {
        $breakdown = $this->data['scoring_engine']['breakdown'] ?? [];
        
        $html = '<table border="0" cellpadding="5">';
        $html .= '<tr style="background-color: #0f172a; color: white; font-weight: bold;">
                    <th width="70%">METRIC CATEGORY</th>
                    <th width="30%" align="right">SCORE</th>
                  </tr>';
        
        $bg = false;
        foreach ($breakdown as $key => $val) {
            $color = $bg ? '#f1f5f9' : '#ffffff';
            $label = ucwords(str_replace('_', ' ', str_replace('_score', '', $key)));
            
            $html .= '<tr style="background-color: '.$color.';">
                        <td style="color: #334155;">'.$label.'</td>
                        <td align="right" style="font-weight: bold; color: #0f172a;">'.$val.'</td>
                      </tr>';
            $bg = !$bg;
        }
        $html .= '</table>';

        $this->writeHTML($html, true, false, true, false, '');
    }
}

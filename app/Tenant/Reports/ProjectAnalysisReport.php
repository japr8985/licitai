<?php

namespace App\Tenant\Reports;

use TCPDF;

class ProjectAnalysisReport extends TCPDF
{
    protected $project;
    protected $data;

    // Colores corporativos RGB (Para métodos nativos de TCPDF)
    const COLOR_NAVY = [10, 25, 47]; // #0a192f
    const COLOR_TEAL = [13, 242, 242]; // #0df2f2
    const COLOR_GRAY = [100, 116, 139];

    // Colores Hexadecimales (Para el HTML interno)
    const HEX_NAVY = '#0a192f';
    const HEX_TEAL = '#0df2f2';
    const HEX_RED = '#ef4444';
    const HEX_AMBER = '#f59e0b';
    const HEX_GREEN = '#10b981';
    const HEX_GRAY = '#64748b';
    const HEX_BG_LIGHT = '#f8fafc';

    public function __construct($project)
    {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $this->project = $project;
        $this->data = $project->ai_analysis_data;

        // Configuración Inicial
        $this->SetCreator('LicitAI ');
        $this->SetAuthor('LicitAI');
        $this->SetTitle('Project Analysis Report - ' . $project->title);
        $this->SetMargins(15, 30, 15); // Márgenes (Left, Top, Right)
        $this->SetAutoPageBreak(TRUE, 25);
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
    }

    // Cabecera Personalizada
    public function Header()
    {
        // Título del Sistema
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->SetY(10);
        $this->Cell(0, 10, 'LICITAI', 0, 1, 'L');
        
        // Línea decorativa Teal
        $this->SetLineStyle(['width' => 0.5, 'color' => self::COLOR_TEAL]);
        $this->Line(15, 19, 195, 19);

        // Subtítulo / Meta del documento
        $this->SetFont('courier', 'B', 9);
        $this->SetTextColorArray(self::COLOR_GRAY);
        $this->SetXY(15, 20);
        $this->Cell(0, 10, 'SYS_REF: ' . substr($this->project->id, 0, 13) . '...', 0, 0, 'L');
        $this->Cell(0, 10, 'TIMESTAMP: ' . date('Y-m-d H:i:s'), 0, 0, 'R');
    }

    // Pie de Página
    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('courier', 'I', 8);
        $this->SetTextColorArray(self::COLOR_GRAY);
        $this->SetLineStyle(['width' => 0.2, 'color' => self::COLOR_GRAY]);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        
        $this->Cell(0, 10, 'RESTRICTED ACCESS - LICITAI INTELLIGENCE REPORT | PAGE ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'C');
    }

    // Método principal
    public function build()
    {
        $this->AddPage();

        // 1. Título del Proyecto e Información Base
        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->MultiCell(0, 8, mb_strtoupper($this->project->title), 0, 'L');
        
        $this->Ln(3);
        $this->SetFont('helvetica', 'B', 10);
        $this->SetTextColorArray(self::COLOR_GRAY);
        
        $meta = $this->data['meta_data'] ?? [];
        $budget = isset($meta['detected_budget']) ? $meta['currency'] . ' $' . $meta['detected_budget'] : 'N/A';
        $industry = $meta['industry'] ?? 'General';

        $this->Cell(0, 6, 'ENTITY: ' . mb_strtoupper($this->project->issuing_entity), 0, 1);
        $this->Cell(90, 6, 'BUDGET: ' . $budget, 0, 0);
        $this->Cell(90, 6, 'INDUSTRY: ' . mb_strtoupper($industry), 0, 1);

        $this->Ln(8);

        // 2. Score y Matriz de Decisión
        $this->renderDecisionMatrix();
        $this->Ln(8);

        // 3. Executive Summary
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->Cell(0, 10, 'EXECUTIVE SUMMARY', 0, 1);
        
        $this->SetFont('courier', '', 10); // Monospace para vibe Terminal
        $this->SetFillColor(248, 250, 252); // self::HEX_BG_LIGHT
        $this->SetTextColor(10, 25, 47);
        $summary = $this->data['analysis_core']['executive_summary'] ?? 'No summary available.';
        $this->MultiCell(0, 0, $summary, 0, 'J', 1, 1, '', '', true, 0, true, true, 0);

        $this->Ln(8);

        // 4. Riesgos (Red Flags) - Usando la nueva estructura
        $this->renderRisks();
        $this->Ln(8);

        // 5. Métricas (Tabla Desglosada)
        $this->renderMetrics();

        // Salida
        return $this->Output('licitai_report.pdf', 'S');
    }

    private function renderDecisionMatrix()
    {
        $score = $this->data['scoring_engine']['total_score'] ?? $this->project->ai_score;
        $recommendation = $this->data['decision_matrix']['recommendation'] ?? 'PENDING';
        $reasoning = $this->data['decision_matrix']['reasoning'] ?? '';
        
        // Color de Score
        $scoreColor = ($score >= 80) ? self::HEX_GREEN : (($score >= 50) ? self::HEX_AMBER : self::HEX_RED);

        $html = <<<EOD
        <table cellspacing="0" cellpadding="10" border="1" style="border-color: #cbd5e1;">
            <tr>
                <td width="25%" align="center" style="background-color: {$scoreColor}; color: white;">
                    <br><span style="font-size: 32px; font-weight: bold;">{$score}</span><br>
                    <span style="font-size: 10px; font-family: courier;">TOTAL SCORE</span>
                </td>
                <td width="75%" style="background-color: #ffffff;">
                    <span style="font-size: 10px; color: #64748b; font-weight: bold;">AI RECOMMENDATION</span><br>
                    <span style="font-size: 16px; font-weight: bold; color: {$scoreColor};">{$recommendation}</span><br><br>
                    <span style="font-size: 10px; color: #0a192f;">{$reasoning}</span>
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
        $this->Cell(0, 10, 'FINANCIAL & OPERATIONAL RED FLAGS', 0, 1);

        $risks = $this->data['margin_protection']['financial_red_flags'] ?? [];

        if (empty($risks)) {
            $this->SetFont('helvetica', 'B', 10);
            $this->SetTextColorArray(16, 185, 129); // Verde
            $this->Cell(0, 10, '[✓] NO CRITICAL RISKS DETECTED IN THIS PLIEGO.', 0, 1);
            return;
        }

        $html = '';
        foreach ($risks as $risk) {
            $sev = strtoupper($risk['severity'] ?? 'LOW');
            
            // Asignar color según severidad
            $sevColor = match($sev) {
                'HIGH' => self::HEX_RED,
                'MODERATE' => self::HEX_AMBER,
                default => self::HEX_TEAL,
            };

            // Simulación de una "Card" de Vue usando Tablas HTML de TCPDF
            $html .= <<<EOD
            <table cellspacing="0" cellpadding="8" border="1" style="border-color: #cbd5e1;">
                <tr style="background-color: #f8fafc;">
                    <td>
                        <strong style="color: {$sevColor}; font-size: 11px;">[{$sev} RISK] {$risk['title']}</strong><br>
                        <span style="font-size: 9px; font-family: courier; color: #64748b;">REF: {$risk['reference']}</span>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #ffffff; color: #0a192f; font-size: 10px;">
                        {$risk['description']}
                    </td>
                </tr>
            </table>
            <br>
            EOD;
        }

        $this->writeHTML($html, true, false, true, false, '');
    }

    private function renderMetrics()
    {
        $this->SetFont('helvetica', 'B', 12);
        $this->SetTextColorArray(self::COLOR_NAVY);
        $this->Cell(0, 10, 'METRICS BREAKDOWN', 0, 1);

        $breakdown = $this->data['scoring_engine']['breakdown'] ?? [];
        
        // Puntuaciones máximas teóricas para mostrar "X / MAX"
        $maxScores = [
            'financial_score' => 40,
            'technical_scope_score' => 30,
            'legal_compliance_score' => 20,
            'timeline_score' => 10
        ];

        $html = '<table border="1" cellpadding="8" style="border-color: #cbd5e1;">';
        $html .= '<tr style="background-color: #0a192f; color: #ffffff; font-weight: bold;">
                    <th width="70%">CATEGORY</th>
                    <th width="30%" align="center">SCORE / MAX</th>
                  </tr>';
        
        $bg = false;
        foreach ($breakdown as $key => $val) {
            $color = $bg ? '#f8fafc' : '#ffffff';
            $label = ucwords(str_replace('_', ' ', str_replace('_score', '', $key)));
            $max = $maxScores[$key] ?? 100;

            // Alerta visual si el puntaje en esa área es menor al 50% de su capacidad
            $scoreColor = ($val < ($max / 2)) ? self::HEX_RED : self::HEX_NAVY;
            
            $html .= '<tr style="background-color: '.$color.';">
                        <td style="color: #334155; font-size: 10px; font-weight: bold;">'.$label.'</td>
                        <td align="center" style="font-weight: bold; font-family: courier; color: '.$scoreColor.';">'.$val.' / '.$max.'</td>
                      </tr>';
            $bg = !$bg;
        }
        $html .= '</table>';

        $this->writeHTML($html, true, false, true, false, '');
    }
}
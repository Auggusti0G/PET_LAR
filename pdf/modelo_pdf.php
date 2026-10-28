<?php

    // =================================================================
    // pdf/modelo_pdf.php - MODELO DOS PDFs
    // Usa a biblioteca FPDF (pasta lib/fpdf), que gera PDF com PHP
    // puro, sem instalar nada no servidor.
    // Aqui criamos uma classe filha da FPDF com o cabeçalho e o
    // rodapé que se repetem em todas as páginas.
    // =================================================================

    require '../lib/fpdf/fpdf.php';

    // A FPDF não entende UTF-8 (acentos). Esta função converte o texto.
    function txt($texto) {
        return iconv('UTF-8', 'windows-1252//TRANSLIT', (string)$texto);
    }

    class PdfPetLar extends FPDF {

        public $tituloRelatorio = '';   // texto que aparece no topo
        public $subtitulo       = '';   // ex.: filtros usados

        // Header() é chamado sozinho pela FPDF no começo de cada página
        function Header() {
            // Faixa colorida com o nome do sistema
            $this->SetFillColor(31, 78, 95);          // azul petróleo
            $this->Rect(0, 0, $this->GetPageWidth(), 22, 'F');
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Helvetica', 'B', 16);
            $this->SetXY(10, 6);
            $this->Cell(0, 10, txt('Pet Lar'), 0, 0, 'L');
            $this->SetFont('Helvetica', '', 9);
            $this->Cell(0, 10, txt('Gestão de adoção de animais'), 0, 0, 'R');

            // Título do relatório
            $this->SetTextColor(31, 78, 95);
            $this->SetFont('Helvetica', 'B', 13);
            $this->SetXY(10, 27);
            $this->Cell(0, 7, txt($this->tituloRelatorio), 0, 1, 'L');
            $this->SetTextColor(100, 100, 100);
            $this->SetFont('Helvetica', '', 9);
            $this->Cell(0, 5, txt($this->subtitulo), 0, 1, 'L');
            $this->Ln(3);
        }

        // Footer() é chamado sozinho no fim de cada página
        function Footer() {
            $this->SetY(-14);
            $this->SetDrawColor(217, 96, 59);         // linha terracota
            $this->Line(10, $this->GetY(), $this->GetPageWidth() - 10, $this->GetY());
            $this->SetTextColor(120, 120, 120);
            $this->SetFont('Helvetica', '', 8);
            $this->Cell(0, 8, txt('Emitido em ' . date('d/m/Y H:i') . ' por ' . $_SESSION['nome']), 0, 0, 'L');
            $this->Cell(0, 8, txt('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
        }

        // Desenha a linha de títulos de uma tabela
        function cabecalhoTabela($colunas, $larguras) {
            $this->SetFillColor(31, 78, 95);
            $this->SetTextColor(255, 255, 255);
            $this->SetFont('Helvetica', 'B', 9);
            foreach ($colunas as $i => $coluna) {
                $this->Cell($larguras[$i], 8, txt($coluna), 0, 0, 'L', true);
            }
            $this->Ln();
            $this->SetTextColor(40, 40, 40);
            $this->SetFont('Helvetica', '', 9);
        }
    }

?>

<?php

    // =================================================================
    // pdf/relatorio.php - RELATÓRIO CONSOLIDADO EM PDF
    // Mesmas consultas do relatorio/index.php, mas desenhadas no PDF.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';
    include 'modelo_pdf.php';

    $inicio = isset($_GET['inicio']) && $_GET['inicio'] != '' ? $_GET['inicio'] : date('Y') . '-01-01';
    $fim    = isset($_GET['fim'])    && $_GET['fim']    != '' ? $_GET['fim']    : date('Y') . '-12-31';

    // --- Situação geral ----
    $sql = "SELECT COUNT(*) AS total,
                   SUM(CASE WHEN status = 'Disponível'    THEN 1 ELSE 0 END) AS disponiveis,
                   SUM(CASE WHEN status = 'Em tratamento' THEN 1 ELSE 0 END) AS tratamento,
                   SUM(CASE WHEN status = 'Adotado'       THEN 1 ELSE 0 END) AS adotados
            FROM animal";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $geral = $stmt->fetch();
    $taxaAdocao = $geral['total'] > 0 ? round($geral['adotados'] * 100 / $geral['total']) : 0;

    // --- Por espécie ----
    $sql = "SELECT especie.nome AS especie,
                   COUNT(animal.id) AS total,
                   SUM(CASE WHEN animal.status = 'Disponível'    THEN 1 ELSE 0 END) AS disponiveis,
                   SUM(CASE WHEN animal.status = 'Em tratamento' THEN 1 ELSE 0 END) AS tratamento,
                   SUM(CASE WHEN animal.status = 'Adotado'       THEN 1 ELSE 0 END) AS adotados
            FROM especie
            LEFT JOIN animal ON animal.especie_id = especie.id
            GROUP BY especie.id, especie.nome
            ORDER BY total DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $porEspecie = $stmt->fetchAll();

    // --- Adoções do período (detalhadas) ----
    $sql = "SELECT adocao.data_adocao, animal.nome AS animal, adotante.nome AS adotante, voluntario.nome AS voluntario
            FROM adocao
            INNER JOIN animal     ON animal.id     = adocao.animal_id
            INNER JOIN adotante   ON adotante.id   = adocao.adotante_id
            INNER JOIN voluntario ON voluntario.id = adocao.voluntario_id
            WHERE adocao.data_adocao BETWEEN :inicio AND :fim
            ORDER BY adocao.data_adocao";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":inicio", $inicio);
    $stmt->bindParam(":fim", $fim);
    $stmt->execute();
    $adocoes = $stmt->fetchAll();

    // --- Montagem do PDF ----
    $pdf = new PdfPetLar('P', 'mm', 'A4');   // P = retrato (em pé)
    $pdf->AliasNbPages();
    $pdf->tituloRelatorio = 'Relatório consolidado do abrigo';
    $pdf->subtitulo = 'Período das adoções: ' . date('d/m/Y', strtotime($inicio)) . ' a ' . date('d/m/Y', strtotime($fim));
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->AddPage();

    // Quadros de números (5 caixas lado a lado)
    $caixas = array(
        'Total'         => $geral['total'],
        'Disponíveis'   => (int)$geral['disponiveis'],
        'Em tratamento' => (int)$geral['tratamento'],
        'Adotados'      => (int)$geral['adotados'],
        'Taxa de adoção'=> $taxaAdocao . '%'
    );
    $x = 10;
    foreach ($caixas as $rotulo => $valor) {
        $pdf->SetFillColor(251, 246, 239);
        $pdf->Rect($x, $pdf->GetY(), 36, 20, 'F');
        $pdf->SetXY($x, $pdf->GetY() + 2);
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(110, 110, 110);
        $pdf->Cell(36, 5, txt($rotulo), 0, 2, 'C');
        $pdf->SetFont('Helvetica', 'B', 16);
        $pdf->SetTextColor(217, 96, 59);
        $pdf->Cell(36, 10, txt($valor), 0, 0, 'C');
        $pdf->SetY($pdf->GetY() - 7);
        $x += 38;
    }
    $pdf->Ln(26);

    // Tabela por espécie
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(31, 78, 95);
    $pdf->Cell(0, 8, txt('Animais por espécie'), 0, 1);
    $larguras = array(60, 30, 32, 34, 34);
    $pdf->cabecalhoTabela(array('Espécie', 'Total', 'Disponíveis', 'Em tratamento', 'Adotados'), $larguras);
    foreach ($porEspecie as $linha) {
        $pdf->Cell($larguras[0], 7, txt($linha['especie']), 'B');
        $pdf->Cell($larguras[1], 7, $linha['total'], 'B', 0, 'C');
        $pdf->Cell($larguras[2], 7, (int)$linha['disponiveis'], 'B', 0, 'C');
        $pdf->Cell($larguras[3], 7, (int)$linha['tratamento'], 'B', 0, 'C');
        $pdf->Cell($larguras[4], 7, (int)$linha['adotados'], 'B', 1, 'C');
    }
    $pdf->Ln(6);

    // Tabela de adoções do período
    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->SetTextColor(31, 78, 95);
    $pdf->Cell(0, 8, txt('Adoções no período (' . count($adocoes) . ')'), 0, 1);
    $larguras = array(28, 40, 60, 62);
    $pdf->cabecalhoTabela(array('Data', 'Animal', 'Adotante', 'Voluntário'), $larguras);
    foreach ($adocoes as $linha) {
        $pdf->Cell($larguras[0], 7, date('d/m/Y', strtotime($linha['data_adocao'])), 'B');
        $pdf->Cell($larguras[1], 7, txt($linha['animal']), 'B');
        $pdf->Cell($larguras[2], 7, txt($linha['adotante']), 'B');
        $pdf->Cell($larguras[3], 7, txt($linha['voluntario']), 'B', 1);
    }

    $pdf->Output('I', 'relatorio_pet_lar.pdf');

?>

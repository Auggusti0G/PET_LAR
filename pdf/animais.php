<?php

    // =================================================================
    // pdf/animais.php - LISTAGEM DE ANIMAIS EM PDF
    // Usa os MESMOS filtros da busca avançada (animal/index.php),
    // então o PDF mostra exatamente o que estava na tela.
    // =================================================================

    $raiz = '../';
    include '../verifica_login.php';
    include '../conexao.php';
    include 'modelo_pdf.php';

    // --- Filtros (iguais aos da tela de animais) ----
    $fNome    = isset($_GET['f_nome'])    ? trim($_GET['f_nome']) : '';
    $fEspecie = isset($_GET['f_especie']) ? $_GET['f_especie']    : '';
    $fStatus  = isset($_GET['f_status'])  ? $_GET['f_status']     : '';
    $fPorte   = isset($_GET['f_porte'])   ? $_GET['f_porte']      : '';
    $fEntrada = isset($_GET['f_entrada']) ? $_GET['f_entrada']    : '';

    $condicoes  = array();
    $parametros = array();
    $descricaoFiltros = array();     // texto que aparece no topo do PDF

    if($fNome != '') {
        $condicoes[] = "animal.nome LIKE :nome";
        $parametros[':nome'] = '%' . $fNome . '%';
        $descricaoFiltros[] = "nome contém \"$fNome\"";
    }
    if($fEspecie != '') {
        $condicoes[] = "animal.especie_id = :especie";
        $parametros[':especie'] = $fEspecie;
        $descricaoFiltros[] = "espécie escolhida";
    }
    if($fStatus != '') {
        $condicoes[] = "animal.status = :status";
        $parametros[':status'] = $fStatus;
        $descricaoFiltros[] = "situação $fStatus";
    }
    if($fPorte != '') {
        $condicoes[] = "animal.porte = :porte";
        $parametros[':porte'] = $fPorte;
        $descricaoFiltros[] = "porte $fPorte";
    }
    if($fEntrada != '') {
        $condicoes[] = "animal.data_entrada >= :entrada";
        $parametros[':entrada'] = $fEntrada;
        $descricaoFiltros[] = "entrada desde " . date('d/m/Y', strtotime($fEntrada));
    }

    // --- Consulta ----
    $sql = "SELECT animal.*, especie.nome AS especie
            FROM animal
            INNER JOIN especie ON especie.id = animal.especie_id";
    if(count($condicoes) > 0) {
        $sql .= " WHERE " . implode(" AND ", $condicoes);
    }
    $sql .= " ORDER BY animal.nome";

    $stmt = $pdo->prepare($sql);
    foreach ($parametros as $chave => $valor) {
        $stmt->bindValue($chave, $valor);
    }
    $stmt->execute();
    $animais = $stmt->fetchAll();

    // --- Montagem do PDF ----
    $pdf = new PdfPetLar('L', 'mm', 'A4');   // L = paisagem (deitado)
    $pdf->AliasNbPages();                         // permite o "de {nb}" no rodapé
    $pdf->tituloRelatorio = 'Listagem de animais';
    $pdf->subtitulo = count($descricaoFiltros) > 0
                    ? 'Filtros: ' . implode(', ', $descricaoFiltros) . ' · ' . count($animais) . ' registro(s)'
                    : 'Sem filtros · ' . count($animais) . ' registro(s)';
    $pdf->SetAutoPageBreak(true, 18);
    $pdf->AddPage();

    $colunas  = array('Nome', 'Espécie', 'Sexo', 'Porte', 'Idade', 'Entrada', 'Situação', 'Observação');
    $larguras = array(32, 25, 20, 22, 18, 25, 30, 105);   // em milímetros (total 277)
    $pdf->cabecalhoTabela($colunas, $larguras);

    // Uma linha por animal, com fundo "zebrado" (alternando cores)
    $zebra = false;
    foreach ($animais as $animal) {
        $pdf->SetFillColor(251, 246, 239);
        $pdf->Cell($larguras[0], 7, txt($animal['nome']), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[1], 7, txt($animal['especie']), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[2], 7, txt($animal['sexo']), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[3], 7, txt($animal['porte']), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[4], 7, txt($animal['idade'] . ' ano(s)'), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[5], 7, date('d/m/Y', strtotime($animal['data_entrada'])), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[6], 7, txt($animal['status']), 0, 0, 'L', $zebra);
        $pdf->Cell($larguras[7], 7, txt(mb_strimwidth((string)$animal['observacao'], 0, 70, '...')), 0, 1, 'L', $zebra);
        $zebra = !$zebra;
    }

    if(count($animais) == 0) {
        $pdf->Cell(0, 10, txt('Nenhum animal encontrado com esses filtros.'), 0, 1, 'C');
    }

    // 'I' = abre o PDF no navegador (Inline)
    $pdf->Output('I', 'animais_pet_lar.pdf');

?>

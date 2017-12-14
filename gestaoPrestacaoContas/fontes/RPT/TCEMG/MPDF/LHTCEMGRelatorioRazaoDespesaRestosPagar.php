<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<!-- DESPESA -->

<?php foreach($arData as $data): ?>
    <h5><?= $data ?></h5>
    
    <table class='border'>
        <thead>
            <tr>
                <th class='text_align_center border' style="width:10mm;">Empenho</th>
                <th class='text_align_left border' style="width:15mm;">Data Emp.</th>
                <th class='text_align_left border' style="width:15mm;">Data Liq.</th>
                <th class='text_align_left border' style="width:15mm;">Data Pag.</th>
                <th class='text_align_left border' style="width:35mm;">Credor</th>
                <th class='text_align_left border' style="width:40mm;">Banco / Ag. / Cc.</th>
                <th class='text_align_left border' style="width:10mm;">Recurso</th>
                <th class='text_align_right border' style="width:15mm;">Valor Pag.</th>
                <th class='text_align_left border' style="width:45mm;">Dotação</th>
                <th class='text_align_left border' style="width:15mm;">Recurso</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach($registros as $registro): ?>
            <?php if($registro['dt_pagamento'] == $data): ?>
            <tr>
                <td class='text_align_center border'><?= $registro['empenho'] ?></td>
                <td class='text_align_left border'><?= $registro['dt_empenho'] ?></td>
                <td class='text_align_left border'><?= $registro['dt_liquidacao'] ?></td>
                <td class='text_align_left border'><?= $registro['dt_pagamento'] ?></td>
                <td class='text_align_left border'><?= $registro['credor'] ?></td>
                <td class='text_align_left border'><?= $registro['banco'] ?></td>
                <td class='text_align_left border'><?= $registro['cod_recurso_banco'] ?></td>
                <td class='text_align_right border'><?= number_format($registro['valor_pago'], '2', ',', '.') ?></td>
                <td class='text_align_left border'><?= $registro['dotacao'] ?></td>
                <td class='text_align_left border'><?= $registro['cod_recurso'] ?></td>
            </tr>
            <?php
                    $totalPago = $totalPago + $registro['valor_pago'];
                    endif;
                endforeach;
            ?>
        
        </tbody>
    </table>
    
    <p>
        Total Pago: <?= number_format($totalPago, '2', ',', '.') ?>
    </p>
    
    <?php
            $totalGeralPago += $totalPago;
            $totalPago      = 0;
        endforeach;
    ?>
    
    <p>
        <h5>Total Geral</h5>
        Pago: <?= number_format($totalGeralPago, '2', ',', '.') ?>
    </p>
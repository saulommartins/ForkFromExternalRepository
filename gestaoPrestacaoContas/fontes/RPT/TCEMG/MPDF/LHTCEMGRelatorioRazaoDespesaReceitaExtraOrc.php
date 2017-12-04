<div class='text_align_center font_weight_bold'>
    <h4>Razão da Despesa - <?= $stTipoRelatorio ?></h4>
</div>

<!-- DESPESA -->

<?php foreach($arDataReceita as $dataReceita): ?>
    <h5><?= $dataReceita ?></h5>
    
    <table class='border'>
        <thead>
            <tr>
                <th class='text_align_left border' style="width:15mm;">Data Transf.</th>
                <th class='text_align_left border' style="width:45mm;">Conta</th>
                <th class='text_align_right border' style="width:15mm;">Valor</th>
                <th class='text_align_left border' style="width:30mm;">Banco / Ag. / Cc.</th>
                <th class='text_align_left border' style="width:35mm;">Conta Recurso</th>
            </tr>
        </thead>
        <tbody>
            
        <?php foreach($registros as $registro): ?>
            <?php if($registro['dt_transferencia'] == $dataReceita): ?>
            <tr>
                <td class='text_align_left border'><?= $registro['dt_transferencia'] ?></td>
                <td class='text_align_left border'><?= $registro['nome_conta'] ?></td>
                <td class='text_align_right border'><?= number_format($registro['valor'], '2', ',', '.') ?></td>
                <td class='text_align_left border'><?= $registro['banco'] ?></td>
                <td class='text_align_left border'><?= $registro['nom_recurso'] ?></td>
            </tr>
            <?php
                    $totalPago = $totalPago + $registro['valor'];
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
    
    
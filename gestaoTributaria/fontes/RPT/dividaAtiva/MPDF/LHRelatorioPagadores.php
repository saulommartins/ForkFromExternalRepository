<h4 class="text_align_center">Relatório de Pagadores</h4>

<?php foreach($arRegistros as $i => $registros): ?>
<h4><?= $registros['codigo'].' - '.$registros['descricao'] ?></h4>

<table class='border'>
    <thead>
        <tr>
            <th class='text_align_center font_size_8 border' width='8%'>CGM</th>
            <th class='text_align_center font_size_8 border' width='40%'>Nome</th>
            <th class='text_align_center font_size_8 border' width='10%'>Inscrição Origem</th>
            <th class='text_align_center font_size_8 border' width='10%'>Valor</th>
            <th class='text_align_center font_size_8 border' width='12%'>Código</th>
            <th class='text_align_center font_size_8 border' width='28%'>Descrição</th>
        </tr>
    </thead>
    
    <tbody>
    <?php foreach($registros['dados'] as $j => $registro): ?>
        <tr>
            <td class='text_align_left  font_size_8 border'><?= $registro['numcgm']  ?></td>
            <td class='text_align_left  font_size_8 border'><?= $registro['nom_cgm'] ?></td>
            <td class='text_align_center font_size_8 border'><?= $registro['inscricao'] ?></td>
            <td class='text_align_right font_size_8 border'><?= number_format($registro['valor'],2,',','.')?></td>
            <td class='text_align_right font_size_8 border'><?= $registro['codigo'] ?></td>
            <td class='text_align_left  font_size_8 border'><?= $registro['descricao'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    
    <tfoot>
        <tr>
            <td class='text_align_left'  colspan="6">TOTAL DE REGISTROS: <?= count($registros['dados']); ?></td>
        </tr>
    </tfoot>
</table>
<?php endforeach; ?>
<table cellPadding="5">
<thead>
    <tr>
        <th width="15mm">Sequência</th>
        <th width="25mm">Norma</th>
        <th class="text_align_left">Descrição</th>
        <th width="25mm">Publicação</th>
    </tr>
</thead>
<tbody>
    <?php
        foreach($arRecordSet as $stValor){
    ?>
    <tr>
           <td class="text_align_center vertical_align_top"><?php echo $stValor['sequencia']; ?></td>
           <td class="text_align_center vertical_align_top"><?php echo $stValor['num_norma_exercicio']; ?></td>
           <td class="text_align_justify"><?php echo $stValor['descricao']; ?></td>
           <td class="text_align_center vertical_align_top"><?php echo $stValor['dt_publicacao']; ?></td>
    </tr>
    <?php
        }
    ?>
<tbody>
</table>
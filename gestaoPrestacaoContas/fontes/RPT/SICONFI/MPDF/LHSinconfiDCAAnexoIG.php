<!-- Criando a tabela de Despesas -->
<table class="font_size_9">
    <thead>
        <tr >
            <th class="border_top border_bottom border_right" rowspan="2">Despesas por Função</th>
            <th class="border_top border_bottom border_left" colspan="4">Execução da Despesa</th>
        </tr>
        <tr>
            <th class="border" >Restos a Pagar Não Processados Pagos</th>
            <th class="border" >Restos a Pagar Não Processados Cancelados</th>
            <th class="border" >Restos a Pagar Processados Pagos</th>
            <th class="border_left border_bottom" >Restos a Pagar Processados Cancelados</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arDespesaFuncoes as $arDespesaFuncao) { ?>
        <tr>
            <td class="border_right tabulacao_nivel_<?php echo $arDespesaFuncao["nivel"]; ?>"><?php echo $arDespesaFuncao["funcao_subfuncao"]." - ".$arDespesaFuncao["descricao"]; ?></td>
            <td style="width: 40mm;" class="text_align_right border_left border_right"><?php echo $arDespesaFuncao["vl_rp_nao_processados_pagos"];?></td>
            <td style="width: 40mm;" class="text_align_right border_left border_right"><?php echo $arDespesaFuncao["vl_rp_nao_processados_cancelados"];?></td>
            <td style="width: 40mm;" class="text_align_right border_left border_right"><?php echo $arDespesaFuncao["vl_rp_processados_pagos"];?></td>
            <td style="width: 40mm;" class="text_align_right border_left"><?php echo $arDespesaFuncao["vl_rp_processados_cancelados"];?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>
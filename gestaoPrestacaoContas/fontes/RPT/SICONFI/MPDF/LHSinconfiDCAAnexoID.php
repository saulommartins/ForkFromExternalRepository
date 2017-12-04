<!-- Criando a tabela de Despesas -->
<table class="font_size_9">
    <thead>
        <tr >
            <th class="border_top border_bottom border_right" rowspan="2">Despesas Orçamentárias</th>
            <th class="border_top border_bottom border_left" colspan="5">Execução da Despesa</th>
        </tr>
        <tr>
            <th class="border" >Despesas Empenhadas</th>
            <th class="border" >Despesas Liquidadas</th>
            <th class="border" >Despesas Pagas</th>
            <th class="border" >Inscrição de RP Não Processados</th>
            <th class="border_left border_bottom" >Inscrição de RP Processados</th>
        </tr>

    </thead>

    <tbody>
        <?php foreach($arDespesaOrcamentarias as $arDespesaOrcamentaria) { ?>
        <tr>
            <td class="border_right tabulacao_nivel_<?php echo $arDespesaOrcamentaria["nivel"]; ?>"><?php echo $arDespesaOrcamentaria["classificacao"]." - ". $arDespesaOrcamentaria["descricao"]; ?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?php echo $arDespesaOrcamentaria["empenhado_ano"];?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?php echo $arDespesaOrcamentaria["liquidado_ano"];?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?php echo $arDespesaOrcamentaria["pago_ano"];?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?php echo $arDespesaOrcamentaria["nao_processados"];?></td>
            <td style="width: 28mm;" class="text_align_right border_left"><?php echo $arDespesaOrcamentaria["processados"];?></td>
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
            <td></td>
        </tr>
    </tfoot>
</table>

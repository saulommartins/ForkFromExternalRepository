<!-- Criando a tabela de Despesas -->
<table class="font_size_9">
    <thead>
        <tr>
            <th class="border_top border_bottom border_right" rowspan="2">Despesas Orçamentárias</th>
            <th class="border_top border_bottom border_left" colspan="5">Execução da Despesa</th>
        </tr>
        <tr>
            <th class="border_left border_bottom" >Restos a Pagar Não Processados Pagos</th>
            <th class="border_left border_bottom" >Restos a Pagar Não Processados Cancelados</th>
            <th class="border_left border_bottom" >Restos a Pagar Processados Pagos</th>
            <th class="border_left border_bottom" >Restos a Pagar Processados Cancelados</th>
        </tr>

    </thead>

    <tbody>
        <?php foreach($arDespElementos as $arDespElemento) { ?>
        <tr>
            <td style="width: 60%;" class="tabulacao_nivel_<?=$arDespElemento['nivel']?>"><?= $arDespElemento["cod_estrutural"] ?> - <?= $arDespElemento["descricao"] ?></td>
            <td style="width: 10%;" class="text_align_right border_left border_right"><?= $arDespElemento["vl_nao_processados_pago"] ?></td>
            <td style="width: 10%;" class="text_align_right border_left border_right"><?= $arDespElemento["vl_nao_processados_cancelado"] ?></td>
            <td style="width: 10%;" class="text_align_right border_left border_right"><?= $arDespElemento["vl_processados_pago"] ?></td>
            <td style="width: 10%;" class="text_align_right border_left border_right"><?= $arDespElemento["vl_processados_cancelado"] ?></td>
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

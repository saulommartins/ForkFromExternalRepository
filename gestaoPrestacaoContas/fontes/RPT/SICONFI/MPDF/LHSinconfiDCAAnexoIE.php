<!-- Criando a tabela de Despesas -->
<table class="font_size_9">
    <thead>
        <tr>
            <th class="border_top border_bottom border_right" rowspan="2">Despesas por Função</th>
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
        <?php foreach($arDadosDespesa as $arDespFuncao) { ?>
        <tr>
            <td class="border_right tabulacao_nivel_<?= $arDespFuncao["nivel"] ?>"> <?= $arDespFuncao["descricao"] ?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?= $arDespFuncao["despesas_empenhadas"] ?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?= $arDespFuncao["despesas_liquidadas"] ?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?= $arDespFuncao["despesas_pagas"] ?></td>
            <td style="width: 28mm;" class="text_align_right border_left border_right"><?= $arDespFuncao["restos_nao_processados"] ?></td>
            <td style="width: 28mm;" class="text_align_right border_left"><?= $arDespFuncao["restos_processados"] ?></td>
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

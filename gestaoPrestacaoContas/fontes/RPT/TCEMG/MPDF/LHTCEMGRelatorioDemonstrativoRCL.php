<!-- Criando a tabela de Receitas -->
<table class="font_size_8">
    <thead>
        <tr class="border_top border_left border_right">
            <th colspan="15">RECEITA CORRENTE LIQUIDA (LIQUIDADA)</th>
        </tr>
        <tr class="border_left border_right">
            <th colspan="15">RECEITAS</th>
        </tr>
        <tr class="border_left border_right border_bottom">
            <th style="width: 11mm;">CODIGO</th>
            <th style="width: 68mm;" class="text_align_justify" >DESCRIÇÃO</th>
            <th><?php echo $arCabecalhoMes["mes_12"].'/'.$arCabecalhoMes["ano_12"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_11"].'/'.$arCabecalhoMes["ano_11"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_10"].'/'.$arCabecalhoMes["ano_10"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_9"] .'/'.$arCabecalhoMes["ano_9"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_8"] .'/'.$arCabecalhoMes["ano_8"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_7"] .'/'.$arCabecalhoMes["ano_7"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_6"] .'/'.$arCabecalhoMes["ano_6"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_5"] .'/'.$arCabecalhoMes["ano_5"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_4"] .'/'.$arCabecalhoMes["ano_4"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_3"] .'/'.$arCabecalhoMes["ano_3"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_2"] .'/'.$arCabecalhoMes["ano_2"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_1"] .'/'.$arCabecalhoMes["ano_1"]; ?></th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arReceitas as $arReceita) { ?>
        <tr class="border_left border_right">
            <td class="text_align_center border_right" ><?php echo $arReceita["cod_estrutural"];?></td>
            <td class="text_align_justify border_right" ><?php echo $arReceita["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceita["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arReceita["total"];?></td>
        </tr>
        <?php } ?>
        <?php foreach($arReceitasTotal AS $arReceitaTotal) { ?>
        <tr class="border">
            <td class="text_align_center background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["cod_estrutural"];?></td>
            <td class="text_align_left border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["nom_conta"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_1"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_2"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_3"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_4"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_5"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_6"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_7"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_8"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_9"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_10"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_11"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["mes_12"];?></td>
            <td class="text_align_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arReceitaTotal["total"];?></td>
        </tr>
        <?php } ?>
        <?php foreach($arDemostrativoReceitaExclusao AS $arReceitaExclusaoTotal) { ?>
        <tr class="border">
            <td class="text_align_center border_right" ><?php echo $arReceitaExclusaoTotal["cod_estrutural"];?></td>
            <td class="text_align_left border_right" ><?php echo $arReceitaExclusaoTotal["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arReceitaExclusaoTotal["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arReceitaExclusaoTotal["total"];?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br />
<br />

<!-- Criando a tabela de Despesas -->
<table class="font_size_8">
    <thead>
        <tr class="border_top border_left border_right">
            <th colspan="15">DESPESAS</th>
        </tr>
        <tr class="border_left border_right border_bottom">
            <th style="width: 11mm;">CODIGO</th>
            <th style="width: 68mm;" class="text_align_justify" >DESCRIÇÃO</th>
            <th><?php echo $arCabecalhoMes["mes_12"].'/'.$arCabecalhoMes["ano_12"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_11"].'/'.$arCabecalhoMes["ano_11"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_10"].'/'.$arCabecalhoMes["ano_10"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_9"] .'/'.$arCabecalhoMes["ano_9"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_8"] .'/'.$arCabecalhoMes["ano_8"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_7"] .'/'.$arCabecalhoMes["ano_7"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_6"] .'/'.$arCabecalhoMes["ano_6"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_5"] .'/'.$arCabecalhoMes["ano_5"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_4"] .'/'.$arCabecalhoMes["ano_4"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_3"] .'/'.$arCabecalhoMes["ano_3"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_2"] .'/'.$arCabecalhoMes["ano_2"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_1"] .'/'.$arCabecalhoMes["ano_1"]; ?></th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arDespesas as $arDespesa) { ?>
        <tr class="border_left border_right">
            <td class="text_align_center border_right" ><?php echo $arDespesa["cod_estrutural"];?></td>
            <td class="text_align_justify border_right" ><?php echo $arDespesa["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesa["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arDespesa["total"];?></td>
        </tr>
        <?php } ?>
        <?php foreach($arDespesasTotal AS $arDespesaTotal) { ?>
        <tr class="border">
            <td class="text_align_center background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["cod_estrutural"];?></td>
            <td class="text_align_left border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["nom_conta"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_1"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_2"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_3"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_4"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_5"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_6"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_7"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_8"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_9"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_10"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_11"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["mes_12"];?></td>
            <td class="text_align_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arDespesaTotal["total"];?></td>
        </tr>
        <?php } ?>
        <?php foreach($arDespesasDeducoes as $arDespesaDeducao) { ?>
        <tr class="border_left border_right">
            <td class="text_align_center border_right" ><?php echo $arDespesaDeducao["cod_estrutural"];?></td>
            <td class="text_align_justify border_right" ><?php echo $arDespesaDeducao["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducao["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arDespesaDeducao["total"];?></td>
        </tr>
        <?php } ?>
    </tbody>
    <tfoot>
        <?php foreach($arDespesasDeducoesTotal AS $arDespesaDeducaoTotal) { ?>
        <tr class="border">
            <td class="text_align_center" ><?php echo $arDespesaDeducaoTotal["cod_estrutural"];?></td>
            <td class="text_align_left border_right" ><?php echo $arDespesaDeducaoTotal["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaDeducaoTotal["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arDespesaDeducaoTotal["total"];?></td>
        </tr>
        <?php } ?>
    </tfoot>
    <tfoot>
        <?php foreach($arValorTotalDespesaPessoal AS $arrValorTotalDespesaPessoal) { ?>
        <tr class="border">
            <td class="text_align_center" ><?php echo $arrValorTotalDespesaPessoal["cod_estrutural"];?></td>
            <td class="text_align_left border_right" ><?php echo $arrValorTotalDespesaPessoal["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arrValorTotalDespesaPessoal["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arrValorTotalDespesaPessoal["total"];?></td>
        </tr>
        <?php } ?>
    </tfoot>
</table>
<br />
<br />
<table class="font_size_8">
    <thead>
        <tr class="border">
            <th style="width: 11mm;"></th>
            <th style="width: 68mm;" class="text_align_justify" ></th>
            <th><?php echo $arCabecalhoMes["mes_12"].'/'.$arCabecalhoMes["ano_12"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_11"].'/'.$arCabecalhoMes["ano_11"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_10"].'/'.$arCabecalhoMes["ano_10"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_9"] .'/'.$arCabecalhoMes["ano_9"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_8"] .'/'.$arCabecalhoMes["ano_8"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_7"] .'/'.$arCabecalhoMes["ano_7"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_6"] .'/'.$arCabecalhoMes["ano_6"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_5"] .'/'.$arCabecalhoMes["ano_5"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_4"] .'/'.$arCabecalhoMes["ano_4"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_3"] .'/'.$arCabecalhoMes["ano_3"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_2"] .'/'.$arCabecalhoMes["ano_2"]; ?></th>
            <th><?php echo $arCabecalhoMes["mes_1"] .'/'.$arCabecalhoMes["ano_1"]; ?></th>
            <th>TOTAL</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arValoresDemostrativoRCL AS $arValorDemostrativoRCL) { ?>
        <tr class="border">
            <td class="text_align_center background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["cod_estrutural"];?></td>
            <td class="text_align_left border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["nom_conta"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_1"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_2"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_3"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_4"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_5"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_6"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_7"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_8"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_9"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_10"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_11"];?></td>
            <td class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["mes_12"];?></td>
            <td class="text_align_right background_color_cinza_c0c0c0 font_weight_bold" ><?php echo $arValorDemostrativoRCL["total"];?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>


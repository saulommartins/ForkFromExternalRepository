<div style="text-align: right"> Valores em Reais </div>
<!-- Criando a tabela de Despesas -->
<table class="font_size_8">
    <thead>
        <tr class="border_top border_left border_right">
            <th colspan="14">DESPESA TOTAL COM PESSOAL</th>
        </tr>
        <tr class="border_left border_right border_bottom">
            <th style="width: 68mm;" class="text_align_justify" >MESES DO EXERCÍCIO MÓVEL</th>
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
        
        <tr class="border">
            <td class="text_align_left border_right background_color_cinza_c0c0c0 font_weight_bold" > (-) EXCLUSÕES </td>
            <td colspan="13" class="text_align_right border_right background_color_cinza_c0c0c0 font_weight_bold"></td>
        </tr>
        
        <?php foreach($arDespesasExclusoes as $arDespesaExlcusao) { ?>
        <tr class="border_left border_right">
            <td class="text_align_justify border_right" ><?php echo $arDespesaExlcusao["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusao["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arDespesaExlcusao["total"];?></td>
        </tr>
        <?php } ?>
    </tbody>    
    <tfoot>
        <?php foreach($arDespesasExclusoesTotal AS $arDespesaExlcusaoTotal) { ?>
        <tr class="border">
            <td class="text_align_left border_right" ><?php echo $arDespesaExlcusaoTotal["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_1"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_2"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_3"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_4"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_5"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_6"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_7"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_8"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_9"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_10"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_11"];?></td>
            <td class="text_align_right border_right" ><?php echo $arDespesaExlcusaoTotal["mes_12"];?></td>
            <td class="text_align_right" ><?php echo $arDespesaExlcusaoTotal["total"];?></td>
        </tr>
        <?php } ?>
    </tfoot>
     <?php  
      if($arExercicio == 2014) {  ?>
    <tfoot>
     
        <tr class="border">
            <td class="text_align_left border_right" ><?php echo $arDespesaPessoal2013["nom_conta"];?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_1"] != '') ? $arDespesaPessoal2013["mes_1"] : "0,00" ;?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_2"] != '') ? $arDespesaPessoal2013["mes_2"] : "0,00" ;?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_3"] != '') ? $arDespesaPessoal2013["mes_3"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_4"] != '') ? $arDespesaPessoal2013["mes_4"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_5"] != '') ? $arDespesaPessoal2013["mes_5"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_6"] != '') ? $arDespesaPessoal2013["mes_6"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_7"] != '') ? $arDespesaPessoal2013["mes_7"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_8"] != '') ? $arDespesaPessoal2013["mes_8"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_9"] != '') ? $arDespesaPessoal2013["mes_9"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_10"] != '') ? $arDespesaPessoal2013["mes_10"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_11"] != '') ? $arDespesaPessoal2013["mes_11"] : "0,00";?></td>
            <td class="text_align_right border_right" ><?php echo ($arDespesaPessoal2013["mes_12"] != '') ? $arDespesaPessoal2013["mes_12"] : "0,00"; ?></td>
            <td class="text_align_right" ><?php echo $arDespesaPessoal2013["total"];?></td>
        </tr>
  
    </tfoot>
    <?php } ?>
    <tfoot>
        <?php foreach($arValorTotalDespesaPessoal AS $arrValorTotalDespesaPessoal) { ?>
        <tr class="border">
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
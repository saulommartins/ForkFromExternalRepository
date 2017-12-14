<table class='border'>
    <thead>
        <tr>
            <th>CÁLCULO PARA CONTRIBUIÇÃO DO PASEP</th>
        </tr>
        <tr>
            <th><?php echo $data_inicial." a ".$data_final?></th>
        </tr>
    </thead>
</table>

<br />

<table>
    <thead>
        <tr class='border'>
            <th colspan=2>I - RECEITAS</th>
        </tr>
        <tr class='border'>
            <th colspan=2>Inc. III, do art. 2º, da Lei n.º 9.715/98</th>
        </tr>
        <tr>
            <th class='border text_align_left' width='80%'>1000.00.00.00 - Receitas Correntes</th>
            <th class='border text_align_center' width='20%'>Valor - R$</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($correntes->getElementos() as $i => $value){
        ?>
            <tr>
                <td class='border text_align_left' width='80%'><?php echo $value['cod_estrutural'].' - '.$value['descricao']?></td>
                <td class='border text_align_right' width='20%'><?php echo $value['arrecadado_periodo']?></td>
            </tr>
        <?php
        }
        ?>
        <tr class='background_color_cinza_c0c0c0'>
            <td class='border text_align_left font_weight_bold' width='80%'>Sub-Total I</td>
            <td class='border text_align_right font_weight_bold' width='20%'><?php echo number_format($total_correntes,2,',','.')?></td>
        </tr>
        <tr>
            <td class='border text_align_left' width='80%'>&nbsp;</td>
            <td class='border text_align_right' width='20%'>&nbsp;</td>
        </tr>
        <?php
        foreach ($capital->getElementos() as $i => $value){
            ?>
            <tr>
                <td class='border text_align_left' width='80%'><?php echo $value['cod_estrutural'].' - '.$value['descricao']?></td>
                <td class='border text_align_right' width='20%'><?php echo $value['arrecadado_periodo']?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr class='font_weight_bold'>
            <td class='border text_align_left' width='80%'>Sub-Total II</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($total_capital,2,',','.')?></td>
        </tr>
        <tr>
            <td class='border text_align_left' width='80%'>Total das Receitas (I)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format(($total_correntes+$total_capital),2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />

<table>
    <thead>
        <tr class='border'>
            <th colspan=2>II - DEDUÇÕES DA RECEITA</th>
        </tr>
        <tr class='border'>
            <th class='border text_align_left' width='80%'>Dedução e Base Legal</th>
            <th class='border text_align_center' width='20%'>Valor - R$</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($deducoes->getElementos() as $i => $value){
        ?>
            <tr>
                <td class='border text_align_left' width='80%'><?php echo $value['descricao']?></td>
                <td class='border text_align_right' width='20%'><?php echo $value['arrecadado_periodo']?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='border text_align_left' width='80%'>Total das Deduções da Receita (II)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($total_deducoes,2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />

<table>
    <thead>
        <tr>
            <th class='border text_align_center' width='80%'>III - TOTAL RECEITA LÍQUIDA (BASE DE CÁLCULO) (I-II)</th>
            <th class='border text_align_right' width='20%'><?php echo number_format($total_geral,2,',','.')?></th>
        </tr>
    </thead>
</table>

<br />

<table>
    <thead>
        <tr>
            <th class='border text_align_center' width='80%'>IV - RETENÇÕES DO PASEP NA FONTE</th>
            <th class='border text_align_center' width='20%'>Retenção PASEP</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($retencoes->getElementos() as $i => $value){
        ?>
            <tr>
                <td class='border text_align_left' width='80%'>Retenções do PASEP</td>
                <td class='border text_align_right' width='20%'><?php echo number_format($value['pago_per'],2,',','.');?></td>
            </tr>
        <?php
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='border text_align_center' width='80%'>TOTAL DOS VALORES RETIDOS (IV)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($total_retencoes,2,',','.');?></td>
        </tr>
    </tfoot>
</table>

<br />

<table>
    <thead>
        <tr>
            <th class='border text_align_center' width='80%'>RESUMO</th>
            <th class='border text_align_center' width='20%'>Valor - R$</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class='border text_align_left' width='80%'>a) Total da Receita Líquida (III)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($total_geral,2,',','.');?></td>
        </tr>
        <tr>
            <td class='border text_align_left' width='80%'>b) 1% sobre total das Receitas (a*1%)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($pasep,2,',','.');?></td>
        </tr>
        <tr>
            <td class='border text_align_left' width='80%'>c) PASEP retido na Fonte (IV)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format($total_retencoes,2,',','.');?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class='border text_align_left' width='80%'>RESULTADO DO CÁLCULO (b-c)</td>
            <td class='border text_align_right' width='20%'><?php echo number_format(abs($pasep - $total_retencoes),2,',','.');?></td>
        </tr>
    </tfoot>
</table>
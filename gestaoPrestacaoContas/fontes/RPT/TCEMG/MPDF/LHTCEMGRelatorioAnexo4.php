<div class='text_align_center font_weight_bold'>
    <p>ANEXO IV<br>Demonstrativo dos Gastos com Pessoal<br>Incluída a Remuneração dos Agentes Políticos<br>(Face ao Disposto pela Lei Complementar nº101, de 04/05/2000)<br></p>
</div>


<!-- DESPESA PREFEITURA -->
<table class='border'>
    <thead>
        <tr>
            <th class='text_align_left' colspan=2>I) DESPESA</th>
        </tr>
        <tr>
            <th class='tabulacao_nivel_1 text_align_left' colspan=2>I-1) DESPESA - PREFEITURA</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($despesa_pref->getElementos() as $i => $value):
    ?>
            <tr>
                <td class='tabulacao_nivel_2 font_size_8' width='80%'><?php echo $value['cod_estrutural'].' - '.$value['descricao']?></td>
                <td class='text_align_right font_size_8' width='20%'><?php echo ($value['cod_estrutural'] == '3.1.00.00.00' ? '' : number_format($value['valor_def'],2,',','.'))?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>SUB-TOTAL</td>
            <td class='text_align_right' width='20%'><?php $total_despesas = $total_despesas + $total_despesa_pref + $restosDesp1;
                                                           echo number_format(($total_despesa_pref + $restosDesp1),2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />
<!-- DESPESA CAMARA -->

<table class='border'>
    <thead>
        <tr>
            <th class='tabulacao_nivel_1 text_align_left' colspan=2>I-2) DESPESA - CÂMARA</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($despesa_cam->getElementos() as $i => $value):
    ?>
            <tr>
                <td class='tabulacao_nivel_2 font_size_8' width='80%'><?php echo $value['cod_estrutural'].' - '.$value['descricao']?></td>
                <td class='text_align_right font_size_8' width='20%'><?php echo ($value['cod_estrutural'] == '3.1.00.00.00' ? '' : number_format($value['valor_def'],2,',','.'))?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>SUB-TOTAL</td>
            <td class='text_align_right' width='20%'><?php $total_despesas = $total_despesas + $total_despesa_cam + $restosDesp2;
                                                           echo number_format(($total_despesa_cam + $restosDesp2),2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />
<!-- DESPESA ADMINISTRACAO -->

<table class='border'>
    <thead>
        <tr>
            <th class='tabulacao_nivel_1 text_align_left' colspan=2>I-3) DESPESA - ADMINISTRAÇÃO INDIRETA</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($despesa_adm->getElementos() as $i => $value):
    ?>
            <tr>
                <td class='tabulacao_nivel_2 text_align_left font_size_8' width='80%'><?php echo $value['cod_estrutural'].' - '.$value['descricao']?></td>
                <td class='text_align_right font_size_8' width='20%'><?php echo ($value['cod_estrutural'] == '3.1.00.00.00' ? '' : number_format($value['valor_def'],2,',','.'))?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>SUB-TOTAL</td>
            <td class='text_align_right' width='20%'><?php $total_despesas = $total_despesas + $total_despesa_adm + $restosDesp3;
                                                           echo number_format(($total_despesa_adm + $restosDesp3),2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />
<!-- DESPESA TOTAIS -->

<table class='border'>
    <thead>
        <tr>
            <th class='tabulacao_nivel_1 text_align_left' width='80%'>TOTAL DAS DESPESAS COM PESSOAL NO MUNICÍPIO</th>
            <th class='tabulacao_nivel_1 text_align_right' width='20%'><?php echo number_format($total_despesas,2,',','.')?></th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($despesa_ttl->getElementos() as $i => $value):
    ?>
            <tr>
                <td class='tabulacao_nivel_2 font_size_8' width='80%'><?php echo $value['descricao']?></td>
                <td class='text_align_right font_size_8' width='20%'><?php echo number_format($value['valor_def'],2,',','.')?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>TOTAL DAS DESPESAS COM PESSOAL</td>
            <td class='text_align_right' width='20%'><?php $total_despesas_final = $total_despesas - $total_despesa_ttl;
                                                           echo number_format(($total_despesas - $total_despesa_ttl),2,',','.')?></td>
        </tr>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>TOTAL DAS DESPESAS COM PESSOAL DO EXERCÍCIO ANTERIOR</td>
            <td class='text_align_right' width='20%'><?php echo number_format($total_despesa_pessoal != '' ? $total_despesa_pessoal : 0,2,',','.')?></td>
        </tr>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>TOTAL DAS DESPESAS COM PESSOAL = BASE DE CÁLCULO</td>
            <td class='text_align_right' width='20%'><?php echo number_format(($total_despesas_final + $total_despesa_pessoal),2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />
<!-- RECEITA -->

<table class='border'>
    <thead>
        <tr>
            <th class='text_align_left' colspan='2'>II) RECEITA</th>
        </tr>
    </thead>
    <tbody>
    <?php
        foreach($receitas->getElementos() as $i => $value):
    ?>
            <tr>
                <td class='tabulacao_nivel_2 font_size_8' width='80%'><?php echo $value['descricao']?></td>
                <td class='text_align_right font_size_8' width='20%'><?php echo number_format($value['valor'],2,',','.')?></td>
            </tr>
    <?php
        endforeach;
    ?>
    </tbody>
    <tfoot>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>RECEITAS CORRENTES LÍQUIDAS</td>
            <td class='text_align_right' width='20%'><?php echo number_format($total_receita,2,',','.')?></td>
        </tr>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>RECEITAS CORRENTES LÍQUIDAS NO EXERCÍCIO ANTERIOR</td>
            <td class='text_align_right' width='20%'><?php echo number_format($total_receita_liquida,2,',','.')?></td>
        </tr>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left' width='80%'>RECEITAS CORRENTES LÍQUIDA = BASE DE CÁLCULO</td>
            <td class='text_align_right' width='20%'><?php echo number_format($total_receita+$total_receita_liquida,2,',','.')?></td>
        </tr>
    </tfoot>
</table>

<br />
<!-- TABELA PERCENTUAIS -->
<table class='border'>
    <thead>
        <tr>
            <th class='text_align_left' colspan='3'>III) PERCENTUAIS MONETÁRIOS DE APLICAÇÃO</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left font_size_8' width='60%'>Aplicação no Exercício</td>
            <td class='text_align_right font_size_8' width='20%'><?php echo number_format((($total_despesas_final*100)/($total_receita+$total_receita_liquida)),2,',','.').'%'?></td>
            <td class='text_align_right font_size_8' width='20%'><?php echo number_format($total_despesas_final,2,',','.')?></td>
        </tr>
        <tr>
            <td class='tabulacao_nivel_2 text_align_left font_size_8' width='60%'>Permitido pela Lei Complementar 101/00</td>
            <td class='text_align_right font_size_8' width='20%'>60,00%</td>
            <td class='text_align_right font_size_8' width='20%'><?php echo number_format((($total_receita+$total_receita_liquida)*0.6),2,',','.')?></td>
        </tr>
        <?php
            if ($total_despesas_final > (($total_receita+$total_receita_liquida)*0.6) ? $excedente = ($total_despesas_final - (($total_receita+$total_receita_liquida)*0.6)) : 0.00);
        ?>
        <tr class='border'>
            <td class='tabulacao_nivel_2 text_align_left font_size_8' width='60%'>Excedente</td>
            <td class='text_align_right font_size_8' width='20%'><?php echo number_format((($percent_excedente*100)/(($total_receita+$total_receita_liquida)*0.6)),2,',','.').'%'?></td>
            <td class='text_align_right font_size_8' width='20%'><?php echo number_format($excedente,2,',','.') ?></td>
        </tr>
    </tbody>
</table>
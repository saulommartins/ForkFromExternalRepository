<!-- Criando a tabela de Contabilidade -->
<table class="font_size_8">
    <thead>
        <tr class="border_top border_left border_right">
            <th colspan="4">CONTABILIDADE</th>
        </tr>
        <tr class="border_left border_right border_bottom">
            <th>Ativo Financeiro</th>
            <th>Passivo Financeiro</th>
            <th>Superavit</th>
            <th>Déficit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arApuracaoContabilidade AS $arContabilidade) { ?>
        <tr class="border">
            <td width="25%" class="text_align_right border_right" ><?php echo number_format($arContabilidade['valor_ativo'], 2, ',', '.');?></td>
            <td width="25%" class="text_align_right border_right" ><?php echo number_format($arContabilidade['valor_passivo'], 2, ',', '.');?></td>
            <td width="25%" class="text_align_right border_right" ><?php echo number_format($arContabilidade['superavit'], 2, ',', '.');?></td>
            <td width="25%" class="text_align_right border_right" ><?php echo number_format($arContabilidade['deficit'], 2, ',', '.');?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br />
<br />
<!-- Criando a tabela de Execução -->
<table class="font_size_8">
    <thead>
        <tr class="border_top border_left border_right">
            <th colspan="5">EXECUÇÃO</th>
        </tr>
        <tr class="border_left border_right border_bottom">
            <th>Recurso</th>
            <th>Ativo Financeiro</th>
            <th>Passivo Financeiro</th>
            <th>Superavit</th>
            <th>Déficit</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($arApuracaoExecucao AS $arExecucao) { ?>
        <tr class="border">
            <td width="40%" class="text_align_left border_right" ><?php echo $arExecucao['cod_recurso']." - ".$arExecucao["nom_recurso"];?></td>
            <td width="15%" class="text_align_right border_right" ><?php echo number_format($arExecucao['valor_ativo'], 2, ',', '.');?></td>
            <td width="15%" class="text_align_right border_right" ><?php echo number_format($arExecucao['valor_passivo'], 2, ',', '.');?></td>
            <td width="15%" class="text_align_right border_right" ><?php echo number_format($arExecucao['superavit'], 2, ',', '.');?></td>
            <td width="15%" class="text_align_right border_right" ><?php echo number_format($arExecucao['deficit'], 2, ',', '.');?></td>
        </tr>
        <?php } ?>
        <tr class="border">
            <td width="40%" class="background_color_cinza_c0c0c0 text_align_right border_right" > TOTAL</td>
            <td width="15%" class="background_color_cinza_c0c0c0 text_align_right border_right" ><?php echo number_format($arApuracaoExecucaoTotal['valor_ativo'], 2, ',', '.');?></td>
            <td width="15%" class="background_color_cinza_c0c0c0 text_align_right border_right" ><?php echo number_format($arApuracaoExecucaoTotal['valor_passivo'], 2, ',', '.');?></td>
            <td width="15%" class="background_color_cinza_c0c0c0 text_align_right border_right" ><?php echo number_format($arApuracaoExecucaoTotal['superavit'], 2, ',', '.');?></td>
            <td width="15%" class="background_color_cinza_c0c0c0 text_align_right border_right" ><?php echo number_format($arApuracaoExecucaoTotal['deficit'], 2, ',', '.');?></td>
        </tr>
    </tbody>
</table>
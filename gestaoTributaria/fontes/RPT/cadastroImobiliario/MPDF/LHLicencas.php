<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Relatório de Licenças </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<div id="restos_pagar_entidades">
    <table class='border'>
        <thead>
            <tr>
                <td class="border text_align_center font_weight_bold" width="10%">Número da Licença</td>
                <td class="border text_align_center font_weight_bold" width="15%">Número da Inscrição Imobiliária</td>
                <td class="border text_align_center font_weight_bold" width="20%">Tipo de Licença</td>
                <td class="border text_align_center font_weight_bold" width="19%">Documento</td>
                <td class="border text_align_center font_weight_bold" width="12%">Data de Início</td>
                <td class="border text_align_center font_weight_bold" width="12%">Data Término</td>
                <td class="border text_align_center font_weight_bold" width="12%">Situação</td>
            </tr>           
        </thead>
        <tbody>
            <tr>
                    <?php
                    foreach ($arLicencas as $key => $licenca) {
                        echo "<tr>";
                        echo "<td class=\"border text_align_right\"  > ". $licenca['licenca']         ."</td>";
                        echo "<td class=\"border text_align_right\"> ".$licenca['inscricao']     ."</td>";
                        echo "<td class=\"border text_align_justify\"> ".$licenca['nom_tipo']     ."</td>";
                        echo "<td class=\"border text_align_justify\"> ".$licenca['nome_documento']     ."</td>";
                        echo "<td class=\"border text_align_center\" > ". $licenca['dt_inicio']  ."</td>";
                        echo "<td class=\"border text_align_center\" > ".$licenca['dt_termino'] ."</td>";
                        echo "<td class=\"border text_align_center\" > ".$licenca['situacao'] ." </td>";
                        echo "</tr>";
                    }
                ?>
             
            </tr>
        
        </tbody>
    </table>
</div>
<br/>



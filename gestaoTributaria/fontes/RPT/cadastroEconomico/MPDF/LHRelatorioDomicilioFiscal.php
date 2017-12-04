<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Relatório de Domicilio Fiscal</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<div id="domicilio_fiscal">
    <table class='border'>
        <thead>
            <tr>
                <td class="border text_align_center font_weight_bold" width="8%">Inscrição Econômica</td>
                <td class="border text_align_center font_weight_bold" width="42%">Razão Social da Empresa</td>
                <td class="border text_align_center font_weight_bold" width="42%">Logradouro</td>
                <td class="border text_align_center font_weight_bold" width="8%">Inscrição Imobiliária</td>
            </tr>           
        </thead>
        <tbody>
            <tr>
                    <?php
                    foreach ($arDomicilioFiscal as $key => $dados) {
                        echo "<tr>";
                        echo "<td class=\"border text_align_center\"  >". $dados['inscricao_economica'] ."</td>";
                        echo "<td class=\"border text_align_justify\" >". $dados['nom_cgm']             ."</td>";
                        echo "<td class=\"border text_align_justify\" >". $dados['nom_logradouro']      ."</td>";
                        echo "<td class=\"border text_align_center\"  >". $dados['inscricao_municipal'] ."</td>";
                        echo "</tr>";
                    }
                ?>
            </tr>
        </tbody>
    </table>
</div>
<br/>
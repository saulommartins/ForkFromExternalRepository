<div>
    <table>
        <thead>
            <tr>
                <?php
                    if ( $filtro['inSimulacao'] == 1){
                        echo "<td class='text_align_center font_weight_bold'>SIMULAÇÃO DO BORDERÔ</td>";
                    }else{
                        echo "<td class='text_align_center font_weight_bold'>DADOS DO BORDERÔ</td>";
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<br/>
<div id="dados_bordero">
        <table>
            <tbody>
                    <tr class="border background_color_cinza_c0c0c0" >
                        <td class="text_align_left" colspan=2 > Dados do Borderô </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Número do Borderô </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['numero_bordero'] ?> </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Data do Borderô </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['data_bordero'] ?> </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Entidade </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['entidade'] ?> </td>
                    </tr>


                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Tipo de Borderô </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['tipo_bordero'] ?> </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Conta Pagadora </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['conta_pagadora'] ?> </td>
                    </tr>

                    <tr class="border background_color_cinza_c0c0c0" >
                        <td class="text_align_left" colspan=2 > Dados do Boletim </td>                        
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Número do Boletim </td>
                        <td class="border text_align_left" > <?= $dados_boletim[0]['numero_boletim'] ?> </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Data do Boletim </td>
                        <td class="border text_align_left" > <?= $dados_bordero[0]['data_bordero'] ?> </td>
                    </tr>

                    <tr class="border background_color_cinza_c0c0c0" >
                        <td class="text_align_left" colspan=2 > <?= $dados_banco_titulo[0]['dados_banco'] ?></td>                        
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Agência </td>
                        <td class="border text_align_left" > <?= $dados_banco[0]['agencia'] ?> </td>
                    </tr>

                    <tr class="border" >
                        <td class="border text_align_right" width=20% > Conta-Corrente </td>
                        <td class="border text_align_left" > <?= $dados_banco[0]['conta_corrente'] ?> </td>
                    </tr>
            </tbody>
        </table>
        <br/>
        <table>
            <tbody>
                <tr class="border" >
                    <td class="border text_align_center " colspan="2" > <?= $autorizacao ?> </td>
                </tr>
            </tbody>
        </table>
        <br/>
        <table>
            <tbody>
            <?php
                foreach ($dados_pagamento as $dados_credor) {
                
                    echo "<tr class=\"border\" >";
                        echo "<td class=\"border text_align_left\" width=5%  > Credor: </td>";
                        echo "<td class=\"border text_align_left\" width=40% colspan=3 > " .$dados_credor['credor']. " </td>";
                        
                        echo "<td class=\"border text_align_left\" width=10%  > CPF/CNPJ: </td>";
                        echo "<td class=\"border text_align_left\" width=20% colspan=3 > " .$dados_credor['cpf_cnpj']. " </td>";

                        echo "<td class=\"border text_align_left\" width=5% > Banco/Agência/CC: </td>";
                        echo "<td class=\"border text_align_left\" width=20% > " .$dados_credor['banco_agencia_cc']. " </td>";
                    echo "</tr>";

                    foreach ($dados_credor['dados_op'] as $dados_op) {
                        echo "<tr class=\"border\" >";
                            echo "<td class=\"border text_align_left\" width=5%  > OP: </td>";
                            echo "<td class=\"border text_align_left\" width=10% > " .$dados_op['op']. " </td>";
                        
                            echo "<td class=\"border text_align_left\" width=10%  > Empenho: </td>";
                            echo "<td class=\"border text_align_left\" width=15% > " .$dados_op['empenho']. " </td>";

                            echo "<td class=\"border text_align_left\" width=10% > Valor Bruto: </td>";
                            echo "<td class=\"border text_align_left\" width=10% > " .number_format($dados_op['valor_bruto'],2,",","."). " </td>";
                        
                            echo "<td class=\"border text_align_left\" width=10% > Retenções: </td>";
                            echo "<td class=\"border text_align_left\" width=10% > " .number_format($dados_op['valor_retencao'],2,",","."). " </td>";

                            echo "<td class=\"border text_align_left\" width=10% > Valor Liquido: </td>";
                            echo "<td class=\"border text_align_left\" width=10% > R$ " .number_format($dados_op['valor_liquido'],2,",","."). " </td>";
                        echo "</tr>";
                        echo "<tr >";
                            echo "<td colspan=1></td>";
                            echo "<td class=\"border text_align_left\" colspan=1 > Observações: </td>";
                            echo "<td class=\"border text_align_left\" colspan=9 > " .$dados_op['observacao']. " </td>";
                        echo "</tr>";    
                    }

                echo "<tr>";
                    echo "<td>&nbsp;</td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td class=\"border text_align_right\" colspan=9 > Valor Total do Credor a ser Creditado em sua C/C: </td>";
                    echo "<td class=\"border text_align_left\" > R$ " .number_format($dados_credor['total_credor'],2,",","."). " </td>";
                echo "</tr>";

                echo "<tr>";
                    echo "<td>&nbsp;</td>";
                echo "</tr>";
            }
            
            echo "<tr>";
                echo "<td class=\"border text_align_right\" colspan=9 > Valor Total do Borderô: </td>";
                echo "<td class=\"border text_align_left\" > R$ " .number_format($total_bordero,2,",","."). " </td>";
            echo "</tr>";
            ?>
            </tbody>
        </table>
</div>
<br/>
<div id="data_extenso">
        <table>
            <tbody>
                <tr>
                    <td style="text_align_right"> Autorizo. </td>
                    <td style="padding-left: 120mm;"> <?= $data_extenso ?> </td>
                </tr>
            </tbody>
        </table>
</div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
<div id="assinaturas">
        <table >
            <tbody>
                <?php
                    foreach ($dados_assinatura as $chave_assinatura => $assinaturas) {
                        
                        echo "<tr >";
                            echo "<td class=\"text_align_center\"> ".$assinaturas['Assinante_1']." </td>";
                            echo "<td class=\"text_align_center\"> ".$assinaturas['Assinante_2']." </td>";
                            echo "<td class=\"text_align_center\"> ".$assinaturas['Assinante_3']." </td>";
                        echo "</tr>";

                    }
                ?>
            </tbody>
        </table>
</div>
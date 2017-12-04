<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Relatório de Lançamentos Automáticos </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<div id="restos_pagar_entidades">
    <table >
        <thead>Código, Usuário, Inscrição, Numeração, Parcela, Vencimento e Valor
            <tr> 
                <td class=" text_align_center font_weight_bold" width="10%">Código</td>
                <td class=" text_align_center font_weight_bold" width="35%">Usuário</td>
                <td class=" text_align_center font_weight_bold" width="10%">Inscrição</td>
                <td class=" text_align_center font_weight_bold" width="15%">Numeração</td>
                <td class=" text_align_center font_weight_bold" width="10%">Parcela</td>
                <td class=" text_align_center font_weight_bold" width="10%">Vencimento</td>
                <td class=" text_align_center font_weight_bold" width="10%">Valor</td>
            </tr>           
        </thead>
        <tbody>
            <tr>
                    <?php

                    foreach ($arLancamentos as $key => $lancamento) {
                        
                        echo "<tr>";
                   
                        if($codLancamento != $lancamento['cod_lancamento'] && $numcgm != $lancamento['numcgm'] && $inscricao !=  $lancamento['inscricao']){
                             echo "<td class=\" text_align_left\"  > ". $lancamento['cod_lancamento']         ."</td>";
                            echo "<td class=\" text_align_left\"> ".$lancamento['numcgm']." - ".$lancamento['nom_cgm'] ."</td>";
                            echo "<td class=\" text_align_center\"> ".$lancamento['inscricao']     ."</td>";                           
                        } else {
                            echo "<td class=\" text_align_right\"  ></td>";
                            echo "<td class=\" text_align_right\"> </td>";
                            echo "<td class=\" text_align_justify\"></td>";
                        }
                        echo "<td class=\" text_align_justify\"> ".$lancamento['numeracao']     ."</td>";
                        echo "<td class=\" text_align_center\" > ". $lancamento['nr_parcela']  ."</td>";
                        echo "<td class=\" text_align_center\" > ".$lancamento['vencimento'] ."</td>";
                        echo "<td class=\" text_align_right \" > R$ ".number_format($lancamento['valor'],2, ",", ".")." </td>";
                       echo "</tr>";                      
         
                 
                       
                     $codLancamento= $lancamento['cod_lancamento'];
                     $numcgm= $lancamento['numcgm'];
                     $inscricao= $lancamento['inscricao'];
                    } 
                ?>
             
            </tr>
        
        </tbody>
    </table>
</div>
<br/>



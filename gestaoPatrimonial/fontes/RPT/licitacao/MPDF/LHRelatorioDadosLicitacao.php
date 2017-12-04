<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Relatório de Itens da Licitação </th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<br/>
<div id="dados_principal">
    <table >
        <thead>
             <?php 
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Licitação', 36, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['inCodLicitacao']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Processo Administrativo', 25, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['processo']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Entidade', 33, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['entidade']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\"  width=\"20%\" > ". str_pad('Mapa de Compras', 26, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['mapa']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Data da Licitação', 31, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['dt_licitacao']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Modalidade', 31, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['modalidade']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Tipo Objeto', 32, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['tipo_objeto']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Objeto', 34, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['objeto']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"20%\" > ". str_pad('Data Homologação', 28, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arDadosLicitacao['dt_homologacao']     ."</td>";
             echo  "</tr>";
               
             ?>
           
        </thead>
        <tbody>
           
        </tbody>
    </table>
    <br/>
</div>
<div id="dados_itens">
    <table>
        <thead>
          
        </thead>
        <tbody>
            <tr>
                    <?php
                    $stChaveFornecedor='';              
                    $vlTotalAutorizacao= 0;
                    $vlTotalLicitacao = 0;
                    foreach ($arDadosLicitacao['arLicitacao'] as $key => $autorizacao) {
                        $vlTotalAutorizacao=0;     
                        $vlTotalLicitacao += $autorizacao['vl_total'];
                        if($stChaveFornecedor != $autorizacao['autorizacao'].$autorizacao['fornecedor'] ) {
                            $stChaveFornecedor= $autorizacao['autorizacao'].$autorizacao['fornecedor'];
                       
                            echo "<tr>";
                                echo "<td colspan=\"6\" >&nbsp;</td>";
                            echo "</tr>";
                            echo "<tr>";
                                echo "<td class=\"font_weight_bold\"  > Autorização </td>";
                                echo "<td class=\"font_weight_bold\" colspan=\"5\"   > Fornecedor </td>";
                            echo "</tr>";
                            echo "<tr>";
                                echo "<td > ".$autorizacao['autorizacao']     ." </td>";
                                echo "<td colspan=\"5\" >".$autorizacao['fornecedor'] ."</td>";
                            echo "</tr>";                
                            echo "<tr>";
                                echo "<td class=\"font_weight_bold\" width=\"5%\" >Seq.</td>";
                                echo "<td class=\"font_weight_bold\" width=\"5%\" >Item</td>";  
                                echo "<td class=\"font_weight_bold\" width=\"60%\" >Descrição</td>";       
                                echo "<td class=\"font_weight_bold text_align_right\" width=\"10%\" >Quantidade</td>";  
                                echo "<td class=\"font_weight_bold text_align_right\" width=\"10%\" >Valor Unitário</td>";       
                                echo "<td class=\"font_weight_bold text_align_right\" width=\"10%\" >Valor Total</td>";   
                            echo "</tr>";    
                            foreach ($arDadosLicitacao['arLicitacao'] as $key => $itens){

                                if($autorizacao['autorizacao'].$autorizacao['fornecedor'] == $itens['autorizacao'].$itens['fornecedor']){
                                    $vlTotalAutorizacao+= $itens['vl_total'];     
                                     echo "<tr>";
                                        echo "<td > ".$itens['num_item']."</td>";
                                        echo "<td > ".$itens['cod_item']."</td>";  
                                        echo "<td > ".$itens['descricao']."</td>";       
                                        echo "<td class=\"text_align_right\">  ".number_format( $itens['quantidade'] , 4, ',', '.')."</td>";  
                                        echo "<td class=\"text_align_right\" > ".number_format( $itens['vl_unitario'] , 2, ',', '.')."</td>";       
                                        echo "<td class=\"text_align_right\" > ".number_format( $itens['vl_total'] , 2, ',', '.')."</td>";       
                                    echo "</tr>";        
                                }
                            }
                            echo "<tr>";
                                echo "<td class=\"font_weight_bold\" colspan=\"4\"  > Total da Autorização </td>";
                                echo "<td class=\"font_weight_bold text_align_right\"   colspan=\"2\" >".number_format( $vlTotalAutorizacao , 2, ',', '.')."</td>";
                            echo "</tr>";                            
                       }                  
                 }  
                 echo "<tr>";
                    echo "<td class=\"font_weight_bold\" colspan=\"4\"  > Total da Licitação </td>";
                    echo "<td class=\"font_weight_bold text_align_right\"   colspan=\"2\" >".number_format( $vlTotalLicitacao , 2, ',', '.')."</td>";
                 echo "</tr>";         
                ?>
             
            </tr>
        
        </tbody>
    </table>
</div>
<br/>



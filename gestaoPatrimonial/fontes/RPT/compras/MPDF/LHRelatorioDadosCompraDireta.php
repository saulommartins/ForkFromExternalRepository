<div>
    <table class='border'>
        <thead>
            <tr class='border font_weight_bold text_align_center'>
                <th colspan=3>Relatório de Itens da Compra Direta </th>
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
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Compra Direta', 35, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['compraDireta']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Entidade', 38, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['entidade']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Mapa de Compras', 32, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['mapa']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\"  width=\"23%\" > ". str_pad('Data da Compra Direta', 31, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['dt_compra_direta']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Modalidade', 37, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['modalidade']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Tipo Objeto', 38, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['tipo_objeto']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Objeto', 40, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['objeto']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Data de Entrega da Proposta', 28, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['dt_entrega_proposta']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Validade da Proposta', 32, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['dt_validade_proposta']     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Condições de Pagamento', 30, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['condicoes_pagamento']     ."</td>";
             echo  "</tr>";               
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Prazo de Entrega', 34, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['prazo_entrega'].' dia(s)'     ."</td>";
             echo  "</tr>";
             echo  "<tr class=\"text_align_left\">";
                echo "<td  class=\"font_weight_bold\" width=\"23%\" > ". str_pad('Data Homologação',34, ".", STR_PAD_RIGHT).':'."</td>";
                echo "<td colspan=\"5\"> ".$arCompra['dt_homologacao']     ."</td>";
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
                    foreach ($arCompra['arDadosCompra'] as $key => $autorizacao) {
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
                            foreach ($arCompra['arDadosCompra'] as $key => $itens){

                                if($autorizacao['autorizacao'].$autorizacao['fornecedor'] == $itens['autorizacao'].$itens['fornecedor']){
                                    $vlTotalAutorizacao+= $itens['vl_total'];     
                                     echo "<tr>";
                                        echo "<td > ".$itens['num_item']."</td>";
                                        echo "<td > ".$itens['cod_item']."</td>";  
                                        echo "<td > ".$itens['descricao']."</td>";       
                                        echo "<td class=\"text_align_right\">  ".number_format( $itens['quantidade']  , 4, ',', '.')."</td>";  
                                        echo "<td class=\"text_align_right\" > ".number_format( $itens['vl_unitario'] , 2, ',', '.')."</td>";       
                                        echo "<td class=\"text_align_right\" > ".number_format( $itens['vl_total']    , 2, ',', '.')."</td>";       
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
                    echo "<td class=\"font_weight_bold\" colspan=\"4\"  > Total da Compra Direta </td>";
                    echo "<td class=\"font_weight_bold text_align_right\"   colspan=\"2\" >".number_format( $vlTotalLicitacao , 2, ',', '.')."</td>";
                 echo "</tr>";         
                ?>
             
            </tr>
        
        </tbody>
    </table>
</div>
<br/>



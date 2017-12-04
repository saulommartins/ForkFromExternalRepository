
<?php
    if ($sem_registro != ''):
    ?>
        <div class='text_align_center font_weight_bold'>
            <p><?php echo $sem_registro ?></p>
        </div>
    <?php
    else:
    ?>
        <table>
            
            <thead>
                <tr >
                    <td class="border text_align_center font_weight_bold" colspan="6"> Anexo IIIA <br>
                        Recursos do FUNDEB do Exercício Anterior Aplicados no Exercício Atual
                    </td>
                </tr> 
                <tr>
                    <?php 
                        echo "<td class='border' colspan='6'>"."Exercício: ".Sessao::getExercicio()."</td>";
                    ?>
                </tr>    
                <tr >
                    <td style="height:40px;" colspan="6" ></td>           
                </tr>
                <tr>
                    <th class="border text_align_center">Função</th>
                    <th class="border text_align_center">Subfunção</th>
                    <th class="border">Programa</th>
                    <th class="border">Especificação</th>
                    <th class="border">Despesas</th>
                </tr>
            </thead>
            
            <?php
            
            foreach( $arReceitas AS $dados) {  
                echo "<tr>";
                echo "<td class='border_right border_left text_align_center'>".$dados['cod_funcao']."</td>";
                echo "<td class='border_right text_align_center'>".$dados['cod_subfuncao']."</td>";
                echo "<td class='border_right text_align_center'>".$dados['cod_programa']."</td>";
                echo "<td class='border_right text_align_left'>".$dados['descricao']."</td>";
                echo "<td class='border text_align_right '>".$dados['valor_pagamento']."</td>";        
                $dados['nivel'] == 1 ? $valorTotal = $dados['valor_pagamento'] : "";
            }    
            echo "<tr>";
            echo "<td class='border font_weight_bold' colspan='4'> TOTAL</td>";
            echo "<td class='border font_weight_bold text_align_right'>".'R$'.$valorTotal."</td>";
            ?>
        </table>
    <?php
    endif;
?>
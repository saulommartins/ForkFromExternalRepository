<div><table class='border'><thead><tr class='border font_weight_bold text_align_center'><th colspan=3>Relatório de Inscrições da Dívida Ativa</th></tr></thead><tbody></tbody></table>
</div>
<br/>
<div id="domicilio_fiscal">
<table class='border'>
<thead>
<tr><td class="border text_align_center font_weight_bold" width="10%">Inscrição Origem</td><td class="border text_align_center font_weight_bold" width="10%">Exercício</td>
<td class="border text_align_center font_weight_bold" width="42%">Imposto</td>
<td class="border text_align_center font_weight_bold" width="8%">Livro</td>
<td class="border text_align_center font_weight_bold" width="8%">Folha</td>
<td class="border text_align_center font_weight_bold" width="10%">I.D.A.</td>
<td class="border text_align_center font_weight_bold" width="10%">Valor Origem</td></tr>           
</thead>
<tbody>
<tr><?php 
 foreach ($arDados as $key => $dados) {echo "<tr>";echo "<td class=\"border text_align_center\"  >". $dados['inscricao_origem'] ."</td>";
echo "<td class=\"border text_align_center\"  >". $dados['exercicio']        ."</td>";echo "<td class=\"border text_align_justify\" >". $dados['imposto']          ."</td>";
echo "<td class=\"border text_align_center\"  >". $dados['livro']            ."</td>";echo "<td class=\"border text_align_center\"  >". $dados['folha']            ."</td>";
echo "<td class=\"border text_align_center\"  >". $dados['ida']              ."</td>";
echo "<td class=\"border text_align_right\"   >". number_format($dados['valor_origem'],2,',','.')     ."</td>";echo "</tr>";} 
?></tr>
</tbody></table></div><br/>
<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

function VerificaFeriado($fdia,$fmes,$fano,$aferiados)
{
   $sOK="";
   $i = 0;
   $qtd = count($aferiados);
   while ($i<=$qtd) {
      if ($aferiados[$i]["ano"]=="" or $aferiados[$i]["ano"]==$fano) {
         if ($aferiados[$i]["mes"]==$fmes) {
            if ($aferiados[$i]["dia"]==$fdia) {
               $sOK=$aferiados[$i]["des"];
               $i=$qtd;
            }
         }
      }
      $i++;
   }

   return($sOK);
}

function str_repete($str, $num)
{
  $buf = "";
  for ($i=0; $i<$num; $i++) {
    $buf .= $str;
  }

  return($buf);
}

function Calendario($mes,$ano)
{
   $mesant = $mes - 1;
   $anoant = $ano;
   $proxmes = $mes + 1;
   $proxano = $ano;
   if ($mes==1) {
      $mesant=12;
      $anoant = $ano - 1;
   } else {
      if ($mes==12) {
         $proxmes=1;
         $proxano = $ano + 1;
      }
   }

   $arquivo = "feriadosLegado.csv";
   $iConta=0;
   $fp = fopen ($arquivo,"r");
   while ($dado = fgetcsv ($fp, 300, ";")) {
      $feriados[$iConta]["ano"] = $dado[0];
      $feriados[$iConta]["mes"] = $dado[1];
      $feriados[$iConta]["dia"] = $dado[2];
      $feriados[$iConta]["des"] = $dado[3];
      $iConta++;
   }
   fclose ($fp);
   $hojedia = (int) date("d",time());
   $hojemes = (int) date("m",time());
   $hojeano = (int) date("Y",time());

   $ameses = array ("Janeiro", "Fevereiro", "Mar&ccedil;o", "Abril", "Maio", "Junho",
                    "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

   echo "<!-- Inicio do Calendario -->
   <table border=\"0\" cellpadding=\"0\" cellspacing=\"1\" width=\"100%\">
   <tr>
      <td class=\"labelcenter\">
         <a href=\"javascript:MudaCalendario(".$mes.",".($ano-1).");\">
            <img src=\"../temas/padrao/imagens/seta2esq.gif\" width=\"10\" height=\"10\" alt=\"\" border=\"0\">
         </a>
      </td>
      <td class=\"labelcenter\" colspan=\"5\">".$ano."</td>
      <td class=\"labelcenter\">
         <a href=\"javascript:MudaCalendario(".$mes.",".($ano+1).");\">
            <img src = \"../temas/padrao/imagens/seta2dir.gif\" width=\"10\" height=\"10\" alt=\"\" border=\"0\">
         </a>
      </td>
   </tr>
   <tr>
      <td class=\"labelcenter\">
         <a href=\"javascript:MudaCalendario(".$mesant.",".$anoant.");\">
            <img src=\"../temas/padrao/imagens/seta2esq.gif\" width=\"10\" height=\"10\" alt=\"\" border=\"0\">
         </a>
      </td>
      <td class=\"labelcenter\" colspan=\"5\">";
         $mesaux = (int) $mes -1;
         echo $ameses[$mesaux]."
      </td>
      <td class=\"labelcenter\">
         <a href=\"javascript:MudaCalendario(".$proxmes.",".$proxano.");\">
            <img src=\"../temas/padrao/imagens/seta2dir.gif\" width=\"10\" height=\"10\" alt=\"\" border=\"0\">
         </a>
      </td>
   </tr>
   <tr>";
   $adia_semana = array ("D","S","T","Q","Q","S","S");
   $qtd = count($adia_semana);
   for ($iconta=0; $iconta<$qtd; $iconta++) {
      echo "\n   <td class=\"labelcenter\">$adia_semana[$iconta]</td>";
   }
   echo "</tr> <br/>";

   $proxmes = $mes+1;
   $ultdia = mktime(0,0,0,$proxmes,0,$ano);
   $ultdia = date(d,$ultdia);

   $pridia = mktime(0,0,0,$mes,1,$ano);
   $dia_semana = date(l,$pridia);
   if ($dia_semana!='Sunday') {
      echo "<tr>\n";
   }
   if ($dia_semana=='Monday') { echo str_repete("<td></td>",1); } elseif ($dia_semana=='Tuesday') { echo str_repete("<td></td>",2); } elseif ($dia_semana=='Wednesday') { echo str_repete("<td></td>",3); } elseif ($dia_semana=='Thursday') { echo str_repete("<td></td>",4); } elseif ($dia_semana=='Friday') { echo str_repete("<td></td>",5); } elseif ($dia_semana=='Saturday') { echo str_repete("<td></td>",6); }

   $conta=1;
   $contafer=0;
   while ($conta<=$ultdia) {
         $dia = mktime(0,0,0,$mes,$conta,$ano);
         $dia_semana = date(l,$dia);
         $bHoje = ($conta==$hojedia and $mes==$hojemes and $ano==$hojeano);
         if ($dia_semana == 'Sunday') {
            echo "<tr>\n";
            $sclasse = "class=\"fieldright\"";
         } else {
            $sclasse = "class=\"fieldright\"";
         }

         $feriado=VerificaFeriado($conta,$mes,$ano,$feriados);
         if ($feriado!="") {
            $contafer++;
            $textofer[$contafer]= sprintf("%02d",$conta) ." - ". $feriado;
            $sclasse = "class=\"label\"";
         }
         if ($bHoje) {
            $sclasse = "class=\"hoje\"";
         }
         $sData = sprintf("%02d/%02d/%04d",$conta,$mes,$ano);
         echo "   <td $sclasse align=\"right\" width=\"20px\">
                     <a title=\"$feriado\" href=\"javascript:EncheCampo('".$sData."');\">".$conta."</a>
                 </td>\n";
         if ($dia_semana == 'Saturday') { echo "</tr>\n"; }
         $conta++;
   }
   echo "
      </tr>
   </table>
   <!-- Fim do Calendario -->\n\n";
}

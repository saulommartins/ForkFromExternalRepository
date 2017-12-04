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
* Arquivo de instância para Tratamento
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 3219 $
$Name$
$Author: lizandro $
$Date: 2005-12-01 14:25:34 -0200 (Qui, 01 Dez 2005) $

* Casos de uso: uc-01.07.98
*/

function geraResumoExame($vetor, $consulta = false)
{
    $js = "";

    $html  = "<table width='100%' cellspacing=0 border=0 cellpadding=0>";
    $html .= "  <tr>";
    $html .= "      <td class='labelleft' width='5%'>&nbsp;</td>";
    $html .= "      <td class='labelcenter' width='30%'>Instituição</td>";
    $html .= "      <td class='labelcenter' width='10%'>Data</td>";
    $html .= "      <td class='labelcenter' width='30%'>Exame</td>";
    if (!$consulta) {
        $html .= "      <td class='labelcenter'>Ação</td>";
    }
    $html .= "  </tr>";
    $html .= "  <tr>";
    $html .= "      <td class='labelcenter' colspan='5'>Descrição</td>";
    $html .= "  </tr>";

    $vet = "";
    $count = 1;

    foreach ($vetor as $chave=>$vet) {
        //Retira as quebras de linha e substitui pela tag <br>
        $desc = preg_replace( "/\r?\n/","<br>",$vet[descExame]);
        $inst = pegaDado("nom_instituicao","cse.instituicao_saude","Where cod_instituicao = '".$vet[codInstExame]."' ");
        $tipo = pegaDado("nom_exame","cse.tipo_exame","Where cod_exame = '".$vet[codExame]."' ");

        $html .= "  <tr>";
        $html .= "      <td class='label' rowspan='2'>".$count."</td>";
        $html .= "      <td class='show_dados'>".$inst."</td>";
        $html .= "      <td class='show_dados_center'>".$vet[dataExame]."</td>";
        $html .= "      <td class='show_dados' width='50%'>".$tipo."</td>";
        if (!$consulta) {
            $html .= "      <td class='botao'>";
            $html .= "<a href='#' onClick='alteraExame(".$chave.");'><img src='".CAM_FW_IMAGENS."ok.gif' width='20' height='20' border='0'></a>";
            $html .= "<a href='#' onClick='excluiExame(".$chave.");'><img src='".CAM_FW_IMAGENS."nao.gif' width='20' height='20' border='0'></a>";
            $html .= "      </td>";
        }
        $html .= "  </tr>";
        $html .= "  <tr>";
        $html .= "      <td class='show_dados' colspan='4'>".$desc."</td>";
        $html .= "  </tr>";
        $count++;
    }

    $html .= "</table>";
    $js .= "aux = d.getElementById('exame'); ";
    $js .= "aux.innerHTML = \"".$html."\"; ";

    $res = array();
    $res[0] = $js;
    $res[1] = $html;

    return $res;
}//Fim da function geraResumoExame

/**************************************************************************
 Gera um javascript que cria uma tabela em html com o resumo dos itens
 Retorna o script na variável $js
/**************************************************************************/
function geraResumoInternacao($vetor, $consulta = false)
{
    $js = "";

    $html  = "<table width='100%' cellspacing=0 border=0 cellpadding=0>";
    $html .= "  <tr>";
    $html .= "      <td class='label' width='5%'>&nbsp;</td>";
    $html .= "      <td class='labelcenter' width='65%'>Instituição</td>";
    $html .= "      <td class='labelcenter' width='25%'>Período</td>";
    if (!$consulta) {
        $html .= "  <td class='labelcenter'>Ação</td>";
    }
    $html .= "  </tr>";
    $html .= "  <tr>";
    $html .= "      <td class='labelcenter' colspan='4'>Descrição</td>";
    $html .= "  </tr>";

    $vet = "";
    $count = 1;

    foreach ($vetor as $chave=>$vet) {
        //Retira as quebras de linha e substitui pela tag <br>
        $desc = preg_replace( "/\r?\n/","<br>",$vet[motivo]);
        $inst = pegaDado("nom_instituicao","cse.instituicao_saude","Where cod_instituicao = '".$vet[codInstituicao]."' ");

        $html .= " <tr>";
        $html .= "      <td class='labelcenter' rowspan='2'>".$count."</td>";
        $html .= "      <td class='show_dados'>".$inst."</td>";
        $html .= "      <td class='show_dados_center'>".$vet[dataBaixa]." a ".$vet[dataAlta]."</td>";
        if (!$consulta) {
            $html .= "      <td class='botao'>";
            $html .= "<a href='#' onClick='alteraInt(".$chave.");'><img src='".CAM_FW_IMAGENS."ok.gif' width='20' height='20' border='0'></a>";
            $html .= "<a href='#' onClick='excluiInt(".$chave.");'><img src='".CAM_FW_IMAGENS."nao.gif' width='20' height='20' border='0'></a>";
            $html .= "      </td>";
        }
        $html .= " </tr>";
        $html .= " <tr>";
        $html .= "      <td class='show_dados' colspan='3'>".$desc."</td>";
        $html .= " </tr>";
        $count++;
    }

    $html .= "</table>";
    $js .= "aux = d.getElementById('internacao'); ";
    $js .= "aux.innerHTML = \"".$html."\"; ";

    $res = array();
    $res[0] = $js;
    $res[1] = $html;

    return $res;
}//Fim da function geraResumoInternacao

?>

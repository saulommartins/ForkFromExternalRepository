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
  * Classe do componente de data com calendário
  * Data de Criação: 09/10/2008

  * @author Analista      Dagiane Vieira
  * @author Desenvolvedor Alex Cardoso

  * @package URBEM
  * @subpackage

  $Id:$

  * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                      );

function preencherEscala()
{
    $obTPontoEscala = new TPontoEscala();
    $stExtensao = $_REQUEST["stExtensao"];
    $inCodEscala = $_REQUEST["inCodEscala".$stExtensao];
    if ($inCodEscala != "") {
        $obTPontoEscala->setDado('cod_escala' , $inCodEscala );
        $obTPontoEscala->recuperaEscalasAtivas( $rsRecordSet, "", "" );
        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "document.frm.inCodEscala$stExtensao.value = '';\n";
            $stJs .= "document.frm.inCodEscala$stExtensao.focus();\n";
            $stJs .= "jQuery('#stEscala$stExtensao').html('$stNull');\n";
            $stJs .= "document.frm.HdninCodEscala$stExtensao.value = '';\n";
            $stJs .= "document.frm.stEscala$stExtensao.value = '';\n";
            $stJs .= "alertaAviso('@Campo Código da Escala inválido. (".$inCodEscala.")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs .= "jQuery('#stEscala$stExtensao').html('".$rsRecordSet->getCampo('descricao')."');\n";
            $stJs .= "document.frm.stEscala$stExtensao.value = '".$rsRecordSet->getCampo('descricao')."';\n";
            $stJs .= "document.frm.HdninCodEscala$stExtensao.value = '".$rsRecordSet->getCampo("cod_escala")."';\n";
        }
    } else {
        $stJs .= "jQuery('#stEscala$stExtensao').html('&nbsp;');";
    }

    return $stJs;
}
switch ($_GET["stCtrl"]) {
    case "preencherEscala":
        $stJs .= preencherEscala();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>

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
    * Oculto do componente ILotacao
    * Data de Criação: 07/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 31018 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 10:05:13 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php"                                );
include_once ( CAM_GRH_PES_MAPEAMENTO."FPessoalOrganogramaVigentePorTimestamp.class.php"            );

function preencherLotacao()
{
    global $request;

    $stJs             = "";
    $stExtensao       = $request->get("stExtensao");
    $inCodOrganograma = $request->get("inCodOrganograma");

    if ( $request->get("inCodLotacao".$stExtensao) != "" ) {

        if ($inCodOrganograma == "") {
            $obFPessoalOrganogramaVigentePorTimestamp = new FPessoalOrganogramaVigentePorTimestamp();
            $obFPessoalOrganogramaVigentePorTimestamp->recuperaOrganogramaVigentePorTimestamp($rsOrganogramaVigente);
            $inCodOrganograma = $rsOrganogramaVigente->getCampo('cod_organograma');
        }

        $stFiltro  = " AND orgao_nivel.cod_organograma = ".$inCodOrganograma;
        $stFiltro .= " AND orgao_nivel.cod_estrutural  = '". $request->get('inCodLotacao'.$stExtensao)."'";

        $obTOrganogramaOrgao = new TOrganogramaOrgao;
        $obTOrganogramaOrgao->setDado('vigencia', date('Y-m-d'));
        $obTOrganogramaOrgao->recuperaOrgaos( $rsRecordSet, $stFiltro, " LIMIT 1 " );

        $stNull = "&nbsp;";
        if ( $rsRecordSet->getNumLinhas() <= 0) {
            $stJs .= "document.frm.inCodLotacao$stExtensao.value = '';                                                           \n";
            $stJs .= "document.frm.inCodLotacao$stExtensao.focus();                                                              \n";
            $stJs .= "document.getElementById('stLotacao$stExtensao').innerHTML = '$stNull';                                     \n";
            $stJs .= "document.frm.HdninCodLotacao$stExtensao.value = '';    \n";
            $stJs .= "document.frm.stLotacao$stExtensao.value = '';                \n";
            $stJs .= "alertaAviso('@Campo Lotação inválido. (".$request->get("inCodLotacao".$stExtensao).")','form','erro','".Sessao::getId()."');\n";
        } else {
            $stJs .= "document.getElementById('stLotacao$stExtensao').innerHTML = '".$rsRecordSet->getCampo('descricao')."';    \n";
            $stJs .= "document.frm.stLotacao$stExtensao.value = '".$rsRecordSet->getCampo('descricao')."';                      \n";
            $stJs .= "document.frm.HdninCodLotacao$stExtensao.value = '".$rsRecordSet->getCampo("cod_orgao")."';                \n";
        }
    } else {
        $stJs .= "document.getElementById('stLotacao$stExtensao').innerHTML = '&nbsp;';";
    }

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case "preencherLotacao":
        $stJs = preencherLotacao();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>

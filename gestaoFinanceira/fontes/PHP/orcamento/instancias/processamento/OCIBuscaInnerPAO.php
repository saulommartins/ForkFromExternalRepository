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
    * Popup de busca do PAO
    * Data de Criação: 11/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30824 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 11:49:55 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-02.01.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherPAO()
{
    $stJs = "";
    $stExtensao = $_REQUEST["stExtensao"];

    $inExercicio = trim($_REQUEST['inExercicio']);
    if ($inExercicio == "") {
        $inExercicio = Sessao::getExercicio();
    }

    include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php");
    $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade();
    $stOrderBy = "
          ORDER BY acao.num_acao
                 , dotacao
    ";
    $obTOrcamentoProjetoAtividade->recuperaPorNumPAODotacao($rsPAO, "WHERE pao.exercicio = '".$inExercicio."' AND acao.num_acao = ".trim($_GET['inNumPAO'.$stExtensao]) , $stOrderBy , $boTransacao);

    if ($rsPAO->getNumLinhas() == 1) {
        $stNomPAO  = $rsPAO->getCampo("titulo");
        $stNumPAO  = $rsPAO->getCampo("num_pao");
        $stNumAcao = $rsPAO->getCampo("num_acao");
        $stDotacao = $rsPAO->getCampo("dotacao");
    } else {
        
        $stNomPAO  = "&nbsp;";
        $stNumPAO  = "";
        $stNumAcao = "";
        $stDotacao = "";
        if ( $rsPAO->getNumLinhas() > 1 ) {
            $stJs .= "alertaAviso('O código PAO ( ".$_GET['inNumPAO'.$stExtensao]." ) deverá ser selecionado apartir da PopUp.','form','aviso','".Sessao::getId()."');\n";
        }
    }

    $stJs .= "d.getElementById('campoInnerPAO$stExtensao').innerHTML = '$stNomPAO';\n";
    $stJs .= "f.campoInnerPAO$stExtensao.value = '".$stNomPAO."';";
    $stJs .= "f.inNumPAO$stExtensao.value = '".$stNumAcao."';";
    $stJs .= "f.inHdnNumPAO$stExtensao.value = '".$stNumPAO."';";
    $stJs .= "f.stHdnDotacao$stExtensao.value = '".$stDotacao."';";

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case "preencherPAO":
        $stJs = preencherPAO();
    break;
}
if ($stJs) {
    echo $stJs;
}
?>

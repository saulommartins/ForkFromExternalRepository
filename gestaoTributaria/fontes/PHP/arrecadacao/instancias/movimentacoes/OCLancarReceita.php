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
  * Página de Formulário Oculto para Lançar Receita
  * Data de criação : 17/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: OCLancarReceita.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.06
**/

/*
$Log$
Revision 1.4  2006/09/15 11:14:47  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoEconomica.class.php" );
include_once ( CAM_FW_URBEM."MontaLocalizacao.class.php"   );

$obMontaLocalizacao = new MontaLocalizacao;
$obRARRAvaliacaoEconomica = new RARRAvaliacaoEconomica;

switch ($_REQUEST['stCtrl']) {
    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );

    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;
    case "buscaFuncao":
        $obRARRAvaliacaoEconomica->obRFuncao->setCodFuncao( $_REQUEST['inCodigoFormula'] );
        $obRARRAvaliacaoEconomica->obRFuncao->consultar();
        if ( $obRARRAvaliacaoEconomica->obRFuncao->getNomeFuncao() ) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$obRARRAvaliacaoEconomica->obRFuncao->getNomeFuncao()."';\n";
            $stJs .= "alertaAviso('','form','aviso','".Sessao::getId()."', '../');";
        } else {
            $stMsg = "Função inválida.";
            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('".$stMsg."(".$_REQUEST["stDescricao"].")','form','erro','".Sessao::getId()."', '../');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaCGM":
        $stText = "inNumCGM";
        $stSpan = "stNomCGM";
        if ($_REQUEST[ $stText ] != "") {

            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST[ $stText ] );
            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";

            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.'.$stText.'.value = "";';
                $stJs .= 'f.'.$stText.'.focus();';
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
            } else {
               $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        } else {
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaAtividade":
        if ($_REQUEST['inCodigoAtividade']) {
            $rsAtividades = new RecordSet;
            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->addInscricaoAtividade();
            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->addAtividade();
            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST['inCodigoAtividade'] );
            $obRARRAvaliacaoEconomica->obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->listarAtividade( $rsAtividades );
            if ( $rsAtividades->getNumLinhas() > 0 ) {
                $stJs .= "d.getElementById('stAtividade').innerHTML = '".$rsAtividades->getCampo( 'nom_atividade' )."';";
            } else {
                $stJs .= "d.getElementById('stAtividade').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Valor inválido. (".$_QUEST['inCodigoAtividade'].")', 'form', 'erro', '".Sessao::getId()."');\n";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
}

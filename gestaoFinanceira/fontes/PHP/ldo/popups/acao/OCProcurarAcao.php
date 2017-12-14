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
 * Página de Oculto do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GF_LDO_VISAO   . 'VLDOManterLDO.class.php');
include_once(CAM_GF_LDO_VISAO   . 'VLDOManterAcao.class.php');
include_once(CAM_GF_PPA_NEGOCIO . 'RPPAManterAcao.class.php');

$boExibePPA 	 = $_GET['boExibePPA'];
$boExibePrograma = $_GET['boExibePrograma'];
$stCampoCod 	 = $_GET['stNomCampoCod'];
$stCampoDesc	 = $_GET['stIdCampoDesc'];
$inNumAcao       = $_GET['inNumAcao'];

switch ($_REQUEST["stCtrl"]) {
    case "listaAcao":
        $rsPPA = VLDOManterLDO::recuperarInstancia()->recuperarPPA();

        if ($rsPPA->inNumLinhas > 0) {
            if ($inNumAcao) {
                $arParametros['inNumAcao'] = $inNumAcao;
                $arParametros['inCodPPA']  = $rsPPA->getCampo('cod_ppa');

                $rsAcao = VLDOManterAcao::recuperarInstancia()->recuperarAcaoPPA($arParametros);

                if ($rsAcao->inNumLinhas > 0) {
                    $obRPPAManterPrograma = new RPPAManterPrograma();
                    $obVPPAManterPrograma = new VPPAManterPrograma($obRPPAManterPrograma);

                    $arParametros['inCodPrograma'] = $rsAcao->getCampo('cod_programa');
                    $rsPPAHomologado = $obVPPAManterPrograma->buscaPrograma($arParametros);

                    $stJs = "document.getElementById('".$stCampoDesc."').innerHTML = '".$rsAcao->getCampo('descricao')."';\n";

                    if ($boExibePrograma) {
                        $arParametros = array();
                        $arParametros['inNumAcao']      = $rsAcao->getCampo('num_acao');
                        $arParametros['inNumPrograma']  = $rsAcao->getCampo('num_programa');
                        $arParametros['stNomPrograma']  = $rsAcao->getCampo('identificacao');
                        $arParametros['stDiagnostico']  = $rsAcao->getCampo('diagnostico');
                        $arParametros['stObjetivo']     = $rsAcao->getCampo('objetivo');
                        $arParametros['stDiretrizes']   = $rsAcao->getCampo('diretriz');
                        $arParametros['stPublico']      = $rsAcao->getCampo('publico_alvo');
                        $arParametros['stNatureza']     = $rsAcao->getCampo('continuo') ? 'Continuo' : 'Temporário';
                        $arParametros['inCodFuncao']    = $rsAcao->getCampo('cod_funcao');
                        $arParametros['stNomFuncao']    = $rsAcao->getCampo('desc_funcao');
                        $arParametros['inCodSubfuncao'] = $rsAcao->getCampo('cod_subfuncao');
                        $arParametros['stNomSubfuncao'] = $rsAcao->getCampo('desc_subfuncao');
                        $arParametros['inCodTipoAcao']  = $rsAcao->getCampo('cod_tipo');

                        $stHTML = VLDOManterAcao::recuperarInstancia()->montarPrograma($arParametros);

                        $stJs.= "document.getElementById('spnPrograma').innerHTML = '".$stHTML."';";
                        $stJs.= "document.getElementById('inCodAcaoPPA').value = '".$rsAcao->getCampo('cod_acao')."';";
                    } else {
                        $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                        $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                        $stJs.= "document.getElementById('inCodAcaoPPA').value = '';";

                        if ($boExibePrograma) {
                            $stJs.= "document.getElementById('spnPrograma').innerHTML  = '&nbsp;';";
                        }

                        $stJs.= "alertaAviso('@Código do Acao (". $inNumAcao .") não encontrado.', 'form','erro','".Sessao::getId()."');";
                    }
                } else {
                    $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                    $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                    $stJs.= "document.getElementById('inCodAcaoPPA').value = '';";

                    if ($boExibePrograma) {
                        $stJs.= "document.getElementById('spnPrograma').innerHTML  = '&nbsp;';";
                    }

                    $stJs.= "alertaAviso('@Código do Acao (". $inNumAcao .") inválido.', 'form','erro','".Sessao::getId()."');";
                }
            } else {
                $stJs = "document.getElementById('".$stCampoCod."').value = '';";
                $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
                $stJs.= "document.getElementById('inCodAcaoPPA').value = '';";

                if ($boExibePrograma) {
                    $stJs.= "document.getElementById('spnPrograma').innerHTML  = '&nbsp;';";
                  }
            }
        } else {
            $stJs = "document.getElementById('".$stCampoCod."').value = '';";
            $stJs.= "document.getElementById('".$stCampoDesc."').innerHTML = '&nbsp;';";
            $stJs.= "document.getElementById('inCodAcaoPPA').value = '';";

            if ($boExibePrograma) {
                $stJs.= "document.getElementById('spnPrograma').innerHTML  = '&nbsp;';";
            }

            $stJs.= "alertaAviso('@Código do Acao (". $inNumAcao .") inválido.', 'form','erro','".Sessao::getId()."');";
        }

        if ($_REQUEST['stScript']) {
            $stJs = str_replace("document.", "window.parent.frames['telaPrincipal'].document.", $stJs);
            echo '<script>' , $stJs , '</script>';
        } else {
            echo $stJs;
        }
    break;
}

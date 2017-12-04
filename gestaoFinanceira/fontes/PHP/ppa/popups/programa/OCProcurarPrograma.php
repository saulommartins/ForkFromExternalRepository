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
* Arquivo de popup de busca de Acaos
* Data de Criação: 02/12/2008

* @author Analista: Heleno Santos
* @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GF_PPA_NEGOCIO . 'RPPAManterPrograma.class.php';
include_once CAM_GF_PPA_VISAO   . 'VPPAManterPrograma.class.php';
# Parâmetros recebidos da lista de Programas.
$stCampoCod            = $_REQUEST['stNomCampoCod'];
$stCampoDesc           = $_REQUEST['stIdCampoDesc'];
$inNumPrograma         = $_REQUEST['inNumPrograma'];
$inCodPPA              = $_REQUEST['inCodPPA'];
$stDscPrograma         = $_REQUEST['stDscPrograma'];
$boScript              = $_REQUEST['boScript'];
switch ($_REQUEST["stCtrl"]) {
    case 'definePrograma':
        $obRPPAManterPrograma  = new RPPAManterPrograma();
        $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);
        $rsPrograma = new RecordSet();

        if ($inNumPrograma) {
            $arParametros['inNumPrograma'] = $inNumPrograma;
            $arParametros['inCodPPA']      = $inCodPPA;

            $rsPrograma = $obVPPAManterPrograma->buscaPrograma($arParametros);

            if ($rsPrograma->inNumLinhas > 0) {
                $stIdentificacao = str_replace(array("\n", "\r"), ' ', $rsPrograma->getCampo('identificacao'));

                if (strlen($stIdentificacao) > 40) {
                    $stIdentificacao = substr($stIdentificacao, 0, 40) . '...';
                }

                $stJs  = "d.getElementById('$stCampoCod').value = '$inNumPrograma';";
                $stJs .= "d.getElementById('$stCampoDesc').innerHTML = '$stIdentificacao';\n";
            } else {
                $stJs  = "d.getElementById('$stCampoCod').value = '';";
                $stJs .= "d.getElementById('$stCampoDesc').innerHTML = '&nbsp;';";

                $stJs  = "alertaAviso('@Código do Programa ($inNumPrograma) não encontrado.', 'form','erro','" . Sessao::getId() . "');";
            }
        } else {
            $stJs  = "d.getElementById('$stCampoCod').value = '';";
            $stJs .= "d.getElementById('$stCampoDesc').innerHTML = '&nbsp;';";
        }

        # Executa ação registrada ao Evento.
        $stJs .= "stEvento = d.getElementById('$stCampoCod').getAttribute('onChange');";
        $stJs .= "eval(stEvento);";
        $stJs .= "d.getElementById('$stCampoCod').focus();";

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case 'buscaPrograma':
        $stJs = '';
        $obRPPAManterPrograma  = new RPPAManterPrograma();
        $obVPPAManterPrograma  = new VPPAManterPrograma($obRPPAManterPrograma);

        $rsPrograma = new RecordSet();

        if ($inNumPrograma != "") {
            $param['inNumPrograma'] = $inNumPrograma;
            $param['inCodPPA']      = $inCodPPA;

            $rsPrograma = $obVPPAManterPrograma->buscaPrograma($param, false);

            if ($rsPrograma->inNumLinhas > 0) {
                $stIdentificacao = str_replace(array("\n", "\r"), ' ', $rsPrograma->getCampo('identificacao'));
                $stIdentificacao = str_replace("'", "\'", $stIdentificacao);

                if (strlen($stIdentificacao) > 40) {
                    $stIdentificacao = substr($stIdentificacao, 0, 40) . '...';
                }

                $stJs .= "jq('#" . $stCampoCod . "').val('" . $inNumPrograma . "');";
                $stJs .= "jq('#" . $stCampoDesc . "').html('" . $stIdentificacao . "');\n";
            } else {
                $stJs .= "jq('#" . $stCampoCod . "').val('');";
                $stJs .= "jq('#" . $stCampoDesc . "').html('&nbsp;');";

                $stJs .= "alertaAviso('@Código do Programa (". $inNumPrograma .") não encontrado.', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "jq('#" . $stCampoCod . "').val('');";
            $stJs .= "jq('#" . $stCampoDesc . "').html('&nbsp;');";
        }
    $stJs .= "LiberaFrames(true, false);";

        echo $stJs;
        break;
}

?>

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
    * Página de Formulario de Oculto de Lancamento
    * Data de Criação   : 18/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-23 11:35:07 -0200 (Sex, 23 Fev 2007) $

    * Casos de uso: uc-02.02.04
*/

/*
$Log$
Revision 1.5  2007/02/23 13:35:07  luciano
#8480#

Revision 1.4  2006/07/05 20:51:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );

$obRContabilidadeHistoricoPadrao     = new RContabilidadeHistoricoPadrao;
$obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
$obRContabilidadeLancamentoValor     = new RContabilidadeLancamentoValor;

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ManterLancamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

switch ($_POST["stCtrl"]) {

    case "buscaHistorico":
    if ($_POST["inCodHistorico"] != "") {
        $obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST["inCodHistorico"] );
        $obRContabilidadeHistoricoPadrao->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeHistoricoPadrao->consultar();
        $stNomHistorico = $obRContabilidadeHistoricoPadrao->getNomHistorico();
        $boComplemento = ($obRContabilidadeHistoricoPadrao->getComplemento() == 't') ? true : false;

        if ( $obRContabilidadeHistoricoPadrao->getCodHistorico() == '1' ) {
            $js .= 'f.inCodHistorico.value = "";';
            $js .= 'f.inCodHistorico.focus();';
            $js .= 'd.getElementById("stNomHistorico").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodHistorico"].")','form','erro','".Sessao::getId()."');";
        } elseif (!$stNomHistorico) {
            $js .= 'f.inCodHistorico.value = "";';
            $js .= 'f.inCodHistorico.focus();';
            $js .= 'd.getElementById("stNomHistorico").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodHistorico"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomHistorico").innerHTML = "'.$stNomHistorico.'";';
        }
        if ($boComplemento) {
            $js .= 'f.stComplemento.disabled=false;';
            $js .= 'f.boComplemento.value=true';
        } else {
            $js .= 'f.stComplemento.disabled=true;';
            $js .= 'f.boComplemento.value=false';
        }
        SistemaLegado::executaFrameOculto($js);
    } else $js .= 'd.getElementById("stNomHistorico").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContaDebito":
    if ($_POST['inCodContaDebito'] != "") {
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaDebito'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaDebito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaDebito) {
            $js .= 'f.inCodContaDebito.value = "";';
            $js .= 'f.inCodContaDebito.focus();';
            $js .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaDebito"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stContaDebito").innerHTML = "'.$stNomContaDebito.'";';
        }
     } else $js .= 'd.getElementById("stContaDebito").innerHTML = "&nbsp;";';
     SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContaCredito":
    if ($_POST['inCodContaCredito'] != "") {
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaCredito'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaCredito) {
            $js .= 'f.inCodContaCredito.value = "";';
            $js .= 'f.inCodContaCredito.focus();';
            $js .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaCredito"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stContaCredito").innerHTML = "'.$stNomContaCredito.'";';
        }
    } else $js .= 'd.getElementById("stContaCredito").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;
    case "buscaLote":
        if ($_POST['inCodLote'] != "") {
            $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
            $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
            $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
            $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->consultar();

            $stNomLote = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getNomLote();
            $stDtLote  = $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->getDtLote();

            if (!$stNomLote) {
                $js .= 'd.getElementById("stNomLote").readOnly = false;';
                $js .= 'd.getElementById("stDtLote").readOnly = false;';
                $js .= 'd.getElementById("stNomLote").value = "";';
                $js .= 'd.getElementById("stDtLote").value  = "";';
                $js .= 'd.getElementById("stNomLote").focus();';
            } else {
                $js .= 'd.getElementById("stNomLote").readOnly = true;';
                $js .= 'd.getElementById("stDtLote").readOnly  = true;';
                $js .= 'd.getElementById("stNomLote").value = "'.$stNomLote.'";';
                $js .= 'd.getElementById("stDtLote").value  = "'.$stDtLote .'";';
                $js .= 'f.inCodContaDebito.focus();';
            }
        } else {
            $js .= 'd.getElementById("stNomLote").readOnly = false;';
            $js .= 'd.getElementById("stDtLote").readOnly  = false;';
            $js .= 'd.getElementById("stNomLote").value = "";';
            $js .= 'd.getElementById("stDtLote").value  = "";';
            $js .= 'd.getElementById("stNomLote").focus();';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "buscaProxLote":
    if ($_POST['inCodEntidade'] != "") {
        $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'M' );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );
        $inCodLote = $rsLote->getCampo('cod_lote')+1;
        $js  = 'f.inCodLote.disabled=false;';
        $js .= 'f.inCodLote.value = "'.$inCodLote.'";';
     } else $js = 'f.inCodLote.value = "";';
     SistemaLegado::executaFrameOculto($js);
    break;

}
?>

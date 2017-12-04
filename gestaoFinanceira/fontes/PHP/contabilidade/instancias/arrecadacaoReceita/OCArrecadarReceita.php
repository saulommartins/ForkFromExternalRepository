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
    * Página de Formulario de Oculto de Arrecadação da Receita
    * Data de Criação   : 20/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-17 15:06:21 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.02.05
*/

/*
$Log$
Revision 1.7  2006/07/17 18:06:21  jose.eduardo
Bug #6383#

Revision 1.6  2006/07/05 20:50:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php" );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php" );

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ArrecadarReceita";
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
        $obRContabilidadeHistoricoPadrao = new RContabilidadeHistoricoPadrao;
        $obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST["inCodHistorico"] );
        $obRContabilidadeHistoricoPadrao->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeHistoricoPadrao->consultar();
        $stNomHistorico = $obRContabilidadeHistoricoPadrao->getNomHistorico();
        $boComplemento = ($obRContabilidadeHistoricoPadrao->getComplemento() == 't') ? true : false;
        if (!$stNomHistorico) {
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
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
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
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
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

    case "buscaReceita":
    if ($_POST['inCodReceita'] != "") {
        $obROrcamentoReceita = new ROrcamentoReceita;
        $obROrcamentoReceita->setCodReceita( $_POST['inCodReceita'] );
        $obROrcamentoReceita->setExercicio( Sessao::getExercicio() );
        $obROrcamentoReceita->listar( $rsReceita );
        $stNomReceita = $rsReceita->getCampo( "descricao" );
        if (!$stNomReceita) {
            $js .= 'f.inCodReceita.value = "";';
            $js .= 'f.inCodReceita.focus();';
            $js .= 'd.getElementById("stNomReceita").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodReceita"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomReceita").innerHTML = "'.$stNomReceita.'";';
        }
    } else $js .= 'd.getElementById("stNomReceita").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaLote":
    if ($_POST['inCodEntidade'] != "") {
        $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'A' );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );
        $inCodLote = $rsLote->getCampo('cod_lote')+1;
        $js  = 'f.inCodLote.readOnly = false;';
        $js .= 'f.inCodLote.value = "'.$inCodLote.'";';
     } else $js = 'f.inCodLote.readOnly = true; f.inCodLote.value = "";';
     SistemaLegado::executaFrameOculto($js);
    break;

    case "validaLote":
    if ($_POST['inCodEntidade'] != "") {
        $obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( 'A' );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );

        $inProxCodLote = $rsLote->getCampo('cod_lote') + 1;

        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_REQUEST['inCodLote'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->listar( $rsLote, 'cod_lote DESC LIMIT 1' );

        $inCodLote = $rsLote->getCampo('cod_lote');
//        $stDtLote  = $rsLote->getCampo('dt_lote');
//        $stDtAtual = date("d/m/Y");

        if ($inCodLote == $_REQUEST['inCodLote']) {
            $js  = 'f.inCodLote.value = "'.$inCodLote.'";';
            $js .= 'f.stNomLote.value = "'.$rsLote->getCampo('nom_lote').'";';
            $js .= 'f.stNomLote.readOnly = true;';
            $js .= 'f.stDtLote.value  = "'.$rsLote->getCampo('dt_lote').'";';
            $js .= 'f.stDtLote.readOnly = true;';
        } elseif ($inProxCodLote == $_REQUEST['inCodLote']) {
            $js  = 'f.inCodLote.value = "'.$inProxCodLote.'";';
            $js .= 'f.stNomLote.value = "";';
            $js .= 'f.stNomLote.readOnly = false;';
            $js .= 'f.stDtLote.value  = "";';
            $js .= 'f.stDtLote.readOnly = false;';
        } else {
            $js  = 'f.inCodLote.value = "";';
            $js .= 'f.stNomLote.value = "";';
            $js .= 'f.stNomLote.readOnly = false;';
            $js .= 'f.stDtLote.value  = "";';
            $js .= 'f.stDtLote.readOnly = false;';
            SistemaLegado::exibeAviso("Número do Lote inválido! (".$_REQUEST['inCodLote'].")","","");
        }
     } else {
        SistemaLegado::exibeAviso("Número do Lote inválido! (".$_REQUEST['inCodLote'].")","","");
        $js = 'f.inCodLote.value = "";';
     }
     SistemaLegado::executaFrameOculto($js);
    break;
}
?>

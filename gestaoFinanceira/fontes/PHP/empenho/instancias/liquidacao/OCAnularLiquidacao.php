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
    * Classe Oculta de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2007-08-30 17:07:23 -0300 (Qui, 30 Ago 2007) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.04
*/

/*
$Log$
Revision 1.10  2007/08/30 20:06:39  luciano
Bug#10034#

Revision 1.9  2006/07/05 20:48:41  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadePlanoContaAnalitica.class.php" );
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "AnularLiquidacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet , $boExecuta = true)
{
//        $obRContabilidadePlanoBanco->setCodPlano     ( $_POST['inCodPlano']      );
//        $obRContabilidadePlanoBanco->setCodEstrutural( $_POST['stCodEstrutural'] );
//        $obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
//        $obRContabilidadePlanoBanco->buscaSaldo( $rsLista );
//        $rsLista->addFormatacao( "valor", "NUMERIC_BR" );

        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "empenhado", "NUMERIC_BR" );
        $rsLista->addFormatacao( "liquidado", "NUMERIC_BR" );
        $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );
        $rsLista->addFormatacao( "vl_a_pagar", "NUMERIC_BR" );
        $rsLista->addFormatacao( "total_a_anular", "NUMERIC_BR" );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição ");
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Empenhado");
        $obLista->ultimoCabecalho->setWidth( 13 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidado");
        $obLista->ultimoCabecalho->setWidth( 13 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("A anular");
        $obLista->ultimoCabecalho->setWidth( 16 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 0 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_item" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "empenhado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "liquidado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obHdnValorPago = new Hidden;
        $obHdnValorPago->setName ("nuValorTotalAnular_");
        $obHdnValorPago->setValue("total_a_anular");

        // Define Objeto Numerico para Valor
        $obTxtValor = new Numerico;
        $obTxtValor->setName     ( "nuValor_[num_item]_" );
        $obTxtValor->setAlign    ( 'RIGHT');
        $obTxtValor->setTitle    ( "" );
        $obTxtValor->setNegativo ( false );
        $obTxtValor->setMaxLength( 19 );
        $obTxtValor->setSize     ( 21 );
        $obTxtValor->setValue    ( "total_a_anular" );

        if($_REQUEST['boAdiantamento'])
            $obTxtValor->setReadOnly ( true );

        $obLista->addDadoComponente( $obTxtValor );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obLista->addDadoComponente( $obHdnValorPago );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\'",$stHTML );
        $stHTML = str_replace( "\\\'","\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['total_a_anular']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['total_a_anular'];
        }
      $nuVlTotal = number_format($nuVlTotal,2,'.','');

        if ($boExecuta) {

            $stJS = "d.getElementById('spnLista').innerHTML = '".$stHTML."';";
            $stJS.= "d.getElementById('nuValorTotal').value='".$nuVlTotal."';";

            if($_REQUEST['boAdiantamento'])
                $stJS.= " alertaAviso('@Esta Liquidação é de adiantamentos/subvenções: a Anulação não poderá ser parcial.','form','erro','".Sessao::getId()."');";

            SistemaLegado::executaFrameOculto($stJS);
        } else {
            return $stHTML;
        }

}

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista( Sessao::read('arItens') );
    break;
    case "buscaHistorico":
    if ($_POST["inCodHistoricoPatrimon"] != "") {
        $obRContabilidadeHistoricoPadrao  = new RContabilidadeHistoricoPadrao;
        $obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST["inCodHistoricoPatrimon"] );
        $obRContabilidadeHistoricoPadrao->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeHistoricoPadrao->consultar();
        $stNomHistorico = $obRContabilidadeHistoricoPadrao->getNomHistorico();
        $boComplemento = ($obRContabilidadeHistoricoPadrao->getComplemento() == 't') ? true : false;
        if (!$stNomHistorico) {
            $js .= 'f.inCodHistoricoPatrimon.value = "";';
            $js .= 'f.inCodHistoricoPatrimon.focus();';
            $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodHistoricoPatrimon"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "'.$stNomHistorico.'";';
        }
        if ($boComplemento) {
            $js .= 'f.stComplemento.disabled=false;';
        } else {
            $js .= 'f.stComplemento.disabled=true;';
        }
        SistemaLegado::executaFrameOculto($js);
    } else $js .= 'd.getElementById("stNomHistoricoPatrimon").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContaContabilFinanc":
    if ($_POST['inCodContaContabilFinanc'] != "") {
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaContabilFinanc'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaDebito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaDebito) {
            $js .= 'f.inCodContaContabilFinanc.value = "";';
            $js .= 'f.inCodContaContabilFinanc.focus();';
            $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaContabilFinanc"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "'.$stNomContaDebito.'";';
        }
     } else $js .= 'd.getElementById("stNomContaContabilFinanc").innerHTML = "&nbsp;";';
     SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContaDebPatrimon":
    if ($_POST['inCodContaDebPatrimon'] != "") {
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaDebPatrimon'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaCredito) {
            $js .= 'f.inCodContaDebPatrimon.value = "";';
            $js .= 'f.inCodContaDebPatrimon.focus();';
            $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaDebPatrimon"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "'.$stNomContaCredito.'";';
        }
    } else $js .= 'd.getElementById("stNomContaDebPatrimon").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case "buscaContaCredPatrimon":
    if ($_POST['inCodContaCredPatrimon'] != "") {
        $obRContabilidadePlanoContaAnalitica = new RContabilidadePlanoContaAnalitica;
        $obRContabilidadePlanoContaAnalitica->setCodPlano( $_POST['inCodContaCredPatrimon'] );
        $obRContabilidadePlanoContaAnalitica->setExercicio( Sessao::getExercicio() );
        $obRContabilidadePlanoContaAnalitica->consultar();
        $stNomContaCredito = $obRContabilidadePlanoContaAnalitica->getNomConta();
        if (!$stNomContaCredito) {
            $js .= 'f.inCodContaCredPatrimon.value = "";';
            $js .= 'f.inCodContaCredPatrimon.focus();';
            $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodContaCredPatrimon"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "'.$stNomContaCredito.'";';
        }
    } else $js .= 'd.getElementById("stNomContaCredPatrimon").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaDataLiquidacaoAnulada':
    if ($_REQUEST['stDtEstorno'] != "" and $_REQUEST['inCodEntidade'] != "") {
        $obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
        $obREmpenhoNotaLiquidacao     = new REmpenhoNotaLiquidacao( $obREmpenhoEmpenhoAutorizacao->obREmpenhoEmpenho );
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoNotaLiquidacao->setDtLiquidacao($_REQUEST['stDtLiquidacao']);
        $obREmpenhoNotaLiquidacao->setExercicio( Sessao::getExercicio() );
        $obREmpenhoNotaLiquidacao->listarMaiorDataAnulacao( $rsMaiorData );

        $stMaiorData = $rsMaiorData->getCampo( "data_anulacao" );

        $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
        if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "data_anulacao" ),$_POST["stDtEstorno"])) {
            $js .= "f.stDtEstorno.value = '".$rsMaiorData->getCampo( "data_anulacao" )."';";
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEstorno.focus();';
            $js .= "alertaAviso('@Data de Estorno de Liquidação deve ser maior ou igual a ".$rsMaiorData->getCampo( "data_anulacao" )." !','form','erro','".Sessao::getId()."');";
        }
        if (SistemaLegado::comparaDatas( $_POST['stDtEstorno'], date('d/m/Y'))) {
            $js .= "f.stDtEstorno.value = '".$rsMaiorData->getCampo( "data_anulacao" )."';";
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEstorno.focus();';
            $js .= "alertaAviso('@Campo Data de Anulação deve ser menor ou igual a data de hoje.','form','erro','".Sessao::getId()."');";
        }
    }
    SistemaLegado::executaFrameOculto($js);
    break;

}
?>

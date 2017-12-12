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
    * Paginae Oculta de Anulação de suplementações
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCAnularSuplementacao.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.07

*/

/*
$Log$
Revision 1.5  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "AnularSuplementacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obROrcamentoSuplementacao = new ROrcamentoSuplementacao;
$obROrcamentoSuplementacao->setExercicio( Sessao::getExercicio() );

function montaLista($rsRecordSet , $boExecuta = true)
{
    if ( !$rsRecordSet->eof() ) {
        $rsRecordSet->addFormatacao('vl_suplementacao', 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao('vl_reducao'      , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao('saldo_dotacao'   , 'NUMERIC_BR' );
        $rsRecordSet->addFormatacao('saldo_posterior' , 'NUMERIC_BR' );

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->ultimoCabecalho->setRowSpan( 2 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Dotação ");
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->ultimoCabecalho->setRowSpan( 2 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Suplementações");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->ultimoCabecalho->setColSpan( 2 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Saldos");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->ultimoCabecalho->setColSpan( 2 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho( true );
        $obLista->ultimoCabecalho->addConteudo("Suplementado");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Reduzido");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Atual");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Após Anulação");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_despesa] - [cod_estrutural]" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_suplementado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->ultimoDado->setCampo( "vl_reducao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "saldo_dotacao" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "saldo_posterior" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $boDotacaoNegativa = false;
        $nuVlTotal = 0;

        while ( !$rsRecordSet->eof() ) {
            if ( $rsRecordSet->getCampo('saldo_posterior') < 0 ) {
                $boDotacaoNegativa = true;
            }
            $nuVlTotal = bcadd( $rsRecordSet->getCampo( "vl_suplementado" ), $nuVlTotal, 4 );
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();

        if ($boDotacaoNegativa) {
            SistemaLegado::exibeAviso('Existe dotação com saldo negativo. Pressione Ok para continuar ou Cancelar para voltar à lista.','','erro');
        }

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp;";
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.getElementById('spnSuplementacao').innerHTML = '".$stHTML."'; f.nuVlTotal.value=".$nuVlTotal."; f.Ok.disabled = false");
    } else {
        return $stHTML;
    }

}

switch ($stCtrl) {

    case 'buscaNorma':
        if ($_POST['inCodNorma']) {
            $obROrcamentoSuplementacao->obRNorma->setCodNorma( $_POST['inCodNorma'] );
            $obROrcamentoSuplementacao->obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obROrcamentoSuplementacao->obRNorma->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obROrcamentoSuplementacao->obRNorma->getNomeNorma() != NULL ) {
                    $stNorma  = $obROrcamentoSuplementacao->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obROrcamentoSuplementacao->obRNorma->getNumNorma();
                    $stNorma .= '/'.$obROrcamentoSuplementacao->obRNorma->getExercicio().' - '.$obROrcamentoSuplementacao->obRNorma->getNomeNorma();
                     $js = 'd.getElementById("stNomTipoNorma").innerHTML = "'.$stNorma.'";';
                } else {
                    $js  = 'f.inCodNorma.value = "";';
                    $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodNorma.focus();';
                    $js .= 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
                    $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
                }
            }
        } else {
            $js = 'd.getElementById("stNomTipoNorma").innerHTML = "&nbsp;";';
        }
        $js .= "LiberaFrames(true,false);z";
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'montaLista':
        montaLista(Sessao::read('rsSuplementacao'));
    break;

}
?>

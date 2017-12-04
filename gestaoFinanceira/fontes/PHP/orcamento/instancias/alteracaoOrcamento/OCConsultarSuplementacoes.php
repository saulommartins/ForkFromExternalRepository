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
    * Página Oculta para Consulta de Suplementação
    * Data de Criação: 19/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.24
*/

/*
$Log$
Revision 1.5  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarSuplementacoes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

list( $inCodNorma , $stDecreto )  = $_REQUEST['inCodNorma'];

$obRegra = new ROrcamentoSuplementacao;
$obRegra->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet, $stTipo, $boExecuta = true)
{
    $rsLista = new RecordSet;
    if ($arRecordSet) {
        $rsLista->preenche( $arRecordSet );
    }
    $rsLista->addFormatacao( "valor", "NUMERIC_BR" );

    if ($stTipo == 'suplementado') {
        $stTitulo = "Registros da Suplementação";
        $stValor  = "Valor";
        $stDotacao = "Dotação Suplementada";
    } elseif ($stTipo == 'reduzido') {
        $stTitulo = "Registros da Suplementação";
        $stValor  = "Valor";
        $stDotacao = "Dotação Reduzida";
    } elseif ($stTipo == 'anulacao') {
        $stTitulo = "Dotações Negativas na Anulação da Suplementação";
        $stValor  = "Valor";
        $stDotacao = "Dotação Negativa";
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->setTitulo( $stTitulo );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo($stDotacao);
    $obLista->ultimoCabecalho->setWidth( 75 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo($stValor);
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dotacao" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "valor" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto("d.getElementById('$stSpan').innerHTML = '".$stHTML."';");
    } else {
        return $stHTML;
    }
}

switch ($stCtrl) {
    case 'montaListaSuplementacoes':
        $stHTML =  montaLista( Sessao::read('arSup'), "suplementado", false );
        $js     = "d.getElementById('spnListaSuplementacoesSup').innerHTML = '".$stHTML."';";

        $stHTML = montaLista( Sessao::read('arRed'), "reduzido",      false );
        $js    .= "d.getElementById('spnListaSuplementacoesRed').innerHTML = '".$stHTML."';";

        $stHTML = montaLista( Sessao::read('arNeg'), "anulacao",      false );
        $js    .= "d.getElementById('spnListaAnulacoes').innerHTML = '".$stHTML."';";

        SistemaLegado::executaFrameOculto( $js );
    break;
    case 'buscaDespesaOrcamentaria':
        if ($_REQUEST["inCodDotacaoOrcamentaria"] != "") {
            $obRegra->addDespesaReducao();
            $obRegra->roUltimoDespesaReducao->setCodDespesa( $_REQUEST["inCodDotacaoOrcamentaria"] );
            $obRegra->roUltimoDespesaReducao->setExercicio( Sessao::getExercicio() );
            $obRegra->roUltimoDespesaReducao->listarDespesa( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo( "descricao" );

            if (!$stNomDespesa) {
                $js  = 'f.inCodDotacaoOrcamentaria.value = "";';
                $js .= 'f.inCodDotacaoOrcamentaria.focus();';
                $js .= 'd.getElementById("stNomDotacaoOrcamentaria").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodDotacaoOrcamentaria"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js  = 'd.getElementById("stNomDotacaoOrcamentaria").innerHTML = "'.$stNomDespesa.'";';
            }
        } else {
            $js  = 'd.getElementById("stNomDotacaoOrcamentaria").innerHTML = "&nbsp;";';
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'buscaNorma':
        if ($_POST['inCodNorma']) {
            $obRegra->obRNorma->setCodNorma( $_POST['inCodNorma'] );
            $obRegra->obRNorma->setExercicio( Sessao::getExercicio() );
            $obErro = $obRegra->obRNorma->consultar( $rsRecordSet );
            if ( !$obErro->ocorreu() ) {
                if ( $obRegra->obRNorma->getNomeNorma() != NULL ) {
                    $stNorma  = $obRegra->obRNorma->obRTipoNorma->getNomeTipoNorma().' '.$obRegra->obRNorma->getNumNorma();
                    $stNorma .= '/'.$obRegra->obRNorma->getExercicio().' - '.$obRegra->obRNorma->getNomeNorma();
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
        SistemaLegado::executaFrameOculto( $js );
    break;
}
?>

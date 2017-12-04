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
    * Oculto de Abrir/Fechar Folha Complementar
    * Data de Criação   : 13/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: alex $
    $Date: 2007-12-14 14:20:26 -0200 (Sex, 14 Dez 2007) $

    * Casos de uso: uc-04.05.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                    );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function listarAnteriores($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoMovimentacao =  new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(Sessao::read('inCodPeriodoMovimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setSituacao("f");
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setTimestamp(Sessao::read('stTimestampFolhaSalarioFechada'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->listarFolhaComplementarFechadaAnteriorSalario($rsAnteriores);

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Folhas Complementares Fechadas Anteriores ao Fechamento da Folha Salário" );
    $obLista->setRecordSet( $rsAnteriores );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Aberta" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Fechada" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Situação Folha Salário" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_complementar" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_abertura" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_fechamento" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "situacao_folha" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(Sessao::read('inCodPeriodoMovimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta();
    if ( Sessao::read('boFolhaSalarioReaberta') == false and $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getSituacao() == 'f' ) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "REABRIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:processarDado('reabrir');" );
        $obLista->ultimaAcao->addCampo("1","cod_complementar");
        $obLista->ultimaAcao->setUnicoBotao(true);
        $obLista->ultimaAcao->setAcaoUnicoBotao("ultimo");
        $obLista->commitAcao();
    } else {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "" );
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listarPosteriores($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoMovimentacao =  new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->obRFolhaPagamentoFolhaSituacao->listarVezesFecharAbrirFolhaPagamento($rsContador,Sessao::read('inCodPeriodoMovimentacao'),"f");
    $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(Sessao::read('inCodPeriodoMovimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setSituacao("f");
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setTimestamp(Sessao::read('stTimestampFolhaSalarioFechada'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->listarFolhaComplementarFechadaPosteriorSalario($rsPosterior);

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    if ( $rsContador->getCampo('contador') >= 1 ) {
        $obLista->setTitulo( "Folhas Complementares Fechadas Posteriores ao Fechamento da Folha Salário" );
    } else {
        $obLista->setTitulo( "Folhas Complementares Fechadas" );
    }

    $obLista->setRecordSet( $rsPosterior );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Aberta" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Fechada" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_complementar" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_abertura" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "data_fechamento" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(Sessao::read('inCodPeriodoMovimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->listarFolhaComplementar($rsFolhaComplementar);

    $rsFolhaComplementar->setUltimoElemento();

    if ( $rsFolhaComplementar->getCampo('situacao') == 'f' ) {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "REABRIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:processarDado('reabrir');" );
        $obLista->ultimaAcao->addCampo("1","cod_complementar");
        $obLista->ultimaAcao->setUnicoBotao(true);
        $obLista->ultimaAcao->setAcaoUnicoBotao("ultimo");
        $obLista->commitAcao();
    } else {
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "" );
        $obLista->commitAcao();
    }

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSpan2').innerHTML = '".$stHtml."';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan3($boExecuta=false)
{
    $obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(Sessao::read('inCodPeriodoMovimentacao'));
    $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta();
    if ( $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getSituacao() == "a" ) {
        $stSituacao = "Aberta";
        $dtData = date("d/m/Y",strtotime(substr($obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getTimestamp(),0,19)));
        $inCodComplementar = $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
    } else {
        $stSituacao = "Nenhuma Folha Aberta";
        $dtData     = "";
    }

    $obLblSituacao = new Label;
    $obLblSituacao->setName                  ( "stSituacao"                                              );
    $obLblSituacao->setValue                 ( $stSituacao                                               );
    $obLblSituacao->setRotulo                ( "Situação"                                                );

    $obLblData = new Label;
    $obLblData->setName                      ( "dtData"                                                  );
    $obLblData->setValue                     ( $dtData                                                   );
    $obLblData->setRotulo                    ( "Data Abertura"                                           );

    $obBtAbrir= new Button;
    $obBtAbrir->setName                      ( "btnAbrir"                                                );
    $obBtAbrir->setValue                     ( "Abrir"                                                   );
    $obBtAbrir->setTipo                      ( "button"                                                  );
    if ($stSituacao == 'Aberta') {
        $obBtAbrir->setDisabled              ( true                                                      );
    }
    $obBtAbrir->obEvento->setOnClick         ( "buscaValor('abrir');"                                    );

    $obBtFechar= new Button;
    $obBtFechar->setName                     ( "btnFechar"                                               );
    $obBtFechar->setValue                    ( "Fechar"                                                  );
    $obBtFechar->setTipo                     ( "button"                                                  );
    if ($stSituacao != 'Aberta') {
        $obBtFechar->setDisabled             ( true                                                      );
    }
    $obBtFechar->obEvento->setOnClick        ( "buscaValor('fechar');"                                   );

    $obBtnExcluir = new Button;
    $obBtnExcluir->setName                   ( "btnExcluir"                                              );
    $obBtnExcluir->setValue                  ( "Excluir"                                                 );
    $obBtnExcluir->setTipo                   ( "button"                                                  );
    if ($stSituacao != 'Aberta') {
        $obBtnExcluir->setDisabled           ( true                                                      );
    }
    //$obBtnExcluir->obEvento->setOnClick      ( "buscaValor('excluir');"                                  );
    $stCaminho   = CAM_GRH_FOL_INSTANCIAS."folhaComplementar/PRManterFolhaComplementar.php";
    $stLink = Sessao::getId().'&inCodPeriodoMovimentacao='.Sessao::read('inCodPeriodoMovimentacao').'&inCodComplementar='.$inCodComplementar.'&stAcao=excluir&'.$stLink."*_*stDescQuestao=Folha Complementar Aberta em ".$dtData;
    $stLink = str_replace( '&', '*_*', $stLink );
    $obBtnExcluir->obEvento->setOnClick("alertaQuestao('".$stCaminho."?".$stLink."','sn_excluir','".Sessao::getId()."');");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                 ( "Folha Complementar"                                      );
    $obFormulario->addComponente             ( $obLblSituacao                                            );
    $obFormulario->addComponente             ( $obLblData                                                );
    $obFormulario->defineBarra( array($obBtAbrir, $obBtFechar, $obBtnExcluir)             );
    $obFormulario->montaInnerHtml();
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$obFormulario->getHTML()."';    \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function processarForm($boExecuta=false)
{
    $stJs .= listarAnteriores();
    $stJs .= listarPosteriores();
    $stJs .= gerarSpan3();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function abrir($boExecuta=false)
{
    $stJs .= "f.stAcao.value = 'abrir';     \n";
    if ($_GET['inCodComplementar']) {
        $stJs .= "f.inCodComplementar.value = ".$_GET['inCodComplementar'].";\n";
    }
    $stJs .= "parent.frames[2].Salvar();BloqueiaFrames(true,false);\n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function fechar($boExecuta=false)
{
    $stJs .= "f.stAcao.value = 'fechar';     \n";
    $stJs .= "parent.frames[2].Salvar();BloqueiaFrames(true,false);\n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluir($boExecuta=false)
{
    $stJs .= "f.stAcao.value = 'excluir';     \n";
    $stJs .= "parent.frames[2].Salvar();BloqueiaFrames(true,false);\n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

switch ($stCtrl) {
    case "abrir":
        $stJs .= abrir();
    break;
    case "fechar":
        $stJs .= fechar();
    break;
    case "excluir":
        $stJs .= excluir();
    break;
    case "reabrir":
        $stJs .= abrir();
    break;

}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>

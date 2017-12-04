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
    * Página de Processamento de Classificação Receita Dedutora
    * Data de Criação   : 10/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: PRClassificacaoReceitaDedutora.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ClassificacaoReceitaDedutora";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoClassificacaoReceita  = new ROrcamentoClassificacaoReceita;
$obROrcamentoClassificacaoReceita->setDedutora( true );
$obRNorma = new RNorma;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $arClassificacao    = explode( "." , $_POST['inCodClassificacao'] );
        $arClassificacaoBD  = explode( "." , $_POST['inCodClassificacao'] );
        $inCount            = count( $arClassificacao );
        $obRNorma->setCodNorma                ( $_REQUEST['inCodNorma']       );

        //busca o codigo da Classificacao que sera inserido e coloca zero na sua posição para verificar se a Classificacao pai existe
        for ($inPosicao = $inCount; $inPosicao >= 0; $inPosicao--) {
            if ($arClassificacao[$inPosicao] != 0) {
                $inTamPos = strlen( $arClassificacao[$inPosicao] );
                $arClassificacao[$inPosicao] = str_pad( '0' , $inTamPos, 0 , STR_PAD_LEFT );
                break;
            }
        }

        //remonta a Classificacao de Receita, colocanco '0' na ultima casa com valor
        for ($inPosicaoNew = 0; $inPosicaoNew < $inCount; $inPosicaoNew++) {
                $stVerificaClassReceita .= $arClassificacao[$inPosicaoNew].".";
        }
        $stVerificaClassReceita = substr( $stVerificaClassReceita, 0, strlen( $stVerificaClassReceita ) - 1 );

        //verifica se existe uma Classificacao pai para a Classificacao de Receita informada
        $checkClass = false;
        $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
        $obROrcamentoClassificacaoReceita->listar      ( $rsClass );
        if ($inPosicao > 0) {
            while ( !$rsClass->eof() ) {
                if ( $rsClass->getCampo('mascara_classificacao') == $stVerificaClassReceita ) {
                    $checkClass = true;
                    break;
                }
                $rsClass->Proximo();
            }
        } else {
            $checkClass = true;
        }

        //verifica se a Classificação informada já não foi inserida
        if ($checkClass == true) {
            $rsClass->setPrimeiroElemento();
            while ( !$rsClass->eof() ) {
                if ( $rsClass->getCampo('mascara_classificacao') == $_POST['inCodClassificacao'] ) {
                    $checkClass = false;
                    $checkClass2 = true;
                    break;
                }
                $rsClass->Proximo();
            }
        }

        if ($checkClass == true) {
            $obROrcamentoClassificacaoReceita->obRNorma->setCodNorma( $_POST['inCodNorma'] );
            foreach ($arClassificacaoBD as $key => $valor) {
                $inCodPosicao = $key + 1;
                $obROrcamentoClassificacaoReceita->setCodClassificacao( $valor                );
                $obROrcamentoClassificacaoReceita->setCodPosicao      ( $inCodPosicao         );
                $obROrcamentoClassificacaoReceita->setDescricao       ( $_POST['stDescricao'] );
                $obROrcamentoClassificacaoReceita->setCodEstrutural   ( implode(".",$arClassificacaoBD) );
                $obROrcamentoClassificacaoReceita->setTipoReceita     ( '1' ); // Dedutoras
                $obErro = $obROrcamentoClassificacaoReceita->incluir();
            }
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm, $_POST['inCodClassificacao']." - ".$_POST['stDescricao'], "incluir", "aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            if( $checkClass2 == true )
                SistemaLegado::exibeAviso('Registro Duplicado - '.$_POST['inCodClassificacao'], "n_incluir" , "erro" );
            else
                SistemaLegado::exibeAviso('Classificação de Receita inválida - '.$_POST['inCodClassificacao'], "n_incluir" , "erro" );
        }
    break;
    case "alterar":
        $obROrcamentoClassificacaoReceita->setCodConta ( $_POST['inCodConta']          );
        $obROrcamentoClassificacaoReceita->setDescricao( $_POST['stDescricao']         );
        $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio()            );
        $obROrcamentoClassificacaoReceita->setTipoReceita ( 1 );
        $obRNorma->setCodNorma                ( $_REQUEST['inCodNorma']       );
        $obErro = $obROrcamentoClassificacaoReceita->alterar();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stArray) {
            foreach ($stArray as $stCampo => $stValor) {
        $stFiltro .= $stCampo."=". $stValor ."&";
        }
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, $_POST['inCodClassificacao']." - ".$_POST['stDescricao'], "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obROrcamentoClassificacaoReceita->setCodConta( $_GET['inCodConta'] );
        $obROrcamentoClassificacaoReceita->setMascClassificacao( $_GET['stMascClassDespesaReduzida'] );
        $obErro = $obROrcamentoClassificacaoReceita->excluir();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
    foreach ($arFiltro['filtro'] as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".htmlentities(urlencode( $stValor ), ENT_NOQUOTES, 'UTF-8')."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro, $_GET['stMascClassReceita']." - ".$_GET['stDescricao'] ,"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()) ,"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>

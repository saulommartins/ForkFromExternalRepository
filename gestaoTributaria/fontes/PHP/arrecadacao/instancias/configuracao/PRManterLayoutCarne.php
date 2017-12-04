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
  * Página de Processamento do LayoutCarne
  * Data de criação : 30/09/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: $

    Caso de uso: uc-05.03.01
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRModeloCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVariaveisLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRObservacaoDebitoLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRObservacaoLayoutCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRInformacaoAdicionalLayoutCarne.class.php" );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLayoutCarne";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

switch ($_REQUEST['stAcao']) {
    case "alterar":
        $obTARRObservacaoDebitoLayoutCarne = new TARRObservacaoDebitoLayoutCarne;
        $obTARRInformacaoAdicionalLayoutCarne = new TARRInformacaoAdicionalLayoutCarne;
        $obTARRVariaveisLayoutCarne = new TARRVariaveisLayoutCarne;
        $obTARRObservacaoLayoutCarne = new TARRObservacaoLayoutCarne;
        $obTARRModeloCarne = new TARRModeloCarne;

        $arLayoutInfo = Sessao::read( "layoutInfo" );

        $rsListaInfo = new RecordSet;
        $rsListaInfo->preenche( $arLayoutInfo );
        $rsListaInfo->ordena( "ordem_lista", "ASC", SORT_STRING );

        $arPosicaoInicialInfo = array();
        $inOrdemAtual = 0;
        while ( !$rsListaInfo->Eof() ) {
            if ( $rsListaInfo->getCampo( "ordem" ) != $inOrdemAtual ) {
                $inOrdemAtual = $rsListaInfo->getCampo( "ordem" );
                $inPosicaoInicial = 1;
                $inPosicaoFinal = $rsListaInfo->getCampo( "largura" );
            } else {
                $inPosicaoInicial += $inPosicaoFinal;
                $inPosicaoFinal = $inPosicaoInicial + $rsListaInfo->getCampo( "largura" );
            }

            $arPosicaoInicialInfo[] = $inPosicaoInicial;
            $rsListaInfo->proximo();
        }

        $rsListaInfo->setPrimeiroElemento();

        $arLayoutVariaveis = Sessao::read( "layoutVariaveis" );

        $rsListaVariaveis = new RecordSet;
        $rsListaVariaveis->preenche( $arLayoutVariaveis );
        $rsListaVariaveis->ordena( "ordem_lista", "ASC", SORT_STRING );

        $arPosicaoInicial = array();

        $inOrdemAtual = 0;
        while ( !$rsListaVariaveis->Eof() ) {
            if ( $rsListaVariaveis->getCampo( "ordem" ) != $inOrdemAtual ) {
                $inOrdemAtual = $rsListaVariaveis->getCampo( "ordem" );
                $inPosicaoInicial = 1;
                $inPosicaoFinal = $rsListaVariaveis->getCampo( "largura" );
            } else {
                $inPosicaoInicial += $inPosicaoFinal;
                $inPosicaoFinal = $inPosicaoInicial + $rsListaVariaveis->getCampo( "largura" );
            }

            $arPosicaoInicial[] = $inPosicaoInicial;
            $rsListaVariaveis->proximo();
        }

        $rsListaVariaveis->setPrimeiroElemento();

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRModeloCarne );
            $obTARRModeloCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            if ( $_REQUEST["cmbModulos"] == 1 )
                $obTARRModeloCarne->setDado( "cod_modulo", 12 );
            else
                $obTARRModeloCarne->setDado( "cod_modulo", 14 );

            $obTARRModeloCarne->setDado( "capa_primeira_folha", $_REQUEST["stCapaUnica"]?true:false );
            $obTARRModeloCarne->alteracao();

            $obTARRObservacaoDebitoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRObservacaoDebitoLayoutCarne->exclusao();

            if ($_REQUEST["stMsgArrecadacao"]) {
                $obTARRObservacaoDebitoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
                $obTARRObservacaoDebitoLayoutCarne->setDado( "observacao_devedor", $_REQUEST["stObservacaoDevedor"]?$_REQUEST["stObservacaoDevedor"]:" " );
                $obTARRObservacaoDebitoLayoutCarne->setDado( "observacao_nao_devedor", $_REQUEST["stObservacaoNDevedor"]?$_REQUEST["stObservacaoNDevedor"]:" " );
                $obTARRObservacaoDebitoLayoutCarne->inclusao();
            }

            $obTARRInformacaoAdicionalLayoutCarne->setCampoCod('cod_modelo');
            $obTARRInformacaoAdicionalLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRInformacaoAdicionalLayoutCarne->exclusao();

            $inY = 0;
            while ( !$rsListaInfo->Eof() ) { //info
                $obTARRInformacaoAdicionalLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
                $obTARRInformacaoAdicionalLayoutCarne->setDado( "cod_informacao", $rsListaInfo->getCampo( "cod_informacao" ) );
                $obTARRInformacaoAdicionalLayoutCarne->setDado( "ordem", $rsListaInfo->getCampo( "ordem" ) );
                $obTARRInformacaoAdicionalLayoutCarne->setDado( "posicao_inicial", $arPosicaoInicialInfo[$inY] );
                $obTARRInformacaoAdicionalLayoutCarne->setDado( "largura", $rsListaInfo->getCampo( "largura" ) );
                $obTARRInformacaoAdicionalLayoutCarne->inclusao();

                $rsListaInfo->proximo();
                $inY++;
            }

            $obTARRVariaveisLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRVariaveisLayoutCarne->exclusao();

            $inY = 0;
            while ( !$rsListaVariaveis->Eof() ) {
                $obTARRVariaveisLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
                $obTARRVariaveisLayoutCarne->setDado( "cod_modulo", $rsListaVariaveis->getCampo( "cod_modulo" ) );
                $obTARRVariaveisLayoutCarne->setDado( "cod_cadastro", $rsListaVariaveis->getCampo( "cod_cadastro" ) );
                $obTARRVariaveisLayoutCarne->setDado( "cod_atributo", $rsListaVariaveis->getCampo( "cod_atributo" ) );
                $obTARRVariaveisLayoutCarne->setDado( "ordem", $rsListaVariaveis->getCampo( "ordem" ) );
                $obTARRVariaveisLayoutCarne->setDado( "posicao_inicial", $arPosicaoInicial[$inY] );
                $obTARRVariaveisLayoutCarne->setDado( "largura", $rsListaVariaveis->getCampo( "largura" ) );
                $obTARRVariaveisLayoutCarne->inclusao();

                $rsListaVariaveis->proximo();
                $inY++;
            }

            $obTARRObservacaoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRObservacaoLayoutCarne->setDado( "capa", 'true' );
            $obTARRObservacaoLayoutCarne->exclusao();

            $obTARRObservacaoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRObservacaoLayoutCarne->setDado( "observacao", "'".$_REQUEST["stObservacaoCapa"]."'" );
            $obTARRObservacaoLayoutCarne->setDado( "capa", 'true' );
            $obTARRObservacaoLayoutCarne->inclusao();

            $obTARRObservacaoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRObservacaoLayoutCarne->setDado( "capa", 'false' );
            $obTARRObservacaoLayoutCarne->exclusao();

            $obTARRObservacaoLayoutCarne->setDado( "cod_modelo", $_REQUEST["cmbModeloArquivo"] );
            $obTARRObservacaoLayoutCarne->setDado( "observacao", "'".$_REQUEST["stObservacaoCorpo"]."'" );
            $obTARRObservacaoLayoutCarne->setDado( "capa", 'false' );
            $obTARRObservacaoLayoutCarne->inclusao();
        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],"Modelo: ".$_REQUEST["cmbModeloArquivo"],"incluir","aviso", Sessao::getId(), "../");
        break;
}

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
    * Página de processamento para o cadastro de loteamento
    * Data de Criação   : 21/03/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterLoteamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.6  2006/09/18 10:30:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMLoteamento.class.php" );

//$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stAcao = $request->get('stAcao');
//$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterLoteamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRCIMLoteamento = new RCIMLoteamento;
$obErro           = new Erro;

switch ($_REQUEST['stAcao']) {
    case "incluir":
        $obRCIMLoteamento->setNomeLoteamento ( $_REQUEST['stNomLoteamento']   );
        $obRCIMLoteamento->setLoteOrigem     ( $_REQUEST['inNumLoteamento']   );
        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inNumProcesso"]    );
        $obRCIMLoteamento->obRProcesso->setCodigoProcesso ( $arProcesso[0]    );
        $obRCIMLoteamento->obRProcesso->setExercicio( $arProcesso[1]          );
        $obRCIMLoteamento->setDataAprovacao  ( $_REQUEST['dtAprovacao']       );
        $obRCIMLoteamento->setDataLiberacao  ( $_REQUEST['dtLiberacao']       );
        $obRCIMLoteamento->setAreaComunitaria( $_REQUEST['inAreaComunitaria'] );
        $obRCIMLoteamento->setAreaLogradouro ( $_REQUEST['inAreaLogradouro' ] );

        if ($_REQUEST['inAreaComunitaria'] == "0,00") {
            $obErro->setDescricao( "Área Comunitária inválida (".$_REQUEST['inAreaComunitaria'].")." );
        } else {
            $obRCIMLoteamento->setAreaComunitaria( $_REQUEST['inAreaComunitaria']   );
        }
        if ($_REQUEST['inAreaLogradouro' ] == "0,00") {
            $obErro->setDescricao( "Área Logradouro inválida (".$_REQUEST['inAreaLogradouro' ].")." );
        } else {
            $obRCIMLoteamento->setAreaLogradouro ( $_REQUEST['inAreaLogradouro' ]   );
        }
        $arLotesSessao = Sessao::read('lotes');
        if ( count($arLotesSessao)>0 ) {
            for ($inCount=0;$inCount< count($arLotesSessao);$inCount++) {
                if (( $arLotesSessao[$inCount]['stLocalizacaoLoteamento'] == $_REQUEST['stNomLoteamento'] ) && ( $arLotesSessao[$inCount]['inNumLote'] == $_REQUEST['inNumLoteamento'] )) {
                    $obErro->setDescricao( "O lote ".$_REQUEST['inNumLoteamento']." consta como lote de origem.");
                } else {
                    $obRCIMLoteamento->addLote( $arLotesSessao[$inCount] );
                }
                $obErro = $obRCIMLoteamento->listarLoteamentoLote( $rsLoteamentoLote );
                if (( !$obErro->ocorreu() ) && ( $rsLoteamentoLote->getNumLinhas() > 0 )) {
                    $obErro->setDescricao( "O lote ".$arLotesSessao[$inCount]['inNumLote']." já consta em um loteamento.");
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        } else {
            $obErro->setDescricao( 'É necessário incluir ao menos um lote.' );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMLoteamento->incluirLoteamento();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm, $obRCIMLoteamento->getCodigoLoteamento()." - ".$_REQUEST['stNomLoteamento'],"incluir","aviso", Sessao::getId(), "../");
            $arLotesSessao = array();
            Sessao::write('lotes', $arLotesSessao);
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRCIMLoteamento->setCodigoLoteamento( $_REQUEST['inCodigoLoteamento'] );
        $obRCIMLoteamento->setNomeLoteamento  ( $_REQUEST['stNomLoteamento']    );
        $obRCIMLoteamento->setLoteOrigem      ( $_REQUEST['inNumLoteamento']    );
        $arProcesso = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST["inNumProcesso"]      );
        $obRCIMLoteamento->obRProcesso->setCodigoProcesso ( $arProcesso[0]      );
        $obRCIMLoteamento->obRProcesso->setExercicio( $arProcesso[1]            );
        $obRCIMLoteamento->setDataAprovacao  ( $_REQUEST['dtAprovacao']         );
        $obRCIMLoteamento->setDataLiberacao  ( $_REQUEST['dtLiberacao']         );

        if ($_REQUEST['inAreaComunitaria'] == "0,00") {
            $obErro->setDescricao( "Área Comunitária inválida (".$_REQUEST['inAreaComunitaria'].")." );
        } else {
            $obRCIMLoteamento->setAreaComunitaria( $_REQUEST['inAreaComunitaria']   );
        }
        if ($_REQUEST['inAreaLogradouro' ] == "0,00") {
            $obErro->setDescricao( "Área Logradouro inválida (".$_REQUEST['inAreaLogradouro' ].")." );
        } else {
            $obRCIMLoteamento->setAreaLogradouro ( $_REQUEST['inAreaLogradouro' ]   );
        }

        $arLotesSessao = Sessao::read('lotes');
        if ( count($arLotesSessao)>0 ) {
            for ($inCount=0;$inCount< count($arLotesSessao);$inCount++) {
                if ($arLotesSessao[$inCount]['inCodLote'] == $_REQUEST['inNumLoteamento']) {//inCodLote
                    $obErro->setDescricao( "O lote ".$arLotesSessao[$inCount]['inNumLote']." consta como lote de origem.");
                } else {
                    $obRCIMLoteamento->addLote( $arLotesSessao[$inCount] );
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        } else {
            $obErro->setDescricao( 'É necessário incluir ao menos um lote.' );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCIMLoteamento->alterarLoteamento();
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stLink, $obRCIMLoteamento->getCodigoLoteamento()." - ".$_REQUEST['stNomLoteamento'],"alterar","aviso", Sessao::getId(), "../");
//            $arLotesSessao = array();
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
        Sessao::write('lotes', $arLotesSessao);
        break;

    case "excluir":
        $obRCIMLoteamento->setCodigoLoteamento ( $_REQUEST['inCodigoLoteamento'] );
        $obErro = $obRCIMLoteamento->excluirLoteamento();
        if ( !$obErro->ocorreu() ) {
            //SistemaLegado::alertaAviso($pgList,"Loteamento: ".$obRCIMLoteamento->getCodigoLoteamento(),"excluir","aviso", Sessao::getId(), "../");
            SistemaLegado::alertaAviso($pgList, $obRCIMLoteamento->getCodigoLoteamento()." - ".$_REQUEST['stNomLoteamento'], "excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}

?>

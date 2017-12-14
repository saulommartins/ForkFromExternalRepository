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
    * Pagina de Processamento de Inclusao/Alteracao de CREDITO
    * Data de Criacao: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterCredito.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php" );

$stAcao = $request->get('stAcao');

$link   = Sessao::read( 'link'  );
$stLink = Sessao::read( 'stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredito";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONCredito = new RMONCredito;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {
    case 'incluir':
        $arDadosFundamentacao = Sessao::read( "listaFundamentacao" );
        if ( count ($arDadosFundamentacao) <= 0 ) {
            sistemaLegado::exibeAviso("Nenhuma 'Fundamentação Legal' foi definida.", "n_incluir", "erro");
            exit;
        }

        $obRMONConvenio = new RMONConvenio;

        $obRMONConvenio->setNumeroConvenio( $_REQUEST["inNumConvenio"] );
        $obRMONConvenio->listarConvenio( $rsConvenio );
        if ( !$rsConvenio->eof() )
            $obRMONCredito->setCodConvenio  ( $rsConvenio->getCampo("cod_convenio"));

        $obRMONCredito->setDescricao    ( trim ($_REQUEST['stDescricao']));
        $obRMONCredito->setCodEspecie   ( trim ($_REQUEST['inCodEspecie']));
        $obRMONCredito->setCodNatureza  ( trim ($_REQUEST['inCodNatureza']));
        $obRMONCredito->setCodGenero    ( trim ($_REQUEST['inCodGenero']));

        $obRMONCredito->setCodFuncaoDesoneracao ( $_REQUEST['inCodigoFormula'] );

        $obRMONCredito->setArCodNorma     ( $arDadosFundamentacao );

        $obRMONCredito->setCodIndicador ( trim ($_REQUEST['inCodIndicador']));
        $obRMONCredito->setCodMoeda     ( trim ($_REQUEST['inCodMoeda']));

        $arDadosContaCorrente = explode( "-", $_REQUEST["cmbContaCorrente"] );
        $obRMONCredito->setCodAgencia ( $arDadosContaCorrente[2] );
        $obRMONCredito->setCodBanco ( $arDadosContaCorrente[1] );
        $obRMONCredito->setCodConta ( $arDadosContaCorrente[0] );
        if ($_REQUEST["cmbCarteira"])
            $obRMONCredito->setCodCarteira  ( $_REQUEST["cmbCarteira"] );

        $acrescimos = Sessao::read( 'acrescimos' );
        $obRMONCredito->setAcrescimos     ( $acrescimos );

        $obErro = $obRMONCredito->IncluirCredito();

        if (!$obErro->ocorreu () ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Crédito ".$obRMONCredito->getCodCredito().'-'.$_REQUEST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
        break;

    case 'excluir':
        $obRMONCredito->setCodCredito   ( trim ($_REQUEST['inCodCredito']));
        $obRMONCredito->setDescricao    ( trim ($_REQUEST['stDescricao']));
        $obRMONCredito->setCodEspecie   ( trim ($_REQUEST['inCodEspecie']));
        $obRMONCredito->setCodNatureza  ( trim ($_REQUEST['inCodNatureza']));
        $obRMONCredito->setCodGenero    ( trim ($_REQUEST['inCodGenero']));

        $obRMONCredito->setCodNorma     ( trim ($_REQUEST['inCodNorma']));
        $obRMONCredito->setCodIndicador ( trim ($_REQUEST['inCodIndicador']));
        $obRMONCredito->setCodMoeda     ( trim ($_REQUEST['inCodMoeda']));

        $obErro = $obRMONCredito->ExcluirCredito();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Credito ".$_REQUEST['inCodCredito'].'-'. $_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_excluir","erro" , Sessao::getId(), "../");
        }
        break;

    case 'alterar':
        $arDadosFundamentacao = Sessao::read( "listaFundamentacao" );
        if ( count ($arDadosFundamentacao) <= 0 ) {
            sistemaLegado::exibeAviso("Nenhuma 'Fundamentação Legal' foi definida.", "n_incluir", "erro");
            exit;
        }

        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setNumeroConvenio( $_REQUEST["inNumConvenio"] );
        $obRMONConvenio->listarConvenio( $rsConvenio );
        if ( !$rsConvenio->eof() )
            $obRMONCredito->setCodConvenio  ( $rsConvenio->getCampo("cod_convenio"));

        if ($_REQUEST["cmbCarteira"])
            $obRMONCredito->setCodCarteira  ( $_REQUEST["cmbCarteira"] );

        $obRMONCredito->setCodCredito   ( trim ($_REQUEST['inCodCredito']));
        $obRMONCredito->setDescricao    ( trim ($_REQUEST['stDescricao']));
        $obRMONCredito->setCodEspecie   ( trim ($_REQUEST['inCodEspecie']));
        $obRMONCredito->setCodNatureza  ( trim ($_REQUEST['inCodNatureza']));
        $obRMONCredito->setCodGenero    ( trim ($_REQUEST['inCodGenero']));

        $obRMONCredito->setCodFuncaoDesoneracao ( $_REQUEST['inCodigoFormula'] );

        $obRMONCredito->setArCodNorma   ( $arDadosFundamentacao );
        $obRMONCredito->setCodIndicador ( trim ($_REQUEST['inCodIndicador']));
        $obRMONCredito->setCodMoeda     ( trim ($_REQUEST['inCodMoeda']));

        $arDadosContaCorrente = explode( "-", $_REQUEST["cmbContaCorrente"] );
        $obRMONCredito->setCodAgencia ( $arDadosContaCorrente[2] );
        $obRMONCredito->setCodBanco ( $arDadosContaCorrente[1] );
        $obRMONCredito->setCodConta ( $arDadosContaCorrente[0] );

        $acrescimos = Sessao::read( 'acrescimos' );
        $obRMONCredito->setAcrescimos   ( $acrescimos );

        $obErro = $obRMONCredito->AlterarCredito();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList  ,"Crédito ".$_REQUEST['inCodCredito'].'-'.$_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
        break;
}

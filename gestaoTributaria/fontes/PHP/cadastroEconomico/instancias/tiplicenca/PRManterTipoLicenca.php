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
    * Página de Processamento de Inclusao/Alteracao de Atividade
    * Data de Criação   : 18/04/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    * $Id: PRManterTipoLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php"   );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );

//$stAcao = $request->get('stAcao');
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma  = "ManterTipoLicenca";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
$obErro          = new Erro;

$inCodAtributosSelecionados = $_REQUEST["inCodAtributosSelecionados"];
switch ($stAcao) {

    case "incluir":

       //Seta a categoria
           $obRCEMTipoLicencaDiversa->setNomeTipoLicencaDiversa 	( $_REQUEST["stNomeTipoLicencaDiversa"] );
        $obRCEMTipoLicencaDiversa->setTipoUtilizacao ( $_REQUEST["inTipoUtilizacao"]);
       //for para pegar os atributos selecionados
       for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCEMTipoLicencaDiversa->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
       }

           if ($_REQUEST["inCodElementosSelecionados"]) {
                foreach ($_REQUEST["inCodElementosSelecionados"] as $valor) {
                $obRCEMTipoLicencaDiversa->addTipoLicencaDiversaElemento();
                $obRCEMTipoLicencaDiversa->roUltimoElemento->setCodigoElemento( $valor );
            }
           }

        $stFiltro = " where a.cod_acao = ". Sessao::read('acao');
        $stFiltro .=" AND b.cod_documento = ". $_REQUEST['stCodDocumentoTxt'];
        $obTModeloDocumento = new TAdministracaoModeloDocumento;
        $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );
        $inX = 0;
        if ( !$rsDocumentos->Eof() ) {
            $inCodTipoDocAtual 	= $rsDocumentos->getCampo( "cod_tipo_documento" );
            $inCodDocAtual		= $rsDocumentos->getCampo( "cod_documento" );

            $arDocumentosSessao = Sessao::read( "documentos" );
            $arDocumentosSessao[$inX]['cod_tipo_documento'] = $inCodTipoDocAtual;
            $arDocumentosSessao[$inX]['cod_documento'] = $inCodDocAtual;
            Sessao::write("documentos", $arDocumentosSessao );
            $inX++;
        }

        $obErro = $obRCEMTipoLicencaDiversa->incluirTipoLicencaDiversa();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Nome tipo de licença diversa: ".$_REQUEST['stNomeTipoLicencaDiversa'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
       $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipoLicencaDiversa"] );
       $obRCEMTipoLicencaDiversa->setNomeTipoLicencaDiversa  ( $_REQUEST["stNomeTipoLicencaDiversa"]   );

       //for para pegar os atributos selecionados
       for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCEMTipoLicencaDiversa->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
       }
       if ($_REQUEST["inCodElementosSelecionados"]) {
            foreach ($_REQUEST["inCodElementosSelecionados"] as $valor) {
                $obRCEMTipoLicencaDiversa->addTipoLicencaDiversaElemento();
                $obRCEMTipoLicencaDiversa->roUltimoElemento->setCodigoElemento( $valor );
            }
        }
        $stFiltro = " where ";
        $stFiltro .= "  a.cod_acao = ". Sessao::read('acao');

        $stFiltro .="\n AND b.cod_documento = ". $_REQUEST['stCodDocumento'];
        $stFiltro .="\n AND b.cod_tipo_documento = 1 ";
        $obTModeloDocumento = new TAdministracaoModeloDocumento;
        $obTModeloDocumento->recuperaRelacionamento( $rsDocumentos, $stFiltro );

        $inX = 0;
        if ( !$rsDocumentos->Eof() ) {
            $inCodTipoDocAtual 	= $rsDocumentos->getCampo( "cod_tipo_documento" );
            $inCodDocAtual		= $rsDocumentos->getCampo( "cod_documento" );

            $arDocumentosSessao = Sessao::read( "documentos" );
            $arDocumentosSessao[$inX]['cod_tipo_documento'] = $inCodTipoDocAtual;
            $arDocumentosSessao[$inX]['cod_documento'] = $inCodDocAtual;

            Sessao::write( "documentos", $arDocumentosSessao );
            $inX++;
        }

        $obErro = $obRCEMTipoLicencaDiversa->alterarTipoLicencaDiversa();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome tipo de licença diversa: ".$_REQUEST['stNomeTipoLicencaDiversa'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }

    break;

    case "excluir":
        //Seta a categoria
        $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $_REQUEST["inCodigoTipoLicencaDiversa"] );
        $obRCEMTipoLicencaDiversa->setNomeTipoLicencaDiversa($_REQUEST['stNomeTipoLicencaDiversa']);
        $obErro = $obRCEMTipoLicencaDiversa->excluirTipoLicencaDiversa();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome tipo de licença diversa: ".$_REQUEST['stNomeTipoLicencaDiversa'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }

    break;
}

?>

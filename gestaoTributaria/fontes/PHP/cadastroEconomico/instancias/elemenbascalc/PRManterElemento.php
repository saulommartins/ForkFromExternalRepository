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
    * Formulario para Edificação
    * Data de Criação   : 14/04/2005
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
    * @package URBEM
    * @subpackage Regra

    * $Id: PRManterElemento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.05

*/

/*
$Log$
Revision 1.6  2007/03/12 20:36:49  rodrigo
Bug #8601#

Revision 1.5  2007/02/26 19:40:09  cassiano
Bug #8429#

Revision 1.4  2006/09/15 14:32:46  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterElemento";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRCEMElemento = new RCEMElemento( new RCEMAtividade );
$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];

switch ($stAcao) {
    case "incluir":
        $obRCEMElemento->setNomeElemento( trim($_REQUEST["stNomeElemento"]) );
        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCEMElemento->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }
        $obErro = $obRCEMElemento->incluirElemento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Nome elemento: ".$_REQUEST['stNomeElemento'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRCEMElemento->setCodigoElemento( $_REQUEST["inCodigoElemento"]     );
        $obRCEMElemento->setNomeElemento  ( trim($_REQUEST["stNomeElemento"]) );
        for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRCEMElemento->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo );
        }
        $obErro = $obRCEMElemento->alterarElemento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome elemento: ".$_REQUEST['stNomeElemento'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRCEMElemento->setCodigoElemento( $_REQUEST["inCodigoElemento"] );
        $obRCEMElemento->setNomeElemento($_REQUEST['stNomeElemento']);
        $obErro = $obRCEMElemento->excluirElemento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome elemento: ".$_REQUEST['stNomeElemento'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            $obErro->setDescricao("Elemento ".$_REQUEST['stNomeElemento']." está sendo referenciado pelo sistema. Exclusão não permitida!");
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;

    case "baixar":
        $obRCEMElemento->setCodigoElemento( $_REQUEST["inCodigoElemento"]      );
        $obRCEMElemento->setMotivo        ( trim($_REQUEST["stJustificativa"]) );
        $obErro = $obRCEMElemento->baixarElemento();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome elemento: ".$_REQUEST['stNomeElemento'],"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_baixar","erro",Sessao::getId(), "../");
        }
    break;
}
?>

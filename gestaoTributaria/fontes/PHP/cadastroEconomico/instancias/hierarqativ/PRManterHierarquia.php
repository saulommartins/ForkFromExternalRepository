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
    * Página de Processamento de Hierarquias
    * Data de Criação   : 18/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRManterHierarquia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06

*/

/*
$Log$
Revision 1.4  2006/09/15 14:32:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php"       );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterHierarquia";
$pgFilt = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

$obRCEMNivelAtividade = new RCEMNivelAtividade;
$inCodAtributosSelecionados = $_REQUEST["inCodAtributoSelecionados"];

switch ($stAcao) {
    case "incluir":
        $obRCEMNivelAtividade->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obRCEMNivelAtividade->setNomeNivel      ( $_REQUEST["stNomeNivel"]      );
        $obRCEMNivelAtividade->setMascara        ( $_REQUEST["stMascaraNivel"]   );
        $obErro = $obRCEMNivelAtividade->incluirNivel();

        if ( !$obErro->ocorreu() ) {
            $pgForm = $pgForm."&inCodigoVigencia=".$_REQUEST["inCodigoVigencia"];
            sistemaLegado::alertaAviso($pgForm,"Nome nivel: ".$_REQUEST['stNomeNivel'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "excluir":
        $obRCEMNivelAtividade->setCodigoNivel    ( $_REQUEST["inCodigoNivel"]    );
        $obRCEMNivelAtividade->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obErro = $obRCEMNivelAtividade->excluirNivel();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome nivel: ".$_REQUEST['stNomeNivel'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
    case "alterar":
        $obRCEMNivelAtividade->setCodigoNivel    ( $_REQUEST["inCodigoNivel"]    );
        $obRCEMNivelAtividade->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );
        $obRCEMNivelAtividade->setNomeNivel      ( $_REQUEST["stNomeNivel"]      );
        $obRCEMNivelAtividade->setMascara        ( $_REQUEST["stMascaraNivel"]   );
        $obErro = $obRCEMNivelAtividade->alterarNivel();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Nome nivel: ".$_REQUEST['stNomeNivel'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}
?>

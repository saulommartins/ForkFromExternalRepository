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
    * Data de Criação: 11/04/2005

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * $Id: PRManterCategoria.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.09

*/

/*
$Log$
Revision 1.7  2007/02/12 16:02:51  rodrigo
#6477#

Revision 1.6  2006/09/15 14:32:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMCategoria.class.php"   );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCategoria";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";

$obRCEMCategoria = new RCEMCategoria;
$obErro          = new Erro;

switch ($stAcao) {

    case "incluir":
       //Seta a categoria
       $obRCEMCategoria->setCodigoCategoria ( $_REQUEST["inCodigoCategoria"]        );
       $obRCEMCategoria->setNomeCategoria   ( trim( $_REQUEST["stNomeCategoria"] ) );
       $obErro = $obRCEMCategoria->incluirCategoria();
          if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Categoria ".$_POST['stNomeCategoria'],"incluir","aviso", Sessao::getId(), "../");
        } else {
          sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
          }

    break;

 case "alterar":
    //seta a categoria
    $obRCEMCategoria->setCodigoCategoria ( $_REQUEST["inCodigoCategoria"]        );
    $obRCEMCategoria->setNomeCategoria   ( trim( $_REQUEST["stNomeCategoria"] ) );
    $obErro = $obRCEMCategoria->alterarCategoria();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Categoria ".$_POST['stNomeCategoria'],"alterar","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
    }
    break;
 case "excluir":
    //seta a categoria
    $obRCEMCategoria->setCodigoCategoria ( $_REQUEST["inCodigoCategoria"]);
    $obRCEMCategoria->setNomeCategoria ( $_REQUEST["stNomeCategoria"]);
    $obErro = $obRCEMCategoria->excluirCategoria();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Categoria ".$_REQUEST['stNomeCategoria'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
    }
    break;
}
?>

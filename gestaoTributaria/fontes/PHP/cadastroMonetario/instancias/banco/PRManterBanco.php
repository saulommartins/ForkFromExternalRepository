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
    * Página de Processamento de Inclusao/Alteracao de Banco
    * Data de Criação: 04/10/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva

    * @ignore

    * $Id: PRManterBanco.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.01

*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:32  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONBanco.class.php"   );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterBanco";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONBanco = new RMONBanco;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

    case "incluir":

        $obRMONBanco->setNumBanco ( trim($_REQUEST["stNumBanco"]) );
        $obRMONBanco->setNomBanco ( trim($_REQUEST["stNomBanco"] ));
        $obErro = $obRMONBanco->incluirBanco();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Banco ".$_REQUEST['stNomBanco'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
        }
    break;

    case "alterar":
        //print_r($_REQUEST);
        $obRMONBanco->setCodBanco ( trim($_REQUEST["inCodBanco"]) );
        $obRMONBanco->setNumBanco ( trim($_REQUEST["stNumBanco"]) );
        $obRMONBanco->setNomBanco ( trim($_REQUEST["stNomBanco"] ));
        $obErro = $obRMONBanco->alterarBanco();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Banco ".$_REQUEST['stNomBanco'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

    case "excluir":
        $obRMONBanco->setCodBanco ( trim($_REQUEST["inCodBanco"]) );
        $obRMONBanco->setNumBanco ( trim($_REQUEST["stNumBanco"]) );
        $obRMONBanco->setNomBanco ( trim($_REQUEST["stNomBanco"] ));
        $obErro = $obRMONBanco->excluirBanco();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Banco ".$_REQUEST['stNomBanco'],"excluir","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()), "n_excluir","erro" , Sessao::getId(), "../");
        }
    break;

}
?>

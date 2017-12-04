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
    * Pagina de Processamento de Inclusao/Alteracao de MOEDA
    * Data de Criacao: 16/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterMoeda.php 60940 2014-11-25 18:03:14Z michel $

    *Casos de uso: uc-05.05.06

*/

/*
$Log$
Revision 1.8  2006/09/15 14:58:03  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php"   );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterMoeda";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";

$obRMONMoeda = new RMONMoeda;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

    case "incluir":
        $obRMONMoeda->setDescSingular   ( trim($_REQUEST["stDescSingular"]) );
        $obRMONMoeda->setDescPlural     ( trim($_REQUEST["stDescPlural"] ));
        $obRMONMoeda->setFracaoSingular ( trim($_REQUEST["stFracaoSingular"]) );
        $obRMONMoeda->setFracaoPlural   ( trim($_REQUEST["stFracaoPlural"] ));
        $obRMONMoeda->setSimbolo        ( trim($_REQUEST["stSimbolo"]) );
        $obRMONMoeda->setDtVigencia     ( trim($_REQUEST["dtVigencia"] ));

        //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
        /*
        $x = explode ('.', $_REQUEST['inCodFuncao'] );
        $obRMONMoeda->setCodModulo     ( $x[0] );
        $obRMONMoeda->setCodBiblioteca ( $x[1] );
        $obRMONMoeda->setCodFuncao     ( $x[2] );
        */

        $obErro = $obRMONMoeda->IncluirMoeda();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Moeda ".$_REQUEST["stSimbolo"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
        }
    break;

    case "excluir":

        $obRMONMoeda->setCodMoeda       ( trim($_REQUEST["inCodMoeda"]));
        $obRMONMoeda->setDescSingular   ( trim($_REQUEST["stDescSingular"]) );
        $obRMONMoeda->setDescPlural     ( trim($_REQUEST["stDescPlural"] ));
        $obRMONMoeda->setFracaoSingular ( trim($_REQUEST["stFracaoSingular"]) );
        $obRMONMoeda->setFracaoPlural   ( trim($_REQUEST["stFracaoPlural"]));
        $obRMONMoeda->setSimbolo        ( trim($_REQUEST["stSimbolo"]) );
        $obRMONMoeda->setDtVigencia     ( trim($_REQUEST["dtVigencia"] ));

        $obErro = $obRMONMoeda->ExcluirMoeda();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Moeda ".$_REQUEST["stSimbolo"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

    case "alterar":

        $obRMONMoeda->setCodMoeda       ( trim($_REQUEST["inCodMoeda"]));
        $obRMONMoeda->setDescSingular   ( trim($_REQUEST["stDescSingular"]) );
        $obRMONMoeda->setDescPlural     ( trim($_REQUEST["stDescPlural"] ));
        $obRMONMoeda->setFracaoSingular ( trim($_REQUEST["stFracaoSingular"]) );
        $obRMONMoeda->setFracaoPlural   ( trim($_REQUEST["stFracaoPlural"] ));
        $obRMONMoeda->setSimbolo        ( trim($_REQUEST["stSimbolo"]) );
        $obRMONMoeda->setDtVigencia     ( trim($_REQUEST["dtVigencia"] ));

        //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
        /*
        if ( trim ($_REQUEST['stFormulaAntiga']) != trim ($_REQUEST['inCodFuncao']) ) {
            $obRMONMoeda->setStrFormulaAntiga ( trim ($_REQUEST['inCodFuncao']) );
        }
        */

        $obErro = $obRMONMoeda->AlterarMoeda();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Moeda ".$_REQUEST["stSimbolo"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

}

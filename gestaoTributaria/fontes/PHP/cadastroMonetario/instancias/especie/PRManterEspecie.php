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
    * Página de Processamento de Inclusao/Alteracao de ESPECIE
    * Data de Criação: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterEspecie.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.09

*/

/*
$Log$
Revision 1.8  2007/02/26 17:34:49  cassiano
Bug #8421#

Revision 1.7  2007/02/26 17:34:02  cassiano
Bug #8421#

Revision 1.6  2006/09/15 14:57:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONEspecieCredito.class.php"   );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterEspecie";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONEspecieCredito = new RMONEspecieCredito;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

case 'incluir':

    $obRMONEspecieCredito->setDescricaoEspecie ( trim ($_REQUEST[stDescricaoEspecie]));
    $obRMONEspecieCredito->setCodEspecie ( trim ($_REQUEST[inCodEspecie]));
    $obRMONEspecieCredito->setCodNatureza ( trim ($_REQUEST[inCodNatureza]));
    $obRMONEspecieCredito->setCodGenero ( trim ($_REQUEST[inCodGenero]));

    $obErro = $obRMONEspecieCredito->IncluirEspecie();
    if (!$obErro->ocorreu () ) {
        sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Especie ".$obRMONEspecieCredito->getCodEspecie().'-'.$_REQUEST['stDescricaoEspecie'],"incluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
    }
break;

case 'excluir':

    $obRMONEspecieCredito->setCodEspecie ( trim ($_REQUEST[inCodEspecie]));
    $obRMONEspecieCredito->setCodNatureza ( trim ($_REQUEST[inCodNatureza]));
    $obRMONEspecieCredito->setCodGenero ( trim ($_REQUEST[inCodGenero]));
    $obRMONEspecieCredito->setDescricaoEspecie ( trim ($_REQUEST[stDescricaoEspecie]));

    $obErro = $obRMONEspecieCredito->ExcluirEspecie();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Especie ".$_REQUEST['inCodEspecie'].'-'.$_REQUEST['stDescricaoEspecie'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],$obErro->getDescricao(),"n_excluir","erro", Sessao::getId(), "../");
    }

break;

case 'alterar':

    $obRMONEspecieCredito->setCodEspecie ( trim ($_REQUEST[inCodEspecie]));
    $obRMONEspecieCredito->setCodNatureza ( trim ($_REQUEST[inCodNatureza]));
    $obRMONEspecieCredito->setCodGenero ( trim ($_REQUEST[inCodGenero]));
    $obRMONEspecieCredito->setDescricaoEspecie ( trim ($_REQUEST[stDescricaoEspecie]));

    $obErro = $obRMONEspecieCredito->AlterarEspecie();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Especie ".$_REQUEST['inCodEspecie'].'-'.$_REQUEST['stDescricaoEspecie'],"alterar","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }

break;
}

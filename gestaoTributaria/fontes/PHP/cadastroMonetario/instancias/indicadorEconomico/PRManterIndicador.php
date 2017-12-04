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
    * Pagina de Processamento de Inclusao/Alteracao de INDICADOR ECONOMICO
    * Data de Criacao: 19/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterIndicador.php 60940 2014-11-25 18:03:14Z michel $

    *Casos de uso: uc-05.05.07

*/

/*
$Log$
Revision 1.6  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"   );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterIndicador";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONIndicador = new RMONIndicadorEconomico;
$obErro      = new Erro;

switch ($stAcao) {

case 'incluir':
    $obRMONIndicador->setCodIndicador   ( trim ($_REQUEST['inCodIndicador']));
    $obRMONIndicador->setDescricao      ( trim ($_REQUEST['stDescricao']));
    $obRMONIndicador->setAbreviatura    ( trim ($_REQUEST['stAbreviatura']));
    $obRMONIndicador->setPrecisao       ( trim ($_REQUEST['inPrecisao']));

    //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
    /*
    $codigo = explode ('.', trim ($_REQUEST['inCodFuncao']) );
    $stModulo = $codigo[0];
    $stBiblioteca = $codigo[1];
    $stFuncao = $codigo[2];
    
    $obRMONIndicador->setCodFuncao      ( $stFuncao );
    $obRMONIndicador->setCodModulo      ( $stModulo );
    $obRMONIndicador->setCodBiblioteca  ( $stBiblioteca );
    $now = date("d/m/Y");
    $obRMONIndicador->setdtVigencia     ( $now );
    */

    $obErro = $obRMONIndicador->IncluirIndicador();
    if (!$obErro->ocorreu () ) {
        sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Indicador ".   $obRMONIndicador->getCodIndicador().'-'. $_REQUEST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
    }
break;

case 'excluir':

    $obRMONIndicador->setCodIndicador   ( trim ($_REQUEST['inCodIndicador']));
    $obRMONIndicador->setPrecisao       ( trim ($_REQUEST['inPrecisao']));
    $obRMONIndicador->setDescricao      ( trim ($_REQUEST['stDescricao']));
    $obRMONIndicador->setAbreviatura    ( trim ($_REQUEST['stAbreviatura']));
    $obRMONIndicador->setCodBiblioteca  ( trim ($_REQUEST['inCodBiblioteca']));
    $obRMONIndicador->setCodModulo      ( trim ($_REQUEST['inCodModulo']));
    //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
    //$obRMONIndicador->setCodFuncao      ( trim ($_REQUEST['inCodFuncao']));
    $obRMONIndicador->setDtVigencia     ( trim ($_REQUEST['dtVigencia']));
    $obRMONIndicador->setValor          ( trim ($_REQUEST['inValor']));

    $obErro = $obRMONIndicador->ExcluirIndicador();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Indicador ".$_REQUEST['inCodIndicador'].'-'. $_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
    }
break;

case 'alterar':

    $obRMONIndicador->setCodIndicador   ( trim ($_REQUEST['inCodIndicador']));
    $obRMONIndicador->setPrecisao       ( trim ($_REQUEST['inPrecisao']));
    $obRMONIndicador->setDescricao      ( trim ($_REQUEST['stDescricao']));
    $obRMONIndicador->setAbreviatura    ( trim ($_REQUEST['stAbreviatura']));
    $obRMONIndicador->setValor          ( trim ($_REQUEST['inValor']));

    $x = explode ('-', trim ($_REQUEST['dtVigenciaAntes']) );
    $dia = $x[2].'/'. $x[1] .'/'. $x[0];
    $obRMONIndicador->setDtVigenciaAntes( $dia );

    //Comentado Regra de Conversão, pois a mesma não vai mais existir na ação, conforme ticket #22008
    /*
    $x = explode ('.', trim ($_REQUEST['inCodFuncao']) );
    $obRMONIndicador->setCodModulo ( $x[0] );
    $obRMONIndicador->setCodBiblioteca ( $x[1] );
    $obRMONIndicador->setCodFuncao ( $x[2] );
    */

    $obErro = $obRMONIndicador->AlterarIndicador();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Indicador ".$_REQUEST['inCodIndicador'].'-'. $_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
    }

break;
}

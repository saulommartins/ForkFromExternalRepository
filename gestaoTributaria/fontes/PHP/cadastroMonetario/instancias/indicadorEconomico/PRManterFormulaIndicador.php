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

    * $Id: PRManterFormulaIndicador.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.07

*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"   );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterIndicador";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONIndicador = new RMONIndicadorEconomico;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

case 'formula':

    $obRMONIndicador->setCodIndicador   ( trim ($_REQUEST['inCodIndicador']));
    $obRMONIndicador->setDescricao      ( trim ($_REQUEST['stDescricao']));
    $obRMONIndicador->setValor          ( trim ($_REQUEST['inValor']));
    $obRMONIndicador->setDtVigenciaAntes( trim ($_REQUEST['dtVigenciaAntes']) );
    $obRMONIndicador->setDtVigencia( trim ($_REQUEST['dtVigencia']) );

    //FORMULA
    $x = explode ('.', trim ($_REQUEST['inCodFuncao']) );
    $obRMONIndicador->setCodModulo ( $x[0] );
    $obRMONIndicador->setCodBiblioteca ( $x[1] );
    $obRMONIndicador->setCodFuncao ( $x[2] );

    $obErro = $obRMONIndicador->AlterarFormulaIndicador();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Formula Indicador ".$_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
    }

break;

}

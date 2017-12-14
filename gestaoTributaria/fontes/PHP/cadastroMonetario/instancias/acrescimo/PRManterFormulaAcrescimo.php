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
    * Pagina de Processamento de Inclusao/Alteracao de FORMULA DE ACRESCIMO
    * Data de Criacao: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterFormulaAcrescimo.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.5  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONFormulaAcrescimo.class.php"   );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterFormulaAcrescimo";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LSManterAcrescimo.php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OCManterAcrescimo.php";
$pgJs          = "JSManterAcrescimo.js";;
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONFormulaAcrescimo = new RMONFormulaAcrescimo;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

case 'formula':

    //FORMULA
    $x = explode ('.', trim ($_REQUEST['inCodFuncao']) );
    $obRMONFormulaAcrescimo->setCodModulo ( $x[0] );
    $obRMONFormulaAcrescimo->setCodBiblioteca ( $x[1] );
    $obRMONFormulaAcrescimo->setCodFuncao ( $x[2] );

    $obRMONFormulaAcrescimo->setCodAcrescimo  ( trim ($_REQUEST['inCodAcrescimo']));
    $obRMONFormulaAcrescimo->setCodTipo       ( trim ($_REQUEST['inCodTipo']));

    //DATA
    $x = explode ('-', trim ($_REQUEST['dtVigenciaAntes']) );
    $dia = $x[2].'/'. $x[1] .'/'. $x[0];
    $obRMONFormulaAcrescimo->setDtVigenciaAntes ( $dia );

    //DATA ANTES
    $x = explode ('-', trim ($_REQUEST['dtVigencia']) );
    $dia = $x[2]. $x[1] . $x[0];
    $obRMONFormulaAcrescimo->setDtVigencia     ( $dia );

    $obErro = $obRMONFormulaAcrescimo->AlterarFormulaAcrescimo();
    if ( !$obErro->ocorreu() ) {
        sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Formula Acréscimo ".$_REQUEST['stDescAcrescimo'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
    }

break;

}

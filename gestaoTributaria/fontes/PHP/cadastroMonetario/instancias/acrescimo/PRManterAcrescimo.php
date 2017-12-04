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
    * Pagina de Processamento de Inclusao/Alteracao de ACRESCIMO
    * Data de Criacao: 08/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterAcrescimo.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.11

*/

/*
$Log$
Revision 1.10  2007/04/16 15:17:31  cassiano
Bug #8424#

Revision 1.9  2006/11/22 17:55:59  cercato
bug #7576#

Revision 1.8  2006/09/15 14:57:21  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php"   );
include_once ( CAM_GT_MON_NEGOCIO."RMONFormulaAcrescimo.class.php"   );

$link   = Sessao::read( "link" );
$stLink = Sessao::read( 'stLink' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAcrescimo";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONAcrescimo = new RMONAcrescimo;
$obRMONFormulaAcrescimo = new RMONFormulaAcrescimo;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {
    case 'definir':
        $obRMONAcrescimo = new RMONAcrescimo;
        $obRMONAcrescimo->setCodTipo( $_REQUEST["inCodTipo"] );
        $obRMONAcrescimo->setCodAcrescimo( $_REQUEST["inCodAcrescimo"] );

        $flNovoValorAcrescimo = $_REQUEST['flValorAcrescimo'];
        $dtNovoVigencia = $_REQUEST['dtVigenciaValor'];
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        $flNovoValorFormatado = number_format( str_replace(',', '.', str_replace('.', '', $flNovoValorAcrescimo)), 2, '.', '' );

        if ($flNovoValorFormatado > 0 && $dtNovoVigencia != "") {
            $insere = true;
            for ($inX=0; $inX<$nregistros; $inX++) {
                if ( $arValoresSessao[$inX]['dtVigencia'] == $dtNovoVigencia && (Sessao::read( "alterar" ) != $inX) ) {
                    //codigo ja estava na lista!
                    $insere = false;
                    break;
                }
            }

            if ($insere) {
                if (Sessao::read( "alterar" ) >= 0) {
                    $arValoresSessao[Sessao::read( "alterar" )]['flValorAcrescimo'] = $flNovoValorAcrescimo;
                    $arValoresSessao[Sessao::read( "alterar" )]['dtVigencia'] = $dtNovoVigencia;
                    Sessao::write( "alterar", -1 );
                } else {
                    $arValoresSessao[$nregistros]['flValorAcrescimo'] = $flNovoValorAcrescimo;
                    $arValoresSessao[$nregistros]['dtVigencia'] = $dtNovoVigencia;
                    $nregistros++;
                }
            }
        }

        Sessao::write( "valores", $arValoresSessao );
        if ($nregistros <= 0) {
            sistemaLegado::exibeAviso("A lista 'Registros de Valores' está vazia.", "n_definir", "erro");
        } else {
            $obRMONAcrescimo->setDadosValorAcrescimo( $arValoresSessao );
            $obErro = $obRMONAcrescimo->IncluirValorAcrescimo();
            if (!$obErro->ocorreu () ) {
                sistemaLegado::alertaAviso($pgList."?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Valor de acréscimo  definido para o código (".$obRMONAcrescimo->getCodAcrescimo().")", "definir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_definir", "erro", Sessao::getId(), "../");
            }
        }
        break;

    case 'incluir':
        $obRMONAcrescimo = new RMONAcrescimo;

        $codigo = explode ('.', trim ($_REQUEST['inCodFuncao']) );
        $stModulo = $codigo[0];
        $stBiblioteca = $codigo[1];
        $stFuncao = $codigo[2];

        $obRMONAcrescimo->setDescricao ( trim ($_REQUEST['stDescAcrescimo']));
        $obRMONAcrescimo->setCodTipo ( trim ($_REQUEST['cmbTipo']) );
        $obRMONAcrescimo->setCodNorma( $_REQUEST["inCodNorma"] );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodFuncao          ( $stFuncao );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodModulo          ( $stModulo );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodBiblioteca      ( $stBiblioteca );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodTipo            ( trim ($_REQUEST['cmbTipo']) );
        $now = date("d/m/Y");
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setDtVigencia         ( $now );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setDtVigenciaAntes    ( trim ($_REQUEST['dtVigenci']) );

        $obErro = $obRMONAcrescimo->IncluirAcrescimo();

        if (!$obErro->ocorreu () ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Acréscimo ".$obRMONAcrescimo->getCodAcrescimo().'-'.$_REQUEST['stDescAcrescimo'], "incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

    case 'excluir':
        $obRMONAcrescimo->setCodAcrescimo ( trim ($_REQUEST['inCodAcrescimo']));
        $obRMONAcrescimo->setCodTipo   ( trim ($_REQUEST['inCodTipo']));
        $obRMONAcrescimo->setDescricao($_REQUEST['stDescAcrescimo']);

        $obErro = $obRMONAcrescimo->ExcluirAcrescimo();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Acréscimo ".$_REQUEST['inCodAcrescimo'].'-'.$_REQUEST['stDescAcrescimo'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;

    case 'alterar':
        $obRMONAcrescimo->setCodNorma( $_REQUEST["inCodNorma"] );
        $obRMONAcrescimo->setCodAcrescimo ( trim ($_REQUEST['inCodAcrescimo']));
        $obRMONAcrescimo->setCodTipo   ( trim ($_REQUEST['inCodTipo']));
        $obRMONAcrescimo->setDescricao ( trim ($_REQUEST['stDescAcrescimo']));

        //DATA
        $stData = date("d/m/Y");
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setDtVigencia( $stData );

        //FORMULA
        $x = explode ('.', trim ($_REQUEST['inCodFuncao']) );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodModulo ( $x[0] );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodBiblioteca ( $x[1] );
        $obRMONAcrescimo->obRMONFormulaAcrescimo->setCodFuncao ( $x[2] );

        $obErro = $obRMONAcrescimo->AlterarAcrescimo();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Acréscimo ".$_REQUEST['inCodAcrescimo'].'-'.$_REQUEST['stDescAcrescimo'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
}

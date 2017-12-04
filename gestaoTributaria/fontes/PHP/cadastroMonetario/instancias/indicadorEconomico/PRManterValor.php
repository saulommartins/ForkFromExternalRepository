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
    * Pagina de Processamento de Inclusao/Alteracao de VALOR DO INDICADOR
    * Data de Criacao: 20/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterValor.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.08

*/

/*
$Log$
Revision 1.2  2006/09/15 14:57:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"   );

$link = Sessao::read( "link" );
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterValor";
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
    case 'definir':
        $inNovoValor = $_REQUEST["inValor"];
        $dtVigencia = $_REQUEST["dtVigencia"];
        $arValoresSessao = Sessao::read( "valores" );
        $nregistros = count ( $arValoresSessao );
        $inNovoValorFormatado = number_format( str_replace(',', '.', str_replace('.', '', $inNovoValor)), 2, '.', '' );
        if ($inNovoValor != "" && $dtVigencia != "" && $inNovoValorFormatado > 0) {
            $cont = 0;
            $insere = true;
            while ($cont < $nregistros) {
                if ( ($arValoresSessao[$cont]['dtVigencia'] == $dtVigencia) && (Sessao::read( "editar" ) != $cont) ) {
                    $insere = false;
                    break;
                } else {
                    $cont++;
                }
            }

            if ($insere) {
                if (Sessao::read( "editar" ) >= 0) {
                    $arValoresSessao[Sessao::read( "editar" )]['inValor'] = $inNovoValor;
                    $arValoresSessao[Sessao::read( "editar" )]['dtVigencia'] = $dtVigencia;
                    Sessao::write( "editar", -1 );
                    Sessao::write( "valores", $arValoresSessao );
                } else {
                    $arValoresSessao[$nregistros]['inValor'] = $inNovoValor;
                    $arValoresSessao[$nregistros]['dtVigencia'] = $dtVigencia;
                    Sessao::write( "valores", $arValoresSessao );
                    $nregistros++;
                }
            }
        }

        if ($nregistros <= 0) {
            sistemaLegado::exibeAviso("A lista 'Registros de Valores' está vazia.", "n_definir", "erro");
        } else {
            $obRMONIndicador->setCodIndicador   ( trim ($_REQUEST['inCodIndicador']) );
            $obRMONIndicador->setDados          ( $arValoresSessao );

            $obErro = $obRMONIndicador->IncluirValorIndicador();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Definir Valor concluído com sucesso! (Código Indicador: ".$_REQUEST['inCodIndicador'].")","definir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_definir","erro");
            }
        }
        break;
}

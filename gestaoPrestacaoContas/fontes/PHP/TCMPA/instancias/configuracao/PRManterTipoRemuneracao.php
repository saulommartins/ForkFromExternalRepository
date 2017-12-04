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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 18/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoRemuneracaoEvento.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoRemuneracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$stAcao = 'incluir';

$obTTPATipoRemuneracaoEvento = new TTPATipoRemuneracaoEvento();
$obErro = $obTTPATipoRemuneracaoEvento->excluirTodos();

if ( !$obErro->ocorreu() ) {

    foreach ($_POST['arArquivosSelecionadosRemuneracaoBase'] as $inChave => $inCodEventoRemuneracaoBase) {
        $obTTPATipoRemuneracaoEvento->setDado( 'codigo', 1 ); // 1 = Remuneração Base
        $obTTPATipoRemuneracaoEvento->setDado( 'cod_evento', $inCodEventoRemuneracaoBase );
        $obErro = $obTTPATipoRemuneracaoEvento->inclusao();
        if ( $obErro->ocorreu() ) {
            break;
        }
    }

    if ( !$obErro->ocorreu() ) {

        if ($_POST['arArquivosSelecionadosGratificacaoFuncao']) {
            foreach ($_POST['arArquivosSelecionadosGratificacaoFuncao'] as $inChave => $inCodEventoGratificacaoFuncao) {
                $obTTPATipoRemuneracaoEvento->setDado( 'codigo', 2 ); // 2 = Gratificação de Função
                $obTTPATipoRemuneracaoEvento->setDado( 'cod_evento', $inCodEventoGratificacaoFuncao );
                $obErro = $obTTPATipoRemuneracaoEvento->inclusao();
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ($_POST['arArquivosSelecionadosOutrasRemuneracoes']) {
                foreach ($_POST['arArquivosSelecionadosOutrasRemuneracoes'] as $inChave => $inCodEventoOutrasRemuneracoes) {
                    $obTTPATipoRemuneracaoEvento->setDado( 'codigo', 3 ); // 3 = Outras Renumerações
                    $obTTPATipoRemuneracaoEvento->setDado( 'cod_evento', $inCodEventoOutrasRemuneracoes );
                    $obErro = $obTTPATipoRemuneracaoEvento->inclusao();
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

SistemaLegado::LiberaFrames();

?>

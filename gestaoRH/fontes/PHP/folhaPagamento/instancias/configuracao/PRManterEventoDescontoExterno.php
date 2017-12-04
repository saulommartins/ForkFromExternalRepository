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
    * Processamento
    * Data de Criação: 08/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Camargo Finger

    * @ignore

    * Casos de uso: uc-04.05.61
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoConfiguracaoEventosDescontoExterno.class.php'                     );
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php'                                                 );

$stPrograma = 'ManterEventoDescontoExterno';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;

Sessao::setTrataExcecao(true);
###########################################################################################################################
# Código de Evento Base da Previdência                                                                                    #
###########################################################################################################################

$stFiltro = " WHERE codigo = '".$_REQUEST['inCodigoEventoBasePrevidencia']."'";
$obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
$nuCodigoEventoBasePrevidencia = $rsEvento->getCampo("cod_evento");

###########################################################################################################################
# Código de Evento Desconto da Previdência                                                                                #
###########################################################################################################################

$stFiltro = " WHERE codigo = '".$_REQUEST['inCodigoEventoDescontoPrevidencia']."'";
$obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
$nuCodigoEventoDescontoPrevidencia = $rsEvento->getCampo("cod_evento");

###########################################################################################################################
# Código de Evento Base IRRF                                                                                              #
###########################################################################################################################

$stFiltro = " WHERE codigo = '".$_REQUEST['inCodigoEventoBaseIRRF']."'";
$obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
$nuCodigoEventoBaseIRRF = $rsEvento->getCampo("cod_evento");

###########################################################################################################################
# Código de Evento Desconto IRRF                                                                                          #
###########################################################################################################################

$stFiltro = " WHERE codigo = '".$_REQUEST['inCodigoEventoDescontoIRRF']."'";
$obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
$nuCodigoEventoDescontoIRRF = $rsEvento->getCampo("cod_evento");

$obTFolhaPagamentoConfiguracaoEventoDescontoExterno = new TFolhaPagamentoConfiguracaoEventosDescontoExterno;
$obTFolhaPagamentoConfiguracaoEventoDescontoExterno->recuperaTodos( $rsConfiguracaoEventoDescontoExterno );

$obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado( "evento_base_irrf", $nuCodigoEventoBaseIRRF                       );
$obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado( "evento_desconto_irrf", $nuCodigoEventoDescontoIRRF               );
$obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado( "evento_base_previdencia", $nuCodigoEventoBasePrevidencia         );
$obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado( "evento_desconto_previdencia", $nuCodigoEventoDescontoPrevidencia );

if ( $rsConfiguracaoEventoDescontoExterno->getNumLinhas() != -1 ) {
    $obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado ( "cod_configuracao", $rsConfiguracaoEventoDescontoExterno->getCampo("cod_configuracao") );
    $obTFolhaPagamentoConfiguracaoEventoDescontoExterno->setDado ( "timestamp", $rsConfiguracaoEventoDescontoExterno->getCampo("timestamp") );
    $obTFolhaPagamentoConfiguracaoEventoDescontoExterno->alteracao();
    $stMsg = "Alterar configuração de evento de desconto externo.";
    Sessao::encerraExcecao();
    sistemaLegado::alertaAviso($pgForm,$stMsg,"alterar","aviso", Sessao::getId(), "../");
} else {
    $obTFolhaPagamentoConfiguracaoEventoDescontoExterno->inclusao();
    $stMsg = "Inserir configuração de evento de desconto externo.";
    Sessao::encerraExcecao();
    sistemaLegado::alertaAviso($pgForm,$stMsg,"incluir","aviso", Sessao::getId(), "../");
}

?>

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
    * Processamento de Configuração do Anexo 1 RGF
    * Data de Criação   : 13/10/2016

    * @author Desenvolvedor: Carlos Adriano

    * @package URBEM
    * @subpackage Configuração
*/

//inclui os arquivos necessarios
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";



$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
$obTAdministracaoConfiguracao->setDado("exercicio"  , Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado("cod_modulo" , '36');
$obTAdministracaoConfiguracao->setDado("parametro"  , 'stn_rgf1_despesas_exercicios_anteriores');
$obTAdministracaoConfiguracao->exclusao();

$obTAdministracaoConfiguracao->setDado("valor", $_REQUEST['inCodDespesa']);
$obTAdministracaoConfiguracao->inclusao();

SistemaLegado::alertaAviso('FMConfigurarRGF1.php', "Configuração realizada com sucesso!", 'incluir', 'aviso');
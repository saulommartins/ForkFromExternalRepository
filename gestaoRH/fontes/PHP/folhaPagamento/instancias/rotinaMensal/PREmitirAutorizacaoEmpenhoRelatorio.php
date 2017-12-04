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
    * Data de Criação: 24/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: alex $
    $Date: 2008-01-24 07:46:11 -0200 (Qui, 24 Jan 2008) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = 'EmitirAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

if (Sessao::read("origem") == "d") {
    $preview = new PreviewBirt(4,27,19);
    $preview->setVersaoBirt("2.5.0");
    $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("stEntidade",Sessao::getEntidade());
    $preview->addParametro("stOrigem",Sessao::read("origem"));
    $preview->addParametro("inCodConfiguracao",0);
    $preview->addParametro("inCodConfiguracaoAutorizacao",0);
    $preview->addParametro("inCodPeriodoMovimentacao",0);
    $preview->addParametro("stCadastro",'');
    $preview->addParametro("inCodPrevidencia",0);
    $preview->addParametro("stFiltro",Sessao::read("stCodigos"));
    $preview->addParametro("stTipoFiltro",Sessao::read("stTipoFiltro"));
    $preview->addParametro("stJoin",'');
} else {
    $preview = new PreviewBirt(4,27,3);
    $preview->setVersaoBirt("2.5.0");
    $preview->addParametro("entidade",Sessao::getCodEntidade($boTransacao));
    $preview->addParametro("stEntidade",Sessao::getEntidade());
    $preview->addParametro("stOrigem",Sessao::read("origem"));
    $preview->addParametro("inCodConfiguracao",Sessao::read("cod_configuracao"));
    $preview->addParametro("inCodConfiguracaoAutorizacao",Sessao::read("cod_configuracao_autorizacao"));
    $preview->addParametro("inCodPeriodoMovimentacao",Sessao::read("cod_periodo_movimentacao"));
    $preview->addParametro("stCadastro",Sessao::read("cadastro"));
    $preview->addParametro("inCodPrevidencia",Sessao::read("cod_previdencia"));
    $preview->addParametro("stFiltro",Sessao::read("filtro"));
    $preview->addParametro("stTipoFiltro",Sessao::read("stTipoFiltro"));
    $preview->addParametro("stJoin",Sessao::read("join"));
}
$preview->preview();
?>

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
* Página de Processamento de Configuração Pessoal
* Data de Criação   : 03/01/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-03-11 12:04:07 -0300 (Ter, 11 Mar 2008) $

* Casos de uso: uc-04.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRConfiguracaoPessoal = new RConfiguracaoPessoal;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRConfiguracaoPessoal->setMascaraRegistro			( $_POST['stMascaraRegistro'] );
$obRConfiguracaoPessoal->setGeracaoRegistro	        ( $_POST['boGeracaoRegistro'] );
$obRConfiguracaoPessoal->setMascaraCBO				( $_POST['stMascaraCBO'] );
$obRConfiguracaoPessoal->setContagemInicial  	    ( $_POST['stContagemInicial'] );

$obErro = $obRConfiguracaoPessoal->salvar();

if ( !$obErro->ocorreu() )
    sistemaLegado::alertaAviso($pgForm," ".$_POST['stMascaraRegistro'],"alterar","aviso", Sessao::getId(), "../");
else
    sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
?>

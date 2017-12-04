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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3659 $
$Name$
$Author: cassiano $
$Date: 2005-12-08 16:11:33 -0200 (Qui, 08 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "PopupRetorno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId();
$pgForm = "FM".$stPrograma.".php?".Sessao::getId();
$pgProc = "PR".$stPrograma.".php?".Sessao::getId();
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RFuncao;
$obErro  = new Erro;
$arFuncao = Sessao::read('Funcao');
$arFuncao['RetornoVar'] = $_POST['stVariavel'];
Sessao::write('Funcao',$arFuncao);

//$stCorpoLN = $obRegra->montaCorpoFuncao();
//$stCorpoLN = str_replace("\\\'","\'",$stCorpoLN);
//$stCorpoLN = str_replace("''","\\'\\'",$stCorpoLN);
//$stCorpoPL = $obRegra->ln2pl();
//$stCorpoPL = str_replace("''","\\'\\'",$stCorpoPL);
$stCorpoLN = $obRegra->montaCorpoFuncao();
$stCorpoPL = $obRegra->ln2pl();
//-->
$stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
$stCorpoPL = str_replace('\"','"',$stCorpoPL);
//<--
SistemaLegado::executaWindowOpener("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");

?>

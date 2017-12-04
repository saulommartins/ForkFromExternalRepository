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
* Arquivo de instância para manutenção de normas
* Data de Criação: 06/09/2005

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15641 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 13:25:02 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAcao.class.php");
$stNomeForm = $_REQUEST['stNomForm'];
$stCampoNom = $_REQUEST['stIdCampoDesc'];
$stCampoNum = $_REQUEST['stNomCampoCod'];
include_once 'JSListarAcao.js';

$rsAcao = new RecordSet();
$obTAdministracaoAcao = new TAdministracaoAcao();
if ($_REQUEST['inCodigoAcao']) {
    $stFiltro = ' WHERE cod_acao='.$_REQUEST['inCodigoAcao'];
    $obTAdministracaoAcao->recuperaTodos($rsAcao,$stFiltro,'cod_funcionalidade,ordem,cod_acao');
}

$stJs = '';
$stNomeAcao = $rsAcao->getCampo('nom_acao') ? $rsAcao->getCampo('nom_acao') : '&nbsp;';
$inCodigoAcao = $rsAcao->getCampo('nom_acao') ? $_REQUEST['inCodigoAcao'] : "''";
$stJs .= "retornaAcaoOculto(".$inCodigoAcao.", '".$stNomeAcao."' );";

if ( $rsAcao->eof() ) {
    SistemaLegado::exibeAviso("Ação ".$_REQUEST['inCodigoAcao']." inválida!","unica","");
}
SistemaLegado::executaFrameOculto($stJs);
?>

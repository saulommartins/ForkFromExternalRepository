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
* Arquivo de Processamento de Modelo de DOcumentos
* Data de Criação: 02/03/2006

* @author Analista: Fabio Bertold
* @author Desenvolvedor: Lucas Texeira Stephanou

$Revision: 7965 $
$Name$
$Author: diego $
$Date: 2006-03-28 17:56:57 -0300 (Ter, 28 Mar 2006) $

Casos de uso: uc-01.03.100
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RModeloDocumento.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterModeloDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRModeloDocumento = new RModeloDocumento;
$obErro = new Erro;

//SistemaLegado::debugRequest();

switch ($stAcao) {
    case "incluir":
    default:

        $obRModeloDocumento->obRAdministracaoAcao->setCodigoAcao( $_REQUEST["inCodAcao"]  );
        $obRModeloDocumento->setCodDocumento      ( $_REQUEST["inCodDocumento"]  );
        $obRModeloDocumento->setCheckSum          ( $_REQUEST["boPadrao"]        );
        $obRModeloDocumento->setArquivosIncluidos ( Sessao::read("arArquivosIncluidos") );
        $obRModeloDocumento->setArquivosExcluidos ( Sessao::read("arArquivosExcluidos") );

        $obErro = $obRModeloDocumento->salvaDocumento();
        //$obErro->setDescricao(" TESTE ");

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Documento: ".$obRModeloDocumento->getCodDocumento(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}
?>

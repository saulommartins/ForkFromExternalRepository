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
    * Página de Processamento de regras de Produtos
    * Data de Criação   : 22/09/2009

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @ignore

    * Casos de uso: uc-02.09.11
*/

/*
$Log: $

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include CAM_GF_PPA_MAPEAMENTO."TPPAProduto.class.php";
include CAM_GF_PPA_CLASSES."negocio/RPPAManterProduto.class.php";
include CAM_GF_PPA_CLASSES."visao/VPPAManterProduto.class.php";

//Define o nome dos arquivos PHP
$stProduto = "ManterProdutos";
$pgFilt     = "FL".$stProduto.".php";
$pgList     = "LS".$stProduto.".php";
$pgForm     = "FM".$stProduto.".php";
$pgProc     = "PR".$stProduto.".php";
$pgOcul     = "OC".$stProduto.".php";

$obRegraProduto = new RPPAManterProduto();
$obVisaoProduto = new VPPAManterProduto( $obRegraProduto );
$obVisaoProduto->executarAcao($_REQUEST);

?>

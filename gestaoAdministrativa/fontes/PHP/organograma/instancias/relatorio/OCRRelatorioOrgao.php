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
    * Arquivo de instância para Relatorio
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: OCRRelatorioOrgao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.05.02

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$obRRelatorio        = new RRelatorio;
$obROrganogramaOrgao = new ROrganogramaOrgao;

$filtroRelatorio = Sessao::read('filtroRelatorio');

if (!empty($filtroRelatorio['inCodOrganogramaOrganograma'])) {
    list($inCodOrganograma) = explode('§', $filtroRelatorio['inCodOrganogramaOrganograma']);
    $obROrganogramaOrgao->obROrganograma->setCodOrganograma($inCodOrganograma);
}

$obROrganogramaOrgao->setCodOrgao($filtroRelatorio['hdnUltimoOrgaoSelecionado']);

$obROrganogramaOrgao->geraRecordSet($rsOrgao);

Sessao::write('rsOrgao', $rsOrgao);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioOrgao.php" );

?>

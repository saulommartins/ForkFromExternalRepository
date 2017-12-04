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
* Página de relatório de Totais por Fornecedor
* Data de Criação   : 22/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30922 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                       );
include_once( CAM_GRH_BEN_NEGOCIO."RRelatorioTotaisPorFornecedor.class.php"                           );

$rsRelatorioTotaisPorFornecedor  = new Recordset;
$obRRelatorio                    = new RRelatorio;
$obRRelatorioTotaisPorFornecedor = new RRelatorioTotaisPorFornecedor;

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

$obRRelatorioTotaisPorFornecedor->setAgruparPorLotacao( isset($arSessaoFiltroRelatorio['stAgruparPorLotacao']) );
$obRRelatorioTotaisPorFornecedor->setAgruparPorLocal  ( isset($arSessaoFiltroRelatorio['stAgruparPorLocal'])   );
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setVigencia($arSessaoFiltroRelatorio['dtVigencia']);
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes(substr($arSessaoFiltroRelatorio['dtVigencia'],3,2));
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio(substr($arSessaoFiltroRelatorio['dtVigencia'],6,4));
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->setNumCGM(implode(",",$arSessaoFiltroRelatorio['FornecedorSelecionados']));
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->obRBeneficioValeTransporte->obRBeneficioItinerario->setCodItinerario(implode(",",$arSessaoFiltroRelatorio['ItinerarioSelecionados']));
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgao( (isset($arSessaoFiltroRelatorio['inCodLotacaoSelecionados'])) ? implode(",",$arSessaoFiltroRelatorio['inCodLotacaoSelecionados']) : '' );
$obRRelatorioTotaisPorFornecedor->obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaLocal->setCodLocal( (isset($arSessaoFiltroRelatorio['inCodLocalSelecionados']))   ? implode(",",$arSessaoFiltroRelatorio['inCodLocalSelecionados'])   : '' );
$obRRelatorioTotaisPorFornecedor->geraRecordSet( $rsRelatorioTotaisPorFornecedor );

Sessao::write('rsRelatorioTotaisPorFornecedor', $rsRelatorioTotaisPorFornecedor);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioTotaisPorFornecedor.php" );
?>

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
* Página de relatório de Concessão de Vale Transporte
* Data de Criação   : 09/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Revision: 30880 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php"                                                    );
include_once ( CAM_GRH_BEN_NEGOCIO."RRelatorioConcessaoValeTransporte.class.php"                    );

$obRRelatorio       = new RRelatorio;
$rsConcessao        = new Recordset;
$obRRelatorioCVT    = new RRelatorioConcessaoValeTransporte;

$arSessaoFiltroRelatorio = Sessao::read('filtroRelatorio');

//Captura dados dos filtro
$stTipoFiltro       = $arSessaoFiltroRelatorio['stRdoOpcoes'];
$inRegistro         = $arSessaoFiltroRelatorio['inContrato'];
$inCodGrupo         = $arSessaoFiltroRelatorio['inCodGrupo'];
$inCodOrgao         = (is_array($arSessaoFiltroRelatorio['inCodLotacaoSelecionados']))?implode(",",$arSessaoFiltroRelatorio['inCodLotacaoSelecionados']):array();
$inCodLocal         = (is_array($arSessaoFiltroRelatorio['inCodLocalSelecionados']))?implode(",",$arSessaoFiltroRelatorio['inCodLocalSelecionados']):array();
$arDataInicial      = explode("/",$arSessaoFiltroRelatorio['stDataInicial']);
$arDataFinal        = ($arSessaoFiltroRelatorio['stDataInicial']!=$arSessaoFiltroRelatorio['stDataFinal'])?explode("/",$arSessaoFiltroRelatorio['stDataFinal']):array();
$inCodMesInicial    = $arDataInicial[1];
$stExercicioInicial = $arDataInicial[2];
$inCodMesFinal      = $arDataFinal[1];
$stExercicioFinal   = $arDataFinal[2];

$obRRelatorioCVT->setTipoFiltro($stTipoFiltro);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->addRBeneficioConcessaoValeTransporte();
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMesInicial);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setCodMesFinal($inCodMesFinal);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicio($stExercicioInicial);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte->setExercicioFinal($stExercicioFinal);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaOrgao->setCodOrgao($inCodOrgao);
$obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaLocal->setCodLocal($inCodLocal);

if ($stTipoFiltro == 'grupo')
    $obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->setCodGrupo($inCodGrupo);
else
    $obRRelatorioCVT->obRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->setRegistro($inRegistro);

$obRRelatorioCVT->geraRecordSet( $rsConcessao );

Sessao::write('transf5', $rsConcessao);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioConcessaoValeTransporte.php" );
?>

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
 * Página de processamento oculto para o relatório de alteração cadastral
 * Data de Criação   : 13/04/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: OCAlteracaoCadastral.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.25
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMRelatorioAlteracaoCadastral.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "AlteracaoCadastral";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;
$obRCIMRelatorioAlteracaoCadastral = new RCIMRelatorioAlteracaoCadastral;

// SETA ELEMENTOS DO FILTRO
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');
$obRCIMRelatorioAlteracaoCadastral->setCodInicioInscricao    ( $arFiltro['inCodInicioInscricao']       );
$obRCIMRelatorioAlteracaoCadastral->setCodInicioLote         ( $arFiltro['inCodInicioLote']            );
$obRCIMRelatorioAlteracaoCadastral->setCodInicioLocalizacao  ( $arFiltro['inCodInicioLocalizacao']     );
$obRCIMRelatorioAlteracaoCadastral->setCodInicioBairro       ( $arFiltro['inCodInicioBairro']          );
$obRCIMRelatorioAlteracaoCadastral->setCodInicioLogradouro   ( $arFiltro['inCodInicioLogradouro']      );
$obRCIMRelatorioAlteracaoCadastral->setCodTerminoInscricao   ( $arFiltro['inCodTerminoInscricao']      );
$obRCIMRelatorioAlteracaoCadastral->setCodTerminoLote        ( $arFiltro['inCodTerminoLote']           );
$obRCIMRelatorioAlteracaoCadastral->setCodTerminoLocalizacao ( $arFiltro['inCodTerminoLocalizacao']    );
$obRCIMRelatorioAlteracaoCadastral->setCodTerminoBairro      ( $arFiltro['inCodTerminoBairro']         );
$obRCIMRelatorioAlteracaoCadastral->setCodTerminoLogradouro  ( $arFiltro['inCodTerminoLogradouro']     );
$obRCIMRelatorioAlteracaoCadastral->setTipoRelatorio         ( $arFiltro['stTipoRelatorio']            );
$obRCIMRelatorioAlteracaoCadastral->setAtributos             ( array_key_exists('inCodAtributosSelecionados', $arFiltro) ? $arFiltro['inCodAtributosSelecionados'] : '');
$obRCIMRelatorioAlteracaoCadastral->setOrder                 ( $arFiltro['stOrder']                    );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioAlteracaoCadastral->geraRecordSet( $rsAlteracaoCadastral , $arCabecalho );
Sessao::write('rsImoveis'  , $rsAlteracaoCadastral);
Sessao::write('arCabecalho', $arCabecalho);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioAlteracaoCadastral.php" );

?>

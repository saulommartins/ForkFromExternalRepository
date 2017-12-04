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
 * Frame Oculto para relatorio de Cadastro Imobiliario
 * Data de Criação: 28/04/2005

 * @author Analista: Fabio Bertoldi
 * @author Desenvolvedor: Marcelo B. Paulino

 * @ignore

 * $Id: OCCadastroImobiliario.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.23
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMRelatorioCadastroImobiliario.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "CadastroImobiliario";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;
$obRCIMRelatorioCadastroImobiliario = new RCIMRelatorioCadastroImobiliario;

// SETA ELEMENTOS DO FILTRO
$stFiltro = "";
$arFiltro = Sessao::read('filtroRelatorio');

$obRCIMRelatorioCadastroImobiliario->setFiltroEdificacao     ( $arFiltro['stImoEd']                    );
$obRCIMRelatorioCadastroImobiliario->setCodInicioInscricao   ( $arFiltro['inCodInicioInscricao']       );
$obRCIMRelatorioCadastroImobiliario->setCodInicioLote        ( $arFiltro['inCodInicioLote']            );
$obRCIMRelatorioCadastroImobiliario->setCodInicioLocalizacao ( $arFiltro['inCodInicioLocalizacao']     );
$obRCIMRelatorioCadastroImobiliario->setCodInicioBairro      ( $arFiltro['inCodInicioBairro']          );
$obRCIMRelatorioCadastroImobiliario->setCodInicioLogradouro  ( $arFiltro['inCodInicioLogradouro']      );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoInscricao  ( $arFiltro['inCodTerminoInscricao']      );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoLote       ( $arFiltro['inCodTerminoLote']           );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoLocalizacao( $arFiltro['inCodTerminoLocalizacao']    );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoBairro     ( $arFiltro['inCodTerminoBairro']         );
$obRCIMRelatorioCadastroImobiliario->setCodTerminoLogradouro ( $arFiltro['inCodTerminoLogradouro']     );
$obRCIMRelatorioCadastroImobiliario->setTipoRelatorio        ( $arFiltro['stTipoRelatorio']            );
$obRCIMRelatorioCadastroImobiliario->setAtributos            ( array_key_exists('inCodAtributosSelecionados', $arFiltro) ? $arFiltro['inCodAtributosSelecionados'] : '');
$obRCIMRelatorioCadastroImobiliario->setAtributosLote2       ( array_key_exists('inCodAtributosLote2Selecionados', $arFiltro) ? $arFiltro['inCodAtributosLote2Selecionados'] : '');
$obRCIMRelatorioCadastroImobiliario->setAtributosLote3       ( array_key_exists('inCodAtributosLote3Selecionados', $arFiltro) ? $arFiltro['inCodAtributosLote3Selecionados'] : '');
$obRCIMRelatorioCadastroImobiliario->setOrder                ( $arFiltro['stOrder']                    );
$obRCIMRelatorioCadastroImobiliario->setTipoSituacao         ( $arFiltro['stTipoSituacao']             );
$obRCIMRelatorioCadastroImobiliario->setFiltroCGMInicio      ( $arFiltro['inCodProprietarioInicial']   );
$obRCIMRelatorioCadastroImobiliario->setFiltroCGMTermino     ( $arFiltro['inCodProprietarioFinal']     );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioCadastroImobiliario->geraRecordSet( $rsCadastroImobiliario , $arCabecalho );
Sessao::write('rsImoveis',   $rsCadastroImobiliario);
Sessao::write('arCabecalho', $arCabecalho);
Sessao::write('filtroRelatorio', $arFiltro);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCadastroImobiliario.php" );

?>

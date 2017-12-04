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
  * Página de Formulario de Configuração de Metas Fiscais LDO
  * Data de Criação: 22/01/2015

  * @author Analista:      Ane Pereira
  * @author Desenvolvedor: Arthur Cruz

  * @ignore
  * $Id: FMManterConfiguracaoMetasFiscaisLDO.php 61541 2015-02-03 12:04:33Z evandro $
  *
  * $Rev: $
  * $Author: $
  * $Date: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TGO_MAPEAMENTO."TTCMGOMetasFiscaisLDO.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoMetasFiscaisLDO";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$rsTTCMGOMetasFiscaisLDO = new RecordSet();
$obTTCMGOMetasFiscaisLDO = new TTCMGOMetasFiscaisLDO();
$obTTCMGOMetasFiscaisLDO->setDado('exercicio',$request->get('inExercicio'));
$obTTCMGOMetasFiscaisLDO->recuperaValoresMetasFiscaisLDO($rsTTCMGOMetasFiscaisLDO);

if ($rsTTCMGOMetasFiscaisLDO->getNumLinhas() > 0) {  
    $vlCorrenteReceita                  = number_format($rsTTCMGOMetasFiscaisLDO->getCampo('valor_corrente_receita'),2,',','.');
    $vlCorrenteDespesa                  = number_format($rsTTCMGOMetasFiscaisLDO->getCampo('valor_corrente_despesa'),2,',','.');
    $vlCorrenteResultadoPrimario        = number_format($rsTTCMGOMetasFiscaisLDO->getCampo('valor_corrente_resultado_primario'),2,',','.');
    $vlCorrenteResultadoNominal         = number_format($rsTTCMGOMetasFiscaisLDO->getCampo('valor_corrente_resultado_nominal'),2,',','.');
    $vlCorrenteDividaConsolidadaLiquida = number_format($rsTTCMGOMetasFiscaisLDO->getCampo('valor_corrente_divida_consolidada_liquida'),2,',','.');
} else {
    $vlCorrenteReceita           = '0,00';
    $vlCorrenteDespesa           = '0,00';
    $vlCorrenteResultadoPrimario = '0,00';
    $vlCorrenteResultadoNominal  = '0,00';
    $vlCorrenteDividaConsolidadaLiquida = '0,00';
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "stCtrl" );

$obHdnStExercicio = new Hidden();
$obHdnStExercicio->setName('stExercicio');
$obHdnStExercicio->setValue($request->get('inExercicio'));

//****************************************//
//Monta valores Correntes
//****************************************//
$obFlValorCorrenteReceita = new Numerico();
$obFlValorCorrenteReceita->setId        ( 'flValorCorrenteReceita' );
$obFlValorCorrenteReceita->setName      ( 'flValorCorrenteReceita' );
$obFlValorCorrenteReceita->setRotulo    ( 'Valor Corrente da Receita' );
$obFlValorCorrenteReceita->setTitle     ( 'Informar o valor corrente da Meta Fiscal da Receita.' );
$obFlValorCorrenteReceita->setDecimais  ( 2 );
$obFlValorCorrenteReceita->setMaxLength ( 15 );
$obFlValorCorrenteReceita->setSize      ( 17 );
$obFlValorCorrenteReceita->setValue     ( $vlCorrenteReceita );

$obFlValorCorrenteDespesa = new Numerico();
$obFlValorCorrenteDespesa->setId       ( 'flValorCorrenteDespesa' );
$obFlValorCorrenteDespesa->setName     ( 'flValorCorrenteDespesa' );
$obFlValorCorrenteDespesa->setRotulo   ( 'Valor Corrente da Despesa' );
$obFlValorCorrenteDespesa->setTitle    ( 'Informar o valor corrente da Meta Fiscal da Despesa.' );
$obFlValorCorrenteDespesa->setDecimais ( 2 );
$obFlValorCorrenteDespesa->setMaxLength( 15 );
$obFlValorCorrenteDespesa->setSize     ( 17 );
$obFlValorCorrenteDespesa->setValue    ( $vlCorrenteDespesa );

$obFlValorCorrenteResultadoPrimario = new Numerico();
$obFlValorCorrenteResultadoPrimario->setId        ( 'flValorCorrenteResultadoPrimario' );
$obFlValorCorrenteResultadoPrimario->setName      ( 'flValorCorrenteResultadoPrimario' );
$obFlValorCorrenteResultadoPrimario->setRotulo    ( 'Valor Corrente do Resultado Primário' );
$obFlValorCorrenteResultadoPrimario->setTitle     ( 'Valor do resultado primário que correspondente a diferença entre a receita primária e despesa primária.' );
$obFlValorCorrenteResultadoPrimario->setDecimais  ( 2 );
$obFlValorCorrenteResultadoPrimario->setMaxLength ( 15 );
$obFlValorCorrenteResultadoPrimario->setSize      ( 17 );
$obFlValorCorrenteResultadoPrimario->setValue     ($vlCorrenteResultadoPrimario );

$obFlValorCorrenteResultadoNominal = new Numerico();
$obFlValorCorrenteResultadoNominal->setId        ( 'flValorCorrenteResultadoNominal' );
$obFlValorCorrenteResultadoNominal->setName      ( 'flValorCorrenteResultadoNominal' );
$obFlValorCorrenteResultadoNominal->setRotulo    ( 'Valor Corrente do Resultado Nominal' );
$obFlValorCorrenteResultadoNominal->setTitle     ( 'Informar o valor corrente da Meta Fiscal esperada para o Resultado Nominal.' );
$obFlValorCorrenteResultadoNominal->setDecimais  ( 2 );
$obFlValorCorrenteResultadoNominal->setMaxLength ( 15 );
$obFlValorCorrenteResultadoNominal->setSize      ( 17 );
$obFlValorCorrenteResultadoNominal->setValue     ( $vlCorrenteResultadoNominal );

$obFlValorCorrenteDividaConsolidadaLiquida = new Numerico();
$obFlValorCorrenteDividaConsolidadaLiquida->setId        ( 'flValorCorrenteDividaConsolidadaLiquida' );
$obFlValorCorrenteDividaConsolidadaLiquida->setName      ( 'flValorCorrenteDividaConsolidadaLiquida' );
$obFlValorCorrenteDividaConsolidadaLiquida->setRotulo    ( 'Valor Corrente da Dívida Consolidada Líquida' );
$obFlValorCorrenteDividaConsolidadaLiquida->setTitle     ( 'Informar o valor corrente da Meta Fiscal para a Dívida Consolidada Líquida do exercício.' );
$obFlValorCorrenteDividaConsolidadaLiquida->setDecimais  ( 2 );
$obFlValorCorrenteDividaConsolidadaLiquida->setMaxLength ( 15 );
$obFlValorCorrenteDividaConsolidadaLiquida->setSize      ( 17 );
$obFlValorCorrenteDividaConsolidadaLiquida->setValue     ( $vlCorrenteDividaConsolidadaLiquida );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm         ( $obForm );
$obFormulario->addHidden       ( $obHdnCtrl );
$obFormulario->addHidden       ( $obHdnAcao );
$obFormulario->addHidden       ( $obHdnStExercicio );
$obFormulario->setLarguraRotulo( 30 );
$obFormulario->setLarguraCampo ( 70 );
$obFormulario->addTitulo       ( "Detalhamento das Metas Fiscais (Valor Corrente)" );
$obFormulario->addComponente   ( $obFlValorCorrenteReceita );
$obFormulario->addComponente   ( $obFlValorCorrenteDespesa );
$obFormulario->addComponente   ( $obFlValorCorrenteResultadoPrimario );
$obFormulario->addComponente   ( $obFlValorCorrenteResultadoNominal );
$obFormulario->addComponente   ( $obFlValorCorrenteDividaConsolidadaLiquida );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
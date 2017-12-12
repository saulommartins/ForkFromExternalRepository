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
  * Página de Formulario para EXECUTAR CALCULOS	 - MODULO ARRECADACAO
  * Data de criação : 01/06/2005

    * @author Analista: Fabio Bertold Rodrigues
    * @author Programador: Lucas Teixeira Stephanou

    * $Id: FMExecutarCalculoGrupo.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.03.05
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
include_once ( CAM_GT_ARR_COMPONENTES."MontaGrupoCredito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCalculos";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PRManterCalculo.php";
$pgOcul          = "OCManterCalculo.php";
$pgJs            = "JSManterCalculo.js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

Sessao::write('link', "" );
Sessao::write('parcelas', array() );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCodModulo = new Hidden;
$obHdnCodModulo->setName  ( "inCodModulo" );
$obHdnCodModulo->setID  ( "inCodModulo" );
$obHdnCodModulo->setValue ( $_REQUEST["inCodModulo"] );

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName  ( "inExercicio" );
$obHdnExercicio->setID  ( "inExercicio" );
$obHdnExercicio->setValue ( $_REQUEST["inExercicio"] );

// DEFINE OBJETOS DO FORMULARIO
$obMontaGrupoCredito = new MontaGrupoCredito;
$obMontaGrupoCredito->obBscCodigoCredito->obCampoCod->obEvento->setOnBlur("validarGrupoCredito(this);");

$obRdbGeral = new Radio;
$obRdbGeral->setRotulo     ( "Tipo de Cálculo"                                                        );
$obRdbGeral->setName       ( "stTipoCalculo"                                                          );
$obRdbGeral->setId         ( "stTipoCalculo"                                                          );
$obRdbGeral->setLabel      ( "Geral"                                                                  );
//$obRdbGeral->setDisabled   ( true                                                                     );
$obRdbGeral->setValue      ( "geral"                                                                  );
$obRdbGeral->setTitle      ( "Tipo de cálculo a ser efetuado."                                        );
$obRdbGeral->setNull       ( false                                                                    );
$obRdbGeral->obEvento->setOnChange	( "buscaValor('mudaTipoCalculo')"                                 );

$obRdbParcial = new Radio;
$obRdbParcial->setRotulo   ( "Tipo de Cálculo"                                                       );
$obRdbParcial->setName     ( "stTipoCalculo"                                                         );
$obRdbParcial->setId       ( "stTipoCalculo"                                                         );
//$obRdbParcial->setDisabled ( true                                                                  );
$obRdbParcial->setLabel    ( "Parcial"                                                               );
$obRdbParcial->setValue    ( "parcial"                                                               );
$obRdbParcial->setNull     ( false                                                                   );
$obRdbParcial->obEvento->setOnChange	( "buscaValor('mudaTipoCalculo')"                            );

$obRdbIndividual = new Radio;
$obRdbIndividual->setRotulo   ( "Tipo de Cálculo"                                                        );
$obRdbIndividual->setName     ( "stTipoCalculo"                                                          );
$obRdbIndividual->setId       ( "stTipoCalculo"                                                          );
//$obRdbIndividual->setDisabled ( true                                                                   );
$obRdbIndividual->setLabel    ( "Individual"                                                             );
$obRdbIndividual->setValue    ( "individual"                                                             );
$obRdbIndividual->setNull     ( false                                                                    );
$obRdbIndividual->obEvento->setOnChange	( "buscaValor('mudaTipoCalculo')"                                );

// span para filtros
$obSpnFiltros = new Span;
$obSpnFiltros->setId( "spnFiltros");
$obSpnFiltros->setValue( "");

$obSpnEmissao = new Span;
$obSpnEmissao->setId( "spnEmissao");
$obSpnEmissao->setValue( "");

$obSpnInfosAdicionais = new Span;
$obSpnInfosAdicionais->setId( "spnInfosAdicionais");
$obSpnInfosAdicionais->setValue( "");

$obSpnModelo = new Span;
$obSpnModelo->setId( "spnModelo");
$obSpnModelo->setValue( "");

$obSpnModoParcelamento = new Span;
$obSpnModoParcelamento->setId( "spnModoParcelamento");
$obSpnModoParcelamento->setValue( "");

$obSpnParcelas = new Span;
$obSpnParcelas->setId( "spnParcelas");
$obSpnParcelas->setValue( "");

$obHdnNumeroDomicilio = new Hidden;
$obHdnNumeroDomicilio->setName  ( "stNumeroDomicilio" );
$obHdnNumeroDomicilio->setId ( "stNumeroDomicilio" );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction           ( $pgProc           );
$obForm->setTarget           ( "oculto"          );
//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm    ( $obForm                  );
$obFormulario->addHidden ( $obHdnCtrl               );
$obFormulario->addHidden ( $obHdnAcao               );
$obFormulario->addHidden ( $obHdnCodModulo          );
$obFormulario->addHidden ( $obHdnExercicio );
$obFormulario->addHidden ( $obHdnNumeroDomicilio );
$obFormulario->addTitulo    ( "Dados para Cálculo"  	);
//$obFormulario->addComponente ( $obBscGrupoCredito       );
$obMontaGrupoCredito->geraFormulario( $obFormulario, true, true );

$obFormulario->agrupaComponentes     ( array( $obRdbGeral, $obRdbParcial, $obRdbIndividual) );
$obFormulario->addSpan ( $obSpnFiltros            );
$obFormulario->addSpan ( $obSpnModoParcelamento );
$obFormulario->addSpan ( $obSpnParcelas );
$obFormulario->addSpan ( $obSpnInfosAdicionais );
$obFormulario->addSpan ( $obSpnEmissao           );
$obFormulario->addSpan ( $obSpnModelo           );

$obFormulario->Ok();
$obFormulario->show();

Sessao::write('calculados', -1 );
sistemaLegado::executaFrameOculto("buscaValor('emissao');");

?>

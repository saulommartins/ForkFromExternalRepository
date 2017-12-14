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
    * Página de filtro
    * Data de Criação: 24/10/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    * Casos de uso: uc-03.03.11

    $Id: FLMovimentacaoRequisicao.php 32939 2008-09-03 21:14:50Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_ALM_COMPONENTES."IPopUpMarca.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php");
include_once( CAM_GP_ALM_COMPONENTES."IPopUpCentroCusto.class.php");
include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoAlmoxarife.class.php");
include_once( CAM_GP_ALM_COMPONENTES."ISelectMultiploAlmoxarifadoAlmoxarife.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "EstornoEntrada";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('link' , '');

$arAlmoxarifadoPadrao = array();
$rsRelacionados = new RecordSet;

$obRegra = new RAlmoxarifadoAlmoxarife();
$obRegra->obRCGMAlmoxarife->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRegra->listarPermissao ( $rsDisponiveis, '', false);
$obRegra->consultar();

$inCodAlmoxarifadoPadrao = $obRegra->obAlmoxarifadoPadrao->getCodigo();
$stNomAlmoxarifadoPadrao = $obRegra->obAlmoxarifadoPadrao->obRCGMAlmoxarifado->getNomCGM();
if ($inCodAlmoxarifadoPadrao) {
   $arAlmoxarifadoPadrao[0]['codigo'] = $inCodAlmoxarifadoPadrao;
   $arAlmoxarifadoPadrao[0]['nom_a'] = $stNomAlmoxarifadoPadrao;
   $rsRelacionados->preenche($arAlmoxarifadoPadrao);

}

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setRotulo        ( "Exercício"                      );
$obTxtExercicio->setTitle         ( "Informe o exercício." );
$obTxtExercicio->setName          ( "stExercicio"                );
$obTxtExercicio->setId            ( "stExercicio"                );
$obTxtExercicio->setValue         ( Sessao::getExercicio()           );
$obTxtExercicio->setSize          ( 4                            );
$obTxtExercicio->setMaxLength     ( 4                            );
$obTxtExercicio->setInteiro       ( true                         );

$obSelectAlmoxarifado = new ISelectMultiploAlmoxarifadoAlmoxarife;

$obLblA = new Label;
$obLblA->setValue( "a" );

$obNroEntradaInicial = new TextBox;
$obNroEntradaInicial->setName     ( "inNroEntradaInicial"          );
$obNroEntradaInicial->setValue    ( $inNroEntradaInicial           );
$obNroEntradaInicial->setRotulo   ( "Número da Entrada"            );
$obNroEntradaInicial->setTitle    ( "Informe o número da entrada." );
$obNroEntradaInicial->setInteiro  ( true                           );
$obNroEntradaInicial->setNull     ( true                           );

$obNroEntradaFinal = new TextBox;
$obNroEntradaFinal->setName       ( "inNroEntradaFinal"            );
$obNroEntradaFinal->setValue      ( $inNroEntradaFinal             );
$obNroEntradaFinal->setRotulo     ( "Número da Entrada"            );
$obNroEntradaFinal->setInteiro    ( true                           );
$obNroEntradaFinal->setNull       ( true                           );

$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

$obTxtCodOrdemInicial = new TextBox;
$obTxtCodOrdemInicial->setName     ( "inCodOrdemInicial" );
$obTxtCodOrdemInicial->setValue    ( $inCodOrdemInicial  );
$obTxtCodOrdemInicial->setRotulo   ( "Número da Ordem"   );
$obTxtCodOrdemInicial->setTitle    ( "Informe o número da Ordem." );
$obTxtCodOrdemInicial->setInteiro  ( true                  );
$obTxtCodOrdemInicial->setNull     ( true                  );

$obTxtCodOrdemFinal = new TextBox;
$obTxtCodOrdemFinal->setName     ( "inCodOrdemFinal" );
$obTxtCodOrdemFinal->setValue    ( $inCodOrdemFinal  );
$obTxtCodOrdemFinal->setRotulo   ( "Número da Ordem" );
$obTxtCodOrdemFinal->setInteiro  ( true                );
$obTxtCodOrdemFinal->setNull     ( true                );

$obBscItem = new IPopUpItem($obForm);
$obBscItem->setNull(true);

$obBscMarca = new IPopUpMarca($obForm);
$obBscMarca->setTitle("Informe a marca do item.");
$obBscMarca->setNull(true);

$obBscCentroCusto = new IPopUpCentroCusto($obForm);
$obBscCentroCusto->setNull(true);

$obPerDataLancamento = new Periodicidade();
$obPerDataLancamento->setRotulo ( "Periodicidade do Lançamento" );
$obPerDataLancamento->setTitle  ( "Informe a Periodicidade." );
$obPerDataLancamento->setName   ( "dtLancamento"                 );
$obPerDataLancamento->setExercicio( Sessao::getExercicio() );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda("UC-03.03.11");
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para filtro"           );
$obFormulario->addComponente    ( $obTxtExercicio               );
$obFormulario->addComponente    ( $obSelectAlmoxarifado         );
$obFormulario->addComponente    ( $obPerDataLancamento          );
$obFormulario->agrupaComponentes( array( $obNroEntradaInicial, $obLblA, $obNroEntradaFinal       ) );
$obFormulario->agrupaComponentes( array( $obTxtCodEmpenhoInicial, $obLblA, $obTxtCodEmpenhoFinal ) );
$obFormulario->agrupaComponentes( array( $obTxtCodOrdemInicial, $obLblA, $obTxtCodOrdemFinal     ) );
$obFormulario->addComponente    ( $obBscItem                    );
$obFormulario->addComponente    ( $obBscMarca                   );
$obFormulario->addComponente    ( $obBscCentroCusto             );
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

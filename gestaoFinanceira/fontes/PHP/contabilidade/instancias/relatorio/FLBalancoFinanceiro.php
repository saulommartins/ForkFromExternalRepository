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
    * Página de Filtro para relatorico de Fluxo de Caixa
    * Data de Criação   : 01/08/2013
    * @author Analista: Valtair
    * @author Desenvolvedor: Evandro Melos
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "BalancoFinanceiro";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgGera     = "OCGeraRelatorio".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName            		( "stAcao"  	          );
$obHdnAcao->setValue                ( $stAcao                 );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                 ( "stCtrl"                );
$obHdnCtrl->setValue                ( $stCtrl                 );

$obHdnEval =  new HiddenEval;
$obHdnEval->setName                 ( "stEval"                );
$obHdnEval->setId                   ( "stEval"                );
$obHdnEval->setValue                ( $stEval                 );

$obForm = new Form;

//Montra Select Multiplo Entidades
include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php");
$obEntidadeUsuario = new ISelectMultiploEntidadeUsuario( $obForm );

//Monta Incluir Assinaturas
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obEntidadeUsuario );

// define objeto Periodicidade
$obPeriodo = new Periodicidade;
$obPeriodo->setExercicio   			( Sessao::getExercicio());
$obPeriodo->setNull        			( false 				);
$obPeriodo->setValidaExercicio 		( true 					);
$obPeriodo->setValue          		( 4 					);

//Situacao
$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setValue               ( $inSituacao                    );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "1", "Empenhadas"              );
$obCmbSituacao->addOption              ( "2", "Liquidadas"              );
$obCmbSituacao->addOption              ( "3", "Pagas"                   );
$obCmbSituacao->setNull                ( false 							);
//$obCmbSituacao->obEvento->setOnChange  ( "buscaValor('mostraSpanContaBanco');" );

//Monta FORMULARIO
$obForm->setAction 					( $pgGera 				);
$obForm->setTarget 					( "telaPrincipal" 		);

$obFormulario = new Formulario;
$obFormulario->addForm 				( $obForm 				);
$obFormulario->addHidden            ( $obHdnAcao            );
$obFormulario->addHidden            ( $obHdnCtrl            );
$obFormulario->addHidden            ( $obHdnEval,true       );
$obFormulario->addTitulo 			( "Dados para Filtro" 	);
$obFormulario->addComponente 		( $obEntidadeUsuario 	);
$obFormulario->addComponente 		( $obPeriodo 			);
$obFormulario->addComponente 		( $obCmbSituacao		);

$obMontaAssinaturas->geraFormulario ( $obFormulario );

$obFormulario->OK();
$obFormulario->show();
?>

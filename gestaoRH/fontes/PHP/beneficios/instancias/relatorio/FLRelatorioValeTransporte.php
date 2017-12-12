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
* Página filtro para relatório de Vale-Transporte
* Data de Criação   : 13/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php" );

include_once 'JSRelatorioValeTransporte.js';

// include_once("JSRelatorioValeTransporte.js");

$obRBeneficioFornecedorValeTransporte  = new RBeneficioFornecedorValeTransporte;

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GRH_BEN_INSTANCIAS."relatorio/OCRelatorioValeTransporte.php" );

//Fornecedor de vale-transporte
$obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporteRelatorio( $rsFornecedores );
$obCmbFornecedor = new Select;
$obCmbFornecedor->setName       ( "inCodFornecedor"      );
$obCmbFornecedor->setValue      ( $inCodFornecedor       );
$obCmbFornecedor->setRotulo     ( "Fornecedor"           );
$obCmbFornecedor->setNull       ( false                  );
$obCmbFornecedor->addOption     ( "", "Selecione"        );
$obCmbFornecedor->setCampoId    ( "numcgm"               );
$obCmbFornecedor->setCampoDesc  ( "nom_cgm"              );
$obCmbFornecedor->addOption     ( "0", "Todos"           );
$obCmbFornecedor->preencheCombo ( $rsFornecedores        );
$obCmbFornecedor->setStyle      ( "width: 200px"         );

$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );

$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo              ( "Ordem"               );
$obCmbOrdem->setName                ( "inCodOrdem"          );
$obCmbOrdem->setStyle               ( "width: 200px"        );
$obCmbOrdem->addOption              ( "", "Selecione"       );
$obCmbOrdem->addOption              ( "1", "Data"           );
$obCmbOrdem->addOption              ( "2", "Nome do Fornecedor" );

//FORM
$obFormulario = new Formulario;

$obFormulario->addForm               ( $obForm                 );
$obFormulario->addHidden             ( $obHdnCaminho           );

$obFormulario->addTitulo             ( "Filtro para Impressão" );
$obFormulario->addComponente         ( $obCmbFornecedor        );
$obFormulario->addComponente         ( $obDtPeriodicidade      );
$obFormulario->addComponente         ( $obCmbOrdem             );

$obLimparForm = new Button;
$obLimparForm->setName                    ( "btnLimparForm"            );
$obLimparForm->setValue                   ( "Limpar"                   );
$obLimparForm->setTipo                    ( "button"                   );
$obLimparForm->obEvento->setOnClick       ( "limparPagina()" );
$obLimparForm->setDisabled                ( false                      );

$obBtnOK = new Ok;
$botoesForm = array ( $obBtnOK , $obLimparForm );

$obFormulario->defineBarra($botoesForm);

$obFormulario->show();

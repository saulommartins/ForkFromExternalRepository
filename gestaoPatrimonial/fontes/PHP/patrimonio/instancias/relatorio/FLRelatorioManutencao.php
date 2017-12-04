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
  * Página de Filtro para emissão do relatório de Manutenção
  * Data de criação : 22/07/2008

  * @author Desenvolvedor: Diogo Zarpelon

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioManutencao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

// Inclui os controles em JS da tela.
include($pgJS);

$obForm = new Form;
$obForm->setAction( $pgOcul );

$obISelectEntidade = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidade->obTextBox->setNull(false);
$obISelectEntidade->setNull(false);

// Constroi o objeto de Periodicidade.
$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio           ( Sessao::getExercicio() );
$obPeriodicidade->setValue               ( 4                  );
$obPeriodicidade->obDataInicial->setName ( "stDtInicial"      );
$obPeriodicidade->obDataFinal->setName   ( "stDtFinal"        );
$obPeriodicidade->setNull                ( false              );

// Define o objeto Select para setar a ordenação do relatório.
$obCmbSituacao = new Select();
$obCmbSituacao->setName     ( "stSituacao"                          );
$obCmbSituacao->setRotulo   ( "Situação"                            );
$obCmbSituacao->setStyle    ( "width: 150px;"                       );
$obCmbSituacao->setTitle    ( "Selecione a ordenação do relatório." );
$obCmbSituacao->addOption   ( "", "Todas"                           );
$obCmbSituacao->addOption   ( "realizada", "Realizadas"             );
$obCmbSituacao->addOption   ( "nao_realizada", "Não Realizadas"     );

// Define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm          );
$obFormulario->addTitulo     ( "Filtros para Relatório de Manutenção");
$obFormulario->addComponente    ( $obISelectEntidade    );
$obFormulario->addComponente ( $obPeriodicidade );
$obFormulario->addComponente ( $obCmbSituacao   );

$obFormulario->OK();
$obFormulario->show();

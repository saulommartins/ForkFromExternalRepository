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
  * Página de filtro do CID
  * Data de Criação: 04/01/2006

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

  * @ignore

  * Casos de uso: uc-03.03.11

  $Id: FLMovimentacaoRequisicao.php 32939 2008-09-03 21:14:50Z domluc $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_COMPONENTES."IPopUpVeiculo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "SaidaAutorizacaoAbastecimento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

//Define a função do arquivo, ex: excluir ou alterar
$stAcao = $request->get('stAcao');

Sessao::remove('filtro');
Sessao::write('link'  , '');
Sessao::write('saida' , new RecordSet() );

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
$obTxtExercicio->setRotulo    ( "Exercício"            );
$obTxtExercicio->setTitle     ( "Informe o exercício." );
$obTxtExercicio->setName      ( "stExercicio"          );
$obTxtExercicio->setId        ( "stExercicio"          );
$obTxtExercicio->setSize      ( 4                      );
$obTxtExercicio->setMaxLength ( 4                      );
$obTxtExercicio->setInteiro   ( true                   );
$obTxtExercicio->setValue     ( Sessao::getExercicio() );

$obTxtCodAutorizacao = new TextBox;
$obTxtCodAutorizacao->setName     ( "inCodAutorizacao" );
$obTxtCodAutorizacao->setRotulo   ( "Número da Autorização"   );
$obTxtCodAutorizacao->setTitle    ( "Informe o número da autorização." );
$obTxtCodAutorizacao->setInteiro  ( true                  );
$obTxtCodAutorizacao->setNull     ( true                  );

$obBscVeiculo = new IPopUpVeiculo($obForm);
$obBscVeiculo->setNull(true);

$obPerDataAutorizacao = new Periodicidade();
$obPerDataAutorizacao->setRotulo ( "Data de Autorização" );
$obPerDataAutorizacao->setTitle  ( "Informe a data de autorização." );
$obPerDataAutorizacao->setName   ( "dtAutorizacao"                 );
$obPerDataAutorizacao->setExercicio( Sessao::getExercicio() );

//Monta FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setAjuda("UC-03.03.11");
$obFormulario->addHidden        ( $obHdnCtrl                    );
$obFormulario->addHidden        ( $obHdnAcao                    );
$obFormulario->addTitulo        ( "Dados para filtro"           );
$obFormulario->addComponente    ( $obTxtExercicio               );
$obFormulario->addComponente    ( $obTxtCodAutorizacao          );
$obFormulario->addComponente    ( $obPerDataAutorizacao         );
$obFormulario->addComponente    ( $obBscVeiculo                 );
$obFormulario->OK();

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

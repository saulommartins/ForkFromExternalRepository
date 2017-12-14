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
/**
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 03/07/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore

  $Id: FMManterRegistroPrecoEmpenho.php 59612 2014-09-02 12:00:51Z gelson $
  $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
  $Author: gelson $
  $Rev: 59612 $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"              );
$obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()            );
$obTxtExercicioEmpenho->setRotulo   ( "Exercício do Empenho"            );
$obTxtExercicioEmpenho->setTitle    ( "Informe o exercício do empenho." );
$obTxtExercicioEmpenho->setInteiro  ( false                             );
$obTxtExercicioEmpenho->setNull     ( false                             );
$obTxtExercicioEmpenho->setMaxLength( 4                                 );
$obTxtExercicioEmpenho->setSize     ( 5                                 );


$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle            ( "Informe o número do empenho."  );
$obBscEmpenho->setRotulo           ( "**Número do Empenho"           );
$obBscEmpenho->setId               ( "stEmpenho"                     );
$obBscEmpenho->setValue            ( $_REQUEST['stEmpenho']          );
$obBscEmpenho->setMostrarDescricao ( true                            );
$obBscEmpenho->obCampoCod->setName ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setId   ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setValue(  $numEmpenho                    );
$obBscEmpenho->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho');" );
$obBscEmpenho->setFuncaoBusca("if( document.frm.inCodEntidade.value != '' ) { abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLProcurarEmpenho.php','frm','numEmpenho','stEmpenho','&inCodigoEntidade='+document.frm.inCodEntidade.value + '&stCampoExercicio=stExercicioEmpenho&registroPrecos=true&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');} else { alertaAviso('Preencher o campo Entidade.','form','aviso','".Sessao::getId()."');}");


$obBtnIncluirEmpenho = new Button;
$obBtnIncluirEmpenho->setName             ( "btnIncluirEmpenho"             );
$obBtnIncluirEmpenho->setId               ( "btnIncluirEmpenho"             );
$obBtnIncluirEmpenho->setValue            ( "Incluir"                       );
$obBtnIncluirEmpenho->obEvento->setOnClick( "montaParametrosGET('incluirEmpenho');" );
$obBtnIncluirEmpenho->setTitle            ( "Clique para incluir o empenho na lista." );

$obSpnEmpenhos = new Span();
$obSpnEmpenhos->setId("spnEmpenhos");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
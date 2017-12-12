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

    * Página de Filtro de Configuracao REGLIC
    * Data de Criação   : 2603/2014

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal

    * @ignore
    *
    * $Id: FLManterConfiguracaoREGLIC.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: $
    * $Author: $
    * $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );


//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoREGLIC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRegra = new RContabilidadeLancamentoValor;
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade, "E.numcgm" );

    
//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgForm );
$obForm->setTarget( "telaPrincipal" );

    //Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "inCodEntidade"         );
$obHdnEntidade->setValue( $_GET['inCodEntidade']  );

//Define o objeto COMBO para Entidade
$obCmbEntidade = new Select;
$obCmbEntidade->setName      ( "inCodEntidade" );
$obCmbEntidade->setRotulo    ( "Entidade" );

// Caso o usuário tenha permissão para mais de uma entidade, exibe o selecionar.
// Se tiver apenas uma, evita o addOption forçando a primeira e única opção ser selecionada.
if ($rsEntidade->getNumLinhas()>1) {
    $obCmbEntidade->addOption    ( "", "Selecione" );
}

$obCmbEntidade->setCampoId   ( "[cod_entidade]" );
$obCmbEntidade->setCampoDesc ( "[cod_entidade] - [nom_cgm]" );
$obCmbEntidade->preencheCombo( $rsEntidade );
$obCmbEntidade->setNull      ( false );
$obCmbEntidade->setTitle     ( 'Selecione uma Entidade' );


//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para lançamento de saldos iniciais"  );
$obFormulario->addComponente($obCmbEntidade);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

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
 * Filtro de Macro Objetivos
 * Data de Criação   : 06/05/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE."validaGF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_COMPONENTES . 'ITextBoxSelectPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterMacroobjetivos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if (empty($stAcao)) {
    $stAcao = "excluir";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

// Define componente seletor de PPA
$obITextBoxSelectPPA = new ITextBoxSelectPPA();
$obITextBoxSelectPPA->setNull(true);

//Define o objeto TEXT para armazenar a descricao do macro objetivo
$obTxtCodigoMacro = new TextBox;
$obTxtCodigoMacro->setName     ( "inCodMacro" );
$obTxtCodigoMacro->setRotulo   ( "Código Macro" );
$obTxtCodigoMacro->setSize     ( 10 );
$obTxtCodigoMacro->setNull     ( true );
$obTxtCodigoMacro->setTitle    ( 'Informe o código do macro objetivo.' );

//Define o objeto TEXT para armazenar a descricao do macro objetivo
$obTxtDescricaoMacro = new TextBox;
$obTxtDescricaoMacro->setName     ( "stDescricao" );
$obTxtDescricaoMacro->setRotulo   ( "Descrição" );
$obTxtDescricaoMacro->setSize     ( 80 );
$obTxtDescricaoMacro->setMaxLength( 80 );
$obTxtDescricaoMacro->setNull     ( true );
$obTxtDescricaoMacro->setTitle    ( 'Informe a descrição do macro objetivo.' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addTitulo    ("Dados para Filtro");
$obFormulario->addComponente($obITextBoxSelectPPA);
$obFormulario->addComponente($obTxtCodigoMacro);
$obFormulario->addComponente($obTxtDescricaoMacro);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

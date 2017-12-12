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
    * Interface de processamento da Subfunção Orçamentátia
    * Subfunções orçamentárias que fazem parte da classificação funcional-programática da despesa
    * Data de Criação   : 14/07/2004

    * @author Analista: Jorge B.
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-12-07 15:07:32 -0200 (Sex, 07 Dez 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.8  2007/05/21 18:56:01  melo
Bug #9229#

Revision 1.7  2006/07/14 18:11:19  leandro.zis
Bug #6376#

Revision 1.6  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"   );
include_once(CAM_GF_ORC_NEGOCIO."ROrcamentoSubfuncao.class.php"      );
/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "SubFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

/**
    * Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
*/
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

/**
    * Instância o OBJETO da regra de negócios ROrcamentoSubfuncao
*/
$obROrcamentoSubfuncao      = new ROrcamentoSubfuncao;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

// Consulta a configuração para selecionar o GRUPO F
$obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
$obRConfiguracaoOrcamento->consultarConfiguracao();
$stMascara = $obRConfiguracaoOrcamento->getMascDespesa();
$arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

// Grupo F;
$stMascara = $arMarcara[3];

//************************************************/
// Define componentes do FORMULARIO
//***********************************************/
/**
    * Instância o formulário
*/
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

/**
    * Define objeto HIDDEN para armazenar a ACAO do formulário
*/
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

/**
    * Define o objeto HIDDEN para armazenar variável de controle (stCtrl)
*/
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

/**
    * Define o objeto da MASCARA CODIGO DA Subfunção
*/
$obHdnMascara = new Hidden;
$obHdnMascara->setName  ( "stMascara" );
$obHdnMascara->setValue ( $stMascara  );

/**
    * Define o objeto TEXTBOX código da função
*/
$obTxtCodSubFuncao = new TextBox;
$obTxtCodSubFuncao->setName ( "inCodigoSubFuncao" );
$obTxtCodSubFuncao->setValue( $inCodigoSubFuncao );
$obTxtCodSubFuncao->setRotulo( "Código" );
$obTxtCodSubFuncao->setTitle ( "Informe o código." );
$obTxtCodSubFuncao->setSize( strlen($stMascara) );
$obTxtCodSubFuncao->setNull( true );
$obTxtCodSubFuncao->setMaxLength( strlen($stMascara) );

/**
    * Define o objeto TEXTBOX descrição
*/
$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao" );
$obTxtDescricao->setValue     ( $stDescricao );
$obTxtDescricao->setRotulo    ( "Descrição" );
$obTxtDescricao->setTitle     ( "Informe a descrição." );
$obTxtDescricao->setSize      ( 80 );
$obTxtDescricao->setMaxLength ( 80 );

/**
    * Define o objeto HIDDEN para controle de paginação
*/
$obHdnPg = new Hidden;
$obHdnPg->setName  ( "pg" );
$obHdnPg->setValue ( $_GET["pg"] );

/**
    * Define o objeto HIDDEN para controle de paginação
*/
$obHdnPos = new Hidden;
$obHdnPos->setName  ( "pos" );
$obHdnPos->setValue ( $_GET["pos"] );

/**
    * Monta o formulário
*/
$obFormulario = new Formulario;

// Form
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda( "UC-02.01.03"       );

// Hiddens
$obFormulario->addHidden        ( $obHdnCtrl                );
$obFormulario->addHidden        ( $obHdnAcao                );
$obFormulario->addHidden        ( $obHdnMascara             );
$obFormulario->addHidden        ( $obHdnPg                  );
$obFormulario->addHidden        ( $obHdnPos                 );

// Titulo Principal
$obFormulario->addTitulo( "Dados para Filtro" );

// Componentes
$obFormulario->addComponente    ( $obTxtCodSubFuncao );
$obFormulario->addComponente    ( $obTxtDescricao );

$obFormulario->OK();
$obFormulario->show();
?>

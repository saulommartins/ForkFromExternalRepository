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
    * Página Formulário - Parâmetros do Arquivo
    * Data de Criação   : 29/01/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

SistemaLegado::BloqueiaFrames();

//Define o nome dos arquivos PHP
$stPrograma = "ManterUnidadeOrcamentaria";
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

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Cria a combo onde as entidades que já foram configuradas no 'Manter Unidade Gestora'
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado      ( 'exercicio', Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaDadosEntidadesConfiguradas( $rsOrcamentoEntidade );

$obCboEntidade = new  Select();
$obCboEntidade->setRotulo    ( 'Unidade Gestora' );
$obCboEntidade->setTitle     ( 'Órgão para identificação dos níveis.' );
$obCboEntidade->setName      ( 'codEntidade' );
$obCboEntidade->setId        ( 'codEntidade' );
$obCboEntidade->setValue     ( '' );
$obCboEntidade->setCampoId   ( 'cod_entidade'  );
$obCboEntidade->setCampoDesc ( '[cod_entidade] - [descricao]' );
$obCboEntidade->addOption    ( '', 'Selecione' );
$obCboEntidade->setNull      ( false );
$obCboEntidade->preencheCombo( $rsOrcamentoEntidade );
$obCboEntidade->obEvento->setOnChange("executaFuncaoAjax('montaDados', '&inCodEntidade='+this.value, true)");

/**********************************************
 * Select Multiplo das Unidades Orçamentárias
**********************************************/
$obCbmUnidadesOrcamentarias = new SelectMultiplo();
$obCbmUnidadesOrcamentarias->setName  ( 'arUnidadesOrcamentarias' );
$obCbmUnidadesOrcamentarias->setRotulo( 'Unidades Orçamentárias' );
$obCbmUnidadesOrcamentarias->setNull  ( false );
$obCbmUnidadesOrcamentarias->setTitle ( 'Selecione as Unidades Orçamentárias.' );
// lista de ARQUIVOS disponiveis
$obCbmUnidadesOrcamentarias->SetNomeLista1      ( 'arCodUnidadesOrcamentariasDisponiveis' );
$obCbmUnidadesOrcamentarias->setCampoId1        ( 'codigo' );
$obCbmUnidadesOrcamentarias->setCampoDesc1      ( 'descricao' );
$obCbmUnidadesOrcamentarias->obSelect1->setStyle( 'width: 450px' );
$obCbmUnidadesOrcamentarias->SetRecord1         ( new RecordSet() );

// lista de ARQUIVOS selecionados
$obCbmUnidadesOrcamentarias->SetNomeLista2      ( 'arCodUnidadesOrcamentariasSelecionadas' );
$obCbmUnidadesOrcamentarias->setCampoId2        ( 'codigo' );
$obCbmUnidadesOrcamentarias->setCampoDesc2      ( 'descricao' );
$obCbmUnidadesOrcamentarias->obSelect2->setStyle( 'width: 450px' );
$obCbmUnidadesOrcamentarias->SetRecord2         ( new RecordSet() );

// Cria o campo text para a inclusão do dado da unidade gestora
$obTxtUnidadeOrcamentaria = new TextBox();
$obTxtUnidadeOrcamentaria->setRotulo   ( 'Código do Órgão' );
$obTxtUnidadeOrcamentaria->setTitle    ( 'Informe é código do Órgão da Unidade Orçamentária.' );
$obTxtUnidadeOrcamentaria->setName     ( 'inCodUnidadeGestora' );
$obTxtUnidadeOrcamentaria->setId       ( 'inCodUnidadeGestora' );
$obTxtUnidadeOrcamentaria->setValue    ( '' );
$obTxtUnidadeOrcamentaria->setInteiro  ( true );
$obTxtUnidadeOrcamentaria->setMaxLength( 7 );
$obTxtUnidadeOrcamentaria->setSize     ( 7 );
$obTxtUnidadeOrcamentaria->setNull     ( false );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Dados para Configurar a Unidade Orçamentária" );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addComponente( $obCboEntidade );
$obFormulario->addComponente( $obCbmUnidadesOrcamentarias );
$obFormulario->addComponente( $obTxtUnidadeOrcamentaria );
$obFormulario->defineBarra  ( array( new Ok(true) ) );
$obFormulario->show();

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

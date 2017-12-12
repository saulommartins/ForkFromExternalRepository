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
    * Data de Criação   : 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25762 $
    $Name$
    $Autor: $
    $Date: 2007-10-02 15:20:03 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeGestora.class.php");
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoUnidade.class.php" );
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentaria.class.php");
include_once( CAM_GPC_TCERN_MAPEAMENTO."TTCERNUnidadeOrcamentariaResponsavel.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write("stOrigem","FL");

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

// Busca as unidades orçamentárias (secretarias)
$obTUnidade = new TOrcamentoUnidade();
$obTUnidade->recuperaTodos($rsUnidade," WHERE unidade.exercicio = '".Sessao::getExercicio()."'", " ORDER BY num_unidade,num_orgao");

$obTGestora = new TTCERNUnidadeGestora();
$obTGestora->recuperaRelacionamento($rsGestora);

$obLblMensagem = new Label;
$obLblMensagem->setName   ( "stMensagem" );
$obLblMensagem->setRotulo ( "Mensagem" );
$obLblMensagem->setValue  ( "Você deve primeiro configurar uma unidade gestora em Gestão Prestação de Contas :: TCE - RN :: Configuração :: Configurar Unidade Gestora" );

$obLblGestora = new Label;
$obLblGestora->setName  ( "stGestora" );
$obLblGestora->setId    ( "stGestora" );
$obLblGestora->setRotulo( "Entidade" );
$obLblGestora->setTitle ( "Unidade Gestora" );
$obLblGestora->setValue ( $rsGestora->getCampo('nom_cgm') );

$hdnIdGestora = new Hidden;
$hdnIdGestora->setName  ( "hdnIdGestora" );
$hdnIdGestora->setId    ( "hdnIdGestora" );
$hdnIdGestora->setValue ( $rsGestora->getCampo('id') );

$obCmbUnidade = new Select;
$obCmbUnidade->setName      ( "stUnidade" );
$obCmbUnidade->setId        ( "stUnidade" );
$obCmbUnidade->setRotulo    ( "Unidade" );
$obCmbUnidade->setTitle     ( "Selecione o nome da unidade" );
$obCmbUnidade->setCampoId   ( "[num_unidade]/[num_orgao]" );
$obCmbUnidade->setCampoDesc ( "nom_unidade" );
$obCmbUnidade->addOption    ( "", "Selecione" );
$obCmbUnidade->setNull      ( false );
$obCmbUnidade->preencheCombo( $rsUnidade );

//****************************************//
// Monta FORMULARIO
//****************************************//
$obForm = new Form;
$obForm->setAction( $pgForm );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addTitulo            ( "Filtro para Unidade Orçamentária" );

if ($rsGestora->getCampo('cod_institucional') == '') {
    $obFormulario->addComponente( $obLblMensagem );
}else{
    $obFormulario->addHidden            ( $obHdnAcao );
    $obFormulario->addHidden            ( $obHdnCtrl );
    $obFormulario->addHidden            ( $hdnIdGestora );
    $obFormulario->addComponente        ( $obLblGestora );
    $obFormulario->addComponente        ( $obCmbUnidade );
    $obFormulario->ok();
}

$obFormulario->show();

include_once( $pgJS );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

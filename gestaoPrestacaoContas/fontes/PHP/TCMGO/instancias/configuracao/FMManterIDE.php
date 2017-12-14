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
    * Página de Formulário para configuração
    * Data de Criação   : 29/04/2016
    * @author 
    * @ignore
    *
    * $Id:$

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
include_once TTGO."TTGOConfiguracaoEntidade.class.php";
include_once(TTGO."TCMGOConfiguracaoIDE.class.php");
include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");

$stPrograma = "ManterIDE";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado("exercicio", Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidades, "","",$boTransacao);

$obTCMGOConfiguracaoIDE = new TCMGOConfiguracaoIDE();
$obTCMGOConfiguracaoIDE->recuperaTodos($rsConfiguracaoIde,"","",$boTransacao);

if($rsConfiguracaoIde->getNumLinhas() > 0 ) {
    $stNomChefeGoverno    = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm" , " WHERE numcgm = ".$rsConfiguracaoIde->getCampo('cgm_chefe_governo'));
    $stNomContador        = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm" , " WHERE numcgm = ".$rsConfiguracaoIde->getCampo('cgm_contador'));
    $stNomControleInterno = SistemaLegado::pegaDado("nom_cgm" , "sw_cgm" , " WHERE numcgm = ".$rsConfiguracaoIde->getCampo('cgm_controle_interno'));
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obSelectEntidades = new Select;
$obSelectEntidades->setName       ( "inCodEntidade" );
$obSelectEntidades->setId         ( "inCodEntidade" );
$obSelectEntidades->setRotulo     ( "Entidade Prefeitura" );
$obSelectEntidades->setTitle      ( "Selecione a entidade prefeitura" );
$obSelectEntidades->setCampoId    ( "[cod_entidade]" );
$obSelectEntidades->setCampoDesc  ( "[nom_cgm]" );
$obSelectEntidades->addOption     ( "", "Selecione" );
$obSelectEntidades->setNull       ( false );
$obSelectEntidades->preencheCombo ( $rsEntidades );

$obCGMChefeGoverno = new IPopUpCGMVinculado( $obForm );
$obCGMChefeGoverno->setTabelaVinculo   ( 'sw_cgm_pessoa_fisica' );
$obCGMChefeGoverno->setCampoVinculo    ( 'numcgm' );
$obCGMChefeGoverno->setNomeVinculo     ( 'Chefe Governo' );
$obCGMChefeGoverno->setRotulo          ( 'Chefe de Governo' );
$obCGMChefeGoverno->setName            ( 'stNomCGMChefeGoverno' );
$obCGMChefeGoverno->setId              ( 'stNomCGMChefeGoverno' );
$obCGMChefeGoverno->obCampoCod->setName( 'inCGMChefeGoverno' );
$obCGMChefeGoverno->obCampoCod->setId  ( 'inCGMChefeGoverno' );
$obCGMChefeGoverno->setNull            ( false );
$obCGMChefeGoverno->obCampoCod->setValue ($rsConfiguracaoIde->getCampo('cgm_chefe_governo'));
$obCGMChefeGoverno->setValue ($stNomChefeGoverno);

$obCGMContador = new IPopUpCGMVinculado( $obForm );
$obCGMContador->setTabelaVinculo   ( 'sw_cgm_pessoa_fisica' );
$obCGMContador->setCampoVinculo    ( 'numcgm' );
$obCGMContador->setNomeVinculo     ( 'Contador' );
$obCGMContador->setRotulo          ( 'Contador' );
$obCGMContador->setName            ( 'stNomCGMContador' );
$obCGMContador->setId              ( 'stNomCGMContador' );
$obCGMContador->obCampoCod->setName( 'inCGMContador' );
$obCGMContador->obCampoCod->setId  ( 'inCGMContador' );
$obCGMContador->setNull            ( false );
$obCGMContador->obCampoCod->setValue ($rsConfiguracaoIde->getCampo('cgm_contador'));
$obCGMContador->setValue ($stNomContador);

$obCGMControleInterno = new IPopUpCGMVinculado( $obForm );
$obCGMControleInterno->setTabelaVinculo   ( 'sw_cgm_pessoa_fisica' );
$obCGMControleInterno->setCampoVinculo    ( 'numcgm' );
$obCGMControleInterno->setNomeVinculo     ( 'Controle Interno' );
$obCGMControleInterno->setRotulo          ( 'Controle Interno' );
$obCGMControleInterno->setName            ( 'stNomCGMControleInterno' );
$obCGMControleInterno->setId              ( 'stNomCGMControleInterno' );
$obCGMControleInterno->obCampoCod->setName( 'inCGMControleInterno' );
$obCGMControleInterno->obCampoCod->setId  ( 'inCGMControleInterno' );
$obCGMControleInterno->setNull            ( false );
$obCGMControleInterno->obCampoCod->setValue ($rsConfiguracaoIde->getCampo('cgm_controle_interno'));
$obCGMControleInterno->setValue ($stNomControleInterno);

$obTxtCRCContador = new TextBox();
$obTxtCRCContador->setRotulo( 'CRC' );
$obTxtCRCContador->setName( 'inCRCContador' );
$obTxtCRCContador->setId( 'inCRCContador' );
$obTxtCRCContador->setMaxLength( 11 );
$obTxtCRCContador->setNull( false );
$obTxtCRCContador->setValue( $rsConfiguracaoIde->getCampo('crc_contador') );


$obTUF = new TUF();
$stFiltro = " WHERE cod_pais = 1 ";
$stOrder = " sigla_uf ASC ";
$obTUF->recuperaTodos( $rsUF, $stFiltro, $stOrder );

$obCmbUFContador = new Select;
$obCmbUFContador->setName       ( "inCodUf" );
$obCmbUFContador->setId         ( "inCodUf" );
$obCmbUFContador->setRotulo     ( "UF CRC" );
$obCmbUFContador->setTitle      ( "Selecione o estado do CRC." );
$obCmbUFContador->setNull       ( true  );
$obCmbUFContador->setCampoId    ( "[cod_uf]" );
$obCmbUFContador->setCampoDesc  ( "[sigla_uf]" );
$obCmbUFContador->addOption     ( "", "Selecione" );
$obCmbUFContador->preencheCombo ( $rsUF );
$obCmbUFContador->setNull( false );
$obCmbUFContador->setValue($rsConfiguracaoIde->getCampo('uf_crc_contador'));

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);

$obFormulario->addHidden ($obHdnAcao);
$obFormulario->addHidden ($obHdnCtrl);
$obFormulario->addTitulo ( "Unidade Gestora" );
$obFormulario->addComponente( $obSelectEntidades );
$obFormulario->addComponente( $obCGMChefeGoverno );
$obFormulario->addComponente( $obCGMContador );
$obFormulario->addComponente( $obTxtCRCContador );
$obFormulario->addComponente( $obCmbUFContador );
$obFormulario->addComponente( $obCGMControleInterno );
$obFormulario->OK ();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

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
  * Página de Formulario de Configuração de IDE
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  * $Id: $

  * $Revision: $
  * $Name: $
  * $Author: $
  * $Date: $

  * $Rev$:
  * $Author$:
  * $Date$:
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfigurarIDE.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoIDE";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$rsTTCEMGConfigurarIDE = new RecordSet;
$obTTCEMGConfigurarIDE = new TTCEMGConfigurarIDE();
$obTTCEMGConfigurarIDE->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGConfigurarIDE->recuperaTodos($rsTTCEMGConfigurarIDE);

if ($rsTTCEMGConfigurarIDE->getNumLinhas() > 0) {
    $stAcao = 'alterar';
    $inCodMunicipio = $rsTTCEMGConfigurarIDE->getCampo('cod_municipio');
    $inOpcaoSemestralidade = $rsTTCEMGConfigurarIDE->getCampo('opcao_semestralidade');
} else {
    $stAcao = 'incluir';
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
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
$obHdnCtrl->setId   ( "stCtrl" );

$obTxtCodMunicipio = new TextBox;
$obTxtCodMunicipio->setRotulo( "Código do Município"            );
$obTxtCodMunicipio->setTitle ( "Informe o Código do Município - Conforme tabela disponibilizada pelo TCEMG no Portal SICOM." );
$obTxtCodMunicipio->setName  ( "inCodMunicipio"                 );
$obTxtCodMunicipio->setSize  ( 5                                );
$obTxtCodMunicipio->setMaxLength (5);
$obTxtCodMunicipio->setMascara ('99999');
$obTxtCodMunicipio->setNull (false);
$obTxtCodMunicipio->setValue($inCodMunicipio);

$obRadioOpcaoSemestralidadeSim = new Radio;
$obRadioOpcaoSemestralidadeSim->setRotulo('Opção Semestral de confirmação dos relatórios da LRF');
$obRadioOpcaoSemestralidadeSim->setTitle('Somente os Municípios com população inferior a cinquenta mil habitantes devem preencher este campo.');
$obRadioOpcaoSemestralidadeSim->setName('opcaoSemestralidade');
$obRadioOpcaoSemestralidadeSim->setLabel("Sim");
$obRadioOpcaoSemestralidadeSim->setValue("1");
$obRadioOpcaoSemestralidadeSim->setNull(true);

$obRadioOpcaoSemestralidadeNao = new Radio;
$obRadioOpcaoSemestralidadeNao->setName('opcaoSemestralidade');
$obRadioOpcaoSemestralidadeNao->setLabel("Não");
$obRadioOpcaoSemestralidadeNao->setValue("2");
$obRadioOpcaoSemestralidadeNao->setNull(true);

if ($inOpcaoSemestralidade == 1) {
    $obRadioOpcaoSemestralidadeSim->setChecked(true);
} else {
    $obRadioOpcaoSemestralidadeNao->setChecked(true);
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( "Dados para Configuração de IDE" );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtCodMunicipio );
$obFormulario->agrupaComponentes (array($obRadioOpcaoSemestralidadeSim, $obRadioOpcaoSemestralidadeNao));

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

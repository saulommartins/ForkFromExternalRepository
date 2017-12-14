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
    * Página de Formulario de Ajustes Gerais Exportacao - TCE-RS
    * Data de Criação   : 11/07/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-17 11:32:12 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.08.15
*/

/*
$Log$
Revision 1.1  2006/07/17 14:30:48  cako
Bug #6013#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfiguracaoOrgaoUnidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgaoUnidadeContas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once($pgJS);

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obTTCMGOConfiguracaoOrgaoUnidade = new TTCMGOConfiguracaoOrgaoUnidade;
$obTTCMGOConfiguracaoOrgaoUnidade->recuperaTodos($rsConfOrgaoUnidade, " WHERE exercicio = '".Sessao::getExercicio()."'");

$arOrgaoUnidade = array();

foreach ($rsConfOrgaoUnidade->getElementos() as $index => $valor) {
    switch ($valor['cod_poder']) {
        case 1:
            $arOrgaoUnidade['executivo'] = str_pad($valor['num_orgao'],2,'0',STR_PAD_LEFT).str_pad($valor['num_unidade'],2,'0',STR_PAD_LEFT);
        break;

        case 2:
            $arOrgaoUnidade['legislativo'] = str_pad($valor['num_orgao'],2,'0',STR_PAD_LEFT).str_pad($valor['num_unidade'],2,'0',STR_PAD_LEFT);
        break;

        case 3:
            $arOrgaoUnidade['RPPS'] = str_pad($valor['num_orgao'],2,'0',STR_PAD_LEFT).str_pad($valor['num_unidade'],2,'0',STR_PAD_LEFT);
        break;

        case 4:
            $arOrgaoUnidade['outros'] = str_pad($valor['num_orgao'],2,'0',STR_PAD_LEFT).str_pad($valor['num_unidade'],2,'0',STR_PAD_LEFT);
        break;
    }
}

$obTxtExecutivo = new TextBox;
$obTxtExecutivo->setName        ( "inCodExecutivo" );
$obTxtExecutivo->setId          ( "inCodExecutivo" );
$obTxtExecutivo->setValue       ( $arOrgaoUnidade['executivo'] );
$obTxtExecutivo->setRotulo      ( "Poder Executivo" );
$obTxtExecutivo->setTitle       ( "Informe o código do orgão/unidade relativo ao poder executivo");
$obTxtExecutivo->setInteiro     ( true );
$obTxtExecutivo->setSize        ( 3 );
$obTxtExecutivo->setMaxLength   ( "4" );
   
$obTxtLegislativo = new Textbox;
$obTxtLegislativo->setName        ( "inCodLegislativo" );
$obTxtLegislativo->setId          ( "inCodLegislativo" );
$obTxtLegislativo->setValue       ( $arOrgaoUnidade['legislativo'] );
$obTxtLegislativo->setRotulo      ( "Poder Legislativo" );
$obTxtLegislativo->setTitle       ( "Informe o código do orgão/unidade relativo ao poder legislativo" );
$obTxtLegislativo->setInteiro     ( true );
$obTxtLegislativo->setSize        ( 3 );
$obTxtLegislativo->setMaxLength   ( "4" );

$obTxtRPPS = new Textbox;
$obTxtRPPS->setName        ( "inCodRPPS" );
$obTxtRPPS->setId          ( "inCodRPPS" );
$obTxtRPPS->setValue       ( $arOrgaoUnidade['RPPS'] );
$obTxtRPPS->setRotulo      ( "RPPS" );
$obTxtRPPS->setTitle       ( "Informe o código do orgão/unidade relativo ao RPPS" );
$obTxtRPPS->setInteiro     ( true );
$obTxtRPPS->setSize        ( 3 );
$obTxtRPPS->setMaxLength   ( "4" );

$obTxtOutros = new Textbox;
$obTxtOutros->setName        ( "inCodOutros" );
$obTxtOutros->setId          ( "inCodOutros" );
$obTxtOutros->setValue       ( $arOrgaoUnidade['outros'] );
$obTxtOutros->setRotulo      ( "Outros" );
$obTxtOutros->setTitle       ( "Informe o código do orgão/unidade para outros poderes" );
$obTxtOutros->setInteiro     ( true );
$obTxtOutros->setSize        ( 3 );
$obTxtOutros->setMaxLength   ( "4" );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo    ( "Configuração de Órgão/Unidade" );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addComponente( $obTxtExecutivo );
$obFormulario->addComponente( $obTxtLegislativo );
$obFormulario->addComponente( $obTxtRPPS );
$obFormulario->addComponente( $obTxtOutros );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();


$jsOnLoad .= "executaFuncaoAjax( 'verificaEntidades' );";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
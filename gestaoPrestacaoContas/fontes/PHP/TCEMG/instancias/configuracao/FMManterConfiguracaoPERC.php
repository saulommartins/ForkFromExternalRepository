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
 * Página Formulário - Configuração de Percentual para aquisição de bens e serviços licitáveis
 * Data de Criação   : 16/01/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes

 * @ignore
 *
 * $Id: FMManterConfiguracaoPERC.php 59612 2014-09-02 12:00:51Z gelson $
 *
 * $Revision: 59612 $
 * $Author: gelson $
 * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoPERC.class.php");
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoPERC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao   = $request->get('stAcao');
if ($stAcao == '') {
    $stAcao = 'incluir';
}

include($pgJS);
$rsTTCEMGConfiguracaoPERC = new RecordSet();
$obTTCEMGConfiguracaoPERC = new TTCEMGConfiguracaoPERC();
$obTTCEMGConfiguracaoPERC->setDado('exercicio', Sessao::getExercicio());
$obTTCEMGConfiguracaoPERC->recuperaTodos($rsTTCEMGConfiguracaoPERC,'','', $boTransacao);

if ($rsTTCEMGConfiguracaoPERC->getNumLinhas() > 0) {
    $stPercentualAnual = $rsTTCEMGConfiguracaoPERC->getCampo('planejamento_anual');
    $flPercentualEstabelecido = $rsTTCEMGConfiguracaoPERC->getCampo('porcentual_anual');
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

$obLabelFrase = new Label;
$obLabelFrase->setValue("No planejamento anual, o município comteplou percentual de até 25% das aquisições de bens e serviços licitáveis, visando tratamento diferenciado para as microempresas e empresas de pequeno porte?<br/>");
$obLabelFrase->setRotulo("Planejamento Anual");

$obRadioPercentualSim = new Radio();
$obRadioPercentualSim->setName('stPercentualAnual');
$obRadioPercentualSim->setId('stPercentualAnual');
$obRadioPercentualSim->setValue(1);
if ($stPercentualAnual == 1) {
    $obRadioPercentualSim->setChecked(true);
}
$obRadioPercentualSim->setLabel('Sim');
$obRadioPercentualSim->obEvento->setOnChange("habilitaPercentual();");

$obRadioPercentualNao = new Radio();
$obRadioPercentualNao->setName('stPercentualAnual');
$obRadioPercentualNao->setId('stPercentualAnual');
$obRadioPercentualNao->setValue(2);
if ($stPercentualAnual == 2) {
    $obRadioPercentualSim->setChecked(true);
}
$obRadioPercentualNao->setLabel('Não');
$obRadioPercentualNao->obEvento->setOnChange("desabilitaPercentual();");

$obPercentagemEstabelecido = new Porcentagem();
$obPercentagemEstabelecido->setRotulo('Percentual Estabelecido');
$obPercentagemEstabelecido->setName('flPercentualEstabelecido');
$obPercentagemEstabelecido->setId('flPercentualEstabelecido');
$obPercentagemEstabelecido->setValue($flPercentualEstabelecido);

//****************************************//
// Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados de Configuração de Percentual para aquisição de bens e serviços licitáveis" );

$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->agrupaComponentes(array($obLabelFrase,$obRadioPercentualSim,$obRadioPercentualNao));

$obFormulario->addComponente($obPercentagemEstabelecido);

$obFormulario->OK();
$obFormulario->show();

$jsOnLoad .= "
    if (jQuery('input:radio:checked').val()==2) {
        jQuery('#flPercentualEstabelecido').attr('disabled',true);
        jQuery('#flPercentualEstabelecido').css('background-color', '#cccccc');
    } ";
if ($flPercentualEstabelecido != '0.00') {
    $jsOnLoad .= " jQuery('#flPercentualEstabelecido').val('".str_replace('.',',',$flPercentualEstabelecido)."'); \n";
} else {
    $jsOnLoad .= " jQuery('#flPercentualEstabelecido').val('0,00'); \n";
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

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
    * Arquivo de Formulário
    * Data de Criação: 30/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-09-13 13:11:28 -0300 (Qui, 13 Set 2007) $

    * Casos de uso: uc-04.05.59
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                     );

//****************************************//
//Define NOME DOS ARQUIVOS
//****************************************//
$stPrograma = "ManterDescontoExterno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//****************************************//
//Define INTENS DE INICIALIZAÇÃO
//****************************************//
Sessao::remove("link");
$stAcao   = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//****************************************//
//Define CABEÇALHO
//****************************************//
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
$obIFiltroContrato = new IFiltroContrato();
$obIFiltroContrato->obIContratoDigitoVerificador->setNull(false);
$obIFiltroContrato->obIContratoDigitoVerificador->setRescindido(false);

if ($stAcao == alterar) {
   $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('montaValoresAlteracao', 'inContrato');");
   $obIFiltroContrato->obIContratoDigitoVerificador->setTipo("desconto_externo_previdencia");
}

$obTxtValorBasePrevidencia = new Moeda;
$obTxtValorBasePrevidencia->setRotulo    		( "Valor Base Previdência"											  );
$obTxtValorBasePrevidencia->setTitle     		( "Informe o valor da base de previdência calculada em outras entidades.");
$obTxtValorBasePrevidencia->setName      		( "inValorBase" 													  );
$obTxtValorBasePrevidencia->setValue     		( $inValorBase  													  );
$obTxtValorBasePrevidencia->setMaxLength 		( 14  																  );
$obTxtValorBasePrevidencia->setSize      		( 15  																  );
$obTxtValorBasePrevidencia->setNull      		( false 															  );

$obTxtValorPrevidencia = new Moeda;
$obTxtValorPrevidencia->setRotulo    		    ( "Valor Desconto Previdência"											  			  );
$obTxtValorPrevidencia->setTitle     		    ( "Informe o valor do desconto da previdência descontado em outras entidades.");
$obTxtValorPrevidencia->setName      		    ( "inValor" 													  	  );
$obTxtValorPrevidencia->setValue     	 	    ( $inValor    													  	  );
$obTxtValorPrevidencia->setMaxLength 		    ( 14  																  );
$obTxtValorPrevidencia->setSize      		    ( 15  																  );
$obTxtValorPrevidencia->setNull      		    ( true 																  );

$obDtVigencia = new Data;
$obDtVigencia->setName                          ( "dtVigencia"													      );
$obDtVigencia->setTitle              		    ( "Informe a vigência, a partir de que data as informações deverão ser utilizadas para os cálculos dos ajustes da previdência.");
$obDtVigencia->setNull               		    ( false                          									  );
$obDtVigencia->setRotulo             		    ( "Vigência"			          									  );
$obDtVigencia->setValue              		    ( $dtVigencia                    									  );
$obDtVigencia->obEvento->setOnChange            ( "montaParametrosGET('validaDataVigencia','dtVigencia,inContrato');"            );

$obBtnOk = new Ok;
$obBtnOk->setName               ("btnOk");
$obBtnOk->setTitle              ("Clique para gravar as informações");
$obBtnOk->obEvento->setOnClick  ( "montaParametrosGET('submeter', '', true);");

if ($stAcao == 'alterar') {
    $obBtnOk->setDisabled( true );
}

$obBtnLimpar = new Limpar;
$obBtnLimpar->setName  ( "btnLimpar" );
$obBtnLimpar->setTitle ( "Clique para limpar os dados dos campos.");
//****************************************//
//Define CONTROLES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction			( $pgProc  );
$obForm->setTarget			( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName 		( "stAcao" );
$obHdnAcao->setValue		( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName 		( "stCtrl" );
$obHdnCtrl->setValue		( $stCtrl  );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName    ( "stTimestamp");
$obHdnTimestamp->setValue   ( $stTimestamp );
//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm   	 	   ( $obForm														  );
$obFormulario->addTitulo 	 	   ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden 	 	   ( $obHdnAcao                                                       );
$obFormulario->addHidden 	 	   ( $obHdnCtrl                                                       );
$obFormulario->addHidden           ( $obHdnTimestamp                                                  );
$obIFiltroContrato->geraFormulario ( $obFormulario                                                    );
$obFormulario->addComponente 	   ( $obTxtValorBasePrevidencia						         	   	  );
$obFormulario->addComponente       ( $obTxtValorPrevidencia											  );
$obFormulario->addComponente       ( $obDtVigencia													  );

//if ($stAcao == "incluir") {
//    $obFormulario->OK();
//}
$obFormulario->defineBarra         ( array ($obBtnOk, $obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

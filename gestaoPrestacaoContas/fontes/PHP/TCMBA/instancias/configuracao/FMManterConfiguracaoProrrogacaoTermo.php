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
  * Página de Formulario de Configuração de Prorrogação de Termos de Parceria/Subvenção/OSCIP
  * Data de Criação: 21/10/2015

  * @author Analista: 
  * @author Desenvolvedor: Franver Sarmento de Moraes
  * @ignore
  *
  * $Id: FMManterConfiguracaoProrrogacaoTermo.php 63828 2015-10-21 20:04:39Z franver $
  * $Revision: 63828 $
  * $Author: franver $
  * $Date: 2015-10-21 18:04:39 -0200 (Wed, 21 Oct 2015) $
*/
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php";
require_once "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php";
require_once CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoProrrogacaoTermo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include $pgJs;

$stCtrl = $request->get("stCtrl");
$stAcao = $request->get("stAcao");
$stExercicioProcesso = $request->get('stExercicioProcesso');
$inCodEntidade       = $request->get('inCodEntidade');
$stNumeroProcesso    = $request->get('stNumeroProcesso');

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setId   ("stAcao");
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ("stCtrl");
$obHdnCtrl->setId   ("stCtrl");
$obHdnCtrl->setValue($stCtrl);

$obHdnInId = new Hidden;
$obHdnInId->setName( "inId" );
$obHdnInId->setId  ( "inId" );


$obHdnExercicio = new Hidden();
$obHdnExercicio->setName ("stExercicioProcesso");
$obHdnExercicio->setId   ("stExercicioProcesso");
$obHdnExercicio->setValue($stExercicioProcesso);

$obHdnNumeroProcesso = new Hidden();
$obHdnNumeroProcesso->setName ("stNumeroProcesso");
$obHdnNumeroProcesso->setId   ("stNumeroProcesso");
$obHdnNumeroProcesso->setValue($stNumeroProcesso);

$obHdnDataTerminoTermo = new Hidden();
$obHdnDataTerminoTermo->setName("stDtTerminoTermo");
$obHdnDataTerminoTermo->setId  ("stDtTerminoTermo");

$obLblExercicio = new Label();
$obLblExercicio->setRotulo("Exercicio do Termo de Parceria");
$obLblExercicio->setId    ("stExercicioTermo");
$obLblExercicio->setName  ("stExercicioTermo");
$obLblExercicio->setValue ($stExercicioProcesso);

$obILabelEntidade = new ILabelEntidade($obFrom);
$obILabelEntidade->setExercicio   ($stExercicioProcesso);
$obILabelEntidade->setMostraCodigo(true);
$obILabelEntidade->setCodEntidade ($inCodEntidade);

$obLblNumProcesso = new Label();
$obLblNumProcesso->setRotulo("Número Processo");
$obLblNumProcesso->setId    ("stNumProcesso");
$obLblNumProcesso->setName  ("stNumProcesso");
$obLblNumProcesso->setValue ($stNumeroProcesso);

$obLblDataInicio = new Label();
$obLblDataInicio->setRotulo("Data de Início");
$obLblDataInicio->setId    ("stDataInicio");
$obLblDataInicio->setName  ("stDataInicio");

$obLblDataTermino = new Label();
$obLblDataTermino->setRotulo("Data de Término");
$obLblDataTermino->setId    ("stDataTermino");
$obLblDataTermino->setName  ("stDataTermino");

$obLblObjeto = new Label();
$obLblObjeto->setRotulo("Objeto");
$obLblObjeto->setId    ("stObjeto");
$obLblObjeto->setName  ("stObjeto");

/******************************
 * Cadastro das Prorrogação Termos de Parceria/Subvenção/OSCIP
 ******************************
 **/
$obTxtExercicioProrrogacao = new TextBox;
$obTxtExercicioProrrogacao->setRotulo          ("Exercício do Prorrogacao");
$obTxtExercicioProrrogacao->setTitle           ("Informe o exercício do Prorrogacao.");
$obTxtExercicioProrrogacao->setName            ("stExercicioProrrogacao");
$obTxtExercicioProrrogacao->setId              ("stExercicioProrrogacao");
$obTxtExercicioProrrogacao->setMaxLength       (4);
$obTxtExercicioProrrogacao->setSize            (5);
$obTxtExercicioProrrogacao->setInteiro         (false);
$obTxtExercicioProrrogacao->setNullBarra       (false);
$obTxtExercicioProrrogacao->setObrigatorioBarra(true);

$obTxtNumeroTermoAditivo = new TextBox();
$obTxtNumeroTermoAditivo->setRotulo          ("Número do termo aditivo");
$obTxtNumeroTermoAditivo->setTitle           ("Informe o número do aditivo que trata da prorrogação");
$obTxtNumeroTermoAditivo->setName            ("stNumeroAditivo");
$obTxtNumeroTermoAditivo->setId              ("stNumeroAditivo");
$obTxtNumeroTermoAditivo->setMaxLength       (36);
$obTxtNumeroTermoAditivo->setSize            (36);
$obTxtNumeroTermoAditivo->setNullBarra       (false);
$obTxtNumeroTermoAditivo->setObrigatorioBarra(true);

$obDtProrrogacao = new Data();
$obDtProrrogacao->setRotulo          ("Data da prorrogação");
$obDtProrrogacao->setName            ("dtProrrogacao");
$obDtProrrogacao->setId              ("dtProrrogacao");
$obDtProrrogacao->setNullBarra       (false);
$obDtProrrogacao->setObrigatorioBarra(true);

$obDtPublicaoProrrogacao = new Data();
$obDtPublicaoProrrogacao->setRotulo          ("Data de Publicação da Prorrogação");
$obDtPublicaoProrrogacao->setName            ("dtPublicacaoProrrogacao");
$obDtPublicaoProrrogacao->setId              ("dtPublicacaoProrrogacao");
$obDtPublicaoProrrogacao->setNullBarra       (false);
$obDtPublicaoProrrogacao->setObrigatorioBarra(true);

$obTxtImprensaOficialProrrogacao = new TextBox();
$obTxtImprensaOficialProrrogacao->setRotulo          ("Imprensa oficial");
$obTxtImprensaOficialProrrogacao->setName            ("stImprensaOficialProrrogacao");
$obTxtImprensaOficialProrrogacao->setId              ("stImprensaOficialProrrogacao");
$obTxtImprensaOficialProrrogacao->setMaxLength       (50);
$obTxtImprensaOficialProrrogacao->setSize            (42);
$obTxtImprensaOficialProrrogacao->setNullBarra       (false);
$obTxtImprensaOficialProrrogacao->setObrigatorioBarra(true);

$obRdAdimplementoSim = new Radio();
$obRdAdimplementoSim->setRotulo          ("Indicador de adimplemento");
$obRdAdimplementoSim->setTitle           ("Selecione caso tenha havido ou não adimplência por parte da OSCIP.");
$obRdAdimplementoSim->setLabel           ("Sim");
$obRdAdimplementoSim->setName            ("boIndicadorAdimplemento");
$obRdAdimplementoSim->setId              ("boIndicadorAdimplemento");
$obRdAdimplementoSim->setChecked         (false);
$obRdAdimplementoSim->setValue           ('t');
$obRdAdimplementoSim->setObrigatorioBarra(true);

$obRdAdimplementoNao = new Radio();
$obRdAdimplementoNao->setRotulo          ("Indicador de adimplemento");
$obRdAdimplementoNao->setTitle           ("Selecione caso tenha havido ou não adimplência por parte da OSCIP.");
$obRdAdimplementoNao->setLabel           ("Não");
$obRdAdimplementoNao->setName            ("boIndicadorAdimplemento");
$obRdAdimplementoNao->setId              ("boIndicadorAdimplemento");
$obRdAdimplementoNao->setChecked         (true);
$obRdAdimplementoNao->setValue           ('f');
$obRdAdimplementoNao->setObrigatorioBarra(true);

$obDtInicioProrrogacao = new Data();
$obDtInicioProrrogacao->setRotulo          ("Data do Início");
$obDtInicioProrrogacao->setName            ("dtInicioProrrogacao");
$obDtInicioProrrogacao->setId              ("dtInicioProrrogacao");
$obDtInicioProrrogacao->setNullBarra       (false);
$obDtInicioProrrogacao->setObrigatorioBarra(true);
$obDtInicioProrrogacao->obEvento->setOnBlur(" if( this.value != '' && jQuery('#stDtTerminoTermo').val() != ''){ montaParametrosGET('validaDataProrrogacao'); }\n");

$obDtTerminoProrrogacao = new Data();
$obDtTerminoProrrogacao->setRotulo          ("Data de Término");
$obDtTerminoProrrogacao->setName            ("dtTerminoProrrogacao");
$obDtTerminoProrrogacao->setId              ("dtTerminoProrrogacao");
$obDtTerminoProrrogacao->setNullBarra       (false);
$obDtTerminoProrrogacao->setObrigatorioBarra(true);
$obDtTerminoProrrogacao->obEvento->setOnBlur(" if(this.value != '' && jQuery('dtInicioProrrogacao').val() != '') { montaParametrosGET('validaPeriodicidade', 'dtInicioProrrogacao,dtTerminoProrrogacao');} \n");

$obVlProrrogacao = new Moeda();
$obVlProrrogacao->setRotulo          ("Valor da prorrogação");
$obVlProrrogacao->setName            ("vlProrrogacao");
$obVlProrrogacao->setId              ("vlProrrogacao");
$obVlProrrogacao->setMaxLength       (21);
$obVlProrrogacao->setSize            (15);
$obVlProrrogacao->setNullBarra       (false);
$obVlProrrogacao->setObrigatorioBarra(true);

$obSpnProrrogacoes = new Span();
$obSpnProrrogacoes->setId('spnProrrogacoes');

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnInId );
$obFormulario->addHidden     ( $obHdnExercicio );
$obFormulario->addHidden     ( $obHdnNumeroProcesso );
$obFormulario->addHidden     ( $obHdnDataTerminoTermo );
$obFormulario->addTitulo     ( "Dados para Termos de Parceria/Subvenção/OSCIP" );
$obILabelEntidade->geraFormulario( $obFormulario );
$obFormulario->addComponente ( $obLblExercicio );
$obFormulario->addComponente ( $obLblNumProcesso );
$obFormulario->addComponente ( $obLblDataInicio );
$obFormulario->addComponente ( $obLblDataTermino );
$obFormulario->addComponente ( $obLblObjeto );
$obFormulario->addTitulo     ( "Dados para Prorrogação do Termos de Parceria/Subvenção/OSCIP" );
$obFormulario->addComponente ( $obTxtExercicioProrrogacao );
$obFormulario->addComponente ( $obTxtNumeroTermoAditivo );
$obFormulario->addComponente ( $obDtProrrogacao );
$obFormulario->addComponente ( $obDtPublicaoProrrogacao );
$obFormulario->addComponente ( $obTxtImprensaOficialProrrogacao );
$obFormulario->addComponenteComposto ( $obRdAdimplementoSim, $obRdAdimplementoNao );
$obFormulario->addComponente ( $obDtInicioProrrogacao );
$obFormulario->addComponente ( $obDtTerminoProrrogacao );
$obFormulario->addComponente ( $obVlProrrogacao );
$obFormulario->IncluirAlterar( 'Prorrogacoes',array($obTxtExercicioProrrogacao,$obTxtNumeroTermoAditivo,$obDtProrrogacao,$obDtPublicaoProrrogacao,$obTxtImprensaOficialProrrogacao,$obRdAdimplementoSim,$obRdAdimplementoNao,$obDtInicioProrrogacao,$obDtTerminoProrrogacao,$obVlProrrogacao), true, false,'', true);
$obFormulario->addSpan       ( $obSpnProrrogacoes );
$obFormulario->OK(true);
$obFormulario->show();


require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
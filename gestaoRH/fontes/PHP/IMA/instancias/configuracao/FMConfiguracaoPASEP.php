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
    * Página de Formulário do IMA Configuração - PASEP
    * Data de Criação: 29/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.22

    $Id: FMConfiguracaoPASEP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php"		                                    );

$stPrograma = "ConfiguracaoPASEP";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write("stNumBanco", "001");
$jsOnload = "executaFuncaoAjax('preencherDadosAgencia');";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

//codigo de convenio com o banco
$obTxtCodigoConvenio = new TextBox;
$obTxtCodigoConvenio->setRotulo          ( "Código Convênio com Banco"		);
$obTxtCodigoConvenio->setName            ( "stCodEmpresa"					);
$obTxtCodigoConvenio->setValue           ( $stCodEmpresa                     );
$obTxtCodigoConvenio->setTitle           ( "Informe o Código do Convênio firmado entre a Prefeitura e o Banco." );
$obTxtCodigoConvenio->setSize            ( 12                                              					    );
$obTxtCodigoConvenio->setMaxLength       ( 6                                                                    );
$obTxtCodigoConvenio->setInteiro         ( true                                                                 );
$obTxtCodigoConvenio->setNull			 ( false											                    );

//Localiza nome do banco
$obTMONBanco = new TMONBanco;
$stFiltro = " WHERE num_banco = '".Sessao::read("stNumBanco")."'";
$obTMONBanco->recuperaTodos($rsBancos,$stFiltro);
$stNumBanco = $rsBancos->getCampo("num_banco");
$inCodBanco = $rsBancos->getCampo("cod_banco");

//Agencia do convenio
$obIMontaAgencia = new IMontaAgencia();
$obIMontaAgencia->obTextBoxSelectAgencia->setTitle("Selecione a agência mantenedora da conta do Convênio.");
$obIMontaAgencia->obTextBoxSelectAgencia->setNull(true);
$obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setDisabled(true);
$obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setValue($stNumBanco);
$obIMontaAgencia->obITextBoxSelectBanco->obSelect->setDisabled(true);
$obIMontaAgencia->obITextBoxSelectBanco->obSelect->setValue($stNumBanco);
$obIMontaAgencia->obTextBoxSelectAgencia->obSelect->obEvento->setOnChange("montaParametrosGET('preencheDadosConta','stNumAgenciaTxt');");
$obIMontaAgencia->obTextBoxSelectAgencia->setNull(false);

//Conta corrente do convenio
$obCmbContaCorrente = new Select;
$obCmbContaCorrente->setRotulo       ( "Conta Corrente"	    );
$obCmbContaCorrente->setName         ( "inTxtContaCorrente" );
$obCmbContaCorrente->setId           ( "inTxtContaCorrente" );
$obCmbContaCorrente->setStyle        ( "width: 200px"		);
$obCmbContaCorrente->setTitle        ( "Selecione a Conta Corrente do Convênio (conta para débito)."	    );
$obCmbContaCorrente->setCampoID      ( "cod_conta" 	        );
$obCmbContaCorrente->setCampoDesc    ( "nom_conta" 	        );
$obCmbContaCorrente->addOption       ( "", "Selecione" 	    );
$obCmbContaCorrente->setValue        ( $inCodConta    	    );
$obCmbContaCorrente->setNull         ( false 			    );

//Envia codigo do banco para processamento
Sessao::write('BANCO', $inCodBanco);

$obTxtEmailContato = new TextBox();
$obTxtEmailContato->setRotulo("E-mail do Contato");
$obTxtEmailContato->setName("stEmailContato");
$obTxtEmailContato->setTitle("Informe o endereço de e-mail do contato.");
$obTxtEmailContato->setSize(70);
$obTxtEmailContato->setMaxLength(50);
$obTxtEmailContato->setNull(false);
// $obTxtEmailContato->setValue($rsEmailContato->getCampo("valor"));

include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                              );
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

$obBscEventoPasep = new BuscaInner;
$obBscEventoPasep->setRotulo                         ( "Evento do PASEP"               );
$obBscEventoPasep->setTitle                          ( "Informe o evento para o PASEP." );
$obBscEventoPasep->setId                             ( "inCampoInnerEventoPasep"                    );
$obBscEventoPasep->setValue                          ( ''                                                     );
$obBscEventoPasep->obCampoCod->setName               ( "inCodigoEventoPasep"                        );
$obBscEventoPasep->setNull                           ( false                                                  );
$obBscEventoPasep->obCampoCod->setMascara            ( $stMascaraEvento                                       );
$obBscEventoPasep->obCampoCod->setPreencheComZeros   ( 'E'                                                    );
$obBscEventoPasep->obCampoCod->obEvento->setOnChange ( "executaFuncaoAjax( 'preencherInnerEvento', '&nuCodigoEvento='+document.frm.inCodigoEventoPasep.value );"                           );
$obBscEventoPasep->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','inCodigoEventoPasep','inCampoInnerEventoPasep','','".Sessao::getId()."&stNaturezasAceitas=P&stNatureza=P&stTipoEvento=n_evento_sistema&stTipo=V','800','550')" );

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addTitulo( "Configuração do PASEP" );
$obFormulario->addTitulo( "Dados do Convênio" );
$obFormulario->addComponente( $obTxtCodigoConvenio );
$obIMontaAgencia->geraFormulario( $obFormulario	);
$obFormulario->addComponente( $obCmbContaCorrente );
$obFormulario->addComponente( $obTxtEmailContato );
$obFormulario->addComponente( $obBscEventoPasep );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

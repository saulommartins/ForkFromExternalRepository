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
    * Página de Formulário do IMA Configuração - CaixaEconomicaFederal
    * Data de Criação: 09/10/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    * Casos de uso: uc-04.08.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php"		                                    );

$stPrograma = "ExportacaoBancoCaixaEconomicaFederal";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write("stNumBanco", "104");
$jsOnload = "executaFuncaoAjax('preencheDados');";
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$jsOnload = "executaFuncaoAjax('preencherDadosAgencia');";

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stAcao = $request->get('stAcao');
 
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
$obTxtCodigoConvenio->setName            ( "stCodConvenio"					);
$obTxtCodigoConvenio->setID              ( "stCodConvenio"                  );
$obTxtCodigoConvenio->setValue           ( $stCodConvenio                   );
$obTxtCodigoConvenio->setTitle           ( "Informe o Código do Convênio firmado entre a Prefeitura e o Banco." );
$obTxtCodigoConvenio->setSize            ( 12                                              					    );
$obTxtCodigoConvenio->setMaxLength       ( 10                                                                   );
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
$obCmbContaCorrente->setRotulo       ( "Conta-Corrente"	    );
$obCmbContaCorrente->setName         ( "inTxtContaCorrente" );
$obCmbContaCorrente->setID           ( "inTxtContaCorrente" );
$obCmbContaCorrente->setStyle        ( "width: 200px"		);
$obCmbContaCorrente->setTitle        ( "Selecione a Conta-Corrente do Convênio (conta para débito)."	    );
$obCmbContaCorrente->setCampoID      ( "cod_conta" 	        );
$obCmbContaCorrente->setCampoDesc    ( "nom_conta" 	        );
$obCmbContaCorrente->addOption       ( "", "Selecione" 	    );
$obCmbContaCorrente->setValue        ( $inCodConta    	    );
$obCmbContaCorrente->setNull         ( false 			    );

//*Tipo de convênio/Layout
$obCmbConvenioLayout = new Select;
$obCmbConvenioLayout->setRotulo       ( "Tipo de convênio/Layout"                    );
$obCmbConvenioLayout->setName         ( "inTipoConvenioLayout"                       );
$obCmbConvenioLayout->setID           ( "inTipoConvenioLayout"                       );
$obCmbConvenioLayout->setStyle        ( "width: 200px"                               );
$obCmbConvenioLayout->setTitle        ( "Selecione o tipo de Convênio para o Layout" );
$obCmbConvenioLayout->setCampoID      ( "cod_convenio_Layout"                        );
$obCmbConvenioLayout->setCampoDesc    ( "nom_convenio_Layout"                        );
$obCmbConvenioLayout->addOption       ( "", "Selecione"                              );
$obCmbConvenioLayout->addOption       ( 1 , "SIACC 150"                              );
$obCmbConvenioLayout->addOption       ( 2 , "SICOV 150 - PADRAO 150 FEBRABAN"        );
$obCmbConvenioLayout->setNull         ( false                                        );

//Envia codigo do banco para processamento
Sessao::write('BANCO', $inCodBanco);

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addTitulo             				( "Configuração da Exportação Bancária" 								);
$obFormulario->addTitulo             				( "Caixa Econ&ocirc;mica Federal"                     		            );
$obFormulario->addComponente         				( $obTxtCodigoConvenio             									    );
$obFormulario->addComponente                        ( $obCmbConvenioLayout                                                  );
$obIMontaAgencia->geraFormulario     				( $obFormulario															);
$obFormulario->addComponente         				( $obCmbContaCorrente              								        );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

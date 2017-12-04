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
/*
    * Página de Formulário para publicação do contrato
    * Data de Criação   : 10/11/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Rodrigo

    * @ignore

    * $Id: FMManterPublicacaoContrato.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-03.05.23
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include de entidade de acordo com permissões do usuário
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php"                      );
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php"                                 );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterPublicacaoContrato";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);
Sessao::write('arValores', array());

//Definição do Form
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto de controle
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"  );
$obHdnAcao->setValue ( "incluir" );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

//Define o objeto de controle do id na listagem do veiculo de publicação
$obHdnCodVeiculo= new Hidden;
$obHdnCodVeiculo->setName  ( "HdnCodVeiculo" );
$obHdnCodVeiculo->setValue ( ""           );

//Define o valor da entidade apos o combo estiver desabilitado
$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "HdnCodEntidade" );
$obHdnCodEntidade->setValue ( ""               );

//Define o valor do contrato apos o campo desabilitado
$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName  ( "HdnCodContrato" );
$obHdnCodContrato->setValue ( ""               );

//Define o valor do numero da licitação
$obHdnCodLicitacao = new Hidden;
$obHdnCodLicitacao->setName  ( "HdnCodLicitacao" );
$obHdnCodLicitacao->setValue ( ""               );

//Define o valor do numero da Modalidade
$obHdnCodModalidade = new Hidden;
$obHdnCodModalidade->setName  ( "HdnCodModalidade" );
$obHdnCodModalidade->setValue ( ""                 );

//Combo de entidade da publicação do Edital
$obISelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obISelectEntidadeUsuario->obTextBox->setNull(false);

//Campo Numero do contrato da publicação do Edital
$obNroContrato = new TextBox;
$obNroContrato->setName      ( "inContrato"                         );
$obNroContrato->setRotulo    ( "Número do Contrato"                 );
$obNroContrato->setTitle     ( "Informe o Número do Contrato."       );
$obNroContrato->setNull      ( false                                );
$obNroContrato->setMaxLength ( 4                                    );
$obNroContrato->setSize      ( 5                                    );
$obNroContrato->obEvento->setOnBlur("montaParametrosGET( 'carregaListaVeiculos','' );" );

//Label Numero da licitação da publicação do Edital
$obLblNroLicitacao = new Label;
$obLblNroLicitacao->setRotulo( "Número da Licitação" );
$obLblNroLicitacao->setValue ( "&nbsp;"              );
$obLblNroLicitacao->setId    ( "inNroLicitacao"      );

//Label Numero da licitação da publicação do Edital
$obLblObjeto = new Label;
$obLblObjeto->setRotulo( "Objeto"      );
$obLblObjeto->setValue ( "&nbsp;"      );
$obLblObjeto->setId    ( "inNroObjeto" );

//Painel veiculos de publicidade
$obVeiculoPublicidade = new IPopUpCGMVinculado( $obForm );
$obVeiculoPublicidade->setTabelaVinculo       ( 'licitacao.veiculos_publicidade' );
$obVeiculoPublicidade->setCampoVinculo        ( 'numcgm'                         );
$obVeiculoPublicidade->setNomeVinculo         ( 'Veículo de Publicação'          );
$obVeiculoPublicidade->setRotulo              ( '*Veículo de Publicação'         );
$obVeiculoPublicidade->setTitle 			  ( 'Informe o Veículo de Publicidade.' );
$obVeiculoPublicidade->setName                ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->setId                  ( 'stNomCgmVeiculoPublicadade'     );
$obVeiculoPublicidade->obCampoCod->setName    ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setId      ( 'inVeiculo'                      );
$obVeiculoPublicidade->obCampoCod->setNull    ( true                             );
$obVeiculoPublicidade->setNull                ( true                             );

//Campo data da Publicação
$dtDataPublicacao  = date('d').'/';
$dtDataPublicacao .= date('m').'/';
$dtDataPublicacao .= date('Y');

$obDataPublicacao = new Data();
$obDataPublicacao->setId   ( "dtDataVigencia" );
$obDataPublicacao->setName ( "dtDataVigencia" );
$obDataPublicacao->setValue( $dtDataPublicacao );
$obDataPublicacao->setRotulo( "Data da Vigência" );
$obDataPublicacao->setNull( false );
$obDataPublicacao->setTitle( "Informe a data de publicação do edital." );

//Campo Observação da Publicação
$obTxtObservacao = new TextArea;
$obTxtObservacao->setId 	( "stObservacao" 							   );
$obTxtObservacao->setName   ( "stObservacao"                               );
$obTxtObservacao->setValue  ( ""                                           );
$obTxtObservacao->setRotulo ( "Observação"                                 );
$obTxtObservacao->setTitle  ( "Informe uma breve observação da publicação.");
$obTxtObservacao->setNull   ( true                                         );
$obTxtObservacao->setRows   ( 2                                            );
$obTxtObservacao->setCols   ( 100                                          );
$obTxtObservacao->setMAxCaracteres( 80 );

//Define Objeto Button para Incluir Veiculo da Publicação
$obBtnIncluirVeiculo = new Button;
$obBtnIncluirVeiculo->setValue             ( "Incluir"                                      );
$obBtnIncluirVeiculo->setId                ( "incluiVeiculo"                                );
$obBtnIncluirVeiculo->obEvento->setOnClick ( "incluiVeiculos('incluirListaVeiculos');" );

//Define Objeto Button para Limpar Veiculo da Publicação
$obBtnLimparVeiculo = new Button;
$obBtnLimparVeiculo->setValue             ( "Limpar"          );
$obBtnLimparVeiculo->obEvento->setOnClick ( "limparVeiculo()" );

//Span da Listagem de veículos de Publicação Utilizados
$obSpnListaVeiculo = new Span;
$obSpnListaVeiculo->setID("spnListaVeiculos");

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                                               );
$obFormulario->addHidden     ( $obHdnAcao                                            );
$obFormulario->addHidden     ( $obHdnCtrl                                            );
$obFormulario->addHidden     ( $obHdnCodVeiculo                                      );
$obFormulario->addHidden     ( $obHdnCodEntidade                                     );
$obFormulario->addHidden     ( $obHdnCodContrato                                     );
$obFormulario->addHidden     ( $obHdnCodLicitacao                                    );
$obFormulario->addHidden     ( $obHdnCodModalidade                                   );
$obFormulario->addTitulo     ( "Dados da Publicação do Contrato"                     );
$obFormulario->addComponente ( $obISelectEntidadeUsuario                             );
$obFormulario->addComponente ( $obNroContrato                                        );
$obFormulario->addComponente ( $obLblNroLicitacao                                    );
$obFormulario->addComponente ( $obLblObjeto                                          );

$obFormulario->addTitulo     ( "Veículo de Publicação"                               );
$obFormulario->addComponente( $obVeiculoPublicidade   );
$obFormulario->addComponente ( $obDataPublicacao                                     );
$obFormulario->addComponente ( $obTxtObservacao                                      );
$obFormulario->agrupaComponentes( array( $obBtnIncluirVeiculo, $obBtnLimparVeiculo ) );

$obFormulario->addTitulo     ( "Veículo de Publicação Utilizados"                    );
$obFormulario->addSpan       ( $obSpnListaVeiculo                                    );

$obFormulario->OK();
$obFormulario->show();
//$stJs .= "montaParametrosGET( 'carregaListaVeiculos','' )";
//$stJs = "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."','carregaListaVeiculos');";
//$jsOnLoad = $stJs;

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

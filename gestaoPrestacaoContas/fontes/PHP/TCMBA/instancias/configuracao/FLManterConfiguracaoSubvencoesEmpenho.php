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

    * Pacote de configuração do TCMBA - Subvenções dos Empenhos
    * Data de Criação   : 25/08/2015

    * @author Analista: 
    * @author Desenvolvedor: Evandro Melos
    * 
    * $id: $
    
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSubvencoesEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnBanco = new Hidden;
$obHdnBanco->setName('inCodBanco');
$obHdnBanco->setId  ('inCodBanco');

$obHdnAgencia = new Hidden;
$obHdnAgencia->setName( 'inCodAgencia' );
$obHdnAgencia->setId  ( 'inCodAgencia' );

$obHdnNumAgencia = new Hidden;
$obHdnNumAgencia->setName( 'hdnNumAgencia' );
$obHdnNumAgencia->setId  ( 'hdnNumAgencia' );

$obHdnContaCorrente = new Hidden;
$obHdnContaCorrente->setName( 'hdnContaCorrente' );
$obHdnContaCorrente->setId  ( 'hdnContaCorrente' );

//CGM fornecedor
$obBscFornecedor = new IPopUpCGMVinculado( $obForm );
$obBscFornecedor->setTabelaVinculo       ( 'sw_cgm_pessoa_juridica' );
$obBscFornecedor->setCampoVinculo        ( 'numcgm'          );
$obBscFornecedor->setNomeVinculo         ( 'CGM Fornecedor'  );
$obBscFornecedor->setRotulo              ( 'CGM Fornecedor'  );
$obBscFornecedor->setName                ( 'stNomFornecedor' );
$obBscFornecedor->setId                  ( 'stNomFornecedor' );
$obBscFornecedor->setTitle               ( 'Informe o CGM.'  );
$obBscFornecedor->obCampoCod->setName    ( "inCGMFornecedor" );
$obBscFornecedor->obCampoCod->setId      ( "inCGMFornecedor" );
$obBscFornecedor->obCampoCod->setNull    ( false             );
$obBscFornecedor->setNull                ( false             );
$stOnchange = $obBscFornecedor->obCampoCod->obEvento->getOnChange();
$obBscFornecedor->obCampoCod->obEvento->setOnChange('');
$obBscFornecedor->obCampoCod->obEvento->setOnBlur( $stOnchange." if( this.value != ''){ montaParametrosGET('carregaDadosCgm'); } ");

// Campo de Data Inicio
$obDatDataInicio = new Data();
$obDatDataInicio->setId       ( 'stDataInicial' );
$obDatDataInicio->setName     ( 'stDataInicial' );
$obDatDataInicio->setRotulo   ( 'Data de Inicio da Subvenção' );
$obDatDataInicio->setNullBarra( false );
$obDatDataInicio->setNull     ( false ) ;

// Campo de Data Final
$obDatDataFinal = new Data();
$obDatDataFinal->setId       ( 'stDataFinal' );
$obDatDataFinal->setName     ( 'stDataFinal' );
$obDatDataFinal->setRotulo   ( 'Data Encerramento da Subvenção' );
$obDatDataFinal->setNullBarra( false );
$obDatDataFinal->setNull     ( false ) ;

//PRazo de Aplicação: em dias
$obIntPrazoAplicacao = new Inteiro();
$obIntPrazoAplicacao->setName      ( "inPrazoAplicacao" );
$obIntPrazoAplicacao->setId        ( "inPrazoAplicacao" );
$obIntPrazoAplicacao->setRotulo    ( "Prazo de Aplicação: em dias");
$obIntPrazoAplicacao->setInteiro   ( true );
$obIntPrazoAplicacao->setMaxLength ( 3 );
$obIntPrazoAplicacao->setMinLength ( 1 );
$obIntPrazoAplicacao->setSize      ( 1 );
$obIntPrazoAplicacao->setNull      ( false ) ;

//Prazo de Comprovação: em dias
$obIntPrazoComprovacao = new Inteiro();
$obIntPrazoComprovacao->setName      ( "inPrazoComprovacao" );
$obIntPrazoComprovacao->setId        ( "inPrazoComprovacao" );
$obIntPrazoComprovacao->setRotulo    ( "Prazo de Comprovação: em dias");
$obIntPrazoComprovacao->setInteiro   ( true );
$obIntPrazoComprovacao->setMaxLength ( 3 );
$obIntPrazoComprovacao->setMinLength ( 1 );
$obIntPrazoComprovacao->setSize      ( 1 ) ;
$obIntPrazoComprovacao->setNull      ( false ) ;

//Norma que reconheceu a utilizada pública
$obIPopUpNormaReconhecida = new IPopUpNorma();
$obIPopUpNormaReconhecida->obInnerNorma->setRotulo          ( "Norma pública utilizada");
$obIPopUpNormaReconhecida->obInnerNorma->setTitle           ( "Norma que reconheceu a utilizada pública.");
$obIPopUpNormaReconhecida->obInnerNorma->setId              ( "stNomeNormaReconhecida" );
$obIPopUpNormaReconhecida->obInnerNorma->obCampoCod->setId  ( "inCodNormaReconhecida" );
$obIPopUpNormaReconhecida->obInnerNorma->obCampoCod->setName( "inCodNormaReconhecida" );

//Norma que estabeleceu o valor a ser concedido
$obIPopUpNormaConcedente = new IPopUpNorma();
$obIPopUpNormaConcedente->obInnerNorma->setRotulo          ( "Norma Concedente");
$obIPopUpNormaConcedente->obInnerNorma->setTitle           ( "Norma que estabeleceu o valor a ser concedido.");
$obIPopUpNormaConcedente->obInnerNorma->setId              ( "stNomeNormaConcedente" );
$obIPopUpNormaConcedente->obInnerNorma->obCampoCod->setId  ( "inCodNormaConcedente" );
$obIPopUpNormaConcedente->obInnerNorma->obCampoCod->setName( "inCodNormaConcedente" );

//Dados bancarios para credito do recurso: Banco, Agencia e conta corrente
//Define Objeto TextBox para Codigo do Banco
$obTxtBanco = new TextBox;
$obTxtBanco->setName     ( "inNumBanco"        );
$obTxtBanco->setId       ( "inNumBanco"        );
$obTxtBanco->setRotulo   ( "Banco"            );
$obTxtBanco->setMaxlength( 5                   );
$obTxtBanco->setTitle    ( "Selecione o banco" );
$obTxtBanco->setInteiro  ( true                );
$obTxtBanco->setNull     ( false );
$obTxtBanco->obEvento->setOnBlur( " montaParametrosGET('montaAgencia'); ");

// Define Objeto Select para Nome do Banco
$obCmbBanco = new Select;
$obCmbBanco->setName      ( "stNomeBanco"   );
$obCmbBanco->setId        ( "stNomeBanco"   );
$obCmbBanco->addOption    ( "", "Selecione" );
$obCmbBanco->setCampoId   ( "num_banco"     );
$obCmbBanco->setCampoDesc ( "nom_banco"     );
$obCmbBanco->setNull      ( false );
$obCmbBanco->obEvento->setOnChange( " montaParametrosGET('selecionaBanco'); ");

// Define Objeto TextBox para Codigo da Agência
$obTxtAgencia = new TextBox;
$obTxtAgencia->setName     ( "inNumAgencia"        );
$obTxtAgencia->setId       ( "inNumAgencia"        );
$obTxtAgencia->setRotulo   ( "Agência"            );
$obTxtAgencia->setMaxLength( 10                    );
$obTxtAgencia->setTitle    ( "Selecione a agência" );
$obTxtAgencia->setNull     ( false );
$obTxtAgencia->obEvento->setOnBlur( " montaParametrosGET('montaContaCorrente'); ");

// Define Objeto Select para Nome da agencia
$obCmbAgencia = new Select;
$obCmbAgencia->setName      ( "stNomeAgencia"  );
$obCmbAgencia->setId        ( "stNomeAgencia"  );
$obCmbAgencia->addOption    ( "", "Selecione"  );
$obCmbAgencia->setNull     ( false );
$obCmbAgencia->obEvento->setOnChange( " montaParametrosGET('selecionaAgencia'); ");

$obCmbContaCorrente = new Select();
$obCmbContaCorrente->setRotulo    ( "Conta Corrente");
$obCmbContaCorrente->setName      ( "stContaCorrente"    );
$obCmbContaCorrente->setId        ( "stContaCorrente"    );
$obCmbContaCorrente->addOption    ( "", "Selecione"          );
$obCmbContaCorrente->setNull     ( false );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addTitulo     ( "Configurar Subvenções de Empenhos"  );
$obFormulario->addHidden     ( $obHdnAcao       );
$obFormulario->addHidden     ( $obHdnBanco      );
$obFormulario->addHidden     ( $obHdnAgencia    );
$obFormulario->addHidden     ( $obHdnNumAgencia );
$obFormulario->addHidden     ( $obHdnContaCorrente );
$obFormulario->addComponente ( $obBscFornecedor );
$obFormulario->addComponente ( $obDatDataInicio );
$obFormulario->addComponente ( $obDatDataFinal  );
$obFormulario->addComponente ( $obIntPrazoAplicacao );
$obFormulario->addComponente ( $obIntPrazoComprovacao );
$obIPopUpNormaReconhecida->geraFormulario($obFormulario);
$obIPopUpNormaConcedente->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes ( array($obTxtBanco  , $obCmbBanco) );
$obFormulario->agrupaComponentes ( array($obTxtAgencia, $obCmbAgencia) );
$obFormulario->addComponente     ( $obCmbContaCorrente );

$obFormulario->Ok();
$obFormulario->show();

$jsOnLoad = " montaParametrosGET('carregaDadosBanco'); montaParametrosGET('carregaDadosCgm'); ";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
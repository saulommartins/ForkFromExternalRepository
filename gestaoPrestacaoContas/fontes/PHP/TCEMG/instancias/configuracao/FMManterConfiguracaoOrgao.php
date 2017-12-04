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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php');
include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php');
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGConfiguracaoOrgao.class.php" );
include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGOrgao.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php" );
include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoOrgao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once ($pgJs);
include_once ($pgOcul);

$rsEntidades = new RecordSet();
$rsRecordResponsavel = new RecordSet();

$stAcao   = $request->get('stAcao','manter');
$stModulo = $request->get('modulo',55);

$obTTCEMGConfiguracaoOrgao = new TTCEMGConfiguracaoOrgao();

$obTTCEMGConfiguracaoOrgao->setDado('cod_entidade',$request->get('inCodEntidade'));
$obTTCEMGConfiguracaoOrgao->setDado('exercicio',Sessao::getExercicio());
$obTTCEMGConfiguracaoOrgao->recuperaResponsaveis($rsRecordResponsavel, $boTransacao);

$arResponsaveis = array();
$arResponsaveis = $rsRecordResponsavel->getElementos();

$inCount = 0;

foreach($arResponsaveis as $responsavel){
    $responsavel["inId"] = $inCount;
    $arrResponsaveis[$inCount] = $responsavel;
    $inCount++;
}
Sessao::write("arResponsaveis", $arrResponsaveis);

// Declaração das variáveis usadas nos componentes para não ocorrer erro de variável nula
$inCodEntidade = $request->get("inCodEntidade");
$inValor = "";
$inOrgaoUnidade = "";

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );
$obForm->setName('frm');

//Define o objeto da ação stAcao
$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "stModulo" );
$obHdnModulo->setValue( $stModulo );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setId   ( "" );

//Define o objeto da ação stAcao
$obHdnStAcao = new Hidden;
$obHdnStAcao->setName ( "stHdnAcao" );
$obHdnStAcao->setId   ( "stHdnAcao" );
$obHdnStAcao->setValue( $stAcao );

//Define o objeto hidden da entidade
$obHdnEntidade = new Hidden;
$obHdnEntidade->setName ( "hdnCodEntidade"  );
$obHdnEntidade->setId   ( "hdnCodEntidade"  );
$obHdnEntidade->setValue( $inCodEntidade );

//Define o objeto hidden do ID
$obHdnInId = new Hidden;
$obHdnInId->setName("hdnInId");
$obHdnInId->setId  ("hdnInId");


//Lista de códigos cadastrados para cada entidade
$obTTCEMGConfiguracaoOrgao->setDado('cod_modulo', $stModulo);
$obTTCEMGConfiguracaoOrgao->setDado('parametro' ,'tcemg_codigo_orgao_entidade_sicom');
$obTTCEMGConfiguracaoOrgao->setDado("exercicio" ,Sessao::getExercicio());
$obTTCEMGConfiguracaoOrgao->recuperaCodigos($rsEntidades," AND ent.cod_entidade = ".$request->get('inCodEntidade')," \n ORDER BY ent.cod_entidade");

if ($rsEntidades->getNumLinhas() > 0) {
    foreach ($rsEntidades->arElementos as $index => $value) {
        if (substr($value['valor'],3,1) == "_") {
            $valor = substr($value['valor'],0,3);
        } else {
            $valor = substr($value['valor'],0,4);
        }
        $rsEntidades->arElementos[$index]['valor'] = $valor;
    }
    
    $inCodEntidade  = $rsEntidades->getCampo('cod_entidade');
    $inValor        = $rsEntidades->getCampo('valor');
    $inOrgaoUnidade = $rsEntidades->getCampo('orgao_unidade');
}

$obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obITextBoxSelectEntidadeUsuario->setNull ( false );
$obITextBoxSelectEntidadeUsuario->setCodEntidade($inCodEntidade);
$obITextBoxSelectEntidadeUsuario->obTextBox->setDisabled(true);
$obITextBoxSelectEntidadeUsuario->obSelect->setDisabled(true);


$obTxtOrgao = new TextBox();
$obTxtOrgao->setRotulo      ( "Orgão"                      );
$obTxtOrgao->setTitle       ( "Informe o número do orgão." );
$obTxtOrgao->setName        ( "inCodigo"                   );
$obTxtOrgao->setId          ( "inCodigo"                   );
$obTxtOrgao->setValue       ( $inValor                     );
$obTxtOrgao->setSize        ( 8                            );
$obTxtOrgao->setMaxLength   ( 2                            );
$obTxtOrgao->setMinLength   ( 2                            );
$obTxtOrgao->setInteiro     ( true                         );
$obTxtOrgao->setNull        ( false                        );

$obTTCEMGOrgao = new TTCEMGOrgao();
$obTTCEMGOrgao->setDado( 'exercicio', Sessao::getExercicio() );
$obTTCEMGOrgao->recuperaTodos( $rsUnidades );

$obCmbUnidades = new Select();
$obCmbUnidades->setRotulo           ( "Tipo Orgão"                   );
$obCmbUnidades->setTitle            ( "Selecione o tipo do órgão"    );
$obCmbUnidades->setName             ( "inNumUnidade"                 );
$obCmbUnidades->setId               ( "inNumUnidade"                 );
$obCmbUnidades->addOption           ( "","Selecione"                 );
$obCmbUnidades->setValue            ( $inOrgaoUnidade                );
$obCmbUnidades->setCampoId          ( "[num_orgao]"                  );
$obCmbUnidades->setCampoDesc        ( "nom_orgao"                    );
$obCmbUnidades->setStyle            ( "width:250px;"                 );
$obCmbUnidades->preencheCombo       ( $rsUnidades                    );

$obBscCGMS = new IPopUpCGM($obForm);
$obBscCGMS->setRotulo               ( "*CGM Responsável"    );
$obBscCGMS->setTitle                ( "Selecione o CGM."    );
$obBscCGMS->setName                 ( 'stNomCGM'            );
$obBscCGMS->setId                   ( "stNomCGM"            );
$obBscCGMS->obCampoCod->setId       ( "inNumCGM"            );
$obBscCGMS->obCampoCod->setName     ( "inNumCGM"            );
$obBscCGMS->setTipo                 ( "fisica"              );
$obBscCGMS->setObrigatorio          (false);
$obBscCGMS->setObrigatorioBarra     (false);

$obCmbTipoRespensavel = new Select();
$obCmbTipoRespensavel->setName   ( "inTipoResponsavel"    );
$obCmbTipoRespensavel->setId     ( "inTipoResponsavel"    );
$obCmbTipoRespensavel->setRotulo ( "*Tipo de Responsável"  );
$obCmbTipoRespensavel->setTitle  ( "Informe o Tipo de Responsável." );
$obCmbTipoRespensavel->addOption ( "","Selecione");
$obCmbTipoRespensavel->addOption ( "1","Gestor" );
$obCmbTipoRespensavel->addOption ( "2","Contador" );
$obCmbTipoRespensavel->addOption ( "3","Controle Interno" );
$obCmbTipoRespensavel->addOption ( "4","Ordenador de Despesa por Delegação" );
$obCmbTipoRespensavel->addOption ( "5","Informações - Folha de Pagamento" );
$obCmbTipoRespensavel->obEvento->setOnChange("buscaCampos('verificaTipoResponsavel', this.value);");

$obTxtCargo = new TextBox();
$obTxtCargo->setRotulo   ( 'Cargo' );
$obTxtCargo->setName     ( 'stCargoGestor' );
$obTxtCargo->setId       ( 'stCargoGestor' );
$obTxtCargo->setSize     ( 50 );
$obTxtCargo->setMaxLength( 50 );

$obDtInicio = new Data();
$obDtInicio->setName  ( 'dtInicio' );
$obDtInicio->setId    ( 'dtInicio' );
$obDtInicio->setRotulo( '*Data de Início' );

$obDtTermino = new Data();
$obDtTermino->setName  ( 'dtFim' );
$obDtTermino->setId    ( 'dtFim' );
$obDtTermino->setRotulo( '*Data de Término' );

$obTxtEmail = new TextBox();
$obTxtEmail->setRotulo   ( 'E-mail' );
$obTxtEmail->setName     ( 'stEMail' );
$obTxtEmail->setId       ( 'stEmail' );
$obTxtEmail->setSize     ( 50 );
$obTxtEmail->setMaxLength( 50 );
$obTxtEmail->setNull     ( true );


$obBtnIncluirResponsavel = new Button;
$obBtnIncluirResponsavel->setName             ( "btIncluirResponsavel"                                                 );
$obBtnIncluirResponsavel->setId               ( "btIncluirResponsavel"                                                 );
$obBtnIncluirResponsavel->setValue            ( "Incluir"                                                              );
$obBtnIncluirResponsavel->obEvento->setOnClick( "buscaValor('incluirResponsavel');"                                          );
$obBtnIncluirResponsavel->setTitle            ( "Clique para incluir o responsável na lista de Responsaveis"   );


$obSpnCGMsResponsaveis = new Span();
$obSpnCGMsResponsaveis->setId("spnCGMsResponsaveis");

$obSpnCamposContador = new Span();
$obSpnCamposContador->setId("spnCamposContador");

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                );
$obFormulario->addHidden     ( $obHdnCtrl             );
$obFormulario->addHidden     ( $obHdnModulo           );
$obFormulario->addHidden     ( $obHdnAcao             );
$obFormulario->addHidden     ( $obHdnEntidade         );
$obFormulario->addHidden     ( $obHdnStAcao           );
$obFormulario->addHidden     ( $obHdnInId             );
$obFormulario->addTitulo     ( "Parâmetros por Orgão" );
$obFormulario->addComponente ( $obITextBoxSelectEntidadeUsuario );
$obFormulario->addComponente ( $obTxtOrgao            ); 
$obFormulario->addComponente ( $obCmbUnidades         );
$obFormulario->addTitulo     ( "Parâmetros responsável" );
$obFormulario->addComponente ( $obBscCGMS             );
$obFormulario->addComponente ( $obCmbTipoRespensavel  );
$obFormulario->addComponente ( $obTxtCargo            );
$obFormulario->addSpan       ( $obSpnCamposContador );
$obFormulario->addComponente ( $obDtInicio            );
$obFormulario->addComponente ( $obDtTermino           );
$obFormulario->addComponente ( $obTxtEmail            );
$obFormulario->addComponente ( $obBtnIncluirResponsavel );
$obFormulario->addSpan       ( $obSpnCGMsResponsaveis );

$obOk     = new Ok;
$obLimpar = new Limpar;
$obLimpar->obEvento->setOnClick("limparFormulario();");

$obFormulario->defineBarra( array($obOk,  $obLimpar ) );

$obFormulario->show();

processarForm(true,"Form",$stAcao);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

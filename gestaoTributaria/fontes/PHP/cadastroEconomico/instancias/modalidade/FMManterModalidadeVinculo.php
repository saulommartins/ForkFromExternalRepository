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
    * Formulario para Modalidade de Lançamento
    * Data de Criação   : 04/01/2005

    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterModalidadeVinculo.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.16  2006/11/08 10:34:57  fabio
alteração do uc_05.02.13

Revision 1.15  2006/09/15 14:33:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeAtividade.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"         );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"            );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"               );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterModalidade";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormVinculo = "FM".$stPrograma."Vinculo.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "definir";
}

// DEFINE OBJETOS DAS CLASSES
$obRCEMModalidadeLancamento = new RCEMModalidadeLancamento;
$obRCEMModalidadeAtividade  = new RCEMModalidadeAtividade;
$obRCEMModalidadeInscricao  = new RCEMModalidadeInscricao;
$obMontaAtividade           = new MontaAtividade;
$rsAtividade                = new RecordSet;
$rsModalidade               = new RecordSet;

$obRCEMConfiguracao = new RCEMConfiguracao;
$obRCEMConfiguracao->setCodigoModulo( 14 );
$obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMConfiguracao->consultarConfiguracao();
$stMascaraInscricao = $obRCEMConfiguracao->getMascaraInscricao();

$obMontaAtividade->obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );

$obMontaAtividade->setCodigoVigencia( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );
$obMontaAtividade->setNivelObrigatorio(1);

// Preenche RecordSet
$obRCEMModalidadeLancamento->listarModalidade( $rsModalidade );

$obHdnCodVigencia = new Hidden;
$obHdnCodVigencia->setName      ( "inCodigoVigencia" );
$obHdnCodVigencia->setValue     ( $rsVigenciaAtual->getCampo( "cod_vigencia" ) );

// OBJETOS HIDDEN
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName      ( "stCtrl" );
$obHdnCtrl->setValue     ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName      ( "stAcao" );
$obHdnAcao->setValue     ( $stAcao  );

$obHdnVinculo = new Hidden;
$obHdnVinculo->setName                 ( "boVinculoModalidade"             );
$obHdnVinculo->setValue                ( $_REQUEST["boVinculoModalidade"]  );

$obHdnInCodigoAtividade = new Hidden;
$obHdnInCodigoAtividade->setName       ( "inCodigoAtividade"               );
$obHdnInCodigoAtividade->setValue      ( $_REQUEST["inCodigoAtividade"]    );

$obHdnInCodigoModalidade = new Hidden;
$obHdnInCodigoModalidade->setName      ( "inCodigoModalidade"              );
$obHdnInCodigoModalidade->setValue     ( $_REQUEST["inCodigoModalidade"]   );

//COMPONENTES PARA INCLUSAO
//ATIVIDADE
$obBscAtividade = new BuscaInner;
$obBscAtividade->setRotulo             ( "Atividade"                       );
$obBscAtividade->setTitle              ( "Atividade Econômica"             );
$obBscAtividade->setId                 ( "campoInner"                      );
$obBscAtividade->setNull               ( false                             );
$obBscAtividade->obCampoCod->setName   ( "stValorComposto"                 );
$obBscAtividade->obCampoCod->setInteiro ( false                            );
$obBscAtividade->obCampoCod->obEvento->setOnBlur ( "buscaDado('validaModalidade');");
$stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','stValorComposto','campoInner',''";
$stBusca .= " ,'".Sessao::getId()."&stCadastro=modalidade','800','550')";
$obBscAtividade->setFuncaoBusca ( $stBusca );

/*$obTxtModalidade = new TextBox;
$obTxtModalidade->setRotulo            ( "Modalidade"                      );
$obTxtModalidade->setName              ( "inCodigoModalidade"              );
$obTxtModalidade->setValue             ( $_REQUEST["inCodigoModalidade"]   );
$obTxtModalidade->setSize              ( 8                                 );
$obTxtModalidade->setMaxLength         ( 8                                 );
$obTxtModalidade->setNull              ( false                             );
$obTxtModalidade->setInteiro           ( true                              );

$obCmbModalidade = new Select;
$obCmbModalidade->setName              ( "cmbCodigoModalidade"             );
$obCmbModalidade->addOption            ( "", "Selecione"                   );
$obCmbModalidade->setCampoId           ( "cod_modalidade"                  );
$obCmbModalidade->setCampoDesc         ( "nom_modalidade"                  );
$obCmbModalidade->preencheCombo        ( $rsModalidade                     );
$obCmbModalidade->setValue             ( $_REQUEST["inCodigoModalidade"]   );
$obCmbModalidade->setNull              ( false                             );
$obCmbModalidade->setStyle             ( "width: 340px"                    );*/

//INSCRICAO ECONOMICA
$obBscInscricaoEconomica = new BuscaInner;
$obBscInscricaoEconomica->setNull            ( false                                       );
$obBscInscricaoEconomica->setRotulo          ( "Inscrição Econômica"                       );
$obBscInscricaoEconomica->setTitle           ( "Pessoa física ou jurídica cadastrada como inscrição econômica");
$obBscInscricaoEconomica->setId              ( "stInscricaoEconomica"                      );
$obBscInscricaoEconomica->obCampoCod->setName( "inInscricaoEconomica"                      );
$obBscInscricaoEconomica->obCampoCod->setSize( strlen($stMascaraInscricao)                 );
$obBscInscricaoEconomica->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)           );
$obBscInscricaoEconomica->obCampoCod->setMascara ( $stMascaraInscricao                     );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange ( "buscaDado('retornaListaAtividade');" );
$obBscInscricaoEconomica->obCampoCod->obEvento->setOnBlur   ( "buscaDado('retornaListaAtividade');" );
$obBscInscricaoEconomica->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');");

$obSpnVisualizarAtividade = new Span;
$obSpnVisualizarAtividade->setId( "spnVisualizarAtividade" );

/*$obTxtCodigoAtividade = new TextBox;
$obTxtCodigoAtividade->setRotulo       ( "*Atividade"                      );
$obTxtCodigoAtividade->setName         ( "stValorComposto"                 );
$obTxtCodigoAtividade->setValue        ( $_REQUEST["stValorComposto"]      );
$obTxtCodigoAtividade->setSize         ( 8                                 );
$obTxtCodigoAtividade->setMaxLength    ( 8                                 );
$obTxtCodigoAtividade->setNull         ( true                              );
$obTxtCodigoAtividade->setInteiro      ( true                              );
$obTxtCodigoAtividade->setReadOnly     ( true                              );

$obTxtNomeAtividade = new TextBox;
$obTxtNomeAtividade->setName           ( "stNomeAtividade"                 );
$obTxtNomeAtividade->setValue          ( $_REQUEST["stNomeAtividade"]      );
$obTxtNomeAtividade->setSize           ( 180                               );
$obTxtNomeAtividade->setMaxLength      ( 180                               );
$obTxtNomeAtividade->setStyle          ( "width: 340px"                    );
$obTxtNomeAtividade->setNull           ( true                              );
$obTxtNomeAtividade->setInteiro        ( true                              );
$obTxtNomeAtividade->setReadOnly       ( true                              );*/

$obTxtModalidade = new TextBox;
$obTxtModalidade->setRotulo            ( "*Modalidade"                     );
$obTxtModalidade->setName              ( "inCodigoModalidade"              );
$obTxtModalidade->setValue             ( $_REQUEST["inCodigoModalidade"]   );
$obTxtModalidade->setSize              ( 8                                 );
$obTxtModalidade->setMaxLength         ( 8                                 );
$obTxtModalidade->setNull              ( true                              );
$obTxtModalidade->setInteiro           ( true                              );

$obCmbModalidade = new Select;
$obCmbModalidade->setName              ( "cmbCodigoModalidade"             );
$obCmbModalidade->addOption            ( "", "Selecione"                   );
$obCmbModalidade->setCampoId           ( "cod_modalidade"                  );
$obCmbModalidade->setCampoDesc         ( "nom_modalidade"                  );
$obCmbModalidade->preencheCombo        ( $rsModalidade                     );
$obCmbModalidade->setValue             ( $_REQUEST["inCodigoModalidade"]   );
$obCmbModalidade->setNull              ( true                              );
$obCmbModalidade->setStyle             ( "width: 340px"                    );

/*
$obBtnDefinir = new Button;
$obBtnDefinir->setName                 ( "btnDefinir"           );
$obBtnDefinir->setValue                ( "Definir"              );
$obBtnDefinir->setTipo                 ( "button"               );
$obBtnDefinir->obEvento->setOnClick    ( "definirModalidade();" );
$obBtnDefinir->setDisabled             ( false                  );

$obBtnLimparDef = new Button;
$obBtnLimparDef->setName               ( "btnLimparDef"         );
$obBtnLimparDef->setValue              ( "Limpar"               );
$obBtnLimparDef->setTipo               ( "button"               );
$obBtnLimparDef->obEvento->setOnClick  ( "limparDef();"         );
$obBtnLimparDef->setDisabled           ( false                  );*/
$boVinculoModalidade = $_REQUEST["boVinculoModalidade"];
$obBtnLimparForm = new Button;
$obBtnLimparForm->setName              ( "btnLimparForm"        );
$obBtnLimparForm->setValue             ( "Limpar"               );
$obBtnLimparForm->setTipo              ( "button"               );
$obBtnLimparForm->obEvento->setOnClick ( "Limpar('$boVinculoModalidade');" );
$obBtnLimparForm->setDisabled          ( false                  );

$obBtnOK = new Ok;

$botoesSpanModalidade = array ( $obBtnDefinir , $obBtnLimparDef  );
$botoesFormulario     = array ( $obBtnOK      , $obBtnLimparForm );

$spnAtividadeInscricao = new Span;
$spnAtividadeInscricao->setId          ( "spnAtividadeInscricao"           );

//  GERAL
$obDataInicio = new Data;
$obDataInicio->setRotulo               ( "Data de Início"                  );
$obDataInicio->setTitle                ( "Data de início da vigência"      );
$obDataInicio->setName                 ( "dtDataInicio"                    );
//$obDataInicio->setValue                ( date('d/m/Y')                     );
$obDataInicio->setSize                 ( 10                                );
$obDataInicio->setMaxLength            ( 10                                );
$obDataInicio->setNull                 ( false                             );
$obDataInicio->setInteiro              ( false                             );

$obRdoTipoPercentual = new Radio;
$obRdoTipoPercentual->setName    ( "stTipoValor"                                                        );
$obRdoTipoPercentual->setLabel   ( "Percentual"                                                         );
$obRdoTipoPercentual->setValue   ( "percentual"                                                         );
$obRdoTipoPercentual->setRotulo  ( "Tipo de Valor"                                                      );
$obRdoTipoPercentual->setTitle   ( "Informe o tipo do valor da alíquota."                               );
$obRdoTipoPercentual->setNull    ( false                                                                );
$obRdoTipoPercentual->setChecked ( true                                                                 );
$obRdoTipoPercentual->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoValor='+this.value, 'spanMoedaIndicador' );" );
//$obRdoTipoPercentual->obEvento->setOnChange( "javascript: montaMoedaIndicador( 'spanMoedaIndicador' );" );

$obRdoTipoMoeda = new Radio;
$obRdoTipoMoeda->setName         ( "stTipoValor"                                                        );
$obRdoTipoMoeda->setLabel        ( "Moeda"                                                              );
$obRdoTipoMoeda->setValue        ( "moeda"                                                              );
$obRdoTipoMoeda->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoValor='+this.value, 'spanMoedaIndicador' );" );
//$obRdoTipoMoeda->obEvento->setOnChange( "javascript: montaMoedaIndicador( 'spanMoedaIndicador' );"      );

$obRdoTipoIndicador = new Radio;
$obRdoTipoIndicador->setName     ( "stTipoValor"                                                        );
$obRdoTipoIndicador->setLabel    ( "Indicador Econômico"                                                );
$obRdoTipoIndicador->setValue    ( "indicador"                                                          );
$obRdoTipoIndicador->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".$pgOcul."?".Sessao::getId()."&stTipoValor='+this.value, 'spanMoedaIndicador' );" );
//$obRdoTipoIndicador->obEvento->setOnChange( "javascript: montaMoedaIndicador( 'spanMoedaIndicador' );"  );

$obTxtValor = new Numerico;
$obTxtValor->setRotulo    ("Valor"                                          );
$obTxtValor->setTitle     ("Informe o valor da alíquota para a modalidade." );
$obTxtValor->setName      ("nuValor"                                        );
$obTxtValor->setSize      (10                                               );
$obTxtValor->setMaxLength (10                                               );
$obTxtValor->setNull      (false                                            );
$obTxtValor->setNegativo  (false                                            );

$spnMoedaIndicador = new Span;
$spnMoedaIndicador->setId("spnMoedaIndicador");

//COMPONENTES PARA BAIXA
//ATIVIDADE
$obHdnValorComposto = new Hidden;
$obHdnValorComposto->setName           ( "stValorComposto"                 );
$obHdnValorComposto->setValue          ( $_REQUEST["stValorComposto"]      );

$obHdnCodigoModalidade = new Hidden;
$obHdnCodigoModalidade->setName        ( "inCodigoModalidade"              );
$obHdnCodigoModalidade->setValue       ( $_REQUEST["inCodigoModalidade"]   );

$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo         ( "Código"                          );
$obLblValorComposto->setName           ( "stValorComposto"                 );
$obLblValorComposto->setValue          ( $_REQUEST["stValorComposto"]      );

$obLblNomeAtividade = new Label;
$obLblNomeAtividade->setRotulo         ( "Nome da Atividade"               );
$obLblNomeAtividade->setName           ( "stNomeAtividade"                 );
$obLblNomeAtividade->setValue          ( $_REQUEST["stNomeAtividade"]      );

$obLblNomeModalidade = new Label;
$obLblNomeModalidade->setRotulo        ( "Nome da Modalidade"              );
$obLblNomeModalidade->setName          ( "stNomeModalidade"                );
$obLblNomeModalidade->setValue         ( $_REQUEST["stNomeModalidade"]     );

//INSCRICAO ECONOMICA
$obHdninInscricaoEconomica = new Hidden;
$obHdninInscricaoEconomica->setName    ( "inInscricaoEconomica"            );
$obHdninInscricaoEconomica->setValue   ( $_REQUEST["inInscricaoEconomica"] );

$obLblNomeCGM = new Label;
$obLblNomeCGM->setRotulo               ( "Nome"                            );
$obLblNomeCGM->setName                 ( "stNomeCGM"                       );
$obLblNomeCGM->setValue                ( $_REQUEST["stNomeCGM"]            );

$obLblinInscricaoEconomica = new Label;
$obLblinInscricaoEconomica->setRotulo  ( "Inscrição Economica"             );
$obLblinInscricaoEconomica->setName    ( "inInscricaoEconomica"            );
$obLblinInscricaoEconomica->setValue   ( $_REQUEST["inInscricaoEconomica"] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de modalidade de atividades de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $_REQUEST["inNumProcesso"] );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

//  GERAL
//$obDataBaixa = new Data;
//$obDataBaixa->setRotulo                ( "Data da Baixa"                 );
//$obDataBaixa->setName                  ( "dtDataBaixa"                   );
//$obDataBaixa->setValue                 ( date('d/m/Y')                   );
//$obDataBaixa->setSize                  ( 10                              );
//$obDataBaixa->setMaxLength             ( 10                              );
//$obDataBaixa->setNull                  ( false                           );
//$obDataBaixa->setInteiro               ( false                           );

$obHdnDataBaixa = new Hidden;
$obHdnDataBaixa->setName               ( "dtDataBaixa"                     );
$obHdnDataBaixa->setValue              ( date('d/m/Y')                     );

$obHdnDataVigencia = new Hidden;
$obHdnDataVigencia->setName            ( "dtVigenciaModalidade"            );
$obHdnDataVigencia->setValue           ( $_REQUEST["dtVigenciaModalidade"] );

$obLblDataVigencia = new Label;
$obLblDataVigencia->setRotulo          ( "Data de Início"                  );
$obLblDataVigencia->setName            ( "dtVigenciaModalidade"            );
$obLblDataVigencia->setValue           ( $_REQUEST["dtVigenciaModalidade"] );

$obLblDataBaixa = new Label;
$obLblDataBaixa->setRotulo             ( "Data de Baixa"                   );
$obLblDataBaixa->setName               ( "dtDataBaixa"                     );
$obLblDataBaixa->setValue              ( date('d/m/Y')                     );

$obTxtMotivo = new TextArea;
$obTxtMotivo->setRotulo                ( "Motivo"                          );
$obTxtMotivo->setTitle                 ( "Motivo da baixa"                 );
$obTxtMotivo->setName                  ( "stMotivo"                        );
$obTxtMotivo->setNull                  ( false                             );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                       ( $obForm                            );
$obFormulario->setAjuda      ( "UC-05.02.13");
$obFormulario->addHidden                     ( $obHdnCtrl                         );
$obFormulario->addHidden                     ( $obHdnAcao                         );
$obFormulario->addHidden                     ( $obHdnVinculo                      );
$obFormulario->addHidden                     ( $obHdnInCodigoAtividade            );
$obFormulario->addHidden                     ( $obHdnCodVigencia );

$obFormulario->addTitulo ( "Dados para modalidade" );

if ($stAcao == "definir") {
    if ($_REQUEST["boVinculoModalidade"] == "atividade") {
        //$obFormulario->addComponente         ( $obBscAtividade                    );
        $obMontaAtividade->geraFormulario    ( $obFormulario                      );
        $obFormulario->addComponente         ( $obDataInicio                      );
        $obFormulario->addComponenteComposto ( $obTxtModalidade, $obCmbModalidade );
        $obFormulario->agrupaComponentes     ( array ($obRdoTipoPercentual, $obRdoTipoMoeda, $obRdoTipoIndicador) );
        $obFormulario->addComponente         ( $obTxtValor                        );
        $obFormulario->addSpan               ( $spnMoedaIndicador                 );
    } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
        //$obFormulario->addHidden             ( $obHdnInCodigoModalidade           );
        $obFormulario->addComponente         ( $obBscInscricaoEconomica           );
        $obFormulario->addComponente         ( $obDataInicio                      );
        $obFormulario->addComponente         ( $obBscProcesso                     );
        //$obFormulario->addTitulo             ( "Atividades Econômicas"            );
        //$obFormulario->agrupaComponentes     ( array( $obTxtCodigoAtividade, $obTxtNomeAtividade ) );
        //$obMontaAtividade->geraFormulario( $obFormulario                          );
        //$obFormulario->addComponenteComposto ( $obTxtModalidade, $obCmbModalidade );
        //$obFormulario->defineBarra           ( $botoesSpanModalidade              );
        $obFormulario->addSpan               ( $obSpnVisualizarAtividade          );
        $obFormulario->addSpan               ( $spnAtividadeInscricao             );
    }
    $obFormulario->defineBarra               ( $botoesFormulario                  );
} elseif ($stAcao == "baixar") {
    $obFormulario->addHidden                 ( $obHdnValorComposto                );
    $obFormulario->addHidden                 ( $obHdnCodigoModalidade             );
    $obFormulario->addHidden                 ( $obHdnDataVigencia                 );
    $obFormulario->addHidden                 ( $obHdnDataBaixa                    );
    $obFormulario->addHidden                 ( $obHdninInscricaoEconomica         );
    if ($_REQUEST["boVinculoModalidade"] == "atividade") {
        $obFormulario->addComponente         ( $obLblValorComposto                );
        $obFormulario->addComponente         ( $obLblNomeAtividade                );
        $obFormulario->addComponente         ( $obLblNomeModalidade               );
    } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
        $obFormulario->addComponente         ( $obLblinInscricaoEconomica         );
        $obFormulario->addComponente         ( $obLblNomeCGM                      );
    }
    $obFormulario->addComponente             ( $obLblDataVigencia                 );
    $obFormulario->addComponente             ( $obLblDataBaixa                    );
    $obFormulario->addComponente             ( $obTxtMotivo                       );
    $obFormulario->Cancelar();
}
$obFormulario->show ();

if ($stAcao == "definir") {
    if ($_REQUEST["boVinculoModalidade"] == "atividade") {
        //sistemaLegado::executaFrameOculto("document.frm.stValorComposto.focus();");
        sistemaLegado::executaFrameOculto("document.frm.stChaveAtividade.focus();");
    } elseif ($_REQUEST["boVinculoModalidade"] == "inscricao") {
        sistemaLegado::executaFrameOculto("document.frm.inInscricaoEconomica.focus();");
    }
} elseif ($stAcao == "baixar") {
    sistemaLegado::executaFrameOculto("document.frm.stMotivo.focus();");
}

?>

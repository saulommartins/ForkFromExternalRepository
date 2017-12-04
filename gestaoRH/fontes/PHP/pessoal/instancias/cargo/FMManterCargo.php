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
    * Página de Formulario de Inclusao/Alteracao de Cargos
    * Data de criação   : 07/12/2004

    * @author Gustavo Passos Tourinho
    * @author Vandre MIguel Ramos

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-12 12:58:14 -0300 (Ter, 12 Jun 2007) $

    * Caso de uso: uc-04.04.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
// include_once ("../../../../../../gestaoAdministrativa/fontes/PHP/CGM/classes/mapeamento/TEscolaridade.class.php"    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once ( CAM_GA_CGM_MAPEAMENTO."TEscolaridade.class.php" );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalRequisito.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];

$jsOnload = "executaFuncaoAjax('onLoad');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obTPessoalRequisito = new TPessoalRequisito;
$obTEscolaridade = new TEscolaridade;
$obTEscolaridade->recuperaTodos($rsEscolaridade, '', ' ORDER BY cod_escolaridade ');

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$rsCargo = new RecordSet;
$rsRequisitosSelecionados = new RecordSet;
if ($stAcao == "alterar") {
    Sessao::write("stAcao",$stAcao);
    Sessao::write("inCodCargo",$_REQUEST["inCodCargo"]);

    $arChaveAtributoCandidato =  array( "cod_cargo" => $_REQUEST['inCodCargo'] );
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setCodCadastro(4);
    $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
    $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

    $obRPessoalCargo = new RPessoalCargo;
    $obRPessoalCargo->setCodCargo( $_REQUEST["inCodCargo"] );
    $obRPessoalCargo->listarCargo( $rsCargo );

    //recupera requisitos selecionados
    $obTPessoalRequisito->recuperaRequisitosCargo($rsRequisitosSelecionados, 'WHERE cargo_requisito.cod_cargo = '.$_REQUEST["inCodCargo"], ' ORDER BY requisito.descricao ');
    //recupera requisitos disponíveis
    $stFiltroRequisitosDisponiveis = 'WHERE requisito.cod_requisito NOT IN (SELECT cod_requisito
                                                                              FROM pessoal.cargo_requisito
                                                                             WHERE cod_cargo = '.$_REQUEST["inCodCargo"].'
                                                                          GROUP BY cod_requisito)';

} else {
    $obRCadastroDinamico = new RCadastroDinamico;
    $obRCadastroDinamico->setCodCadastro(4);
    $obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $stFiltroRequisitosDisponiveis = '';
}
//recupera requisitos disponíveis
$obTPessoalRequisito->recuperaRequisitosDisponiveisCargo($rsRequisitosDisponiveis, $stFiltroRequisitosDisponiveis, ' ORDER BY descricao ');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnCodCargo =  new Hidden;
$obHdnCodCargo->setName   ( "hdnCodCargo" );
$obHdnCodCargo->setValue  ( $rsCargo->getCampo('cod_cargo') );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo      ( "Descrição"                    );
$obTxtDescricao->setName        ( "stDescricao"                  );
$obTxtDescricao->setValue       ( $rsCargo->getCampo('descricao') );
$obTxtDescricao->setTitle       ( "Informe a descrição do cargo." );
$obTxtDescricao->setNull        ( false                          );
$obTxtDescricao->setSize        ( 100                            );
$obTxtDescricao->setMaxLength   ( 100                            );
$obTxtDescricao->setEspacosExtras ( false );
if ($stAcao == "alterar") {
    $obTxtDescricao->setReadOnly(true);
}

$obCmbEscolaridadeMinima = new Select;
$obCmbEscolaridadeMinima->setRotulo     ( "Escolaridade Mínima"    );
$obCmbEscolaridadeMinima->setName       ( "inCodEscolaridadeMinima");
$obCmbEscolaridadeMinima->setId         ( "inCodEscolaridadeMinima");
$obCmbEscolaridadeMinima->setValue      ( $rsCargo->getCampo('cod_escolaridade') );
$obCmbEscolaridadeMinima->setCampoID    ( "cod_escolaridade"       );
$obCmbEscolaridadeMinima->setCampoDesc  ( "descricao"              );
$obCmbEscolaridadeMinima->setNull       ( false                    );
$obCmbEscolaridadeMinima->addOption     ( "", "Selecione"          );
$obCmbEscolaridadeMinima->preencheCombo ( $rsEscolaridade          );

$obTxtDescricaoAtribuicoes = new TextArea;
$obTxtDescricaoAtribuicoes->setRotulo      ( "Descrição das Atribuições"            );
$obTxtDescricaoAtribuicoes->setName        ( "stAtribuicoes"                        );
$obTxtDescricaoAtribuicoes->setValue       ( $rsCargo->getCampo('atribuicoes')      );
$obTxtDescricaoAtribuicoes->setTitle       ( "Informe a descrição das atribuições." );
$obTxtDescricaoAtribuicoes->setNull        ( false                                  );

$obCmbRequisitos = new SelectMultiplo;
$obCmbRequisitos->setName ('inCodRequisitos');
$obCmbRequisitos->setRotulo ( "Requisitos" );
$obCmbRequisitos->setNull(false);
$obCmbRequisitos->setTitle( "Selecione os requisitos para o exercício do cargo." );

// lista de atributos disponiveis
$obCmbRequisitos->SetNomeLista1('inCodRequisitosDisponiveis');
$obCmbRequisitos->setCampoId1('cod_requisito');
$obCmbRequisitos->setCampoDesc1('descricao');
$obCmbRequisitos->SetRecord1( $rsRequisitosDisponiveis );

// lista de atributos selecionados
$obCmbRequisitos->SetNomeLista2('inCodRequisitosSelecionados');
$obCmbRequisitos->setCampoId2('cod_requisito');
$obCmbRequisitos->setCampoDesc2('descricao');
$obCmbRequisitos->SetRecord2( $rsRequisitosSelecionados );

$obLnkRequisitos = new Link;
$obLnkRequisitos->setRotulo( "&nbsp;" );
$obLnkRequisitos->setHref  ( "JavaScript:abrePopUp('".CAM_GRH_PES_POPUPS."cargo/FMManterRequisito.php','frm','','','','".Sessao::getId()."&stAcao=incluir&cod_cargo=".$_REQUEST["inCodCargo"]."','800','600');" );
$obLnkRequisitos->setValue ( "Cadastrar Requisito" );

$obRdoCargo = new SimNao;
$obRdoCargo->setRotulo          ( "Cargo de Confiança"            );
$obRdoCargo->setTitle           ( "Informe o cargo de confiança." );
$obRdoCargo->setName            ( "rdCargo"                      );
if ($rsCargo->getCampo('cargo_cc') == "t") {
    $obRdoCargo->setChecked("S");
} else {
    $obRdoCargo->setChecked("N");
}

$obRdoFuncao = new SimNao;
$obRdoFuncao->setRotulo         ( "Função Gratificada"                   );
$obRdoFuncao->setTitle          ( "Informe se é uma função gratificada." );
$obRdoFuncao->setName           ( "rdFuncao"                            );
if ($rsCargo->getCampo('funcao_gratificada') == "t") {
    $obRdoFuncao->setChecked("S");
} else {
    $obRdoFuncao->setChecked("N");
}

$obChkEspecialidade = new SimNao();
$obChkEspecialidade->setRotulo             ( "Cargo com Especialidade"                                  );
$obChkEspecialidade->setTitle              ( "Informe se existem especialidades associadas ao cargo."   );
$obChkEspecialidade->setName               ( "boEspecialidade"                                          );
$obChkEspecialidade->setChecked            ( ($boEspecialidade == true)                                  );
$obChkEspecialidade->obRadioSim->obEvento->setOnChange ( "montaParametrosGET('gerarSpanCargoEspecialidade','boEspecialidade');" );
$obChkEspecialidade->obRadioSim->setId("boEspecialidadeSim");
$obChkEspecialidade->obRadioNao->obEvento->setOnChange ( "montaParametrosGET('gerarSpanCargoEspecialidade','boEspecialidade');" );
$obChkEspecialidade->obRadioNao->setId("boEspecialidadeNao");

$obSpnCargo = new Span;
$obSpnCargo->setId ( "spnCargo" );

$obHdnCargo = new HiddenEval();
$obHdnCargo->setId("hdnCargo");
$obHdnCargo->setName("hdnCargo");

//flag para gerar vinculo de cargo aos eventos da folha
$obHdnBoEvento = new Hidden;
$obHdnBoEvento->setId    ( "boVincularEventos" );
$obHdnBoEvento->setName  ( "boVincularEventos" );
$obHdnBoEvento->setValue ( 'false' );

include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                                         );
$obMontaAtributos = new MontaAtributos;
$obMontaAtributos->setTitulo                    ( "Atributos"                                                           );
$obMontaAtributos->setName                      ( "Atributo_"                                                           );
$obMontaAtributos->setRecordSet                 ( $rsAtributos                                                          );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm               ( $obForm                           );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addAba                ( "Cargo"                           );
$obFormulario->addTitulo             ( "Dados do Cargo"                  );
$obFormulario->addHidden             ( $obHdnAcao                        );
$obFormulario->addHidden             ( $obHdnCtrl                        );
$obFormulario->addHidden             ( $obHdnBoEvento                    );
$obFormulario->addHidden             ( $obHdnCodCargo                    );
$obFormulario->addComponente         ( $obTxtDescricao                   );
$obFormulario->addComponente         ( $obCmbEscolaridadeMinima          );
$obFormulario->addComponente         ( $obCmbRequisitos                  );
$obFormulario->addComponente         ( $obLnkRequisitos                  );
$obFormulario->addComponente         ( $obTxtDescricaoAtribuicoes        );
$obFormulario->addComponente         ( $obChkEspecialidade               );
$obFormulario->addComponente         ( $obRdoCargo                       );
$obFormulario->addComponente         ( $obRdoFuncao                      );
$obFormulario->addSpan               ( $obSpnCargo                       );
$obFormulario->addHidden             ($obHdnCargo,true                   );
$obFormulario->addAba                ( "Atributos"                       );
$obMontaAtributos->geraFormulario    ( $obFormulario                     );

if ($stAcao == "incluir") {

    $obBtnOk = new Ok();
    $obBtnOk->obEvento->setOnClick("confirmPopUp('Vincular cargo a eventos da folha', 'Deseja vincular o cargo \'' + document.frm.stDescricao.value +  '\' a todos eventos da folha ?',
                                   'document.frm.boVincularEventos.value = \'true\'; montaParametrosGET(\'submeter\',\'\',true)', 'montaParametrosGET(\'submeter\',\'\',true)');");

    $obBtnCancelar = new Button();
    $obBtnCancelar->setValue("Cancelar");
    $obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao."');");

    $obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));

} else {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$stAcao);
}

$obFormulario->show ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

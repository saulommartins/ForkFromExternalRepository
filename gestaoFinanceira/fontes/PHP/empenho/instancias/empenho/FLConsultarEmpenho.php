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
    * Página de Filtro de Consulta de Empenho
    * Data de Criação   : 03/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 32089 $
    $Name$
    $Autor: $
    $Date: 2007-12-04 12:08:26 -0200 (Ter, 04 Dez 2007) $

    * Casos de uso: uc-02.03.03
*/

/*
$Log$
Revision 1.12  2007/08/31 19:11:15  hboaventura
Inclusão dos atributos dinâmicos no filtro

Revision 1.11  2007/05/04 20:47:41  cako
Bug #8890#

Revision 1.10  2006/07/18 21:10:49  leandro.zis
Bug #6186#

Revision 1.9  2006/07/18 20:24:02  cleisson
Bug #6185#

Revision 1.8  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO.'RCadastroDinamico.class.php' );
include_once ( CAM_GF_EMP_COMPONENTES."IMontaCompraDiretaLicitacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarEmpenho";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
Sessao::remove('link');

$rsOrgao = $rsRecurso = $rsRecordset    = new RecordSet;
$obRegra        = new REmpenhoEmpenhoAutorizacao;
$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

$obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

//$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->setExercicio( Sessao::getExercicio() );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoHistorico->listar( $rsHistorico );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades." );
$obCmbEntidades->setNull   ( false );

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
       $rsRecordset = $rsEntidades;
       $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ( 'cod_entidade' );
$obCmbEntidades->setCampoDesc1 ( 'nom_cgm' );
$obCmbEntidades->SetRecord1    ( $rsEntidades );
// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ( $rsRecordset );

// Define objeto TextBox para Armazenar Exercicio
$obTxtAno = new TextBox;
$obTxtAno->setName      ( "stExercicio"         );
$obTxtAno->setId        ( "stExercicio"         );
$obTxtAno->setValue     ( Sessao::getExercicio()    );
$obTxtAno->setRotulo    ( "Exercício do Empenho");
$obTxtAno->setTitle     ( "Informe o exercício do empenho.");
$obTxtAno->setNull      ( false                 );
$obTxtAno->setMaxLength ( 4                     );
$obTxtAno->setSize      ( 4                     );
$obTxtAno->setInteiro   ( true                  );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                       );
$obTxtOrgao->setTitle               ( "Selecione o orgão orçamentário." );
$obTxtOrgao->setName                ( "inNumOrgaoTxt"               );
$obTxtOrgao->setValue               ( $inNumOrgaoTxt                );
$obTxtOrgao->setSize                ( 10                            );
$obTxtOrgao->setMaxLength           ( 10                            );
$obTxtOrgao->setInteiro             ( true                          );
$obTxtOrgao->obEvento->setOnChange  ( "buscaDado('MontaUnidade');"  );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inNumOrgao"                  );
$obCmbOrgao->setValue               ( $inNumOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaDado('MontaUnidade');"  );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Selecione a unidade orçamentária." );
$obTxtUnidade->setName                ( "inNumUnidadeTxt"               );
$obTxtUnidade->setValue               ( $inNumUnidadeTxt                );
$obTxtUnidade->setSize                ( 10                              );
$obTxtUnidade->setMaxLength           ( 10                              );
$obTxtUnidade->setInteiro             ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inNumUnidade"                  );
$obCmbUnidade->setValue               ( $inNumUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "num_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );

//ELEMENTO DESPESA
$stMascaraRubrica    = $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa"                       );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa."            );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa"                        );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa"                              );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica)                   );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica)                   );
$obBscRubricaDespesa->obCampoCod->setValue    ( ''                                          );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left"                                       );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );"       );
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); } else document.getElementById('stDescricaoDespesa').innerHTML = '&nbsp;';");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=$stMascaraRubrica','".Sessao::getId()."','800','550');" );

// Define Objeto BuscaInner para Dotacao Redutoras
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe uma dotação orçamentária." );
$obBscDespesa->setNulL   ( true                     );
$obBscDespesa->setId     ( "stNomDotacao"           );
$obBscDespesa->setValue  ( $stNomDotacao            );
$obBscDespesa->obCampoCod->setName ( "inCodDotacao" );
$obBscDespesa->obCampoCod->setId   ( "inCodDotacao" );
//Linha baixo utilizada para seguir um tamanho padrão de campo de acordo com o elemento da despesa
//Utilizado somente nesta interface
$obBscDespesa->obCampoCod->setSize      ( strlen($stMascaraRubrica)  );
//$obBscDespesa->obCampoCod->setSize    ( 10                         );
$obBscDespesa->obCampoCod->setMaxLength ( 5                          );
$obBscDespesa->obCampoCod->setValue     ( $inCodDotacao              );
$obBscDespesa->obCampoCod->setAlign     ("left"                      );
$obBscDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('buscaDotacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); } else document.getElementById('stNomDotacao').innerHTML = '&nbsp;';");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoInicial->setInteiro  ( true                  );
$obTxtCodEmpenhoInicial->setNull     ( true                  );

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue( "a" );

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName     ( "inCodEmpenhoFinal" );
$obTxtCodEmpenhoFinal->setValue    ( $inCodEmpenhoFinal  );
$obTxtCodEmpenhoFinal->setRotulo   ( "Número do Empenho" );
$obTxtCodEmpenhoFinal->setTitle    ( "Informe o número do empenho." );
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

//Define o objeto TEXT para armazenar Codigo da Autorizacao
$obTxtCodAutorizacao = new TextBox;
$obTxtCodAutorizacao->setName     ( "inCodAutorizacao"      );
$obTxtCodAutorizacao->setValue    ( $inCodAutorizacao       );
$obTxtCodAutorizacao->setRotulo   ( "Número da Autorização" );
$obTxtCodAutorizacao->setTitle    ( "Informe o número da autorização." );
$obTxtCodAutorizacao->setNull     ( true                    );
$obTxtCodAutorizacao->setInteiro  ( true                    );

//Define o objeto TEXT para armazenar o CPF
$obTxtCPF = new TextBox;
$obTxtCPF->setName              ( "inCPF"                 );
$obTxtCPF->setValue             ( $inCPF                  );
$obTxtCPF->setRotulo            ( "CPF Fornecedor"        );
$obTxtCPF->setTitle             ( "Informe o CPF fornecedor." );
$obTxtCPF->setNull              ( true                    );
$obTxtCPF->setSize              ( 14                      );
$obTxtCPF->setMaxLength         ( 14                      );
$obTxtCPF->obEvento->setOnKeyUp ( "mascaraCPF( this, event );return autoTab(this, 14, event);" );
$obTxtCPF->obEvento->setOnKeyPress( "return(isValido(this, event, '0123456789'));");

//Define o objeto TEXT para armazenar o CNPJ
$obTxtCNPJ = new TextBox;
$obTxtCNPJ->setName              ( "inCNPJ"                );
$obTxtCNPJ->setValue             ( $inCNPJ                 );
$obTxtCNPJ->setRotulo            ( "CNPJ Fornecedor"       );
$obTxtCNPJ->setTitle             ( "Informe o CNPJ fornecedor." );
$obTxtCNPJ->setNull              ( true                    );
$obTxtCNPJ->setSize              ( 18                      );
$obTxtCNPJ->setMaxLength         ( 18                      );
$obTxtCNPJ->obEvento->setOnKeyUp ( "mascaraCNPJ( this, event );return autoTab(this, 18, event);" );
$obTxtCNPJ->obEvento->setOnKeyPress( "return(isValido(this, event, '0123456789'));");

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Fornecedor"      );
$obBscFornecedor->setTitle                  ( "Informe o fornecedor.");
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->setValue                  ( $stNomFornecedor  );
$obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCodFornecedor  );
$obBscFornecedor->obCampoCod->setAlign      ("left"             );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaDado('buscaFornecedorDiverso');");
$obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/*
$obTxtRecurso = new TextBox;
$obTxtRecurso->setRotulo              ( "Recurso"                       );
$obTxtRecurso->setTitle               ( "Selecione o recurso."           );
$obTxtRecurso->setName                ( "inCodRecursoTxt"               );
$obTxtRecurso->setValue               ( $inCodRecursoTxt                );
$obTxtRecurso->setSize                ( 6                               );
$obTxtRecurso->setMaxLength           ( 4                               );
$obTxtRecurso->setInteiro             ( true                            );

$obCmbRecurso = new Select;
$obCmbRecurso->setRotulo              ( "Recurso"                       );
$obCmbRecurso->setName                ( "inCodRecurso"                  );
$obCmbRecurso->setValue               ( $inCodRecurso                   );
$obCmbRecurso->setStyle               ( "width: 200px"                  );
$obCmbRecurso->setCampoID             ( "cod_recurso"                   );
$obCmbRecurso->setCampoDesc           ( "nom_recurso"                   );
$obCmbRecurso->addOption              ( "", "Selecione"                 );
$obCmbRecurso->preencheCombo          ( $rsRecurso                      );
*/

// Define objeto Data para Periodo
$obDtInicial = new Data;
$obDtInicial->setName     ( "stDtInicial"   );
$obDtInicial->setRotulo   ( "Período"       );
$obDtInicial->setTitle    ( 'Informe o período.' );
$obDtInicial->setNull     ( true            );

// Define Objeto Label
$obLabel = new Label;
$obLabel->setValue( " até " );

// Define objeto Data para validade final
$obDtFinal = new Data;
$obDtFinal->setName     ( "stDtFinal"   );
$obDtFinal->setRotulo   ( "Período"     );
$obDtFinal->setTitle    ( ''            );
$obDtFinal->setNull     ( true          );

// Define Objeto Select para Histórico
$obCmbHistorico = new Select;
$obCmbHistorico->setName      ( "inCodHistorico"   );
$obCmbHistorico->setTitle     ( "Selecione o histórico padrão." );
$obCmbHistorico->setRotulo    ( "Histórico Padrão" );
$obCmbHistorico->setId        ( "inCodHistorico"   );
$obCmbHistorico->setValue     ( $inCodHistorico    );
$obCmbHistorico->addOption    ( "", "Selecione"    );
$obCmbHistorico->setCampoId   ( "cod_historico"    );
$obCmbHistorico->setCampoDesc ( "nom_historico"    );
$obCmbHistorico->preencheCombo( $rsHistorico       );
$obCmbHistorico->setNull      ( true               );

$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo              ( "Situação"                             );
$obTxtSituacao->setTitle               ( "Selecione a situação do empenho."      );
$obTxtSituacao->setName                ( "inSituacaoTxt"                        );
$obTxtSituacao->setValue               ( $inSituacaoTxt                         );
$obTxtSituacao->setSize                ( 6                                      );
$obTxtSituacao->setMaxLength           ( 3                                      );
$obTxtSituacao->setInteiro             ( true                                   );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setValue               ( $inSituacao                    );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "1", "Pagos"                   );
$obCmbSituacao->addOption              ( "2", "A Pagar"                 );
$obCmbSituacao->addOption              ( "3", "Liquidados"              );
$obCmbSituacao->addOption              ( "4", "Anulados"                );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"       );
$obBtnClean->setValue                   ( "Limpar"         );
$obBtnClean->setTipo                    ( "button"         );
$obBtnClean->obEvento->setOnClick       ( "limparFiltro();");
$obBtnClean->setDisabled                ( false            );

//adicionados os atributos dinâmicos do empenho
$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );
$obRCadastroDinamico->obRModulo->setCodModulo( 10 );
$obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosDinamicos );

for ( $i = 0; $i < count( $rsAtributosDinamicos->arElementos ); $i++ ) {
    $rsAtributosDinamicos->arElementos[$i]['nao_nulo'] = 't';
}

$obMontaAtributos = new MontaAtributos();
$obMontaAtributos->setName( 'atributos_' );
$obMontaAtributos->setRecordSet( $rsAtributosDinamicos );
$obMontaAtributos->setTitulo( 'Atributos' );

$obIMontaCompraDiretaLicitacaoEmpenho = new IMontaCompraDiretaLicitacaoEmpenho($obForm);

$obBtnOK = new Ok;

$botoesForm = array ( $obBtnOK , $obBtnClean );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                                   );

$obFormulario->addHidden            ( $obHdnAcao                                );
$obFormulario->addHidden            ( $obHdnCtrl                                );

$obFormulario->addTitulo            ( "Dados para Filtro"                       );
$obFormulario->addComponente        ( $obCmbEntidades                           );
$obFormulario->addComponente        ( $obTxtAno                                 );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao                  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade              );
$obFormulario->addComponente        ( $obBscDespesa                             );
$obFormulario->addHidden            ( $obHdnMascClassificacao                   );
$obFormulario->addComponente        ( $obBscRubricaDespesa                      );
$obFormulario->agrupaComponentes    ( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->addComponente        ( $obTxtCodAutorizacao                      );
$obFormulario->addComponente        ( $obBscFornecedor                          );
$obFormulario->addComponente        ( $obTxtCPF                                 );
$obFormulario->addComponente        ( $obTxtCNPJ                                );
//$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso              );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->agrupaComponentes    ( array( $obDtInicial,$obLabel, $obDtFinal ));
$obFormulario->addComponente        ( $obCmbHistorico                           );
$obFormulario->addComponenteComposto( $obTxtSituacao, $obCmbSituacao            );
if ( $rsAtributosDinamicos->getNumLinhas() > 0 ) {
    $obMontaAtributos->geraFormulario( $obFormulario );
}
$obIMontaCompraDiretaLicitacaoEmpenho->geraFormulario( $obFormulario );
$obFormulario->defineBarra          ( $botoesForm                               );

//$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

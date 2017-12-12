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
    * Data de Criação   : 05/05/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

/*
$Log$
Revision 1.10  2007/05/11 13:57:52  cako
Bug #8887#

Revision 1.9  2007/02/23 15:15:05  gelson
Sempre que for autorização tem que ir a reserva. Adicionado em todos arquivos o caso de uso da reserva.

Revision 1.8  2006/07/17 19:26:36  leandro.zis
Bug #6184#

Revision 1.7  2006/07/17 13:27:56  leandro.zis
Bug #6183#

Revision 1.6  2006/07/05 20:47:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php" );
include_once ( "../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
include_once ( CAM_GF_EMP_COMPONENTES."IMontaCompraDiretaLicitacaoEmpenho.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarAutorizacao";
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

Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);
Sessao::remove('link');

$rsOrgao = $rsRecurso = $rsRecordset    = new RecordSet;
$obRegra        = new REmpenhoAutorizacaoEmpenho;
$obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );

$obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();

//$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
//$obRegra->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );

$obRegra->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obRegra->obREmpenhoHistorico->setExercicio( Sessao::getExercicio() );
$obRegra->obREmpenhoHistorico->listar( $rsHistorico );

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

include_once( CAM_GF_ORC_COMPONENTES."ISelectMultiploEntidadeUsuario.class.php" );
$obCmbEntidades = new ISelectMultiploEntidadeUsuario();

// Define objeto TextBox para Armazenar Exercicio
$obTxtAno = new TextBox;
$obTxtAno->setName      ( "stExercicio"         );
$obTxtAno->setId        ( "stExercicio"         );
$obTxtAno->setValue     ( Sessao::getExercicio()    );
$obTxtAno->setRotulo    ( "Exercício"           );
$obTxtAno->setTitle     ( "Informe o exercício.");
$obTxtAno->setNull      ( false                 );
$obTxtAno->setMaxLength ( 4                     );
$obTxtAno->setSize      ( 4                     );
$obTxtAno->setInteiro   ( true                  );

//Define o objeto TEXT para Codigo da Autorização Inicial
$obTxtCodAutorizacaoInicial = new TextBox;
$obTxtCodAutorizacaoInicial->setName     ( "inCodAutorizacaoInicial" );
$obTxtCodAutorizacaoInicial->setValue    ( $inCodAutorizacaoInicial  );
$obTxtCodAutorizacaoInicial->setRotulo   ( "Número da Autorização"   );
$obTxtCodAutorizacaoInicial->setTitle    ( "Informe o número da autorização."   );
$obTxtCodAutorizacaoInicial->setInteiro  ( true                  );
$obTxtCodAutorizacaoInicial->setNull     ( true                  );

//Define objeto Label
$obLblAutorizacao = new Label;
$obLblAutorizacao->setValue( "a" );

//Define o objeto TEXT para Codigo da Autorização Final
$obTxtCodAutorizacaoFinal = new TextBox;
$obTxtCodAutorizacaoFinal->setName     ( "inCodAutorizacaoFinal" );
$obTxtCodAutorizacaoFinal->setValue    ( $inCodAutorizacaoFinal  );
$obTxtCodAutorizacaoFinal->setRotulo   ( "Número da Autorização" );
$obTxtCodAutorizacaoFinal->setInteiro  ( true                );
$obTxtCodAutorizacaoFinal->setNull     ( true                );

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

// Define Objeto BuscaInner para Dotacao Redutoras
include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltro.class.php" );
$obPopUpDotacao = new IPopUpDotacaoFiltro($obCmbEntidades);

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

//ELEMENTO DESPESA
$stMascaraRubrica    = $obRegra->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
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
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); }");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/*
$obTxtRecurso = new TextBox;
$obTxtRecurso->setRotulo              ( "Recurso"                       );
$obTxtRecurso->setTitle               ( "Selecione o recurso."          );
$obTxtRecurso->setName                ( "inCodRecursoTxt"               );
$obTxtRecurso->setValue               ( $inCodRecursoTxt                );
$obTxtRecurso->setSize                ( 6                               );
$obTxtRecurso->setMaxLength           ( 3                               );
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
$obTxtSituacao->setTitle               ( "Selecione a situação do empenho."     );
$obTxtSituacao->setName                ( "inSituacaoTxt"                        );
$obTxtSituacao->setValue               ( $inSituacaoTxt                         );
$obTxtSituacao->setSize                ( 6                                      );
$obTxtSituacao->setMaxLength           ( 3                                      );
$obTxtSituacao->setInteiro             ( true                                   );

$obIMontaCompraDiretaLicitacaoEmpenho = new IMontaCompraDiretaLicitacaoEmpenho($obForm);

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setValue               ( $inSituacao                    );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "1", "Empenhada"               );
$obCmbSituacao->addOption              ( "2", "Não Empenhada"           );
$obCmbSituacao->addOption              ( "3", "Anulada"                 );

$obBtnClean = new Button;
$obBtnClean->setName                    ( "btnClean"       );
$obBtnClean->setValue                   ( "Limpar"         );
$obBtnClean->setTipo                    ( "button"         );
$obBtnClean->obEvento->setOnClick       ( "limparFiltro();");
$obBtnClean->setDisabled                ( false            );

$obBtnOK = new Ok;

$botoesForm     = array ( $obBtnOK , $obBtnClean );

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
$obFormulario->agrupaComponentes    ( array( $obTxtCodAutorizacaoInicial, $obLblAutorizacao, $obTxtCodAutorizacaoFinal ) );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao                  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade              );
$obFormulario->addComponente        ( $obBscFornecedor                          );
$obFormulario->addComponente        ( $obPopUpDotacao                           );
$obFormulario->addHidden            ( $obHdnMascClassificacao                   );
$obFormulario->addComponente        ( $obBscRubricaDespesa                      );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
//$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso              );
$obFormulario->agrupaComponentes    ( array( $obDtInicial,$obLabel, $obDtFinal ));
$obFormulario->addComponente        ( $obCmbHistorico                           );
$obFormulario->addComponenteComposto( $obTxtSituacao, $obCmbSituacao            );
$obIMontaCompraDiretaLicitacaoEmpenho->geraFormulario( $obFormulario );

$obFormulario->defineBarra          ( $botoesForm                               );

//$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

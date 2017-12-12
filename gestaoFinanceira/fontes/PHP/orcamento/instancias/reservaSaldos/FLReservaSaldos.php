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
    * Página de Filtro de Reserva de Saldos
    * Data de Criação   : 05/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.08
*/

/*
$Log$
Revision 1.15  2007/08/14 14:41:32  bruce
Bug#9908#

Revision 1.14  2007/05/21 18:59:17  melo
Bug #9229#

Revision 1.13  2006/07/17 18:57:48  andre.almeida
Bug #6401#

Revision 1.12  2006/07/06 19:30:10  cako
Bug #6026#

Revision 1.11  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if( ($stAcao<>"consultar") && ($stAcao<> "imprimir") ) include_once( CAM_GF_INCLUDE."validaGF.inc.php");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ReservaSaldos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');

//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$rsRecordset = new RecordSet;
$obREmpenhoEmpenhoAutorizacao = new REmpenhoEmpenhoAutorizacao;
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( Sessao::getExercicio() );
$obREmpenhoEmpenhoAutorizacao->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidades->eof() ) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo( 'nom_cgm');
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();
while ( !$rsOrgao->eof() ) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo('num_orgao')] = ' - '.$rsOrgao->getCampo( 'nom_orgao');
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;

if ($stAcao == "imprimir") {
    $obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
    $obForm->setTarget( "oculto" );
} else {
    $obForm->setAction( $pgList );
    $obForm->setTarget( "telaPrincipal");
}

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCReservaSaldos.php" );

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
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
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

// Define objeto text para o codigo da entidade
$obTxtCodigoEntidade = new TextBox;
$obTxtCodigoEntidade->setName        ( "inCodigoEntidade"             );
$obTxtCodigoEntidade->setId          ( "inCodigoEntidade"             );
$obTxtCodigoEntidade->setValue       ( $inCodigoEntidade              );
$obTxtCodigoEntidade->setRotulo      ( "Entidade"                     );
$obTxtCodigoEntidade->setTitle       ( "Selecione a Entidade"         );
$obTxtCodigoEntidade->obEvento->setOnChange( "limparCampos();"        );
$obTxtCodigoEntidade->setInteiro     ( true                           );
$obTxtCodigoEntidade->setNull        ( false                          );

/*
// Define Objeto Select para Nome da Entidade
$obCmbNomeEntidade = new Select;
$obCmbNomeEntidade->setName          ( "stNomeEntidade"               );
$obCmbNomeEntidade->setId            ( "stNomeEntidade"               );
$obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );
$obCmbNomeEntidade->addOption        ( "", "Selecione"                );
$obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidade->setStyle         ( "width: 520"                   );
$obCmbNomeEntidade->preencheCombo    ( $rsEntidades                   );
$obCmbNomeEntidade->setNull          ( false                          );
*/
// Define Objeto BuscaInner para Despesa
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe a dotação orçamentária para o filtro."                       );
$obBscDespesa->setNulL   ( true                     );
$obBscDespesa->setId     ( "stNomDespesa"           );
$obBscDespesa->setValue  ( $stNomDespesa            );
$obBscDespesa->obCampoCod->setName ( "inCodDespesa" );
$obBscDespesa->obCampoCod->setSize ( 10 );
$obBscDespesa->obCampoCod->setMaxLength( 5 );
$obBscDespesa->obCampoCod->setValue ( $inCodDespesa );
$obBscDespesa->obCampoCod->setAlign ("left");
//$obBscDespesa->obCampoCod->obEvento->setOnBlur("buscaDado('buscaDespesa');");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDespesa','stNomDespesa','autorizacaoEmpenho&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
$obBscDespesa->setValoresBusca( CAM_GF_ORC_POPUPS.'despesa/OCDespesa.php?'.Sessao::getId(), $obForm->getName() );

//Define o objeto TEXT para armazenar o Codigo da Reserva
$obTxtCodReserva = new TextBox;
$obTxtCodReserva->setName     ( "inCodReserva" );
$obTxtCodReserva->setInteiro  ( true               );
$obTxtCodReserva->setRotulo   ( "Número da Reserva" );
$obTxtCodReserva->setTitle    ( "Informe o número da reserva para o filtro." );
$obTxtCodReserva->setNull     ( true );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidade->obDataFinal->setName      ( "stDtFinal" );
if ($stAcao == 'imprimir') {
    $obPeriodicidade->setNull       ( false );
}

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

/*
// Define objeto BuscaInner para o Recurso
$obBscRecurso = new BuscaInner();
$obBscRecurso->setRotulo                 ( "Recurso"                                           );
$obBscRecurso->setTitle                  ( "Informe o Recurso"                                 );
$obBscRecurso->setId                     ( "stDescricaoRecurso"                                );
$obBscRecurso->setValue                  ( $stDescricaoRecurso                                 );
$obBscRecurso->setNull                   ( true                                                );
$obBscRecurso->obCampoCod->setName       ( "inCodRecurso"                                      );
$obBscRecurso->obCampoCod->setSize       ( 10                                                  );
$obBscRecurso->obCampoCod->setMaxLength  ( strlen($obROrcamentoConfiguracao->getMascRecurso()) );
$obBscRecurso->obCampoCod->setValue      ( $inCodRecurso                                       );
$obBscRecurso->obCampoCod->setAlign      ( "left"                                              );
$obBscRecurso->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$obROrcamentoConfiguracao->getMascRecurso()."', this, event);");
$obBscRecurso->setFuncaoBusca   ("abrePopUp('".CAM_GF_ORC_POPUPS."recurso/FLRecurso.php','frm','inCodRecurso','stDescricaoRecurso','','".Sessao::getId()."','800','550');");
$obBscRecurso->setValoresBusca  ( CAM_GF_ORC_POPUPS.'recurso/OCRecurso.php?'.Sessao::getId(), $obForm->getName() );
*/

//orgao e unidade inicial
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
$obTxtOrgao->setName                ( "inNumOrgaoTxt"              );
$obTxtOrgao->setValue               ( $inNumOrgaoTxt               );
$obTxtOrgao->setSize                ( 6                            );
$obTxtOrgao->setMaxLength           ( 3                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaDado('montaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inNumOrgao"                  );
$obCmbOrgao->setValue               ( $inNumOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaDado('montaUnidade');"  );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
$obTxtUnidade->setName                ( "inNumUnidadeTxt"               );
$obTxtUnidade->setValue               ( $inNumUnidadeTxt                );
$obTxtUnidade->setSize                ( 6                               );
$obTxtUnidade->setMaxLength           ( 3                               );
$obTxtUnidade->setInteiro             ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inNumUnidade"                  );
$obCmbUnidade->setValue               ( $inNumUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );

$obCmbSituacao= new Select;
if ($stAcao == 'imprimir') {
    $obCmbSituacao->setRotulo             ( "Situação no Período"                      );
    $obCmbSituacao->setTitle              ( "Selecione a situação no período para o filtro" );
} else {
    $obCmbSituacao->setRotulo             ( "Situação"                      );
    $obCmbSituacao->setTitle              ( "Selecione a situação para o filtro" );
}
$obCmbSituacao->setName               ( "stSituacao"                    );
$obCmbSituacao->setStyle              ( "width: 200px"                  );
$obCmbSituacao->addOption             ( "", "Selecione"                 );
$obCmbSituacao->addOption             ( "ativas"  , "Ativas"            );
$obCmbSituacao->addOption             ( "inativas", "Inativas"          );
$obCmbSituacao->addOption             ( "anuladas", "Anuladas"          );

$obCmbListar= new Select;
$obCmbListar->setRotulo               ( "Listar Reservas"               );
$obCmbListar->setTitle                ( "Selecione qual lista de reservas para o filtro." );
$obCmbListar->setName                 ( "stReservas"                    );
$obCmbListar->setStyle                ( "width: 200px"                  );
$obCmbListar->addOption               ( "", "Selecione"                 );
$obCmbListar->addOption               ( "manuais", "Manuais"            );
$obCmbListar->addOption               ( "automaticas", "Automáticas"    );

$obHdnListar = new Hidden;
$obHdnListar->setName("stReservas");
$obHdnListar->setValue("manuais");

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);
//sessao->assinaturas['papeis'] = array();

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario = new Formulario;
$obFormulario->setAjuda ( "UC-02.01.08"           );
$obFormulario->addForm  ( $obForm                 );

$obFormulario->addHidden( $obHdnAcao              );
$obFormulario->addHidden( $obHdnCtrl              );

$obFormulario->addTitulo( "Dados para Filtro"  );
$obFormulario->addHidden    ( $obHdnCaminho    );
$obFormulario->addComponente( $obCmbEntidades  );
$obFormulario->addComponente( $obBscDespesa    );
$obFormulario->addComponente( $obTxtCodReserva );
$obFormulario->addComponente( $obPeriodicidade );
//$obFormulario->addComponente( $obBscRecurso );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
if ($stAcao == 'imprimir') {
    $obFormulario->addComponenteComposto( $obTxtOrgao  , $obCmbOrgao   );
    $obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade );
}
$obFormulario->addComponente( $obCmbSituacao );
if ($stAcao=='anular') {
    $obFormulario->addHidden( $obHdnListar );
} else {
    $obFormulario->addComponente( $obCmbListar   );
}

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();
include_once ($pgJS);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

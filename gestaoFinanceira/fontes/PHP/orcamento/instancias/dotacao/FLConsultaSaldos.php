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
    * Página de Filtro de Consulta Saldos de Dotação
    * Data de Criação   : 21/06/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-08-14 11:42:00 -0300 (Ter, 14 Ago 2007) $

    * Casos de uso: uc-02.01.26
*/

/*
$Log$
Revision 1.12  2007/08/14 14:39:56  bruce
Bug#9908#

Revision 1.11  2006/07/27 19:47:08  leandro.zis
Bug #6415#

Revision 1.10  2006/07/14 19:51:31  leandro.zis
Bug #6415#

Revision 1.9  2006/07/05 20:42:50  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"       );
//Define o nome dos arquivos PHP
$stPrograma = "ConsultaSaldos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );
/*
$rsRecordset = new RecordSet;
$obRegra = new ROrcamentoDespesa;
$obRegra->obROrcamentoClassificacaoDespesa->setExercicio( Sessao::getExercicio() );
$stMascaraRubrica = $obRegra->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obRegra->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRegra->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
*/

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal");

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."dotacao/OCConsultaSaldos.php" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

// Define objeto span para pesquisa por entidades ou dotação
$obSpnPesquisa = new Span();
$obSpnPesquisa->setId( "spnPesquisa" );
$obSpnPesquisa->setValue( "" );

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades." );
$obCmbEntidades->setNull   ( false );

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
/*
// Define Objeto Select para Nome da Entidade
$obCmbNomeEntidade = new Select;
$obCmbNomeEntidade->setRotulo        ( "Entidade" );
$obCmbNomeEntidade->setName          ( "inCodEntidade_"               );
$obCmbNomeEntidade->setId            ( "inCodEntidade_"               );
$obCmbNomeEntidade->setValue         ( $inCodigoEntidade              );
$obCmbNomeEntidade->addOption        ( "", "Selecione"                );
$obCmbNomeEntidade->obEvento->setOnChange( "limparCampos();"          );
$obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
$obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
$obCmbNomeEntidade->setStyle         ( "width: 520"                   );
$obCmbNomeEntidade->preencheCombo    ( $rsEntidades                   );
$obCmbNomeEntidade->setNull          ( false                          );

// Define Objeto BuscaInner para Dotacao Redutoras
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe uma dotação orçamentária" );
$obBscDespesa->setNull   ( true                     );
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
$obBscDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('buscaDotacao','".$pgOcul."','".
$pgList."','','".Sessao::getId()."'); }");
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','alteracaoOrcamento&inCodEntidade='+document.frm.inCodEntidade_.value,'".Sessao::getId()."','800','550');");
*/
//Radios de Tipo de Pesquisa
$obRdbTipoPesquisa = new Radio;
$obRdbTipoPesquisa->setRotulo ( "Tipo de Pesquisa"  );
$obRdbTipoPesquisa->setChecked( true                );
$obRdbTipoPesquisa->setName   ( "stTipoPesquisa"    );
$obRdbTipoPesquisa->setValue  ( "entidade"          );
$obRdbTipoPesquisa->setTitle  ( "Selecione o tipo de pesquisa."  );
$obRdbTipoPesquisa->setLabel  ( "Por Entidades"     );
$obRdbTipoPesquisa->setNull   ( false               );
$obRdbTipoPesquisa->obEvento->setOnClick("buscaDado('montaFiltroEntidades');");

$obRdbTipoDotacao = new Radio;
$obRdbTipoDotacao->setName   ( "stTipoPesquisa"       );
$obRdbTipoDotacao->setValue  ( "dotacao"              );
$obRdbTipoDotacao->setLabel  ( "Por Dotação"          );
$obRdbTipoDotacao->setNull   ( false                  );
$obRdbTipoDotacao->obEvento->setOnClick("buscaDado('montaFiltroDotacao');");

$obRdbTipoRecurso = new Radio;
$obRdbTipoRecurso->setName   ( "stTipoPesquisa"       );
$obRdbTipoRecurso->setValue  ( "recurso"              );
$obRdbTipoRecurso->setLabel  ( "Por Recurso"          );
$obRdbTipoRecurso->setNull   ( false                  );
$obRdbTipoRecurso->obEvento->setOnClick("buscaDado('montaFiltroRecurso');");

// Define objeto TextBox para Armazenar Exercicio
$obTxtExercicio = new TextBox;
$obTxtExercicio->setName      ( "stExercicio"         );
$obTxtExercicio->setId        ( "stExercicio"         );
$obTxtExercicio->setValue     ( Sessao::getExercicio()    );
$obTxtExercicio->setRotulo    ( "Exercício"           );
$obTxtExercicio->setTitle     ( "Informe o exercício." );
$obTxtExercicio->setNull      ( false                 );
$obTxtExercicio->setMaxLength ( 4                     );
$obTxtExercicio->setSize      ( 4                     );

//$obPeriodo = new Periodo;
//$obPeriodo->setRotulo   ( "Período"       );
//$obPeriodo->setTitle    ( ''              );
//$obPeriodo->setNull     ( true            );
// Define Objeto TextBox para Data Final

$obTxtDtFinal = new Data();
$obTxtDtFinal->setRotulo( "Situação até" );
$obTxtDtFinal->setTitle ( "Informe a data final da situação." );
$obTxtDtFinal->setName  ( "stDataFinal"  );
$obTxtDtFinal->setId    ( "stDataFinal"  );
$obTxtDtFinal->setNull  ( false          );

$obHdnDtInicial = new Hidden;
$obHdnDtInicial->setName ( "stDataInicial" );
$obHdnDtInicial->setValue( '01/01/'.Sessao::getExercicio() );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setId("stEval");
//$obHdnEval->setValue( "selecionaTodosSelect(document.frm.inCodEntidade);" );

SistemaLegado::executaFramePrincipal('buscaDado("montaFiltroEntidades");');

//****************************************//
//Monta FORMULARIO
//****************************************//

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->setAjuda ( 'UC-02.01.26' );
$obFormulario->addHidden( $obHdnAcao          );
$obFormulario->addHidden( $obHdnCtrl          );
$obFormulario->addHidden( $obHdnDtInicial     );
$obFormulario->addHidden( $obHdnEval , true);
$obFormulario->agrupaComponentes( array( $obRdbTipoPesquisa, $obRdbTipoDotacao, $obRdbTipoRecurso) );
$obFormulario->addSpan  ( $obSpnPesquisa      );
$obFormulario->addComponente( $obTxtExercicio );
$obFormulario->addComponente( $obTxtDtFinal   );
//$obFormulario->addComponente( $obCmbEntidades);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

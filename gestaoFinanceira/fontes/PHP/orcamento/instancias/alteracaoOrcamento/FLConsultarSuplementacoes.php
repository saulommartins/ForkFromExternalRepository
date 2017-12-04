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
    * Página de Filtro para Consulta de Suplementação
    * Data de Criação: 18/05/2005

    * @author Analista: Dieine
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30813 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.24
*/

/*
$Log$
Revision 1.10  2007/05/21 18:54:36  melo
Bug #9229#

Revision 1.9  2006/07/28 17:40:00  leandro.zis
Bug #6690#

Revision 1.8  2006/07/24 20:19:42  andre.almeida
Bug #6408#

Revision 1.7  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php" );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoSuplementacao.class.php"       );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );

$stPrograma = "ConsultarSuplementacoes";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
include_once( $pgJs );

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );
Sessao::remove('link');
//sessao->link    = array();
//sessao->transf5 = "";

$rsRecordset                     = new RecordSet;
$obROrcamentoSuplementacao       = new ROrcamentoSuplementacao;
$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$stOrdem = "ORDER BY cod_entidade";
$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades, $stOrdem );
$obROrcamentoSuplementacao->setExercicio( Sessao::getExercicio() );
$obROrcamentoSuplementacao->listarTipo( $rsTipoSuplementacao );

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnValidacao = new HiddenEval;
$obHdnValidacao->setName("boValidacao");
$obHdnValidacao->setValue( " " ); //Preenchido a partir do JS

// Define SELECT multiplo para codigo da entidade
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Informe as Entidades." );
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

// Define Objeto BuscaInner para Norma
$obBscNorma = new BuscaInner;
$obBscNorma->setRotulo ( "Lei/Decreto"              );
$obBscNorma->setTitle  ( "Informe a Lei/Decreto."    );
$obBscNorma->setNull   ( true                       );
$obBscNorma->setId     ( "stNomTipoNorma"           );
$obBscNorma->setValue  ( $stNomTipoNorma            );
$obBscNorma->obCampoCod->setName     ( "inCodNorma" );
$obBscNorma->obCampoCod->setId       ( "inCodNorma" );
$obBscNorma->obCampoCod->setSize     ( 10           );
$obBscNorma->obCampoCod->setMaxLength( 7            );
$obBscNorma->obCampoCod->setValue    ( $inCodNorma  );
$obBscNorma->obCampoCod->setAlign    ( "left"       );
$obBscNorma->obCampoCod->obEvento->setOnBlur( "buscaDado('buscaNorma');" );
$obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");

// define objeto Data
$obDtInicio = new Data;
$obDtInicio->setName     ( "stDtInicio" );
$obDtInicio->setRotulo   ( "Período" );
$obDtInicio->setTitle    ( 'Informe o intervalo entre datas.' );
$obDtInicio->setNull     ( true );
// define objeto Label
$obLblPeriodo = new Label;
$obLblPeriodo->setValue( " até " );
// define objeto Data
$obDtTermino = new Data;
$obDtTermino->setName     ( "stDtTermino" );
$obDtTermino->setRotulo   ( "Período" );
$obDtTermino->setTitle    ( 'Informe o intervalo entre datas.' );
$obDtTermino->setNull     ( true );

// Define Objeto BuscaInner para Dotacao Orcamentaria
$obBscDespesaOrcamentaria = new BuscaInner;
$obBscDespesaOrcamentaria->setRotulo ( "Dotação Orçamentária"           );
$obBscDespesaOrcamentaria->setTitle  ( "Informe a Dotação Orçamentária." );
$obBscDespesaOrcamentaria->setNulL   ( true                             );
$obBscDespesaOrcamentaria->setId     ( "stNomDotacaoOrcamentaria"       );
$obBscDespesaOrcamentaria->setValue  ( $stNomDotacaoOrcamentaria        );
$obBscDespesaOrcamentaria->obCampoCod->setName     ( "inCodDotacaoOrcamentaria" );
$obBscDespesaOrcamentaria->obCampoCod->setId       ( "inCodDotacaoOrcamentaria" );
$obBscDespesaOrcamentaria->obCampoCod->setSize     ( 10 );
$obBscDespesaOrcamentaria->obCampoCod->setMaxLength( 5 );
$obBscDespesaOrcamentaria->obCampoCod->setValue    ( $inCodDotacaoOrcamentaria );
$obBscDespesaOrcamentaria->obCampoCod->setAlign    ( "left" );
$obBscDespesaOrcamentaria->obCampoCod->obEvento->setOnBlur( "buscaDado('buscaDespesaOrcamentaria');" );
$obBscDespesaOrcamentaria->setFuncaoBusca( "abrePopUp( '".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php', 'frm', 'inCodDotacaoOrcamentaria', 'stNomDotacaoOrcamentaria', 'inCodEntidade='+document.frm.inCodEntidade.value, '".Sessao::getId()."','800','550');" );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto TextBox para Codigo do tipo de suplementação
$obTxtTipoSuplementacao = new TextBox;
$obTxtTipoSuplementacao->setRotulo  ( "Tipo de Suplementação"          );
$obTxtTipoSuplementacao->setTitle   ( "Informe o Tipo de Suplementação." );
$obTxtTipoSuplementacao->setName    ( "inCodTipoSuplementacao"         );
$obTxtTipoSuplementacao->setValue   ( $inCodTipoSuplementacao          );
$obTxtTipoSuplementacao->setNull    ( true                             );
$obTxtTipoSuplementacao->setInteiro ( true                             );

// Define Objeto Select para Tipo de Suplementação
$obCmbTipoSuplementacao = new Select;
$obCmbTipoSuplementacao->setRotulo    ( "Tipo de Suplementação"  );
$obCmbTipoSuplementacao->setName      ( "stTipoSuplementacao" );
$obCmbTipoSuplementacao->setId        ( "stTipoSuplementacao" );
$obCmbTipoSuplementacao->setTitle     ( "Informe o Tipo de Suplementação." );
$obCmbTipoSuplementacao->setValue     ( $inCodTipoSuplementacao  );
$obCmbTipoSuplementacao->addOption    ( "", "Selecione" );
$obCmbTipoSuplementacao->setCampoId   ( "cod_tipo"      );
$obCmbTipoSuplementacao->setCampoDesc ( "nom_tipo"      );
$obCmbTipoSuplementacao->preencheCombo( $rsTipoSuplementacao );
$obCmbTipoSuplementacao->setNull      ( true );
//
// Define Objeto Select para Situação da Suplementação
$obCmbSituacao = new Select;
$obCmbSituacao->setRotulo    ( "Situação"           );
$obCmbSituacao->setName      ( "inCodSituacao"      );
$obCmbSituacao->setTitle     ( "Informe a Situação." );
$obCmbSituacao->setValue     ( $inCodSituação       );
$obCmbSituacao->addOption    ( "", "Selecione"      );
$obCmbSituacao->addOption    ( "1", "Todas"         );
$obCmbSituacao->addOption    ( "2", "Válidas"       );
$obCmbSituacao->addOption    ( "3", "Anuladas"      );
$obCmbSituacao->setStyle     ( "width: 150px"       );
$obCmbSituacao->setNull      ( false                );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda ( "UC-02.01.07" );
$obFormulario->addForm  ( $obForm       );
$obFormulario->addHidden( $obHdnCtrl    );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addHidden( $obHdnValidacao, true );

$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades );
$obFormulario->addComponente( $obBscNorma     );
$obFormulario->addComponente( $obBscDespesaOrcamentaria );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->agrupaComponentes( array( $obDtInicio, $obLblPeriodo ,$obDtTermino) );
//$obFormulario->addComponente( $obCmbTipoSuplementacao );
$obFormulario->addComponenteComposto( $obTxtTipoSuplementacao, $obCmbTipoSuplementacao );
$obFormulario->addComponente( $obCmbSituacao          );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

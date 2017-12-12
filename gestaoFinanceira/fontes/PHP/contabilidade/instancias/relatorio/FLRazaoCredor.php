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
    * Página de Formulario do relatório Razão do Credor
    * Data de Criação   : 08/06/2005

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: FLRazaoCredor.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-02.02.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeRelatorioRazaoCredor.class.php" );

include_once 'JSRazaoCredor.js';

$obRegra = new RContabilidadeRelatorioRazaoCredor;
$rsRecordset = $rsOrgao = $rsRecurso = new RecordSet;

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$stMascaraRubrica    = $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

$arFiltroNomRelatorio = Sessao::read('filtroNomRelatorio');
while ( !$rsEntidades->eof() ) {
    $arFiltroNomRelatorio['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

while ( !$rsOrgao->eof() ) {
    $arFiltroNomRelatorio['orgao'][$rsOrgao->getCampo( 'nom_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();
Sessao::write('filtroNomRelatorio', $arFiltroNomRelatorio);

$obForm = new Form;
$obForm->setAction( CAM_GF_CONT_INSTANCIAS."relatorio/OCGeraRelatorioRazaoCredor.php" );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "" );
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

$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidade->setNull     ( false              );
$obDtPeriodicidade->setValue    ( 4                  );
$obDtPeriodicidade->setValidaExercicio( true );

$obTxtCodEmpenho = new TextBox;
$obTxtCodEmpenho->setRotulo     ( "Número do Empenho"           );
$obTxtCodEmpenho->setTitle      ( "Informe o código do empenho" );
$obTxtCodEmpenho->setName       ( "inCodEmpenho"                );
$obTxtCodEmpenho->setValue      ( $inCodEmpenho                 );
$obTxtCodEmpenho->setSize       ( 10                            );
$obTxtCodEmpenho->setMaxLength  ( 10                            );
$obTxtCodEmpenho->setInteiro    ( true                          );
$obTxtCodEmpenho->setNull       ( true                          );

$obLblExercicio = new Label;
$obLblExercicio->setValue ( " / ".Sessao::getExercicio() );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Informe o órgão para Filtro");
$obTxtOrgao->setName                ( "inNumOrgaoTxt"              );
$obTxtOrgao->setValue               ( $inNumOrgaoTxt               );
$obTxtOrgao->setSize                ( 10                            );
$obTxtOrgao->setMaxLength           ( 10                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inNumOrgao"                  );
$obCmbOrgao->setValue               ( $inNumOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Informe a unidade para filtro" );
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
//$size =  strlen($stMascaraRubrica) + 10;
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa para Filtro" );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange ("if (this.value) { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."'); }");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once( CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Credor"          );
$obBscFornecedor->setTitle                  ( ""                );
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->setValue                  ( $stNomFornecedor  );
$obBscFornecedor->setNull                   ( false             );
$obBscFornecedor->obCampoCod->setName       ( "inCGM"           );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCGM            );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            );
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stNomFornecedor','','".Sessao::getId()."','800','550');");
$obBscFornecedor->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioDemoLiquidacao = new SimNao();
$obRadioDemoLiquidacao->setRotulo ( 'Demonstrar Liquidação' );
$obRadioDemoLiquidacao->setName   ( 'boDemoLiquidacao'      );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioDemoRestos = new SimNao();
$obRadioDemoRestos->setRotulo ( 'Demonstrar Restos a Pagar' );
$obRadioDemoRestos->setName   ( 'boDemoRestos'              );

$obFormulario = new Formulario;
$obFormulario->setAjuda('UC-02.02.16');
$obFormulario->addForm      ( $obForm              );
$obFormulario->addTitulo    ( "Dados para Filtro"  );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obDtPeriodicidade   );
$obFormulario->agrupaComponentes( array( $obTxtCodEmpenho, $obLblExercicio ) );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obIMontaRecursoDestinacao->geraFormulario ($obFormulario );
$obFormulario->addComponente( $obBscFornecedor     );
$obFormulario->addComponente( $obRadioDemoLiquidacao );
$obFormulario->addComponente( $obRadioDemoRestos     );

$obFormulario->OK();
$obFormulario->show();

?>

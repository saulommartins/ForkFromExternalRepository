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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 25/02/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Id: FLRPCredor.php 64417 2016-02-18 18:03:51Z michel $

    * Casos de uso : uc-02.03.10
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once 'JSRPCredor.js';

$rsRecordset = $rsOrgao = $rsUnidade = $rsRecurso = new RecordSet;

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->recuperaExerciciosRP( $rsExercicio );

$arFiltroNom = Sessao::read('filtroNomRelatorio');

$obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

$obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );

while ( !$rsRecurso->eof() ) {
    $arFiltroNom['recurso'][$rsRecurso->getCampo( 'cod_recurso' )] = $rsRecurso->getCampo( 'nom_recurso' );
    $rsRecurso->proximo();
}
$rsRecurso->setPrimeiroElemento();

Sessao::write('filtroNomRelatorio', $arFiltroNom);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCEmpenhoRPCredor.php" );

$obHdnModulo = new Hidden;
$obHdnModulo->setName ( "inCodModulo" );
$obHdnModulo->setValue( $request->get('modulo') );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
$obCmbEntidades->setNull   ( false );

// Ações disparadas por eventos
$obCmbEntidades->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');

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

$obCmbExercicio = new Select;
$obCmbExercicio->setRotulo              ( "Exercício"                   );
$obCmbExercicio->setName                ( "inExercicio"                 );
$obCmbExercicio->setTitle               ( "Informe o exercício para o filtro." );
$obCmbExercicio->setValue               ( $inExercicio                  );
$obCmbExercicio->setStyle               ( "width: 200px"                );
$obCmbExercicio->setCampoID             ( "exercicio"                   );
$obCmbExercicio->setCampoDesc           ( "exercicio"                   );
$obCmbExercicio->addOption              ( "", "Selecione"               );
$obCmbExercicio->preencheCombo          ( $rsExercicio                  );
$obCmbExercicio->obEvento->setOnChange  ( "buscaValor('MontaOrgao');"   );
$obCmbExercicio->setNull                ( true                          );

$obSituacaoAte = new Data;
$obSituacaoAte->setName     ( "stDataSituacao" );
$obSituacaoAte->setValue    ( $stDataSituacao  );
$obSituacaoAte->setRotulo   ( "Situação Até" );
$obSituacaoAte->setTitle    ( "Informe a data da situação a pagar" );
$obSituacaoAte->setNull     ( false );

//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName     ( "inCodEmpenhoInicial" );
$obTxtCodEmpenhoInicial->setValue    ( $inCodEmpenhoInicial  );
$obTxtCodEmpenhoInicial->setRotulo   ( "Número do Empenho"   );
$obTxtCodEmpenhoInicial->setTitle    ( "Informe a faixa de número dos empenhos para o filtro."   );
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
$obTxtCodEmpenhoFinal->setInteiro  ( true                );
$obTxtCodEmpenhoFinal->setNull     ( true                );

// Define Objeto Span Para Orgao e Unidade
$obSpan = new Span;
$obSpan->setId( "spnOrgaoUnidade" );

//ELEMENTO DESPESA
$stMascaraRubrica    = $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

$obTxtCodElementoDespesa = new BuscaInner;
$obTxtCodElementoDespesa->setRotulo               ( "Elemento de Despesa" );
$obTxtCodElementoDespesa->setTitle                ( "Informe o elemento de despesa para filtro" );
$obTxtCodElementoDespesa->setId                   ( "stDescricaoDespesa" );
$obTxtCodElementoDespesa->obCampoCod->setName     ( "stElementoDespesa" );
$obTxtCodElementoDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obTxtCodElementoDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obTxtCodElementoDespesa->obCampoCod->setValue    ( '' );
$obTxtCodElementoDespesa->obCampoCod->setAlign    ("left");
$obTxtCodElementoDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obTxtCodElementoDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obTxtCodElementoDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."'); }");
$obTxtCodElementoDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stElementoDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Credor"          );
$obBscFornecedor->setTitle                  ( "Informe o credor para o filtro." );
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->setValue                  ( $stNomFornecedor  );
$obBscFornecedor->obCampoCod->setName       ( "inCGM"           );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCGM            );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaValor('buscaFornecedor');");
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stNomFornecedor','','".Sessao::getId()."','800','550');");

$obTxtCodFuncao = new TextBox;
$obTxtCodFuncao->setRotulo            ( "Função" );
$obTxtCodFuncao->setTitle             ( "Informe a função para o filtro" );
$obTxtCodFuncao->setName              ( "stCodFuncao" );
$obTxtCodFuncao->setValue             ( $stCodFuncao  );
$obTxtCodFuncao->setSize              ( 20 );
$obTxtCodFuncao->setMaxLength         ( 150 );

$obTxtCodSubFuncao = new TextBox;
$obTxtCodSubFuncao->setRotulo            ( "Sub-função" );
$obTxtCodSubFuncao->setTitle             ( "Informe a sub-função para filtro" );
$obTxtCodSubFuncao->setName              ( "stCodSubFuncao" );
$obTxtCodSubFuncao->setValue             ( $stCodSubFuncao  );
$obTxtCodSubFuncao->setSize              ( 20 );
$obTxtCodSubFuncao->setMaxLength         ( 150 );

$obTxtOrdenacao = new TextBox;
$obTxtOrdenacao->setRotulo              ( "Ordenação"                            );
$obTxtOrdenacao->setTitle               ( "Informe a ordenação para filtro"      );
$obTxtOrdenacao->setName                ( "inOrdenacaoTxt"                       );
$obTxtOrdenacao->setValue               ( $inOrdenacaoTxt                        );
$obTxtOrdenacao->setSize                ( 6                                      );
$obTxtOrdenacao->setMaxLength           ( 3                                      );
$obTxtOrdenacao->setInteiro             ( true                                   );
$obTxtOrdenacao->setNull                ( true );

$obCmbOrdenacao= new Select;
$obCmbOrdenacao->setRotulo              ( "Ordenação"                  );
$obCmbOrdenacao->setName                ( "inOrdenacao"                );
$obCmbOrdenacao->setValue               ( $inOrdenacao                 );
$obCmbOrdenacao->setStyle               ( "width: 200px"               );
$obCmbOrdenacao->addOption              ( "", "Selecione"              );
$obCmbOrdenacao->addOption              ( "1", "Empenho"               );
$obCmbOrdenacao->addOption              ( "2", "Vencimento"            );
$obCmbOrdenacao->addOption              ( "3", "Recurso"               );
$obCmbOrdenacao->addOption              ( "4", "Credor"                );
$obCmbOrdenacao->setNull                ( true );

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                 );
$obFormulario->addHidden    ( $obHdnCaminho                           );
$obFormulario->addHidden    ( $obHdnModulo                            );
$obFormulario->addTitulo    ( "Dados para Filtro"                     );
$obFormulario->addComponente( $obCmbEntidades                         );
$obFormulario->addComponente( $obCmbExercicio                         );
$obFormulario->addComponente( $obSituacaoAte                          );
$obFormulario->agrupaComponentes    ( array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal ) );
$obFormulario->addSpan      ( $obSpan );
$obFormulario->addComponente( $obTxtCodElementoDespesa                );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obBscFornecedor );
$obFormulario->addComponente( $obTxtCodFuncao                         );
$obFormulario->addComponente( $obTxtCodSubFuncao                      );
if(Sessao::getExercicio() < 2016)
    $obFormulario->addComponenteComposto( $obTxtOrdenacao, $obCmbOrdenacao);

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>

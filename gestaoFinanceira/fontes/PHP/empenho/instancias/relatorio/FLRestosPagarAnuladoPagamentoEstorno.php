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
    * Página de Filtro do Relatório Restos a Pagar Anulado, Pagamentos ou Estorno
    * Data de Criação   : 08/09/2008

    * @author Analista: Tonismar R. Bernardo
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage empenho
    * @ignore relatorio

    * $Id:$

    * Casos de uso : uc-02.03.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php";
include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";

$stPrograma    = "RestosPagarAnuladoPagamentoEstorno";
$pgOcul        = "OC".$stPrograma.".php";
$pgGeraRel     = "OCGeraRelatorio".$stPrograma.".php";

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

//ELEMENTO DESPESA
$obROrcamentoDespesa = new ROrcamentoDespesa;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

Sessao::remove('arEntidades');
while ( !$rsEntidades->eof() ) {
    $arEntidades[$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
Sessao::write('arEntidades', $arEntidades);
$rsEntidades->setPrimeiroElemento();

$rsRecordset = new RecordSet;
$rsExercicio = $rsOrgao = $rsUnidade = $rsRecurso = new RecordSet;

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->recuperaExerciciosRP( $rsExercicio );

if(Sessao::getExercicio()<2016){
    $obForm = new Form;
    $obForm->setAction( $pgGeraRel );
    $obForm->setTarget( "telaPrincipal" );

    $obHdnCaminho = new Hidden;
    $obHdnCaminho->setName("stCaminho");
    $obHdnCaminho->setValue( "" );
}else{
    $obForm = new Form;
    $obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
    $obForm->setTarget( "oculto" );

    $obHdnCaminho = new Hidden;
    $obHdnCaminho->setName("stCaminho");
    $obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/".$pgOcul );
}



$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio() );
$obPeriodicidade->setValidaExercicio( true );
$obPeriodicidade->setNull           ( false );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
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
$obCmbExercicio->obEvento->setOnChange  ( "montaParametrosGET('MontaOrgao');"   );
$obCmbExercicio->setNull                ( true                          );

// Define Objeto Span Para Orgao e Unidade
$obSpan = new Span;
$obSpan->setId( "spnOrgaoUnidade" );

//ELEMENTO DESPESA
$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
//$size =  strlen($stMascaraRubrica) + 10;
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa para filtro" );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnChange ("montaParametrosGET('mascaraClassificacao');");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

$maxLenghtRecurso = strlen($obROrcamentoConfiguracao->getMascRecurso());

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

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


$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo              ( "Situação"                             );
$obTxtSituacao->setTitle               ( "Informa a situação para filtro"       );
$obTxtSituacao->setName                ( "inSituacaoTxt"                        );
$obTxtSituacao->setValue               ( $inSituacaoTxt                         );
$obTxtSituacao->setSize                ( 6                                      );
$obTxtSituacao->setMaxLength           ( 3                                      );
$obTxtSituacao->setInteiro             ( true                                   );
$obTxtSituacao->setNull                ( false );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setValue               ( $inSituacao                    );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "1", "Anulados"                );
$obCmbSituacao->addOption              ( "2", "Liquidados"              );
$obCmbSituacao->addOption              ( "3", "Anulação de Liquidação"  );
$obCmbSituacao->addOption              ( "4", "Pagos"                   );
$obCmbSituacao->addOption              ( "5", "Anulação de Pagamento"   );
$obCmbSituacao->setNull                ( false );

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Credor"          			  );
$obBscFornecedor->setTitle                  ( "Informe o credor para filtro." );
$obBscFornecedor->setId                     ( "stNomFornecedor" 			  );
$obBscFornecedor->setName                   ( "stNomFornecedor" 			  );
$obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" 			  );
$obBscFornecedor->obCampoCod->setId         ( "inCodFornecedor" 			  );
$obBscFornecedor->obCampoCod->setSize       ( 10                			  );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 			  );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            			  );
$obBscFornecedor->obCampoCod->obEvento->setOnChange("montaParametrosGET('buscaFornecedor');");
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

// Instanciação do objeto Lista de Assinaturas
include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obCmbExercicio      );
$obFormulario->addSpan      ( $obSpan );
$obFormulario->addComponente( $obBscFornecedor	);

$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obTxtCodFuncao                         );
$obFormulario->addComponente( $obTxtCodSubFuncao                      );
$obFormulario->addComponenteComposto( $obTxtSituacao, $obCmbSituacao  );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

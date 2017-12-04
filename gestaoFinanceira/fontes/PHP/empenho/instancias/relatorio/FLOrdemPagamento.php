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
    * Data de Criação   : 12/05/2005

    * @author Desenvolvedor: João Rafael Tissot

    * @ignore

    * $Id: FLOrdemPagamento.php 66607 2016-09-29 19:58:43Z carlos.silva $

    * Casos de uso: uc-02.03.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdensPagamento.class.php";
include_once 'JSOrdemPagamento.js';

$stPrograma = "OrdemPagamento";
$pgOcul     = "OC".$stPrograma.".php";
$pgGeraRel  = "OCGeraRelatorio".$stPrograma.".php";

Sessao::remove('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');

$obRegra = new REmpenhoRelatorioOrdensPagamento();

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
Sessao::write('filtroNomRelatorio', $arFiltroNom);
$rsEntidades->setPrimeiroElemento();

$rsRecordset = $rsOrgao = $rsRecurso = new RecordSet;

$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
$obRegra->obREmpenhoEmpenho->recuperaExercicios($rsExercicios, '', Sessao::getExercicio());

$obForm = new Form;
$obForm->setAction( $pgGeraRel );
$obForm->setTarget( "telaPrincipal" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( "" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Informe a entidade" );
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

$obCmbEntidades->obSelect1->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obSelect2->obEvento->setOnDblClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao1->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao2->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao3->obEvento->setOnClick('getIMontaAssinaturas()');
$obCmbEntidades->obGerenciaSelects->obBotao4->obEvento->setOnClick('getIMontaAssinaturas()');

$obCmbExercicio = new Select;
$obCmbExercicio->setRotulo("Exercício do Empenho");
$obCmbExercicio->setTitle("Selecione o exercício");
$obCmbExercicio->setId("stExercicioEmpenho");
$obCmbExercicio->setName("stExercicioEmpenho");
$obCmbExercicio->setCampoID("exercicio");
$obCmbExercicio->setCampoDesc("exercicio");
$obCmbExercicio->addOption("", "Selecione");
$obCmbExercicio->preencheCombo($rsExercicios);

//$obTxtExercicio = new TextBox;
//$obTxtExercicio->setRotulo("Exercício do Empenho");
//$obTxtExercicio->setTitle("Informe o exercício do empenho");
//$obTxtExercicio->setName("stExercicio");
//$obTxtExercicio->setValue(Sessao::getExercicio());
//$obTxtExercicio->setSize(4);
//$obTxtExercicio->setMaxLength(4);
//$obTxtExercicio->setNull(false);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setNull(true);

$obTxtEmpenhoInicio = new TextBox();
$obTxtEmpenhoInicio->setName  ("cod_empenho_inicio");
$obTxtEmpenhoInicio->setRotulo("Número do Empenho");
$obTxtEmpenhoInicio->setTitle ("Informe a faixa de números de empenho para o filtro.");

$obLblEmpenho = new Label();
$obLblEmpenho->setValue(" a ");

$obTxtEmpenhoFinal = new TextBox();
$obTxtEmpenhoFinal->setName("cod_empenho_final");
$obTxtEmpenhoFinal->setRotulo("Número do Empenho");

$obTxtOrdemInicio = new TextBox();
$obTxtOrdemInicio->setName("cod_ordem_inicio");
$obTxtOrdemInicio->setRotulo("Número da Ordem");
$obTxtOrdemInicio->setTitle ("Informe a faixa de número de ordens para o filtro.");

$obLblOrdem = new Label();
$obLblOrdem->setValue(" a ");
$obLblOrdem->setTitle ("Informe a faixa de número de ordens para o filtro.");

$obTxtOrdemFinal = new TextBox();
$obTxtOrdemFinal->setName("cod_ordem_final");
$obTxtOrdemFinal->setRotulo("Número da Ordem");
$obTxtOrdemFinal->setTitle ("Informe a faixa de número de ordens para o filtro.");

$obBscCredor= new BuscaInner;
$obBscCredor->setRotulo           ( "Credor" );
$obBscCredor->setTitle            ( "Informe o credor para o filtro." );
$obBscCredor->setId               ( "stCredor" );
$obBscCredor->setValue            ($stCredor);
$obBscCredor->obCampoCod->setName ( "cgm_beneficiario" );
$obBscCredor->obCampoCod->setValue( $cgm_beneficiario );
$obBscCredor->obCampoCod->setAlign("left");
$obBscCredor->obCampoCod->obEvento->setOnBlur("buscaFornecedor();");
$obBscCredor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','cgm_beneficiario','stCredor','','".Sessao::getId()."','800','550');");

$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo   ( "Situação"                      );
$obTxtSituacao->setTitle    ( "Informe a situação para filtro");
$obTxtSituacao->setName     ( "situacaoTxt"                   );
$obTxtSituacao->setValue    ( $situacaoTxt                    );
$obTxtSituacao->setSize     ( 6                               );
$obTxtSituacao->setMaxLength( 3                               );
$obTxtSituacao->setInteiro  ( true                            );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo( "Situação"     );
$obCmbSituacao->setName  ( "situacao"     );
$obCmbSituacao->setValue ( $situacao      );
$obCmbSituacao->setStyle ( "width: 200px" );
$obCmbSituacao->addOption( "", "Selecione");
$obCmbSituacao->addOption( "1", "A Pagar" );
$obCmbSituacao->addOption( "2", "Pagas"   );
$obCmbSituacao->addOption( "3", "Anuladas");
$obCmbSituacao->obEvento->setOnChange( "montaParametrosGET('carregaOrdenacao','');" );

$obCmbOrdenacao= new Select;
$obCmbOrdenacao->setRotulo( "Ordenação"                       );
$obCmbOrdenacao->setTitle ( "Infome por qual campo o relatório será ordenado." );
$obCmbOrdenacao->setName  ( "ordenacao"                       );
$obCmbOrdenacao->setValue ( $ordenacao                        );
$obCmbOrdenacao->setStyle ( "width: 230px"                    );
$obCmbOrdenacao->addOption( "op", "Por Nro de OP"             );
$obCmbOrdenacao->addOption( "emissao", "Por Data de Emissão"  );
$obCmbOrdenacao->addOption( "credor", "Por Credor"            );
$obCmbOrdenacao->addOption( "pagamento", "Por Data de Pagamento da OP" );

$obDtPagamento = new Periodicidade();
$obDtPagamento->setExercicio(Sessao::getExercicio());
$obDtPagamento->setDefinicao("Data do Pagamento");
$obDtPagamento->setRotulo("Data do Pagamento");
$obDtPagamento->setTitle( "Selecione a periodicidade da Data do Pagamento."  );
$obDtPagamento->setIdComponente("DtPagamento");
$obDtPagamento->setNull(true);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

// ################ FORMULARIO #########################
$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnCaminho);
$obFormulario->addTitulo    ("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obCmbExercicio);
$obFormulario->agrupaComponentes(array($obTxtEmpenhoInicio, $obLblEmpenho ,$obTxtEmpenhoFinal));
$obFormulario->agrupaComponentes(array($obTxtOrdemInicio, $obLblOrdem ,$obTxtOrdemFinal));
$obFormulario->addComponente($obBscCredor);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponenteComposto($obTxtSituacao, $obCmbSituacao);
$obFormulario->addComponente($obDtPagamento);
$obFormulario->addComponente($obCmbOrdenacao);
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

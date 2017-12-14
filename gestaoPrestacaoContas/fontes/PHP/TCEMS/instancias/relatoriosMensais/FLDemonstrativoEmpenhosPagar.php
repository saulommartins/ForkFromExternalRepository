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
    * Data de Criação: 19/02/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Rafael Almeida
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: FLEmpenhoPagar.php 35209 2008-11-03 12:50:45Z girardi $

    * Casos de uso : uc-02.03.07
*/

/* includes de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
/* includes de regra de negocio */
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhoPagar.class.php";
/* includes de componentes */
include_once CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php";
include_once CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php";
/* includes de javascript */
include_once 'JSDemonstrativoEmpenhosPagar.js';

$obRegra = new REmpenhoRelatorioEmpenhoPagar;
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM(Sessao::read('numCgm'));
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades , " ORDER BY cod_entidade");

$arFiltroNom = array();
while ( !$rsEntidades->eof() ) {
    $arNomeEntidades[$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}

Sessao::write('arNomeEntidades', $arNomeEntidades);

$rsEntidades->setPrimeiroElemento();

$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar($rsOrgao);
$obRegra->obREmpenhoEmpenho->recuperaExercicios($rsExercicio, $boTransacao, Sessao::getExercicio());

$rsRecordset = new RecordSet;

$stEval = "
if (document.frm.stDataSituacao.value != '') {
    stDataInicial   = document.frm.stDataInicial.value.substring(6,10) + document.frm.stDataInicial.value.substring(3,5) + document.frm.stDataInicial.value.substring(0,2);
    stDataSituacao  = document.frm.stDataSituacao.value.substring(6,10) + document.frm.stDataSituacao.value.substring(3,5) + document.frm.stDataSituacao.value.substring(0,2);
    if (stDataSituacao<stDataInicial) {
        erro = true;
        mensagem += '@Situação até menor que Data de Emissão!';
    }
    if (stDataSituacao < 20050101) {
        erro = true;
        mensagem += '@Situação até menor que 01/01/2005!';
    }
}
";

$obForm = new Form;
$obForm->setAction( 'OCGeraDemonstrativoEmpenhosPagar.php' );
$obForm->setTarget( "telaPrincipal" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( "" );

$obHdnEval = new HiddenEval;
$obHdnEval->setName("stEval");
$obHdnEval->setValue($stEval);

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName('inCodEntidade');
$obCmbEntidades->setRotulo("Entidades");
$obCmbEntidades->setTitle("Selecione as entidades para o filtro.");
$obCmbEntidades->setNull(false);

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas()==1) {
    $rsRecordset = $rsEntidades;
    $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1('cod_entidade');
$obCmbEntidades->setCampoDesc1('nom_cgm');
$obCmbEntidades->SetRecord1($rsEntidades);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2('inCodEntidade');
$obCmbEntidades->setCampoId2('cod_entidade');
$obCmbEntidades->setCampoDesc2('nom_cgm');
$obCmbEntidades->SetRecord2($rsRecordset);

//EXERCICIO
$obCmbExercicio = new Select;
$obCmbExercicio->setRotulo("Exercício"            );
$obCmbExercicio->setTitle("Selecione o exercício");
$obCmbExercicio->setName("inExercicio");
$obCmbExercicio->setValue($stExercicio);
$obCmbExercicio->setStyle("width: 200px");
$obCmbExercicio->setCampoID("exercicio");
$obCmbExercicio->setCampoDesc("exercicio");
$obCmbExercicio->addOption("", "Selecione");
$obCmbExercicio->preencheCombo($rsExercicio);
$obCmbExercicio->setNull(false);

//Orgão
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Selecione o órgão para filtro.");
$obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
$obTxtOrgao->setValue               ( $inCodOrgaoTxt               );
$obTxtOrgao->setSize                ( 6                            );
$obTxtOrgao->setMaxLength           ( 3                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");
$obTxtOrgao->setObrigatorio         ( true                         );

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inCodOrgao"                  );
$obCmbOrgao->setValue               ( $inCodOrgao                   );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );
$obCmbOrgao->setObrigatorio         ( true                          );

//Unidade
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Selecione a unidade para filtro." );
$obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
$obTxtUnidade->setValue               ( $inCodUnidadeTxt                );
$obTxtUnidade->setSize                ( 6                               );
$obTxtUnidade->setMaxLength           ( 3                               );
$obTxtUnidade->setInteiro             ( true                            );
$obTxtUnidade->setObrigatorio         ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inCodUnidade"                  );
$obCmbUnidade->setValue               ( $inCodUnidade                   );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );
$obCmbUnidade->setObrigatorio         ( true                            );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setRotulo("Periodicidade Emissão");
$obPeriodicidade->setTitle("Informe a Periodicidade de Emissão dos empenhos que deseja pesquisar");
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setValue(4);
$obPeriodicidade->setObrigatorio      ( true                            );

// Define objeto Data para validade inicial
$obSituacaoAte = new Data;
$obSituacaoAte->setName("stDataSituacao");
$obSituacaoAte->setValue($stDataSituacao);
$obSituacaoAte->setRotulo("Situação Até");
$obSituacaoAte->setTitle("Informe a data da situação a pagar");
$obSituacaoAte->setNull(true);

//EMPENHO
//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName("inCodEmpenhoInicial");
$obTxtCodEmpenhoInicial->setTitle("Informe o(s) Número(s) do(s) Empenho(s) que deseja pesquisar");
$obTxtCodEmpenhoInicial->setValue($inCodEmpenhoInicial);
$obTxtCodEmpenhoInicial->setRotulo("Número do Empenho");
$obTxtCodEmpenhoInicial->setInteiro(true);
$obTxtCodEmpenhoInicial->setNull(true);

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue("a");

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName("inCodEmpenhoFinal");
$obTxtCodEmpenhoFinal->setTitle("Informe o(s) Número(s) do(s) Empenho(s) que deseja pesquisar");
$obTxtCodEmpenhoFinal->setValue($inCodEmpenhoFinal);
$obTxtCodEmpenhoFinal->setRotulo("Número do Empenho");
$obTxtCodEmpenhoFinal->setInteiro(true);
$obTxtCodEmpenhoFinal->setNull(true);

//ORDENAÇÃO
$obTxtOrdenacao = new TextBox;
$obTxtOrdenacao->setRotulo("Ordenação");
$obTxtOrdenacao->setTitle("Informe a Ordenação do relatório: Empenho ou Credor");
$obTxtOrdenacao->setName("inOrdenacaoTxt");
$obTxtOrdenacao->setValue($inOrdenacaoTxt);
$obTxtOrdenacao->setSize(6);
$obTxtOrdenacao->setMaxLength(3);
$obTxtOrdenacao->setInteiro(true);

$obCmbOrdenacao= new Select;
$obCmbOrdenacao->setRotulo("Ordenação");
$obCmbOrdenacao->setName("inOrdenacao");
$obCmbOrdenacao->setValue($inOrdenacao);
$obCmbOrdenacao->setStyle("width: 200px");
$obCmbOrdenacao->addOption("", "Selecione");
$obCmbOrdenacao->addOption("1", "Empenho");
$obCmbOrdenacao->addOption("2", "Credor");

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo("Credor");
$obBscFornecedor->setTitle("Informe o credor para o filtro.");
$obBscFornecedor->setId("stNomFornecedor");
$obBscFornecedor->setValue($stNomFornecedor);
$obBscFornecedor->obCampoCod->setName("inCodFornecedor");
$obBscFornecedor->obCampoCod->setSize(10);
$obBscFornecedor->obCampoCod->setMaxLength(8);
$obBscFornecedor->obCampoCod->setValue($inCodFornecedor);
$obBscFornecedor->obCampoCod->setAlign("left");
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaValor('buscaFornecedorDiverso');");
$obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

// Monta as assinaturas
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

/**
 * Monta o formulario
 */
$obFormulario = new Formulario;
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnCaminho);
$obFormulario->addHidden($obHdnEval,true);
$obFormulario->addTitulo("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obCmbExercicio);
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao        );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade    );
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponente($obSituacaoAte);
$obFormulario->agrupaComponentes(array($obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal));
$obFormulario->addComponenteComposto($obTxtOrdenacao, $obCmbOrdenacao);
$obFormulario->addComponente($obBscFornecedor);

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

?>

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

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: FLSituacaoEmpenho.php 64470 2016-03-01 13:12:50Z jean $

    * Casos de uso: uc-02.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoEmpenho.class.php";
include_once 'JSSituacaoEmpenho.js';

$obRegra = new REmpenhoRelatorioSituacaoEmpenho;
$rsRecordset = $rsOrgao = $rsUnidade = $rsRecurso = $rsExercicio = new RecordSet;

if ($_GET['stExercicio']) {
    $stExercicio = $_GET['stExercicio'];
    SistemaLegado::LiberaFrames(true,False);
} else {
    $stExercicio = Sessao::getExercicio();
}
$obRegra->obREmpenhoEmpenho->recuperaExercicios( $rsExercicio, $boTransacao, Sessao::getExercicio());

$arFiltroNom = Sessao::read('filtroNomRelatorio');
//************************************************//
// CONSULTA DE ACORDO COM O EXERCÍCIO DA SESSÃO   //
//************************************************//

//Consulta de Entidade
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->setExercicio          ($stExercicio);
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade($rsEntidades, " ORDER BY cod_entidade");
while ( !$rsEntidades->eof() ) {
    $arFiltroNom['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

//Consulta de Recurso
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setExercicio( $stExercicio );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
while ( !$rsRecurso->eof() ) {
    $arFiltroNom['recurso'][$rsRecurso->getCampo('cod_recurso')] = $rsRecurso->getCampo('nom_recurso');
    $rsRecurso->proximo();
}
$rsRecurso->setPrimeiroElemento();

//Consulta de Mascara para Elemento de Despesa
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setExercicio( $stExercicio );
$stMascaraRubrica = $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaMascara();

//Consulta Orgão
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( $stExercicio );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
while ( !$rsOrgao->eof() ) {
    $arFiltroNom['orgao'][$rsOrgao->getCampo('num_orgao')] = $rsOrgao->getCampo('nom_orgao');
    $rsOrgao->proximo();
}
Sessao::write('filtroNomRelatorio', $arFiltroNom);
$rsOrgao->setPrimeiroElemento();

//FORMULÁRIO PRINCIPAL
$obForm = new Form;
$obForm->setAction(CAM_FW_POPUPS."relatorio/OCRelatorio.php");
$obForm->setTarget("oculto");

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GF_EMP_INSTANCIAS."relatorio/OCSituacaoEmpenho.php");

//MASCARA RUBRICA
$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ("stMascClassificacao");
$obHdnMascClassificacao->setValue($stMascaraRubrica);

//EXERCICIO
$obCmbExercicio = new Select;
$obCmbExercicio->setRotulo            ("Exercício");
$obCmbExercicio->setTitle             ("Informe o exercício");
$obCmbExercicio->setName              ("inExercicio");
$obCmbExercicio->setValue             ($stExercicio);
$obCmbExercicio->setStyle             ("width: 200px");
$obCmbExercicio->setCampoID           ("exercicio");
$obCmbExercicio->setCampoDesc         ("exercicio");
$obCmbExercicio->addOption            ("", "Selecione");
$obCmbExercicio->preencheCombo        ($rsExercicio);
$obCmbExercicio->obEvento->setOnChange("montaInterface(this.value);");
$obCmbExercicio->setNull              (true);

//ENTIDADE
//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName  ('inCodEntidade');
$obCmbEntidades->setRotulo("Entidades");
$obCmbEntidades->setTitle ("Selecione as entidades que deseja pesquisar.");
$obCmbEntidades->setNull  (false);

// Caso o usuário tenha permissão para somente uma entidade, a mesma já virá selecionada
if ($rsEntidades->getNumLinhas() == 1) {
    $rsRecordset = $rsEntidades;
    $rsEntidades = new RecordSet;
}

// lista de atributos disponiveis
$obCmbEntidades->SetNomeLista1 ('inCodEntidadeDisponivel');
$obCmbEntidades->setCampoId1   ('cod_entidade');
$obCmbEntidades->setCampoDesc1 ('nom_cgm');
$obCmbEntidades->SetRecord1    ($rsEntidades);

// lista de atributos selecionados
$obCmbEntidades->SetNomeLista2 ('inCodEntidade');
$obCmbEntidades->setCampoId2   ('cod_entidade');
$obCmbEntidades->setCampoDesc2 ('nom_cgm');
$obCmbEntidades->SetRecord2    ($rsRecordset);

//PERIODICIDADE EMISSÃO
$obPeriodicidadeEmissao = new Periodicidade();
$obPeriodicidadeEmissao->setRotulo             ("Periodicidade Emissão");
$obPeriodicidadeEmissao->setTitle              ("Informe a Periodicidade de Emissão dos empenhos que deseja pesquisar");
$obPeriodicidadeEmissao->setName               ("stPeriodicidadeEmissao");
$obPeriodicidadeEmissao->obDataInicial->setName("stDataInicialEmissao");
$obPeriodicidadeEmissao->obDataFinal->setName  ("stDataFinalEmissao");
$obPeriodicidadeEmissao->obSpan->setId         ("spanPeriodicidadeEmissao");
$obPeriodicidadeEmissao->setExercicio          ($stExercicio);
$obPeriodicidadeEmissao->setValidaExercicio    (true);
$obPeriodicidadeEmissao->setNull               (false);

$obSituacaoAte = new Data;
$obSituacaoAte->setName  ("stDataSituacao");
$obSituacaoAte->setValue ($stDataSituacao);
$obSituacaoAte->setRotulo("Situação Até");
$obSituacaoAte->setTitle ("Informe a data da situação a pagar");
$obSituacaoAte->setNull  (false);

// FAZ AS BUSCAS DO TIPO DE EMPENHO
$obTEmpenhoTipoEmpenho = new TEmpenhoTipoEmpenho;
$obTEmpenhoTipoEmpenho->recuperaTodos($rsTipoEmpenho);

// MONTA A COMBO COM OS TIPOS DE EMPENHO, ONDE NÃO DEVE APARECER O TIPO 0 (ZERO) E O TIPO 1 E 2 DEVEM FICAR
// JUNTOS NA MESMA OPÇÃO DA COMBO
$inCount = 0;
while (!$rsTipoEmpenho->eof()) {
    if ($rsTipoEmpenho->getCampo('cod_tipo') != 0) {
        $arTipoEmpenho[$inCount]['cod_tipo'] = $rsTipoEmpenho->getCampo('cod_tipo');
        $arTipoEmpenho[$inCount]['nom_tipo'] = $rsTipoEmpenho->getCampo('nom_tipo');
        $inCount++;
    }
    $rsTipoEmpenho->proximo();
}

$rsTipoEmpenho = new RecordSet;
$rsTipoEmpenho->preenche($arTipoEmpenho);
unset($arTipoEmpenho);

//TIPO EMPENHO
$obCmbTipoEmpenho = new Select;
$obCmbTipoEmpenho->setRotulo    ("Tipo de Empenho");
$obCmbTipoEmpenho->setName      ("inCodTipoEmpenho");
$obCmbTipoEmpenho->setId        ("inCodTipoEmpenho");
$obCmbTipoEmpenho->setCampoID   ("cod_tipo");
$obCmbTipoEmpenho->setCampoDesc ("nom_tipo");
$obCmbTipoEmpenho->addOption    ("", "Selecione");
$obCmbTipoEmpenho->preencheCombo($rsTipoEmpenho);

//EMPENHO
//Define o objeto TEXT para Codigo do Empenho Inicial
$obTxtCodEmpenhoInicial = new TextBox;
$obTxtCodEmpenhoInicial->setName   ("inCodEmpenhoInicial");
$obTxtCodEmpenhoInicial->setTitle  ("Informe o(s) Número(s) do(s) Empenho(s) que deseja pesquisar" );
$obTxtCodEmpenhoInicial->setValue  ($inCodEmpenhoInicial);
$obTxtCodEmpenhoInicial->setRotulo ("Número do Empenho");
$obTxtCodEmpenhoInicial->setInteiro(true);
$obTxtCodEmpenhoInicial->setNull   (true);

//Define objeto Label
$obLblEmpenho = new Label;
$obLblEmpenho->setValue("a");

//Define o objeto TEXT para Codigo do Empenho Final
$obTxtCodEmpenhoFinal = new TextBox;
$obTxtCodEmpenhoFinal->setName   ("inCodEmpenhoFinal");
$obTxtCodEmpenhoFinal->setTitle  ("Informe o(s) Número(s) do(s) Empenho(s) que deseja pesquisar");
$obTxtCodEmpenhoFinal->setValue  ($inCodEmpenhoFinal);
$obTxtCodEmpenhoFinal->setRotulo ("Número do Empenho");
$obTxtCodEmpenhoFinal->setInteiro(true);
$obTxtCodEmpenhoFinal->setNull   (true);

//DOTAÇÃO ORÇAMENTÁRIA
// Define Objeto BuscaInner para Dotacao Redutoras
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo("Dotação Orçamentária");
$obBscDespesa->setTitle ("Informe a Dotação Orçamentária que deseja pesquisar");
$obBscDespesa->setNulL  (true);
$obBscDespesa->setId    ("stNomDotacao");
$obBscDespesa->setValue ($stNomDotacao);
$obBscDespesa->obCampoCod->setName("inCodDotacao");
$obBscDespesa->obCampoCod->setId  ("inCodDotacao");

//Linha baixo utilizada para seguir um tamanho padrão de campo de acordo com o elemento da despesa
//Utilizado somente nesta interface
$obBscDespesa->obCampoCod->setSize      (strlen($stMascaraRubrica));
$obBscDespesa->obCampoCod->setMaxLength (5);
$obBscDespesa->obCampoCod->setValue     ($inCodDotacao);
$obBscDespesa->obCampoCod->setAlign     ("left");
$obBscDespesa->obCampoCod->obEvento->setOnBlur("if (this.value!='') { buscaValor('buscaDotacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); }");
$obBscDespesa->obImagem->setId('imgDespesa');

//ELEMENTO DESPESA
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ("Elemento de Despesa");
$obBscRubricaDespesa->setTitle                ("Informe o Elemento de Despesa que deseja pesquisar");
$obBscRubricaDespesa->setId                   ("stDescricaoDespesa");
$obBscRubricaDespesa->obCampoCod->setName     ("inCodDespesa");
$obBscRubricaDespesa->obCampoCod->setSize     (strlen($stMascaraRubrica));
$obBscRubricaDespesa->obCampoCod->setMaxLength(strlen($stMascaraRubrica));
$obBscRubricaDespesa->obCampoCod->setValue    ('');
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); }");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');");

//ORGÃO
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo            ("Órgão");
$obTxtOrgao->setTitle             ("Informe o Órgão Orçamentário que deseja pesquisar");
$obTxtOrgao->setName              ("inNumOrgaoTxt");
$obTxtOrgao->setValue             ($inNumOrgaoTxt);
$obTxtOrgao->setSize              (10);
$obTxtOrgao->setMaxLength         (10);
$obTxtOrgao->setInteiro           (true);
$obTxtOrgao->obEvento->setOnChange("buscaValor('MontaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo            ("Órgão");
$obCmbOrgao->setName              ("inNumOrgao");
$obCmbOrgao->setValue             ($inNumOrgao);
$obCmbOrgao->setStyle             ("width: 200px");
$obCmbOrgao->setCampoID           ("num_orgao");
$obCmbOrgao->setCampoDesc         ("nom_orgao");
$obCmbOrgao->addOption            ("", "Selecione");
$obCmbOrgao->preencheCombo        ($rsOrgao);
$obCmbOrgao->obEvento->setOnChange("buscaValor('MontaUnidade');");

//UNIDADE
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo   ("Unidade");
$obTxtUnidade->setTitle    ("Informe a Unidade Orçamentária que deseja pesquisar");
$obTxtUnidade->setName     ("inNumUnidadeTxt");
$obTxtUnidade->setValue    ($inNumUnidadeTxt);
$obTxtUnidade->setSize     (10);
$obTxtUnidade->setMaxLength(10);
$obTxtUnidade->setInteiro  (true);

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo   ("Unidade");
$obCmbUnidade->setName     ("inNumUnidade");
$obCmbUnidade->setValue    ($inNumUnidade);
$obCmbUnidade->setStyle    ("width: 200px");
$obCmbUnidade->setCampoID  ("num_unidade");
$obCmbUnidade->setCampoDesc("descricao");
$obCmbUnidade->addOption   ("", "Selecione");

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

//ORDENAÇÃO
$obTxtOrdenacao = new TextBox;
$obTxtOrdenacao->setRotulo              ( "Ordenação"                            );
$obTxtOrdenacao->setTitle               ( "Informa a Ordenação do relatório: Ordenado por Empenho OU Credor OU Data de Pagamento" );
$obTxtOrdenacao->setName                ( "inOrdenacaoTxt"                        );
$obTxtOrdenacao->setValue               ( $inOrdenacaoTxt                         );
$obTxtOrdenacao->setSize                ( 6                                      );
$obTxtOrdenacao->setMaxLength           ( 3                                      );
$obTxtOrdenacao->setInteiro             ( true                                   );

$obCmbOrdenacao= new Select;
$obCmbOrdenacao->setRotulo              ( "Ordenação"                    );
$obCmbOrdenacao->setName                ( "inOrdenacao"                  );
$obCmbOrdenacao->setValue               ( $inOrdenacao                   );
$obCmbOrdenacao->setStyle               ( "width: 200px"                 );
$obCmbOrdenacao->addOption              ( "", "Selecione"                );
$obCmbOrdenacao->addOption              ( "1", "Empenho"                 );
$obCmbOrdenacao->addOption              ( "2", "Credor"                  );
$obCmbOrdenacao->addOption              ( "3", "Data Pagamento"          );

//CREDOR
// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Credor"      );
$obBscFornecedor->setTitle                  ( "Informe o código CGM do credor que deseja pesquisar");
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->setValue                  ( $stNomFornecedor  );
$obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCodFornecedor  );
$obBscFornecedor->obCampoCod->setAlign      ("left"             );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaValor('buscaFornecedorDiverso');");
$obBscFornecedor->setFuncaoBusca("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

//SITUAÇÃO
$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo   ("Situação");
$obTxtSituacao->setTitle    ("Informe a situação que deseja pesquisar: Empenhados, Anulados, Liquidados, A Liquidar, Pagos ou A Pagar");
$obTxtSituacao->setName     ("inSituacaoTxt");
$obTxtSituacao->setValue    ($inSituacaoTxt);
$obTxtSituacao->setSize     (6);
$obTxtSituacao->setMaxLength(3);
$obTxtSituacao->setInteiro  (true);

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo("Situação");
$obCmbSituacao->setName  ("inSituacao");
$obCmbSituacao->setValue ($inSituacao);
$obCmbSituacao->setStyle ("width: 200px");
$obCmbSituacao->addOption("", "Selecione");
$obCmbSituacao->addOption("1", "Empenhados");
$obCmbSituacao->addOption("2", "Anulados");
$obCmbSituacao->addOption("3", "Liquidados");
$obCmbSituacao->addOption("4", "A Liquidar");
$obCmbSituacao->addOption("5", "Pagos");
$obCmbSituacao->addOption("6", "A Pagar");

if (Sessao::getExercicio() > '2015') {
    $obCentroCusto = new TextBox;
    $obCentroCusto->setRotulo ("Centro de Custo");
    $obCentroCusto->setTitle ("Informe o centro de custo");
    $obCentroCusto->setName ('inCentroCusto');
    $obCentroCusto->setId ('inCentroCusto');
    $obCentroCusto->setInteiro (true);  
}

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm                    ($obForm);
$obFormulario->addHidden                  ($obHdnCaminho);
$obFormulario->addTitulo                  ("Dados para Filtro");
$obFormulario->addComponente              ($obCmbExercicio);
$obFormulario->addComponente              ($obCmbEntidades);
$obFormulario->addComponente              ($obPeriodicidadeEmissao);
$obFormulario->addComponente              ($obSituacaoAte);
$obFormulario->agrupaComponentes          (array( $obTxtCodEmpenhoInicial, $obLblEmpenho, $obTxtCodEmpenhoFinal));
$obFormulario->addComponente              ($obCmbTipoEmpenho);
if (Sessao::getExercicio() > '2015') {
    $obFormulario->addComponente( $obCentroCusto );
}
$obFormulario->addComponente              ($obBscDespesa);
$obFormulario->addHidden                  ($obHdnMascClassificacao);
$obFormulario->addComponente              ($obBscRubricaDespesa);
$obFormulario->addComponenteComposto      ($obTxtOrgao, $obCmbOrgao);
$obFormulario->addComponenteComposto      ($obTxtUnidade, $obCmbUnidade);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
$obFormulario->addComponenteComposto      ($obTxtOrdenacao, $obCmbOrdenacao);
$obFormulario->addComponente              ($obBscFornecedor);
$obFormulario->addComponenteComposto      ($obTxtSituacao, $obCmbSituacao);
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

$jsOnload = "
    jq(document).ready( function () {
        //quando clicar no bscDespesa, concatena os valores da entidade.
        jq('#imgDespesa').click(function () {
            var codEntidade = '';
            jq('#inCodEntidade option').each(function () {
                if (this.value != '') {
                    codEntidade = codEntidade + ',' + this.value
                }
            });
            codEntidade = codEntidade.substring(1);
            if (codEntidade != '') {
                abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','&inCodEntidade='+codEntidade+'&tipoBusca=autorizacaoEmpenho','".Sessao::getId()."','800','550');
            }
        });
    })
";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

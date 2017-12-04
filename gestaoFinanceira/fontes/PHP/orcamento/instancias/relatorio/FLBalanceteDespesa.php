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
    * Data de Criação   : 06/08/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: FLBalanceteDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.22
*/

//Define o nome dos arquivos PHP
$stPrograma = "BalanceteDespesa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"   );
include_once CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php";
include_once CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectFuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectSubfuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectPrograma.class.php';
/* includes da pagina javascript */
include_once $pgJS;

$rsRecordset = $rsOrgao = $rsEntidades = new RecordSet();

$obRegra = new ROrcamentoRelatorioBalanceteDespesa;
$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

//Recupera Mascara da Classificao de Despesa
$obROrcamentoDespesa = new ROrcamentoDespesa;
$obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setExercicio(Sessao::getExercicio());
$mascClassificacao = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

$arNomFiltro = Sessao::read('filtroNomRelatorio');

$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
$obROrcamentoProjetoAtividade->setExercicio(Sessao::getExercicio());
$obROrcamentoProjetoAtividade->listarSemMascara( $rsPao );

while (!$rsPao->eof()) {
    $arNomFiltro['pao'][$rsPao->getCampo('num_acao')] = $rsPao->getCampo('nom_pao');
    $rsPao->proximo();
}
$rsPao->setPrimeiroElemento();

//Consulta Orgão
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

while (!$rsOrgao->eof()) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo('num_orgao')] = $rsOrgao->getCampo('nom_orgao');
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();

$obRegra->obREntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obRegra->obREntidade->setExercicio          (Sessao::getExercicio());
$obRegra->obREntidade->listarUsuariosEntidade($rsEntidades , " ORDER BY cod_entidade");
while (!$rsEntidades->eof()) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo('cod_entidade')] = $rsEntidades->getCampo('nom_cgm');
    $rsEntidades->proximo();
}
Sessao::write('filtroNomRelatorio', $arNomFiltro);
$rsEntidades->setPrimeiroElemento();

$obForm = new Form;
$obForm->setAction(CAM_FW_POPUPS."relatorio/OCRelatorio.php");
$obForm->setTarget("oculto");

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue(CAM_GF_ORC_INSTANCIAS."relatorio/OCBalanceteDespesa.php");

//insere o máscara como hidden
$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName("stMascClassificacao");
$obHdnMascClassificacao->setValue($mascClassificacao);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio(Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio(true);
$obPeriodicidade->setNull(false);
$obPeriodicidade->setValue(4);

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades que deseja pesquisar." );
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

//ORGÃO
$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo   ("Órgão");
$obTxtOrgao->setTitle    ("Selecione o órgão orçamentário que deseja pesquisar.");
$obTxtOrgao->setName     ("inNumOrgaoTxt");
$obTxtOrgao->setValue    ($inNumOrgaoTxt);
$obTxtOrgao->setSize     (10);
$obTxtOrgao->setMaxLength(10);
$obTxtOrgao->setInteiro  (true);
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo    ("Órgão");
$obCmbOrgao->setName      ("inNumOrgao");
$obCmbOrgao->setValue     ($inNumOrgao);
$obCmbOrgao->setStyle     ("width: 200px");
$obCmbOrgao->setCampoID   ("num_orgao");
$obCmbOrgao->setCampoDesc ("nom_orgao");
$obCmbOrgao->addOption    ("", "Selecione");
$obCmbOrgao->preencheCombo($rsOrgao);
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

//UNIDADE
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo   ("Unidade");
$obTxtUnidade->setTitle    ("Selecione a unidade orçamentária que deseja pesquisar." );
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

$obISelectFuncao     = new ISelectFuncao;
$obISelectSubfuncao  = new ISelectSubfuncao;
$obISelectPrograma   = new ISelectPrograma;

$obTxtPao = new TextBox;
$obTxtPao->setRotulo   ("PAO");
$obTxtPao->setTitle    ("Selecione o PAO para filtro." );
$obTxtPao->setName     ("inCodPaoTxt");
$obTxtPao->setValue    ($inCodPao);
$obTxtPao->setSize     (6);
$obTxtPao->setMaxLength(4);
$obTxtPao->setInteiro  (true);

$obCmbPao = new Select;
$obCmbPao->setRotulo              ( "PAO"                       );
$obCmbPao->setName                ( "inCodPao"                      );
$obCmbPao->setValue               ( $inCodPao                       );
$obCmbPao->setStyle               ( "width: 200px"                  );
$obCmbPao->setCampoID             ( "num_acao"                       );
$obCmbPao->setCampoDesc           ( "nom_pao"                       );
$obCmbPao->addOption              ( "", "Selecione"                 );
$obCmbPao->preencheCombo          ( $rsPao                          );

// Pop Uo de intervalo de Dotação
$obPopUpIntervaloDotacao = new IIntervaloPopUpDotacao ( $obCmbEntidades ) ;

//ELEMENTO DESPESA
$obROrcamentoDespesa = new ROrcamentoDespesa;
$stMascaraEstruturalDespesa  = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

$obBscEstruturalInicial = new BuscaInner;
$obBscEstruturalInicial->setRotulo               ( "Código Estrutural Inicial" );
$obBscEstruturalInicial->setTitle                ( "Informe o elemento de despesa inicial para filtro." );
$obBscEstruturalInicial->setId                   ( "stDescricaoDespesaInicial" );
$obBscEstruturalInicial->obCampoCod->setName     ( "stCodEstruturalInicial" );
$obBscEstruturalInicial->obCampoCod->setSize     ( strlen($stMascaraEstruturalDespesa) );
$obBscEstruturalInicial->obCampoCod->setMaxLength( strlen($stMascaraEstruturalDespesa) );
$obBscEstruturalInicial->obCampoCod->setValue    ( '' );
$obBscEstruturalInicial->obCampoCod->setAlign    ("left");
$obBscEstruturalInicial->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this);");
$obBscEstruturalInicial->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraEstruturalDespesa."', this, event);");
$obBscEstruturalInicial->obCampoCod->obEvento->setOnBlur("buscaValor('mascaraClassificacaoInicial','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscEstruturalInicial->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stCodEstruturalInicial','stDescricaoDespesaInicial','&mascClassificacao=".$stMascaraEstruturalDespesa."','".Sessao::getId()."','800','550');");

$obBscEstruturalFinal = new BuscaInner;
$obBscEstruturalFinal->setRotulo               ( "Código Estrutural Final" );
$obBscEstruturalFinal->setTitle                ( "Informe o elemento de despesa final para filtro." );
$obBscEstruturalFinal->setId                   ( "stDescricaoDespesaFinal" );
$obBscEstruturalFinal->obCampoCod->setName     ( "stCodEstruturalFinal" );
$obBscEstruturalFinal->obCampoCod->setSize     ( strlen($stMascaraEstruturalDespesa) );
$obBscEstruturalFinal->obCampoCod->setMaxLength( strlen($stMascaraEstruturalDespesa) );
$obBscEstruturalFinal->obCampoCod->setValue    ( '' );
$obBscEstruturalFinal->obCampoCod->setAlign    ("left");
$obBscEstruturalFinal->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this);");
$obBscEstruturalFinal->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraEstruturalDespesa."', this, event);");
$obBscEstruturalFinal->obCampoCod->obEvento->setOnBlur("buscaValor('mascaraClassificacaoFinal','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscEstruturalFinal->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','stCodEstruturalFinal','stDescricaoDespesaFinal','&mascClassificacao=".$stMascaraEstruturalDespesa."','".Sessao::getId()."','800','550');");

$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro(true);

$inForma = $obROrcamentoConfiguracao->getFormaExecucaoOrcamento();
if ($inForma == 1) {
    // Define Objeto SimNao para emitir relatorio com ou sem demonstração dos desdobramentos dos elementos de despesa
    $obSimNaoDemonstrar = new SimNao();
    $obSimNaoDemonstrar->setRotulo ( "Demonstrar Desdobramentos" );
    $obSimNaoDemonstrar->setTitle ( "Demonstrar os desdobramentos dos elementos de despesa.");
    $obSimNaoDemonstrar->setName   ( 'boDemonstrarDesdobramentos'      );
    $obSimNaoDemonstrar->setNull   ( true                       );
    $obSimNaoDemonstrar->setChecked( 'SIM'                      );
}
SistemaLegado::executaFramePrincipal($js);

$obSimNaoResumoRecurso = new SimNao();
$obSimNaoResumoRecurso->setRotulo("Totalizar por Recurso");
$obSimNaoResumoRecurso->setTitle("Demonstrar o resumo por recurso.");
$obSimNaoResumoRecurso->setName('radResumoRecurso');
$obSimNaoResumoRecurso->setId('radResumoRecurso');
$obSimNaoResumoRecurso->setNull(true);
$obSimNaoResumoRecurso->setChecked('Não');

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda     ('UC-02.01.22');
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnCaminho);
$obFormulario->addHidden    ($obHdnMascClassificacao);
$obFormulario->addTitulo    ("Dados para Filtro");
$obFormulario->addComponente($obCmbEntidades);
$obFormulario->addComponente($obPeriodicidade);
$obFormulario->addComponenteComposto($obTxtOrgao, $obCmbOrgao);
$obFormulario->addComponenteComposto($obTxtUnidade, $obCmbUnidade);
$obFormulario->addComponente($obISelectFuncao);
$obFormulario->addComponente($obISelectSubfuncao);
$obFormulario->addComponente($obISelectPrograma);
$obFormulario->addComponenteComposto( $obTxtPao, $obCmbPao  );
//$obFormulario->addComponente($obTxtPao);
$obFormulario->addComponente($obPopUpIntervaloDotacao);
$obFormulario->addComponente($obBscEstruturalInicial);
$obFormulario->addComponente($obBscEstruturalFinal);
$obIMontaRecursoDestinacao->geraFormulario($obFormulario);
if ($inForma == 1) {
    $obFormulario->addComponente($obSimNaoDemonstrar);
}
$obFormulario->addComponente($obSimNaoResumoRecurso);
// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario($obFormulario);

$obFormulario->OK();
$obFormulario->show();

?>

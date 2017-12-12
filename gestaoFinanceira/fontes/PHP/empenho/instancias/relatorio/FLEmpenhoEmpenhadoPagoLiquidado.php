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
    * Data de Criação   : 15/02/2005

    * @author Cassiano de Vasconcellos Ferreira
    * @author Lucas Leusin Oaigen

    * @ignore

    $Id: FLEmpenhoEmpenhadoPagoLiquidado.php 65643 2016-06-06 20:09:34Z jean $

    * Casos de uso : uc-02.03.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoHistorico.class.php"            );
include_once( CAM_GF_ORC_COMPONENTES."ISelectFuncao.class.php"            );
include_once( CAM_GF_ORC_COMPONENTES."ISelectSubfuncao.class.php"            );
include_once( CAM_GF_ORC_COMPONENTES."ISelectPrograma.class.php"            );
include_once CAM_GP_ALM_COMPONENTES.'IPopUpCentroCustoUsuario.class.php';

//ELEMENTO DESPESA
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once 'JSEmpenhoEmpenhadoPagoLiquidado.js';

$pgProc = empty($pgProc) ? null : $pgProc;
$pgOcul = "OCEmpenhoEmpenhadoPagoLiquidado.php";

//ELEMENTO DESPESA
$obROrcamentoDespesa                 = new ROrcamentoDespesa;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$rsPao = $rsOrgao = $rsUnidade = $rsRecurso = new RecordSet;

$obRUnidade = new ROrcamentoUnidadeOrcamentaria;
$obRUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
$obRUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
$obROrcamentoProjetoAtividade->setExercicio(Sessao::getExercicio());
$obROrcamentoProjetoAtividade->listarSemMascara( $rsPao );

$obREmpenhoHistorico = new REmpenhoHistorico;
$obREmpenhoHistorico->setExercicio( Sessao::getExercicio() );
$obREmpenhoHistorico->listar( $rsHistorico );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCEmpenhoEmpenhadoPagoLiquidado.php" );

//Define o objeto SelectMultiplo para armazenar os ELEMENTOS
$obCmbEntidades = new SelectMultiplo();
$obCmbEntidades->setName   ('inCodEntidade');
$obCmbEntidades->setRotulo ( "Entidades" );
$obCmbEntidades->setTitle  ( "Selecione as entidades para filtro." );
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

Sessao::write('obCmbEntidades', $obCmbEntidades);

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

$stMascaraRubrica  = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();

// Define Objeto BuscaInner para Dotacao Redutoras
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe uma dotação orçamentária." );
$obBscDespesa->setNulL   ( true                     );
$obBscDespesa->setName   ( "stNomDotacao"           );
$obBscDespesa->setId     ( "stNomDotacao"           );
$obBscDespesa->obCampoCod->setName ( "inCodDotacao" );
$obBscDespesa->obCampoCod->setId   ( "inCodDotacao" );
//Linha baixo utilizada para seguir um tamanho padrão de campo de acordo com o elemento da despesa
//Utilizado somente nesta interface
$obBscDespesa->obCampoCod->setSize      ( strlen($stMascaraRubrica)  );
$obBscDespesa->obCampoCod->setMaxLength ( 5                          );
$obBscDespesa->obCampoCod->setAlign     ("left"                      );
$obBscDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','','".Sessao::getId()."','800','550');");
$obBscDespesa->setValoresBusca ( CAM_GF_ORC_POPUPS."despesa/OCDespesa.php?".Sessao::getId(), $obForm->getName(), '');

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Selecione o órgão para filtro.");
$obTxtOrgao->setName                ( "inCodOrgaoTxt"              );
$obTxtOrgao->setSize                ( 6                            );
$obTxtOrgao->setMaxLength           ( 3                            );
$obTxtOrgao->setInteiro             ( true                         );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"                       );
$obCmbOrgao->setName                ( "inCodOrgao"                  );
$obCmbOrgao->setStyle               ( "width: 200px"                );
$obCmbOrgao->setCampoID             ( "num_orgao"                   );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"                   );
$obCmbOrgao->addOption              ( "", "Selecione"               );
$obCmbOrgao->preencheCombo          ( $rsOrgao                      );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('MontaUnidade');" );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Selecione a unidade para filtro." );
$obTxtUnidade->setName                ( "inCodUnidadeTxt"               );
$obTxtUnidade->setSize                ( 6                               );
$obTxtUnidade->setMaxLength           ( 3                               );
$obTxtUnidade->setInteiro             ( true                            );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"                       );
$obCmbUnidade->setName                ( "inCodUnidade"                  );
$obCmbUnidade->setStyle               ( "width: 200px"                  );
$obCmbUnidade->setCampoID             ( "cod_unidade"                   );
$obCmbUnidade->setCampoDesc           ( "descricao"                     );
$obCmbUnidade->addOption              ( "", "Selecione"                 );

$obISelectFuncao     = new ISelectFuncao;
$obISelectSubfuncao  = new ISelectSubfuncao;
$obISelectPrograma   = new ISelectPrograma;

$obTxtPao = new TextBox;
$obTxtPao->setRotulo              ( "PAO"                       );
$obTxtPao->setTitle               ( "Selecione o PAO para filtro." );
$obTxtPao->setName                ( "inCodPaoTxt"               );
$obTxtPao->setSize                ( 6                               );
$obTxtPao->setMaxLength           ( 4                               );
$obTxtPao->setInteiro             ( true                            );

$obCmbPao = new Select;
$obCmbPao->setRotulo              ( "PAO"                       );
$obCmbPao->setName                ( "inCodPao"                      );
$obCmbPao->setStyle               ( "width: 200px"                  );
$obCmbPao->setCampoID             ( "num_acao"                       );
$obCmbPao->setCampoDesc           ( "nom_pao"                       );
$obCmbPao->addOption              ( "", "Selecione"                 );
$obCmbPao->preencheCombo          ( $rsPao                          );

//ELEMENTO DESPESA
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa para filtro." );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this );");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

// Define Objeto BuscaInner para Fornecedor
$obBscFornecedor = new BuscaInner;
$obBscFornecedor->setRotulo                 ( "Credor"          );
$obBscFornecedor->setTitle                  ( "Informe o credor para filtro." );
$obBscFornecedor->setId                     ( "stNomFornecedor" );
$obBscFornecedor->obCampoCod->setName       ( "inCodFornecedor" );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaFornecedor();");
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodFornecedor','stNomFornecedor','','".Sessao::getId()."','800','550');");

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto Select para Histórico
$obCmbHistorico = new Select;
$obCmbHistorico->setName      ( "inCodHistorico"   );
$obCmbHistorico->setRotulo    ( "Histórico Padrão" );
$obCmbHistorico->setTitle     ( "Selecione o histórico padrão para filtro." );
$obCmbHistorico->setId        ( "inCodHistorico"   );
$obCmbHistorico->addOption    ( "", "Selecione"    );
$obCmbHistorico->setCampoId   ( "cod_historico"    );
$obCmbHistorico->setCampoDesc ( "nom_historico"    );
$obCmbHistorico->preencheCombo( $rsHistorico       );
$obCmbHistorico->setNull      ( true               );

$obTxtSituacao = new TextBox;
$obTxtSituacao->setRotulo              ( "Situação"                             );
$obTxtSituacao->setTitle               ( "Selecione a situação para filtro."       );
$obTxtSituacao->setName                ( "inSituacaoTxt"                        );
$obTxtSituacao->setSize                ( 6                                      );
$obTxtSituacao->setMaxLength           ( 3                                      );
$obTxtSituacao->setInteiro             ( true                                   );
$obTxtSituacao->setNull                ( false );
$obTxtSituacao->obEvento->setOnChange  ( "buscaValor('mostraSpanContaBanco');"  );

// Define Objeto TextBox para Codigo do Tipo de Empenho
$obTxtCodTipo = new TextBox;
$obTxtCodTipo->setName   ( "inCodTipo"                   );
$obTxtCodTipo->setId     ( "inCodTipo"                   );
$obTxtCodTipo->setRotulo ( "Tipo de Empenho"             );
$obTxtCodTipo->setTitle  ( "Selecione o tipo de empenho." );
$obTxtCodTipo->setInteiro( true  );
$obTxtCodTipo->setNull   ( true  );

include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipo, " cod_tipo <> 0 " );

// Define Objeto Select para Nome do tipo de empenho
$obCmbNomTipo = new Select;
$obCmbNomTipo->setName      ( "stNomTipo"     );
$obCmbNomTipo->setId        ( "stNomTipo"     );
$obCmbNomTipo->setCampoId   ( "cod_tipo"      );
$obCmbNomTipo->setCampoDesc ( "nom_tipo"      );
$obCmbNomTipo->addOption    ( "", "Selecione ");
$obCmbNomTipo->preencheCombo( $rsTipo         );
$obCmbNomTipo->setNull      ( true            );

// Define Objeto Select para Categoria do Empenho
include_once( TEMP."TEmpenhoCategoriaEmpenho.class.php" );

$rsCategoriaEmpenho = new RecordSet();
$obCategoriaEmpenho = new TEmpenhoCategoriaEmpenho();

$obCategoriaEmpenho->recuperaTodos($rsCategoriaEmpenho);

$obCmbCategoriaEmpenho = new Select;
$obCmbCategoriaEmpenho->setRotulo    ( "Categoria do Empenho"            );
$obCmbCategoriaEmpenho->setTitle     ( "Informe a categoria do empenho." );
$obCmbCategoriaEmpenho->setName      ( "inCodCategoria"                  );
$obCmbCategoriaEmpenho->setId        ( "inCodCategoria"                  );
$obCmbCategoriaEmpenho->setNull      ( true                              );
$obCmbCategoriaEmpenho->setStyle     ( "width: 250"                      );
$obCmbCategoriaEmpenho->setCampoId   ( "cod_categoria"                   );
$obCmbCategoriaEmpenho->setCampoDesc ( "descricao"                       );
$obCmbCategoriaEmpenho->addOption    ( "", "Selecione");
$obCmbCategoriaEmpenho->preencheCombo( $rsCategoriaEmpenho               );
$obCmbCategoriaEmpenho->obEvento->setOnChange("buscaValor('buscaContrapartida');");

$obSpanContraPartida = new Span;
$obSpanContraPartida->setId( "spnContrapartida" );

$obCmbSituacao= new Select;
$obCmbSituacao->setRotulo              ( "Situação"                     );
$obCmbSituacao->setName                ( "inSituacao"                   );
$obCmbSituacao->setStyle               ( "width: 200px"                 );
$obCmbSituacao->addOption              ( "", "Selecione"                );
$obCmbSituacao->addOption              ( "1", "Empenhados"              );
$obCmbSituacao->addOption              ( "2", "Pagos"                   );
$obCmbSituacao->addOption              ( "3", "Liquidados"              );
$obCmbSituacao->addOption              ( "4", "Anulados"                );
$obCmbSituacao->addOption              ( "5", "Estornados"              );
$obCmbSituacao->addOption              ( "6", "Estornados na Liquidação" );
$obCmbSituacao->setNull                ( false );
$obCmbSituacao->obEvento->setOnChange  ( "buscaValor('mostraSpanContaBanco');" );

$obSpanContaBanco = new Span;
$obSpanContaBanco->setId( "spnContaBanco" );

$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo              ( "Ordenação"                     );
$obCmbOrdem->setTitle               ( "Selecione a ordenação."        );
$obCmbOrdem->setName                ( "stOrdenacao"                   );
$obCmbOrdem->setStyle               ( "width: 150px"                  );
$obCmbOrdem->addOption              ( "data", "Data / Empenho"        );
$obCmbOrdem->addOption              ( "credor", "Data / Credor"       );
$obCmbOrdem->addOption              ( "credor_data", "Credor / Data"       );
$obCmbOrdem->setNull                ( true );

$obRDemonstracaoDescricaoEmpenho = new SimNao();
$obRDemonstracaoDescricaoEmpenho->setRotulo( "Demonstrar Descrição do Empenho"     );
$obRDemonstracaoDescricaoEmpenho->setTitle ( "Selecione demonstração da descrição do empenho."     );
$obRDemonstracaoDescricaoEmpenho->setName  ( "stDemonstracaoDescricaoEmpenho" );
$obRDemonstracaoDescricaoEmpenho->setChecked('Não');

$obRDemonstracaoRecursoEmpenho = new SimNao();
$obRDemonstracaoRecursoEmpenho->setRotulo( "Demonstrar Recurso do Empenho"     );
$obRDemonstracaoRecursoEmpenho->setTitle ( "Selecione demonstração do Recurso do empenho."     );
$obRDemonstracaoRecursoEmpenho->setName  ( "stDemonstracaoRecursoEmpenho" );
$obRDemonstracaoRecursoEmpenho->setChecked('Não');

$obRDemonstracaoElementoDespesa = new SimNao();
$obRDemonstracaoElementoDespesa->setRotulo( "Demonstrar Elemento de Despesa" );
$obRDemonstracaoElementoDespesa->setTitle ( "Selecione demonstração do Elemento de Despesa." );
$obRDemonstracaoElementoDespesa->setName  ( "stDemonstracaoElementoDespesa" );
$obRDemonstracaoElementoDespesa->setChecked('Não');

$obTxtEntidadeSelecionadas = new Hidden();
$obTxtEntidadeSelecionadas->setId('stEntidadeSelecionadas');
$obTxtEntidadeSelecionadas->setName('stEntidadeSelecionadas');
$obTxtEntidadeSelecionadas->setValue('');

if (Sessao::getExercicio() > '2015') {
    $obCentroCusto = new IPopUpCentroCustoUsuario($obForm);
    $obCentroCusto->setNull             ( true );
    $obCentroCusto->setRotulo           (' Centro de Custo' );
    $obCentroCusto->obCampoCod->setName ( 'inCentroCusto' );
    $obCentroCusto->obCampoCod->setId   ( 'inCentroCusto' );
}


// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obTxtEntidadeSelecionadas);
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obPeriodicidade );
if (Sessao::getExercicio() > '2015'){
    $obFormulario->addComponente( $obCentroCusto );
}
$obFormulario->addComponente( $obBscDespesa );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponente( $obISelectFuncao );
$obFormulario->addComponente( $obISelectSubfuncao );
$obFormulario->addComponente( $obISelectPrograma );
$obFormulario->addComponenteComposto( $obTxtPao, $obCmbPao  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addComponente( $obBscFornecedor             );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obFormulario->addComponente( $obCmbHistorico       );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponenteComposto( $obTxtCodTipo, $obCmbNomTipo );
$obFormulario->addComponente( $obCmbCategoriaEmpenho );
$obFormulario->addSpan( $obSpanContraPartida );
$obFormulario->addComponenteComposto( $obTxtSituacao, $obCmbSituacao  );
$obFormulario->addSpan( $obSpanContaBanco  );
$obFormulario->addComponente( $obCmbOrdem );
$obFormulario->addComponente( $obRDemonstracaoDescricaoEmpenho );
$obFormulario->addComponente( $obRDemonstracaoRecursoEmpenho );
$obFormulario->addComponente( $obRDemonstracaoElementoDespesa );

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

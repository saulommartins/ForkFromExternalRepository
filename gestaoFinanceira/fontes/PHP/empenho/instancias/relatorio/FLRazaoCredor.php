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
    * Data de Criação   : 15/04/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31874 $
    $Name$
    $Autor:$
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso: uc-02.03.11
*/

/*
$Log$
Revision 1.8  2006/07/17 17:42:06  andre.almeida
Bug #6201#

Revision 1.7  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioRazaoCredor.class.php"  );

include_once 'JSRazaoCredor.js';

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltro['inCodEntidade'] = "";
Sessao::write('filtroRelatorio', $arFiltro);
$obRegra      = new REmpenhoRelatorioRazaoCredor;

$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$rsRecordset = $rsOrgao = $rsRecurso = new RecordSet;

$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
$obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCRazaoCredor.php" );

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

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName("stExercicio");
$obHdnExercicio->setId("stExercicio");
$obHdnExercicio->setValue( Sessao::getExercicio() );

$obLblExercicio = new Label;
$obLblExercicio->setRotulo('Exercício');
$obLblExercicio->setName('stExercicio');
$obLblExercicio->setValue( Sessao::getExercicio() );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Informe o órgão para filtro");
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
$obTxtUnidade->setSize                ( 10                               );
$obTxtUnidade->setMaxLength           ( 10                               );
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
$stMascaraRubrica    = $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
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
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."'); }");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

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
$obBscFornecedor->setNull                   ( false             );
$obBscFornecedor->obCampoCod->setName       ( "inCGM"           );
$obBscFornecedor->obCampoCod->setSize       ( 10                );
$obBscFornecedor->obCampoCod->setMaxLength  ( 8                 );
$obBscFornecedor->obCampoCod->setValue      ( $inCGM            );
$obBscFornecedor->obCampoCod->setAlign      ( "left"            );
$obBscFornecedor->obCampoCod->obEvento->setOnBlur("buscaFornecedor();");
$obBscFornecedor->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stNomFornecedor','','".Sessao::getId()."','800','550');");

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obHdnExercicio );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obLblExercicio     );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obIMontaRecursoDestinacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obBscFornecedor );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>

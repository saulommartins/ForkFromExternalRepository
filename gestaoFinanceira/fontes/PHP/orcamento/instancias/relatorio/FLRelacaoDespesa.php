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

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.18
*/

/*
$Log$
Revision 1.10  2007/05/21 18:58:57  melo
Bug #9229#

Revision 1.9  2006/11/20 21:37:50  gelson
Bug #7444#
Parte 1

Revision 1.8  2006/07/17 19:58:53  leandro.zis
Bug #6395#

Revision 1.7  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRelacaoDespesa.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"  );
include_once ( CAM_GF_ORC_COMPONENTES."MontaDotacaoOrcamentaria.class.php"      );
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoProjetoAtividade.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectFuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectSubfuncao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ISelectPrograma.class.php';

include_once 'JSRelacaoDespesa.js';
$stAcao = $request->get('stAcao');

$rsPao = $rsRecordset = $rsOrgao = $rsEntidades = new RecordSet;

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

$obRegra = new ROrcamentoRelatorioRelacaoDespesa;

//Consulta Orgão
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( Sessao::getExercicio() );
$obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );
$arNomFiltro = Sessao::read('filtroNomRelatorio');
while ( !$rsOrgao->eof() ) {
    $arNomFiltro['orgao'][$rsOrgao->getCampo( 'num_orgao' )] = $rsOrgao->getCampo( 'nom_orgao' );
    $rsOrgao->proximo();
}
$rsOrgao->setPrimeiroElemento();

$obROrcamentoProjetoAtividade = new ROrcamentoProjetoAtividade;
$obROrcamentoProjetoAtividade->setExercicio(Sessao::getExercicio());
$obROrcamentoProjetoAtividade->listarSemMascara( $rsPao );

$obRegra->obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obRegra->obREntidade->setExercicio          ( Sessao::getExercicio() );
$obRegra->obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
while ( !$rsEntidades->eof() ) {
    $arNomFiltro['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
    $rsEntidades->proximo();
}
$rsEntidades->setPrimeiroElemento();

while ( !$rsPao->eof() ) {
    $arNomFiltro['pao'][$rsPao->getCampo('num_acao')] = $rsPao->getCampo('nom_pao');
    $rsPao->proximo();
}
$rsPao->setPrimeiroElemento();

Sessao::write('filtroNomRelatorio', $arNomFiltro);

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCRelacaoDespesa.php" );

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
$obTxtOrgao->setRotulo              ( "Órgão"                      );
$obTxtOrgao->setTitle               ( "Selecione o órgão orçamentário que deseja pesquisar.");
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

//UNIDADE
$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"                       );
$obTxtUnidade->setTitle               ( "Selecione a unidade orçamentária que deseja pesquisar." );
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

$obISelectFuncao     = new ISelectFuncao;
$obISelectSubfuncao  = new ISelectSubfuncao;
$obISelectPrograma   = new ISelectPrograma;

$obTxtPao = new TextBox;
$obTxtPao->setRotulo              ( "PAO"                       );
$obTxtPao->setTitle               ( "Selecione o PAO para filtro." );
$obTxtPao->setName                ( "inCodPaoTxt"               );
$obTxtPao->setValue               ( $inCodPaoTxt                );
$obTxtPao->setSize                ( 6                               );
$obTxtPao->setMaxLength           ( 4                               );
$obTxtPao->setInteiro             ( true                            );

$obCmbPao = new Select;
$obCmbPao->setRotulo              ( "PAO"                       );
$obCmbPao->setName                ( "inCodPao"                      );
$obCmbPao->setValue               ( $inCodPao                       );
$obCmbPao->setStyle               ( "width: 200px"                  );
$obCmbPao->setCampoID             ( "num_acao"                       );
$obCmbPao->setCampoDesc           ( "nom_pao"                       );
$obCmbPao->addOption              ( "", "Selecione"                 );
$obCmbPao->preencheCombo          ( $rsPao                          );

//ELEMENTO DESPESA
$obROrcamentoDespesa = new ROrcamentoDespesa;
$stMascaraRubrica  = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
$obBscRubricaDespesa = new BuscaInner;
$obBscRubricaDespesa->setRotulo               ( "Elemento de Despesa" );
$obBscRubricaDespesa->setTitle                ( "Informe o elemento de despesa para filtro." );
$obBscRubricaDespesa->setId                   ( "stDescricaoDespesa" );
$obBscRubricaDespesa->obCampoCod->setName     ( "inCodDespesa" );
$obBscRubricaDespesa->obCampoCod->setSize     ( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setMaxLength( strlen($stMascaraRubrica) );
$obBscRubricaDespesa->obCampoCod->setValue    ( '' );
$obBscRubricaDespesa->obCampoCod->setAlign    ("left");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnFocus("selecionaValorCampo( this);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnKeyUp("mascaraDinamico('".$stMascaraRubrica."', this, event);");
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur("buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscRubricaDespesa->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');");

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

//Radios de Tipo de Relatório
$obRdOrdenacaoEstrutural = new Radio;
$obRdOrdenacaoEstrutural->setRotulo ( "Ordenação" );
$obRdOrdenacaoEstrutural->setTitle  ( "Selecione a ordenação." );
$obRdOrdenacaoEstrutural->setChecked( true );
$obRdOrdenacaoEstrutural->setName   ( "stTipoOrdenacao" );
$obRdOrdenacaoEstrutural->setValue  ( "estrutural");
$obRdOrdenacaoEstrutural->setLabel  ( "Por Código Estrutural" );
$obRdOrdenacaoEstrutural->setNull   ( false );

$obRdOrdenacaoReduzido = new Radio;
$obRdOrdenacaoReduzido->setName   ( "stTipoOrdenacao" );
$obRdOrdenacaoReduzido->setValue  ( "reduzido" );
$obRdOrdenacaoReduzido->setLabel  ( "Por Código Reduzido" );
$obRdOrdenacaoReduzido->setNull   ( false );

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
$arAssinaturas = Sessao::read('assinaturas');
$arAssinaturas['papeis'] = array();
Sessao::write('assinaturas',$arAssinaturas);

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;
$obMontaAssinaturas->setEventosCmbEntidades ( $obCmbEntidades );

$obFormulario = new Formulario;
$obFormulario->setAjuda     ( "UC-02.01.18" );
$obFormulario->addForm      ( $obForm );
$obFormulario->addHidden    ( $obHdnCaminho );
$obFormulario->addHidden    ( $obHdnMascClassificacao );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponenteComposto   ( $obTxtOrgao, $obCmbOrgao                  );
$obFormulario->addComponenteComposto   ( $obTxtUnidade, $obCmbUnidade              );
$obFormulario->addComponente( $obISelectFuncao );
$obFormulario->addComponente( $obISelectSubfuncao );
$obFormulario->addComponente( $obISelectPrograma );
$obFormulario->addComponenteComposto( $obTxtPao, $obCmbPao  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->agrupaComponentes( array($obRdOrdenacaoEstrutural,$obRdOrdenacaoReduzido));

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();
?>

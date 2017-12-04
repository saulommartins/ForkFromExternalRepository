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
    * Filtro do relatório Relação de Despesa Orçamentária
    * Data de Criação   : 02/04/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Pacuslki Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php"   );
include_once( CAM_GF_ORC_COMPONENTES."ISelectFuncao.class.php"            );
include_once( CAM_GF_ORC_COMPONENTES."ISelectSubfuncao.class.php"            );
include_once( CAM_GF_ORC_COMPONENTES."ISelectPrograma.class.php"            );
include_once ( CAM_GF_CONT_COMPONENTES. "IIntervaloPopUpContaBanco.class.php" );

//ELEMENTO DESPESA
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );

include_once 'JSRelacaoDespesaOrcamentaria.js';

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

$obForm = new Form;
$obForm->setAction( "OCGeraRelacaoDespesaOrcamentaria.php" );
$obForm->setTarget( "telaPrincipal" );

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

//DOTAÇÃO ORÇAMENTÁRIA
$stMascaraRubrica  = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
// Define Objeto BuscaInner para Dotacao Redutoras
$obBscDespesa = new BuscaInner;
$obBscDespesa->setRotulo ( "Dotação Orçamentária"   );
$obBscDespesa->setTitle  ( "Informe a Dotação Orçamentária que deseja pesquisar" );
$obBscDespesa->setNulL   ( true                     );
$obBscDespesa->setId     ( "stNomDotacao"           );
$obBscDespesa->obCampoCod->setName ( "inCodDotacao" );
$obBscDespesa->obCampoCod->setId   ( "inCodDotacao" );
$obBscDespesa->obCampoCod->setSize      ( strlen($stMascaraRubrica)  );
$obBscDespesa->obCampoCod->setMaxLength ( 5                          );
$obBscDespesa->obCampoCod->setAlign     ("left"                      );
$obBscDespesa->obCampoCod->obEvento->setOnBlur ("if (this.value!='') { buscaValor('buscaDotacao','".$pgOcul."','".$pgList."','','".Sessao::getId()."'); }");
$obBscDespesa->obImagem->setId('imgDespesa');

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
$obCmbPao->setCampoID             ( "num_acao"                      );
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

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

$obTxtEntidadeSelecionadas = new Hidden();
$obTxtEntidadeSelecionadas->setId('stEntidadeSelecionadas');
$obTxtEntidadeSelecionadas->setName('stEntidadeSelecionadas');
$obTxtEntidadeSelecionadas->setValue('');

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

//Define o objeto INNER para armazenar a Conta Banco
$obBscContaBanco = new IIntervaloPopUpContaBanco;

$obCmbOrdem = new Select;
$obCmbOrdem->setRotulo   ( "Ordenação" );
$obCmbOrdem->setTitle    ( "Selecione a ordenação."   );
$obCmbOrdem->setName     ( "inOrdenacao"              );
$obCmbOrdem->setStyle    ( "width: 150px"             );
$obCmbOrdem->addOption   ( "1", "Selecione"           );
$obCmbOrdem->addOption   ( "2", "Conta Banco"         );
$obCmbOrdem->addOption   ( "3", "Recurso"             );
$obCmbOrdem->addOption   ( "4", "Dotação"             );
$obCmbOrdem->addOption   ( "5", "Elemento de Despesa" );
$obCmbOrdem->setNull     ( true );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Dados para Filtro" );
$obFormulario->addComponente( $obCmbEntidades      );
$obFormulario->addComponente( $obPeriodicidade );
$obFormulario->addComponente( $obBscDespesa );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponente( $obISelectFuncao );
$obFormulario->addComponente( $obISelectSubfuncao );
$obFormulario->addComponente( $obISelectPrograma );
$obFormulario->addComponenteComposto( $obTxtPao, $obCmbPao  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addComponente( $obBscContaBanco         );
$obFormulario->addHidden( $obHdnMascClassificacao   );
$obFormulario->addHidden    ( $obTxtEntidadeSelecionadas);
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obCmbOrdem );

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

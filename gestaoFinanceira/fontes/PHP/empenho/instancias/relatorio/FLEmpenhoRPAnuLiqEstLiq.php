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

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-02 08:44:54 -0200 (Qua, 02 Jan 2008) $

    * Casos de uso : uc-02.03.08
*/

/*
$Log$
Revision 1.12  2006/08/09 18:10:27  jose.eduardo
Bug #6737#

Revision 1.11  2006/08/09 18:09:16  jose.eduardo
Bug #6702#

Revision 1.10  2006/08/07 14:32:52  jose.eduardo
Bug #6280#

Revision 1.9  2006/07/17 17:12:10  andre.almeida
Bug #6198#

Revision 1.8  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

//ELEMENTO DESPESA
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );

include_once 'JSEmpenhoRPAnuLiqEstLiq.js';

$obROrcamentoConfiguracao = new ROrcamentoConfiguracao;
$obROrcamentoConfiguracao->consultarConfiguracao();

//ELEMENTO DESPESA
$obROrcamentoDespesa                 = new ROrcamentoDespesa;

$obREntidade = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
$rsRecordset = new RecordSet;

$rsExercicio = $rsOrgao = $rsUnidade = $rsRecurso = new RecordSet;

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->recuperaExerciciosRP( $rsExercicio );

/* $obROrcamentoRecurso = new ROrcamentoRecurso;
$obROrcamentoRecurso->listar( $rsRecurso ); */

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_EMP_INSTANCIAS."relatorio/OCEmpenhoRPAnuLiqEstLiq.php" );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setNull           ( false             );
$obPeriodicidade->setValue          ( 4                 );

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
$obCmbExercicio->obEvento->setOnChange  ( "buscaValor('MontaOrgao');"   );
$obCmbExercicio->setNull                ( false                         );

// Define Objeto Span Para Orgao e Unidade
$obSpan = new Span;
$obSpan->setId( "spnOrgaoUnidade" );

//ELEMENTO DESPESA
$stMascaraRubrica    = $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaMascara();
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
$obBscRubricaDespesa->obCampoCod->obEvento->setOnBlur ("buscaValor('mascaraClassificacao','".$pgOcul."','".$pgProc."','oculto','".Sessao::getId()."');");
$obBscRubricaDespesa->setFuncaoBusca( "abrePopUp('".CAM_GF_ORC_POPUPS."classificacaodespesa/FLClassificacaoDespesa.php','frm','inCodDespesa','stDescricaoDespesa','&mascClassificacao=".$stMascaraRubrica."','".Sessao::getId()."','800','550');" );

$obHdnMascClassificacao = new Hidden;
$obHdnMascClassificacao->setName ( "stMascClassificacao" );
$obHdnMascClassificacao->setValue( $stMascaraRubrica );

$maxLenghtRecurso = strlen($obROrcamentoConfiguracao->getMascRecurso());

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

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
$obCmbSituacao->addOption              ( "3", "Anulados (Liquidados)"   );
$obCmbSituacao->setNull                ( false );

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
//$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao  );
//$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade  );
$obFormulario->addComponente( $obBscRubricaDespesa         );
$obFormulario->addHidden( $obHdnMascClassificacao   );
//$obFormulario->addComponenteComposto( $obTxtRecurso, $obCmbRecurso  );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponenteComposto( $obTxtSituacao, $obCmbSituacao  );

$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->OK();
$obFormulario->show();

?>

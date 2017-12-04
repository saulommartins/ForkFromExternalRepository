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
    * Página de Formulario do relatório Razão da Despesa
    * Data de Criação   : 06/03/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Tonismar R. Bernardo

    * @ignore

    * $Id: FLRazaoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-02.01.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioRazaoDespesa.class.php";
include_once 'JSRazaoDespesa.js';

Sessao::remove('filtroRelatorio');
Sessao::remove('arRecordSet');
Sessao::remove('arRecordSet1');

$obROrcamentoRelatorioRazaoDespesao = new ROrcamentoRelatorioRazaoDespesa;

$rsDesdobramento = new RecordSet;

$obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidades , ' ORDER BY cod_entidade' );
// $obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
//$obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaDotacao( $rsDesdobramento );
$obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio( Sessao::getExercicio() );
$obROrcamentoRelatorioRazaoDespesao->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->listar( $rsOrgao );

$obForm = new Form;
$obForm->setAction( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCodReduzido = new Hidden;
$obHdnCodReduzido->setName ( "inCodReduzido" );
$obHdnCodReduzido->setValue( $inCodReduzido  );

$obHdnCodEstrutural = new Hidden;
$obHdnCodEstrutural->setName ( "inCodEstrutural" );
$obHdnCodEstrutural->setValue( $inCodEstrutural  );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName("stCaminho");
$obHdnCaminho->setValue( CAM_GF_ORC_INSTANCIAS."relatorio/OCRazaoDespesa.php" );

$obTxtEntidade = new TextBox;
$obTxtEntidade->setRotulo              ( "Entidade"           );
$obTxtEntidade->setName                ( "inCodEntidadeTxt"   );
$obTxtEntidade->setValue               ( $inCodEntidadeTxt    );
$obTxtEntidade->setSize                ( 10                   );
$obTxtEntidade->setMaxLength           ( 10                   );
$obTxtEntidade->setNull                ( false                );
$obTxtEntidade->setInteiro             ( true                 );
$obTxtEntidade->obEvento->setOnBlur    ( "buscaValor('limpaCombos');" );
$obTxtEntidade->setTitle  ( "Selecione a entidade para o filtro" );

//$obCmbEntidades = new Select;
//$obCmbEntidades->setNull   ( false );
//$obCmbEntidades->setName   ('inCodEntidade');
//$obCmbEntidades->setRotulo ( "Entidade" );
//$obCmbEntidades->setTitle  ( "Selecione as entidades para o filtro." );
//$obCmbEntidades->setValue  ( $inCodEntidade );
//$obCmbEntidades->setCampoID( 'cod_entidade' );
//$obCmbEntidades->setCampoDesc( '[cod_entidade]-[nom_cgm]' );
//$obCmbEntidades->addOption ( '','Selecione' );
//$obCmbEntidades->preencheCombo( $rsEntidades );
//$obCmbEntidades->obEvento->setOnChange ( "buscaValor('limpaCombos');" );

include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );

$obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obITextBoxSelectEntidadeUsuario->obTextBox->obEvento->setOnChange( "buscaValor('limpaCombos');getIMontaAssinaturas()" );
$obITextBoxSelectEntidadeUsuario->obSelect->obEvento->setOnChange( "buscaValor('limpaCombos');getIMontaAssinaturas()" );

$obDtPeriodicidade = new Periodicidade();
$obDtPeriodicidade->setExercicio( Sessao::getExercicio() );
$obDtPeriodicidade->setNull     ( false              );
$obDtPeriodicidade->setValue    ( 4                  );
$obDtPeriodicidade->setValidaExercicio( true );
$obDtPeriodicidade->setTitle    ( 'Informe a periodicidade' );

$obTxtOrgao = new TextBox;
$obTxtOrgao->setRotulo              ( "Órgão"           );
$obTxtOrgao->setTitle               ( "Informe o órgão" );
$obTxtOrgao->setName                ( "inNumOrgaoTxt"   );
$obTxtOrgao->setValue               ( $inNumOrgaoTxt    );
$obTxtOrgao->setSize                ( 10                );
$obTxtOrgao->setMaxLength           ( 10                );
$obTxtOrgao->setInteiro             ( true              );
$obTxtOrgao->obEvento->setOnChange  ( "buscaValor('carregaUnidade');");

$obCmbOrgao = new Select;
$obCmbOrgao->setRotulo              ( "Órgão"             );
$obCmbOrgao->setName                ( "inNumOrgao"        );
$obCmbOrgao->setValue               ( $inNumOrgao         );
$obCmbOrgao->setStyle               ( "width: 200px"      );
$obCmbOrgao->setCampoID             ( "num_orgao"         );
$obCmbOrgao->setCampoDesc           ( "nom_orgao"         );
$obCmbOrgao->addOption              ( "", "Selecione"     );
$obCmbOrgao->preencheCombo          ( $rsOrgao            );
$obCmbOrgao->obEvento->setOnChange  ( "buscaValor('carregaUnidade');" );

$obTxtUnidade = new TextBox;
$obTxtUnidade->setRotulo              ( "Unidade"           );
$obTxtUnidade->setTitle               ( "Informe a unidade" );
$obTxtUnidade->setName                ( "inNumUnidadeTxt"   );
$obTxtUnidade->setValue               ( $inNumUnidadeTxt    );
$obTxtUnidade->setSize                ( 10                  );
$obTxtUnidade->setMaxLength           ( 10                  );
$obTxtUnidade->setInteiro             ( true                );

$obCmbUnidade= new Select;
$obCmbUnidade->setRotulo              ( "Unidade"            );
$obCmbUnidade->setName                ( "inNumUnidade"       );
$obCmbUnidade->setValue               ( $inNumUnidade        );
$obCmbUnidade->setStyle               ( "width: 200px"       );
$obCmbUnidade->setCampoID             ( "num_unidade"        );
$obCmbUnidade->setCampoDesc           ( "descricao"          );
$obCmbUnidade->addOption              ( "", "Selecione"      );

$obBscDotacao = new BuscaInner;
$obBscDotacao->setRotulo ( "Dotação Orçamentária"   );
$obBscDotacao->setTitle  ( "Informe a dotação orçamentária."                       );
$obBscDotacao->setNulL   ( true                     );
$obBscDotacao->setId     ( "stNomDotacao"   );
$obBscDotacao->setValue  ( $stNomDotacao    );
$obBscDotacao->obCampoCod->setName ( "inCodDotacao" );
$obBscDotacao->obCampoCod->setSize ( 10 );
$obBscDotacao->obCampoCod->setMaxLength( 5 );
$obBscDotacao->obCampoCod->setValue ( $inCodDotacao );
$obBscDotacao->obCampoCod->setAlign ("left");
$obBscDotacao->obCampoCod->obEvento->setOnChange("BloqueiaFrames(true,false); buscaValor('buscaDotacao');");
$obBscDotacao->obCampoCod->obEvento->setOnBlur("BloqueiaFrames(true,false); buscaValor('buscaDotacao');");
$obBscDotacao->setFuncaoBusca("abrePopUp('".CAM_GF_ORC_POPUPS."despesa/LSDespesa.php','frm','inCodDotacao','stNomDotacao','inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");

$obCmbDesdobramento= new Select;
$obCmbDesdobramento->setRotulo      ( "Desdobramento"      );
$obCmbDesdobramento->setTitle       ( "Selecione desdobramento"      );
$obCmbDesdobramento->setName        ( "inCodDesdobramento" );
$obCmbDesdobramento->setValue       ( $inCodDesdobramento  );
$obCmbDesdobramento->setStyle       ( "width: 600px"       );
$obCmbDesdobramento->setCampoID     ( "cod_estrutura"      );
$obCmbDesdobramento->setCampoDesc   ( "[cod_estrutural]-[descricao]");
$obCmbDesdobramento->addOption      ( "", "Selecione"      );
$obCmbDesdobramento->preencheCombo  ( $rsDesdobramento     );
//$obCmbDesdobramento->obEvento->setOnChange("buscaValor('carregaOrgao');");

include_once(CAM_GF_ORC_COMPONENTES."IMontaRecursoDestinacao.class.php");
$obIMontaRecursoDestinacao = new IMontaRecursoDestinacao;
$obIMontaRecursoDestinacao->setFiltro ( true );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioEmpenhoAnulacao = new SimNao();
$obRadioEmpenhoAnulacao->setRotulo ( 'Demonstrar Empenho/Anulação' );
$obRadioEmpenhoAnulacao->setTitle  ( 'Selecione se deseja demonstrar empenho/anulação.' );
$obRadioEmpenhoAnulacao->setName   ( 'boEmpenhoAnulacao' );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioLiquidacaoAnulacao = new SimNao();
$obRadioLiquidacaoAnulacao->setRotulo ( 'Demonstrar Liquidação/Anulação' );
$obRadioLiquidacaoAnulacao->setName   ( 'boLiquidacaoAnulacao' );
$obRadioLiquidacaoAnulacao->setTitle  ( 'Selecione se deseja demonstrar liquidação/anulação.' );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioPagamentoEstorno = new SimNao();
$obRadioPagamentoEstorno->setRotulo ( 'Demonstrar Pagamento/Estorno' );
$obRadioPagamentoEstorno->setName   ( 'boPagamentoEstorno' );
$obRadioPagamentoEstorno->setTitle  ( 'Selecione se deseja demonstrar pagamento/estorno.' );

// Define Objeto SimNao para demonstrar liquidacao
$obRadioSuplementacaoReducao = new SimNao();
$obRadioSuplementacaoReducao->setRotulo ( 'Demonstrar Suplementação/Redução' );
$obRadioSuplementacaoReducao->setName   ( 'boSuplementacaoReducao' );
$obRadioSuplementacaoReducao->setTitle  ( 'Selecione se deseja demonstrar suplementação/redução.' );

// Instanciação do objeto Lista de Assinaturas
// Limpa papeis das Assinaturas na Sessão
//sessao->assinaturas['papeis'] = array();

include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obMontaAssinaturas = new IMontaAssinaturas;

$obFormulario = new Formulario();
$obFormulario->setAjuda( "UC-02.01.32" );
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( "Dados para Filtro" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCaminho );
$obFormulario->addHidden( $obHdnCodReduzido );
$obFormulario->addHidden( $obHdnCodEstrutural );
//$obFormulario->addComponenteComposto( $obTxtEntidade, $obCmbEntidades );
$obFormulario->addComponente( $obITextBoxSelectEntidadeUsuario );
$obFormulario->addComponente( $obDtPeriodicidade );
$obFormulario->addComponente( $obBscDotacao );
$obFormulario->addComponente( $obCmbDesdobramento );
$obFormulario->addComponenteComposto( $obTxtOrgao, $obCmbOrgao );
$obFormulario->addComponenteComposto( $obTxtUnidade, $obCmbUnidade );
$obIMontaRecursoDestinacao->geraFormulario ( $obFormulario );
$obFormulario->addComponente( $obRadioEmpenhoAnulacao );
$obFormulario->addComponente( $obRadioLiquidacaoAnulacao );
$obFormulario->addComponente( $obRadioPagamentoEstorno );
$obFormulario->addComponente( $obRadioSuplementacaoReducao );

// Injeção de código no formulário
$obMontaAssinaturas->geraFormulario( $obFormulario );

$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

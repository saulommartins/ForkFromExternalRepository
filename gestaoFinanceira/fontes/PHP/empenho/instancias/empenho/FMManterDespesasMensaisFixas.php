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
    * Página de Formulário de Empenhamento de Despesas Mensais Fixas
    * Data de Criação : 08/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2007-05-28 12:45:48 -0300 (Seg, 28 Mai 2007) $

    * Casos de uso: uc-02.03.30
*/

/**

$Log$
Revision 1.11  2007/05/28 15:45:48  cako
Bug #9177#

Revision 1.10  2006/12/21 15:52:32  rodrigo
Bloqueio das rotinas do bando cd dados apos a virada de ano.

Revision 1.9  2006/11/16 22:24:11  gelson
Bug #7305#

Revision 1.8  2006/11/13 20:04:45  cleisson
Bug #7446#

Revision 1.7  2006/11/09 23:07:06  cleisson
Bug #7324#

Revision 1.6  2006/10/26 12:41:13  cako
Bug #7205#

Revision 1.5  2006/10/21 16:35:04  tonismar
bug #7259#

Revision 1.4  2006/10/21 16:28:34  tonismar
bug #7259#

Revision 1.3  2006/09/26 17:58:01  tonismar
Manter Empenho Despesas Mensais Fixas

Revision 1.2  2006/09/22 11:39:21  tonismar
*** empty log message ***

Revision 1.1  2006/09/08 17:56:08  tonismar
desenvolvendo uc-02.03.30

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ILabelEntidade.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoConfiguracao.class.php" );
include_once ( TEMP."TEmpenhoTipoDespesaFixa.class.php" );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterDespesasMensaisFixas";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJS          = "JS".$stPrograma.".js";

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true') {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    Sessao::remove('arItens');
    include $pgJS;
    $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho();
    $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento( $stFormaExecucao );
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoTipoEmpenho->listar( $rsTipoEmpenho );

    $obREmpenhoConfiguracao = new REmpenhoConfiguracao();
    $obREmpenhoConfiguracao->consultar();

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

    if ( empty( $stAcao ) ) {
        $stAcao = "incluir";
    }

    $obForm = new Form;
    $obForm->setAction ( $pgProc    );
    $obForm->setTarget ( "oculto"   );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao"   );
    $obHdnAcao->setValue( $stAcao   );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName( "stCtrl"   );
    $obHdnCtrl->setValue( ""        );

    $obHdnContrato = new Hidden;
    $obHdnContrato->setName ( "hdnContrato" );
    $obHdnContrato->setValue( $inContrato   );

    $obHdnId = new Hidden;
    $obHdnId->setName( "id" );
    $obHdnId->setValue( $id );

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName( "inCodEntidade" );
    $obHdnCodEntidade->setValue( $inCodEntidade );

    $obHdnDtVencimento = new Hidden;
    $obHdnDtVencimento->setName( "dtVencimento" );
    $obHdnDtVencimento->setValue( $dtVencimento );

    $obHdnCodDespesa = new Hidden;
    $obHdnCodDespesa->setName( "inCodDespesa" );
    $obHdnCodDespesa->setValue( $inCodDespesa );

    $obHdnCodDespesaFixa = new Hidden;
    $obHdnCodDespesaFixa->setName( "inCodDespesaFixa" );
    $obHdnCodDespesaFixa->setValue( $inCodDespesaFixa );

    $obHdnCodFornecedor = new Hidden;
    $obHdnCodFornecedor->setName( "inCodFornecedor" );
    $obHdnCodFornecedor->setValue( $inCodFornecedor );

    $obHdnNumOrgao = new Hidden;
    $obHdnNumOrgao->setName( "inNumOrgao" );
    $obHdnNumOrgao->setValue( $inNumOrgao );

    $obHdnLocal = new Hidden;
    $obHdnLocal->setName( "stNomLocal" );
    $obHdnLocal->setValue( $stNomLocal );

    $obHdnDespesa = new Hidden;
    $obHdnDespesa->setName( "stNomDespesa" );
    $obHdnDespesa->setValue( $stNomDespesa );

    $obHdnTipoDespesaFixa = new Hidden;
    $obHdnTipoDespesaFixa->setName( "stTipoDespesaFixa" );
    $obHdnTipoDespesaFixa->setValue( $stTipoDespesaFixa );

    $obHdnNumUnidade = new Hidden;
    $obHdnNumUnidade->setName( "inNumUnidade" );
    $obHdnNumUnidade->setValue( $inNumUnidade );

    $obHdnValorTotal = new Hidden;
    $obHdnValorTotal->setName( "flValorTotalItem" );
    $obHdnValorTotal->setValue( $flValorTotalItem );

    $obTEmpenhoTipoDespesaFixa = new TEmpenhoTipoDespesaFixa();
    $obTEmpenhoTipoDespesaFixa->recuperaTodos( $rsTipo );

    $obTxtTipo = new TextBox;
    $obTxtTipo->setName   ( "inCodTipo"         );
    $obTxtTipo->setId     ( "inCodTipo"         );
    $obTxtTipo->setValue  ( $inCodTipo          );
    $obTxtTipo->setRotulo ( "Tipo Despesa"      );
    $obTxtTipo->setTitle  ( "Selecione o Tipo de Despesa." );
    $obTxtTipo->setInteiro( true                );
    $obTxtTipo->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodTipo='+this.value,'carregaIdentificador');" );
    $obTxtTipo->setNull   ( false               );

    $obCmbTipo = new Select;
    $obCmbTipo->setName      ( "stNomeTipo"    );
    $obCmbTipo->setId        ( "stNomeTipo"    );
    $obCmbTipo->setValue     ( $inCodTipo      );
    $obCmbTipo->addOption    ( "", "Selecione" );
    $obCmbTipo->setCampoId   ( "cod_tipo"      );
    $obCmbTipo->setCampoDesc ( "descricao"     );
    $obCmbTipo->preencheCombo( $rsTipo         );
    $obCmbTipo->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodTipo='+this.value,'carregaIdentificador');" );
    $obCmbTipo->setNull      ( false );

    // Define Objeto TextBox para Codigo do Tipo de Empenho
    $obTxtCodTipo = new TextBox;
    $obTxtCodTipo->setName   ( "inCodTipoEmpenho"            );
    $obTxtCodTipo->setId     ( "inCodTipoEmpenho"            );
    $obTxtCodTipo->setValue  ( $inCodTipoEmpenho             );
    $obTxtCodTipo->setRotulo ( "Tipo de Empenho"             );
    $obTxtCodTipo->setTitle  ( "Selecione o tipo de empenho." );
    $obTxtCodTipo->setInteiro( true  );
    $obTxtCodTipo->setNull   ( false );

    // Define Objeto Select para Nome do tipo de empenho
    $obCmbNomTipo = new Select;
    $obCmbNomTipo->setName      ( "stNomTipoEmpenho"     );
    $obCmbNomTipo->setId        ( "stNomTipoEmpenho"     );
    $obCmbNomTipo->setValue     ( $inCodTipoEmpenho      );
    $obCmbNomTipo->addOption    ( "", "Selecione" );
    $obCmbNomTipo->setCampoId   ( "cod_tipo"      );
    $obCmbNomTipo->setCampoDesc ( "nom_tipo"      );
    $obCmbNomTipo->preencheCombo( $rsTipoEmpenho         );
    $obCmbNomTipo->setNull      ( false           );

    $obCmbIdentificador = new Select;
    $obCmbIdentificador->setName      ( "stIdentificador"    );
    $obCmbIdentificador->setId        ( "stIdentificador"    );
    $obCmbIdentificador->setValue     ( $stIdentificador     );
    $obCmbIdentificador->addOption    ( "", "Selecione"      );
    $obCmbIdentificador->setRotulo    ( "Identificador"      );
    $obCmbIdentificador->setCampoId   ( "num_identificacao"  );
    $obCmbIdentificador->setCampoDesc ( "num_identificacao"  );
    $obCmbIdentificador->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodTipo='+document.frm.inCodTipo.value+'&stIdentificador='+this.value,'carregaDespesasFixas');" );
    $obCmbIdentificador->setNull      ( false );

    //$obILabelEntidade = new ILabelEntidade($obForm);
    //$obILabelEntidade->setMostraCodigo(true);

    $obILabelEntidade = new Label();
    $obILabelEntidade->setName( 'stEntidade' );
    $obILabelEntidade->setId  ( 'stEntidade' );
    $obILabelEntidade->setRotulo( 'Entidade' );

    $obLblCredor = new Label();
    $obLblCredor->setName( 'stCredor' );
    $obLblCredor->setId  ( 'stCredor' );
    $obLblCredor->setRotulo( 'Credor' );

    $obLblDotacao = new Label();
    $obLblDotacao->setName( 'stDotacao' );
    $obLblDotacao->setId  ( 'stDotacao' );
    $obLblDotacao->setRotulo( 'Dotação' );

    $rsDesdobramento = new RecordSet();
    $obCmbDesdobramento = new Select;
    $obCmbDesdobramento->setRotulo               ( "Desdobramento" );
    $obCmbDesdobramento->setTitle                ( "Informe o desdobramento da despesa." );
    $obCmbDesdobramento->setName                 ( "stDesdobramento" );
    $obCmbDesdobramento->setId                   ( "stDesdobramento" );
    $obCmbDesdobramento->setValue                ( $stDesdobramento );
    $obCmbDesdobramento->setStyle                ( "width: 600" );
    $obCmbDesdobramento->setNull                 ( ($stFormaExecucao) ? false : true );
    $obCmbDesdobramento->setDisabled             ( ($stFormaExecucao) ? false : true );
    $obCmbDesdobramento->addOption               ( "", "Selecione" );
    $obCmbDesdobramento->setCampoId              ( "cod_estrutural" );
    $obCmbDesdobramento->setCampoDesc            ( "cod_estrutural" );
    $obCmbDesdobramento->preencheCombo           ( $rsDesdobramento);

    $obSpanSaldo = new Span;
    $obSpanSaldo->setId( "spnSaldoDotacao" );

    $obDataEmpenho = new Data();
    $obDataEmpenho->setName ( 'stDtEmpenho' );
    $obDataEmpenho->setValue( $stDtEmpenho  );
    $obDataEmpenho->setRotulo( 'Data do Empenho' );
    $obDataEmpenho->obEvento->setOnChange(" validaDataEmpenho(); montaParametrosGET('recuperaDataVencimento');" );

    $obHdnDtUltimoEmpenho = new Hidden();
    $obHdnDtUltimoEmpenho->setId   ('dtUltimaDataEmpenho');
    $obHdnDtUltimoEmpenho->setName ('dtUltimaDataEmpenho');

    $obLblDataVencimento = new Label();
    $obLblDataVencimento->setName( 'stDtVencimento' );
    $obLblDataVencimento->setId  ( 'stDtVencimento' );
    $obLblDataVencimento->setRotulo( 'Data do Vencimento' );
    $obLblDataVencimento->setValue( $stDtVencimento );

    $obLblContrato = new Label();
    $obLblContrato->setName( 'inContrato' );
    $obLblContrato->setId  ( 'inContrato' );
    $obLblContrato->setRotulo( 'Contrato' );

    $obLblLocal = new Label();
    $obLblLocal->setName( 'stLocal' );
    $obLblLocal->setId  ( 'stLocal' );
    $obLblLocal->setRotulo( 'Local' );

    $obLblHistorico = new Label();
    $obLblHistorico->setName( 'stHistorico' );
    $obLblHistorico->setId  ( 'stHistorico' );
    $obLblHistorico->setRotulo( 'Histórico' );

    $obTxtComplemento = new TextBox;
    $obTxtComplemento->setName   ( "stComplemento" );
    $obTxtComplemento->setId     ( "stComplemento" );
    $obTxtComplemento->setValue  ( $stComplemento  );
    $obTxtComplemento->setRotulo ( "Complemento"   );
    $obTxtComplemento->setTitle  ( "Informe o complemento." );
    $obTxtComplemento->setSize   ( 60 );
    $obTxtComplemento->setMaxLength( 160 );

    $obTxtConsumo = new TextBox;
    $obTxtConsumo->setName   ( "stConsumo" );
    $obTxtConsumo->setId     ( "stConsumo" );
    $obTxtConsumo->setValue  ( $stConsumo  );
    $obTxtConsumo->setRotulo ( "Consumo"   );
    $obTxtConsumo->setTitle  ( "Informe o consumo." );
    $obTxtConsumo->setObrigatorioBarra(true);
    $obTxtConsumo->setInteiro( true );

    $obTxtValor = new Moeda;
    $obTxtValor->setName  ( 'flValor' );
    $obTxtValor->setId    ( 'flValor' );
    $obTxtValor->setValue ( $flValor  );
    $obTxtValor->setRotulo( 'Valor'   );
    $obTxtValor->setTitle ( 'Informe o valor.' );
    $obTxtValor->setObrigatorioBarra(true);

    $obDtDocumento = new Data;
    $obDtDocumento->setName  ('dtDataDocumento' );
    $obDtDocumento->setRotulo('Data do Documento' );
    $obDtDocumento->setTitle ('Informe a data do documento.' );
    $obDtDocumento->setValue ( $dtDataDocumento );
    $obDtDocumento->setObrigatorioBarra(true);

    $obSpan = new Span;
    $obSpan->setId( "spnLista" );

    $obLblTotal = new Label;
    $obLblTotal->setId( "inTotal" );
    $obLblTotal->setRotulo( "Total" );

    $obREmpenhoAutorizacaoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );

    $obMontaAtributos = new MontaAtributos;
    $obMontaAtributos->setTitulo     ( "Atributos"  );
    $obMontaAtributos->setName       ( "Atributo_"  );
    $obMontaAtributos->setRecordSet  ( $rsAtributos );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addHidden( $obHdnAcao );
    $obFormulario->addHidden( $obHdnCtrl );
    $obFormulario->addHidden( $obHdnContrato );
    $obFormulario->addHidden( $obHdnCodEntidade );
    $obFormulario->addHidden( $obHdnId );
    $obFormulario->addHidden( $obHdnDtVencimento );
    $obFormulario->addHidden( $obHdnCodFornecedor );
    $obFormulario->addHidden( $obHdnNumOrgao );
    $obFormulario->addHidden( $obHdnLocal );
    $obFormulario->addHidden( $obHdnCodDespesa );
    $obFormulario->addHidden( $obHdnDespesa );
    $obFormulario->addHidden( $obHdnNumUnidade );
    $obFormulario->addHidden( $obHdnCodDespesaFixa );
    $obFormulario->addHidden( $obHdnValorTotal );
    $obFormulario->addHidden( $obHdnTipoDespesaFixa );
    $obFormulario->addHidden( $obHdnDtUltimoEmpenho );
    $obFormulario->addTitulo( "Dados para Empenho de Despesa Mensais Fixas" );
    $obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
    $obFormulario->addComponente( $obCmbIdentificador );
    $obFormulario->addComponente( $obILabelEntidade );
    $obFormulario->addComponente( $obLblCredor );
    $obFormulario->addComponente( $obLblDotacao );
    $obFormulario->addComponente( $obCmbDesdobramento );
    $obFormulario->addSpan      ( $obSpanSaldo );
    $obFormulario->addComponenteComposto( $obTxtCodTipo, $obCmbNomTipo );
    $obFormulario->addComponente( $obDataEmpenho );
    $obFormulario->addComponente( $obLblDataVencimento );
    $obMontaAtributos->geraFormulario ( $obFormulario );
    $obFormulario->addTitulo( "Dados do Item" );
    $obFormulario->addComponente( $obLblContrato );
    $obFormulario->addComponente( $obLblLocal );
    $obFormulario->addComponente( $obLblHistorico );
    $obFormulario->addComponente( $obTxtComplemento );
    $obFormulario->addComponente( $obTxtConsumo );
    $obFormulario->addComponente( $obTxtValor );
    $obFormulario->addComponente( $obDtDocumento );
    $obFormulario->Incluir( 'ListaItens', array( $obTxtConsumo, $obTxtValor, $obDtDocumento ) );
    $obFormulario->addTitulo( 'Itens do Empenho' );
    $obFormulario->addSpan( $obSpan );
    $obFormulario->addComponente( $obLblTotal );
    $obFormulario->Ok();
    $obFormulario->Show();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

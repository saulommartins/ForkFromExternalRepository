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
    * Página de Formulario de Despesas Mensais Fixas
    * Data de Criação   : 28/08/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-02-15 15:27:07 -0200 (Qui, 15 Fev 2007) $

    * Casos de uso: uc-02.03.29
*/

/**
    $Log$
    Revision 1.6  2007/02/15 17:27:07  luciano
    #8385#

    Revision 1.5  2007/02/05 18:33:29  rodrigo_sr
    Bug #7914#

    Revision 1.4  2006/11/25 15:22:31  cleisson
    Ajustada Tag log

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php" );
include_once( CAM_GF_ORC_COMPONENTES."IPopUpDotacaoFiltro.class.php" );
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDespesaFixa.class.php" );
include_once( CAM_GF_EMP_COMPONENTES."IPopUpCredor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterDespesasMensaisFixas";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once($pgJs);

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

if ($stAcao == "incluir") {
    $obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();

    $obTEmpenhoTipoDespesaFixa = new TEmpenhoTipoDespesaFixa();
    $obTEmpenhoTipoDespesaFixa->recuperaTodos( $rsTipo );

    $obTxtTipo = new TextBox;
    $obTxtTipo->setName   ( "inCodTipo"         );
    $obTxtTipo->setId     ( "inCodTipo"         );
    $obTxtTipo->setValue  ( $inCodTipo          );
    $obTxtTipo->setRotulo ( "Tipo"              );
    $obTxtTipo->setTitle  ( "Selecione o Tipo de Despesa a Cadastrar." );
    $obTxtTipo->setInteiro( true                    );
    $obTxtTipo->setNull   ( false );

    $obCmbTipo = new Select;
    $obCmbTipo->setName      ( "stNomeTipo"    );
    $obCmbTipo->setId        ( "stNomeTipo"    );
    $obCmbTipo->setValue     ( $inCodTipo      );
    $obCmbTipo->addOption    ( "", "Selecione" );
    $obCmbTipo->setCampoId   ( "cod_tipo"      );
    $obCmbTipo->setCampoDesc ( "descricao"     );
    $obCmbTipo->preencheCombo( $rsTipo         );
    $obCmbTipo->setNull      ( false );

    $obTxtIdentificacao = new TextBox;
    $obTxtIdentificacao->setName   ( "inIdentificacao"         );
    $obTxtIdentificacao->setId     ( "inIdentificacao"         );
    $obTxtIdentificacao->setValue  ( $inIdentificacao          );
    $obTxtIdentificacao->setRotulo ( "Nr. Identificação"         );
    $obTxtIdentificacao->setTitle  ( "Informe o Número de Identificação ( telefone, hidrômetro, nr. prédio, etc...)." );
    $obTxtIdentificacao->setInteiro( true                    );
    $obTxtIdentificacao->setNull   ( false );

    $obTxtContrato = new TextBox;
    $obTxtContrato->setName   ( "inContrato"         );
    $obTxtContrato->setId     ( "inContrato"         );
    $obTxtContrato->setValue  ( $inContrato          );
    $obTxtContrato->setRotulo ( "Nr. Contrato"       );
    $obTxtContrato->setTitle  ( "Digite o Número do Contrato." );
    $obTxtContrato->setInteiro( true                    );
    $obTxtContrato->setNull   ( false );

    $obPopUpDotacao = new IPopUpDotacaoFiltro($obITextBoxSelectEntidadeUsuario->obSelect );
    $obPopUpDotacao->setNull ( false );

    $obPopUpCredor = new IPopUpCredor($obForm );

} else {
    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName     ( "inCodEntidade" );
    $obHdnCodEntidade->setValue    ( $inCodEntidade  );

    $obHdnCodCredor = new Hidden;
    $obHdnCodCredor->setName     ( "inCodCredor" );
    $obHdnCodCredor->setValue    ( $inCodCredor  );

    $obHdnExercicio = new Hidden;
    $obHdnExercicio->setName     ( "stExercicio" );
    $obHdnExercicio->setValue    ( $stExercicio  );

    $obHdnCodDespesaFixa = new Hidden;
    $obHdnCodDespesaFixa->setName     ( "inCodDespesaFixa" );
    $obHdnCodDespesaFixa->setValue    ( $inCodDespesaFixa  );

    $obHdnCodDotacao = new Hidden;
    $obHdnCodDotacao->setName     ( "inCodDotacao" );
    $obHdnCodDotacao->setValue    ( $inCodDotacao  );

    $obHdnCodTipo = new Hidden;
    $obHdnCodTipo->setName     ( "inCodTipo" );
    $obHdnCodTipo->setValue    ( $inCodTipo  );

    $obHdnIdentificacao = new Hidden;
    $obHdnIdentificacao->setName     ( "inIdentificacao" );
    $obHdnIdentificacao->setValue    ( $inIdentificacao  );

    $obHdnContrato = new Hidden;
    $obHdnContrato->setName     ( "inContrato" );
    $obHdnContrato->setValue    ( $inContrato  );

    $obLblEntidade = new Label();
    $obLblEntidade->setId    ( 'inCodEntidade' );
    $obLblEntidade->setRotulo( 'Entidade'      );
    $obLblEntidade->setValue ( $inCodEntidade."-".$stEntidade  );

    $obLblExercicio = new Label();
    $obLblExercicio->setId    ( 'stExercicio'   );
    $obLblExercicio->setRotulo( 'Exercício'     );
    $obLblExercicio->setValue ( $stExercicio    );

    $obLblDataInclusao = new Label();
    $obLblDataInclusao->setId    ( 'dtDataInclusao'   );
    $obLblDataInclusao->setRotulo( 'Data de Inclusão' );
    $obLblDataInclusao->setValue ( $stDataInclusao    );

    $obLblTipo = new Label();
    $obLblTipo->setId    ( 'stTipo' );
    $obLblTipo->setRotulo( 'Tipo'   );
    $obLblTipo->setValue ( $inCodTipo."-".$stDescricaoTipo  );

    $obLblIdentificacao = new Label();
    $obLblIdentificacao->setId    ( 'inIdentificacao'      );
    $obLblIdentificacao->setRotulo( 'Nr. de Identificação' );
    $obLblIdentificacao->setValue ( $inIdentificacao       );

    $obLblContrato = new Label();
    $obLblContrato->setId    ( 'inContrato'   );
    $obLblContrato->setRotulo( 'Nr. Contrato' );
    $obLblContrato->setValue ( $inContrato    );

    $obLblDotacao = new Label();
    $obLblDotacao->setId    ( 'inCodDotacao' );
    $obLblDotacao->setRotulo( 'Dotação'      );
    $obLblDotacao->setValue ( $inCodDotacao  );

    $obLblCredor = new Label();
    $obLblCredor->setId    ( 'inCodCredor' );
    $obLblCredor->setRotulo( 'Credor'      );
    $obLblCredor->setValue ( $inCodCredor."-".$stCredor  );

    sistemaLegado::executaFrameOculto("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLocal='+".$inCodLocal.",'buscarLocal');");
}

$obBscLocal = new BuscaInner;
$obBscLocal->setRotulo                      ( "Local"                               );
$obBscLocal->setTitle                       ( "Informe o Local (prédio, escola, secretaria, etc...)."       );
$obBscLocal->setNull                        ( false                                  );
$obBscLocal->setId                          ( "stLocal"                             );
$obBscLocal->obCampoCod->setName            ( "inCodLocal"                          );
$obBscLocal->obCampoCod->setValue           ( $inCodLocal                           );
$obBscLocal->obCampoCod->setSize            ( 10                                    );
//$obBscLocal->obCampoCod->obEvento->setOnBlur("buscaValor('buscarLocal',".$pgOcul.");");
$obBscLocal->obCampoCod->obEvento->setOnChange( "ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&inCodLocal='+this.value,'buscarLocal');");
$obBscLocal->setFuncaoBusca                 ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/LSProcurarLocal.php','frm','inCodLocal','stLocal','','".Sessao::getId()."','800','550')" );

$obTxtDiaVencimento = new TextBox;
$obTxtDiaVencimento->setName   ( "inDiaVencimento"         );
$obTxtDiaVencimento->setId     ( "inDiaVencimento"         );
$obTxtDiaVencimento->setValue  ( trim($inDiaVencimento)    );
$obTxtDiaVencimento->setRotulo ( "Dia de Vencimento"       );
$obTxtDiaVencimento->setTitle  ( "Informe a Data do Vencimento da Despesa." );
$obTxtDiaVencimento->setInteiro( true                      );
$obTxtDiaVencimento->setNull   ( false );
$obTxtDiaVencimento->setSize   ( 2 );

$obDtVigencia = new Periodo();
$obDtVigencia->setRotulo          ( 'Vigência'      );
$obDtVigencia->setExercicio       (  Sessao::getExercicio() );
$obDtVigencia->setValidaExercicio ( true            );
$obDtVigencia->setNull            ( false           );
$obDtVigencia->setValue           ( 4               );
$obDtVigencia->obDataInicial->setValue( date("d/m/Y") );
$obDtVigencia->obDataFinal->setValue( $stDataFinal  );

$obTxtHistorico = new TextArea;
$obTxtHistorico->setName   ( "stHistorico" );
$obTxtHistorico->setId     ( "stHistorico" );
$obTxtHistorico->setValue  ( $stHistorico  );
$obTxtHistorico->setRotulo ( "Histórico"   );
$obTxtHistorico->setTitle  ( "Digite o histórico de empenhamento para a despesa." );
$obTxtHistorico->setNull   ( false          );
$obTxtHistorico->setRows   ( 2             );
$obTxtHistorico->setCols   ( 240           );

if ($inCodStatus == 'f') {
    $inCodStatus = 2;
} else {
    $inCodStatus = 1;
}

$obCmbStatus = new Select;
$obCmbStatus->setName      ( "inCodStatus"   );
$obCmbStatus->setId        ( "inCodStatus"   );
$obCmbStatus->setRotulo    ( "Status"        );
$obCmbStatus->addOption    ( "", "Selecione" );
$obCmbStatus->addOption    ( "1", "Ativo"    );
$obCmbStatus->addOption    ( "2", "Inativo"  );
$obCmbStatus->setCampoDesc ( "nom_unidade"   );
$obCmbStatus->setNull      ( false           );
$obCmbStatus->setValue     ( $inCodStatus    );

$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo ( "Dados para Despesas Mensais Fixas" );
if ($stAcao == "incluir") {
    $obFormulario->addComponente( $obITextBoxSelectEntidadeUsuario );
    $obFormulario->addComponenteComposto( $obTxtTipo, $obCmbTipo );
    $obFormulario->addComponente( $obTxtIdentificacao );
    $obFormulario->addComponente( $obTxtContrato );
} elseif ($stAcao == "alterar") {
    $obFormulario->addHidden( $obHdnExercicio );
    $obFormulario->addHidden( $obHdnCodDespesaFixa );
    $obFormulario->addHidden( $obHdnCodTipo );
    $obFormulario->addHidden( $obHdnIdentificacao );
    $obFormulario->addHidden( $obHdnContrato );
    $obFormulario->addHidden( $obHdnCodEntidade );
    $obFormulario->addHidden( $obHdnCodCredor );
    $obFormulario->addHidden( $obHdnCodDotacao );
    $obFormulario->addComponente( $obLblEntidade );
    $obFormulario->addComponente( $obLblDataInclusao );
    $obFormulario->addComponente( $obLblTipo );
    $obFormulario->addComponente( $obLblIdentificacao );
    $obFormulario->addComponente( $obLblContrato );
    $obFormulario->addComponente( $obLblDotacao );
    $obFormulario->addComponente( $obLblCredor );
}
$obFormulario->addComponente( $obTxtDiaVencimento );
if ($stAcao == "incluir") {
    $obFormulario->addComponente( $obPopUpDotacao );
    $obFormulario->addComponente( $obPopUpCredor  );
}
$obFormulario->addComponente( $obBscLocal );
$obFormulario->addComponente( $obDtVigencia );
$obFormulario->addComponente( $obTxtHistorico );
$obFormulario->addComponente( $obCmbStatus );
$obFormulario->Ok();
$obFormulario->Show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

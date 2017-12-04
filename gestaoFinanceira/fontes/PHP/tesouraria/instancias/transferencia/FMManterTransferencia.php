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
    * Página de Formulário para Transferência
    * Data de Criação   : 04/11/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 31627 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 13:27:48 -0300 (Seg, 23 Out 2006) $

    * Casos de uso: uc-02.04.09

*/

/*
$Log$
Revision 1.26  2006/10/23 16:27:48  domluc
Add opção para multiplos boletins

Revision 1.25  2006/08/08 20:01:46  jose.eduardo
Bug #6713#

Revision 1.24  2006/07/05 20:40:06  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_IAPPLETTERMINAL );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTransferencia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$rsEntidades = $rsLista = new RecordSet;

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
$obRTesourariaBoletim->addTransferencia();
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obRTesourariaBoletim->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$arFiltro = Sessao::read('filtro');

if ( !count( $arFiltro ) > 0 ) {
    $arFiltro = $_REQUEST;
} else {
    $_REQUEST = $arFiltro;
}

if ($stAcao=="excluir") {
    if ( count($arFiltro) > 0 ) {
        $stFiltro = '';
        foreach ($arFiltro as $stCampo => $stValor) {
            if (is_array($stValor)) {
                foreach ($stValor as $stCampo2 => $stValor2) {
                    $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
                }
            } else {
                $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
            }
        }
    }

    $obRTesourariaBoletimLista = new RTesourariaBoletim();
    $obRTesourariaBoletimLista->addTransferencia();
    $obRTesourariaBoletimLista->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_REQUEST['inCodLote'] );
    $obRTesourariaBoletimLista->roUltimaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
    $obRTesourariaBoletimLista->setExercicio( $_REQUEST['stExercicio'] );
    $obRTesourariaBoletimLista->roUltimaTransferencia->listarTransferenciaAtiva( $rsLista );
}

$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->setTarget ( "oculto" );

$obIApplet = new IAppletTerminal( $obForm );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$stHdnValor = "
    if (document.frm.inCodPlanoDebito.value == document.frm.inCodPlanoCredito.value) {
         erro = true;
         mensagem += '@A Conta Crédito deve ser diferente da Conta Débito! (' + document.frm.inCodPlanoCredito.value + ')';
    }
    var stValor;
    stValor = document.frm.nuValor.value;
    while (stValor.indexOf('.')>0) {
        stValor = stValor.replace('.','');
    }
    stValor = stValor.replace(',','.');
    if (!(stValor > 0)) {
         erro = true;
         mensagem += '@Campo Valor deve ser maior que 0,00!';
    }
    var stVlTransferencia;
    var stVlTransf;
    stVlTransferencia = document.frm.nuValor.value;
    while (stVlTransferencia.indexOf('.')>0) {
        stVlTransferencia = stVlTransferencia.replace('.','');
    }
    stVlTransf = stVlTransferencia.replace(',','.');
    if ( parseFloat(stVlTransf) > parseFloat(document.frm.nuSaldoContaBanco.value) ) { ;
      if ( confirm( 'O saldo da conta informada não é suficiente para realizar a transferência.\\n (Saldo da conta: R$ '+document.frm.nuSaldoContaBancoBR.value+')\\n Se efetuar esta transferência, o saldo da conta vai ficar negativo. Deseja continuar?')) {
        erro = false
      } else erro = true;
    }
";

$obHdnValor = new Hidden;
$obHdnValor->setName( "stHdnValor" );
$obHdnValor->setValue( $stHdnValor );

$obHdnEval = new HiddenEval;
$obHdnEval->setName( "stEval" );
$obHdnEval->setValue( $stHdnValor );

$obHdnCodBoletim = new Hidden;
$obHdnCodBoletim->setName( "inCodBoletim" );
$obHdnCodBoletim->setValue( $inCodBoletim );

$obHdnDtBoletim = new Hidden;
$obHdnDtBoletim->setName ( "stDtBoletim" );
$obHdnDtBoletim->setValue( $stDtBoletim );

$obHdnVlSaldoContaBanco = new Hidden;
$obHdnVlSaldoContaBanco->setName ( "nuSaldoContaBanco" );
$obHdnVlSaldoContaBanco->setValue( $nuSaldoContaBanco  );

$obHdnVlSaldoContaBancoBR = new Hidden;
$obHdnVlSaldoContaBancoBR->setName ( "nuSaldoContaBancoBR" );
$obHdnVlSaldoContaBancoBR->setValue( $nuSaldoContaBancoBR  );

//// Define Objeto Label para número do boletim
//$obLblNumBoletim = new Label;
//$obLblNumBoletim->setRotulo  ( "Número do Boletim" );
//$obLblNumBoletim->setId      ( "inCodBoletimLbl"   );
//$obLblNumBoletim->setValue   ( $inCodBoletim       );
//
//// Define Objeto Label para data do boletim
//$obLblDtBoletim = new Label;
//$obLblDtBoletim->setRotulo  ( "Data do Boletim" );
//$obLblDtBoletim->setId      ( "stDtBoletimLbl"  );
//$obLblDtBoletim->setValue   ( $stDtBoletim      );

$obSpnBoletim = new Span();
$obSpnBoletim->setId( 'spnBoletim' );

if ($stAcao=="incluir") {
    // Define Objeto TextBox para Codigo da Entidade
    $obTxtCodEntidade = new TextBox;
    $obTxtCodEntidade->setName              ( "inCodEntidade"       );
    $obTxtCodEntidade->setId                ( "inCodEntidade"       );
    if ($rsEntidade->getNumLinhas()==1) {
         $obTxtCodEntidade->setValue          ( $rsEntidade->getCampo('cod_entidade')  );
    } else $obTxtCodEntidade->setValue          ( $inCodEntidade            );
    $obTxtCodEntidade->setRotulo            ( "Entidade"            );
    $obTxtCodEntidade->setTitle             ( "Selecione a Entidade das contas para transferência.");
    $obTxtCodEntidade->obEvento->setOnChange( "buscaDado('mostraSpanContas');"  );
    $obTxtCodEntidade->setInteiro           ( true                  );
    $obTxtCodEntidade->setNull              ( false                 );

    // Define Objeto Select para Nome da Entidade
    $obCmbNomEntidade = new Select;
    $obCmbNomEntidade->setName              ( "stNomEntidade"       );
    $obCmbNomEntidade->setRotulo            ( "Entidade"            );
    $obCmbNomEntidade->setId                ( "stNomEntidade"       );
    $obCmbNomEntidade->setValue             ( $inCodEntidade        );
    if ($rsEntidade->getNumLinhas() > 1) {
        $obCmbNomEntidade->addOption    ( ""            ,"Selecione" );
        $obCmbNomEntidade->obEvento->setOnChange( "buscaDado('mostraSpanContas');" );
    } else $jsSL = "buscaDado('mostraSpanContas');";
    $obCmbNomEntidade->setCampoId           ( "cod_entidade"        );
    $obCmbNomEntidade->setCampoDesc         ( "nom_cgm"             );
    $obCmbNomEntidade->setStyle             ( "width: 520"          );
    $obCmbNomEntidade->preencheCombo        ( $rsEntidade           );
    $obCmbNomEntidade->setNull              ( false                 );

    // Define Objeto Span para Itens da ordem ou liquidacao
    $obSpnContas = new Span();
    $obSpnContas->setId( 'spnContas' );

    // Define Objeto Numeric para valor recebido
    $obTxtValor = new Numerico;
    $obTxtValor->setName     ( "nuValor"   );
    $obTxtValor->setId       ( "nuValor"   );
    $obTxtValor->setValue    ( $nuValor    );
    $obTxtValor->setRotulo   ( "Valor"     );
    $obTxtValor->setTitle    ( "Informe o Valor a ser transferido." );
    $obTxtValor->setDecimais ( 2                );
    $obTxtValor->setNegativo ( false            );
    $obTxtValor->setNull     ( false             );
    $obTxtValor->setSize     ( 23               );
    $obTxtValor->setMaxLength( 23               );
    $obTxtValor->setMinValue ( 1                );
} else {
    $obHdnCodLoteTransferencia = new Hidden;
    $obHdnCodLoteTransferencia->setName ( "inCodLoteTransferencia" );
    $obHdnCodLoteTransferencia->setValue( $rsLista->getCampo("cod_lote")  );

    $obHdnCodEntidade = new Hidden;
    $obHdnCodEntidade->setName ( "inCodEntidade" );
    $obHdnCodEntidade->setValue( $rsLista->getCampo("cod_entidade")  );

    $obLblCodEntidade = new Label;
    $obLblCodEntidade->setRotulo  ( "Entidade"      );
    $obLblCodEntidade->setId      ( "stCodEntidade" );
    $obLblCodEntidade->setValue   ( $rsLista->getCampo("cod_entidade") . " - " . $rsLista->getCampo("nom_entidade")    );

    $obHdnCodCredito = new Hidden;
    $obHdnCodCredito->setName ( "inCodPlanoCredito" );
    $obHdnCodCredito->setValue( $rsLista->getCampo("cod_plano_debito")  );

    $obLblCodCredito = new Label;
    $obLblCodCredito->setRotulo  ( "Conta a Crédito"      );
    $obLblCodCredito->setId      ( "stCodPlanoCredito" );
    $obLblCodCredito->setValue   ( $rsLista->getCampo("cod_plano_debito") . " - " . $rsLista->getCampo("nom_conta_debito")    );

    $obHdnCodDebito = new Hidden;
    $obHdnCodDebito->setName ( "inCodPlanoDebito" );
    $obHdnCodDebito->setValue( $rsLista->getCampo("cod_plano_credito")  );

    $obLblCodDebito = new Label;
    $obLblCodDebito->setRotulo  ( "Conta a Débito"      );
    $obLblCodDebito->setId      ( "stCodPlanoDebito" );
    $obLblCodDebito->setValue   ( $rsLista->getCampo("cod_plano_credito") . " - " . $rsLista->getCampo("nom_conta_credito")    );

    $obHdnValor = new Hidden;
    $obHdnValor->setName ( "nuValor" );
    $obHdnValor->setValue( $rsLista->getCampo("valor")  );

    $obLblValor = new Label;
    $obLblValor->setRotulo  ( "Valor"      );
    $obLblValor->setId      ( "stValor" );
    $obLblValor->setValue   ( $rsLista->getCampo("valor") );
}

// Define objeto BuscaInner para cgm
$obBscHistorico = new BuscaInner();
$obBscHistorico->setRotulo                 ( "Histórico Padrão"                                                           );
$obBscHistorico->setTitle                  ( "Informe o código do Histórico Padrão."                                       );
$obBscHistorico->setId                     ( "stNomHistorico"                                                                   );
$obBscHistorico->setValue                  ( $stNomHistorico                                                                    );
$obBscHistorico->setNull                   ( false                                                                         );
$obBscHistorico->obCampoCod->setName       ( "inCodHistorico"                                                                   );
$obBscHistorico->obCampoCod->setSize       ( 10                                                                           );
$obBscHistorico->obCampoCod->setMaxLength  ( 5                                                                            );
$obBscHistorico->obCampoCod->setValue      ( $inCodHistorico                                                                    );
$obBscHistorico->obCampoCod->setAlign      ( "left"                                                                       );
$obBscHistorico->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."historicoPadrao/FLHistoricoPadrao.php','frm','inCodHistorico','stNomHistorico','','".Sessao::getId()."','800','550');");
$obBscHistorico->setValoresBusca           ( CAM_GF_CONT_POPUPS.'historicoPadrao/OCHistoricoPadrao.php?'.Sessao::getId(), $obForm->getName() );

// Define Objeto TextArea para observações
$obTxtObs = new TextArea;
$obTxtObs->setName   ( "stObservacoes" );
$obTxtObs->setId     ( "stObservacoes" );
$obTxtObs->setValue  ( $stObservacoes  );
$obTxtObs->setRotulo ( "Observações"   );
$obTxtObs->setTitle  ( "Informe as observações da transferência." );
$obTxtObs->setNull   ( true            );
$obTxtObs->setRows   ( 2               );
$obTxtObs->setCols   ( 100             );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCtrl           );
if ($stAcao=="incluir") {
    $obFormulario->addHidden    ( $obHdnValor       );
    $obFormulario->addHidden    ( $obHdnEval , true );
} else {
    $obFormulario->addHidden    ( $obHdnCodLoteTransferencia );
    $obFormulario->addHidden    ( $obHdnCodEntidade );
    $obFormulario->addHidden    ( $obHdnCodCredito  );
    $obFormulario->addHidden    ( $obHdnCodDebito   );
    $obFormulario->addHidden    ( $obHdnValor       );
}
$obFormulario->addHidden     ( $obHdnCodBoletim     );
$obFormulario->addHidden     ( $obHdnDtBoletim      );
$obFormulario->addHidden     ( $obHdnVlSaldoContaBanco      );
$obFormulario->addHidden     ( $obHdnVlSaldoContaBancoBR    );
$obFormulario->addHidden( $obIApplet                                );
$obFormulario->addTitulo( "Dados do Boletim" );
//$obFormulario->addComponente ( $obLblNumBoletim     );
//$obFormulario->addComponente ( $obLblDtBoletim      );
$obFormulario->addSpan       ( $obSpnBoletim        );
$obFormulario->addTitulo( "Dados da Transferência" );
if ($stAcao=="incluir") {
    $obFormulario->addComponenteComposto( $obTxtCodEntidade, $obCmbNomEntidade );
    $obFormulario->addComponente ( $obBscHistorico      );
    $obFormulario->addSpan       ( $obSpnContas         );
} else {
    $obFormulario->addComponente ( $obLblCodEntidade    );
    $obFormulario->addComponente ( $obLblCodCredito     );
    $obFormulario->addComponente ( $obLblCodDebito      );
    $obFormulario->addComponente ( $obBscHistorico      );
}
if($stAcao=="incluir")
    $obFormulario->addComponente ( $obTxtValor         );
else
    $obFormulario->addComponente ( $obLblValor         );
$obFormulario->addComponente ( $obTxtObs    );

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

$obOk   = new Ok;

if ($stAcao=="incluir") {
    $obLimpar = new Limpar;
    $obFormulario->defineBarra( array($obOk,  $obLimpar ) );
} else {
    $obVoltar = new Button;
    $obVoltar->setName  ( "Voltar" );
    $obVoltar->setValue ( "Voltar" );
    $obVoltar->obEvento->setOnClick("Cancelar('".$stLocation."');");
    $obFormulario->defineBarra( array($obOk,  $obVoltar ) );
}

$obFormulario->show();
if ($jsSL) SistemaLegado::executaFrameOculto($jsSL);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

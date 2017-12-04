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
    * Página de Formulário para Arrecadação Receita
    * Data de Criação   : 06/01/2006

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    $Revision: 31183 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 13:33:46 -0300 (Seg, 23 Out 2006) $

    * Casos de uso: uc-02.04.20

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CLA_IAPPLETTERMINAL                                                                   );
include_once( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"                                     );
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterBorderoPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);

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
    include_once( $pgJs );

    $obRTesourariaBoletim = new RTesourariaBoletim();
    $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->setDataBoletim( date( 'd/m/'.Sessao::getExercicio() ) );
    $obRTesourariaBoletim->addArrecadacao();
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->setExercicio( Sessao::getExercicio() );
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
    $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoEntidade->listarUsuariosEntidade( $rsEntidade );

    //Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
    $stAcao = $request->get('stAcao');

    $obForm = new Form;
    $obForm->setAction ( $pgProc    );
    $obForm->setTarget ( "oculto"   );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName( "stAcao"   );
    $obHdnAcao->setValue( $stAcao   );

    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName( "stCtrl"   );
    $obHdnCtrl->setValue( ""        );

    $obHdnCodBoletim = new Hidden();
    $obHdnCodBoletim->setName( 'inCodBoletim' );
    $obHdnCodBoletim->setValue( $inCodBoletim );

    $obHdnDtBoletim = new Hidden();
    $obHdnDtBoletim->setName( 'stDtBoletim' );
    $obHdnDtBoletim->setValue( $stDtBoletim );

    $obHdnNumBordero = new Hidden;
    $obHdnNumBordero->setName( "inNumBordero"   );
    $obHdnNumBordero->setValue( ""        );

    $obHdnAction = new Hidden;
    $obHdnAction->setName("stAction");
    $obHdnAction->setValue(CAM_FW_POPUPS."relatorio/OCRelatorio.php");

    $obHdnCaminho = new Hidden;
    $obHdnCaminho->setName("stCaminho");
    $obHdnCaminho->setValue( CAM_GF_TES_INSTANCIAS."pagamentos/OCRelatorioBorderoPagamento.php" );

    $stEval = "
        if (trim(document.getElementById('spnLista').innerHTML) == '') {
            erro = true;
            mensagem += '@Adicione uma Ordem de Pagamento!()';
        }

        if (parseFloat(document.frm.nuVlTotalBordero.value) > parseFloat(document.frm.nuSaldoContaBanco.value)) {
            if (confirm('O saldo da conta informada não é suficiente para pagar este empenho.\\n (Saldo da conta: R$ '+f.nuSaldoContaBancoBR.value+')\\n Se efetuar este pagamento, o saldo da conta vai ficar negativo. Deseja continuar?')) { erro = false } else erro = true;
        }
        if (erro == false) {
            document.frm.inCodEntidade.disabled = false;
        }
    ";

    $obHdnEval = new HiddenEval;
    $obHdnEval->setName  ( "stEval"            );
    $obHdnEval->setValue ( $stEval             );

    $obHdnVlTotalBordero = new Hidden;
    $obHdnVlTotalBordero->setName( "nuVlTotalBordero" );
    $obHdnVlTotalBordero->setValue( ""                );

    $obHdnVlSaldoConta = new Hidden;
    $obHdnVlSaldoConta->setName( "nuSaldoContaBanco" );
    $obHdnVlSaldoConta->setValue( ""                 );

    $obHdnVlSaldoContaBR = new Hidden;
    $obHdnVlSaldoContaBR->setName( "nuSaldoContaBancoBR" );
    $obHdnVlSaldoContaBR->setValue( ""                   );

    $obHdnSimulacao = new Hidden;
    $obHdnSimulacao->setName( "inSimulacao" );
    $obHdnSimulacao->setValue( ""           );

    $obHdnCodigoEntidade = new Hidden;
    $obHdnCodigoEntidade->setName( "inCodigoEntidade" );
    $obHdnCodigoEntidade->setValue( ""           );

    $obApplet = new IAppletTerminal( $obForm );

    // Define Objeto Select para Entidade
    $obCmbEntidade = new Select();
    $obCmbEntidade->setRotulo    ( "*Entidade"                );
    $obCmbEntidade->setName      ( "inCodEntidade"            );
    $obCmbEntidade->setTitle     ( "Selecione a Entidade"     );
    $obCmbEntidade->setCampoId   ( "cod_entidade"             );
    $obCmbEntidade->setCampoDesc ( "nom_cgm"                  );
    $obCmbEntidade->setValue     ( $inCodEntidade             );
    $obCmbEntidade->setNull      ( true                       );
    if ($rsEntidade->getNumLinhas() > 1) {
        $obCmbEntidade->addOption    ( ""            ,"Selecione" );
        $obCmbEntidade->obEvento->setOnChange( "mostraSpanBoletim();" );
    } else {
        $jsSL = "mostraSpanBoletim();";
    }
    $obCmbEntidade->preencheCombo( $rsEntidade                );

    $obSpanBoletim = new Span;
    $obSpanBoletim->setId( "spnBoletim" );

    //Define Objeto Text para o Exercicio
    $obTxtExercicio = new TextBox;
    $obTxtExercicio->setName      ( "stExercicio"         );
    $obTxtExercicio->setValue     ( Sessao::getExercicio()    );
    $obTxtExercicio->setRotulo    ( "*Exercício"          );
    $obTxtExercicio->setTitle     ( "Informe o Exercício" );
    $obTxtExercicio->setNull      ( true                  );
    $obTxtExercicio->setMaxLength ( 4                     );
    $obTxtExercicio->setSize      ( 5                     );

    $obSpanContaBanco = new Span;
    $obSpanContaBanco->setId( "spnContaBanco" );

    $obCmbTipoTransacao = new Select;
    $obCmbTipoTransacao->setRotulo ( "*Tipo"                             );
    $obCmbTipoTransacao->setName   ( "stTipoTransacao"                   );
    $obCmbTipoTransacao->addOption ( "1","Não Informado"                 );
    $obCmbTipoTransacao->addOption ( "2","Transferência - C/C"           );
    $obCmbTipoTransacao->addOption ( "3" ,"Transferência - Poupança"     );
    $obCmbTipoTransacao->addOption ( "4","DOC"                           );
    $obCmbTipoTransacao->addOption ( "5","TED"                           );
    $obCmbTipoTransacao->setValue  ( ""                                  );
    $obCmbTipoTransacao->setStyle  ( "width: 200px"                      );
    $obCmbTipoTransacao->setNull   ( true                                );
    $obCmbTipoTransacao->setTitle  ( "Informe o Tipo de Transação a efetuar" );

    // Define Objeto Span para BuscaInner da Ordem de Pagamento
    $obSpanBscOrdemPagamento = new Span;
    $obSpanBscOrdemPagamento->setId( "spnBscOrdemPagamento" );

    // Define Objeto Span Para Dados da Ordem
    $obSpanOrdem = new Span;
    $obSpanOrdem->setId( "spnOrdem" );

    $obSpan = new Span;
    $obSpan->setId( "spnLista" );

    $obBtnIncluir = new Button();
    $obBtnIncluir->setValue( 'Incluir' );
    $obBtnIncluir->obEvento->setOnClick( "inclui();" );

    $obBtnLimpar = new Button();
    $obBtnLimpar->setValue( 'Limpar' );
    $obBtnLimpar->obEvento->setOnClick( "limpa();" );

    $obBscCGM = new BuscaInner();
    $obBscCGM->setRotulo               ( "CGM"                                                  );
    $obBscCGM->setTitle                ( "Informe o CGM do assinante do Borderô"                );
    $obBscCGM->setId                   ( "stNomAssinante_1"                                     );
    $obBscCGM->setValue                ( $stNomAssinante_1                                      );
    $obBscCGM->setNull                 ( true                                                   );
    $obBscCGM->obCampoCod->setName     ( "inNumAssinante_1"                                     );
    $obBscCGM->obCampoCod->setSize     ( 10                                                     );
    $obBscCGM->obCampoCod->setMaxLength( 8                                                      );
    $obBscCGM->obCampoCod->setValue    ( $inNumAssinante_1                                      );
    $obBscCGM->obCampoCod->setAlign    ( "left"                                                 );
    $obBscCGM->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_1','stNomAssinante_1','geral','".Sessao::getId()."','800','550');");
    $obBscCGM->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

    $obTxtMatricula = new TextBox;
    $obTxtMatricula->setRotulo        ( "Nr. Matrícula"                               );
    $obTxtMatricula->setTitle         ( "Informe a Matrícula do assinante do Borderô" );
    $obTxtMatricula->setName          ( "inNumMatricula_1"                            );
    $obTxtMatricula->setValue         ( $inNumMatricula_1                             );
    $obTxtMatricula->setSize          ( 10                                            );
    $obTxtMatricula->setMaxLength     ( 6                                             );
    $obTxtMatricula->setNull          ( true                                          );
    $obTxtMatricula->setInteiro       ( true                                          );

    $obTxtCargo = new TextBox;
    $obTxtCargo->setRotulo        ( "Cargo"                                   );
    $obTxtCargo->setTitle         ( "Informe o Cargo do assinante do Borderô" );
    $obTxtCargo->setName          ( "stCargo_1"                               );
    $obTxtCargo->setValue         ( $stCargo_1                                );
    $obTxtCargo->setSize          ( 30                                        );
    $obTxtCargo->setMaxLength     ( 25                                        );
    $obTxtCargo->setNull          ( true                                      );

    $obBscCGM2 = new BuscaInner();
    $obBscCGM2->setRotulo               ( "CGM"                                                  );
    $obBscCGM2->setTitle                ( "Informe o CGM do assinante do Borderô"                );
    $obBscCGM2->setId                   ( "stNomAssinante_2"                                     );
    $obBscCGM2->setValue                ( $stNomAssinante_2                                      );
    $obBscCGM2->setNull                 ( true                                                   );
    $obBscCGM2->obCampoCod->setName     ( "inNumAssinante_2"                                     );
    $obBscCGM2->obCampoCod->setSize     ( 10                                                     );
    $obBscCGM2->obCampoCod->setMaxLength( 8                                                      );
    $obBscCGM2->obCampoCod->setValue    ( $inNumAssinante_2                                      );
    $obBscCGM2->obCampoCod->setAlign    ( "left"                                                 );
    $obBscCGM2->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_2','stNomAssinante_2','geral','".Sessao::getId()."','800','550');");
    $obBscCGM2->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

    $obTxtMatricula2 = new TextBox;
    $obTxtMatricula2->setRotulo        ( "Nr. Matrícula"                               );
    $obTxtMatricula2->setTitle         ( "Informe a Matrícula do assinante do Borderô" );
    $obTxtMatricula2->setName          ( "inNumMatricula_2"                            );
    $obTxtMatricula2->setValue         ( $inNumMatricula_2                             );
    $obTxtMatricula2->setSize          ( 10                                            );
    $obTxtMatricula2->setMaxLength     ( 6                                             );
    $obTxtMatricula2->setNull          ( true                                          );
    $obTxtMatricula2->setInteiro       ( true                                          );

    $obTxtCargo2 = new TextBox;
    $obTxtCargo2->setRotulo        ( "Cargo"                                   );
    $obTxtCargo2->setTitle         ( "Informe o Cargo do assinante do Borderô" );
    $obTxtCargo2->setName          ( "stCargo_2"                               );
    $obTxtCargo2->setValue         ( $stCargo_2                                );
    $obTxtCargo2->setSize          ( 30                                        );
    $obTxtCargo2->setMaxLength     ( 25                                        );
    $obTxtCargo2->setNull          ( true                                      );

    $obBscCGM3 = new BuscaInner();
    $obBscCGM3->setRotulo               ( "CGM"                                                  );
    $obBscCGM3->setTitle                ( "Informe o CGM do assinante do Borderô"                );
    $obBscCGM3->setId                   ( "stNomAssinante_3"                                     );
    $obBscCGM3->setValue                ( $stNomAssinante_3                                      );
    $obBscCGM3->setNull                 ( true                                                   );
    $obBscCGM3->obCampoCod->setName     ( "inNumAssinante_3"                                     );
    $obBscCGM3->obCampoCod->setSize     ( 10                                                     );
    $obBscCGM3->obCampoCod->setMaxLength( 8                                                      );
    $obBscCGM3->obCampoCod->setValue    ( $inNumAssinante_3                                      );
    $obBscCGM3->obCampoCod->setAlign    ( "left"                                                 );
    $obBscCGM3->setFuncaoBusca          ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumAssinante_3','stNomAssinante_3','geral','".Sessao::getId()."','800','550');");
    $obBscCGM3->setValoresBusca         ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

    $obTxtMatricula3 = new TextBox;
    $obTxtMatricula3->setRotulo        ( "Nr. Matrícula"                               );
    $obTxtMatricula3->setTitle         ( "Informe a Matrícula do assinante do Borderô" );
    $obTxtMatricula3->setName          ( "inNumMatricula_3"                            );
    $obTxtMatricula3->setValue         ( $inNumMatricula_3                             );
    $obTxtMatricula3->setSize          ( 10                                            );
    $obTxtMatricula3->setMaxLength     ( 6                                             );
    $obTxtMatricula3->setNull          ( true                                          );
    $obTxtMatricula3->setInteiro       ( true                                          );

    $obTxtCargo3 = new TextBox;
    $obTxtCargo3->setRotulo        ( "Cargo"                                   );
    $obTxtCargo3->setTitle         ( "Informe o Cargo do assinante do Borderô" );
    $obTxtCargo3->setName          ( "stCargo_3"                               );
    $obTxtCargo3->setValue         ( $stCargo_3                                );
    $obTxtCargo3->setSize          ( 30                                        );
    $obTxtCargo3->setMaxLength     ( 25                                        );
    $obTxtCargo3->setNull          ( true                                      );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addTitulo     ( "Dados para Borderô" );
    $obFormulario->addForm       ( $obForm                  );
    $obFormulario->addHidden     ( $obHdnAcao               );
    $obFormulario->addHidden     ( $obHdnCtrl               );
    $obFormulario->addHidden     ( $obHdnCodBoletim         );
    $obFormulario->addHidden     ( $obHdnDtBoletim          );
    $obFormulario->addHidden     ( $obApplet                );
    $obFormulario->addHidden     ( $obHdnEval, true         );
    $obFormulario->addHidden     ( $obHdnAction             );
    $obFormulario->addHidden     ( $obHdnNumBordero         );
    $obFormulario->addHidden     ( $obHdnCaminho            );
    $obFormulario->addHidden     ( $obHdnVlTotalBordero     );
    $obFormulario->addHidden     ( $obHdnVlSaldoConta       );
    $obFormulario->addHidden     ( $obHdnVlSaldoContaBR     );
    $obFormulario->addHidden     ( $obHdnSimulacao          );
    $obFormulario->addHidden     ( $obHdnCodigoEntidade     );
    $obFormulario->addComponente ( $obCmbEntidade           );
    $obFormulario->addSpan       ( $obSpanBoletim           );
    $obFormulario->addComponente ( $obTxtExercicio          );
    $obFormulario->addSpan       ( $obSpanContaBanco        );
    $obFormulario->addTitulo     ( "Dados para Transações de  Borderô" );
    $obFormulario->addComponente ( $obCmbTipoTransacao      );
    $obFormulario->addSpan       ( $obSpanBscOrdemPagamento );
    $obFormulario->addSpan       ( $obSpanOrdem             );
    $obFormulario->agrupaComponentes(array($obBtnIncluir, $obBtnLimpar));
    $obFormulario->addSpan( $obSpan );

    $obFormulario->addComponente ( $obBscCGM           );
    $obFormulario->addComponente ( $obTxtMatricula     );
    $obFormulario->addComponente ( $obTxtCargo         );

    $obFormulario->addComponente ( $obBscCGM2          );
    $obFormulario->addComponente ( $obTxtMatricula2    );
    $obFormulario->addComponente ( $obTxtCargo2        );

    $obFormulario->addComponente ( $obBscCGM3          );
    $obFormulario->addComponente ( $obTxtMatricula3    );
    $obFormulario->addComponente ( $obTxtCargo3        );

    $obBtnPreEmissao = new Button;
    $obBtnPreEmissao->setValue("Pré-Emissão do Borderô para Conferência");
    $obBtnPreEmissao->obEvento->setOnClick( "mostraPreEmissao();" );
    $obFormulario->defineBarra(array($obBtnPreEmissao));

    $obBtnOk = new Ok;
    $obBtnCancelar = new Button;
    $obBtnCancelar->setValue( "Cancelar" );
    $obBtnCancelar->obEvento->setOnClick( "limpaForm();" );
    $obFormulario->defineBarra( array($obBtnOk, $obBtnCancelar) );

    $obFormulario->show();

    if ($jsSL)
        SistemaLegado::executaFrameOculto($jsSL);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
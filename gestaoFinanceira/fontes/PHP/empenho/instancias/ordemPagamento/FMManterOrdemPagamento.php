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
    * Formulario para Empenho - Ordem de Pagamento
    * Data de Criação   : 16/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: FMManterOrdemPagamento.php 65431 2016-05-20 14:19:30Z arthur $

    * Casos de uso: uc-02.03.20
                    uc-02.03.04
                    uc-02.03.05
                    uc-02.03.28
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once CAM_GF_INCLUDE."validaGF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php";
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemPagamento";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgRelBirt  = "OCGeraRelatorio".$stPrograma."Birt.js";
$pgProx = CAM_GF_EMP_INSTANCIAS . "liquidacao/".$request->get('pgProxLiquidacao');
if ( empty($js) ) {
    $js = "";
}
include_once $pgJs;

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//valida a utilização da rotina de encerramento do mês contábil
$mesAtual = date('m');
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

if ($rsUltimoMesEncerrado->getCampo('mes') >= $mesAtual AND $boUtilizarEncerramentoMes == 'true' AND $stAcao == "incluir") {
    $obSpan = new Span;
    $obSpan->setValue('<b>Não é possível utilizar esta rotina pois o mês atual está encerrado!</b>');
    $obSpan->setStyle('align: center;');
    $obFormulario = new Formulario;
    $obFormulario->addSpan($obSpan);
    $obFormulario->show();
} else {
    $stFiltro = '';
    if ( Sessao::read('filtro') ) {
        $arFiltro = Sessao::read('filtro');
        $stFiltro = '';
        foreach ($arFiltro as $stCampo => $stValor) {
            if (is_array($stValor)) {
                $stFiltro .= "&".$stCampo."=".urlencode( implode(',', $stValor) );
            } else {
                $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
            }
        }
        $stFiltro .= '&pg='.Sessao::read('pg').'&pos='.Sessao::read('pos').'&paginando'.Sessao::read('paginando');
    }

    Sessao::remove('itemOrdem');
    Sessao::remove('itemRetencao');
    Sessao::remove('nuTlValorRetencao');
    Sessao::remove('dtUltimaLiquidacao');
    Sessao::remove('valorTotalOrdem');
    Sessao::remove('cgmFornecedor');
    Sessao::remove('assinaturas');

    // DEFINE OBJETOS DAS CLASSES
    $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setExercicio( Sessao::getExercicio()    );
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->obRCGM->setNumCGM( Sessao::read('numCgm')  );
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->listarEntidadeRestos( $rsEntidade );

    $stNomeEntidade = $request->get('stNomeEntidade');

    if ($stAcao == 'anular') {
        $obREmpenhoOrdemPagamento->setCodigoOrdem ( $_REQUEST["inCodigoOrdem"] );
        $obREmpenhoOrdemPagamento->setExercicio( Sessao::getExercicio() );
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
        $obREmpenhoOrdemPagamento->consultar();
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->consultarNomes($rsEntidade);
        $stNomeEntidade = $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getNomeEntidade();
        $stDescricaoOrdem = $obREmpenhoOrdemPagamento->getObservacao();

        $obTEmpenhoOrdemPagamentoLiquidacaoAnulada = new TEmpenhoOrdemPagamentoLiquidacaoAnulada();
        $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'cod_ordem'   , $obREmpenhoOrdemPagamento->getCodigoOrdem() );
        $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'cod_entidade', $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade() );
        $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado( 'exercicio'   , Sessao::getExercicio() );
        $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->recuperaValorAnular( $rsValores );

        while ( !$rsValores->eof() ) {
            $vlTotalAAnular += $rsValores->getCampo( 'vl_a_anular' );
            $rsValores->proximo();
        }
        $vlTotalAAnular = number_format( $vlTotalAAnular, 2, ',','.' );
    }

    $dtDataEmissao = date('d/m')."/".Sessao::getExercicio();
    $dtDataVencimento = $request->get('dtDataVencimento') ? $_REQUEST['dtDataVencimento'] : "31/12/".Sessao::getExercicio();
    // OBJETOS HIDDEN
    $obHdnCtrl = new Hidden;
    $obHdnCtrl->setName  ( "stCtrl"            );
    $obHdnCtrl->setValue ( $request->get('stCtrl') );

    $obHdnAcao = new Hidden;
    $obHdnAcao->setName  ( "stAcao"            );
    $obHdnAcao->setValue ( $request->get('stAcao') );

    $obHdnValorTotal = new Hidden;
    $obHdnValorTotal->setName  ( "hdnValorTotal" );
    $obHdnValorTotal->setValue ( $request->get('flValorTotal')   );

    $obHdnValorAnulado = new Hidden;
    $obHdnValorAnulado->setName  ( "hdnValorAnulado" );
    $obHdnValorAnulado->setValue ( $request->get('flValorAnulado')   );

    $obHdnValorAnular = new Hidden;
    $obHdnValorAnular->setName  ( "hdnValorAnular" );
    $obHdnValorAnular->setValue ( $request->get('flValorAnular')   );

    $obHdnDataEmissao = new Hidden;
    $obHdnDataEmissao->setName  ( "hdnDataEmissao" );
    $obHdnDataEmissao->setValue ( $dtDataEmissao   );

    $obHdnCodigoOrdem = new Hidden;
    $obHdnCodigoOrdem->setName  ( "hdnCodigoOrdem"           );
    $obHdnCodigoOrdem->setValue ( $request->get('inCodigoOrdem') );

    $obHdnExercicioOrdem = new Hidden;
    $obHdnExercicioOrdem->setName  ( "hdnExercicioOrdem"      );
    $obHdnExercicioOrdem->setValue ( $request->get('stExercicio') );

    $obHdnExercicioNota = new Hidden;
    $obHdnExercicioNota->setName  ( "hdnExercicioNota"       );
    $obHdnExercicioNota->setValue ( $request->get('stExercicioNota') );

    $obHdnCodigoEntidade = new Hidden;
    $obHdnCodigoEntidade->setName  ( "hdnCodigoEntidade"           );
    $obHdnCodigoEntidade->setId ( "hdnCodigoEntidade" );
    $obHdnCodigoEntidade->setValue ( $request->get('inCodEntidade') );

    $obHdnDataLiquidacao = new Hidden;
    $obHdnDataLiquidacao->setName  ( "stDataLiquidacao"            );
    $obHdnDataLiquidacao->setValue ( "01/01/".Sessao::getExercicio() );

    $obHdnDataVencimento = new Hidden;
    $obHdnDataVencimento->setName  ( "dtDataVencimento"            );
    $obHdnDataVencimento->setValue ( $request->get('dtDataVencimento') );

    if ($stAcao == 'anular') {
        $obHdnImplantado = new Hidden;
        $obHdnImplantado->setName  ( "boImplantado"            );
        $obHdnImplantado->setValue ( $_REQUEST["boImplantado"] );
    }

    if ($request->get('stLiq') ) {
        $inCodigoEntidade = $_REQUEST['inCodEntidade'];

        // Define Objeto Hidden para informar que a ultima acao foi a liquidacao de um empenho
        $obHdnEmissaoEmpenho = new Hidden;
        $obHdnEmissaoEmpenho->setName   ( "stEmitirEmpenho" );
        $obHdnEmissaoEmpenho->setValue  ( $_REQUEST['stEmitirEmpenho']  );

        // Define Objeto Hidden para informar que a ultima acao foi a liquidacao de um empenho
        $obHdnEmissaoLiquidacao = new Hidden;
        $obHdnEmissaoLiquidacao->setName   ( "stLiq" );
        $obHdnEmissaoLiquidacao->setValue  ( $_REQUEST['stLiq']  );

        $obHdnCodEntidadeLiquidacao = new Hidden;
        $obHdnCodEntidadeLiquidacao->setName  ( "inCodEntidadeLiquidacao");
        $obHdnCodEntidadeLiquidacao->setValue ( $_REQUEST['inCodEntidade'] );

        $obHdnCodEmpenhoLiquidacao = new Hidden;
        $obHdnCodEmpenhoLiquidacao->setName  ( "inCodEmpenhoLiquidacao");
        $obHdnCodEmpenhoLiquidacao->setValue ( $_REQUEST['inCodEmpenho'] );

        $obHdnExercicioEmpenhoLiquidacao = new Hidden;
        $obHdnExercicioEmpenhoLiquidacao->setName  ( "stExercicioEmpenhoLiquidacao");
        $obHdnExercicioEmpenhoLiquidacao->setValue ( $_REQUEST['dtExercicioEmpenho'] );

        $obHdnCodNotaLiquidacao = new Hidden;
        $obHdnCodNotaLiquidacao->setName  ( "inCodNotaLiquidacao");
        $obHdnCodNotaLiquidacao->setValue ( $_REQUEST['inCodNota'] );

        $obHdnExercicioNotaLiquidacao = new Hidden;
        $obHdnExercicioNotaLiquidacao->setName  ( "stExercicioNotaLiquidacao");
        $obHdnExercicioNotaLiquidacao->setValue ( $_REQUEST['stExercicioNota'] );

        // Define Objeto Hidden para setar a acao da pagina da liquidacao
        $obHdnAcaoLiq = new Hidden;
        $obHdnAcaoLiq->setName   ( "stAcaoLiquidacao" );
        $obHdnAcaoLiq->setValue  ( $_REQUEST['stAcaoLiquidacao'] );

        // Define Objeto Hidden para setar a pagina para direcionamento da liquidacao
        $obHdnPgProxLiquidacao = new Hidden;
        $obHdnPgProxLiquidacao->setName   ( "pgProxLiquidacao" );
        $obHdnPgProxLiquidacao->setValue  ( $pgProx            );

        // Define Objeto Hidden para setar a acao da liquidacao
        $obHdnAcaoLiquidacao = new Hidden;
        $obHdnAcaoLiquidacao->setName   ( "acaoLiquidacao" );
        $obHdnAcaoLiquidacao->setValue  ( $_REQUEST['acaoLiquidacao'] );

        // Define Objeto Hidden para setar o modulo da liquidacao
        $obHdnModuloLiquidacao = new Hidden;
        $obHdnModuloLiquidacao->setName   ( "moduloLiquidacao" );
        $obHdnModuloLiquidacao->setValue  ( $_REQUEST['moduloLiquidacao'] );

        // Define Objeto Hidden para setar a funcionalidade da liquidacao
        $obHdnFuncionalidadeLiquidacao = new Hidden;
        $obHdnFuncionalidadeLiquidacao->setName   ( "funcionalidadeLiquidacao" );
        $obHdnFuncionalidadeLiquidacao->setValue  ( $_REQUEST['funcionalidadeLiquidacao'] );

        // Define Objeto Hidden para setar a acao da pagina do empenho
        $obHdnAcaoEmp = new Hidden;
        $obHdnAcaoEmp->setName   ( "stAcaoEmpenho" );
        $obHdnAcaoEmp->setValue  ( $_REQUEST['stAcaoEmpenho'] );

        // Define Objeto Hidden para setar a pagina para direcionamento do empenho
        $obHdnPgProxEmpenho = new Hidden;
        $obHdnPgProxEmpenho->setName   ( "pgProxEmpenho" );
        $obHdnPgProxEmpenho->setValue  ( $_REQUEST['pgProxEmpenho'] );

        $obHdnPgDespesasFixas = new Hidden;
        $obHdnPgDespesasFixas->setName ( 'pgDespesasFixas' );
        $obHdnPgDespesasFixas->setValue ($_REQUEST['pgDespesasFixas'] );

        // Define Objeto Hidden para setar a acao do empenho
        $obHdnAcaoEmpenho = new Hidden;
        $obHdnAcaoEmpenho->setName   ( "acaoEmpenho" );
        $obHdnAcaoEmpenho->setValue  ( $_REQUEST['acaoEmpenho'] );

        // Define Objeto Hidden para setar o modulo do empenho
        $obHdnModuloEmpenho = new Hidden;
        $obHdnModuloEmpenho->setName   ( "moduloEmpenho" );
        $obHdnModuloEmpenho->setValue  ( $_REQUEST['moduloEmpenho'] );

        // Define Objeto Hidden para setar a funcionalidade do empenho
        $obHdnFuncionalidadeEmpenho = new Hidden;
        $obHdnFuncionalidadeEmpenho->setName   ( "funcionalidadeEmpenho" );
        $obHdnFuncionalidadeEmpenho->setValue  ( $_REQUEST['funcionalidadeEmpenho'] );

        // Define Objeto Hidden para setar a pagina da liquidacao
        $obHdnPgLiquidacao = new Hidden;
        $obHdnPgLiquidacao->setName   ( "inPg" );
        $obHdnPgLiquidacao->setValue  ( $_REQUEST['pg'] );

        // Define Objeto Hidden para setar a posicao da liquidacao
        $obHdnPosLiquidacao = new Hidden;
        $obHdnPosLiquidacao->setName   ( "inPos" );
        $obHdnPosLiquidacao->setValue  ( $_REQUEST['pos'] );
    }

    // DEFINE OBJETOS DO FORMULARIO - INCLUIR
    // LABELS PARA ANULACAO
    $obLblEntidade = new Label;
    $obLblEntidade->setRotulo      ( "Entidade"           );
    $obLblEntidade->setName        ( "stEntidade"         );
    $obLblEntidade->setValue       ( $request->get('inCodEntidade')." - ".$stNomeEntidade );

    $obLblNumeroOrdem = new Label;
    $obLblNumeroOrdem->setRotulo   ( "Número da Ordem"    );
    $obLblNumeroOrdem->setName     ( "inCodigoOrdem"      );
    $obLblNumeroOrdem->setValue    ( $request->get('inCodigoOrdem')."/".$request->get('stExercicio') );

    $obLblFornecedor = new Label;
    $obLblFornecedor->setRotulo    ( "Fornecedor"         );
    $obLblFornecedor->setName      ( "stFornecedor"       );
    $obLblFornecedor->setValue     ( $request->get('inNumCGM')." - ".$request->get('stNomeCGM') );

    $obLblVencimento = new Label;
    $obLblVencimento->setRotulo    ( "Data de Vencimento" );
    $obLblVencimento->setName      ( "dtVencimento"       );
    $obLblVencimento->setValue     ( $request->get('dtDataVencimento') );

    if ($request->get('stLiq')) {
        $inCodEntidade = $_REQUEST['inCodEntidade'];
    }

    $obTxtCodigoEntidade = new TextBox;
    $obTxtCodigoEntidade->setName        ( "inCodEntidade"             );
    $obTxtCodigoEntidade->setId          ( "inCodEntidade"             );
    if ($rsEntidade->getNumLinhas()==1) {
         $obTxtCodigoEntidade->setValue       ( $rsEntidade->getCampo('cod_entidade')  );
           $obTxtCodigoEntidade->obEvento->setOnChange("buscaDado('buscaDtOrdem');");
    } else {
        if (isset($inCodEntidade)) {
            $obTxtCodigoEntidade->setValue       ( $inCodEntidade              );
        }
         $obTxtCodigoEntidade->obEvento->setOnChange("buscaDado('buscaDtOrdem');");

    }
    $obTxtCodigoEntidade->setRotulo      ( "Entidade"                     );
    $obTxtCodigoEntidade->setTitle       ( "Selecione a entidade."        );
    $obTxtCodigoEntidade->setInteiro     ( true                           );
    $obTxtCodigoEntidade->setNull        ( false                          );

    // Define Objeto Select para Nome da Entidade
    $obCmbNomeEntidade = new Select;
    $obCmbNomeEntidade->setName          ( "stNomeEntidade"               );
    $obCmbNomeEntidade->setId            ( "stNomeEntidade"               );
    if ($rsEntidade->getNumLinhas()>1) {
        $obCmbNomeEntidade->addOption              ( "", "Selecione"               );
        $obCmbNomeEntidade->obEvento->setOnChange("buscaDado('buscaDtOrdem');"  );
    }
    if (isset($inCodEntidade)) {
        $obCmbNomeEntidade->setValue         ( $inCodEntidade                 );
    }
    $obCmbNomeEntidade->setCampoId       ( "cod_entidade"                 );
    $obCmbNomeEntidade->setCampoDesc     ( "nom_cgm"                      );
    $obCmbNomeEntidade->setStyle         ( "width: 520"                   );
    $obCmbNomeEntidade->preencheCombo    ( $rsEntidade                    );
    $obCmbNomeEntidade->setNull          ( false                          );

    Sessao::write('componentes', $obCmbNomeEntidade );

    $obTxtDescricao = new TextArea;
    $obTxtDescricao->setId               ( "stDescricaoOrdem"             );
    $obTxtDescricao->setName             ( "stDescricaoOrdem"             );
    $obTxtDescricao->setRotulo           ( "Descrição da Ordem"           );
    $obTxtDescricao->setTitle            ( "Informe a descrição da ordem.");
    if (isset($stDescricaoOrdem)) {
        $obTxtDescricao->setValue            ( $stDescricaoOrdem              );
    }
    $obTxtDescricao->setMaxCaracteres    ( 600                            );
    if ($_REQUEST["stAcao"] == "anular") {
        $obTxtDescricao->setReadOnly     ( true                           );
        $obTxtDescricao->setNull         ( true                           );
    } else {
        $obTxtDescricao->setNull         ( false                          );
    }

    $obDtOrdem = new Data;
    $obDtOrdem->setRotulo ( "Data da OP"             );
    $obDtOrdem->setId     ( "stDtOrdem"              );
    $obDtOrdem->setName   ( "stDtOrdem"              );
    $obDtOrdem->setRotulo ( "Data da Ordem"          );
    $obDtOrdem->setTitle  ('Informe a data da ordem.');
    $obDtOrdem->setNull   ( false );

    $obDtDataVencimento = new Data;
    $obDtDataVencimento->setName          ( "dtDataVencimento"            );
    $obDtDataVencimento->setRotulo        ( "Data do Vencimento"          );
    $obDtDataVencimento->setTitle         ( "Informe a data do vencimento." );
    $obDtDataVencimento->setValue         ( $dtDataVencimento             );
    $obDtDataVencimento->setMaxLength     ( 20                            );
    $obDtDataVencimento->setSize          ( 10                            );
    $obDtDataVencimento->setNull          ( false                         );

    $obTxtExercicioEmpenho = new Exercicio;
    $obTxtExercicioEmpenho->setRotulo    ( 'Exercício do Empenho' );
    $obTxtExercicioEmpenho->setName      ( 'stExercicioEmpenho' );
    $obTxtExercicioEmpenho->setid        ( 'stExercicioEmpenho' );
    $obTxtExercicioEmpenho->setValue     ( Sessao::getExercicio()   );
    $obTxtExercicioEmpenho->setNull      ( false                );
    $obTxtExercicioEmpenho->obEvento->setOnBlur( "buscaLiquidacoes();" );

    // Define objeto BuscaInner para descrição e codigo do empenho
    $obBscEmpenho = new BuscaInner;
    $obBscEmpenho->setTitle                 ( "Informe o número do empenho.");
    $obBscEmpenho->setRotulo                ( "*Número do Empenho"          );
    $obBscEmpenho->setId                    ( "stDescEmpenho"               );
    if (isset($stDescEmpenho)) {
        $obBscEmpenho->setValue                 ( $stDescEmpenho                );
    }
    $obBscEmpenho->setNull                  ( true                          );
    $obBscEmpenho->obCampoCod->setName      ( "inCodigoEmpenho"             );
    $obBscEmpenho->obCampoCod->setValue     ( $request->get('inCodEmpenho') );
    $obBscEmpenho->obCampoCod->setSize      ( 10                            );
    $obBscEmpenho->obCampoCod->setMaxLength ( 10                            );
    $obBscEmpenho->obCampoCod->setInteiro   ( true                          );
    $obBscEmpenho->obCampoCod->setNull      ( true                          );
    $obBscEmpenho->obCampoCod->obEvento->setOnBlur  ( "buscaLiquidacoes();"         );
    $obBscEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','inCodigoEmpenho','stDescEmpenho','buscaEmpenho&inCodEntidade='+document.frm.inCodEntidade.value+'&stCampoExercicio=stExercicioEmpenho&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

    $obCmbLiquidacao = new Select;
    $obCmbLiquidacao->setName             ( "cmbLiquidacao"               );
    $obCmbLiquidacao->setRotulo           ( "*Liquidação"                 );
    $obCmbLiquidacao->setTitle            ( "Selecione a liquidação."     );
    $obCmbLiquidacao->addOption           ( "", "Selecione"               );
    $obCmbLiquidacao->setCampoId          ( "mixCombo"                    );
    $obCmbLiquidacao->setCampoDesc        ( "mixCombo"                    );
    $obCmbLiquidacao->setNull             ( true                          );
    $obCmbLiquidacao->setStyle            ( "width: 220px"                );
    $obCmbLiquidacao->obEvento->setOnChange ( "recuperaValorPagar(); "    );

    $obTxtValorPagar = new Moeda;
    $obTxtValorPagar->setName             ( "flValorPagar"                );
    $obTxtValorPagar->setId               ( "flValorPagar"               );
    $obTxtValorPagar->setRotulo           ( "Valor a Pagar"               );
    $obTxtValorPagar->setTitle            ( "Informe o valor a pagar."    );
    $obTxtValorPagar->setValue            ( $request->get('flValorPagar')     );
    $obTxtValorPagar->setSize             ( 14                            );
    $obTxtValorPagar->setMaxLength        ( 14                            );
    $obTxtValorPagar->setNull             ( true                          );
    $obTxtValorPagar->setReadOnly         ( false                         );

    $obTxtValorTotal = new Moeda;
    $obTxtValorTotal->setName             ( "flValorTotal"                );
    $obTxtValorTotal->setRotulo           ( "Total"                       );
    $obTxtValorTotal->setStyle            ( "border:none; background-color:#DCDCDC;");
    $obTxtValorTotal->setValue            ( $request->get('flValorTotal')     );
    $obTxtValorTotal->setSize             ( 14                            );
    $obTxtValorTotal->setMaxLength        ( 14                            );
    $obTxtValorTotal->setNull             ( true                          );
    $obTxtValorTotal->setReadOnly         ( true                          );

    $obLblValorAnulado = new Label;
    $obLblValorAnulado->setRotulo ('Valor Anulado ');
    $obLblValorAnulado->setValue  ( $request->get('flValorAnulado') );

    $obLblValorTotal = new Label;
    $obLblValorTotal->setRotulo ('Valor Original da OP');
    $obLblValorTotal->setValue  ( $request->get('flValorOP') );

    $obTxtFornecedor = new TextBox;
    $obTxtFornecedor->setName             ( "stFornecedor"                );
    $obTxtFornecedor->setRotulo           ( "Fornecedor"                  );
    $obTxtFornecedor->setStyle            ( "border:none; background-color:#DCDCDC;");
    $obTxtFornecedor->setValue            ( $request->get('stFornecedor')     );
    $obTxtFornecedor->setSize             ( 80                            );
    $obTxtFornecedor->setMaxLength        ( 80                            );
    $obTxtFornecedor->setNull             ( true                          );
    $obTxtFornecedor->setReadOnly         ( true                          );

    $obTxtMotivo = new TextArea;
    $obTxtMotivo->setName                 ( "stMotivoAnulacao"            );
    $obTxtMotivo->setRotulo               ( "Motivo"                      );
    $obTxtMotivo->setTitle                ( "Informe o motivo."           );
    $obTxtMotivo->setValue                ( $request->get('stMotivoAnulacao') );
    $obTxtMotivo->setNull                 ( false                         );

    $obTxtValorAnulado = new Moeda;
    $obTxtValorAnulado->setName           ( "flValorAnulado"              );
    $obTxtValorAnulado->setRotulo         ( "Valor Anulado"               );
    $obTxtValorAnulado->setTitle          ( "Informe o valor anulado."    );
    $obTxtValorAnulado->setValue          ( $request->get('flValorAnulado')   );
    $obTxtValorAnulado->setSize           ( 14                            );
    $obTxtValorAnulado->setMaxLength      ( 14                            );
    $obTxtValorAnulado->setNull           ( true                          );
    $obTxtValorAnulado->setReadOnly       ( true                          );

    $obSpnListaItem = new Span;
    $obSpnListaItem->setID("spnListaItem");

    $obSpnRetencoes = new Span;
    $obSpnRetencoes->setID( 'spnRetencoes' );

    $obBtnIncluirItem = new Button;
    $obBtnIncluirItem->setName              ( "btnIncluirItem" );
    $obBtnIncluirItem->setValue             ( "Incluir"        );
    $obBtnIncluirItem->setTipo              ( "button"         );
    $obBtnIncluirItem->obEvento->setOnClick ( "incluirItem();" );
    $obBtnIncluirItem->setDisabled          ( false            );

    $obBtnLimparItem = new Button;
    $obBtnLimparItem->setName               ( "btnLimparItem"  );
    $obBtnLimparItem->setValue              ( "Limpar"         );
    $obBtnLimparItem->setTipo               ( "button"         );
    $obBtnLimparItem->obEvento->setOnClick  ( "limparItem();"  );
    $obBtnLimparItem->setDisabled           ( false            );

    $obBtnClean = new Button;
    $obBtnClean->setName                    ( "btnClean"       );
    $obBtnClean->setValue                   ( "Limpar"         );
    $obBtnClean->setTipo                    ( "button"         );
    $obBtnClean->obEvento->setOnClick       ( "limparOrdem();" );
    $obBtnClean->setDisabled                ( false            );

    $obBtnOK = new Ok();
    $obBtnOK->obEvento->setOnClick ('if (Valida()) { Salvar(); BloqueiaFrames(true, false); }');

    $botoesSpanItem = array ( $obBtnIncluirItem , $obBtnLimparItem );
    $botoesForm     = array ( $obBtnOK , $obBtnClean );

    $obIFrame = new IFrame;
    $obIFrame->setName("telaListaNotaLiquidacao");
    $obIFrame->setWidth("0");
    $obIFrame->setHeight("0");
    $obIFrame->show();

    //DEFINICAO DOS COMPONENTES
    $obForm = new Form;
    $obForm->setAction ( $pgProc  );
    $obForm->setTarget ( "oculto" );

    if ( $request->get('stAcao') == "incluir" ) {

        if ($request->get('stLiq') ) {
            $js .= "document.frm.stDescricaoOrdem.focus();\n";
            $js .= "buscaDado('preencheListaLiquidacoes');\n";
        } else {
            $js .= "document.frm.inCodEntidade.focus();\n";
        }
    } elseif ( $request->get('stAcao') == "anular" ) {
        $js .= "focusAnular();\n";
    }

    SistemaLegado::executaiFrameOculto($js);

    if ( $request->get('stAcao') == "incluir" ) {
        include_once( CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
        $obMontaAssinaturas = new IMontaAssinaturas(null, 'ordem_pagamento');
        $obMontaAssinaturas->definePapeisDisponiveis('ordem_pagamento');
        $obMontaAssinaturas->setCampoEntidades('inCodEntidade');
        $obMontaAssinaturas->setFuncaoJS();
        $obMontaAssinaturas->setOpcaoAssinaturas( false );

    }

    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm       ( $obForm              );
    $obFormulario->addHidden     ( $obHdnCtrl           );
    $obFormulario->addHidden     ( $obHdnAcao           );
    $obFormulario->addHidden     ( $obHdnDataLiquidacao );
    $obFormulario->addHidden     ( $obHdnCodigoOrdem    );
    $obFormulario->addHidden     ( $obHdnExercicioOrdem );
    $obFormulario->addHidden     ( $obHdnExercicioNota  );
    $obFormulario->addHidden     ( $obHdnCodigoEntidade );
    $obFormulario->addHidden     ( $obHdnValorTotal     );
    $obFormulario->addHidden     ( $obHdnValorAnulado   );

    if ($request->get('stLiq') ) {

        $obFormulario->addHidden     ( $obHdnEmissaoEmpenho           );
        $obFormulario->addHidden     ( $obHdnEmissaoLiquidacao        );
        $obFormulario->addHidden     ( $obHdnAcaoLiq                  );
        $obFormulario->addHidden     ( $obHdnPgProxLiquidacao         );
        $obFormulario->addHidden     ( $obHdnAcaoLiquidacao           );
        $obFormulario->addHidden     ( $obHdnModuloLiquidacao         );
        $obFormulario->addHidden     ( $obHdnFuncionalidadeLiquidacao );
        $obFormulario->addHidden     ( $obHdnAcaoEmp                  );
        $obFormulario->addHidden     ( $obHdnPgProxEmpenho            );
        $obFormulario->addHidden     ( $obHdnPgDespesasFixas          );
        $obFormulario->addHidden     ( $obHdnAcaoEmpenho              );
        $obFormulario->addHidden     ( $obHdnModuloEmpenho            );
        $obFormulario->addHidden     ( $obHdnFuncionalidadeEmpenho    );
        $obFormulario->addHidden     ( $obHdnCodEntidadeLiquidacao      );
        $obFormulario->addHidden     ( $obHdnCodEmpenhoLiquidacao       );
        $obFormulario->addHidden     ( $obHdnExercicioEmpenhoLiquidacao );
        $obFormulario->addHidden     ( $obHdnCodNotaLiquidacao          );
        $obFormulario->addHidden     ( $obHdnExercicioNotaLiquidacao    );
        $obFormulario->addHidden     ( $obHdnPgLiquidacao               );
        $obFormulario->addHidden     ( $obHdnPosLiquidacao              );
    }
    if ($_REQUEST["stAcao"] == "incluir") {
        $obFormulario->addTitulo     ( "Dados da Ordem de Pagamento" );
        $obFormulario->addComponenteComposto( $obTxtCodigoEntidade , $obCmbNomeEntidade );
        $obFormulario->addComponente ( $obDtOrdem             );
        $obFormulario->addComponente ( $obDtDataVencimento    );
        $obFormulario->addTitulo     ( "Ítens"                );
        $obFormulario->addComponente ( $obTxtExercicioEmpenho );
        $obFormulario->addComponente ( $obBscEmpenho          );
        $obFormulario->addComponente ( $obCmbLiquidacao       );
        $obFormulario->addComponente ( $obTxtValorPagar       );
        $obFormulario->defineBarra   ( $botoesSpanItem        );
        $obFormulario->addComponente ( $obTxtFornecedor       );
        $obFormulario->addSpan       ( $obSpnListaItem        );
        $obFormulario->addComponente ( $obTxtValorTotal       );
        $obFormulario->addSpan       ( $obSpnRetencoes        );

        $obMontaAssinaturas->geraFormulario($obFormulario);
        $obFormulario->addTitulo     ( "Descrição da Ordem de Pagamento"   );
        $obFormulario->addComponente ( $obTxtDescricao        );

        $obFormulario->defineBarra   ( $botoesForm,'left',''  );

    } elseif ($_REQUEST["stAcao"] == "anular") {
        $obFormulario->addTitulo     ( "Dados da ordem"       );
        $obFormulario->addHidden     ( $obHdnImplantado       );
        $obFormulario->addHidden     ( $obHdnDataVencimento   );
        $obFormulario->addHidden     ( $obHdnValorAnular      );
        $obFormulario->addComponente ( $obLblEntidade         );
        $obFormulario->addComponente ( $obLblNumeroOrdem      );
        $obFormulario->addComponente ( $obLblFornecedor       );
        $obFormulario->addComponente ( $obTxtDescricao        );
        $obFormulario->addComponente ( $obLblValorTotal       );
        $obFormulario->addComponente ( $obLblValorAnulado     );
        $obFormulario->addComponente ( $obLblVencimento       );
        $obFormulario->addTitulo     ( "Anulação"             );
        $obFormulario->addComponente ( $obTxtMotivo           );
        $obFormulario->addSpan       ( $obSpnListaItem        );
        $stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro;

        $obOk = new Ok();
        $obOk->obEvento->setOnClick('if (Valida()) { Salvar(); BloqueiaFrames(true, false) }');

        $obCancelar = new Button();
        $obCancelar->setValue ("Cancelar");
        $obCancelar->obEvento->setOnclick("Cancelar('".$stLocation."');");

        //verificar depois se anulação precisa de assinaturar

        $obFormulario->defineBarra ( Array( $obOk, $obCancelar));
    }

    $obFormulario->show ();
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

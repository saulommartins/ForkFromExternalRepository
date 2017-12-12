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
    * Formulário
    * Data de Criação: 08/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-10 13:08:17 -0300 (Qua, 10 Out 2007) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                 );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once (CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php"                              );

//Define o nome dos arquivos PHP
$link = Sessao::read("link");
$stPrograma = "ManterRegistroEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$obTPessoalAdidoCedido = new TPessoalAdidoCedido();
$stFiltro = " AND contrato.cod_contrato = ".$request->get('inCodContrato');
$obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro,"",$boTransacao);

$stAcao = $request->get('stAcao');
$stLink = "";
foreach ($request->getAll() as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}

$stLocation = $pgList.$stLink;

$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

if( ($rsAdidoCedido->getCampo("tipo_cedencia") == "a" and $rsAdidoCedido->getCampo("indicativo_onus") == "c") or
    ($rsAdidoCedido->getCampo("tipo_cedencia") == "c" and $rsAdidoCedido->getCampo("indicativo_onus") == "e")){
    $stMensagem = "Para tipo de cedência adido/cedido e indicativo de ônus cedente/cessionário não é permitido registro de eventos.";
    $obLblMensagem = new Label;
    $obLblMensagem->setRotulo               ( "Situação"                                                );
    $obLblMensagem->setValue                ( $stMensagem                                               );

    $obBtnVoltar = new Voltar();
    $obBtnVoltar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");

    $obFormulario = new Formulario;
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addComponente($obLblMensagem);
    $obFormulario->defineBarra(array($obBtnVoltar),"","");
    $obFormulario->show();
} else {
    $inContrato          = ( $request->get('inContrato')          != "" ) ? $request->get('inContrato')          : Sessao::read('inContrato');
    $inNumCGM            = ( $request->get('inNumCGM')            != "" ) ? $request->get('inNumCGM')            : Sessao::read('inNumCGM');
    $stServidor          = ( $request->get('stNomCGM')            != "" ) ? $request->get('stNomCGM')            : Sessao::read('stNomCGM');
    $inCodFuncao         = ( $request->get('inCodFuncao')         != "" ) ? $request->get('inCodFuncao')         : Sessao::read('inCodFuncao');
    $inCodContrato       = ( $request->get('inCodContrato')       != "" ) ? $request->get('inCodContrato')       : Sessao::read('inCodContrato');
    $stAcao              = ( $request->get('stAcao')              != "" ) ? $request->get('stAcao')              : Sessao::read('stAcao');
    $inCodigo            = ( $request->get('inCodigo')            != "" ) ? $request->get('inCodigo')            : Sessao::read('inCodigo');
    $stDescricao         = ( $request->get('stDescricao')         != "" ) ? $request->get('stDescricao')         : Sessao::read('stDescricaoEvento');
    $stTextoComplementar = ( $request->get('stTextoComplementar') != "" ) ? $request->get('stTextoComplementar') : Sessao::read('stTextoComplementar');
    $stTipo              = ( $request->get('stTipo')              != "" ) ? $request->get('stTipo')              : Sessao::read('stTipo');
    $stFixado            = ( $request->get('stFixado')            != "" ) ? $request->get('stFixado')            : Sessao::read('stFixado');
    $nuValor             = ( $request->get('nuValor')             != "" ) ? $request->get('nuValor')             : Sessao::read('nuValor');
    $boLimiteCalculo     = ( $request->get('boLimiteCalculo')     != "" ) ? $request->get('boLimiteCalculo')     : Sessao::read('boLimiteCalculo');
    $stProventosDescontos= ( $request->get('stProventosDescontos')!= "" ) ? $request->get('stProventosDescontos'): Sessao::read('stProventosDescontos');

    $obRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inCodContrato );
    $obRFolhaPagamentoPeriodoContratoServidor->consultarContratoServidorSubDivisaoFuncao($rsSubDivisao,"","",$boTransacao);
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( $inCodFuncao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($rsSubDivisao->getCampo('cod_sub_divisao'));
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo($rsEspecialidade,$boTransacao);
    Sessao::write('inCodSubDivisao',$rsSubDivisao->getCampo('cod_sub_divisao'));
    Sessao::write('inCodEspecialidade',$rsEspecialidade->getCampo('cod_especialidade'));
    Sessao::write('inCodFuncao',$inCodFuncao);
    
    if ($stAcao == 'alterar') {
        $stMensagem = "Matrícula: ".Sessao::read('inContrato');
        Sessao::write('eventosFixos',array());
        Sessao::write('eventosVariaveis',array());
        Sessao::write('eventosProporcionais',array());
        Sessao::write('eventosBase',array());
        //Busca dos registros de evento já cadastrados para o contrato.
        $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsMovimentacao,$boTransacao);
        $obRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inCodContrato );
        $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsMovimentacao->getCampo('cod_periodo_movimentacao') );
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEvento.class.php");
        $obTFolhaPagamentoRegistroEvento = new TFolhaPagamentoRegistroEvento();
        $stFiltro  = " AND contrato.cod_contrato = ".$inCodContrato;
        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltro .= " AND evento_calculado.desdobramento is null";
        $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro,$boTransacao);        

        if ( $rsRegistroEvento->getNumLinhas() > 0 ) {
            Sessao::write('status',"alterar");
            $arEventosFixos = Sessao::read("eventosFixos");
            $arEventosVariaveis = Sessao::read("eventosVariaveis");
            $arEventosProporcionais = Sessao::read("eventosProporcionais");
            $rsRegistroEvento->addFormatacao('valor','NUMERIC_BR');
            $rsRegistroEvento->addFormatacao('quantidade','NUMERIC_BR');
            while ( !$rsRegistroEvento->eof() ) {
                if ( $rsRegistroEvento->getCampo('automatico') == 'f' ) {
                    $stAutomatico = 'Não';
                } else {
                    $stAutomatico = 'Sim';
                }
                $inIdExclusao = $rsRegistroEvento->getCampo('codigo').$rsRegistroEvento->getCampo('desdobramento');
                if ( $rsRegistroEvento->getCampo('evento_sistema') == 'f' and $rsRegistroEvento->getCampo('proporcional') == 't'  and $rsRegistroEvento->getCampo('natureza') != 'B' ) {
                    //Registro de eventos proporcionais
                    $arElementos = array();
                    $arElementos['inId']                = count($arEventosProporcionais);
                    $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                    $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
                    $arElementos['stDesdobramento']     = $rsRegistroEvento->getCampo('desdobramento');
                    $arElementos['nuValor']             = $rsRegistroEvento->getCampo('valor');
                    $arElementos['nuQuantidade']        = $rsRegistroEvento->getCampo('quantidade');
                    $arElementos['stTextoComplementar'] = $rsRegistroEvento->getCampo('observacao');
                    $arElementos['stTipo']              = $rsRegistroEvento->getCampo('tipo');
                    $arElementos['stFixado']            = $rsRegistroEvento->getCampo('fixado');
                    $arElementos['boLimiteCalculo']     = $rsRegistroEvento->getCampo('limite_calculo');
                    $arElementos['stProventosDescontos']= $rsRegistroEvento->getCampo('proventos_descontos');
                    $arElementos['inQuantidadeParc']    = $rsRegistroEvento->getCampo('parcela');
                    $arElementos['inMesCarencia']       = $rsRegistroEvento->getCampo('mes_carencia');
                    $arElementos['boAutomatico']        = $stAutomatico;
                    $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                    $arEventosProporcionais[]  = $arElementos;
                } elseif ( $rsRegistroEvento->getCampo('evento_sistema') == 'f' and $rsRegistroEvento->getCampo('tipo') == "V"  and $rsRegistroEvento->getCampo('natureza') != 'B' ) {
                    //Registro de eventos variáveis
                    $arElementos = array();
                    $arElementos['inId']                = count($arEventosVariaveis);
                    $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                    $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
                    $arElementos['stDesdobramento']     = $rsRegistroEvento->getCampo('desdobramento');
                    $arElementos['nuValor']             = $rsRegistroEvento->getCampo('valor');
                    $arElementos['nuQuantidade']        = $rsRegistroEvento->getCampo('quantidade');
                    $arElementos['stTextoComplementar'] = $rsRegistroEvento->getCampo('observacao');
                    $arElementos['stTipo']              = $rsRegistroEvento->getCampo('tipo');
                    $arElementos['stFixado']            = $rsRegistroEvento->getCampo('fixado');
                    $arElementos['boLimiteCalculo']     = $rsRegistroEvento->getCampo('limite_calculo');
                    $arElementos['stProventosDescontos']= $rsRegistroEvento->getCampo('proventos_descontos');
                    $arElementos['inQuantidadeParc']    = $rsRegistroEvento->getCampo('parcela');
                    $arElementos['inMesCarencia']       = $rsRegistroEvento->getCampo('mes_carencia');
                    $arElementos['boAutomatico']        = $stAutomatico;
                    $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                    $arEventosVariaveis[] = $arElementos;
                } elseif ( $rsRegistroEvento->getCampo('evento_sistema') == 'f' 
                           && $rsRegistroEvento->getCampo('natureza') != 'B' 
                           && $rsRegistroEvento->getCampo('automatico') != 't' 
                         ) {
                    //Registro de eventos fixos
                    $arElementos = array();
                    $arElementos['inId']                = count($arEventosFixos);
                    $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                    $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
                    $arElementos['stDesdobramento']     = $rsRegistroEvento->getCampo('desdobramento');
                    $arElementos['nuValor']             = $rsRegistroEvento->getCampo('valor');
                    $arElementos['nuQuantidade']        = $rsRegistroEvento->getCampo('quantidade');
                    $arElementos['stTextoComplementar'] = $rsRegistroEvento->getCampo('observacao');
                    $arElementos['stTipo']              = $rsRegistroEvento->getCampo('tipo');
                    $arElementos['stFixado']            = $rsRegistroEvento->getCampo('fixado');
                    $arElementos['boLimiteCalculo']     = $rsRegistroEvento->getCampo('limite_calculo');
                    $arElementos['stProventosDescontos']= $rsRegistroEvento->getCampo('proventos_descontos');
                    $arElementos['boAutomatico']        = $stAutomatico;
                    $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                    $arEventosFixos[]   = $arElementos;
                }
                $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodEvento($rsRegistroEvento->getCampo('cod_evento'));
                $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento($rsEvento);
                $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setTimestamp($rsEvento->getCampo('timestamp'));
                $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEventosBase($rsEventosBase);
                $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setTimestamp("");
                $arEventosBase = Sessao::read("eventosBase");
                while ( !$rsEventosBase->eof() ) {
                    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento($rsEventosBasePai);
                    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento_base'));
                    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento($rsEvento);
                    $arElementos = array();
                    $arElementos['inId']                = count($arEventosBase);
                    $arElementos['inCodigo']            = $rsEventosBasePai->getCampo('codigo');
                    $arElementos['inCodigoBase']        = $rsEvento->getCampo('codigo');
                    $arElementos['stDescricao']         = $rsEvento->getCampo('descricao');
                    $arElementos['stDesdobramento']     = $rsEvento->getCampo('desdobramento');
                    $arElementos['nuValor']             = $rsEvento->getCampo('valor');
                    $arElementos['nuQuantidade']        = $rsEvento->getCampo('quantidade');
                    $arElementos['stTextoComplementar'] = $rsEvento->getCampo('observacao');
                    $arElementos['stTipo']              = $rsEvento->getCampo('tipo');
                    $arElementos['stFixado']            = $rsEvento->getCampo('fixado');
                    $arElementos['boLimiteCalculo']     = $rsEvento->getCampo('limite_calculo');
                    $arElementos['stProventosDescontos']= $rsEvento->getCampo('proventos_descontos');
                    $arElementos['boAutomatico']        = $stAutomatico;
                    $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                    $arEventosBase[]    = $arElementos;
                    $rsEventosBase->proximo();
                }
                Sessao::write("eventosBase",$arEventosBase);
                $rsRegistroEvento->proximo();
            }
            Sessao::write("eventosFixos",$arEventosFixos);
            Sessao::write("eventosVariaveis",$arEventosVariaveis);
            Sessao::write("eventosProporcionais",$arEventosProporcionais);
        } else {
            Sessao::write('status',"incluir");
        }
        $inCodigo = "";
        $stTextoComplementar = "";
        $stAcao = "incluir";
    }
    if ($inCodigo != ""
    and $stDescricao != ""
    and $nuValor     != "") {
        Sessao::write('stProcesso',"inclusao");
    }
    Sessao::write('numAba',( Sessao::read('numAba') != "" ) ? Sessao::read('numAba') : 1);
    Sessao::write('inContrato',$inContrato);
    Sessao::write('inNumCGM',$inNumCGM);
    Sessao::write('stNomCGM',$stServidor);
    Sessao::write('stAcao','incluir');
    Sessao::write('stDescricaoEvento',$stDescricao);
    Sessao::write('inCodigo',$inCodigo);
    Sessao::write('stTextoComplementar',$stTextoComplementar);
    Sessao::write('stTipo',$stTipo);
    Sessao::write('stFixado',$stFixado);
    Sessao::write('nuValor',$nuValor);
    Sessao::write('boLimiteCalculo',$boLimiteCalculo);
    Sessao::write('stProventosDescontos',$stProventosDescontos);

    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    Sessao::write('boBase',($obRFolhaPagamentoConfiguracao->getApresentaAbaBase() == 'true')? true : false);
    include_once($pgJS);
    include_once($pgOcul);

    //DEFINICAO DOS COMPONENTES
    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                              );
    $obHdnAcao->setValue                            ( $stAcao                                               );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                              );
    $obHdnCtrl->setValue                            ( $stStrl                                               );

    $obLblContrato= new Label;
    $obLblContrato->setRotulo                       ( "Matrícula"                                            );
    $obLblContrato->setName                         ( "inContrato"                                          );
    $obLblContrato->setValue                        ( $inContrato                                           );

    $obLblCGM= new Label;
    $obLblCGM->setRotulo                            ( "CGM"                                                 );
    $obLblCGM->setName                              ( "stCGM"                                               );
    $obLblCGM->setValue                             ( $inNumCGM .' - '. str_replace("\\","",$stServidor)     );

    $obSpnSpan1 = new Span;
    $obSpnSpan1->setId                              ( "spnSpan1"                                            );

    $obHdnOkRetorno = new hidden();
    $obHdnOkRetorno->setName("stOkRetorno");

    $obBtnOkFiltro = new Ok;
    $obBtnOkFiltro->setName("okFiltro");
    $obBtnOkFiltro->setValue("OK/Filtro");
    $obBtnOkFiltro->obEvento->setOnClick("salvarOkFiltro();");

    $obBtnOkLista = new Ok;
    $obBtnOkLista->setName("okLista");
    $obBtnOkLista->setValue("OK/Lista");
    $obBtnOkLista->obEvento->setOnClick("salvarOkLista();");

    $obBtnCancelar = new Button;
    $obBtnCancelar->setName                         ( 'cancelar'                                            );
    $obBtnCancelar->setValue                        ( 'Cancelar'                                            );
    $obBtnCancelar->obEvento->setOnClick            ( "Cancelar('".$stLocation."');"                        );

    //DEFINICAO DO FORM
    $obForm = new Form;
    $obForm->setAction                              ( $pgProc                                               );
    $obForm->setTarget                              ( "oculto"                                              );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new FormularioAbas;
    $obFormulario->addForm                          ( $obForm                                               );
    $obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
    $obFormulario->addTitulo                        ( "Dados da Matrícula Servidor"                          );
    $obFormulario->addHidden                        ( $obHdnAcao                                            );
    $obFormulario->addHidden                        ( $obHdnCtrl                                            );
    $obFormulario->addComponente                    ( $obLblContrato                                        );
    $obFormulario->addComponente                    ( $obLblCGM                                             );

    $obFormulario->addAba                           ( "Eventos Fixos"                                       );
    $obFormulario->arAbas[0]->setTipTitle           ('Aba que contém os eventos do tipo fixo (permanentes).');
    
    $obFormulario->addAba                           ( "Eventos Proporcionais",true                          );
    $obFormulario->arAbas[1]->setTipTitle           ('Aba que contém os eventos fixos/variáveis proporcionalizados (sobrepõem aos fixos/variáveis somente para essa competência).');
    
    $obFormulario->addAba                           ( "Eventos Variaveis"                                   );
    $obFormulario->arAbas[2]->setTipTitle           ('Aba que contém os eventos do tipo variável (válidos para essa competência ou até data limite/parcelas).');
    
    $obFormulario->addAba                           ( "Prévia"                                              );
    $obFormulario->arAbas[3]->setTipTitle           ('Aba que executa o cálculo prévio dos eventos.');
    
    if ( Sessao::read('boBase') ) {
        $obFormulario->addAba                       ( "Base"                                                );
        $obFormulario->arAbas[4]->setTipTitle       ('Aba que contém os eventos do tipo base.');
        $inAbas = 5;
    } else {
        $inAbas = 4;
    }

    $obFormulario->addDiv                           ( $inAbas, "componente"                                 );
    $obFormulario->addSpan                          ( $obSpnSpan1                                           );
    $obFormulario->fechaDiv();
    $obFormulario->addHidden($obHdnOkRetorno);
    $obFormulario->defineBarra(array($obBtnOkFiltro,$obBtnOkLista,$obBtnCancelar));
    $obFormulario->show();

    processarFormInclusao(true);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
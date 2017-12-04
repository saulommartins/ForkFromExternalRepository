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
    * Formulário Manter Registro de Evento (Folha Complementar)
    * Data de Criação: 20/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-23 10:56:25 -0200 (Sex, 23 Nov 2007) $

    * Casos de uso: uc-04.05.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                 );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once (CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php"                              );

$link = Sessao::read("link");
//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
include_once($pgJS);

#sessao->transf = "";
Sessao::write('eventos',array());

$stAcao = ( $_POST['stAcao'] != "" ) ? $_POST['stAcao'] : $_GET['stAcao'];
$stLink = "";
foreach ($_GET as $stCampo=>$stValor) {
    if ($stCampo != 'PHPSESSID' and $stCampo != 'iURLRandomica' and $stCampo != 'stAcao') {
        $stLink .= "&".$stCampo."=".$stValor;
    }
}

$stLocation = $pgList.$stLink;

$obTPessoalAdidoCedido = new TPessoalAdidoCedido();
$stFiltro = " AND contrato.cod_contrato = ".$_GET['inCodContrato'];
$obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltro);

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
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
    $inContrato          = ( $_REQUEST['inContrato']          != "" ) ? $_REQUEST['inContrato']          : Sessao::read('inContrato');
    $inNumCGM            = ( $_REQUEST['inNumCGM']            != "" ) ? $_REQUEST['inNumCGM']            : Sessao::read('inNumCGM');
    $stServidor          = ( $_REQUEST['stNomCGM']            != "" ) ? $_REQUEST['stNomCGM']            : Sessao::read('stNomCGM');
    $stServidor          = str_replace("\\","",$stServidor);
    $inCodFuncao         = ( $_REQUEST['inCodFuncao']         != "" ) ? $_REQUEST['inCodFuncao']         : Sessao::read('inCodFuncao');
    $inCodContrato       = ( $_REQUEST['inCodContrato']       != "" ) ? $_REQUEST['inCodContrato']       : Sessao::read('inCodContrato');
    $stAcao              = ( $_REQUEST['stAcao']              != "" ) ? $_REQUEST['stAcao']              : Sessao::read('stAcao');
    $inCodigo            = ( $_REQUEST['inCodigo']            != "" ) ? $_REQUEST['inCodigo']            : Sessao::read('inCodigo');
    $stDescricao         = ( $_REQUEST['stDescricao']         != "" ) ? $_REQUEST['stDescricao']         : Sessao::read('stDescricaoEvento');
    $stTextoComplementar = ( $_REQUEST['stTextoComplementar'] != "" ) ? $_REQUEST['stTextoComplementar'] : Sessao::read('stTextoComplementar');
    $stTipo              = ( $_REQUEST['stTipo']              != "" ) ? $_REQUEST['stTipo']              : Sessao::read('stTipo');
    $stFixado            = ( $_REQUEST['stFixado']            != "" ) ? $_REQUEST['stFixado']            : Sessao::read('stFixado');
    $nuValor             = ( $_REQUEST['nuValor']             != "" ) ? $_REQUEST['nuValor']             : Sessao::read('nuValor');
    $boLimiteCalculo     = ( $_REQUEST['boLimiteCalculo']     != "" ) ? $_REQUEST['boLimiteCalculo']     : Sessao::read('boLimiteCalculo');
    $stProventosDescontos= ( $_REQUEST['stProventosDescontos']!= "" ) ? $_REQUEST['stProventosDescontos']: Sessao::read('stProventosDescontos');

    $obRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inCodContrato );
    $obRFolhaPagamentoPeriodoContratoServidor->consultarContratoServidorSubDivisaoFuncao($rsSubDivisao);
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo( $inCodFuncao );
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addCargoSubDivisao();
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->setCodSubDivisao($rsSubDivisao->getCampo('cod_sub_divisao'));
    $obRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->consultaEspecialidadeCargo($rsEspecialidade);
    Sessao::write('inCodSubDivisao',$rsSubDivisao->getCampo('cod_sub_divisao'));
    Sessao::write('inCodEspecialidade',$rsEspecialidade->getCampo('cod_especialidade'));
    Sessao::write('inCodFuncao',$inCodFuncao);
    $stMensagem = "Matrícula: ".Sessao::read('inContrato');
    #sessao->transf                         = array();
    Sessao::read('eventos',array());
    //Busca dos registros de evento já cadastrados para o contrato.
    $obRFolhaPagamentoFolhaComplementar = new RFolhaPagamentoFolhaComplementar( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoFolhaComplementar->addRFolhaPagamentoRegistroEventoComplementar();
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsMovimentacao,$boTransacao);
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $inCodContrato );
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsMovimentacao->getCampo('cod_periodo_movimentacao') );
    $link = Sessao::read("link");
    $obRFolhaPagamentoFolhaComplementar->setCodComplementar($link['inCodComplementar']);
    $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->listarRegistroEventoComplementar($rsRegistroEventoComplementar,$boTransacao);
    if ( $rsRegistroEventoComplementar->getNumLinhas() > 0 ) {
        $rsRegistroEventoComplementar->addFormatacao('valor','NUMERIC_BR');
        $rsRegistroEventoComplementar->addFormatacao('quantidade','NUMERIC_BR');
        $arEventos = Sessao::read("eventos");
        while ( !$rsRegistroEventoComplementar->eof() ) {
            if ( $rsRegistroEventoComplementar->getCampo('automatico') == 'f' ) {
                $stAutomatico = 'Não';
            } else {
                $stAutomatico = 'Sim';
            }
            if ( $rsRegistroEventoComplementar->getCampo('evento_sistema') == 'f' and $rsRegistroEventoComplementar->getCampo('natureza') != 'B' ) {
                //Registro de eventos
                $arElementos = array();
                $arElementos['inId']                = count($arEventos);
                $arElementos['inCodigo']            = $rsRegistroEventoComplementar->getCampo('codigo');
                $arElementos['stDescricao']         = $rsRegistroEventoComplementar->getCampo('descricao');
                $arElementos['stDesdobramento']     = $rsRegistroEventoComplementar->getCampo('desdobramento');
                $arElementos['nuValor']             = $rsRegistroEventoComplementar->getCampo('valor');
                $arElementos['nuQuantidade']        = $rsRegistroEventoComplementar->getCampo('quantidade');
                $arElementos['stTextoComplementar'] = $rsRegistroEventoComplementar->getCampo('observacao');
                $arElementos['inCodConfiguracao']   = $rsRegistroEventoComplementar->getCampo('cod_configuracao');
                $arElementos['stConfiguracao']      = trim($rsRegistroEventoComplementar->getCampo('descricao_configuracao'));
                $arElementos['stTipo']              = $rsRegistroEventoComplementar->getCampo('tipo');
                $arElementos['stFixado']            = $rsRegistroEventoComplementar->getCampo('fixado');
                $arElementos['boLimiteCalculo']     = $rsRegistroEventoComplementar->getCampo('limite_calculo');
                $arElementos['stProventosDescontos']= $rsRegistroEventoComplementar->getCampo('proventos_descontos');
                $arElementos['inQuantidadeParc']    = $rsRegistroEventoComplementar->getCampo('parcela');
                $arElementos['inCodRegistro']       = $rsRegistroEventoComplementar->getCampo('cod_registro');
                $arElementos['stTimestamp']         = $rsRegistroEventoComplementar->getCampo('timestamp');
                $arEventos[]  = $arElementos;
            }
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->setCodEvento($rsRegistroEventoComplementar->getCampo('cod_evento'));
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->listarEvento($rsEvento);
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->setTimestamp($rsEvento->getCampo('timestamp'));
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->listarEventosBase($rsEventosBase);
            $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->setTimestamp("");
            $arEventosBase = Sessao::read("eventosBase");
            while ( !$rsEventosBase->eof() ) {
                $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento'));
                $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->listarEvento($rsEventosBasePai);
                $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->setCodEvento($rsEventosBase->getCampo('cod_evento_base'));
                $obRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoRegistroEventoComplementar->obRFolhaPagamentoEvento->listarEvento($rsEvento);
                $arElementos = array();
                $arElementos['inId']                = count($arEventosBase);
                $arElementos['inCodigo']            = $rsEventosBasePai->getCampo('codigo');
                $arElementos['inCodigoBase']        = $rsEvento->getCampo('codigo');
                $arElementos['stDescricao']         = $rsEvento->getCampo('descricao');
                $arElementos['stDesdobramento']     = $rsRegistroEventoComplementar->getCampo('desdobramento');
                $arElementos['nuValor']             = $rsEvento->getCampo('valor');
                $arElementos['nuQuantidade']        = $rsEvento->getCampo('quantidade');
                $arElementos['stTextoComplementar'] = $rsEvento->getCampo('observacao');
                $arElementos['stTipo']              = $rsEvento->getCampo('tipo');
                $arElementos['stFixado']            = $rsEvento->getCampo('fixado');
                $arElementos['boLimiteCalculo']     = $rsEvento->getCampo('limite_calculo');
                $arElementos['stProventosDescontos']= $rsEvento->getCampo('proventos_descontos');
                $arElementos['inCodConfiguracao']   = $rsRegistroEventoComplementar->getCampo('cod_configuracao');
                $arElementos['stConfiguracao']      = $rsRegistroEventoComplementar->getCampo('descricao_configuracao');
                $arElementos['inCodRegistro']       = $rsRegistroEventoComplementar->getCampo('cod_registro');
                $arEventosBase[]    = $arElementos;
                $rsEventosBase->proximo();
            }
            Sessao::write("eventosBase",$arEventosBase);
            $rsRegistroEventoComplementar->proximo();
        }
        Sessao::write("eventos",$arEventos);
    }

    $inCodigo = "";
    $stTextoComplementar = "";
    $stAcao = "incluir";

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

    $arMeses['01'] = "Janeiro";
    $arMeses['02'] = "Fevereiro";
    $arMeses['03'] = "Março";
    $arMeses['04'] = "Abril";
    $arMeses['05'] = "Maio";
    $arMeses['06'] = "Junho";
    $arMeses['07'] = "Julho";
    $arMeses['08'] = "Agosto";
    $arMeses['09'] = "Setembro";
    $arMeses['10'] = "Outubro";
    $arMeses['11'] = "Novembro";
    $arMeses['12'] = "Dezembro";

    //DEFINICAO DOS COMPONENTES
    $obHdnAcao =  new Hidden;
    $obHdnAcao->setName                             ( "stAcao"                                                  );
    $obHdnAcao->setValue                            ( $stAcao                                                   );

    $obHdnCtrl =  new Hidden;
    $obHdnCtrl->setName                             ( "stCtrl"                                                  );
    $obHdnCtrl->setValue                            ( $stStrl                                                   );

    $link = Sessao::read("link");
    $obLblComplementar= new Label;
    $obLblComplementar->setRotulo                   ( "Complementar"                                            );
    $obLblComplementar->setName                     ( "stComplementar"                                          );
    $obLblComplementar->setValue                    ( $link['inCodComplementar']." - ".$arMeses[$link['stCompetencia']] );

    $obHdnComplementar =  new Hidden;
    $obHdnComplementar->setName                     ( "inCodComplementar"                                       );
    $obHdnComplementar->setValue                    ( $link['inCodComplementar']                        );

    $obLblContrato= new Label;
    $obLblContrato->setRotulo                       ( "Matrícula"                                                );
    $obLblContrato->setName                         ( "inContrato"                                              );
    $obLblContrato->setValue                        ( $inContrato                                               );

    $obLblCGM= new Label;
    $obLblCGM->setRotulo                            ( "CGM"                                                     );
    $obLblCGM->setName                              ( "stCGM"                                                   );
    $obLblCGM->setValue                             ( $inNumCGM .' - '. $stServidor                             );

    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar($boTransacao);
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $obBscEvento = new BuscaInner;
    $obBscEvento->setRotulo                         ( "*Evento"                                                 );
    $obBscEvento->setTitle                          ( "Informe o evento para ser lançado para o servidor."      );
    $obBscEvento->setId                             ( "inCampoInner"                                            );
    $obBscEvento->setValue                          ( ''                                                        );
    $obBscEvento->obCampoCod->setName               ( "inCodigo"                                                );
    $obBscEvento->obCampoCod->setValue              ( $inCodigo                                                 );
    $obBscEvento->obCampoCod->setMascara            ( $stMascaraEvento                                          );
    $obBscEvento->obCampoCod->setPreencheComZeros   ( 'E'                                                       );
    $obBscEvento->obCampoCod->obEvento->setOnBlur   ( "buscaValor('buscaEvento');"                              );
    $obBscEvento->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_FOL_POPUPS."folhaComplementar/FLManterRegistroEventoComplementar.php','frm','inCodigo','inCampoInner','','".Sessao::getId()."','800','550')" );

    $obLblTextoComplementar= new Label;
    $obLblTextoComplementar->setRotulo              ( "Texto Complementar"                                      );
    $obLblTextoComplementar->setName                ( "stTextoComplementar"                                     );
    $obLblTextoComplementar->setId                  ( "stTextoComplementar"                                     );
    $obLblTextoComplementar->setValue               ( $stTextoComplementar                                      );

    $obHdnTextoComplementar= new Hidden;
    $obHdnTextoComplementar->setName                ( "hdnTextoComplementar"                                    );
    $obHdnTextoComplementar->setValue               ( $stTextoComplementar                                      );

    $obSpnSpan1 = new Span;
    $obSpnSpan1->setId                              ( "spnSpan1"                                                );

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
    $obForm->setAction                              ( $pgProc                                                   );
    $obForm->setTarget                              ( "oculto"                                                  );

    //DEFINICAO DO FORMULARIO
    $obFormulario = new FormularioAbas;
    $obFormulario->addForm                          ( $obForm                                                   );
    $obFormulario->addHidden                        ( $obHdnAcao                                                );
    $obFormulario->addHidden                        ( $obHdnCtrl                                                );
    $obFormulario->addHidden                        ( $obHdnComplementar                                        );
    $obFormulario->addTitulo                    ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"   );
    $obFormulario->addTitulo                        ( "Folha Complementar"                                      );
    $obFormulario->addComponente                    ( $obLblComplementar                                        );
    $obFormulario->addTitulo                        ( "Dados da Matrícula Servidor"                              );
    $obFormulario->addComponente                    ( $obLblContrato                                            );
    $obFormulario->addComponente                    ( $obLblCGM                                                 );
    $obFormulario->addAba                           ( "Eventos"                                                 );
    $obFormulario->addTitulo                        ( "Dados do Evento"                                         );
    $obFormulario->addComponente                    ( $obBscEvento                                              );
    $obFormulario->addComponente                    ( $obLblTextoComplementar                                   );
    $obFormulario->addHidden                        ( $obHdnTextoComplementar                                   );

    if ( Sessao::read('boBase') ) {
        $obFormulario->addAba                       ( "Base"                                                    );
        $inAbas = 5;
    } else {
        $inAbas = 4;
    }

    $obFormulario->addDiv                           ( $inAbas, "componente"                                     );
    $obFormulario->addSpan                          ( $obSpnSpan1                                               );
    $obFormulario->fechaDiv();
    $obFormulario->addHidden($obHdnOkRetorno);
    $obFormulario->defineBarra(array($obBtnOkFiltro,$obBtnOkLista,$obBtnCancelar));
    $obFormulario->show();

    processarForm(true);
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

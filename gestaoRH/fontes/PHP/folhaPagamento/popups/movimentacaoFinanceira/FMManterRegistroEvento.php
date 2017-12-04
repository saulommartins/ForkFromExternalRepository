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

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-26 07:00:14 -0300 (Ter, 26 Set 2006) $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                          );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );

$inRegistro          = ( $_REQUEST['inRegistro']          != "" ) ? $_REQUEST['inRegistro']          : Sessao::read('inRegistro');
$inNumCGM            = ( $_REQUEST['inNumCGM']            != "" ) ? $_REQUEST['inNumCGM']            : Sessao::read('inNumCGM');
$stServidor          = ( $_REQUEST['stServidor']          != "" ) ? $_REQUEST['stServidor']          : Sessao::read('stServidor');
$stAcao              = ( $_REQUEST['stAcao']              != "" ) ? $_REQUEST['stAcao']              : Sessao::read('stAcao');
$inCodigo            = ( $_REQUEST['inCodigo']            != "" ) ? $_REQUEST['inCodigo']            : Sessao::read('inCodigo');
$stDescricao         = ( $_REQUEST['stDescricao']         != "" ) ? $_REQUEST['stDescricao']         : Sessao::read('stDescricaoEvento');
$stTextoComplementar = ( $_REQUEST['stTextoComplementar'] != "" ) ? $_REQUEST['stTextoComplementar'] : Sessao::read('stTextoComplementar');
$stTipo              = ( $_REQUEST['stTipo']              != "" ) ? $_REQUEST['stTipo']              : Sessao::read('stTipo');
$stFixado            = ( $_REQUEST['stFixado']            != "" ) ? $_REQUEST['stFixado']            : Sessao::read('stFixado');
$nuValor             = ( $_REQUEST['nuValor']             != "" ) ? $_REQUEST['nuValor']             : Sessao::read('nuValor');
$boLimiteCalculo     = ( $_REQUEST['boLimiteCalculo']     != "" ) ? $_REQUEST['boLimiteCalculo']     : Sessao::read('boLimiteCalculo');
$stProventosDescontos= ( $_REQUEST['stProventosDescontos']!= "" ) ? $_REQUEST['stProventosDescontos']: Sessao::read('stProventosDescontos');
if ($stAcao == 'alterar' or $boInclusao == 1) {
    $stMensagem = "Matrícula: ".Sessao::read('inRegistro');
    #sessao->transf                         = array();
    Sessao::write('eventosFixos',array());
    Sessao::write('eventosVariaveis',array());
    Sessao::write('eventosProporcionais',array());
    //Busca dos registros de evento já cadastrados para o contrato.
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->setRegistro( $inRegistro );
    $obRFolhaPagamentoPeriodoContratoServidor->listarContratosServidorResumido($rsContrato,$boTransacao);
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsMovimentacao,$boTransacao);
    $obRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $rsContrato->getCampo('cod_contrato') );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $rsMovimentacao->getCampo('cod_periodo_movimentacao') );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->listarRegistroEvento($rsRegistroEvento,$boTransacao);
    if ( $rsRegistroEvento->getNumLinhas() > 0 ) {
        $rsRegistroEvento->addFormatacao('valor','NUMERIC_BR');
        $rsRegistroEvento->addFormatacao('quantidade','NUMERIC_BR');
        while ( !$rsRegistroEvento->eof() ) {
            if ( $rsRegistroEvento->getCampo('automatico') == 'f' ) {
                $stAutomatico = 'Não';
            } else {
                $stAutomatico = 'Sim';
            }
            if ( $rsRegistroEvento->getCampo('proporcional') == 't' ) {
                //Registro de eventos proporcionais
                $arEventosProporcionais = Sessao::read("eventosProporcionais");
                $arElementos = array();
                $arElementos['inId']                = count($arEventosProporcionais);
                $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
                $arElementos['nuValor']             = $rsRegistroEvento->getCampo('valor');
                $arElementos['nuQuantidade']        = $rsRegistroEvento->getCampo('quantidade');
                $arElementos['stTextoComplementar'] = $rsRegistroEvento->getCampo('observacao');
                $arElementos['stTipo']              = $rsRegistroEvento->getCampo('tipo');
                $arElementos['stFixado']            = $rsRegistroEvento->getCampo('fixado');
                $arElementos['boLimiteCalculo']     = $rsRegistroEvento->getCampo('limite_calculo');
                $arElementos['stProventosDescontos']= $rsRegistroEvento->getCampo('proventos_descontos');
                $arElementos['inQuantidadeParc']    = $rsRegistroEvento->getCampo('parcela');
                $arElementos['boAutomatico']        = $stAutomatico;
                $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                $arEventosProporcionais[] = $arElementos;
                Sessao::write("eventosProporcionais",$arEventosProporcionais);
            } elseif ( $rsRegistroEvento->getCampo('parcela') != "" ) {
                //Registro de eventos variáveis
                $arEventosVariaveis = Sessao::read("eventosVariaveis");
                $arElementos = array();
                $arElementos['inId']                = count($arEventosVariaveis);
                $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
                $arElementos['nuValor']             = $rsRegistroEvento->getCampo('valor');
                $arElementos['nuQuantidade']        = $rsRegistroEvento->getCampo('quantidade');
                $arElementos['stTextoComplementar'] = $rsRegistroEvento->getCampo('observacao');
                $arElementos['stTipo']              = $rsRegistroEvento->getCampo('tipo');
                $arElementos['stFixado']            = $rsRegistroEvento->getCampo('fixado');
                $arElementos['boLimiteCalculo']     = $rsRegistroEvento->getCampo('limite_calculo');
                $arElementos['stProventosDescontos']= $rsRegistroEvento->getCampo('proventos_descontos');
                $arElementos['inQuantidadeParc']    = $rsRegistroEvento->getCampo('parcela');
                $arElementos['boAutomatico']        = $stAutomatico;
                $arElementos['inCodRegistro']       = $rsRegistroEvento->getCampo('cod_registro');
                $arEventosVariaveis[]= $arElementos;
                Sessao::write("eventosVariaveis",$arEventosVariaveis);
            } else {
                //Registro de eventos fixos
                $arEventosFixos = Sessao::read("eventosFixos");
                $arElementos = array();
                $arElementos['inId']                = count($arEventosFixos);
                $arElementos['inCodigo']            = $rsRegistroEvento->getCampo('codigo');
                $arElementos['stDescricao']         = $rsRegistroEvento->getCampo('descricao');
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
                Sessao::write("eventosFixos",$arEventosFixos);
            }
            $rsRegistroEvento->proximo();
        }
    }
    $inCodigo = "";
    $stTextoComplementar = "";
    $stAcao = "incluir";
    if ($boInclusao == 1) {
        sistemaLegado::alertaAvisoPopUpPrincipal($pgFilt,$stMensagem,"incluir","aviso",Sessao::getId(), "../");
    }
}
if ($inCodigo != ""
and $stDescricao != ""
and $nuValor     != "") {
    Sessao::write('stProcesso',"inclusao");
}
Sessao::write('numAba',( Sessao::read('numAba') != "" ) ? Sessao::read('numAba') : 1);
Sessao::write('inRegistro',$inRegistro);
Sessao::write('inNumCGM',$inNumCGM);
Sessao::write('stServidor',$stServidor);
Sessao::write('stAcao','incluir');
Sessao::write('stDescricaoEvento',$stDescricao);
Sessao::write('inCodigo',$inCodigo);
Sessao::write('stTextoComplementar',$stTextoComplementar);
Sessao::write('stTipo',$stTipo);
Sessao::write('stFixado',$stFixado);
Sessao::write('nuValor',$nuValor);
Sessao::write('boLimiteCalculo',$boLimiteCalculo);
Sessao::write('stProventosDescontos',$stProventosDescontos);
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
$obLblContrato->setName                         ( "inRegistro"                                          );
$obLblContrato->setValue                        ( $inRegistro                                           );

$obLblCGM= new Label;
$obLblCGM->setRotulo                            ( "CGM"                                                 );
$obLblCGM->setName                              ( "stCGM"                                               );
$obLblCGM->setValue                             ( $inNumCGM .' - '. $stServidor                         );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                            );

$obBtnOk = new Ok;

$obBtnCancelar = new Button;
$obBtnCancelar->setName                         ( 'cancelar'                                            );
$obBtnCancelar->setValue                        ( 'Cancelar'                                            );
$obBtnCancelar->obEvento->setOnClick            ( "window.close();"                                     );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                               );
$obForm->setTarget                              ( "oculto"                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addIFrameOculto                  ( "oculto"                                              );
$obFormulario->obIFrame->setWidth               ( "100%"                                                );
$obFormulario->obIFrame->setHeight              ( "0"                                                   );
$obFormulario->addTitulo                        ( "Dados da Matrícula Servidor"                          );
$obFormulario->addHidden                        ( $obHdnAcao                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addComponente                    ( $obLblContrato                                        );
$obFormulario->addComponente                    ( $obLblCGM                                             );

//$obFormulario->addTitulo("Dados do Evento");

$obFormulario->addAba                           ( "Eventos Fixos"                                       );

$obFormulario->addAba                           ( "Eventos Proporcionais",true                          );

$obFormulario->addAba                           ( "Eventos Variaveis"                                   );

$obFormulario->addAba                           ( "Prévia"                                              );

$obFormulario->addDiv                           ( 4, "componente"                                       );
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obFormulario->fechaDiv();

$obFormulario->defineBarra                      ( array( $obBtnOk,$obBtnCancelar ) , '', ''             );
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName                              ( "telaMensagem"                                        );
$obIFrame->setWidth                             ( "100%"                                                );
$obIFrame->setHeight                            ( "50"                                                  );
$obIFrame->show();

processarFormInclusao(true);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

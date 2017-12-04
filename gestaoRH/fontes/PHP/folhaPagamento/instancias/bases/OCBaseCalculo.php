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
    * Data de Criação: 06/08/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.05.67

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculo.class.php"                               );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSequenciaCalculoEvento.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                            );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBases.class.php"                                          );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBasesEvento.class.php"                                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBasesEventoCriado.class.php"                              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                               );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

$stPrograma = 'BaseCalculo';
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stCtrl               = $request->get("stCtrl");
$stAcao               = $request->get("stAcao");
$inCodBase            = $request->get("inCodBase");
$boInsercaoAutomatica = $request->get("boInsercaoAutomatica");
$boEventoSistema      = $request->get("boEventoSistema");

function gerarSpanListaEvento()
{
    global $stAcao, $inCodBase;
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");

    $rsEventos = new RecordSet;
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = " AND evento.natureza in ('P', 'D') \n";
    $stOrdem  = " ORDER BY codigo                   \n";
    $obTFolhaPagamentoEvento->recuperaEventosFormatado( $rsEventos, $stFiltro, $stOrdem );

    $table = new Table();
    $table->setRecordset( $rsEventos );
    $table->setSummary('Lista de Eventos para o cálculo da Base');
    $table->Head->addCabecalho( 'Código'           , 10 );
    $table->Head->addCabecalho( 'Descrição'        , 40 );
    $table->Head->addCabecalho( 'Natureza'         , 20 );
    $table->Head->addCabecalho( 'Tipo'             , 10 );
    $table->Head->addCabecalho( 'Fixado'           , 5  );
    $table->Head->addCabecalho( 'Seq'              , 5  );

    $obChkTodosN = new Checkbox;
    $obChkTodosN->setName                        ( "boTodos" );
    $obChkTodosN->setId                          ( "boTodos" );
    $obChkTodosN->setRotulo                      ( "Marcar todas" );
    $obChkTodosN->obEvento->setOnChange          ( "selecionarTodos('n');" );
    $obChkTodosN->montaHTML();

    $table->Head->addCabecalho( $obChkTodosN->getHTML() , 1  );

    $table->Body->addCampo( 'codigo'    , 'C' );
    $table->Body->addCampo( 'descricao' , 'E' );
    $table->Body->addCampo( 'natureza'  , 'C' );
    $table->Body->addCampo( 'tipo'      , 'C' );
    $table->Body->addCampo( 'fixado'    , 'C' );
    $table->Body->addCampo( 'sequencia' , 'C' );

    $obChkPermissao = new CheckBox;
    $obChkPermissao->setName('arEventosCalculoBase_[cod_evento]_[codigo]');
    $obChkPermissao->setId('arEventosCalculoBase_[cod_evento]_[codigo]');

    $table->Body->addComponente($obChkPermissao, 'ok');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "jq('#spnListaEventos').html('". $stHTML ."'); ";

    if ($stAcao == "alterar") {
        // Todos os eventos da lista
        $arEventos = $rsEventos->getElementos();

        // Eventos que deverão estar setados
        $stCondicao = " AND bases_evento.cod_base = ".$inCodBase;
        $obTFolhaPagamentoBasesEvento = new TFolhaPagamentoBasesEvento();
        $obTFolhaPagamentoBasesEvento->recuperaRelacionamento($rsEventosBase, $stCondicao);

        $arEventosSetados = array();

        while ( !$rsEventosBase->eof() ) {
            if ( count($arEventos)>0) {
                foreach ($arEventos as $chave => $dadosEvento) {
                    $arEventosSetados[$chave] = $dadosEvento;
                    if ($rsEventosBase->getCampo('cod_evento') == $dadosEvento["cod_evento"]) {
                        $stJs .= "d.getElementById('arEventosCalculoBase_".$arEventosSetados[$chave]["cod_evento"]."_".$arEventosSetados[$chave]["codigo"]."').checked = true; ";
                    }

                    $arEventos = array();
                    $arEventos = $arEventosSetados;
                }
            }
            $rsEventosBase->proximo();
        }
        $rsEventos->preenche( $arEventosSetados );
        $rsEventos->setPrimeiroElemento();
    }

    return $stJs;
}

function gerarSpanInfEventoBase()
{
    global $stAcao, $inCodBase, $boInsercaoAutomatica;

    $boBloquea = false;

    $stOrdem = " ORDER BY sequencia";
    $obTFolhaPagamentoSequenciaCalculo = new TFolhaPagamentoSequenciaCalculo;
    $obTFolhaPagamentoSequenciaCalculo->recuperaTodos($rsSequencia, $stCondicao="", $stOrdem);

    //Busca Mascara
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    //Carregando sugestao de codigo do evento
    if ($stAcao == "incluir") {
        $stOrdem  = " ORDER BY codigo DESC";
        $stOrdem .= " LIMIT 1";
        $obTFolhaPagamentoEvento =  new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->recuperaTodos($rsListaEvento, $stCondicao="", $stOrdem);

        $stSugestaoCodigo = str_pad(($rsListaEvento->getCampo('codigo')+1),strlen($stMascaraEvento),"0",STR_PAD_LEFT);
        $stCodigo = $_REQUEST["stCodigo"] ? $_REQUEST["stCodigo"] : $stSugestaoCodigo;
    } elseif ($stAcao == "alterar") {
        $boBloquea = true;

        $boInsercaoAutomatica = "N";
        $stFiltro = " WHERE cod_base = ".$inCodBase;

        $boTFolhaPagamentoBases = new TFolhaPagamentoBases();
        $boTFolhaPagamentoBases->recuperaTodos( $rsBases, $stFiltro );

        $boTFolhaPagamentoBasesEventoCriado = new TFolhaPagamentoBasesEventoCriado();
        $boTFolhaPagamentoBasesEventoCriado->recuperaTodos( $rsBasesEventoCriado, $stFiltro );

        if ( $rsBasesEventoCriado->getNumLinhas() > 0 ) {
            $stFiltro = " AND evento.cod_evento = ".$rsBasesEventoCriado->getCampo('cod_evento');
            $obTFolhaPagamentoSequenciaCalculoEvento = new TFolhaPagamentoSequenciaCalculoEvento();
            $obTFolhaPagamentoSequenciaCalculoEvento->recuperaRelacionamento( $rsEvento, $stFiltro);

            $stCodigo = $rsEvento->getCampo('codigo');
            $stDescricaoEventoBase = $rsEvento->getCampo('descricao');
            $boInsercaoAutomatica = $rsBases->getCampo('insercao_automatica') == 'f' ? 'N' : 'S';
            $boEventoSistema = $rsEvento->getCampo('evento_sistema') == 'f' ? 'N' : 'S';
            $inCodSequencia = $rsEvento->getCampo('cod_sequencia');
        }
    }

    if (trim($boInsercaoAutomatica) != "N") {
        $obTxtCodigo= new TextBox;
        $obTxtCodigo->setRotulo           ( "Código do Evento" );
        $obTxtCodigo->setTitle            ( "Informe o código do evento da base, que não deve coincidir com códigos de eventos já criados." );
        $obTxtCodigo->setName             ( "stCodigo" );
        $obTxtCodigo->setValue            ( $stCodigo );
        $obTxtCodigo->setSize             ( 10 );
        $obTxtCodigo->setMaxLength        ( 5 );
        $obTxtCodigo->setMascara          ( $stMascaraEvento );
        $obTxtCodigo->setPreencheComZeros ( 'E' );
        $obTxtCodigo->setNull             ( false );
        $obTxtCodigo->setReadOnly         ( $boBloquea );

        $obTxtNomBase = new TextBox;
        $obTxtNomBase->setRotulo      ( "Descrição" );
        $obTxtNomBase->setTitle       ( "Informe a descrição do evento de base do cálculo." );
        $obTxtNomBase->setName        ( "stDescricaoEventoBase" );
        $obTxtNomBase->setId          ( "stDescricaoEventoBase" );
        $obTxtNomBase->setValue       ( $stDescricaoEventoBase  );
        $obTxtNomBase->setSize        ( 50 );
        $obTxtNomBase->setMaxLength   ( 50 );
        $obTxtNomBase->setNull        ( false );
        $obTxtNomBase->setInteiro     ( false  );
        $obTxtNomBase->setReadOnly    ( $boBloquea );

        $obRdEventoAutomaticoSistemaSim = new Radio();
        $obRdEventoAutomaticoSistemaSim->setName        ( "boEventoSistema" );
        $obRdEventoAutomaticoSistemaSim->setRotulo      ( "Evento Automático do Sistema" );
        $obRdEventoAutomaticoSistemaSim->setTitle       ( "Marque Sim em caso de base para utilização em tabelas pré definidas no sistema. Exemplo: BaseINSS, BaseIRRF, BaseFGTS." );
        $obRdEventoAutomaticoSistemaSim->setLabel       ( "Sim" );
        $obRdEventoAutomaticoSistemaSim->setValue       ( "S" );
        $obRdEventoAutomaticoSistemaSim->setNull        ( false );
        $obRdEventoAutomaticoSistemaSim->setDisabled    ( $boBloquea );
        if ($boEventoSistema == 'S') {
            $obRdEventoAutomaticoSistemaSim->setChecked ( true );
        }

        $obRdEventoAutomaticoSistemaNao = new Radio();
        $obRdEventoAutomaticoSistemaNao->setName        ( "boEventoSistema" );
        $obRdEventoAutomaticoSistemaNao->setRotulo      ( "Evento Automático do Sistema" );
        $obRdEventoAutomaticoSistemaNao->setTitle       ( "Marque Sim em caso de base para utilização em tabelas pré definidas no sistema. Exemplo: BaseINSS, BaseIRRF, BaseFGTS." );
        $obRdEventoAutomaticoSistemaNao->setLabel       ( "Não" );
        $obRdEventoAutomaticoSistemaNao->setValue       ( "N" );
        $obRdEventoAutomaticoSistemaNao->setNull        ( false );
        $obRdEventoAutomaticoSistemaNao->setDisabled    ( $boBloquea );
        if ($boEventoSistema != 'S') {
            $obRdEventoAutomaticoSistemaNao->setChecked ( true );
        }

        $obCmbSequenciaCalculo = new Select;
        $obCmbSequenciaCalculo->setRotulo       ( "Sequência de Cálculo"                                 );
        $obCmbSequenciaCalculo->setName         ( "inCodSequencia"                                       );
        $obCmbSequenciaCalculo->setStyle        ( "width: 200px"                                         );
        $obCmbSequenciaCalculo->setTitle        ( "Selecionar a sequência de cálculo para o evento de base. A sequência da base deve ser posterior a maior sequência dos eventos que a compõem." );
        $obCmbSequenciaCalculo->setCampoID      ( "cod_sequencia"                                        );
        $obCmbSequenciaCalculo->setCampoDesc    ( "sequencia"                                            );
        $obCmbSequenciaCalculo->addOption       ( "", "Selecione"                                        );
        $obCmbSequenciaCalculo->setValue        ( $inCodSequencia                                        );
        $obCmbSequenciaCalculo->setNull         ( false                                                  );
        $obCmbSequenciaCalculo->preencheCombo   ( $rsSequencia                                           );

        $obFormulario = new Formulario;
        $obFormulario->addTitulo         ( "Informações do Evento da Base de Cálculo" );
        $obFormulario->addComponente     ( $obTxtCodigo );
        $obFormulario->addComponente     ( $obTxtNomBase );
        $obFormulario->agrupaComponentes ( array($obRdEventoAutomaticoSistemaSim, $obRdEventoAutomaticoSistemaNao) );
        $obFormulario->addComponente     ( $obCmbSequenciaCalculo );

        $obFormulario->montaInnerHtml();
        $obFormulario->obJavaScript->montaJavaScript();
        $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
        $stEval = str_replace("\n","",$stEval);
        $stJs = isset($stJs) ? $stJs : "";

        $stJs .= "$('spnInfEventoBaseCalculo').innerHTML = '".$obFormulario->getHTML()."';  \n";
        $stJs .= "$('hdnInfEventoBaseCalculo').value = '".$stEval."';  \n";
    } else {
        $stJs .= "$('spnInfEventoBaseCalculo').innerHTML = '&nbsp;';  \n";
        $stJs .= "$('hdnInfEventoBaseCalculo').value = '';  \n";
    }

    return $stJs;
}

switch ($stCtrl) {
    case "gerarSpanListaEvento":
        $stJs = gerarSpanListaEvento();
    break;

    case "gerarSpanInfEventoBase":
        $stJs = gerarSpanInfEventoBase();
    break;
}

if ($stJs) {
    echo($stJs);
}
?>

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
* Página de Formulário Evento
* Data de Criação   : 03/02/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @ignore

$Id: FMManterEvento.php 66393 2016-08-23 18:52:42Z michel $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                   );
include_once( CAM_GA_ADM_NEGOCIO ."RFuncao.class.php"                                                 );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php"                             );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php"                           );
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoVerbaRescisoriaMTE.class.php" );

$link = Sessao::read("link");
$stPrograma = "ManterEvento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$link["pg"]."&pos=".$link["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
include_once($pgJs);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('eventosBaseSal', array());
Sessao::write('eventosBaseFer', array());
Sessao::write('eventosBase13o', array());
Sessao::write('eventosBaseRes', array());

$rsFuncao = new RecordSet;

//Instanciacao de objetos de regra
$obRFolhaPagamentoEvento  = new RFolhaPagamentoEvento;
$obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;

//Carrega combos de funcoes
$obRFuncao = new RFuncao;
$obRFuncao->setTipoFuncao( 'externa' );
$obRFuncao->listar( $rsFuncao );

//Carrega combo de sequencia
$obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->listarSequencia($rsSequencia);

//Carrega máscara do codigo do evento
$obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
$obRFolhaPagamentoConfiguracao->consultar();
$stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

//Carregando sugestao de codigo do evento
$obRFolhaPagamentoEvento->listarEvento($rsListaEvento);
$rsListaEvento->setUltimoElemento();
$stCodigo = str_pad(($rsListaEvento->getCampo('codigo')+1),strlen($stMascaraEvento),"0",STR_PAD_LEFT);

if ( (int) $stCodigo < (int) $stMascaraEvento) {
    for ($inIndex=(strlen($stMascaraEvento)-1);$inIndex>=0;$inIndex--) {
        if ($stCodigo[$inIndex] > $stMascaraEvento[$inIndex]) {
            $stCodigo[$inIndex]   = $stMascaraEvento[$inIndex];
            $stCodigo[$inIndex-1] = $stCodigo[$inIndex-1] + 1;
        }
    }
} else {
    $stCodigo = '';
}

//Carrega máscara de Elemento de Despesa
$stMascaraElementoDespesa = $obROrcamentoClassificacaoDespesa->recuperaMascara();

//Recupera atributos dinâmicos
if ($stAcao == "incluir") {
    $obRFolhaPagamentoEvento->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
} else {
    $arChaveAtributoEvento =  array( "cod_evento" => $_REQUEST["inCodEvento"] );
    $obRFolhaPagamentoEvento->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEvento );
    $obRFolhaPagamentoEvento->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
}

if ( empty($stAcao) || $stAcao == "incluir" ) {

    $obRFolhaPagamentoEvento->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
    #sessao->transf = "";
    #sessao->transf4 = "";
    #sessao->transf4["stAcao"] = $stAcao = "incluir";
    $stAcao = "incluir";
    Sessao::write("stAcao",$stAcao);
    $js .= "buscaValor('gerarSpans');";

} elseif ($stAcao) {

    SistemaLegado::BloqueiaFrames();

    Sessao::write("stAcao",$stAcao);
    $arAbas = array('1'=>'Sal','2'=>'Fer','3'=>'13o','4'=>'Res');

    $obRFolhaPagamentoEvento->setCodEvento( $_GET['inCodEvento'] );
    $obRFolhaPagamentoEvento->setTimestamp( $_GET['stTimestamp'] );
    $obRFolhaPagamentoEvento->consultarEvento();
    //Aba Identificacao
    $inCodEvento                = $obRFolhaPagamentoEvento->getCodEvento();
    $stTimestamp                = $obRFolhaPagamentoEvento->getTimestamp();
    $stCodigo                   = $obRFolhaPagamentoEvento->getCodigo();
    $stDescricaoIde             = $obRFolhaPagamentoEvento->getDescricao();
    $stSigla                    = $obRFolhaPagamentoEvento->getSigla();
    $stTextoComplementar        = $obRFolhaPagamentoEvento->getObservacao();
    $nuValor                    = $obRFolhaPagamentoEvento->getValor();
    $nuUnidadeQuantitativa      = $obRFolhaPagamentoEvento->getUnidadeQuantitativa();
    $stNatureza                 = $obRFolhaPagamentoEvento->getNatureza();
    Sessao::write('natureza',$stNatureza);
    $stTipo                     = $obRFolhaPagamentoEvento->getTipo();
    $stFixado                   = $obRFolhaPagamentoEvento->getFixado();
    $boLimiteCalculo            = $obRFolhaPagamentoEvento->getLimiteCalculo();
    $boApresentaParcela         = $obRFolhaPagamentoEvento->getApresentaParcela();
    $stEventoAutomatico         = $obRFolhaPagamentoEvento->getEventoAutomaticoSistema();
    $inCodSequencia             = $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->getCodSequencia();
    $stCodVerbaRescisoriaMTE    = $obRFolhaPagamentoEvento->getCodVerbaRescisoriaMTE();

    //Atributos dinâmicos
    $arChaveAtributoEvento =  array( "cod_evento" => $_GET["inCodEvento"] );
    $obRFolhaPagamentoEvento->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoEvento );
    $obRFolhaPagamentoEvento->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

    //Trata cada aba (tambem chamada de configuracao)
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->listarConfiguracaoEventoPorChave($rsConfiguracaoEvento,$inCodEvento,$stTimestamp,"");
    unset($obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento);
    unset($obRFolhaPagamentoEvento->arRFolhaPagamentoConfiguracaoEvento);
    while ( !$rsConfiguracaoEvento->eof() ) {
        $stAba = $arAbas[ $rsConfiguracaoEvento->getCampo('cod_configuracao') ];
        $arConfiguracao = array();

        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao( $rsConfiguracaoEvento->getCampo('cod_configuracao') );
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->consultarConfiguracaoEvento();

        $stVarRubrica = 'stRubricaDespesa'.$stAba;
        $$stVarRubrica = $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getMascClassificacao();

        //Consulta casos desta configuracao
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->listarConfiguracaoEventoCaso($rsConfiguracaoEventoCaso,$inCodEvento,$stTimestamp,$rsConfiguracaoEvento->getCampo('cod_configuracao'));
        unset($obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento);
        unset($obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->arFolhaPagamentoCasoEvento);
        while ( !$rsConfiguracaoEventoCaso->eof() ) {
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->setCodCaso( $rsConfiguracaoEventoCaso->getCampo('cod_caso') );
            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->consultarCasoEvento();

            //Trata cada caso (tambem chamado de particularidade)
            $stDescricaoCaso    = $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getDescricao();
            if ($obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getProporcaoAdiantamento() == "t") {
                $boConsProporcaoAdiantamento = "true";
            } else {
                $boConsProporcaoAdiantamento = "false";
            }
            if ($obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getProporcaoAbono() == "t") {
                $boProporcionalizarAbono = "true";
            } else {
                $boProporcionalizarAbono = "false";
            }
            if ( $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodTipoMedia() != "" ) {
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
                $obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
                $stFiltro = " WHERE cod_tipo = '".$obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->getCodTipoMedia()."'";
                $obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro);
                $inCodigoTipoMedia  = $rsTipoMedia->getCampo("codigo");
            }
            $inCodFuncao     = $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->roRModulo->getCodModulo().".";
            $inCodFuncao    .= $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->obRBiblioteca->getCodigoBiblioteca().".";
            $inCodFuncao    .= $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->getCodFuncao();
            $stFuncao        = $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roRFuncao->getNomeFuncao();

            $arRPessoalSubDivisao = $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->arRPessoalSubDivisao;
            $arSubDivisao = array();
            for ( $inIndex = 0; $inIndex < count($arRPessoalSubDivisao); $inIndex++  ) {
                $obRPessoalSubDivisao = $arRPessoalSubDivisao[$inIndex];
                $arSubDivisao[] = $obRPessoalSubDivisao->roPessoalRegime->getCodRegime()."/".$obRPessoalSubDivisao->getCodSubDivisao();

            }

            $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->listarConfiguracaoEventoCasoCargo($rsConfiguracaoEventoCasoCargo,$inCodEvento,$stTimestamp,$rsConfiguracaoEvento->getCampo('cod_configuracao'),$rsConfiguracaoEventoCaso->getCampo('cod_caso'),$stTimestamp);
            $arCargo = array();
            while ( !$rsConfiguracaoEventoCasoCargo->eof() ) {
                $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->listarConfiguracaoEventoCasoEspecialidade($rsConfiguracaoEventoCasoEspecialidade,$inCodEvento,$stTimestamp,$rsConfiguracaoEvento->getCampo('cod_configuracao'),$rsConfiguracaoEventoCaso->getCampo('cod_caso'),$rsConfiguracaoEventoCasoCargo->getCampo('cod_cargo'),$stTimestamp);
                if ( $rsConfiguracaoEventoCasoEspecialidade->getNumLinhas() > 0 ) {
                    while ( !$rsConfiguracaoEventoCasoEspecialidade->eof() ) {
                        $arCargo[] = $rsConfiguracaoEventoCasoCargo->getCampo('cod_cargo')."/".$rsConfiguracaoEventoCasoEspecialidade->getCampo('cod_especialidade');
                        $rsConfiguracaoEventoCasoEspecialidade->proximo();
                    }
                } else {
                    $arCargo[] = $rsConfiguracaoEventoCasoCargo->getCampo('cod_cargo');
                }
                $rsConfiguracaoEventoCasoCargo->proximo();
            }
            //Processamento das informações sobre eventos base
            $obRFolhaPagamentoEvento->listarEventosBase($rsEventosBase);
            $arEventosBase = (is_array($rsEventosBase->getElementos()))?$rsEventosBase->getElementos():array();
            $arEventosBaseTemp = array();
            $inIdEventoBase = 1;
            foreach ($arEventosBase as $arEventoBase) {
                $arTemp = array();
                $arTemp['inId']             = $inIdEventoBase;
                $arTemp['cod_evento']       = $arEventoBase['cod_evento_base'];
                $arTemp['cod_caso']         = $arEventoBase['cod_caso_base'];
                $arTemp['cod_configuracao'] = $arEventoBase['cod_configuracao_base'];
                $arTemp['timestamp']        = $arEventoBase['timestamp_base'];
                $arTemp['codigo']           = $arEventoBase['codigo'];
                $arTemp['descricao']        = $arEventoBase['descricao'];
                $arEventosBaseTemp[]        = $arTemp;
                $inIdEventoBase++;
            }
            $rsEventosBase = new recordset;
            $rsEventosBase->preenche($arEventosBaseTemp);

            #sessao->transf['Caso'.$stAba][] = array (
            $arConfiguracao[] = array (
                'inId'            => $rsConfiguracaoEventoCaso->getCorrente() ,
                'inCodFuncao'     => $inCodFuncao ,
                'stFuncao'        => $stFuncao ,
                'stDescricaoCaso' => $stDescricaoCaso ,
                'boConsProporcaoAdiantamento' => $boConsProporcaoAdiantamento,
                'boProporcionalizarAbono' => $boProporcionalizarAbono,
                'inCodigoTipoMedia'=> $inCodigoTipoMedia,
                'arSubDivisao'    => $arSubDivisao ,
                'arCargo'         => $arCargo,
                'eventosBase'.$stAba  => $rsEventosBase
                );
            $rsConfiguracaoEventoCaso->proximo();
        }
        Sessao::write('Caso'.$stAba,$arConfiguracao);
        $rsConfiguracaoEvento->proximo();
    }
    //$js.= "BloqueiaFrames(true,false); \n";
    $js.= "buscaValor('montaAlteracao'); \n";
}

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//Define campos hidden do Evento
$obHdnCodEvento = new Hidden;
$obHdnCodEvento->setName  ( "inCodEvento"        );
$obHdnCodEvento->setValue ( $_REQUEST['inCodEvento'] );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName  ( "stTimestamp"        );
$obHdnTimestamp->setValue ( $_REQUEST['stTimestamp'] );

//Campo hidden com a natureza
//Usado na alteracao de evento, para desbloquear(ou não) as abas no oculto
$obHdnNatureza = new Hidden;
$obHdnNatureza->setName  ( "natureza"  );
$obHdnNatureza->setValue ( $stNatureza );

//Inclui abas
include_once 'FMManterEventoAbaIdentificacao.php';
include_once 'FMManterEventoAbaSalario.php';
include_once 'FMManterEventoAbaFerias.php';
include_once 'FMManterEventoAba13o.php';
include_once 'FMManterEventoAbaRescisao.php';
include_once 'FMManterEventoAbaAtributosDinamicos.php';

SistemaLegado::executaFramePrincipal($js);

//Define formulário com abas
$obFormulario = new FormularioAbas;
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addForm( $obForm );

$obFormulario->addHidden             ( $obHdnCtrl                );
$obFormulario->addHidden             ( $obHdnAcao                );
$obFormulario->addHidden             ( $obHdnCodEvento           );
$obFormulario->addHidden             ( $obHdnTimestamp           );
//Aba Identificacao
$obFormulario->addAba                ( "Identificação"    );
$obFormulario->addTitulo             ( "Dados do evento"  );
$obFormulario->addComponente         ( $obTxtCodEventoIde );
$obFormulario->addComponente         ( $obTxtDescricaoIde );
if ($stAcao == 'incluir') {
    $obFormulario->agrupaComponentes (array($obRdbNaturezaProventoIde,$obRdbNaturezaDescontoIde,$obRdbNaturezaInformativoIde,$obRdbNaturezaBaseIde));
    $obFormulario->addSpan           ( $obSpnContraChequeNatureza );
    $obFormulario->addHidden         ( $obHdnNatureza     );
    $obFormulario->addSpan           ( $obSpnBase         );
    $obFormulario->addSpan           ( $obSpnTipoVariavel );
} else {
    $obFormulario->addHidden         ( $obHdnCodigoEvento );
    $obFormulario->addHidden         ( $obHdnCodVerbaRescisoriaMTE );
    $obFormulario->addSpan           ( $obSpnContraChequeNatureza );
    $obFormulario->addComponente     ( $obLblNaturezaIde );
    $obFormulario->addHidden         ( $obHdnNatureza    );
    if ($stNatureza != 'B') {
        $obFormulario->addComponente ( $obLblTipoIde                );
        $obFormulario->addComponente ( $obLblFixarIde               );
        $obFormulario->addComponente ( $obTxtValorIde               );
        $obFormulario->addComponente ( $obTxtUnidadeQuantitativaIde );
        $obFormulario->addComponente ( $obLblLimiteIde              );
        $obFormulario->addComponente ( $obLblParcelaIde             );
    }
}
$obFormulario->addComponente         ( $obTxtTextoComplementarIde                           );

if ($stAcao == 'incluir') {
    $obFormulario->addComponenteComposto ( $obRdbEventoAutomaticoSim, $obRdbEventoAutomaticoNao );
} else {
    $obFormulario->addComponente ( $obLblEventoAutomatico );
}
$obFormulario->addSpan           ( $obSpnVerbaRescisoriaMTE );
$obFormulario->addComponente($obTxtSigla);

$obFormulario->addTitulo             ( "Sequência de Cálculo"        );
$obFormulario->addComponente         ( $obCmbSequenciaNumeroIde      );
$obFormulario->addComponente         ( $obLblSequenciaDescricaoIde   );
$obFormulario->addComponente         ( $obLblSequenciaComplementoIde );

//Aba Salario
$obFormulario->addAba                ( "Salário" );
$obFormulario->addTitulo             ( "Elemento de despesa" );
$obBscRubricaDespesaSal->obCampoCod->setMascara ( $obROrcamentoClassificacaoDespesa->recuperaMascara() );
$obFormulario->addComponente         ( $obBscRubricaDespesaSal );

$obFormulario->addTitulo             ( "Particularidades para evento de salário" );

$obFormulario->addComponente         ( $obTxtDescricaoSal );
$obFormulario->addSpan               ( $obSpnSpan1 );
$obFormulario->addComponente         ( $obBscFuncaoSal );
$obFormulario->addSpan               ( $obSpnEventoBaseSal );
$obFormulario->defineBarraAba        ( array( $obBtnIncluirSal, $obBtnAlterarSal, $obBtnLimparSal) , '' , '' );

$obFormulario->addSpan               ( $obSpnListaSal , 2 );
$obFormulario->addHidden             ( $obHdnSal );

//Aba Ferias
$obFormulario->addAba                ( "Férias" );
$obFormulario->addTitulo             ( "Elemento de despesa" );

$obBscRubricaDespesaFer->obCampoCod->setMascara( $obROrcamentoClassificacaoDespesa->recuperaMascara());
$obFormulario->addComponente         ( $obBscRubricaDespesaFer );
$obFormulario->addTitulo             ( "Particularidades para evento de férias" );
$obFormulario->addComponente         ( $obTxtDescricaoFer );
$obFormulario->addComponenteComposto ( $obTxtTipoMedia,$obCmbTipoMedia );
$obFormulario->addComponente         ( $obLblObservacao );
$obFormulario->addComponente         ( $obChkProporcionalizarAbono );
$obFormulario->addSpan               ( $obSpnSpan2 );
$obFormulario->addComponente         ( $obBscFuncaoFer );
$obFormulario->addSpan               ( $obSpnEventoBaseFer );
$obFormulario->defineBarraAba        ( array( $obBtnIncluirFer, $obBtnAlterarFer, $obBtnLimparFer) , '' , '' );
$obFormulario->addSpan               ( $obSpnListaFer , 2 );
$obFormulario->addHidden             ( $obHdnFer );

//Aba 13o Salario
$obFormulario->addAba                ( "13o Salário" );
$obFormulario->addTitulo             ( "Elemento de despesa" );

$obBscRubricaDespesa13o->obCampoCod->setMascara  ( $obROrcamentoClassificacaoDespesa->recuperaMascara() );
$obFormulario->addComponente         ( $obBscRubricaDespesa13o );
$obFormulario->addTitulo             ( "Particularidades para evento de 13o salário" );
$obFormulario->addComponente         ( $obTxtDescricao13o );
$obFormulario->addComponenteComposto ( $obTxtTipoMedia13o,$obCmbTipoMedia13o );
$obFormulario->addComponente         ( $obLblObservacao13o );
$obFormulario->addComponente($obCkbConsPorporcaoAdiantamento);
$obFormulario->addSpan               ( $obSpnSpan3 );
$obFormulario->addComponente         ( $obBscFuncao13o );
$obFormulario->addSpan               ( $obSpnEventoBase13o );
$obFormulario->defineBarraAba        ( array( $obBtnIncluir13o, $obBtnAlterar13o, $obBtnLimpar13o) , '' , '' );
$obFormulario->addSpan               ( $obSpnLista13o , 2 );
$obFormulario->addHidden             ( $obHdn13o );

//Aba Rescisao
$obFormulario->addAba                ( "Rescisão" );
$obFormulario->addTitulo             ( "Elemento de despesa" );

$obBscRubricaDespesaRes->obCampoCod->setMascara( $obROrcamentoClassificacaoDespesa->recuperaMascara());
$obFormulario->addComponente         ( $obBscRubricaDespesaRes );
$obFormulario->addTitulo             ( "Particularidades para evento de rescisão" );
$obFormulario->addComponente         ( $obTxtDescricaoRes );
$obFormulario->addComponenteComposto ( $obTxtTipoMediaRes,$obCmbTipoMediaRes );
$obFormulario->addComponente         ( $obLblObservacaoRes );
$obFormulario->addSpan               ( $obSpnSpan4 );
$obFormulario->addComponente         ( $obBscFuncaoRes );
$obFormulario->addSpan               ( $obSpnEventoBaseRes );
$obFormulario->defineBarraAba        ( array( $obBtnIncluirRes, $obBtnAlterarRes, $obBtnLimparRes) , '' , '' );
$obFormulario->addSpan               ( $obSpnListaRes , 2 );
$obFormulario->addHidden             ( $obHdnRes );

//Aba AtriBUTOS Dinamicos
$obFormulario->addAba                ( "Atributos Dinâmicos" );
$obFormulario->addTitulo             ( "Informações de atributos dinâmicos" );
$obMontaAtributos->geraFormulario    ( $obFormulario );

if ($stAcao == 'incluir') {
    $obFormulario->OK();
    $obFormulario->setFormFocus( $obTxtCodEventoIde->getId() );
} else {
    Sessao::write('stCodigo',$_GET['stCodigo']);
    Sessao::write('stDescricao',$_GET['stDescricao']);
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao );
}

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
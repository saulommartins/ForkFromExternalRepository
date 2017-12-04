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
    * Página de Frame Oculto para Consulta de Arrecadacao
    * Data de Criação   : 26/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    * $Id: OCManterBaixaManual.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.24  2007/07/31 13:30:46  cercato
Bug#9784#

Revision 1.23  2007/07/27 13:16:08  cercato
Bug#9762#

Revision 1.22  2007/07/25 15:23:35  fabio
Bug#9578#

Revision 1.21  2007/07/16 21:10:27  cercato
Bug #9668#

Revision 1.20  2007/04/11 13:31:41  dibueno
Bug #8965#

Revision 1.19  2007/03/12 19:30:22  cercato
adicionada opcao para baixa da carne da divida.

Revision 1.18  2007/02/16 11:31:53  dibueno
Bug #8432#

Revision 1.17  2006/11/17 11:49:43  cercato
bug #7357#

Revision 1.16  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"  );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"  );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDivida.class.php" );
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpCobranca.class.php" );

function montaListaRegistroPagamento($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $rsLista->addFormatacao("valor_parcela","NUMERIC_BR");
        $rsLista->addFormatacao("valor_pago","NUMERIC_BR");
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista  );
        $obLista->setTitulo ( "Registros de Pagamento" );
        $obLista->setMostraPaginacao ( false );
        $obLista->setTotaliza ( "valor_pago, Total, right, 7"  );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Número do Carnê");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Contribuinte");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vencimento");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Pagamento");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Valor Pago");
        $obLista->ultimoCabecalho->setWidth( 8 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "numeracao" );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO"                  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "[numcgm] - [nom_cgm]"     );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO"                  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "vencimento_parcela" );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO"             );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "valor_parcela" );
        $obLista->ultimoDado->setAlinhamento    ( "DIREITA" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "data_pagamento" );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO"  );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "valor_pago" );
        $obLista->ultimoDado->setAlinhamento    ( "DIREITA"       );
        $obLista->commitDado();

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n";

    sistemaLegado::executaFrameOculto($js);
}

function BuscarCredito($stParam1, $stParam2)
{
    $obRegra = new RARRGrupo;

    if ($_REQUEST[$stParam1]) {
        $arDados = explode("/", $_REQUEST[$stParam1]);
        $stMascara = "";
        $obRARRGrupo = new RARRGrupo;
        $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascara );
        $stMascara .= "/9999";

        if ( strlen($_REQUEST[$stParam1]) < strlen($stMascara) ) {
            $stJs = 'f.'.$stParam1.'.value= "";';
            $stJs .= 'f.'.$stParam1.'.focus();';
            $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
            $stJs .= "alertaAviso('@Código Grupo/Ano exercício incompleto. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
        } else {
            $obRARRGrupo->setCodGrupo( $arDados[0] );
            $obRARRGrupo->setExercicio( $arDados[1] );

            $obRARRGrupo->listarGrupos( $rsListaGrupo );
            if ( $rsListaGrupo->Eof() ) {
                $stJs = 'f.'.$stParam1.'.value= "";';
                $stJs .= 'f.'.$stParam1.'.focus();';
                $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@Código Grupo/Ano exercício inválido. (".$_REQUEST[$stParam1].")', 'form','erro','".Sessao::getId()."');";
            } else {
                $stJs = 'd.getElementById("'.$stParam2.'").innerHTML = "'.$rsListaGrupo->getCampo("descricao").'";';
            }
        }
    } else {
        $stJs = 'f.inCodGrupo.value= "";';
        $stJs .= 'd.getElementById("'.$stParam2.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function AtualizaValores()
{
    $obRARRCarne = new RARRCarne;
    $arTipo = explode( "-", $_REQUEST['stTipo'] );
    if ($arTipo[1] == 't') {
        #echo '<br>b';
        $arDataBase = explode( "/" , $_REQUEST['dtPagamento'] );
        $dtDataBase = $arDataBase[2]."-".$arDataBase[1]."-".$arDataBase[0];

        $obRARRCarne->setNumeracao( $_REQUEST['inNumeracao'] );
        $obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $_REQUEST['inCodLancamento'] );

        $nuTotalJuros = 0;
        $nuTotalMulta = 0;
        $nuTotalCorrecao = 0;

        if ( Sessao::read( 'consultadivida' ) ) {
            $obErro = $obRARRCarne->listarDetalheCreditosBaixaDivida( $rsConsultaCredito, $boTransacao, $dtDataBase );
            while ( !$rsConsultaCredito->eof() ) {
                $nuTotalJuros += $rsConsultaCredito->getCampo('juros');
                $nuTotalMulta += $rsConsultaCredito->getCampo('multa');
                $nuTotalCorrecao += $rsConsultaCredito->getCampo('correcao');
                $rsConsultaCredito->proximo();
            }
        } else {
            $obErro = $obRARRCarne->listarDetalheCreditosBaixa( $rsConsultaCredito, $boTransacao, $dtDataBase );
            while ( !$rsConsultaCredito->eof() ) {
                $nuTotalJuros += $rsConsultaCredito->getCampo('valor_credito_juros');
                $nuTotalMulta += $rsConsultaCredito->getCampo('valor_credito_multa');
                $nuTotalCorrecao += $rsConsultaCredito->getCampo('valor_credito_correcao');
                $rsConsultaCredito->proximo();
            }
        }

        $nuValorCorrigido = round( ( $_REQUEST['nuValorOriginal'] + $nuTotalJuros + $nuTotalMulta + $nuTotalCorrecao) , 2 );
        $stJs .= "f.nuValorTotal.value = '".$nuValorCorrigido."';\n";

        $nuTotalCorrecao = number_format ( $nuTotalCorrecao, 2, ',', '.' );
        $nuTotalJuros = number_format ( $nuTotalJuros, 2, ',','.' );
        $nuTotalMulta = number_format ( $nuTotalMulta, 2, ',','.' );
        $nuValorCorrigido = number_format ( $nuValorCorrigido, 2, ',','.' );

        $stJs .= "d.getElementById('Juro').innerHTML           = 'R$ ".$nuTotalJuros."';\n";
        $stJs .= "d.getElementById('Multa').innerHTML          = 'R$ ".$nuTotalMulta."';\n";
        $stJs .= "d.getElementById('Correcao').innerHTML       = 'R$ ".$nuTotalCorrecao."';\n";
        $stJs .= "d.getElementById('ValorCorrigido').innerHTML = 'R$ ".$nuValorCorrigido."';\n";
    } else {
        $stJs .= "d.getElementById('Juro').innerHTML            = 'R$ 0,00';\n";
        $stJs .= "d.getElementById('Multa').innerHTML          = 'R$ 0,00';\n";
        $stJs .= "d.getElementById('Correcao').innerHTML          = 'R$ 0,00';\n";
        $stJs .= "d.getElementById('ValorCorrigido').innerHTML = 'R$ 0,00';\n";
        $stJs .= "f.nuValorTotal.value = '0.00';\n";
    }

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "BuscaCodCredito":
        $stJs = BuscarCredito( "inCodGrupo", "stGrupo" );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "BancoAgenciaLista":
        $rsLista = new RecordSet();
        if ($_REQUEST['inNumbanco'] && $_REQUEST['inNumAgencia']) {
            $obRMONAgencia = new RMONAgencia;
            $obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumbanco'] );
            $obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
            $obRMONAgencia->consultarAgencia( $rsListaAgencias );
            if ( !$rsListaAgencias->eof() ) {
                $obRARRPagamento = new RARRPagamento;
                $obRARRPagamento->obRMONAgencia->setCodAgencia( $obRMONAgencia->getCodAgencia() );
                $obRARRPagamento->obRMONBanco->setCodBanco ( $obRMONAgencia->obRMONBanco->getCodBanco() );
                $obRARRPagamento->listarPagamentosManuais( $rsLista );
            }
        }

        $dtDiaHoje = date ("d-m-Y");

        $obLblBanco = new Label;
        $obLblBanco->setName   ( 'labelBanco' );
        $obLblBanco->setTitle  ( 'Banco.' );
        $obLblBanco->setRotulo ( 'Banco' );
        $obLblBanco->setValue  ( $obRMONAgencia->obRMONBanco->getNumBanco() . ' - ' . $obRMONAgencia->obRMONBanco->getNomBanco() );

        $obLblAgencia = new Label;
        $obLblAgencia->setName   ( 'labelAgencia' );
        $obLblAgencia->setTitle  ( 'Agência.' );
        $obLblAgencia->setRotulo ( 'Agência' );
        $obLblAgencia->setValue  ( $obRMONAgencia->getNumAgencia() . ' - ' . $obRMONAgencia->getNomAgencia() );

        $obLblData = new Label;
        $obLblData->setName   ( 'labelData' );
        $obLblData->setTitle  ( 'Data de fechamento.' );
        $obLblData->setRotulo ( 'Data de Fechamento' );
        $obLblData->setValue  ( $dtDiaHoje );

        $obFormulario = new Formulario;
        $obFormulario->addComponente ( $obLblBanco );
        $obFormulario->addComponente ( $obLblAgencia );
        $obFormulario->addComponente ( $obLblData );
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnListaBanco').innerHTML = '".$obFormulario->getHtml()."';\n";
        sistemaLegado::executaFrameOculto($js);

        montaListaRegistroPagamento( $rsLista, $obFormulario->getHTML() );
        break;

    case "AgenciaLista":
        $rsLista = new RecordSet();
        if ($_REQUEST['inNumbanco'] && $_REQUEST['inNumAgencia']) {
            $obRMONAgencia = new RMONAgencia;
            $obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumbanco'] );
            $obRMONAgencia->setNumAgencia( $_REQUEST['inNumAgencia'] );
            $obRMONAgencia->consultarAgencia( $rsListaAgencias );
            if ( !$rsListaAgencias->eof() ) {
                $obRARRPagamento = new RARRPagamento;
                $obRARRPagamento->obRMONAgencia->setCodAgencia( $obRMONAgencia->getCodAgencia() );
                $obRARRPagamento->obRMONBanco->setCodBanco ( $obRMONAgencia->obRMONBanco->getCodBanco() );
                $obRARRPagamento->listarPagamentosManuais( $rsLista );
            }
        }

        montaListaRegistroPagamento( $rsLista );
        break;

    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();
            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_POST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaContribuinte":
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inCodContribuinte' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );
            $obRCGM->consultar( $rsCGM );
            $stNull = "";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inNumCGM.value = "";';
                $stJs .= 'f.inNumCGM.focus();';
                $stJs .= 'd.getElementById("innerCGM").innerHTML = "'.$stNull.'";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST['inCodContribuinte'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "buscaIE":
        if ($_REQUEST["inInscricaoEconomica"]) {
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( !$rsInscricao->eof()) {
                $js .= "f.inInscricaoEconomica.value = '".$_REQUEST["inInscricaoEconomica"]."';\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '".$rsInscricao->getCampo("nom_cgm")."' ;\n";
            } else {
                $stMsg = "Inscrição Econômica ".$_REQUEST["inInscricaoEconomica"]."  não encontrada!";
                $js = "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '';\n";
        }
        SistemaLegado::executaFrameOculto($js);
        break;

    case "referencia":

        $obTxtExercicio = new TextBox;
        $obTxtExercicio->setName             ( "inExercicio"    );
        $obTxtExercicio->setId               ( "inExercicio"    );
        $obTxtExercicio->setRotulo           ( "Exercício"      );
        $obTxtExercicio->setTitle            ( "Exercício."      );
        $obTxtExercicio->setNull             ( false            );
        $obTxtExercicio->setSize             ( 4                );
        $obTxtExercicio->setMaxLength        ( 4                );
        $obTxtExercicio->setInteiro          ( true );

        $obFormulario = new Formulario;
        switch ($_REQUEST["stReferencia"]) {
            case "da":

                  $obIPopUpCobranca = new IPopUpCobranca;
                  $obIPopUpCobranca->obInnerCobranca->setNull ( false );
                  $obIPopUpCobranca->geraFormulario( $obFormulario );
                  $obTxtExercicio->setNull ( true );

                break;

            case "cgm":
                $obBscContribuinte = new BuscaInner;
                $obBscContribuinte->setId               ( "stContribuinte"          );
                $obBscContribuinte->setRotulo           ( "Contribuinte"            );
                $obBscContribuinte->setTitle            ( "Codigo do contribuinte."  );
                $obBscContribuinte->setNull             ( false                     );
                $obBscContribuinte->obCampoCod->setName ("inCodContribuinte"        );
                $obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinte');");
                $obBscContribuinte->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinte','stContribuinte','','".Sessao::getId()."','800','450');" );
                $obFormulario->addComponente ( $obBscContribuinte );
            break;
            case "ii":
                $obBscInscricaoMunicipal = new BuscaInner;
                $obBscInscricaoMunicipal->setNull                  ( true                         );
                $obBscInscricaoMunicipal->setRotulo                ( "Inscrição Imobiliária"      );
                $obBscInscricaoMunicipal->obCampoCod->setName      ( "inInscricaoImobiliaria"     );
                $obBscInscricaoMunicipal->setNull                  ( false                        );
                $obBscInscricaoMunicipal->obCampoCod->setMaxLength ( strlen($stMascaraInscricao)  );
                $obBscInscricaoMunicipal->obCampoCod->setMascara   ( $stMascaraInscricao          );
                $obBscInscricaoMunicipal->obCampoCod->setInteiro   ( false                        );
                $obBscInscricaoMunicipal->setFuncaoBusca( "abrePopUp( '".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php', 'frm', 'inInscricaoImobiliaria', 'stCampo', 'todos', '".Sessao::getId()."', '800', '550' );" );
                $obFormulario->addComponente ( $obBscInscricaoMunicipal );
            break;
            case "ie":
                $obBscInscricaoEconomica = new BuscaInner;
                $obBscInscricaoEconomica->setId                   ( "stInscricaoEconomica"  );
                $obBscInscricaoEconomica->setRotulo               ( "Inscrição Econômica"   );
                $obBscInscricaoEconomica->setTitle                ( "Pessoa física ou jurídica cadastrada como inscrição econômica.");
                $obBscInscricaoEconomica->obCampoCod->setName     ( "inInscricaoEconomica"  );
                $obBscInscricaoEconomica->setNull                 ( false                   );
                $obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen($stMascaraInscricaoEconomico ));
                $obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricao         );
                $obBscInscricaoEconomica->obCampoCod->obEvento->setOnChange("buscaValor('buscaIE');");
                $obBscInscricaoEconomica->setFuncaoBusca          ( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inInscricaoEconomica','stInscricaoEconomica','todos','".Sessao::getId()."','800','550');" );
                $obFormulario->addComponente ( $obBscInscricaoEconomica );
            break;
        }

        $obFormulario->addComponente ( $obTxtExercicio );

        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stHtml = str_replace("\'", "'", $stHtml);

        echo ($stHtml);

    break;

    case "buscaCredito":

        $obRARRGrupo = new RARRGrupo;
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $obRARRGrupo->obRMONCredito->setCodCredito  ($arValores[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);
        // VERIFICAR PERMISSAO
        //$obRARRGrupo->obRMONCredito->consultarCreditoPermissao();
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
            $stJs .= "f.inCodigoCredito.value ='".$inCodCredito."';\n";
            //$stJs .= "d.getElementById('inCodigoCredito').innerHTML = '".$inCodCredito."';\n";
            if ( $stAcao == 'incluir')
                $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }

    break;
     case "buscaGrupo":
        $obRARRGrupo = new RARRGrupo;

        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->consultarGrupo();

        $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
        $stDescricao     = $obRARRGrupo->getDescricao() ;
        $inCodModulo   = $obRARRGrupo->getCodModulo() ;
        $stExercicio      = $obRARRGrupo->getExercicio() ;
        if ( !empty($stDescricao) ) {
            $stJs .= "f.inCodGrupo.value = '".$inCodGrupo."';\n";
            $stJs .= "f.inCodModuloGrupo.value = '".$inCodModulo."';\n";
            //$stJs .= "f.stExercicio.value = '".$stExercicio."';\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML= '". $stDescricao ."';\n";
        } else {
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "f.inCodModuloGrupo.value ='';\n";
            $stJs .= "f.stExercicioGrupo.value ='';\n";
            $stJs .= "f.inCodGrupo.focus();\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "preencheAgencia":
        $obRMONAgencia = new RMONAgencia;
        $js .= "f.inNumAgencia.value=''; \n";
        $js .= "limpaSelect(f.cmbAgencia,1); \n";
        $js .= "f.cmbAgencia[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST['inNumbanco']) {
            $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inNumbanco"] );
            $obRMONAgencia->listarAgencia( $rsAgencia );

            $inContador = 1;
            while ( !$rsAgencia->eof() ) {
                $inNumAgencia = $rsAgencia->getCampo( "num_agencia" );
                $stNomAgencia = $rsAgencia->getCampo( "nom_agencia" );
                $js .= "f.cmbAgencia.options[$inContador] = new Option('".$stNomAgencia."','".$inNumAgencia."'); \n";
                $inContador++;
                $rsAgencia->proximo();
            }
        }
        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inNumAgencia.value='".$_REQUEST["inNumAgencia"]."'; \n";
            $js .= "f.cmbAgencia.options[".$_REQUEST["inNumAgencia"]."].selected = true; \n";
        }
        echo $js;
        break;

    case "atualizaValorCorrigido":

        if (!$_REQUEST['stTipo']) {
            //se não há tipo de pagamento já selecionado, ele lista os tipos possíveis

            $obRARRTipoPagamento = new RARRTipoPagamento;
            $obRARRTipoPagamento->setSistema( TRUE );
            $arDataPagamento = explode( "/", $_REQUEST["dtPagamento"] );
            $arDataVencimento = explode( "/", $_REQUEST["dtVencimento"] );
            include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPermissaoCancelamento.class.php" );
            $obTARRPermissaoCancelamento = new TARRPermissaoCancelamento;
            $obTARRPermissaoCancelamento->recuperaListaCGMs( $rsListaCGM, "" );
            $boEstaNaLista = false;
            while ( !$rsListaCGM->Eof() ) {
                if ( $rsListaCGM->getCampo("numcgm") == Sessao::read('numCgm') ) {
                    $boEstaNaLista = true;
                    break;
                }

                $rsListaCGM->proximo();
            }

            $boListar = true;
            if ($_REQUEST["inNrParcela"] == 0 && $_REQUEST['boValida'] == 'f') {
                if ($arDataPagamento[2].$arDataPagamento[1].$arDataPagamento[0] > $arDataVencimento[2].$arDataVencimento[1].$arDataVencimento[0]) {
                    if ($boEstaNaLista) {
                        $obRARRTipoPagamento->setPagamento ( 'f' );
                    } else {
                        $boListar = false;
                    }
                }else
                    if ( !$boEstaNaLista )
                        $obRARRTipoPagamento->setPagamento ( 't' );
            }else
                if ( !$boEstaNaLista )
                    $obRARRTipoPagamento->setPagamento ( 't' );

            $stJs = "";
            if ( $boListar )
                $obRARRTipoPagamento->listarTipoPagamento( $rsTipoPagamento );
            else
                $rsTipoPagamento = new RecordSet;

            if ($rsTipoPagamento) {
                $stJs .= "limpaSelect(f.stTipo,1); \n";
                $stJs .= "f.stTipo[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsTipoPagamento->eof() ) {
                    $stJs .= "f.stTipo.options[$inContador] = new Option('".$rsTipoPagamento->getCampo("nom_tipo")."','".$rsTipoPagamento->getCampo("cod_tipo")."-".$rsTipoPagamento->getCampo("pagamento")."'); \n";

                    $rsTipoPagamento->proximo();
                    $inContador++;
                }

            }
        } else {
            $boListar = true;
        }

        if ($boListar) {
            $stJs .= AtualizaValores();
        }

        echo $stJs;
    break;

    case "buscaUtilizacaoTipoPagamento":
        $arTipo = explode( "-", $_REQUEST['stTipo'] );
        if ($arTipo[1] == 't') {
            #echo '<br>a';
            $stUtilzacao = "Pagamento";

            $obTxtValor = new TextBox;
            $obTxtValor->setName         ( "nuValorPagamento"  );
            $obTxtValor->setRotulo       ( "Valor"             );
            $obTxtValor->setMaxLength    ( 10                  );
            $obTxtValor->setSize         ( 10                  );
            $obTxtValor->setValue        ( $nuValorPagamento   );
            $obTxtValor->setNull         ( false               );
            $obTxtValor->setFloat        ( true                );

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtValor  );
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $stJs .= "d.getElementById(\"spnValor\").innerHTML = '".$stHtml."'; ";
        } elseif ($arTipo[1] == 'f') {
            $stUtilzacao = "Cancelamento";
            $stJs .= "d.getElementById(\"spnValor\").innerHTML = ''; ";
        } else {
            $stUtilzacao = "&nbsp;";
            $stJs .= "d.getElementById(\"spnValor\").innerHTML = ''; ";
        }

        $stJs .= "d.getElementById('stLblTipo').innerHTML = '".$stUtilzacao."';\n";
        $stJs .= AtualizaValores ();
        echo $stJs;
        break;

}
//sistemaLegado::executaFrameOculto ($stJs);
?>

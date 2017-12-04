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
* Página Oculta de FolhaPagamentoPrevidencia
* Data de Criação   : 29/11/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Rafael Almeida

* @ignore

$Revision: 30840 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"    );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra  = new RFolhaPagamentoPrevidencia;
$rsFaixas = new RecordSet;

function listarFaixas($arRecordSet, $boExecuta=true)
{
    global $obRegra;
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Faixas cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Inicial" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Final" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Percentual Desconto" );
        $obLista->ultimoCabecalho->setWidth( 80 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flSalarioInicial" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flSalarioFinal" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flPercentualDesc" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnFaixas').innerHTML = '".$stHtml."';";
    $stJs .= "f.flSalarioInicial.value = '';";
    $stJs .= "f.flSalarioFinal.value = '';";
    $stJs .= "f.flPercentualDesc.value = '';";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheEventos($boExecuta=false)
{
    $obRFolhaPagamentoPrevidencia = new RFolhaPagamentoPrevidencia;
    $obRFolhaPagamentoPrevidencia->setCodPrevidencia( $_REQUEST['inCodPrevidencia'] );
    $obRFolhaPagamentoPrevidencia->listarPrevidenciaEvento($rsPrevidenciaEvento);
    while ( !$rsPrevidenciaEvento->eof() ) {
        $stInner          = "inCampoInnerPrev".$rsPrevidenciaEvento->getCampo('cod_tipo');
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsPrevidenciaEvento->getCampo('descricao')."';";
        $rsPrevidenciaEvento->proximo();
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

/*
Função  : buscaEvento
Objetivo: buscar um evento pelo código e se encontra-lo mostrar seu nome no text do BuscaInner
Autor   : Bruce
Data    : 22/02/2005
Retorno : JavaScript
*/
function buscaEvento($boExecuta = true)
{
    // pegando o codigo digitado pelo usuário
    $codigo = $_POST['inCodigoPrev'.$_GET['inCodTipo']];
    $rsLista = new RecordSet;
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodigo( $codigo  );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento( $rsLista );

    // alterando o valor do TextBox do Inner
    $stInner = "inCampoInnerPrev".$_GET['inCodTipo'];
    if ( $rsLista->getNumLinhas() > 0 ) {
        $stJs .= "f.inCodigoPrev".$_GET['inCodTipo'].".value = '".$rsLista->getCampo('codigo')."';             \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsLista->getCampo('descricao')."';  \n";
    } else {
        $stJs .= "f.inCodigoPrev". $_GET['inCodTipo'].".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }

/*

    $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsLista->getCampo('descricao')."';  \n";

    SistemaLegado::executaFrameOculto($stJs);
*/
}//function buscaEvento() {

function preencherEvento($boExecuta=false)
{
    $inCodTipo = $_GET['inCodTipo'];
    $stNatureza= $_GET['stNatureza'];
    $obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
    $obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setCodigo( $_POST['inCodigoPrev'.$inCodTipo]  );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setNatureza( $stNatureza );
    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->setEventoSistema( "true" );

    $obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEvento( $rsEvento );
    $stInner = "inCampoInnerPrev".$inCodTipo;
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $stJs .= "f.inCodigoPrev".$inCodTipo.".value = '".$rsEvento->getCampo('codigo')."';             \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
    } else {
        $stJs .= "f.inCodigoPrev".$inCodTipo.".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaAliquotas()
{
    $stHtml = "";
    $inCodRegimePrevidencia = $_POST['inCodRegimePrevidencia'] ? $_POST['inCodRegimePrevidencia'] : Sessao::read('inCodRegimePrevidencia') ;
    if ($inCodRegimePrevidencia == 1 OR $inCodRegimePrevidencia == "") {
        $obTxtAliquotaRat = new Moeda;
        $obTxtAliquotaRat->setRotulo     ( "Alíquota do RAT" );
        $obTxtAliquotaRat->setName       ( "flAliquotaRat" );
        $obTxtAliquotaRat->setValue      ( Sessao::read('flAliquotaRat')  );
        $obTxtAliquotaRat->setTitle      ( "Informe o valor da alíquota do RAT." );
        $obTxtAliquotaRat->setNull       ( false );
        $obTxtAliquotaRat->setMaxLength  ( 6     );
        $obTxtAliquotaRat->obEvento->setOnChange("buscaValor('validaRat');");

        $obTxtAliquotaFap = new Moeda;
        $obTxtAliquotaFap->setRotulo     ( "Alíquota do FAP" );
        $obTxtAliquotaFap->setName       ( "flAliquotaFap" );
        $obTxtAliquotaFap->setValue      ( Sessao::read('flAliquotaFap')  );
        $obTxtAliquotaFap->setTitle      ( "Informe o valor da alíquota do Fap." );
        $obTxtAliquotaFap->setNull       ( false );
        $obTxtAliquotaFap->setMaxLength  ( 8     );
        $obTxtAliquotaFap->setDecimais   ( 4     );
        $obTxtAliquotaFap->obEvento->setOnChange("buscaValor('validaFap');");

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obTxtAliquotaRat );
        $obFormulario->addComponente( $obTxtAliquotaFap );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
    }

    $stJs .= "d.getElementById('spnAliquotas').innerHTML = '$stHtml';    \n";

    return $stJs ;
}

function gerarSpan1($boExecuta=false)
{
    $inCodRegimePrevidencia = $_POST['inCodRegimePrevidencia'] ? $_POST['inCodRegimePrevidencia'] : Sessao::read('inCodRegimePrevidencia') ;
    $stTipo                 = $_POST['stTipo']                 ? $_POST['stTipo']                 : Sessao::read('stTipo');

    if ($stTipo == 'o' or $stTipo == '') {
        $obSpnAliquotas = new Span();
        $obSpnAliquotas->setId("spnAliquotas");

        // $obTxtAliquotaRat = new Moeda;
        // $obTxtAliquotaRat->setRotulo     ( "Alíquota do RAT" );
        // $obTxtAliquotaRat->setName       ( "flAliquotaRat" );
        // $obTxtAliquotaRat->setValue      ( Sessao::read('flAliquotaRat')  );
        // $obTxtAliquotaRat->setTitle      ( "Informe o valor da alíquota do RAT." );
        // $obTxtAliquotaRat->setNull       ( false );
        // $obTxtAliquotaRat->setMaxLength  ( 6     );
        // $obTxtAliquotaRat->obEvento->setOnChange("buscaValor('validaRat');");

        $obRdoRegimePrevidenciario1 = new Radio;
        $obRdoRegimePrevidenciario1->setRotulo      ( "Regime Previdenciário"                                           );
        $obRdoRegimePrevidenciario1->setName        ( "inCodRegimePrevidencia"                                          );
        $obRdoRegimePrevidenciario1->setValue       ( 1                                                                 );
        $obRdoRegimePrevidenciario1->setLabel       ( "RGPS"                                                            );
        $obRdoRegimePrevidenciario1->setChecked     ( ($inCodRegimePrevidencia == 1 or !$inCodRegimePrevidencia)        );
        $obRdoRegimePrevidenciario1->setNull        ( false                                                             );
        if ($inCodRegimePrevidencia != "") {
            $obRdoRegimePrevidenciario1->setDisabled( true                                                              );
        }
        $obRdoRegimePrevidenciario1->obEvento->setOnChange( "buscaValor('montaAliquotas');"                                 );

        $obRdoRegimePrevidenciario2 = new Radio;
        $obRdoRegimePrevidenciario2->setRotulo      ( "Regime Previdenciário"                                           );
        $obRdoRegimePrevidenciario2->setName        ( "inCodRegimePrevidencia"                                          );
        $obRdoRegimePrevidenciario2->setValue       ( 2                                                                 );
        $obRdoRegimePrevidenciario2->setLabel       ( "RPPS"                                                            );
        $obRdoRegimePrevidenciario2->setChecked     ( ($inCodRegimePrevidencia == 2)                                    );
        $obRdoRegimePrevidenciario2->setNull        ( false                                                             );
        if ($inCodRegimePrevidencia != "") {
            $obRdoRegimePrevidenciario2->setDisabled( true                                                              );
        }
        $obRdoRegimePrevidenciario2->obEvento->setOnChange( "buscaValor('montaAliquotas');"                                 );

        $obFormulario = new Formulario;
        $obFormulario->addComponenteComposto( $obRdoRegimePrevidenciario1,$obRdoRegimePrevidenciario2);
        $obFormulario->addSpan($obSpnAliquotas);
        //$obFormulario->addComponente( $obTxtAliquotaRat );
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";
        $stJs .= montaAliquotas();
    } else {
        $obHdnRegime = new Hidden;
        $obHdnRegime->setName  ( 'inCodRegimePrevidencia' );
        $obHdnRegime->setValue ( 0 );

        $obFormulario = new Formulario;
        $obFormulario->addHidden($obHdnRegime);
        $obFormulario->montaInnerHtml();
        $stHtml = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function validaRat()
{
    if ( str_replace(',','',$_POST['flAliquotaRat']) > 10000 ) {
        $stJs .= "f.flAliquotaRat.value='';\n";
        $stJs .= "f.flAliquotaRat.focus();\n";
    }

    return $stJs;
}

function validaFap()
{
    if ( str_replace(',','',$_POST['flAliquotaFap']) > 1000000 ) {
        $stJs .= "f.flAliquotaFap.value='';\n";
        $stJs .= "f.flAliquotaFap.focus();\n";
    }

    return $stJs;
}

// Acoes por pagina
switch ($stCtrl) {

//monta HTML com os ATRIBUTOS relativos a Faixa de Desconto
    case "MontaFaixa":
        $stMensagem = false;
        $arElementos = array ();
        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( Sessao::read('Faixas') );
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if (!$inUltimoId) {
            $inProxId = 1;
        } else {
            $inProxId = $inUltimoId + 1;
        }

        $ultimoValorIncluido = $rsRecordSet->getCampo("flSalarioFinal");
        $ultimoValorIncluido = str_replace('.', '', $ultimoValorIncluido);
        $ultimoValorIncluido = str_replace(',', '', $ultimoValorIncluido);

        $ValorIncluido   = str_replace('.','',$_POST['flSalarioInicial']);
        $ValorIncluido   = str_replace(',','.',$ValorIncluido);
        $arValorIncluido = explode('.',$ValorIncluido);
                if ($arValorIncluido[1] == NULL) {
                   $ValorIncluido = $ValorIncluido.',00';
                }
        $ValorIncluido = number_format($ValorIncluido,2,',','.');
        $ValorIncluido = str_replace('.', '', $ValorIncluido);
        $ValorIncluido = str_replace(',', '', $ValorIncluido);

        $ultimoPercentualIncluido = $rsRecordSet->getCampo("flPercentualDesc");
        $ultimoPercentualIncluido = str_replace('.', '', $ultimoPercentualIncluido);
        $ultimoPercentualIncluido = str_replace(',', '', $ultimoPercentualIncluido);

        $PercentualIncluido = $_POST['flPercentualDesc'];
        $PercentualIncluido = str_replace('.', '', $PercentualIncluido);
        $PercentualIncluido = str_replace(',', '', $PercentualIncluido);

        if ( ((int) $ultimoPercentualIncluido >= (int) $PercentualIncluido) && (count (Sessao::read("Faixas")) > 0 )) {
                $stMensagem = "O valor referente ao percentual informado deve ser maior que o da última faixa cadastrada.";
                $stJs .= "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."');      \n";
        } else {
            if ( ($ultimoValorIncluido >= $ValorIncluido) && (count (Sessao::read("Faixas")) > 0 )) {
                $stMensagem = "O valor informado para o salário inicial deve ser maior que o da última faixa cadastrada do salário final.";
                $stJs .= "alertaAviso('@".$stMensagem."','form','erro','".Sessao::getId()."');      \n";
            } else {
                $arFaixas = Sessao::read("Faixas");
                $arElementos['inId']             = $inProxId;
                $flSalarioInicial = str_replace('.','',$_POST['flSalarioInicial']);
                $flSalarioInicial = str_replace(',','.',$flSalarioInicial);
                $arSalarioInicial = explode('.',$flSalarioInicial);
                if ($arSalarioInicial[1] == NULL) {
                   $flSalarioInicial = $flSalarioInicial.',00';
                }

                $flSalarioFinal = str_replace('.','',$_POST['flSalarioFinal']);
                $flSalarioFinal = str_replace(',','.',$flSalarioFinal);
                $arSalarioFinal = explode('.',$flSalarioFinal);
                if ($arSalarioFinal[1] == NULL) {
                   $flSalarioFinal = $flSalarioFinal.',00';
                }
                $arElementos['flSalarioInicial'] = number_format($flSalarioInicial,2,',','.');
                $arElementos['flSalarioFinal']   = number_format($flSalarioFinal,2,',','.');
                $arElementos['flPercentualDesc'] = $_POST['flPercentualDesc'];
                $arFaixas[]      = $arElementos;
                Sessao::write("Faixas",$arFaixas);
                listarFaixas( Sessao::read('Faixas') );
            }
        }

    break;

    case "excluiFaixa":
        $id = $_GET['inId'];
        $stMensagem = false;
        $arFaixas = Sessao::read("Faixas");
        if ($_REQUEST['stAcao']=='alterar') {
            reset($arFaixas);
            while ( list( $arId ) = each( $arFaixas ) ) {
                if ($arFaixas["inId"] == $id) {
                    $obRegra->setCodPrevidencia     ( $_POST['inCodPrevidencia'] );
                    $obRegra->obRFaixa->setCodFaixa ( $arFaixas["inCodFaixa"] );
                }
            }
        }

        if ($stMensagem==false) {
            reset($arFaixas);
            while ( list( $arId ) = each($arFaixas) ) {
                if ($arFaixas[$arId]["inId"] != $id) {
                    $arElementos['inId']           = $arFaixas[$arId]["inId"];
                    $arElementos['flSalarioInicial']   = $arFaixas[$arId]["flSalarioInicial"];
                    $arElementos['flSalarioFinal']     = $arFaixas[$arId]["flSalarioFinal"];
                    $arElementos['flPercentualDesc']   = $arFaixas[$arId]["flPercentualDesc"];
                    $arTMP[] = $arElementos;
                }
            }
            Sessao::write('Faixas',$arTMP);
            listarFaixas( $arTMP );
        } else {
            $stJs = "SistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;
    case 'preencheInner':
        if ( count( Sessao::read('Faixas') ) ) {
            $stJs  = listarFaixas( Sessao::read('Faixas'), false );
        }
        $stJs .= preencheEventos();
    break;

    case 'preencherEvento':
        $stJs .= preencherEvento();
    break;
    case 'gerarSpan1':
        $stJs .= gerarSpan1();
    break;
    case 'validaRat':
        $stJs .= validaRat();
    break;
    case 'validaFap':
        $stJs .= validaFap();
    break;
    case 'montaAliquotas':
        $stJs .= montaAliquotas();
    break;
}

if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>

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
    * Oculto de TabelaIRRF
    * Data de Criação   : 05/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name:  $
    $Author: alex $
    $Date: 2007-11-14 11:18:50 -0200 (Qua, 14 Nov 2007) $

    * Casos de uso: uc-04.05.23
*/

/*
$Log: OCManterTabelaIRRF.php,v $
Revision 1.7  2007/06/05 19:56:43  souzadl
Bug #9309#

Revision 1.6  2006/09/04 17:34:11  souzadl
Bug #6825#

Revision 1.5  2006/08/08 17:43:06  vandre
Adicionada tag log.

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoIRRF.class.php"                                   );
include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php"                    );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function validarFaixa()
{
    $obErro = new erro;
    $nuValorInicial = str_replace('.','',$_POST['flValorInicial']);
    $nuValorInicial = (float) str_replace(',','.',$nuValorInicial);
    $nuValorFinal   = str_replace('.','',$_POST['flValorFinal']);
    $nuValorFinal   = (float) str_replace(',','.',$nuValorFinal);
    $nuAliquota     = str_replace('.','',$_POST['flAliquota']);
    $nuAliquota     = (float) str_replace(',','.',$nuAliquota);
    $nuParcela      = str_replace('.','',$_POST['flParcela']);
    $nuParcela      = (float) str_replace(',','.',$nuParcela);
    if ( !$obErro->ocorreu() and $_POST['flValorInicial'] == "" ) {
        $obErro->setDescricao("Campo Valor Inicial da Base inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['flValorFinal'] == "" ) {
        $obErro->setDescricao("Campo Valor Final da Base inválido!()");
    }
    if ( !$obErro->ocorreu() and $nuValorInicial >= $nuValorFinal ) {
        $obErro->setDescricao("Valor Inicial da Base deve ser menos que Valor Final da Base!()");
    }
    if ( !$obErro->ocorreu() and $_POST['flAliquota'] == "" ) {
        $obErro->setDescricao("Campo Alíquota inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['flParcela'] == "" ) {
        $obErro->setDescricao("Campo Parcela a Deduzir inválido!()");
    }

    return $obErro;
}

function _float($stValor)
{
    $nuValor = str_replace('.','',$stValor);
    $nuValor = (float) str_replace(',','.',$nuValor);

    return $nuValor;
}

function _real($nuValor)
{
    $stValor = number_format($nuValor,'2',',','.');

    return $stValor;
}

function _validarFaixa()
{
    $obErro = new erro;
    $arFaixa = Sessao::read("faixa");
    $inId = count($arFaixa);
    $arAnterior  = $arFaixa[$inId-1];
    $arAtual     = $arFaixa[$inId];
    $nuBaseInicialAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flValorInicial"])  : 0;
    $nuBaseInicialAtual     = _float($_POST['flValorInicial']);
    if ($nuBaseInicialAnterior > 0) {
        if ( !($nuBaseInicialAtual > $nuBaseInicialAnterior) ) {
            $stMensagem .= "@A Base Inicial deve ser maior que "._real($nuBaseInicialAnterior);
        }
    }

    $nuBaseFinalAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flValorFinal"])  : 0;
    $nuBaseFinalAtual     = _float($_POST['flValorFinal']);
    if ($nuBaseFinalAnterior > 0) {
        if ( !($nuBaseFinalAtual > $nuBaseFinalAnterior) ) {
            $stMensagem .= "@A Base Final deve ser maior que "._real($nuBaseFinalAnterior);
        }
    }

    $nuAliquotaAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flAliquota"])  : 0;
    $nuAliquotaAtual     = _float($_POST['flAliquota']);
    if ($nuAliquotaAnterior > 0) {
        if ( !($nuAliquotaAtual > $nuAliquotaAnterior) ) {
            $stMensagem .= "@A Alíquota deve ser maior que "._real($nuAliquotaAnterior);
        }
    }

    $nuParcelaAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flParcela"])  : 0;
    $nuParcelaAtual     = _float($_POST['flParcela']);
    if ($nuParcelaAnterior > 0) {
        if ( !($nuParcelaAtual > $nuParcelaAnterior) ) {
            $stMensagem .= "@A Parcela a Deduzir deve ser maior que "._real($nuParcelaAnterior);
        }
    }

    $obErro->setDescricao($stMensagem);

    return $obErro;
}

function __validarFaixa($inId)
{
    $obErro = new Erro();
    $arFaixa = Sessao::read("faixa");
    if ( is_array($arFaixa) ) {
        $inId--;
        $arAnterior  = $arFaixa[$inId-1];
        $arAtual     = $arFaixa[$inId];
        $arPosterior = $arFaixa[$inId+1];
        $nuBaseInicialAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flValorInicial"])  : 0;
        $nuBaseInicialAtual     = _float($_POST['flValorInicial']);
        $nuBaseInicialPosterior = ( is_array($arPosterior) ) ? _float($arPosterior["flValorInicial"]) : 0;
        switch (true) {
            case $nuBaseInicialAnterior > 0 and $nuBaseInicialPosterior > 0 :
                if ( !($nuBaseInicialAtual > $nuBaseInicialAnterior and $nuBaseInicialAtual < $nuBaseInicialPosterior) ) {
                    $stMensagem .= "@A Base Inicial deve estar entre "._real($nuBaseInicialAnterior)." e "._real($nuBaseInicialPosterior);
                }
                break;
            case $nuBaseInicialAnterior > 0 :
                if ( !($nuBaseInicialAtual > $nuBaseInicialAnterior ) ) {
                    $stMensagem .= "@A Base Inicial deve ser maior que "._real($nuBaseInicialAnterior);
                }
                break;
            case $nuBaseInicialPosterior > 0 :
                if ( !($nuBaseInicialAtual < $nuBaseInicialPosterior) ) {
                    $stMensagem .= "@A Base Inicial deve ser menor que "._real($nuBaseInicialPosterior);
                }
                break;
        }

        $nuBaseFinalAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flValorFinal"])  : 0;
        $nuBaseFinalAtual     = _float($_POST['flValorFinal']);
        $nuBaseFinalPosterior = ( is_array($arPosterior) ) ? _float($arPosterior["flValorFinal"]) : 0;
        switch (true) {
            case $nuBaseFinalAnterior > 0 and $nuBaseFinalPosterior > 0 :
                if ( !($nuBaseFinalAtual > $nuBaseFinalAnterior and $nuBaseFinalAtual < $nuBaseFinalPosterior) ) {
                    $stMensagem .= "@A Base Final deve estar entre "._real($nuBaseFinalAnterior)." e "._real($nuBaseFinalPosterior);
                }
                break;
            case $nuBaseFinalAnterior > 0 :
                if ( !($nuBaseFinalAtual > $nuBaseFinalAnterior ) ) {
                    $stMensagem .= "@A Base Final deve ser maior que "._real($nuBaseFinalAnterior);
                }
                break;
            case $nuBaseFinalPosterior > 0 :
                if ( !($nuBaseFinalAtual < $nuBaseFinalPosterior) ) {
                    $stMensagem .= "@A Base Final deve ser menor que "._real($nuBaseFinalPosterior);
                }
                break;
        }

        $nuAliquotaAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flAliquota"])  : 0;
        $nuAliquotaAtual     = _float($_POST['flAliquota']);
        $nuAliquotaPosterior = ( is_array($arPosterior) ) ? _float($arPosterior["flAliquota"]) : 0;
        switch (true) {
            case $nuAliquotaAnterior > 0 and $nuAliquotaPosterior > 0 :
                if ( !($nuAliquotaAtual > $nuAliquotaAnterior and $nuAliquotaAtual < $nuAliquotaPosterior) ) {
                    $stMensagem .= "@A Alíquota deve estar entre "._real($nuAliquotaAnterior)." e "._real($nuAliquotaPosterior);
                }
                break;
            case $nuAliquotaAnterior > 0 :
                if ( !($nuAliquotaAtual > $nuAliquotaAnterior ) ) {
                    $stMensagem .= "@A Alíquota deve ser maior que "._real($nuAliquotaAnterior);
                }
                break;
            case $nuAliquotaPosterior > 0 :
                if ( !($nuAliquotaAtual < $nuAliquotaPosterior) ) {
                    $stMensagem .= "@A Alíquota deve ser menor que "._real($nuAliquotaPosterior);
                }
                break;
        }

        $nuParcelaAnterior  = ( is_array($arAnterior)  ) ? _float($arAnterior["flParcela"])  : 0;
        $nuParcelaAtual     = _float($_POST['flParcela']);
        $nuParcelaPosterior = ( is_array($arPosterior) ) ? _float($arPosterior["flParcela"]) : 0;
        switch (true) {
            case $nuParcelaAnterior > 0 and $nuParcelaPosterior > 0 :
                if ( !($nuParcelaAtual > $nuParcelaAnterior and $nuParcelaAtual < $nuParcelaPosterior) ) {
                    $stMensagem .= "@AParcela a Deduzir deve estar entre "._real($nuParcelaAnterior)." e "._real($nuParcelaPosterior);
                }
                break;
            case $nuParcelaAnterior > 0 :
                if ( !($nuParcelaAtual > $nuParcelaAnterior ) ) {
                    $stMensagem .= "@AParcela a Deduzir deve ser maior que "._real($nuParcelaAnterior);
                }
                break;
            case $nuParcelaPosterior > 0 :
                if ( !($nuParcelaAtual < $nuParcelaPosterior) ) {
                    $stMensagem .= "@AParcela a Deduzir deve ser menor que "._real($nuParcelaPosterior);
                }
                break;
        }

        $obErro->setDescricao($stMensagem);
    }

    return $obErro;
}

function incluirFaixa($boExecuta=false)
{
    $obErro = new erro;
    $nuValorInicial = str_replace('.','',$_POST['flValorInicial']);
    $nuValorInicial = (float) str_replace(',','.',$nuValorInicial);
    $nuValorFinal   = str_replace('.','',$_POST['flValorFinal']);
    $nuValorFinal   = (float) str_replace(',','.',$nuValorFinal);
    $nuAliquota     = str_replace('.','',$_POST['flAliquota']);
    $nuAliquota     = (float) str_replace(',','.',$nuAliquota);
    $nuParcela      = str_replace('.','',$_POST['flParcela']);
    $nuParcela      = (float) str_replace(',','.',$nuParcela);
    if ( Sessao::read('processo') == 'alteracao' ) {
        $obErro->setDescricao("Processo de alteração em progresso, conclua o processo ou limpe o formulário!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarFaixa();
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = _validarFaixa();
    }
    if ( !$obErro->ocorreu() ) {
        $arFaixa = Sessao::read('faixa');
        $inId = ( is_array($arFaixa) ) ? $arFaixa[count($arFaixa)-1]['inId'] +1 : 1;
        $arTemp['inId']             = $inId;
        $arTemp['flValorInicial']   = $_POST['flValorInicial'];
        $arTemp['flValorFinal']     = $_POST['flValorFinal'];
        $arTemp['flAliquota']       = $_POST['flAliquota'];
        $arTemp['flParcela']        = $_POST['flParcela'];
        $arFaixa[]= $arTemp;
        Sessao::write("faixa",$arFaixa);
        $stJs .= listarFaixa();
        $stJs .= limparFaixa();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function alterarFaixa($boExecuta=false)
{
    $obErro = new erro;
    if ( Sessao::read('processo') != 'alteracao' ) {
        $obErro->setDescricao("Processo de inclusao em progresso, conclua o processo ou limpe o formulário!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarFaixa();
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = __validarFaixa($_POST['inId']);
    }
    if ( !$obErro->ocorreu() ) {
        $arTemp['inId']             = $_POST['inId'];
        $arTemp['flValorInicial']   = $_POST['flValorInicial'];
        $arTemp['flValorFinal']     = $_POST['flValorFinal'];
        $arTemp['flAliquota']       = $_POST['flAliquota'];
        $arTemp['flParcela']        = $_POST['flParcela'];
        $arFaixas = Sessao::read("faixa");
        foreach ($arFaixas as $arFaixa) {
            if ($arFaixa['inId'] == $_POST['inId']) {
                $arFaixaEditado[] = $arTemp;
            } else {
                $arFaixaEditado[] = $arFaixa;
            }
        }
        Sessao::write('faixa',$arFaixaEditado);
        $stJs .= listarFaixa();
        $stJs .= limparFaixa();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirFaixa($boExecuta=false)
{
    $arTemp = array();
    $arFaixas = Sessao::read("faixa");
    foreach ($arFaixas as $arFaixa) {
        if ($arFaixa['inId'] != $_GET['inId']) {
            $arTemp[] = $arFaixa;
        }
    }
    Sessao::write('faixa',$arTemp);
    $stJs .= listarFaixa();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparFaixa($boExecuta=false)
{
    $stJs .= "f.flValorInicial.value = '';      \n";
    $stJs .= "f.flValorFinal.value = '';        \n";
    $stJs .= "f.flAliquota.value = '';          \n";
    $stJs .= "f.flParcela.value = '';           \n";
    Sessao::write('processo',"");
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaAlterarFaixa($boExecuta=false)
{
    $arFaixas = Sessao::read("faixa");
    foreach ($arFaixas as $arFaixa) {
        if ($arFaixa['inId'] == $_GET['inId']) {
            $stJs .= "f.inId.value = '".$_GET['inId']."';                        \n";
            $stJs .= "f.flValorInicial.value = '".$arFaixa['flValorInicial']."'; \n";
            $stJs .= "f.flValorFinal.value = '".$arFaixa['flValorFinal']."';     \n";
            $stJs .= "f.flAliquota.value = '".$arFaixa['flAliquota']."';         \n";
            $stJs .= "f.flParcela.value = '".$arFaixa['flParcela']."';           \n";
            Sessao::write('processo','alteracao');
        }
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listarFaixa($boExecuta=false)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array(Sessao::read('faixa')) ? Sessao::read('faixa') : array() );
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Faixas de Descontos Cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Base Inicial" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Base Final" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Alíquota" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Parcela a Deduzir" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flValorInicial" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flValorFinal" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flAliquota" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flParcela" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('montaAlterarFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluirFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSpan2').innerHTML = '".$stHtml."';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencherEvento($boExecuta=false)
{
    $obRFolhaPagamentoIRRF = new RFolhaPagamentoIRRF;
    $obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
    $stCampoNome = $_GET['stCampoNome'];
    $arStNatureza= explode(",",$_GET['stNatureza']);

    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->setCodigo($_POST['inCodigo'.$stCampoNome]);
    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->setEventoSistema($_GET['boEventoSistema']);

    foreach ($arStNatureza as $stNatureza) {
        $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->setNatureza($stNatureza);
        $obRFolhaPagamentoIRRF->roRFolhaPagamentoEvento->listarEvento($rsEvento);

        if($rsEvento->getNumLinhas() > 0)
            break;
    }
    $stInner = "inCampoInner".$stCampoNome;
    if ( $rsEvento->getNumLinhas() > 0 and $_POST['inCodigo'.$stCampoNome] != "" ) {
        $stJs .= "f.inCodigo".$stCampoNome.".value = '".$rsEvento->getCampo('codigo')."';             \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
    } else {
        if(trim($_POST['inCodigo'.$stCampoNome]) != "")
            $stJs .= "alertaAviso('@Codigo de Evento Inválido','form','erro','".Sessao::getId()."');\n";
        $stJs .= "f.inCodigo".$stCampoNome.".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencherInnerEventos($boExecuta=false)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php");
    $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento;
    $stFiltro  = " AND tabela_irrf_evento.cod_tabela = ".$_GET['inCodTabela'];
    $stFiltro .= " AND tabela_irrf_evento.timestamp = '".$_GET["stTimestamp"]."'";
    $obTFolhaPagamentoTabelaIrrfEvento->recuperaRelacionamento($rsEventoIRRF,$stFiltro,$stOrder);

    while (!$rsEventoIRRF->eof()) {
        $stInner          = "inCampoInnerIRRF".$rsEventoIRRF->getCampo('cod_tipo');
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEventoIRRF->getCampo('descricao')."';  \n";
        $rsEventoIRRF->proximo();
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencheCID()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCID.class.php");
    $obTPessoalCid = new TPessoalCID;
    $obTPessoalCid->setDado('cod_cid', $_REQUEST['inCodCID']);
    $obTPessoalCid->recuperaPorChave($rsCid);

    $stJs = "d.getElementById('stCID').innerHTML = '".$rsCid->getCampo('descricao')."'; \n";
    SistemaLegado::executaFrameOculto($stJs);
}

function incluirCID($boExecuta=false)
{
    $obRFolhaPagamentoIRRF = new RFolhaPagamentoIRRF;
    $obRFolhaPagamentoIRRF->addRPessoalCID();
    $obErro = new erro;
    if ($_POST['inCodCID'] == "") {
        $obErro->setDescricao("Campo CID inválido!()");
    }
    $arCids = Sessao::read("cid");
    if ( !$obErro->ocorreu() and is_array($arCids) ) {
        foreach ($arCids as $arCID) {
            if ($arCID['cod_cid'] == $_POST['inCodCID']) {
                $obErro->setDescricao("O CID informado já está presenta na lista!()");
                break;
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $inId = ( is_array($arCids) ) ? $arCids[count($arCids)-1]['inId'] +1 : 1;
        $obRFolhaPagamentoIRRF->roRPessoalCID->setCodCID($_POST['inCodCID']);
        $obRFolhaPagamentoIRRF->roRPessoalCID->listarOrdenadoPorDescricao($rsCID);
        $arTemp['inId']         = $inId;
        $arTemp['cod_cid']      = $_POST['inCodCID'];
        $arTemp['descricao']    = $rsCID->getCampo('descricao');
        $arCids[]= $arTemp;
        Sessao::write("cid",$arCids);
        $stJs .= listarCID();
        $stJs .= limparCID();
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirCID($boExecuta=false)
{
    $arTemp = array();
    $arCids = Sessao::read("cid");
    foreach ($arCids as $arCID) {
        if ($arCID['inId'] != $_GET['inId']) {
            $arTemp[] = $arCID;
        }
    }
    Sessao::write("cid",$arTemp);
    $stJs .= listarCID();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparCID($boExecuta=false)
{
    $stJs .= "f.inCodCID.value = '';  \n";
    $stJs .= "d.getElementById('stCID').innerHTML = '&nbsp;'; \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function listarCID($boExecuta=false)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array(Sessao::read('cid')) ? Sessao::read('cid') : array() );

    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "CIDs Isentas de IRRF" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_cid" );
        $obLista->ultimoDado->setAlinhamento( "DIREITA" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->ultimoDado->setAlinhamento( "ESQUERDA" );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluirCID');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$stHtml."';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparForm($boExecuta=false)
{
    $obRFolhaPagamentoIRRF = new RFolhaPagamentoIRRF;
    $obRFolhaPagamentoIRRF->addRFolhaPagamentoEvento();
    $obRFolhaPagamentoIRRF->roRFolhaPagamentoEventoIRRF->listarEventoIRRF($rsTiposEvento);
    #sessao->transf = array();
    $stJs .= limparCID();
    $stJs .= limparFaixa();
    $stJs .= listarCID();
    $stJs .= listarFaixa();
    $stJs .= "f.flValorDependente.value = '';   \n";
    $stJs .= "f.flValorLimite.value = '';       \n";
    while (!$rsTiposEvento->eof()) {
        $stValue          = "inCodigoIRRF".$rsTiposEvento->getCampo('cod_tipo');
        $stInner          = "inCampoInnerIRRF".$rsTiposEvento->getCampo('cod_tipo');
        $stJs .= "f.".$stValue.".value = '';                                \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
        $rsTiposEvento->proximo();
    }
    $stJs .= "f.dtVigencia.value = '';          \n";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function processarFormAlteracao($boExecuta=false)
{
    $stJs .= preencherInnerEventos();
    $stJs .= listarCID();
    $stJs .= listarFaixa();
    $stJs .= listarEventoAjudaCusto();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function validarVigencia()
{
    if ( sistemaLegado::comparaDatas(Sessao::read('dtVigencia'),$_POST['dtVigencia']) ) {
        $stMensagem = "A vigência deve ser posterior a ".Sessao::read('dtVigencia');
        $stJs .= "alertaAviso('$stMensagem','form','erro','".Sessao::getId()."');       \n";
        $stJs .= "f.dtVigencia.value = '".Sessao::read('dtVigencia')."';";
    }

    return $stJs;
}

function listarEventoAjudaCusto($boExecuta=false)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array(Sessao::read('eventoAjudaCusto')) ? Sessao::read('eventoAjudaCusto') : array() );
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Eventos de Diárias e Ajuda de Custo" );
        $obLista->setNumeracao(false);

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Código do Evento");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 16 );
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Proventos/Descontos" );
        $obLista->ultimoCabecalho->setWidth( 5);
        $obLista->commitCabecalho();
        
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 2 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flCodigo" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flDescricao" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "flDescNatureza" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluirEventoAjudaCusto');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$stHtml."';";
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function validarEventoAjudaCusto()
{
    $obErro = new erro;
    $inCodigoEventoAjudaCusto = preg_replace( "/[^0-9]/i","",$_POST['inCodigoEventoAjudaCusto']);

    if ($inCodigoEventoAjudaCusto == "") {
        $obErro->setDescricao("Campo Código do Evento Provento ou Desconto para Comprovante de Rendimentos inválido!()");
    }

    if ( !$obErro->ocorreu() ) {
        $arEventoAjudaCusto = Sessao::read("eventoAjudaCusto");
        if (is_array($arEventoAjudaCusto)) {
            foreach ($arEventoAjudaCusto as $arTemp) {
                if ($arTemp['flCodigo'] == $inCodigoEventoAjudaCusto) {
                    $obErro->setDescricao("Código do Evento Provento ou Desconto para Comprovante de Rendimentos já consta na lista!()");
                    break;
                }
            }
        }
    }

    return $obErro;
}

function incluirEventoAjudaCusto($boExecuta=false)
{
    $obErro = new erro;
    $inCodigoEventoAjudaCusto = preg_replace( "/[^0-9]/i","",$_POST['inCodigoEventoAjudaCusto']);

    if ( Sessao::read('processo') == 'alteracao' ) {
        $obErro->setDescricao("Processo de alteração em progresso, conclua o processo ou limpe o formulário!()");
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = validarEventoAjudaCusto();
    }

    if ( !$obErro->ocorreu() ) {
        $rsEvento = new RecordSet();

        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
        $obTFolhaPagamentoEvento->setDado('natureza', 'P');
        $obTFolhaPagamentoEvento->setDado('codigo', $inCodigoEventoAjudaCusto);
        $obTFolhaPagamentoEvento->listar($rsEvento);

        if ($rsEvento->getNumLinhas() <= 0) {
            $obTFolhaPagamentoEvento->setDado('natureza', 'D');
            $obTFolhaPagamentoEvento->listar($rsEvento);
        }

        if ($rsEvento->getNumLinhas() <= 0) {
            $obErro->setDescricao("Campo Código do Evento Provento ou Desconto para Comprovante de Rendimentos inválido!()");
        } else {
            $arEventoAjudaCusto = Sessao::read("eventoAjudaCusto");
            $inId = ( is_array($arEventoAjudaCusto) ) ? $arEventoAjudaCusto[count($arEventoAjudaCusto)-1]['inId'] +1 : 1;
            $arTemp['inId']             = $inId;
            $arTemp['flCodigoEvento']   = $rsEvento->getCampo('cod_evento');
            $arTemp['flCodigo']         = $rsEvento->getCampo('codigo');
            $arTemp['flDescricao']      = $rsEvento->getCampo('descricao');
        $arTemp['flDescNatureza']   = ($rsEvento->getCampo('natureza')=='D')?"Descontos":"Proventos";
            $arEventoAjudaCusto[]= $arTemp;
            Sessao::write("eventoAjudaCusto",$arEventoAjudaCusto);
            $stJs .= listarEventoAjudaCusto();
            $stJs .= limparEventoAjudaCusto();
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function excluirEventoAjudaCusto($boExecuta=false)
{
    $arTemp = array();
    $arEventosAjudaCusto = Sessao::read("eventoAjudaCusto");
    foreach ($arEventosAjudaCusto as $areventoAjudaCusto) {
        if ($areventoAjudaCusto['inId'] != $_GET['inId']) {
            $arTemp[] = $areventoAjudaCusto;
        }
    }
    Sessao::write('eventoAjudaCusto',$arTemp);
    $stJs .= listarEventoAjudaCusto();
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function limparEventoAjudaCusto($boExecuta=false)
{
    $stJs .= "f.inCodigoEventoAjudaCusto.value = '';\n";
    $stJs .= "d.getElementById('inCampoInnerEventoAjudaCusto').innerHTML = '&nbsp;';\n";
    Sessao::write('processo',"");
    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

switch ($stCtrl) {
    case 'preencherEvento':
        $stJs .= preencherEvento();
    break;
   case 'preencheCID':
        $stJs .= preencheCID();
    break;
    case 'incluirCID':
        $stJs .= incluirCID();
    break;
    case 'excluirCID':
        $stJs .= excluirCID();
    break;
    case 'limparCID':
        $stJs .= limparCID();
    break;
    case 'incluirFaixa':
        $stJs .= incluirFaixa();
    break;
    case 'alterarFaixa':
        $stJs .= alterarFaixa();
    break;
    case 'excluirFaixa':
        $stJs .= excluirFaixa();
    break;
    case 'limparFaixa':
        $stJs .= limparFaixa();
    break;
    case 'montaAlterarFaixa':
        $stJs .= montaAlterarFaixa();
    break;
    case 'limparForm':
        $stJs .= limparForm();
    break;
    case 'validarVigencia':
        $stJs .= validarVigencia();
    break;
    case 'incluirEventoAjudaCusto':
        $stJs .= incluirEventoAjudaCusto();
    break;
    case 'excluirEventoAjudaCusto':
        $stJs .= excluirEventoAjudaCusto();
    break;
    case 'limparEventoAjudaCusto':
        $stJs .= limparEventoAjudaCusto();
    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>

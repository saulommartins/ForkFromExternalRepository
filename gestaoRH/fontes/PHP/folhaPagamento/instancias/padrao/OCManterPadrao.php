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
    * Página Oculta de Padrao
    * Data de Criação   : 02/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 10:16:48 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso :uc-04.05.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php"                                       );

$stCtrl = $request->get('stCtrl');

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
$rsFaixas = new RecordSet;

function listarFaixas($arRecordSet, $boExecuta=true)
{
    global $obRegra;

    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( is_array($arRecordSet) ? $arRecordSet : array() );

    $rsRecordSet->addFormatacao( "valor", "NUMERIC_BR" );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Progressões Cadastradas" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Percentual" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Meses" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "percentual" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "qtdmeses" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alteraDado('alteraFaixa');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->ultimaAcao->addCampo("2","descricao");
        $obLista->ultimaAcao->addCampo("3","valor");
        $obLista->ultimaAcao->addCampo("4","percentual");
        $obLista->ultimaAcao->addCampo("5","qtdmeses");
        $obLista->commitAcao();

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
    $stJs .= "d.getElementById('spnListaProgressao').innerHTML = '".$stHtml."';";
    $stJs .= "f.inIdProgressao.value = '';";
    $stJs .= "f.stDescricaoNivel.value = '';";
    $stJs .= "f.stValorCorrecao.value = '';";
    $stJs .= "f.stPercentual.value = '';";
    $stJs .= "f.stMeses.value = '';";

    if ($boExecuta==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function MontaNorma($stSelecionado = "")
{
    global $_POST;
    global $obRFolhaPagamentoPadrao;

    $stCombo  = "inCodNorma";
    $stFiltro = "inCodTipoNorma";
    $stJs .= "limpaSelect(f.".$stCombo.",0); \n";
    $stJs .= "f.".$stCombo.".options[0] = new Option('Selecione','');\n";
    $stJs .= "f.".$stCombo."Txt.value='".$stSelecionado."';\n";

    if ($_POST[ $stFiltro ] != "") {
        $inCodTipoNorma = $_POST[ $stFiltro ];
        $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
        $obRFolhaPagamentoPadrao->obRNorma->listar( $rsCombo );
        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("cod_norma");
            $stDesc = $rsCombo->getCampo("nom_norma");
            if ($stSelecionado == $inId) {
                $stSelected = 'selected';
            } else {
                $stSelected = '';
            }
            $stJs .= "f.".$stCombo.".options[".$inCount."] = new Option('".$stDesc."','".$inId."'); \n";
            $rsCombo->proximo();
        }
        $stJs .= "f.".$stCombo.".value='".$stSelecionado."';\n";
    }

    return $stJs;

}

function MontaNiveis()
{
    global $obRFolhaPagamentoPadrao;

    $arProgressao = array ();
    $obRFolhaPagamentoPadrao->setCodPadrao                            ( $_REQUEST["hdnCodPadrao"]  );
    $obRFolhaPagamentoPadrao->addNivelPadrao                          ( );
    $obRFolhaPagamentoPadrao->roUltimoNivelPadrao->listarNivelPadrao  ( $rsNivelPadrao             );
    $rsNivelPadrao->addFormatacao ("valor", "NUMERIC_BR");

    $inCount = 0;
    while ( !$rsNivelPadrao->eof () ) {
        $arProgressao[$inCount]["inId"       ] = $inCount;
        $arProgressao[$inCount]["descricao"  ] = $rsNivelPadrao->getCampo("descricao" );
        $arProgressao[$inCount]["valor"      ] = $rsNivelPadrao->getCampo("valor"     );
        $arProgressao[$inCount]["percentual" ] = str_replace (".", ",", $rsNivelPadrao->getCampo("percentual"));
        $arProgressao[$inCount]["qtdmeses"   ] = $rsNivelPadrao->getCampo("qtdmeses"  );
        $inCount++;
        $rsNivelPadrao->proximo ();
    }

    Sessao::write("Progressao",$arProgressao);
    $stJS = listarProgressao ( Sessao::read("Progressao") );

    return $stJS;

}

function validaProgressao($inIdProgressao="")
{
    $obErro = new Erro;
    if ( !$obErro->ocorreu() and $_POST['stDescricaoNivel'] == "" ) {
        $obErro->setDescricao("Campo Descrição da Progressão inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stPercentual'] == "" ) {
        $obErro->setDescricao("Campo Percentual inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stValorCorrecao'] == "" ) {
        $obErro->setDescricao("Campo Valor de Correção inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stMeses'] == "" ) {
        $obErro->setDescricao("Campo Meses para Incidência inválido!()");
    }
    $arProgressoes = Sessao::read("Progressao");
    if ( !$obErro->ocorreu() and is_array($arProgressoes) ) {
        foreach ($arProgressoes as $arProgressao) {
            if ( ($arProgressao['descricao'] == $_POST['stDescricaoNivel']) and ($inIdProgressao != $arProgressao['inId']) ) {
                $obErro->setDescricao("Descrição já cadastrada.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = _validaProgressaoPercentual($inIdProgressao);
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = _validaProgressaoValor($inIdProgressao);
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = _validaProgressaoMeses($inIdProgressao);
    }

    return $obErro;
}

function _float($stValor)
{
    $nuValor = str_replace('.','',$stValor);
    $nuValor = (float) str_replace(',','.',$nuValor);

    return $nuValor;
}

function _validaProgressaoPercentual($inIdProgressao)
{
    $obErro = new erro;
    $arProgressoes = Sessao::read('Progressao');
    $arProgressao        = $arProgressoes[($inIdProgressao-2)];
    $nuPercentualInicial = _float($arProgressao['percentual']);
    $arProgressao        = $arProgressoes[($inIdProgressao)];
    $nuPercentualFinal   = _float($arProgressao['percentual']);
    $nuPercentualAtual   = _float($_POST['stPercentual']);
    switch (true) {
        case $nuPercentualFinal > 0 and $nuPercentualInicial > 0:
            if ( !($nuPercentualAtual > $nuPercentualInicial and $nuPercentualAtual < $nuPercentualFinal) ) {
                $nuPercentualInicial = number_format($nuPercentualInicial,2,',','.');
                $nuPercentualFinal   = number_format($nuPercentualFinal,2,',','.');
                $obErro->setDescricao("O valor referente ao Percentual informado deve estar entre ".$nuPercentualInicial." e ".$nuPercentualFinal.".");
            }
            break;
        case $nuPercentualFinal == "" and $nuPercentualInicial > 0:
            if ( !($nuPercentualAtual > $nuPercentualInicial) ) {
                $nuPercentualInicial = number_format($nuPercentualInicial,2,',','.');
                $obErro->setDescricao("O valor referente ao Percentual informado deve ser maior que ".$nuPercentualInicial.".");
            }
            break;
        case $nuPercentualFinal > 0 and $nuPercentualInicial == "":
            if ( !($nuPercentualAtual < $nuPercentualFinal) ) {
                $nuPercentualFinal = number_format($nuPercentualFinal,2,',','.');
                $obErro->setDescricao("O valor referente ao Percentual informado deve ser menor que ".$nuPercentualFinal.".");
            }
            break;
    }

    return $obErro;
}

function _validaProgressaoValor($inIdProgressao)
{
    $obErro = new erro;
    $arProgressoes = Sessao::read('Progressao');
    $arProgressao       = $arProgressoes[($inIdProgressao-2)];
    $nuValorInicial     = $arProgressao['valor'];
    $arProgressao       = $arProgressoes[($inIdProgressao)];
    $nuValorFinal       = _float($arProgressao['valor']);
    $nuValorAtual       = _float($_POST['stValorCorrecao']);
    switch (true) {
        case $nuValorFinal > 0 and $nuValorInicial > 0:
            if ( !($nuValorAtual > $nuValorInicial and $nuValorAtual < $nuValorFinal) ) {
                $nuValorInicial = number_format($nuValorInicial,2,',','.');
                $nuValorFinal   = number_format($nuValorFinal,2,',','.');
                $obErro->setDescricao("O valor referente ao Valor de Correção informado deve estar entre ".$nuValorInicial." e ".$nuValorFinal.".");
            }
            break;
        case $nuValorFinal == "" and $nuValorInicial > 0:
            if ( !($nuValorAtual > $nuValorInicial) ) {
                $nuValorInicial = number_format($nuValorInicial,2,',','.');
                $obErro->setDescricao("O valor referente ao Valor de Correção informado deve ser maior que ".$nuValorInicial.".");
            }
            break;
        case $nuValorFinal > 0 and $nuValorInicial == "":
            if ( !($nuValorAtual < $nuValorFinal) ) {
                $nuValorFinal = number_format($nuValorFinal,2,',','.');
                $obErro->setDescricao("O valor referente ao Valor de Correção informado deve ser menor que ".$nuValorFinal.".");
            }
            break;
    }

    return $obErro;
}

function _validaProgressaoMeses($inIdProgressao)
{
    $obErro = new erro;
    $arProgressoes = Sessao::read('Progressao');
    $arProgressao       = $arProgressoes[($inIdProgressao-2)];
    $nuMesInicial       = $arProgressao['qtdmeses'];
    $arProgressao       = $arProgressoes[($inIdProgressao)];
    $nuMesFinal         = $arProgressao['qtdmeses'];
    $nuMesAtual         = $_POST['stMeses'];
    switch (true) {
        case $nuMesFinal > 0 and $nuMesInicial > 0:
            if ( !($nuMesAtual > $nuMesInicial and $nuMesAtual < $nuMesFinal) ) {
                $obErro->setDescricao("O valor referente aos Meses para Incidência informado deve estar entre ".$nuMesInicial." e ".$nuMesFinal.".");
            }
            break;
        case $nuMesFinal == "" and $nuMesInicial > 0:
            if ( !($nuMesAtual > $nuMesInicial) ) {
                $obErro->setDescricao("O valor referente aos Meses para Incidência informado deve ser maior que ".$nuMesInicial.".");
            }
            break;
        case $nuMesFinal > 0 and $nuMesInicial == "":
            if ( !($nuMesAtual < $nuMesFinal) ) {
                $obErro->setDescricao("O valor referente aos Meses para Incidência informado deve ser menor que ".$nuMesFinal.".");
            }
            break;
    }

    return $obErro;
}

function recalcularProgressao()
{
    $arProgressoes = Sessao::read("Progressao");
    $arElementos = $__arProgressoes = array();
    foreach ($arProgressoes as $progressao) {
        $vlProgressao = str_replace(",", ".", str_replace(".", "", $progressao['valor']));
        $percentual   = str_replace(",", ".", $progressao['percentual']);
        $vlPadrao     = str_replace(",", ".", str_replace(".", "", $_REQUEST['stValorPadrao']));

        $arElementos['inId']            = $progressao['inId'];
        $arElementos['descricao']       = $progressao['descricao'];
        $arElementos['valor']           = (($vlPadrao / 100) * ($percentual + 100));
        $arElementos['percentual']      = $progressao['percentual'];
        $arElementos['qtdmeses']        = $progressao['qtdmeses'];
        $__arProgressoes[]              = $arElementos;
    }

    Sessao::write("Progressao",$__arProgressoes);
    $stJs  = listarFaixas( Sessao::read("Progressao"), false );

    return $stJs;
}

function incluirProgressao()
{
    $arProgressoes = Sessao::read("Progressao");
    $obErro = validaProgressao(count($arProgressoes)+1);
    if ( !$obErro->ocorreu() ) {
        $arElementos['inId']            = count($arProgressoes)+1;
        $arElementos['descricao']       = $_POST['stDescricaoNivel'];
        $arElementos['valor']           = str_replace(",", ".", str_replace(".", "", $_POST['stValorCorrecao']));
        $arElementos['percentual']      = $_POST['stPercentual'];
        $arElementos['qtdmeses']        = $_POST['stMeses'];
        $arProgressoes[]= $arElementos;
        Sessao::write("Progressao",$arProgressoes);
        $stJs .= listarFaixas( Sessao::read("Progressao"), false );
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }

    return $stJs;
}

function alterarProgressao()
{
    $obErro = validaProgressao($_POST['inIdProgressao']);
    $arProgressoes = Sessao::read("Progressao");
    if ( !$obErro->ocorreu() ) {
        $arElementos['inId']            = $_POST['inIdProgressao'];
        $arElementos['descricao']       = $_POST['stDescricaoNivel'];
        $arElementos['valor']           = str_replace(",", ".", str_replace(".", "", $_POST['stValorCorrecao']));
        $arElementos['percentual']      = $_POST['stPercentual'];
        $arElementos['qtdmeses']        = $_POST['stMeses'];
        $arProgressoes[$_POST['inIdProgressao']-1]= $arElementos;
        Sessao::write("Progressao",$arProgressoes);
        $stJs .= listarFaixas( Sessao::read("Progressao"), false );
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');      \n";
    }

    return $stJs;
}

function montaFaixa($boExecuta=false)
{
    $stMensagem  = false;
    $arElementos = array ();
    $stJs        = "";
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( Sessao::read("Progressao") );
    $boErroDescricao = false;
    while (!$rsRecordSet->eof()) {
        if ( $rsRecordSet->getCampo("descricao") == $_POST['stDescricaoNivel'] ) {
            $boErroDescricao = true;
            break;
        }
        $rsRecordSet->proximo();
    }
    //$rsRecordSet->setUltimoElemento();
    $inUltimoId = $rsRecordSet->getCampo("inId");
    if (!$inUltimoId) {
        $inProxId = 1;
    } else {
        $inProxId = $inUltimoId + 1;
    }

    $ultimoMesIncluido          = $rsRecordSet->getCampo("qtdmeses");
    $ultimoPercentualIncluido   = $rsRecordSet->getCampo("percentual");
    $ultimoValorIncluido        = $rsRecordSet->getCampo("valor");
    $ultimaDescricaoIncluida    = $rsRecordSet->getCampo("descricao");
    $MesIncluido = $_POST['stMeses'];
    if ( ($ultimoMesIncluido >= $MesIncluido) && (count (Sessao::read("Progressao")) > 0 )) {
        $stJs .= "alertaAviso('@O valor referente ao mês informado deve ser maior que o da última faixa cadastrada.','form','erro','".Sessao::getId()."');      \n";
    } elseif ( $ultimoPercentualIncluido >= $_POST['stPercentual'] and count(Sessao::read("Progressao")) > 0 ) {
        $stJs .= "alertaAviso('@O valor referente ao percentual informado deve ser maior que o da última faixa cadastrada.','form','erro','".Sessao::getId()."');      \n";
    } elseif ( $ultimoValorIncluido >= $_POST['stValorCorrecao'] and count(Sessao::read("Progressao")) > 0 ) {
        $stJs .= "alertaAviso('@O valor referente ao valor informado deve ser maior que o da última faixa cadastrada.','form','erro','".Sessao::getId()."');      \n";
    } elseif ( $boErroDescricao and count(Sessao::read("Progressao")) > 0 ) {
        $stJs .= "alertaAviso('@Descricao já cadastrada.','form','erro','".Sessao::getId()."');      \n";
    } else {
        $arProgressoes = Sessao::read("Progressao");
        $arElementos['inId']            = $inProxId;
        $arElementos['descricao']       = $_POST['stDescricaoNivel'];
        $arElementos['valor']           = str_replace(",", ".", str_replace(".", "", $_POST['stValorCorrecao']));
        $arElementos['percentual']      = $_POST['stPercentual'];
        $arElementos['qtdmeses']        = $_POST['stMeses'];
        $arProgressoes[]= $arElementos;
        Sessao::write("Progressao",$arProgressoes);
        $stJs .= listarFaixas( Sessao::read("Progressao"), false );
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaFaixaAlteracao($boExecuta=false)
{
    $id = $_POST['inIdProgressao'];
    $arProgressoes = Sessao::read("Progressao");

    $stMensagem = false;
    $arElementos = array ();
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arProgressoes );

    $MesIncluido                = $_POST['stMeses'];

    $ultimoMesIncluido          = $arProgressoes[($id-2)]["qtdmeses"];
    if($ultimoMesIncluido=="") $ultimoMesIncluido = $MesIncluido - 1;
    $proximoMesIncluido         = $arProgressoes[($id)]["qtdmeses"];
    if($proximoMesIncluido=="") $proximoMesIncluido = $MesIncluido + 1;

    if ( ($ultimoMesIncluido >= $MesIncluido) && (count ($arProgressoes) > 0 )) {
        $stJs .= "alertaAviso('@O valor referente ao mês informado deve ser maior que o da faixa anterior.','form','erro','".Sessao::getId()."');      \n";
    } elseif ( ($proximoMesIncluido <= $MesIncluido) && (count ($arProgressoes) > 0 )) {
        $stJs .= "alertaAviso('@O valor referente ao mês informado deve ser menor que o da faixa seguinte.','form','erro','".Sessao::getId()."');      \n";
    } else {

        #reset(#sessao->transf4['Progressao']);
        while ( list( $arId ) = each( $arProgressoes ) ) {
            if ($arProgressoes[$arId]["inId"] != $id) {
                $arElementos['inId']            = $arProgressoes[$arId]["inId"];
                $arElementos['descricao']       = $arProgressoes[$arId]["descricao"];
                $arElementos['percentual']      = $arProgressoes[$arId]["percentual"];
                $arElementos['valor']           = $arProgressoes[$arId]["valor"];
                $arElementos['qtdmeses']        = $arProgressoes[$arId]["qtdmeses"];
                $arTMP[] = $arElementos;
            } else {
                $arElementos['inId']            = $arProgressoes[$arId]["inId"];
                $arElementos['descricao']       = $_POST['stDescricaoNivel'];
                $arElementos['valor']           = $_POST['stValorCorrecao'];
                $arElementos['percentual']      = $_POST['stPercentual'];
                $arElementos['qtdmeses']        = $_POST['stMeses'];
                $arElementos['valor'] = str_replace('.', '', $arElementos['valor']);
                $arElementos['valor'] = str_replace(',', '.', $arElementos['valor']);
                $arTMP[] = $arElementos;
            }
        }
        Sessao::write("Progressao",$arTMP);
        #sessao->transf4['Progressao'] = $arTMP;
        $stJs .= listarFaixas( $arTMP,false );

    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

// Acoes por pagina
switch ($stCtrl) {

    case "recalcularProgressao":
        $stJs .= recalcularProgressao();
    break;

    case "incluirProgressao":
        $stJs .= incluirProgressao();
    break;
    case "alterarProgressao":
        $stJs .= alterarProgressao();
    break;
    case "MontaNorma":
        $stJs = MontaNorma();
    break;
    case "MontaFaixa":
        $stJs .= montaFaixa();
    break;

    case "MontaFaixaAlteracao":
        $stJs .= montaFaixaAlteracao();
    break;

    case "excluiFaixa":
        $id = $_GET['inId'];
        $stMensagem = false;
        if ($_REQUEST['stAcao']=='alterar') {
            $arProgressoes = Sessao::read("Progressao");
            reset($arProgressoes);
            while ( list( $arId ) = each( $arProgressoes ) ) {
                if ($arProgressoes["inId"] == $id) {
                    $obRegra->setCodPadrao     ( $_POST['hdnCodPadrao'] );
                }
            }
        }

        if ($stMensagem==false) {
            reset($arProgressoes);
            $cont=0;
            while ( list( $arId ) = each( $arProgressoes ) ) {
                if ($arProgressoes[$arId]["inId"] != $id) {
                    //código para deixar os registro com ordem sequencial
                    $cont++;
                    $arProgressoes[$arId]["inId"] = $cont;

                    $arElementos['inId']        = $arProgressoes[$arId]["inId"];
                    $arElementos['descricao']   = $arProgressoes[$arId]["descricao"];
                    $arElementos['percentual']  = $arProgressoes[$arId]["percentual"];
                    $arElementos['valor']       = $arProgressoes[$arId]["valor"];
                    $arElementos['qtdmeses']    = $arProgressoes[$arId]["qtdmeses"];
                    $arTMP[] = $arElementos;
                }
            }
            Sessao::write("Progressao",$arTMP);
            listarFaixas( $arTMP );
        } else {
            $stJs = "SistemaLegado::alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
    break;

    case "excluiTodaFaixa":
        $arProgressoes = Sessao::read("Progressao");
        if (count($arProgressoes) > 0) {
            reset($arProgressoes);
            #while ( list( $arId ) = each( sessao->transf4['Progressao'] ) ) { }
            Sessao::write("Progressao",$arTMP);
            listarFaixas( $arTMP );
        }
    break;

    case 'preencheInner':
        $arProgressoes = Sessao::read("Progressao");
        if ( count( $arProgressoes ) ) {
            $stJs = listarFaixas( $arProgressoes, false );
        }
        $stJs .= MontaNorma($_REQUEST ["inCodNormaTxt"]);
        //$stJs .= MontaNiveis ();
    break;

    case "limparFormulario":
        Sessao::write("Progressao","");
        $stJs  = "f.reset (); ";
        $stJs .= "d.getElementById('spnListaProgressao').innerHTML = '';";
    break;
}
if($stJs)
    SistemaLegado::executaFrameOculto($stJs);

?>

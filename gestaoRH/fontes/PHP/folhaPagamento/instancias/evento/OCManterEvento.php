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
* Página de Oculto - Folha de Pagamento - Evento
* Data de Criação   : 10/02/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Lucas Leusin Oaigen

* @ignore

$Id: OCManterEvento.php 66449 2016-08-30 18:45:21Z michel $

* Casos de uso: uc-04.05.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php";
include_once CAM_GA_ADM_NEGOCIO."RFuncao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoVerbaRescisoriaMTE.class.php";

$stCtrl = $request->get('stCtrl');

$obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
$obRFuncao               = new RFuncao;

function alteraLinkAbas($boExecuta = false)
{
    $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_2'].href = \"javascript:buscaValor('layer_2');HabilitaLayer('layer_2');\";\n";
    $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_3'].href = \"javascript:buscaValor('layer_3');HabilitaLayer('layer_3');\";\n";
    $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_4'].href = \"javascript:buscaValor('layer_4');HabilitaLayer('layer_4');\";\n";
    $stJs .= "window.parent.frames['telaPrincipal'].document.links['id_layer_5'].href = \"javascript:buscaValor('layer_5');HabilitaLayer('layer_5');\";\n";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto( $stJs );
    } else {
        return $stJs;
    }
}

function preencheMascClassificacao($boExecuta=false, $stMascClassificacao, $stAba)
{
    if ($stMascClassificacao != "") {
        $obROrcamentoClassificacaoDespesa = new ROrcamentoClassificacaoDespesa;
        $obROrcamentoClassificacaoDespesa->setMascClassificacao( $stMascClassificacao );
        $obROrcamentoClassificacaoDespesa->listar($rsClassificacaoDespesa);
        $inNumLinhas = $rsClassificacaoDespesa->getNumLinhas();
        if ($inNumLinhas > 0) {
            $stDescricaoDespesa = $rsClassificacaoDespesa->getCampo("descricao");
            $js .= 'd.getElementById("stRubricaDespesa'.$stAba.'").innerHTML = "'.$stDescricaoDespesa.'";';

            if ($stAba == 'Sal') {
                $stAba = 'Fer';
                $js .= 'if (window.parent.frames["telaPrincipal"].document.forms[0].stMascClassificacao'.$stAba.'.value == "") {';
                $js .= '   window.parent.frames["telaPrincipal"].document.forms[0].stMascClassificacao'.$stAba.'.value = "'.$rsClassificacaoDespesa->getCampo('mascara_classificacao').'";';
                $js .= '   window.parent.frames["telaPrincipal"].document.getElementById("stRubricaDespesa'.$stAba.'").innerHTML = "'.$stDescricaoDespesa.'";';
                $js .= '}';

                $stAba = '13o';
                $js .= 'if (window.parent.frames["telaPrincipal"].document.forms[0].stMascClassificacao'.$stAba.'.value == "") {';
                $js .= '   window.parent.frames["telaPrincipal"].document.forms[0].stMascClassificacao'.$stAba.'.value = "'.$rsClassificacaoDespesa->getCampo('mascara_classificacao').'";';
                $js .= '   window.parent.frames["telaPrincipal"].document.getElementById("stRubricaDespesa'.$stAba.'").innerHTML = "'.$stDescricaoDespesa.'";';
                $js .= '}';
            }

        }
    }

    if ( $stMascClassificacao == "" || ($stMascClassificacao != "" && $inNumLinhas <= 0) ) {
        $js .= 'f.stMascClassificacao'.$stAba.'.value = "";';
        $js .= 'f.stMascClassificacao'.$stAba.'.focus();';
        $js .= 'd.getElementById("stRubricaDespesa'.$stAba.'").innerHTML = "&nbsp;";';
        if ($stMascClassificacao != "" && $inNumLinhas <= 0) {
            sistemaLegado::exibeAviso("Valor inválido. (".$stMascClassificacao.")"," "," ");
        }
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($js);
    } else {
        return $js;
    }
}

function preencheCargoEspecialidade($boExecuta=false , $arPostSubDivisao  , $stAba)
{
    global $obRFolhaPagamentoEvento;

    $stJs .= "limpaSelect(f.inCodCargoDisponiveis".$stAba.",0); \n";
    $stJs .= "limpaSelect(f.inCodCargoSelecionados".$stAba.",0); \n";

    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addSubDivisao();
    if (count($arPostSubDivisao)) {
        foreach ($arPostSubDivisao as $arRegimeSubdivisao) {
            if ( is_array($arRegimeSubdivisao) ) {
                $stCodSubDivisao .= $arRegimeSubdivisao[2].",";
            } else {
                $arRegimeSubdivisao = explode("/",$arRegimeSubdivisao);
                $stCodSubDivisao .= $arRegimeSubdivisao[1].",";
            }
        }
    }
    if ( !empty( $stCodSubDivisao ) ) {
        $stCodSubDivisao = substr($stCodSubDivisao,0,strlen($stCodSubDivisao)-1);
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roUltimoSubDivisao->setCodSubDivisao( $stCodSubDivisao );
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roUltimoSubDivisao->listarCargoEspecialidade( $rsCargo );
        $inCount = 0;
        while (!$rsCargo->eof()) {
            if ( $rsCargo->getCampo('cod_especialidade') ) {
                $stOption = $rsCargo->getCampo('cod_cargo')."/".$rsCargo->getCampo('cod_especialidade');
                $stValue  = $rsCargo->getCampo('descr_cargo')."/".$rsCargo->getCampo('descr_espec');
            } else {
                $stOption = $rsCargo->getCampo('cod_cargo');
                $stValue  = $rsCargo->getCampo('descr_cargo');
            }
            $stJs .= "f.inCodCargoDisponiveis".$stAba."[".$inCount."] = new Option('$stValue','$stOption',''); \n";
            $rsCargo->proximo();
            $inCount++;
        }
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaListaCaso($arDados  , $stAba)
{
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arDados );

    if ($rsRecordSet->getNumLinhas() > 0) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Particularidades cadastradas" );
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
        $obLista->ultimoCabecalho->addConteudo( "Função" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Alterar" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "Javascript:processaCaso('montaAlteraCaso".$stAba."');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Excluir" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "Javascript:processaCaso('excluiCaso".$stAba."');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stDescricaoCaso" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("ESQUERDA");
        $obLista->ultimoDado->setCampo( "stFuncao" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "d.getElementById('spnLista".$stAba."').innerHTML = '".$stHtml."';";

    return $stJs;

}

function incluiCaso($stAba , $inIndice)
{
    global $stCtrl;
    $boInclusao = empty( $inIndice );
    if ( (Sessao::read('stAcao'.$stAba) == "alterar") && $boInclusao ) {

        SistemaLegado::exibeAviso('Não é possível incluir particularidade durante uma alteração.');

    } elseif ( (Sessao::read('stAcao'.$stAba) != "alterar") && ($stCtrl == "alteraCaso".$stAba) ) {

        SistemaLegado::exibeAviso('Selecione uma particularidade para alterar.');

    } else {
        $obRFuncao   = new RFuncao;
        $obErro      = new Erro;
        $arElementos = array();
        $stDescricao                 = $_POST['stDescricao'.$stAba];
        $inCodigoTipoMedia           = $_POST['inCodigoTipoMedia'.$stAba];
        $boConsProporcaoAdiantamento = $_POST["boConsProporcaoAdiantamento"];
        $boProporcionalizarAbono     = $_POST["boProporcionalizarAbono"];
        $inCodFuncao                 = $_POST['inCodFuncao'.$stAba];
        $arCodSubDivisaoSelecionados = $_POST['inCodSubDivisaoSelecionados'.$stAba];
        $arCodCargoSelecionados      = $_POST['inCodCargoSelecionados'.$stAba];

        if ( empty($stDescricao) )
            $obErro->setDescricao('Preencha a descrição para esta particularidade.');
        elseif ( empty($inCodFuncao) )
            $obErro->setDescricao('Selecione função para esta particularidade.');

        if ($_POST['hdnNatureza'] != 'Base') {
            if ( empty($arCodSubDivisaoSelecionados) )
                $obErro->setDescricao('Selecione subdivisões para esta particularidade.');
            elseif ( empty($arCodCargoSelecionados) )
                $obErro->setDescricao('Selecione cargos para esta particularidade.');
        } else {
            if ( count(Sessao::read('Caso'.$stAba)) and $stCtrl == "incluiCaso".$stAba ) {
                $obErro->setDescricao('Não é possível inserir mais de uma particularidade para um evento de natureza Base.');
            }
        }
        if ( $obErro->ocorreu() ) {
            SistemaLegado::exibeAviso( $obErro->getDescricao() );
        } else {

            //Verifica se o regime/subdivisao/cargo já esta inserido
            $arCasosAba = Sessao::read('Caso'.$stAba);
            if (isset($arCasosAba) and $_POST['hdnNatureza'] != 'Base') {
                foreach ($arCodSubDivisaoSelecionados as $inCodSubDivisaoSelecionado) {
                    $arCargosUsadosPelaSubDivisao = array();
                    $inCont = 1;
                    foreach ($arCasosAba as $arCaso) {
                        //Caso $inIndice esteja setado eh alteracao, nao podendo considerar o proprio indice
                        if (in_array($inCodSubDivisaoSelecionado,$arCaso['arSubDivisao']) && $inIndice != $inCont) {
                            foreach ($arCaso['arCargo'] as $inCodCargo) {
                                $arCargosUsadosPelaSubDivisao[] = $inCodCargo;
                                $arFuncao[$inCodCargo] = $arCaso['stFuncao'];
                            }
                        }
                        $inCont++;
                    }
                    foreach ($arCodCargoSelecionados as $inCodCargoSelecionado) {
                        if (in_array($inCodCargoSelecionado,$arCargosUsadosPelaSubDivisao)) {
                            $arTmp = explode('/',$inCodSubDivisaoSelecionado);
                            $obRPessoalSubdivisao = new RPessoalSubdivisao( new RPessoalRegime );
                            $obRPessoalSubdivisao->setCodSubdivisao( $arTmp[1] );
                            $obRPessoalSubdivisao->listarSubdivisao($rsSubdivisao);
                            $stNomeSubDivisao = $arTmp[1]."/".$rsSubdivisao->getCampo('nom_sub_divisao');
                            $arTmp = explode('/',$inCodCargoSelecionado);
                            $obRPessoalCargo = new RPessoalCargo;
                            $obRPessoalCargo->setCodCargo($arTmp[0]);
                            $obRPessoalCargo->listarCargo($rsCargo);
                            $stNomeCargo = $arTmp[0]."/".$rsCargo->getCampo('descricao');
                            $obErro->setDescricao($stNomeSubDivisao." - ".$stNomeCargo." já está configurado para a função ".$arFuncao[$inCodCargoSelecionado].".");
                            break;
                        }
                    }
                    if ($obErro->ocorreu())
                        break;
                }
            }
            if ($obErro->ocorreu()) {
                SistemaLegado::exibeAviso( $obErro->getDescricao() );
            } else {

                if ($boInclusao) {
                    $arCasoAba = Sessao::read('Caso'.$stAba);
                    $arElementos['inId'] = $arCasoAba[count( $arCasoAba ) - 1]['inId'] + 1;
                } else {
                    $arElementos['inId'] = $inIndice;
                }
                $arElementos['stDescricaoCaso']            = $stDescricao;
                $arElementos['inCodigoTipoMedia']          = $inCodigoTipoMedia;
                $arElementos["boConsProporcaoAdiantamento"]= $boConsProporcaoAdiantamento;
                $arElementos["boProporcionalizarAbono"]    = $boProporcionalizarAbono;
                $arElementos['inCodFuncao']                = $inCodFuncao;
                $arTmpFuncao = explode('.',$inCodFuncao);
                $obRFuncao->obRBiblioteca->roRModulo->setCodModulo($arTmpFuncao[0]);
                $obRFuncao->obRBiblioteca->setCodigoBiblioteca($arTmpFuncao[1]);
                $obRFuncao->setCodFuncao($arTmpFuncao[2]);
                $obRFuncao->consultar();
                $arElementos['stFuncao']                   = $obRFuncao->getNomeFuncao();
                if ($_POST['hdnNatureza'] != 'Base') {
                    foreach ($arCodSubDivisaoSelecionados as $campo => $valor) {
                        $arElementos['arSubDivisao'][$campo] = $valor;
                    }
                    foreach ($arCodCargoSelecionados as $campo => $valor) {
                        $arElementos['arCargo'][$campo] = $valor;
                    }
                }
                switch ( Sessao::read('inAba') ) {
                    case 2:
                        $arElementos['eventosBaseSal'] = Sessao::read('eventosBaseSal');
                        Sessao::write('eventosBaseSal',"");
                    break;
                    case 3:
                        $arElementos['eventosBaseFer'] = Sessao::read('eventosBaseFer');
                        Sessao::write('eventosBaseFer',"");
                    break;
                    case 4:
                        $arElementos['eventosBase13o'] = Sessao::read('eventosBase13o');
                        Sessao::write('eventosBase13o',"");
                    break;
                    case 5:
                        $arElementos['eventosBaseRes'] = Sessao::read('eventosBaseRes');
                        Sessao::write('eventosBaseRes',"");
                    break;
                }
                if ($boInclusao) {
                    #sessao->transf['Caso'.$stAba][] = $arElementos;
                    $arCasoAba = Sessao::read('Caso'.$stAba);
                    $arCasoAba[] = $arElementos;
                    Sessao::write('Caso'.$stAba,$arCasoAba);
                } else {
                    $inI = 0;
                    $arCasoAba = Sessao::read('Caso'.$stAba);
                    foreach ($arCasoAba as $arTmp) {
                        if ( $arTmp['inId'] == $inIndice )
                            break;
                        $inI++;
                    }
                    #sessao->transf['Caso'.$stAba][$inI] = $arElementos;
                    $arCasoAba[$inI] = $arElementos;
                    Sessao::write('Caso'.$stAba,$arCasoAba);
                }
                $stJs .= montaListaCaso( Sessao::read('Caso'.$stAba) , $stAba );
                $stJs .= limpaCamposCaso($stAba);
                Sessao::write('stAcao'.$stAba,"incluir");

                return $stJs;
            }
        }
    }
}

function limpaCamposCaso($stAba)
{
    global $obRFolhaPagamentoEvento;
    $stJs .= "f.stDescricao".$stAba.".value = '';                          \n";
    if ($stAba == "13o") {
        $stJs .= "f.boConsProporcaoAdiantamento.checked = true;\n";
    }
    if ($stAba == "Fer") {
        $stJs .= "f.boProporcionalizarAbono.checked = true;\n";
    }
    if ($stAba == "Fer"  or $stAba == "13o" or $stAba == "Res") {
        $stJs .= "f.inCodigoTipoMedia".$stAba.".value = '';                    \n";
        $stJs .= "f.stCodigoTipoMedia".$stAba.".value = '';                    \n";
        $stJs .= "d.getElementById('stObservacao".$stAba."').innerHTML = '&nbsp;';\n";
    }
    if ($_POST['hdnNatureza'] != 'Base') {
        $stJs .= "limpaSelect(f.inCodSubDivisaoSelecionados".$stAba.",0);      \n";
        $stJs .= "limpaSelect(f.inCodCargoDisponiveis".$stAba.",0);            \n";
        $stJs .= "limpaSelect(f.inCodCargoSelecionados".$stAba.",0);           \n";
    }
    $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;'; \n";
    $stJs .= "f.inCodFuncao".$stAba.".value = '';                          \n";
    if ($_POST['hdnNatureza'] != 'Base') {
        switch (Sessao::read('inAba')) {
            case 2:
                $stJs .= "d.getElementById('spnEventosBaseSalCadastrados').innerHTML = '';\n";
            break;
            case 3:
                $stJs .= "d.getElementById('spnEventosBaseFerCadastrados').innerHTML = '';\n";
            break;
            case 4:
                $stJs .= "d.getElementById('spnEventosBase13oCadastrados').innerHTML = '';\n";
            break;
            case 5:
                $stJs .= "d.getElementById('spnEventosBaseResCadastrados').innerHTML = '';\n";
            break;
        }

        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addSubDivisao();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roUltimoSubDivisao->listarSubDivisao($rsSubDivisao);

        $inCount = 0;
        while (!$rsSubDivisao->eof()) {
            $stValue  = $rsSubDivisao->getCampo('cod_regime')."/".$rsSubDivisao->getCampo('cod_sub_divisao');
            $stOption = $rsSubDivisao->getCampo('nom_regime')."/".$rsSubDivisao->getCampo('nom_sub_divisao');
            $stJs .= "f.inCodSubDivisaoDisponiveis".$stAba."[".$inCount."] = new Option('".$stOption."','".$stValue."',''); \n";
            $inCount++;
            $rsSubDivisao->proximo();
        }
    }

    Sessao::write('stAcao'.$stAba,"incluir");

    return $stJs;
}

function excluiCaso($stAba , $inId)
{
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $arTemp = array();
    $obErro =  new erro;
    $arCasoAba = Sessao::read('Caso'.$stAba);
    foreach ($arCasoAba as $arCaso) {
        if ($arCaso['inId'] != $inId) {
            $arTemp[] = $arCaso;
        } else {
            $obRFolhaPagamentoEvento->addConfiguracaoEvento();
            switch ($stAba) {
                case('Sal'):
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao(1);
                break;
                case('Fer'):
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao(2);
                break;
                case('13o'):
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao(3);
                break;
                case('Res'):
                    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao(4);
                break;
            }
            $obRFolhaPagamentoEvento->setCodEvento($_POST['inCodEvento']);
            $obRFolhaPagamentoEvento->listarEventosDeEventosBase($rsEventoBase);
            if ( $rsEventoBase->getNumLinhas() > 0 ) {
                $arTemp[] = $arCaso;
                $obErro->setDescricao("Esse Evento Base está sendo utilizado por um Evento e não pode ser removido.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        #unset( sessao->transf['Caso'.$stAba] );
        #sessao->transf['Caso'.$stAba] = $arTemp;
        Sessao::write('Caso'.$stAba,$arTemp);

        $stJs = montaListaCaso( Sessao::read('Caso'.$stAba) , $stAba );
        if ( $inId == $_POST['hdn'.$stAba] )
            $stJs .= limpaCamposCaso($stAba);
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function montaAlteraCaso($stAba , $inId)
{
    $obRPessoalRegime = new RPessoalRegime;
    $obRPessoalSubdivisao = new RPessoalSubdivisao($obRPessoalRegime);
    $arTemp = array();

    $arCasoAba = Sessao::read('Caso'.$stAba);
    foreach ($arCasoAba as $arCaso) {
        if ($arCaso['inId'] == $inId) {
            $arTemp = $arCaso;
            break;
        }
    }
    $stJs .= limpaCamposCaso($stAba);
    Sessao::write('stAcao'.$stAba,"alterar");
    $stJs .= "f.hdn".$stAba.".value = '".$arTemp['inId']."'; \n";
    $stJs .= "f.stDescricao".$stAba.".value = '".$arTemp['stDescricaoCaso']."'; \n";
    if ($stAba == "13o") {
        if ($arTemp["boConsProporcaoAdiantamento"] == "true") {
            $stJs .= "f.boConsProporcaoAdiantamento.checked = true;\n";
        } else {
            $stJs .= "f.boConsProporcaoAdiantamento.checked = false;\n";
        }
    }
    if ($stAba == "Fer") {
        $stJs .= "f.boProporcionalizarAbono.value = 'true';\n";
        if ($arTemp["boProporcionalizarAbono"] == "true") {
            $stJs .= "f.boProporcionalizarAbono.checked = true;\n";
        } else {
            $stJs .= "f.boProporcionalizarAbono.checked = false;\n";
        }
    }
    if ($stAba == "Fer" or $stAba == "13o" or $stAba == "Res") {
        $stJs .= "f.inCodigoTipoMedia".$stAba.".value = '".$arTemp['inCodigoTipoMedia']."';\n";
        $stJs .= "f.stCodigoTipoMedia".$stAba.".value = '".$arTemp['inCodigoTipoMedia']."';\n";
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
        $obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
        $stFiltro = " WHERE codigo = '".$arTemp['inCodigoTipoMedia']."'";
        $obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro);
        $stObservacao = ( $rsTipoMedia->getNumLinhas() == 1 ) ? $rsTipoMedia->getCampo("observacao") : "&nbsp;";
        $stJs .= "d.getElementById('stObservacao$stAba').innerHTML = '$stObservacao'; \n";
    }

    if ($_POST['hdnNatureza'] != 'Base') {
        //Insere a subdivisao na caixa selecionados e procura para remover da caixa disponiveis
        $inI = 0;
        foreach ($arTemp['arSubDivisao'] as $stRegimeSubDivisao) {
            $arRegimeSubdivisao = explode("/",$stRegimeSubDivisao);
            $obRPessoalSubdivisao->setCodSubdivisao($arRegimeSubdivisao[1]);
            $obRPessoalSubdivisao->listarSubDivisao($rsSubDivisao);
            $stValue = $stRegimeSubDivisao;
            $stOption  = $rsSubDivisao->getCampo('nom_regime')."/".$rsSubDivisao->getCampo('nom_sub_divisao');
            $stJs .= "f.inCodSubDivisaoSelecionados".$stAba."[".$inI."] = new Option('".$stOption."','".$stValue."',''); \n";
            $stJs .= "inI = 0;                                                                                                     \n";
            $stJs .= "boAchou = false;                                                                                             \n";
            $stJs .= "obSubDivisaoDisp = f.inCodSubDivisaoDisponiveis".$stAba.";                                                   \n";
            $stJs .= "while (obSubDivisaoDisp.options[inI] && !boAchou) {                                                        \n";
            $stJs .= "    if (obSubDivisaoDisp.options[inI].value == '".$stValue."') {                                 \n";
            $stJs .= "        obSubDivisaoDisp.options[inI] = null;                                                                \n";
            $stJs .= "        boAchou = true;                                                                                      \n";
            $stJs .= "    }                                                                                                        \n";
            $stJs .= "    inI++;                                                                                                   \n";
            $stJs .= "}                                                                                                            \n";
            $inI++;
        }
        $stJs .= preencheCargoEspecialidade( false ,$arTemp['arSubDivisao'] , $stAba );

        //Insere o cargo na caixa selecionados e procura para remover da caixa disponiveis
        $inI = 0;
        $obRPessoalCargo = new RPessoalCargo;
        $obRPessoalEspecialidade = new RPessoalEspecialidade($obRPessoalCargo);
        foreach ($arTemp['arCargo'] as $stCargoEspecialidade) {
            $arTemp2              = explode("/",$stCargoEspecialidade);
            if ($arTemp2[1] != "") {
                $arCargoEspecialidade[] = $arTemp2[0]."-".$arTemp2[1];
            } else {
                $arCargoEspecialidade[] = $arTemp2[0]."-".'0';
            }
        }
        $obRPessoalCargo->listarCargosEspecialidadePorCodigo($rsCargos,$arCargoEspecialidade);
        while (!$rsCargos->eof()) {
            if ( $rsCargos->getCampo('cod_especialidade') ) {
                $stOption = $rsCargos->getCampo('descricao')."/".$rsCargos->getCampo('descricao_especialidade');
                $stValue  = $rsCargos->getCampo('cod_cargo')."/".$rsCargos->getCampo('cod_especialidade');
            } else {
                $stOption = $rsCargos->getCampo('descricao');
                $stValue  = $rsCargos->getCampo('cod_cargo');
            }
            $stJs .= "f.inCodCargoSelecionados".$stAba."[".($rsCargos->getCorrente()-1)."] = new Option('".$stOption."','".$stValue."','');     \n";
            $stJs .= "inI = 0;                                                                                                     \n";
            $stJs .= "boAchou = false;                                                                                             \n";
            $stJs .= "obCargoDisp = f.inCodCargoDisponiveis".$stAba.";                                                             \n";
            $stJs .= "while (obCargoDisp.options[inI] && !boAchou) {                                                             \n";
            $stJs .= "    if (obCargoDisp.options[inI].value == '".$stValue."') {                                                \n";
            $stJs .= "        obCargoDisp.options[inI] = null;                                                                     \n";
            $stJs .= "        boAchou = true;                                                                                      \n";
            $stJs .= "    }                                                                                                        \n";
            $stJs .= "    inI++;                                                                                                   \n";
            $stJs .= "}                                                                                                            \n";
            $rsCargos->proximo();
        }
        $obRPessoalEspecialidade->listarEspecialidadesPorCodigo($rsEspecialidades,$arCodEspecialidades);

        //Monta a lista de eventos base
        switch (Sessao::read('inAba')) {
            case 2:
                $rsRecordSet = ( is_object($arTemp['eventosBaseSal']) )? $arTemp['eventosBaseSal'] : new recordset;
                Sessao::write('eventosBaseSal',$rsRecordSet);
                $stJs .= gerarListaEventoBase();
            break;
            case 3:
                $rsRecordSet = ( is_object($arTemp['eventosBaseFer']) )? $arTemp['eventosBaseFer'] :  new recordset;
                Sessao::write('eventosBaseFer',$rsRecordSet);
                $stJs .= gerarListaEventoBase();
            break;
            case 4:
                $rsRecordSet = ( is_object($arTemp['eventosBase13o']) )? $arTemp['eventosBase13o'] : new recordset;
                Sessao::write('eventosBase13o',$rsRecordSet);
                $stJs .= gerarListaEventoBase();
            break;
            case 5:
                $rsRecordSet = ( is_object($arTemp['eventosBaseRes']) )? $arTemp['eventosBaseRes'] : new recordset;
                Sessao::write('eventosBaseRes',$rsRecordSet);
                $stJs .= gerarListaEventoBase();
            break;
        }
    }

    $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '".$arTemp['stFuncao']."';                                    \n";
    $stJs .= "f.inCodFuncao".$stAba.".value = '".$arTemp['inCodFuncao']."';                                                    \n";
    $stJs .= "f.stDescricao".$stAba.".focus();                                                                                 \n";

    return $stJs;

}

function montaAlteracao()
{
    $stJs .= montaListaCaso( Sessao::read('CasoSal') , 'Sal' );
    $stJs .= montaListaCaso( Sessao::read('CasoFer') , 'Fer' );
    $stJs .= montaListaCaso( Sessao::read('Caso13o') , '13o' );
    $stJs .= montaListaCaso( Sessao::read('CasoRes') , 'Res' );

    if ( !empty($_POST['stMascClassificacaoSal']) )
        $stJs .= preencheMascClassificacao( false , $_POST["stMascClassificacaoSal"], 'Sal');

    if ( !empty($_POST['stMascClassificacaoFer']) )
        $stJs .= preencheMascClassificacao( false , $_POST["stMascClassificacaoFer"], 'Fer');

    if ( !empty($_POST['stMascClassificacao13o']) )
        $stJs .= preencheMascClassificacao( false , $_POST["stMascClassificacao13o"], '13o');

    if ( !empty($_POST['stMascClassificacaoRes']) )
        $stJs .= preencheMascClassificacao( false , $_POST["stMascClassificacaoRes"], 'Res');

    $stJs .= preencheSequencia();
    $stJs .= alteraLinkAbas();
    if ( Sessao::read('natureza') != 'B' ) {
        $stJs .= gerarSpans();
        $stJs .= gerarSpanBase();
        if (Sessao::read('natureza') == 'I') {
            $stJs .= montaSpanContraChequeNatureza();
        }
    } else {
        $stJs .= montaSpanContraChequeNatureza();
    }

    return $stJs;
}

function gerarSpanBase()
{
    $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
    $obRFolhaPagamentoConfiguracao->consultar();
    $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

    $arAbaNome = array();
    $arAbaNome[2] = "Sal";
    $arAbaNome[3] = "Fer";
    $arAbaNome[4] = "13o";
    $arAbaNome[5] = "Res";
    Sessao::write('arAbaNome',$arAbaNome);

    for($i=2; $i<=5; $i++){
        $stNomeAba = $arAbaNome[$i];

        $obBscEvento = new BuscaInner;
        $obBscEvento->setRotulo                        ( "Evento Base"                                );
        $obBscEvento->setTitle                         ( "Selecione as bases utilizadas pelo evento." );
        $obBscEvento->obCampoCod->setValue             ( $inCodigo                                    );
        $obBscEvento->obCampoCod->setAlign             ( "LEFT"                                       );
        $obBscEvento->obCampoCod->setMascara           ( $stMascaraEvento                             );
        $obBscEvento->obCampoCod->setPreencheComZeros  ( "E"                                          );
        $obBscEvento->obCampoCod->obEvento->setOnBlur  ( " buscaValor('preencherEventoBase'); "       );
        $obBscEvento->setId                            ( "inCampoInner".$stNomeAba                    );
        $obBscEvento->obCampoCod->setName              ( "inCodigo".$stNomeAba                        );
        $obBscEvento->setFuncaoBusca                   ( "abrePopUp('".CAM_GRH_FOL_POPUPS."movimentacaoFinanceira/FLManterRegistroEvento.php','frm','inCodigo".$stNomeAba."','inCampoInner".$stNomeAba."','','".Sessao::getId()."&boBase=true&boEventoSistema=false&boEventoBase=true','800','550')" );

        $obBtnIncluir = new Button;
        $obBtnIncluir->setValue             ( "Incluir Base"                     );
        $obBtnIncluir->setDisabled          ( true                               );
        $obBtnIncluir->obEvento->setOnClick ( "buscaValor('incluirEventoBase');" );
        $obBtnIncluir->setName              ( "btnIncluirEventoBase".$stNomeAba  );
        $obBtnIncluir->setId                ( "btnIncluirEventoBase".$stNomeAba  );

        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btnLimparEventoBase"             );
        $obBtnLimpar->setValue             ( "Limpar"                          );
        $obBtnLimpar->obEvento->setOnClick ( "buscaValor('limparEventoBase');" );

        $obSpanEventosBaseCadastrados = new Span;
        $obSpanEventosBaseCadastrados->setId ( "spnEventosBase".$stNomeAba."Cadastrados" );

        $obFormulario = new Formulario;
        $obFormulario->addComponente     ( $obBscEvento                      );
        $obFormulario->agrupaComponentes ( array($obBtnIncluir,$obBtnLimpar) );
        $obFormulario->addSpan           ( $obSpanEventosBaseCadastrados     );
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $stJs .= " jq_('#spnEventoBase".$stNomeAba."').html('".$stHtml."'); \n";
    }

    return $stJs;
}

function incluirEventoBase()
{
    $obErro = new erro;
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;

    $arAbaNome = Sessao::read('arAbaNome');
    $stNomeAba = $arAbaNome[Sessao::read('inAba')];

    switch (Sessao::read('inAba')) {
        case 2:
            $inCodConfiguracao = 1;
        break;
        case 3:
            $inCodConfiguracao = 2;
        break;
        case 4:
            $inCodConfiguracao = 3;
        break;
        case 5:
            $inCodConfiguracao = 4;
        break;
    }

    $rsEventosBase  = ( is_object(Sessao::read('eventosBase'.$stNomeAba)) ) ? Sessao::read('eventosBase'.$stNomeAba) : new recordset;
    $inCodigo       = $_POST['inCodigo'.$stNomeAba];
    $stDescricao    = $_POST['HdninCodigo'.$stNomeAba];

    $obRFolhaPagamentoEvento->setCodigo($inCodigo);
    $obRFolhaPagamentoEvento->listarEvento($rsEvento);
    $inCodEvento = $rsEvento->getCampo('cod_evento');
    $stTimestamp = $rsEvento->getCampo('timestamp');
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->listarConfiguracaoEventoCaso($rsConfiguracaoEventoCaso,$inCodEvento,$stTimestamp,$inCodConfiguracao);

    if ( $rsConfiguracaoEventoCaso->getNumLinhas() < 0 ) {
        $obErro->setDescricao("Esse Evento Base não possui configuração para esta aba.");
    }
    if ( !$obErro->ocorreu() ) {
        while (!$rsEventosBase->eof()) {
            if ( $rsEventosBase->getCampo('codigo') == $inCodigo ) {
                $obErro->setDescricao('Esse Evento Base já está inserido na lista.');
            }
            $rsEventosBase->proximo();
        }
    }
    if ( !$obErro->ocorreu() and $inCodigo == "" ) {
        $obErro->setDescricao('Campo Evento Base inválido().');
    }
    if ( !$obErro->ocorreu() ) {
        $obRFolhaPagamentoEvento->setCodigo($inCodigo);
        $obRFolhaPagamentoEvento->setNatureza('B');
        $obRFolhaPagamentoEvento->listarEvento($rsRecordSet);
        if ( $rsRecordSet->getNumLinhas() < 0 ) {
            $obErro->setDescricao('Esse Evento não é um Evento Base.');
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsEventosBase->setUltimoElemento();
        $inId = $rsEventosBase->getCampo('inId')+1;
        $arEventosBase = ( is_array($rsEventosBase->getElementos()) ) ? $rsEventosBase->getElementos() : array();
        $arTemp = array();
        $obRFolhaPagamentoEvento->setCodEvento($rsRecordSet->getCampo('cod_evento'));
        $obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao(Sessao::read('inAba')-1);
        $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->listarCasoEvento($rsCasoEventoBase);
        $arTemp['inId']             = $inId;
        $arTemp['cod_evento']       = $rsCasoEventoBase->getCampo('cod_evento');
        $arTemp['cod_caso']         = $rsCasoEventoBase->getCampo('cod_caso');
        $arTemp['cod_configuracao'] = $rsCasoEventoBase->getCampo('cod_configuracao');
        $arTemp['timestamp']        = $rsCasoEventoBase->getCampo('timestamp');
        $arTemp['codigo']           = $inCodigo;
        $arTemp['descricao']        = $stDescricao;
        $arEventosBase[]            = $arTemp;
        $rsEventosBase = new recordset;
        $rsEventosBase->preenche( $arEventosBase );

        Sessao::write('eventosBase'.$stNomeAba,$rsEventosBase);

        $stJs .= "f.inCodigo".$stNomeAba.".value = '';                                 \n";
        $stJs .= "f.HdninCodigo".$stNomeAba.".value = '';                              \n";
        $stJs .= "d.getElementById('inCampoInner".$stNomeAba."').innerHTML = '&nbsp;'; \n";
        $stJs .= gerarListaEventoBase();
        $stJs .= " jq_('#btnIncluirEventoBase".$stNomeAba."').prop('disabled',true);   \n";
    } else {
        $stJs .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirEventoBase()
{
    $arAbaNome = Sessao::read('arAbaNome');
    $stNomeAba = $arAbaNome[Sessao::read('inAba')];

    $rsEventosBase = Sessao::read('eventosBase'.$stNomeAba);

    $arEventosBase = $rsEventosBase->getElementos();
    $arTemp = array();
    foreach ($arEventosBase as $arEventoBase) {
        if ($arEventoBase['inId'] != $_GET['inId']) {
            $arTemp[] = $arEventoBase;
        }
    }
    $rsEventosBase = new recordset;
    $rsEventosBase->preenche( $arTemp );
    
    Sessao::write('eventosBase'.$stNomeAba,$rsEventosBase);

    $stJs .= gerarListaEventoBase();

    return $stJs;
}

function limparEventoBase()
{
    $arAbaNome = Sessao::read('arAbaNome');
    $stNomeAba = $arAbaNome[Sessao::read('inAba')];

    $stJs .= "f.inCodigo".$stNomeAba.".value = '';                                 \n";
    $stJs .= "d.getElementById('inCampoInner".$stNomeAba."').innerHTML = '&nbsp;'; \n";
    $stJs .= " jq_('#btnIncluirEventoBase".$stNomeAba."').prop('disabled',true);   \n";

    return $stJs;
}

function gerarListaEventoBase()
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Eventos de Base Cadastrados" );

    $arAbaNome = Sessao::read('arAbaNome');
    $stNomeAba = $arAbaNome[Sessao::read('inAba')];

    $rsEventosBase = Sessao::read('eventosBase'.$stNomeAba);
    $stIdSpan      = "spnEventosBase".$stNomeAba."Cadastrados";

    $obLista->setRecordSet( $rsEventosBase );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 45 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "codigo" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "Excluir" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "Javascript:processaCaso('excluirEventoBase');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "d.getElementById('".$stIdSpan."').innerHTML = '".$stHtml."';";

    return $stJs;
}

function preencherEventoBase($request)
{
    global $obRFolhaPagamentoEvento;

    $arAbaNome = Sessao::read('arAbaNome');
    $stNomeAba = $arAbaNome[Sessao::read('inAba')];

    $inCodigo = $request->get('inCodigo'.$stNomeAba,'');

    $obRFolhaPagamentoEvento->setCodigo($inCodigo);
    $obRFolhaPagamentoEvento->setNatureza("B");
    $obRFolhaPagamentoEvento->setEventoSistema( "false" );
    $obRFolhaPagamentoEvento->listarEvento($rsEventosBase);

    if ( $rsEventosBase->getNumLinhas() > 0 && $inCodigo != "" ) {
        $stJs .= " jq_('#inCampoInner".$stNomeAba."').html('".$rsEventosBase->getCampo('descricao')."');          \n";
        $stJs .= " jq_('input[name=HdninCodigo".$stNomeAba."]').val('".$rsEventosBase->getCampo('descricao')."'); \n";
        $stJs .= " jq_('#btnIncluirEventoBase".$stNomeAba."').removeProp('disabled');                             \n";
    } else {
        $stJs .= " jq_('input[name=inCodigo".$stNomeAba."]').val('');                \n";
        $stJs .= " jq_('input[name=HdninCodigo".$stNomeAba."]').val('');             \n";
        $stJs .= " jq_('#inCampoInner".$stNomeAba."').html('&nbsp;');                \n";
        $stJs .= " jq_('#btnIncluirEventoBase".$stNomeAba."').prop('disabled',true); \n";
    }

    return $stJs;
}

function montaTipoVariavel()
{
    $obFormulario = new Formulario;

    if ($_POST['stTipo'] == 'V') {

        $obRdbLimiteIde = new SimNao();
        $obRdbLimiteIde->setRotulo ( 'Mês/Ano Limite para Cálculo' );
        $obRdbLimiteIde->setName   ( 'boLimiteCalculo' );
        if ($_POST['boLimiteCalculo'] != 'S')
            $obRdbLimiteIde->setChecked( 'Não' );
        $obRdbLimiteIde->setTitle  ( 'Informe se o evento possuirá uma data limite para cálculo ou não' );
        $obRdbLimiteIde->obRadioSim->obEvento->setOnClick( "buscaValor('montaTipoVariavel');" );
        $obRdbLimiteIde->obRadioNao->obEvento->setOnClick( "buscaValor('montaTipoVariavel');" );
        $obFormulario->addComponente( $obRdbLimiteIde  );

        if ($_POST['boLimiteCalculo'] == 'S') {
            $obRdbParcelaIde = new SimNao();
            $obRdbParcelaIde->setRotulo ( 'Apresentar Parcela' );
            $obRdbParcelaIde->setName   ( 'boApresentaParcela' );
            $obRdbParcelaIde->setTitle  ( 'Informe se a parcela será mostrada no cálculo ou não' );
            $obFormulario->addComponente( $obRdbParcelaIde );
        }

    }

    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnTipoVariavel').innerHTML = '".$obFormulario->getHTML()."'; \n";

    return $stJs;
}

function montaSpanBase()
{
    $obFormulario = new Formulario;
    $stNatureza = ( isset($_POST['natureza']) ) ? $_POST['natureza'] : $_POST['hdnNatureza'];
    $stNatureza = ( $stNatureza == 'Provento' ) ? 'P'                : $stNatureza;
    $stNatureza = ( $stNatureza == 'Desconto' ) ? 'D'                : $stNatureza;

    if ($stNatureza != 'B') {

        $obRdbTipoFixoIde = new Radio;
        $obRdbTipoFixoIde->setRotulo          ( "Tipo"                                         );
        $obRdbTipoFixoIde->setName            ( "stTipo"                                       );
        $obRdbTipoFixoIde->setLabel           ( "Fixo"                                         );
        $obRdbTipoFixoIde->setValue           ( "F"                                            );
        $obRdbTipoFixoIde->setTitle           ( "Informe se o evento será tratado como evento fixo ou variável." );
        $obRdbTipoFixoIde->setNull            ( false                                          );
        $obRdbTipoFixoIde->setChecked         ( true                                           );
        $obRdbTipoFixoIde->obEvento->setOnClick( "buscaValor('montaTipoVariavel');"            );

        $obRdbTipoVariavelIde = new Radio;
        $obRdbTipoVariavelIde->setName        ( "stTipo"                                       );
        $obRdbTipoVariavelIde->setLabel       ( "Variável"                                     );
        $obRdbTipoVariavelIde->setValue       ( "V"                                            );
        $obRdbTipoVariavelIde->setNull        ( false                                          );
        $obRdbTipoVariavelIde->setChecked     ( false                                          );
        $obRdbTipoVariavelIde->obEvento->setOnClick( "buscaValor('montaTipoVariavel');"        );

        $obRdbFixarValorIde = new Radio;
        $obRdbFixarValorIde->setRotulo          ( "Fixar Evento"                                 );
        $obRdbFixarValorIde->setName            ( "stFixar"                                      );
        $obRdbFixarValorIde->setLabel           ( "Valor"                                        );
        $obRdbFixarValorIde->setValue           ( "V"                                            );
        $obRdbFixarValorIde->setTitle           ( "Informe se o evento será fixado por valor ou por quantidade." );
        $obRdbFixarValorIde->setNull            ( false                                          );
        $obRdbFixarValorIde->setChecked         ( true                                           );

        $obRdbFixarQuantidadeIde = new Radio;
        $obRdbFixarQuantidadeIde->setName        ( "stFixar"                                      );
        $obRdbFixarQuantidadeIde->setLabel       ( "Quantidade"                                   );
        $obRdbFixarQuantidadeIde->setValue       ( "Q"                                            );
        $obRdbFixarQuantidadeIde->setNull        ( false                                          );
        $obRdbFixarQuantidadeIde->setChecked     ( false                                          );

        $obTxtValorIde = new Numerico;
        $obTxtValorIde->setName                          ( "nuValor"                                          );
        $obTxtValorIde->setAlign                         ( "RIGHT"                                            );
        $obTxtValorIde->setRotulo                        ( "Valor/Quantidade Padrão"                          );
        $obTxtValorIde->setMaxLength                     ( 14                                                 );
        $obTxtValorIde->setMaxValue                      ( 999999999.99                                       );
        $obTxtValorIde->setSize                          ( 12                                                 );
        $obTxtValorIde->setDecimais                      ( 2                                                  );
        $obTxtValorIde->setNegativo                      ( false                                              );
        $obTxtValorIde->setNull                          ( true                                               );
        $obTxtValorIde->setValue                         ( $nuValor                                           );
        $obTxtValorIde->setTitle                         ( "Informe o valor do evento"                        );

        $obTxtUnidadeQuantitativaIde = new Numerico;
        $obTxtUnidadeQuantitativaIde->setName            ( "nuUnidadeQuantitativa"                            );
        $obTxtUnidadeQuantitativaIde->setAlign           ( "RIGHT"                                            );
        $obTxtUnidadeQuantitativaIde->setRotulo          ( "Unidade Quantitativa"                             );
        $obTxtUnidadeQuantitativaIde->setMaxLength       ( 14                                                 );
        $obTxtUnidadeQuantitativaIde->setMaxValue        ( 999999999.99                                       );
        $obTxtUnidadeQuantitativaIde->setSize            ( 12                                                 );
        $obTxtUnidadeQuantitativaIde->setDecimais        ( 2                                                  );
        $obTxtUnidadeQuantitativaIde->setNegativo        ( false                                              );
        $obTxtUnidadeQuantitativaIde->setNull            ( true                                               );
        $obTxtUnidadeQuantitativaIde->setValue           ( $nuUnidadeQuantitativa                             );
        $obTxtUnidadeQuantitativaIde->setTitle           ( "Informe a quantitativa ou valor que representa a integralidade para uma competência" );

        $obFormulario->addComponenteComposto($obRdbTipoFixoIde  ,$obRdbTipoVariavelIde    );
        $obFormulario->addComponenteComposto($obRdbFixarValorIde,$obRdbFixarQuantidadeIde );
        $obFormulario->addComponente        ($obTxtValorIde                               );
        $obFormulario->addComponente        ($obTxtUnidadeQuantitativaIde                 );

    }

    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnBase').innerHTML = '".$obFormulario->getHTML()."'; \n";
    $stJs .= "d.getElementById('spnTipoVariavel').innerHTML = ''; \n";

    return $stJs;
}

function preencheSequencia()
{
    if ($inCodSequencia = $_POST['inCodSequencia']) {
        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
        $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->setCodSequencia($inCodSequencia);
        $obRFolhaPagamentoEvento->obRFolhaPagamentoSequencia->listarSequencia($rsSequencia);
        $rsSequencia->addFormatacao("complemento","N_TO_BR");
        $stDescricao   .= $rsSequencia->getCampo('descricao');
        $stComplemento .= $rsSequencia->getCampo('complemento');
    } else {
        $stDescricao   .= "&nbsp;";
        $stComplemento .= "&nbsp;";
    }
    $stJs .= "d.getElementById('stSequenciaDescricao').innerHTML = '".$stDescricao."' \n";
    $stJs .= "d.getElementById('stSequenciaComplemento').innerHTML = '".$stComplemento."' \n";

    return $stJs;
}

function buscaFuncao($stAba)
{
    if ($_POST["inCodFuncao".$stAba]) {
        $arCodFuncao = explode('.',$_POST["inCodFuncao".$stAba]);
        $obRFuncao = new RFuncao;
        $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
        $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
        $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
        $obRFuncao->consultar();
        $stNomeFuncao = $obRFuncao->getNomeFuncao();
        if ( !empty($stNomeFuncao) ) {
            $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '".$stNomeFuncao."';\n";
        } else {
            $stJs .= "f.inCodFuncao".$stAba.".value = '';\n";
            $stJs .= "f.inCodFuncao".$stAba.".focus();\n";
            $stJs .= "d.getElementById('stFuncao".$stAba."').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Função informada não existe. (".$_POST["inCodFuncao".$stAba].")','form','erro','".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function identificaLayer($inAba)
{
    Sessao::write('inAba',$inAba);
}

function gerarSpans($boExecuta=false)
{
    //Carrega combos de subdivisoes
    $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
    $obRFolhaPagamentoEvento->addConfiguracaoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->addCasoEvento();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->addSubDivisao();
    $obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->roUltimoCasoEvento->roUltimoSubDivisao->listarSubDivisao($rsSubDivisao);
    $rsRecordSet = new recordset;
    $stNatureza = ( isset($_POST['natureza']) ) ? $_POST['natureza'] : $_POST['hdnNatureza'];
    $stNatureza = ( $stNatureza == 'Provento' ) ? 'P'                : $stNatureza;
    $stNatureza = ( $stNatureza == 'Desconto' ) ? 'D'                : $stNatureza;
    if ($stNatureza != 'B') {
        $arRecordSet = array($rsSubDivisao,$rsRecordSet,$rsRecordSet,$rsRecordSet);
        $stJs .= gerarSpan1($arRecordSet);
        $stJs .= gerarSpan2($arRecordSet);
        $stJs .= gerarSpan3($arRecordSet);
        $stJs .= gerarSpan4($arRecordSet);
        $stJs .= gerarSpanBase();        
        $stJs .= montaVerbaRescisoriaMTE($stNatureza);
        
        if ($stNatureza == 'I') {
            $stJs .= montaSpanContraChequeNatureza();
        } else {
            $stJs .= "d.getElementById('spnContraChequeNatureza').innerHTML = '';  \n";
        }
        $stJs .= "f.hdnNatureza.value = '".$stNatureza."';              \n";
    } else {
        $stJs .= "d.getElementById('spnSpan1').innerHTML = '';          \n";
        $stJs .= "d.getElementById('spnSpan2').innerHTML = '';          \n";
        $stJs .= "d.getElementById('spnSpan3').innerHTML = '';          \n";
        $stJs .= "d.getElementById('spnSpan4').innerHTML = '';          \n";
        $stJs .= "d.getElementById('spnEventoBaseSal').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnEventoBaseFer').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnEventoBase13o').innerHTML = '';  \n";
        $stJs .= "d.getElementById('spnEventoBaseRes').innerHTML = '';  \n";
        $stJs .= "f.hdnNatureza.value = 'Base';                         \n";
        $stJs .= montaSpanContraChequeNatureza();
    }
    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan1($arRecordSet,$boExecuta=false)
{
    $obCmbRegimeSubDivisaoSal = new SelectMultiplo();
    $obCmbRegimeSubDivisaoSal->setName         ( 'inCodSubDivisaoSal'                                               );
    $obCmbRegimeSubDivisaoSal->setRotulo       ( "*Regime/Subdivisões"                                              );
    $obCmbRegimeSubDivisaoSal->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de salário" );
    $obCmbRegimeSubDivisaoSal->SetNomeLista1   ( 'inCodSubDivisaoDisponiveisSal'                                    );
    $obCmbRegimeSubDivisaoSal->setCampoId1     ( '[cod_regime]/[cod_sub_divisao]'                                   );
    $obCmbRegimeSubDivisaoSal->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                   );
    $obCmbRegimeSubDivisaoSal->setStyle1       ( "width: 300px"                                                     );
    $obCmbRegimeSubDivisaoSal->SetRecord1      ( $arRecordSet[0]                                                    );
    $obCmbRegimeSubDivisaoSal->SetNomeLista2   ( 'inCodSubDivisaoSelecionadosSal'                                   );
    $obCmbRegimeSubDivisaoSal->setCampoId2     ( '[cod_regime]/[cod_sub_divisao]'                                   );
    $obCmbRegimeSubDivisaoSal->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                   );
    $obCmbRegimeSubDivisaoSal->setStyle2       ( "width: 300px"                                                     );
    $obCmbRegimeSubDivisaoSal->SetRecord2      ( $arRecordSet[1]                                                    );
    $stOnClick = "selecionaSubDivisao('Sal',true);buscaValor('preencheCargoEspecialidadeSal');selecionaSubDivisao('Sal',false);";
    $obCmbRegimeSubDivisaoSal->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoSal->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoSal->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoSal->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoSal->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $obCmbRegimeSubDivisaoSal->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $obCmbCargoSal = new SelectMultiplo();
    $obCmbCargoSal->setName                    ( 'inCodCargoSal'                                                            );
    $obCmbCargoSal->setRotulo                  ( "*Cargos Vinculados"                                                       );
    $obCmbCargoSal->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de salário"       );
    $obCmbCargoSal->SetNomeLista1              ( 'inCodCargoDisponiveisSal'                                                 );
    $obCmbCargoSal->setCampoId1                ( '[cod_cargo]/[cod_especialidade]'                                          );
    $obCmbCargoSal->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoSal->setStyle1                  ( "width: 300px"                                                             );
    $obCmbCargoSal->SetRecord1                 ( $arRecordSet[2]                                                            );
    $obCmbCargoSal->SetNomeLista2              ( 'inCodCargoSelecionadosSal'                                                );
    $obCmbCargoSal->setCampoId2                ( '[cod_cargo]/[cod_especialidade]'                                          );
    $obCmbCargoSal->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoSal->setStyle2                  ( "width: 300px"                                                             );
    $obCmbCargoSal->SetRecord2                 ( $arRecordSet[3]                                                            );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obCmbRegimeSubDivisaoSal);
    $obFormulario->addComponente($obCmbCargoSal);
    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnSpan1').innerHTML = '".$obFormulario->getHTML()."'; \n";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan2($arRecordSet,$boExecuta=false)
{
    $rsRecord1 = $arRecordSet[0];
    $rsRecord1->setPrimeiroElemento();
    $rsRecord2 = $arRecordSet[1];
    $rsRecord2->setPrimeiroElemento();
    $rsRecord3 = $arRecordSet[2];
    $rsRecord3->setPrimeiroElemento();
    $rsRecord4 = $arRecordSet[3];
    $rsRecord4->setPrimeiroElemento();

    $obCmbRegimeSubDivisaoFer = new SelectMultiplo();
    $obCmbRegimeSubDivisaoFer->setName         ( 'inCodSubDivisaoFer'                                                       );
    $obCmbRegimeSubDivisaoFer->setRotulo       ( "*Regime/Subdivisões"                                                      );
    $obCmbRegimeSubDivisaoFer->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de férias"          );
    $obCmbRegimeSubDivisaoFer->SetNomeLista1   ( 'inCodSubDivisaoDisponiveisFer'                                            );
    $obCmbRegimeSubDivisaoFer->setCampoId1     ( '[cod_regime]/[cod_sub_divisao]'                         );
    $obCmbRegimeSubDivisaoFer->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                           );
    $obCmbRegimeSubDivisaoFer->setStyle1       ( "width: 300px"                                                             );
    $obCmbRegimeSubDivisaoFer->SetRecord1      ( $rsRecord1                                                            );
    $obCmbRegimeSubDivisaoFer->SetNomeLista2   ( 'inCodSubDivisaoSelecionadosFer'                                           );
    $obCmbRegimeSubDivisaoFer->setCampoId2     ( '[cod_regime]/[cod_sub_divisao]'                         );
    $obCmbRegimeSubDivisaoFer->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                           );
    $obCmbRegimeSubDivisaoFer->setStyle2       ( "width: 300px"                                                             );
    $obCmbRegimeSubDivisaoFer->SetRecord2      ( $rsRecord2                                                            );
    $stOnClick = "selecionaSubDivisao('Fer',true);buscaValor('preencheCargoEspecialidadeFer');selecionaSubDivisao('Fer',false);";
    $obCmbRegimeSubDivisaoFer->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoFer->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoFer->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoFer->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoFer->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $obCmbRegimeSubDivisaoFer->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $obCmbCargoFer = new SelectMultiplo();
    $obCmbCargoFer->setName                    ( 'inCodCargoFer'                                                            );
    $obCmbCargoFer->setRotulo                  ( "*Cargos Vinculados"                                                       );
    $obCmbCargoFer->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de férias"        );
    $obCmbCargoFer->SetNomeLista1              ( 'inCodCargoDisponiveisFer'                                                 );
    $obCmbCargoFer->setCampoId1                ( '[cod_cargo]/[cod_especialidade]'                                                              );
    $obCmbCargoFer->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoFer->setStyle1                  ( "width: 300px"                                                             );
    $obCmbCargoFer->SetRecord1                 ( $rsRecord3                                                            );
    $obCmbCargoFer->SetNomeLista2              ( 'inCodCargoSelecionadosFer'                                                );
    $obCmbCargoFer->setCampoId2                ( '[cod_cargo]/[cod_especialidade]'              );
    $obCmbCargoFer->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoFer->setStyle2                  ( "width: 300px"                                                             );
    $obCmbCargoFer->SetRecord2                 ( $rsRecord4                                                            );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obCmbRegimeSubDivisaoFer);
    $obFormulario->addComponente($obCmbCargoFer);
    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnSpan2').innerHTML = '".$obFormulario->getHTML()."'; \n";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan3($arRecordSet,$boExecuta=false)
{
    $rsRecord1 = $arRecordSet[0];
    $rsRecord1->setPrimeiroElemento();
    $rsRecord2 = $arRecordSet[1];
    $rsRecord2->setPrimeiroElemento();
    $rsRecord3 = $arRecordSet[2];
    $rsRecord3->setPrimeiroElemento();
    $rsRecord4 = $arRecordSet[3];
    $rsRecord4->setPrimeiroElemento();

    $obCmbRegimeSubDivisao13o = new SelectMultiplo();
    $obCmbRegimeSubDivisao13o->setName         ( 'inCodSubDivisao13o'                                                           );
    $obCmbRegimeSubDivisao13o->setRotulo       ( "*Regime/Subdivisões"                                                          );
    $obCmbRegimeSubDivisao13o->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de 13o salário"         );
    $obCmbRegimeSubDivisao13o->SetNomeLista1   ( 'inCodSubDivisaoDisponiveis13o'                                                );
    $obCmbRegimeSubDivisao13o->setCampoId1     ( '[cod_regime]/[cod_sub_divisao]'                             );
    $obCmbRegimeSubDivisao13o->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                               );
    $obCmbRegimeSubDivisao13o->setStyle1       ( "width: 300px"                                                                 );
    $obCmbRegimeSubDivisao13o->SetRecord1      ( $rsRecord1                                                                );
    $obCmbRegimeSubDivisao13o->SetNomeLista2   ( 'inCodSubDivisaoSelecionados13o'                                               );
    $obCmbRegimeSubDivisao13o->setCampoId2     ( '[cod_regime]/[cod_sub_divisao]'                             );
    $obCmbRegimeSubDivisao13o->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                               );
    $obCmbRegimeSubDivisao13o->setStyle2       ( "width: 300px"                                                                 );
    $obCmbRegimeSubDivisao13o->SetRecord2      ( $rsRecord2                                                                );
    $stOnClick = "selecionaSubDivisao('13o',true);buscaValor('preencheCargoEspecialidade13o');selecionaSubDivisao('13o',false);";
    $obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisao13o->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisao13o->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $obCmbRegimeSubDivisao13o->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $obCmbCargo13o = new SelectMultiplo();
    $obCmbCargo13o->setName                    ( 'inCodCargo13o'                                                                );
    $obCmbCargo13o->setRotulo                  ( "*Cargos Vinculados"                                                           );
    $obCmbCargo13o->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de 13o salário"       );
    $obCmbCargo13o->SetNomeLista1              ( 'inCodCargoDisponiveis13o'                                                     );
    $obCmbCargo13o->setCampoId1                ( '[cod_cargo]/[cod_especialidade]'                                                                  );
    $obCmbCargo13o->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                                  );
    $obCmbCargo13o->setStyle1                  ( "width: 300px"                                                                 );
    $obCmbCargo13o->SetRecord1                 ( $rsRecord3                                                                );
    $obCmbCargo13o->SetNomeLista2              ( 'inCodCargoSelecionados13o'                                                    );
    $obCmbCargo13o->setCampoId2                ( '[cod_cargo]/[cod_especialidade]'                  );
    $obCmbCargo13o->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                                  );
    $obCmbCargo13o->setStyle2                  ( "width: 300px"                                                                 );
    $obCmbCargo13o->SetRecord2                 ( $rsRecord4                                                                );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obCmbRegimeSubDivisao13o);
    $obFormulario->addComponente($obCmbCargo13o);
    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnSpan3').innerHTML = '".$obFormulario->getHTML()."'; \n";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function gerarSpan4($arRecordSet,$boExecuta=false)
{
    $rsRecord1 = $arRecordSet[0];
    $rsRecord1->setPrimeiroElemento();
    $rsRecord2 = $arRecordSet[1];
    $rsRecord2->setPrimeiroElemento();
    $rsRecord3 = $arRecordSet[2];
    $rsRecord3->setPrimeiroElemento();
    $rsRecord4 = $arRecordSet[3];
    $rsRecord4->setPrimeiroElemento();

    $obCmbRegimeSubDivisaoRes = new SelectMultiplo();
    $obCmbRegimeSubDivisaoRes->setName         ( 'inCodSubDivisaoRes'                                                       );
    $obCmbRegimeSubDivisaoRes->setRotulo       ( "*Regime/Subdivisões"                                                      );
    $obCmbRegimeSubDivisaoRes->setTitle        ( "Selecione os regimes/subdivisões associados ao evento de rescisão"        );
    $obCmbRegimeSubDivisaoRes->SetNomeLista1   ( 'inCodSubDivisaoDisponiveisRes'                                            );
    $obCmbRegimeSubDivisaoRes->setCampoId1     ( '[cod_regime]/[cod_sub_divisao]'                         );
    $obCmbRegimeSubDivisaoRes->setCampoDesc1   ( '[nom_regime]/[nom_sub_divisao]'                                           );
    $obCmbRegimeSubDivisaoRes->setStyle1       ( "width: 300px"                                                             );
    $obCmbRegimeSubDivisaoRes->SetRecord1      ( $rsRecord1                                                            );
    $obCmbRegimeSubDivisaoRes->SetNomeLista2   ( 'inCodSubDivisaoSelecionadosRes'                                           );
    $obCmbRegimeSubDivisaoRes->setCampoId2     ( '[cod_regime]/[cod_sub_divisao]'                         );
    $obCmbRegimeSubDivisaoRes->setCampoDesc2   ( '[nom_regime]/[nom_sub_divisao]'                                           );
    $obCmbRegimeSubDivisaoRes->setStyle2       ( "width: 300px"                                                             );
    $obCmbRegimeSubDivisaoRes->SetRecord2      ( $rsRecord2                                                            );
    $stOnClick = "selecionaSubDivisao('Res',true);buscaValor('preencheCargoEspecialidadeRes');selecionaSubDivisao('Res',false);";
    $obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao1->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao2->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao3->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoRes->obGerenciaSelects->obBotao4->obEvento->setOnClick( $stOnClick );
    $obCmbRegimeSubDivisaoRes->obSelect1->obEvento->setOnDblClick( $stOnClick );
    $obCmbRegimeSubDivisaoRes->obSelect2->obEvento->setOnDblClick( $stOnClick );

    $obCmbCargoRes = new SelectMultiplo();
    $obCmbCargoRes->setName                    ( 'inCodCargoRes'                                                            );
    $obCmbCargoRes->setRotulo                  ( "*Cargos Vinculados"                                                       );
    $obCmbCargoRes->setTitle                   ( "Selecione os cargos/especialidades associados ao evento de rescisão"      );
    $obCmbCargoRes->SetNomeLista1              ( 'inCodCargoDisponiveisRes'                                                 );
    $obCmbCargoRes->setCampoId1                ( '[cod_cargo]/[cod_especialidade]'                                                              );
    $obCmbCargoRes->setCampoDesc1              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoRes->setStyle1                  ( "width: 300px"                                                             );
    $obCmbCargoRes->SetRecord1                 ( $rsRecord3                                                            );
    $obCmbCargoRes->SetNomeLista2              ( 'inCodCargoSelecionadosRes'                                                );
    $obCmbCargoRes->setCampoId2                ( '[cod_cargo]/[cod_especialidade]'              );
    $obCmbCargoRes->setCampoDesc2              ( '[descr_cargo]/[descr_espec]'                                              );
    $obCmbCargoRes->setStyle2                  ( "width: 300px"                                                             );
    $obCmbCargoRes->SetRecord2                 ( $rsRecord4                                                            );

    $obFormulario = new Formulario;
    $obFormulario->addComponente($obCmbRegimeSubDivisaoRes);
    $obFormulario->addComponente($obCmbCargoRes);
    $obFormulario->montaInnerHTML();
    $stJs .= "d.getElementById('spnSpan4').innerHTML = '".$obFormulario->getHTML()."'; \n";

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function processarHdnSal($boExecuta=false)
{
    if ($_POST['hdnNatureza']!='Base') {
        $stJs .= "eval(document.frm.hdnSpan1.value)";
    }
    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function preencherObservacao($stAba)
{
    $rsTipoMedia = new Recordset;
    if ($_POST['inCodigoTipoMedia'.$stAba] != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoMedia.class.php");
        $obTFolhaPagamentoTipoMedia = new TFolhaPagamentoTipoMedia;
        $stFiltro = " WHERE codigo = '".$_POST['inCodigoTipoMedia'.$stAba]."'";
        $obTFolhaPagamentoTipoMedia->recuperaTodos($rsTipoMedia,$stFiltro);
    }
    $stObservacao = ( $rsTipoMedia->getNumLinhas() == 1 ) ? $rsTipoMedia->getCampo("observacao") : "&nbsp;";
    $stJs .= "d.getElementById('stObservacao$stAba').innerHTML = '$stObservacao'; \n";

    return $stJs;
}

function montaSpanContraChequeNatureza()
{
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
    $stFiltro = !empty($_POST['inCodEvento']) ? " AND FPE.cod_evento=".$_POST['inCodEvento'] : '';
    $obTFolhaPagamentoEvento->recuperaEventos($rsRecordSet, $stFiltro);

    $boChecked = $rsRecordSet->getCampo('apresentar_contracheque');

    $obRdbContraChequeNaturezaNao = new Radio;
    $obRdbContraChequeNaturezaNao->setRotulo          ( "Apresentar no ContraCheque "                      );
    $obRdbContraChequeNaturezaNao->setName            ( "boApresentarContraCheque"                          );
    $obRdbContraChequeNaturezaNao->setLabel           ( "Não"                                              );
    $obRdbContraChequeNaturezaNao->setValue           ( "f"                                                );
    $obRdbContraChequeNaturezaNao->setChecked         ( ( $boChecked == 't' ) ? false : true               );

    $obRdbContraChequeNaturezaSim = new Radio;
    $obRdbContraChequeNaturezaSim->setRotulo          ( "Apresentar no ContraCheque "                      );
    $obRdbContraChequeNaturezaSim->setName            ( "boApresentarContraCheque"                          );
    $obRdbContraChequeNaturezaSim->setLabel           ( "Sim"                                              );
    $obRdbContraChequeNaturezaSim->setValue           ( "t"                                                );
    $obRdbContraChequeNaturezaSim->setChecked         ( ( $boChecked == 't' ) ? true : false               );

    $obFormulario = new Formulario;
    $obFormulario->agrupaComponentes( array($obRdbContraChequeNaturezaNao,$obRdbContraChequeNaturezaSim ) );
    $obFormulario->montaInnerHTML();

    $stJs .= "d.getElementById('spnContraChequeNatureza').innerHTML = '".$obFormulario->getHTML()."' \n";

    return $stJs;
}

function montaVerbaRescisoriaMTE($stNatureza)
{
    switch ($stNatureza) {
        case 'P':
            $rsRecordSet = new Recordset;
            $obTFolhaPagamentoVerbaRescisoriaMTE = new TFolhaPagamentoVerbaRescisoriaMTE;
            $obTFolhaPagamentoVerbaRescisoriaMTE->recuperaTodos($rsRecordSet, " WHERE natureza = 'P'",  " ORDER BY nom_verba ASC");
            
            $obTxtVerbaRescisoriaMTE = new TextBox;
            $obTxtVerbaRescisoriaMTE->setRotulo       ( "Verba Rescisória MTE"               );
            $obTxtVerbaRescisoriaMTE->setName         ( "inCodVerbaRescisoriaMTE"            );
            $obTxtVerbaRescisoriaMTE->setId           ( "inCodVerbaRescisoriaMTE"            );
            $obTxtVerbaRescisoriaMTE->setTitle        ( "Informe a verba rescisória MTE."    );
            $obTxtVerbaRescisoriaMTE->setValue        ( $_REQUEST['stHdnCodVerbaRescisoriaMTE'] );
            $obTxtVerbaRescisoriaMTE->setNull         ( false                                );
            
            $obCmbVerbaRescisoriaMTE = new Select;
            $obCmbVerbaRescisoriaMTE->setRotulo       ( "Verba Rescisória MTE"               );
            $obCmbVerbaRescisoriaMTE->setName         ( "stCodVerbaRescisoriaMTE"            );
            $obCmbVerbaRescisoriaMTE->setStyle        ( "width: 400px"                       );
            $obCmbVerbaRescisoriaMTE->setTitle        ( "Informe a verba rescisória MTE."    );
            $obCmbVerbaRescisoriaMTE->setCampoID      ( "cod_verba"                          );
            $obCmbVerbaRescisoriaMTE->setCampoDesc    ( "nom_verba"                          );
            $obCmbVerbaRescisoriaMTE->addOption       ( "", "Selecione"                      );
            $obCmbVerbaRescisoriaMTE->setValue        ( $_REQUEST['stHdnCodVerbaRescisoriaMTE'] );
            $obCmbVerbaRescisoriaMTE->setNull         ( false                                );
            $obCmbVerbaRescisoriaMTE->preencheCombo   ( $rsRecordSet                         );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponenteComposto($obTxtVerbaRescisoriaMTE,$obCmbVerbaRescisoriaMTE);
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
        break;
    
    
        case 'D':
            $rsRecordSet = new Recordset;
            $obTFolhaPagamentoVerbaRescisoriaMTE = new TFolhaPagamentoVerbaRescisoriaMTE;
            $obTFolhaPagamentoVerbaRescisoriaMTE->recuperaTodos($rsRecordSet, " WHERE natureza = 'D'",  " ORDER BY nom_verba ASC");

            $obTxtVerbaRescisoriaMTE = new TextBox;
            $obTxtVerbaRescisoriaMTE->setRotulo       ( "Verba Rescisória MTE"               );
            $obTxtVerbaRescisoriaMTE->setName         ( "inCodVerbaRescisoriaMTE"            );
            $obTxtVerbaRescisoriaMTE->setId           ( "inCodVerbaRescisoriaMTE"            );
            $obTxtVerbaRescisoriaMTE->setTitle        ( "Informe a verba rescisória MTE."    );
            $obTxtVerbaRescisoriaMTE->setValue        ( $_REQUEST['stHdnCodVerbaRescisoriaMTE'] );
            $obTxtVerbaRescisoriaMTE->setNull         ( false                                );

            
            $obCmbVerbaRescisoriaMTE = new Select;
            $obCmbVerbaRescisoriaMTE->setRotulo       ( "Verba Rescisória MTE"               );
            $obCmbVerbaRescisoriaMTE->setName         ( "stCodVerbaRescisoriaMTE"            );
            $obCmbVerbaRescisoriaMTE->setStyle        ( "width: 400px"                       );
            $obCmbVerbaRescisoriaMTE->setTitle        ( "Informe a verba rescisória MTE."    );
            $obCmbVerbaRescisoriaMTE->setCampoID      ( "cod_verba"                          );
            $obCmbVerbaRescisoriaMTE->setCampoDesc    ( "nom_verba"                          );
            $obCmbVerbaRescisoriaMTE->addOption       ( "", "Selecione"                      );
            $obCmbVerbaRescisoriaMTE->setValue        ( $_REQUEST['stHdnCodVerbaRescisoriaMTE'] );
            $obCmbVerbaRescisoriaMTE->setNull         ( false                                );
            $obCmbVerbaRescisoriaMTE->preencheCombo   ( $rsRecordSet                         );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponenteComposto($obTxtVerbaRescisoriaMTE,$obCmbVerbaRescisoriaMTE);
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
        break;
    
        default:
            $stHTML = '';
        break;
    }

    $stJs .= " jq_('#spnVerbaRescisoriaMTE').html('".$stHTML."'); \n";

    SistemaLegado::LiberaFrames(true,true);
    
    return $stJs; 
    
}

switch ($stCtrl) {

    case 'preencheMascClassificacaoSal':
        $stJs .= preencheMascClassificacao( false , $_REQUEST["stMascClassificacaoSal"], 'Sal');
    break;

    case 'preencheMascClassificacaoFer':
        $stJs .= preencheMascClassificacao( false , $_REQUEST["stMascClassificacaoFer"], 'Fer');
    break;

    case 'preencheMascClassificacao13o':
        $stJs .= preencheMascClassificacao( false , $_REQUEST["stMascClassificacao13o"], '13o');
    break;

    case 'preencheMascClassificacaoRes':
        $stJs .= preencheMascClassificacao( false , $_REQUEST["stMascClassificacaoRes"], 'Res');
    break;

    case 'preencheCargoEspecialidadeSal':
        $stJs .= preencheCargoEspecialidade( false ,$_POST['inCodSubDivisaoSelecionadosSal'] , 'Sal');
    break;

    case 'preencheCargoEspecialidadeFer':
        $stJs .= preencheCargoEspecialidade( false ,$_POST['inCodSubDivisaoSelecionadosFer'] , 'Fer');
    break;

    case 'preencheCargoEspecialidade13o':
        $stJs .= preencheCargoEspecialidade( false ,$_POST['inCodSubDivisaoSelecionados13o'] , '13o');
    break;

    case 'preencheCargoEspecialidadeRes':
        $stJs .= preencheCargoEspecialidade( false ,$_POST['inCodSubDivisaoSelecionadosRes'] , 'Res');
    break;

    case 'incluiCasoSal':
        $stJs .= incluiCaso( 'Sal' , '' );
    break;

    case 'incluiCasoFer':
        $stJs .= incluiCaso( 'Fer' , '' );
    break;

    case 'incluiCaso13o':
        $stJs .= incluiCaso( '13o' , '' );
    break;

    case 'incluiCasoRes':
        $stJs .= incluiCaso( 'Res' , '' );
    break;

    case 'limpaCamposCasoSal':
        $stJs .= limpaCamposCaso( 'Sal' );
    break;

    case 'limpaCamposCasoFer':
        $stJs .= limpaCamposCaso( 'Fer' );
    break;

    case 'limpaCamposCaso13o':
        $stJs .= limpaCamposCaso( '13o' );
    break;

    case 'limpaCamposCasoRes':
        $stJs .= limpaCamposCaso( 'Res' );
    break;

    case 'excluiCasoSal':
        $stJs .= excluiCaso( 'Sal' , $_GET['inId'] );
    break;

    case 'excluiCasoFer':
        $stJs .= excluiCaso( 'Fer' , $_GET['inId'] );
    break;

    case 'excluiCaso13o':
        $stJs .= excluiCaso( '13o' , $_GET['inId'] );
    break;

    case 'excluiCasoRes':
        $stJs .= excluiCaso( 'Res' , $_GET['inId'] );
    break;

    case 'montaAlteraCasoSal':
        $stJs .= montaAlteraCaso( 'Sal' , $_GET['inId'] );
    break;

    case 'montaAlteraCasoFer':
        $stJs .= montaAlteraCaso( 'Fer' , $_GET['inId'] );
    break;

    case 'montaAlteraCaso13o':
        $stJs .= montaAlteraCaso( '13o' , $_GET['inId'] );
    break;

    case 'montaAlteraCasoRes':
        $stJs .= montaAlteraCaso( 'Res' , $_GET['inId'] );
    break;

    case 'alteraCasoSal':
        $stJs .= incluiCaso( 'Sal' , $_POST['hdnSal'] );
    break;

    case 'alteraCasoFer':
        $stJs .= incluiCaso( 'Fer' , $_POST['hdnFer'] );
    break;

    case 'alteraCaso13o':
        $stJs .= incluiCaso( '13o' , $_POST['hdn13o'] );
    break;

    case 'alteraCasoRes':
        $stJs .= incluiCaso( 'Res' , $_POST['hdnRes'] );
    break;

    case 'exibeAviso':
        SistemaLegado::exibeAviso("Um evento informativo não permite parâmetros para este caso."," "," ");
    break;

    case 'montaAlteracao':
        $stJs .= montaAlteracao();
        SistemaLegado::LiberaFrames(true,true);
    break;

    case 'montaTipoVariavel':
        $stJs .= montaTipoVariavel();
    break;

    case 'preencheSequencia':
        $stJs .= preencheSequencia();
    break;

    case 'buscaFuncaoSal':
        $stJs .= buscaFuncao('Sal');
    break;

    case 'buscaFuncaoFer':
        $stJs .= buscaFuncao('Fer');
    break;

    case 'buscaFuncao13o':
        $stJs .= buscaFuncao('13o');
    break;

    case 'buscaFuncaoRes':
        $stJs .= buscaFuncao('Res');
    break;
    case 'preencherEventoBase':
        $stJs .= preencherEventoBase($request);
    break;
    case 'incluirEventoBase':
        $stJs .= incluirEventoBase();
    break;
    case 'excluirEventoBase':
        $stJs .= excluirEventoBase();
    break;
    case 'limparEventoBase':
        $stJs .= limparEventoBase();
    break;
    case 'layer_2':
        $stJs .= identificaLayer(2);
    break;
    case 'layer_3':
        $stJs .= identificaLayer(3);
    break;
    case 'layer_4':
        $stJs .= identificaLayer(4);
    break;
    case 'layer_5':
        $stJs .= identificaLayer(5);
    break;
    case 'gerarSpans':
        $stJs .= gerarSpans();
        $stJs .= montaSpanBase();
        $stJs .= alteraLinkAbas();
    break;
    case 'processarHdnSal':
        $stJs .= processarHdnSal();
    break;
    case 'preencherObservacaoFer':
        $stJs .= preencherObservacao("Fer");
    break;
    case 'preencherObservacao13o':
        $stJs .= preencherObservacao("13o");
    break;
    case 'preencherObservacaoRes':
        $stJs .= preencherObservacao("Res");
    break;
    case 'montaVerbaRescisoriaMTE':
        $stJs .= montaVerbaRescisoriaMTE();
    break;
}

if ( $stJs )
    SistemaLegado::executaFrameOculto($stJs);

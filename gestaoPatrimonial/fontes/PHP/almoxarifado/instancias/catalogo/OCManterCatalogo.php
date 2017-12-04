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
    * Página Oculta de Funções
    * Data de Criação   : 07/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Id: OCManterCatalogo.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(CAM_GP_ALM_NEGOCIO."RAlmoxarifadoCatalogo.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RAlmoxarifadoCatalogo;

function listaValores($arRecordSet, $executa=true)
{
    $stJs = isset($stJs) ? $stJs : null;
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arRecordSet );

    $rsRecordSet->setPrimeiroElemento();

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Níveis" );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nível" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Máscara" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "nivel" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "mascara" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "descricao" );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('MontaAlteracaoValor');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        if ($_POST['stAcao'] == 'alterar') {
            $obRegra = new RAlmoxarifadoCatalogo;
            $obRegra->setCodigo($_POST['inCodigo']);
            $obErro = $obRegra->verificarClassificacao($obErroVerifica);

            if (!$obErro->ocorreu()) {
                if (!$obErroVerifica->ocorreu()) {
                    $obLista->addAcao();
                    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
                    $obLista->ultimaAcao->setFuncao( true );
                    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluiValor');" );
                    $obLista->ultimaAcao->addCampo("1","inId");
                    $obLista->commitAcao();
                }
            }
        } else {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluiValor');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->commitAcao();
        }

        $obLista->montaHTML();

        $html = $obLista->getHTML();

        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);
    }

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnListaValores').innerHTML = '".$html."';";

    if ($executa==true) {
        SistemaLegado::executaFrameOculto($stJs);
    } else {
        return $stJs;
    }
}

function montaFormulario($boAltera=true)
{
    $obForm = new Form();
    if ($boAltera) {
        $obTxtMascara = new TextBox;
        $obTxtMascara->setRotulo        ( "*Máscara" );
        $obTxtMascara->setTitle         ( "Informe a máscara para este nível." );
        $obTxtMascara->setName          ( "stMascara" );
        $obTxtMascara->setId            ( "stMascara" );
        $obTxtMascara->setValue         ( '' );
        $obTxtMascara->setSize          ( 10 );
        $obTxtMascara->setMaxLength     ( 10 );
        $obTxtMascara->setInteiro       ( true );
        $obTxtMascara->setNull          ( true );
        $obTxtMascara->obEvento->setOnKeyUp("verificaTecla(event, this);");
    } else {
        $obLblMascara = new Label();
        $obLblMascara->setRotulo        ( "Máscara" );
        $obLblMascara->setName          ( "stMascara" );
        $obLblMascara->setId            ( "stMascara" );
        $obLblMascara->setValue			( '' );
    }

    $obTxtDescricaoNivel = new TextBox;
    $obTxtDescricaoNivel->setRotulo        ( "*Descrição" );
    $obTxtDescricaoNivel->setTitle         ( "Informe a descrição do nível." );
    $obTxtDescricaoNivel->setName          ( "stDescricaoNivel" );
    $obTxtDescricaoNivel->setValue         ( '' );
    $obTxtDescricaoNivel->setSize          ( 50 );
    $obTxtDescricaoNivel->setMaxLength     ( 160 );
    $obTxtDescricaoNivel->setNull          ( true );

    $obBtnIncluir= new Button;
    $obBtnIncluir->setName ( "btnIncluir" );
    $obBtnIncluir->setId ( "btnIncluir" );
    $obBtnIncluir->setTipo ( "button" );
    if ($stAcao == 'alterar') {
        $obBtnIncluir->setValue( 'Alterar' );
        $obBtnIncluir->obEvento->setOnClick ( "return AdicionaValores('alteraValor');" );
    } else {
        $obBtnIncluir->setValue( "Incluir" );
        $obBtnIncluir->obEvento->setOnClick ( "return AdicionaValores('MontaValoresLista');" );

    }

    $obBtnLimpar = new Button;
    $obBtnLimpar->setName( "btnLimpar" );
    $obBtnLimpar->setValue( "Limpar" );
    $obBtnLimpar->setTipo( "button" );
    $obBtnLimpar->obEvento->setOnClick ( "limpaValores();" );

    $obFormulario = new Formulario();
    $obFormulario->addForm( $obForm );
    $obFormulario->addTitulo            ( "Dados do Nível" );
    if ($boAltera) {
        $obFormulario->addComponente		( $obTxtMascara );
    } else {
        $obFormulario->addComponente( $obLblMascara );
    }
    $obFormulario->addComponente		( $obTxtDescricaoNivel );
    $obFormulario->defineBarra  		( array($obBtnIncluir , $obBtnLimpar) );

    $obFormulario->montaInnerHTML();

    return $obFormulario->getHTML();

}

$arrayValores = Sessao::read('Valores');

switch ($stCtrl) {
    case "MontaCadastro":
        if ($_REQUEST["inCodigo"]) {
            $obRCadastroDinamico->obRModulo->setCodModulo( $_REQUEST["inCodigo"] );
            $obErro = $obRCadastroDinamico->recuperaCadastros( $rsCadastro );
            if ( !$obErro->ocorreu() ) {
                $stJs .= "limpaSelect(f.inCodCadastro,0); \n";
                $stJs .= "f.inCodCadastro[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsCadastro->eof() ) {
                    $inCodCadastro = $rsCadastro->getCampo( "cod_cadastro" );
                    $stNomCadastro = $rsCadastro->getCampo( "nom_cadastro" );
                    $stJs .= "f.inCodCadastro.options[$inContador] = new Option('".$stNomCadastro."','".$inCodCadastro."');\n";
                    $inContador++;
                    $rsCadastro->proximo();
                }
                SistemaLegado::executaFrameOculto($stJs);
            } else {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando os cadastros: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $stJs .= "limpaSelect(f.inCodCadastro,0); \n";
            $stJs .= "f.inCodCadastro[0] = new Option('Selecione','', 'selected');\n";
            SistemaLegado::executaFrameOculto($stJs);
        }

        break;
    case "MontaValoresListaAltera":

        listaValores( $arrayValores );
        break;
    case "MontaValoresLista":

        $boAdiciona = true;

        $arrayValores = Sessao::read('Valores');

        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche( $arrayValores );
        $rsRecordSet->setUltimoElemento();

        $inUltimoId = $rsRecordSet->getCampo("inId");
        $inUltimoNivel = $rsRecordSet->getCampo("nivel");

        $inProxId = (!isset($inUltimoId)) ? 0 : ($inUltimoId + 1);
        $inProxNivel = (!$inUltimoNivel) ? 1 : ($inUltimoNivel + 1);

        for ($iCount = 0; $iCount < count( $arrayValores ); $iCount++ ) {
            if ($arrayValores[$iCount]["descricao"] == $_REQUEST["stDescricaoNivel"]) {
                $boAdiciona = false;
                $stMensagem = "Não é possível inserir valores iguais!";
            }
        }

        if ($_POST['stMascara'] == '') {
            $boAdiciona = false;
            $stMensagem = "Insira um valor para a máscara!";
        }

        if ($boAdiciona) {
            // Insere novos valores no vetor
            $arElementos['inId']            = $inProxId;
            $arElementos['nivel']           = $inProxNivel;
            $arElementos['mascara']         = $_POST['stMascara'];
            $arElementos['descricao']       = $_POST['stDescricaoNivel'];
            $arElementos['excluir']         = true;

            ##sessao->transf['Valores'][] = $arElementos;
            $arrayValores[] = $arElementos;

            Sessao::write('Valores',$arrayValores);

            $stJs = listaValores( $arrayValores,false );
            $stJs .= "f.stMascara.value = '';";
        //$stJs.=
            $stJs .= "d.getElementById('hdnMascara').value = '';";
            $stJs .= "f.stDescricaoNivel.value='';";
            SistemaLegado::executaFrameOculto($stJs);

        } else {
            $stJs = "alertaAviso('@$stMensagem','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($stJs);
        }

        break;

    case "excluiCatalogo":
        $boVerifica = settype($_GET['boDesabilitaBotao'], 'bool');
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId'];
        $inCount = 0;

        foreach ($arrayValores as $campo => $valor) {
            if ( ($arrayValores[$campo]['inId'] == $id) && (!$arrayValores[$campo]['excluir']) ) {
                $boVerifica = true;
                $stMensagem = "Não é possível excluir este valor da lista.";
            }

            if ( $id != (count($arrayValores)) ) {
                $boVerifica = true;
                $stMensagem = "Somente é possível excluir o último nível da lista.";
            }
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
            foreach ($arrayValores as $campo => $valor) {
                if ($arrayValores[$campo]["inId"] != $id) {

                    $arElementos['inId']        = $inCount + 1;
                    $arElementos['nivel']       = $inCount + 1;
                    /*$arElementos['mascara']     = ##sessao->transf['Valores'][$campo]["mascara"];
                    $arElementos['descricao']   = ##sessao->transf['Valores'][$campo]["descricao"];
                    $arElementos['excluir']     = ##sessao->transf['Valores'][$campo]["excluir"];*/

                    $arElementos['mascara']     = Sessao::read('Valores['.$campo.'][mascara]');
                    $arElementos['descricao']   = Sessao::read('Valores['.$campo.'][descricao]');
                    $arElementos['excluir']     = Sessao::read('Valores['.$campo.'][excluir]');

                    $inCount++;

                    $arTMP[] = $arElementos;
                }
            }
            ##sessao->transf['Valores'] = $arTMP;
            Sessao::write('Valores',$arTMP);
            $stJs  = listaValores( $arTMP , false);
            $stJs .= "f.btnAlterar.disabled = true;";
            $stJs .= "f.btnIncluir.disabled = false;";
        } else {
            $stJs = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "excluiValor":

        $boVerifica = "false";
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId'];
        $inCount = 0;

        if ( $id != count($arrayValores)) {
            $boVerifica = "true";
            $stMensagem = "Somente é possível excluir o último nível da lista.";
        }

        if ($boVerifica == "false") {
            // Lista o Array e remonta sem o ID selecionado
            foreach ($arrayValores as $campo => $valor) {
                if ($arrayValores[$campo]["inId"] != $id) {

                    $arElementos['inId']        = ($arrayValores[$campo]["inId"]) ? $arrayValores[$campo]["inId"] : ($inCount);
                    $arElementos['nivel']       = ($arrayValores[$campo]["nivel"]) ? $arrayValores[$campo]["nivel"] : ($inCount + 1);
                    $arElementos['mascara']     = $arrayValores[$campo]["mascara"];
                    $arElementos['descricao']   = $arrayValores[$campo]["descricao"];
                    $arElementos['excluir']     = $arrayValores[$campo]["excluir"];

                    $inCount++;

                    $arTMP[] = $arElementos;
                }
            }

            ##sessao->transf['Valores'] = $arTMP;
            Sessao::write('Valores',$arTMP);

            $stJs  = listaValores( $arTMP , false);
            $stJs .= "f.btnAlterar.disabled = true;";
            $stJs .= "f.btnIncluir.disabled = false;";
            SistemaLegado::executaFrameOculto($stJs);
        } else {
            $stJs = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($stJs);
        }
        break;
    case "MontaAlteracaoValor":

        $boAltera = true;

        if ($_REQUEST['inCodigo'] != '') {
            include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoClassificacao.class.php");

            $obTCatalogoClassificacao = new TAlmoxarifadoCatalogoClassificacao();
            $stFiltro = " WHERE cod_catalogo = ".$_REQUEST['inCodigo']." ";
            $obTCatalogoClassificacao->recuperaTodos( $rsClassificacao, $stFiltro );
            if ( $rsClassificacao->getNumLinhas() > 0 ) {
                $boAltera = false;
            }
        }

        $stJs .= "d.getElementById('spnFormulario').innerHTML = '".montaFormulario($boAltera)."';";

        $id = $_GET['inId'];

        foreach ($arrayValores as $campo => $valor) {
            if ($arrayValores[$campo]["inId"] == $id) {
                if ($boAltera) {
                    $stJs .= "f.stMascara.value='".$arrayValores[$campo]["mascara"]."';";
                } else {
                    $stJs .= "d.getElementById('stMascara').innerHTML='".$arrayValores[$campo]["mascara"]."';";
                }
                $stJs .= "f.hdnMascara.value='".$arrayValores[$campo]["mascara"]."';";
                $stJs .= "f.stDescricaoNivel.value='".$arrayValores[$campo]["descricao"]."';";
                $stJs .= "f.inIDPos.value='".$id."';";
                $stJs .= "f.btnIncluir.value='Alterar';";
                $stJs .= "f.btnIncluir.setAttribute('onclick','return AdicionaValores(\'alteraValor\')');";
            }
        }

        $stJs .= listaValores( $arrayValores , false);
        SistemaLegado::executaFrameOculto($stJs);
        break;
    case "alteraValor":

        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_POST['inIDPos'];

        if (!isset($_REQUEST['stMascara'])) {
            $_REQUEST['stMascara'] = $_REQUEST['hdnMascara'];
        }

        $obRegra->setCodigo($_REQUEST['inCodigo']);

        if (!$_REQUEST['stMascara']) {
            $boAdiciona = false;
            $stMensagem = 'Campo máscara inválido('.$_REQUEST['stMascara'].').';
        } else {
            $boAdiciona = true;
        }

        if ($boAdiciona) {

            foreach ($arrayValores as $campo => $valor) {
                if ($arrayValores[$campo]["inId"] == $id) {
                    if ($_REQUEST['stAcao'] == 'alterar') {
                        $obRegra->addCatalogoNivel();
                        $obRegra->roCatalogoNivel->setNivel($arrayValores[$campo]['nivel']);
                        $obRegra->roCatalogoNivel->setMascara($_REQUEST['hdnMascara']);

                        $obErro = $obRegra->validarNiveis($obErroValidar);

                        if ($obErroValidar->ocorreu()) {
                            $obRegra->roCatalogoNivel->setMascara($arrayValores[$campo]['mascara']);

                            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                        } else {
                            $obRegra->roCatalogoNivel->setMascara($_POST['hdnMascara']);
                            if ($_REQUEST['hdnMascara']==$_REQUEST['stMascara']) {
                                ##sessao->transf['Valores'][$campo]["mascara"]     = $_REQUEST['hdnMascara'];
                                $arrayValores[$campo]['mascara'] = $_REQUEST['hdnMascara'];
                            } else {
                                ##sessao->transf['Valores'][$campo]["mascara"]     = $_REQUEST['stMascara'];
                                ##Sessao::write('Valores['.$campo.'][mascara]',$_REQUEST['stMascara']);
                                $arrayValores[$campo]['mascara'] = $_REQUEST['stMascara'];
                            }
                            ##sessao->transf['Valores'][$campo]["descricao"]   = $_POST['stDescricaoNivel'];
                            ##Sessao::write('Valores['.$campo.'][descricao]',$_REQUEST['stDescricaoNivel']);
                            $arrayValores[$campo]['descricao'] = $_REQUEST['stDescricaoNivel'];
                        }
                    } else {
                        ##sessao->transf['Valores'][$campo]["mascara"]     = $_POST['stMascara'];
                        ##sessao->transf['Valores'][$campo]["descricao"]   = $_POST['stDescricaoNivel'];

                        $arrayValores[$campo][mascara] = $_REQUEST['stMascara'];
                        $arrayValores[$campo][descricao] = $_REQUEST['stDescricaoNivel'];
                    }
                }
            }

            Sessao::write('Valores', $arrayValores);

            $stJs  = listaValores( $arrayValores, true);
            if ($stAcao == 'incluir') {
                $stJs .= "f.stMascara.value = '';";
                $stJs .= "f.btnIncluir.value='Incluir';";
                $stJs .= "f.btnIncluir.setAttribute('onclick','return AdicionaValores(\'MontaValoresLista\')');";
            } else {
                include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");

                $obTCatalogoItem = new TAlmoxarifadoCatalogoItem();
                $stFiltro = " WHERE cod_catalogo = ".$_REQUEST['inCodigo']." ";
                $obTCatalogoItem->recuperaTodos( $rsItens, $stFiltro );

                if ( $rsItens->getNumLinhas() == -1 ) {
                    $stJs .= "f.stMascara.value = '';";
                    $stJs .= "f.btnIncluir.value='Incluir';";
                    $stJs .= "f.btnIncluir.setAttribute('onclick','return AdicionaValores(\'MontaValoresLista\')');";
                } else {
                    $stJs.= "d.getElementById('spnFormulario').innerHTML='';";
                    $stJs.= "d.getElementById('stMascara').innerHTML='';";
                }
            }
            $stJs .= "f.stDescricaoNivel.value='';";
        } else {
            $stJs .= "alertaAviso('".$stMensagem."','form','erro', '".Sessao::getId()."');\n";
        }
        SistemaLegado::executaFrameOculto($stJs);

        break;
    case 'preencheInner':
        if ($_POST['inCodTipoAtributo']==3 || $_POST['inCodTipoAtributo']==4) {
            $stJs  = listaValores( $arrayValores , false);
        } else {
            $stJs = "f.stValorPadrao.value = '".$arrayValores[0]['valor']."';";
            SistemaLegado::executaFrameOculto($stJs);
        }
        break;
    case 'montaFormulario':

        if ($_REQUEST['inCodigo']) {

            include_once(CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php");

            $obTCatalogoItem = new TAlmoxarifadoCatalogoItem();
            $stFiltro = " WHERE cod_catalogo = ".$_REQUEST['inCodigo']." ";
            $obTCatalogoItem->recuperaTodos( $rsItens, $stFiltro );

            if ( $rsItens->getNumLinhas() == -1 ) {
                echo "document.getElementById('spnFormulario').innerHTML = '".montaFormulario()."';\n";
            }
        } else {
            echo "document.getElementById('spnFormulario').innerHTML = '".montaFormulario()."';\n";
        }

        break;
}
?>

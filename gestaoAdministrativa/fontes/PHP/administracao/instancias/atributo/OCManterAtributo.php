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
* Arquivo de instância para manutenção de atributos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18116 $
$Name$
$Author: cassiano $
$Date: 2006-11-24 09:33:05 -0200 (Sex, 24 Nov 2006) $

Casos de uso: uc-01.03.96
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RAtributoDinamico.class.php");
include_once(CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php");

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obRegra = new RAtributoDinamico;
$obRCadastroDinamico = new RCadastroDinamico;

function listaValores($arRecordSet, $executa=true)
{
    global $obRegra;
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arRecordSet );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Variáveis" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Código" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Ativo" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 45 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("DIREITA");
        $obLista->ultimoDado->setCampo( "cod_valor" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setAlinhamento("CENTRO");
        $obLista->ultimoDado->setCampo( "ativo" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "valor" );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('MontaAlteracaoValor');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluiValor');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $html = $obLista->getHTML();
        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);
    }
    // preenche a lista com innerHTML
    $js .= "if ( d.getElementById('spnListaValores') ) {\n";
    $js .= "    d.getElementById('spnListaValores').innerHTML = '".$html."';\n";
    $js .= "}\n";

    if ($executa==true) {
        $js .= "f.stValorPadrao.value = '';";
        $js .= "f.boAtivo[0].checked=true;";
        $js .= "f.boAtivo[1].checked=false;";
        SistemaLegado::executaFrameOculto($js);
    } else {
        return $js;
    }
}
// Acoes por pagina
switch ($stCtrl) {
    case "MontaModulo":
        if ($_POST['inCodGestao']) {
            $obRAdministracaoGestao = new RAdministracaoGestao;
            $obRAdministracaoGestao->setCodigoGestao( $_POST['inCodGestao'] );
            $obErro = $obRAdministracaoGestao->consultarGestao();
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRAdministracaoGestao->listarModulos();
                if ( !$obErro->ocorreu() ) {
                    $inContador = 1;
                    $stJs .= "limpaSelect(f.inCodModulo,1); \n";
                    $stJs .= "limpaSelect(f.inCodCadastro,1); \n";
                    while ( !$obRAdministracaoGestao->rsRModulo->eof() ) {
                        $obRModulo = $obRAdministracaoGestao->rsRModulo->getObjeto();
                        $stJs .= "f.inCodModulo.options[".$inContador++."] = ";
                        $stJs .= "new Option('".$obRModulo->getNomModulo()."','".$obRModulo->getCodModulo()."');\n";
                        $obRAdministracaoGestao->rsRModulo->proximo();
                    }
                    SistemaLegado::executaFrameOculto($stJs);
                }
            }
            if ( $obErro->ocorreu() ) {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando os módulos: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $stJs .= "limpaSelect(f.inCodModulo,1); \n";
            $stJs .= "limpaSelect(f.inCodCadastro,1); \n";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "MontaCadastro":
        if ($_REQUEST["inCodModulo"]) {
            $obRCadastroDinamico->obRModulo->setCodModulo( $_REQUEST["inCodModulo"] );
            $obErro = $obRCadastroDinamico->recuperaCadastros( $rsCadastro );
            if ( !$obErro->ocorreu() ) {
                $js .= "limpaSelect(f.inCodCadastro,0); \n";
                $js .= "f.inCodCadastro[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsCadastro->eof() ) {
                    $inCodCadastro = $rsCadastro->getCampo( "cod_cadastro" );
                    $stNomCadastro = $rsCadastro->getCampo( "nom_cadastro" );
                    $js .= "f.inCodCadastro.options[$inContador] = new Option('".$stNomCadastro."','".$inCodCadastro."');\n";
                    $inContador++;
                    $rsCadastro->proximo();
                }
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando os cadastros: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $js .= "limpaSelect(f.inCodCadastro,0); \n";
            $js .= "f.inCodCadastro[0] = new Option('Selecione','', 'selected');\n";
            SistemaLegado::executaFrameOculto($js);
        }

    break;
    case "MontaValores":
        $obFormulario = new Formulario;
        if ($_POST['inCodTipoAtributo']) {

            $arSessaoValores = Sessao::read('Valores');

            switch ($_POST['inCodTipoAtributo']) {
                case 1:
                    $obTxtValor = new TextBox;
                    $obTxtValor->setRotulo        ( "Valor Padrão" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setValue         ( $arSessaoValores[0]['valor'] );
                    $obTxtValor->setSize          ( 60 );
                    $obTxtValor->setMaxLength     ( 500 );
                    $obTxtValor->setInteiro       ( true );
                    $obFormulario->addComponente( $obTxtValor );
                break;
                case 2:
                    $obTxtValor = new TextBox;
                    $obTxtValor->setRotulo        ( "Valor Padrão" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setValue         ( $arSessaoValores[0]['valor'] );
                    $obTxtValor->setSize          ( 60 );
                    $obTxtValor->setMaxLength     ( 500 );
                    $obFormulario->addComponente( $obTxtValor );
                break;
                case 5:
                    $obTxtValor = new Data;
                    $obTxtValor->setRotulo        ( "Valor Padrão" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setValue         ( $arSessaoValores[0]['valor'] );
                    $obTxtValor->setSize          ( 60 );
                    $obTxtValor->setMaxLength     ( 500 );
                    $obFormulario->addComponente( $obTxtValor );
                break;
                case 6:
                    $obTxtValor = new TextBox;
                    $obTxtValor->setRotulo        ( "Valor Padrão" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setValue         ( $arSessaoValores[0]['valor'] );
                    $obTxtValor->setSize          ( 60 );
                    $obTxtValor->setMaxLength     ( 500 );
                    $obTxtValor->setFloat         ( true );
                    $obFormulario->addComponente( $obTxtValor );
                break;
                case 7:
                    $obTxtValor = new TextArea;
                    $obTxtValor->setRotulo        ( "Valor Padrão" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setValue         ( str_replace( "\n" , '\n' ,$arSessaoValores[0]['valor']) );                    
                    $obTxtValor->setMaxCaracteres ( 500 );
                    $obFormulario->addComponente  ( $obTxtValor );
                break;
                case 3:
                case 4:
                    $obTxtValor = new TextBox;
                    $obTxtValor->setRotulo        ( "*Valor" );
                    $obTxtValor->setName          ( "stValorPadrao" );
                    $obTxtValor->setSize          ( 60 );
                    $obTxtValor->setMaxLength     ( 500 );

                    $obRdnAtivo = new SimNao;
                    $obRdnAtivo->setRotulo ( "Ativo"   );
                    $obRdnAtivo->setName   ( "boAtivo" );
                    $obRdnAtivo->setNull   ( false     );
                    $obRdnAtivo->setChecked( "Sim"     );
                    $obRdnAtivo->obRadioSim->setValue  ("Sim");
                    $obRdnAtivo->obRadioNao->setValue  ("Não");

                    $obBtnAdicionar = new Button;
                    $obBtnAdicionar->setName ( "btnAdicionar" );
                    $obBtnAdicionar->setValue( "Adicionar" );
                    $obBtnAdicionar->setTipo ( "button" );
                    $obBtnAdicionar->obEvento->setOnClick ( "return AdicionaValores('MontaValoresLista');" );

                    $obBtnAlterar = new Button;
                    $obBtnAlterar->setName    ( "btnAlterar" );
                    $obBtnAlterar->setValue   ( "Alterar" );
                    $obBtnAlterar->setTipo    ( "button" );
                    $obBtnAlterar->setDisabled( true );
                    $obBtnAlterar->obEvento->setOnClick ( "return AdicionaValores('alteraValor');" );

                    $obBtnLimpar = new Button;
                    $obBtnLimpar->setName( "btnLimpar" );
                    $obBtnLimpar->setValue( "Limpar" );
                    $obBtnLimpar->setTipo( "button" );
                    $obBtnLimpar->obEvento->setOnClick ( "limpaValores();" );

                    $obSpnListaValores = new Span;
                    $obSpnListaValores->setID('spnListaValores');

                    $obHdnInIDPos = new Hidden;
                    $obHdnInIDPos->setName  ('inIDPos');

                    $obFormulario->addHidden    ( $obHdnInIDPos );
                    $obFormulario->addComponente( $obTxtValor );
                    $obFormulario->addComponente( $obRdnAtivo );
                    $obFormulario->defineBarra  ( array($obBtnAdicionar , $obBtnAlterar, $obBtnLimpar) );
                    $obFormulario->addSpan      ( $obSpnListaValores );
                break;
            }
            $obFormulario->montaInnerHTML();
            $js = "d.getElementById('spnValores').innerHTML = '".$obFormulario->getHTML()."';";
            $js .= "if (f.btnAlterar) {f.btnAlterar.disabled = true;}\n";
            $js .= "f.stValorPadrao.focus();\n";
            $js .= listaValores( $arSessaoValores , false);
        } else {
            $js = "d.getElementById('spnValores').innerHTML = '';";
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "MontaValoresLista":
        $arSessaoValores = Sessao::read('Valores');
        $boAdiciona = true;
        $rsRecordSet = new Recordset;
        $rsRecordSet->preenche($arSessaoValores);
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        $inProxId = (!$inUltimoId) ? 1 : ($inUltimoId + 1);

        for ($iCount = 0; $iCount < count( $arSessaoValores ); $iCount++ ) {
            if ($arSessaoValores[$iCount]["valor"] == $_REQUEST["stValorPadrao"]) {
                $boAdiciona = false;
                SistemaLegado::exibeAviso ("Não é possível inserir valores iguais!","","erro");
            }
        }

        if ($boAdiciona) {
            // Insere novos valores no vetor
            $arElementos['inId']            = $inProxId;
            $arElementos['cod_valor']       = $inProxId;
            $arElementos['valor']           = $_POST['stValorPadrao'];
            $arElementos['ativo']           = $_POST['boAtivo'];
            $arElementos['excluir']         = true;

            $arSessaoValores[] = $arElementos;
        }
        Sessao::write('Valores',$arSessaoValores);
        listaValores( $arSessaoValores );
    break;
    case "excluiValor":
        $arSessaoValores = Sessao::read('Valores');
        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId'];

        foreach ($arSessaoValores as $campo => $valor) {
            if ( ($arSessaoValores[$campo]['inId'] == $id) && (!$arSessaoValores[$campo]['excluir']) ) {
                $boVerifica = true;
                $stMensagem = "Não é possível excluir este valor da lista.";
            }
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
            foreach ($arSessaoValores as $campo => $valor) {
                if ($arSessaoValores[$campo]["inId"] != $id) {

                    $arElementos['inId']        = $arSessaoValores[$campo]["inId"];
                    $arElementos['cod_valor']   = $arSessaoValores[$campo]["cod_valor"];
                    $arElementos['valor']       = $arSessaoValores[$campo]["valor"];
                    $arElementos['ativo']       = $arSessaoValores[$campo]["ativo"];
                    $arElementos['excluir']     = $arSessaoValores[$campo]["excluir"];
                    $arTMP[] = $arElementos;
                 }
            }

            Sessao::write('Valores',$arTMP);
            $js  = listaValores( $arTMP );
            $js .= "f.btnAlterar.disabled = true;";
        } else {
            $js = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;
    case "MontaAlteracaoValor":
        $arSessaoValores = Sessao::read('Valores');
        $id = $_GET['inId'];

        foreach ($arSessaoValores as $campo => $valor) {
            if ($arSessaoValores[$campo]["inId"] == $id) {
                $js  = "f.stValorPadrao.value='".$arSessaoValores[$campo]["valor"]."';";
                $js .= "f.inIDPos.value='".$id."';";
                if ($arSessaoValores[$campo]["ativo"] == 'Sim') {
                    $js .= "f.boAtivo[0].checked=true;";
                    $js .= "f.boAtivo[1].checked=false;";
                } else {
                    $js .= "f.boAtivo[0].checked=false;";
                    $js .= "f.boAtivo[1].checked=true;";
                }
            }
        }

        $js .= listaValores( $arSessaoValores , false);
        $js .= "f.btnAlterar.disabled = false;";
        SistemaLegado::executaFrameOculto($js);
    break;
    case "alteraValor":
        $arSessaoValores = Sessao::read('Valores');
        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_POST['inIDPos'];

        // Lista o Array e remonta sem o ID selecionado
        foreach ($arSessaoValores as $campo => $valor) {
            if ($arSessaoValores[$campo]["inId"] == $id) {
                 $arSessaoValores[$campo]["valor"]     = $_POST['stValorPadrao'];
                 $arSessaoValores[$campo]["ativo"]     = $_POST['boAtivo'];
             }
        }

        Sessao::write('Valores',$arSessaoValores);

        $js  = listaValores($arSessaoValores,false);
        $js .= "f.btnAlterar.disabled = true;";
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'preencheInner':
        $arSessaoValores = Sessao::read('Valores');
        if ($_POST['inCodTipoAtributo']==3 || $_POST['inCodTipoAtributo']==4) {
            $js  = listaValores($arSessaoValores);
            //$js .= "f.btnAlterar.disabled = true;";
        } else {
            $js = "f.stValorPadrao.value = '".$arSessaoValores[0]['valor']."';";
            SistemaLegado::executaFrameOculto($js);
        }
    break;
}
?>

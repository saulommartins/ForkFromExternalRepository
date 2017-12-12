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
    * Arquivo de instância para manutenção de funções
    * Data de Criação: 25/07/2005

    * @author Analista: Cassiano
    * @author Desenvolvedor: Cassiano

    $Id: OCManterFuncao.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/bancoDados/postgreSQL/PalavrasReservadas.php';
include_once(CAM_GA_ADM_NEGOCIO . "RFuncao.class.php");
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );

$stCtrl = $_REQUEST['stCtrl'];
$obRegra = new RFuncao;
$arPalavrasReservadas = new PalavrasReservadas();
$boVerificaPalavras = false;
$obErro = new Erro;

$obRBiblioteca = Sessao::read('obRBiblioteca');

function listaParametrosTipo($arRecordSet, $executa=true)
{
    $obRegra = new RFuncao;
    //global $obRegra;
    $arFuncao = Sessao::read('Funcao');

    // monta lista com valores de confrontacao
    $rsRecordSet = new Recordset;

    $rsRecordSet->preenche( $arRecordSet );

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Parâmetros" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 40 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        if ($executa==true) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo( "Ações" );
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();
        }

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomeParametro" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTipoParametro" );
        $obLista->commitDado();

        if ($executa==true) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "SUBIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:ordenaDado('Acima');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->ultimaAcao->addCampo("","stNomeParametro");
            $obLista->commitAcao();
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "BAIXAR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:ordenaDado('Abaixo');" );
            $obLista->ultimaAcao->addCampo("1","inId");
            $obLista->ultimaAcao->addCampo("","stNomeParametro");
            $obLista->commitAcao();
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiParametrosTipo');" );
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
    $js .= "d.getElementById('spnListaParametros').innerHTML = '".$html."';";
    //$js .= "f.stNomeParametro.value = '';";
    //$js .= "f.stTipoParametro.value = '';";
    //$js .= "f.stTipoParametroTxt.value = '';";
    $js .= "if (f.stNomeParametro) { f.stNomeParametro.value    = ''; }";
    $js .= "if (f.stTipoParametro) { f.stTipoParametro.value    = ''; }";
    $js .= "if (f.stTipoParametroTxt) { f.stTipoParametroTxt.value = ''; }";

    if ($executa==true) {
        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        //-->
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        //<--
        $js .= "d.getElementById('spnCorpoLN').innerHTML ='".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';";
        SistemaLegado::executaFrameOculto($js);
    } else {
        return $js;
    }
}

function listaVariaveisTipo($arRecordSet, $executa=true)
{
    //global $obRegra;
$obRegra = new RFuncao;

    $arFuncao = Sessao::read('Funcao');

    foreach ($arRecordSet as $campo => $valor) {
        $arRecordSet[$campo]['inId'] = $valor['inId'] + 1;
    }
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
        $obLista->ultimoCabecalho->addConteudo( "Nome" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor Inicial" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stNomeVariavel" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTipoVariavel" );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stValorVariavel" );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiVariaveisTipo');" );
        $obLista->ultimaAcao->addCampo("1","inId" );
        $obLista->commitAcao();

        $obLista->montaHTML();
        $html = $obLista->getHTML();
        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);
    }
    // preenche a lista com innerHTML
    $js .= "d.getElementById('spnListaVariaveis').innerHTML = '".$html."';";
    $js .= "f.stNomeVariavel.value = '';";
    $js .= "f.stTipoVariavel.value = '';";
    $js .= "f.stTipoVariavelTxt.value = '';";

    if ($executa==true) {
        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        //-->
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        //<--
        $js .= "d.getElementById('spnValorVariavel').innerHTML = '';";
        $js .= "d.getElementById('spnCorpoLN').innerHTML ='".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';";
        SistemaLegado::executaFrameOculto($js);
    } else {
        return $js;
    }
}
// Acoes por pagina

switch ($stCtrl) {
    case "preencheFuncao":
        $arFuncao = Sessao::read('Funcao');
        if ($_GET["inCodFuncao"]) {
            $obTFuncao = new TAdministracaoFuncao;
            $arCodFuncao = explode('.', $_GET["inCodFuncao"] );
            if ( ( $_GET["stCodModulo"] == $arCodFuncao[0] ) && ( $_GET["stCodBiblioteca"] == $arCodFuncao[1] ) ) {
                $obTFuncao->setDado( "cod_biblioteca", $arCodFuncao[1] );
                $obTFuncao->setDado( "cod_modulo", $arCodFuncao[0] );
                $obTFuncao->setDado( "cod_funcao", $arCodFuncao[2] );
                $obTFuncao->recuperaPorChave( $rsFuncao );
                if ( $rsFuncao->Eof() ) {
                    $stJs = "f.inCodFuncao.value ='';\n";
                    $stJs .= "f.inCodFuncao.focus();\n";
                    $stJs .= "d.getElementById('stFuncao').innerHTML = '&nbsp;';\n";
                    $stJs .= "alertaAviso('@Código informado não existe. (".$_GET["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
                } else {
                    $stJs = "d.getElementById('stFuncao').innerHTML = '".$rsFuncao->getCampo("nom_funcao")."';\n";
                }
            } else {
                $stJs = "alertaAviso('@Código informado inválido. (".$_GET["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodFuncao.value ='';\n";
                $stJs .= "f.inCodFuncao.focus();\n";
                $stJs .= "d.getElementById('stFuncao').innerHTML = '&nbsp;';\n";
            }
        } else {
            $stJs = "f.inCodFuncao.value ='';\n";
            $stJs .= "d.getElementById('stFuncao').innerHTML = '&nbsp;';\n";
            if ($_GET["inCodFuncao"] == '0') {
                $stJs .= "alertaAviso('@Código informado não existe. (".$_GET["inCodFuncao"].")','form','erro','".Sessao::getId()."');";
            }
        }
        echo $stJs;
        break;

    case "NomeFuncao":
        $arFuncao = Sessao::read('Funcao');
        $arFuncao['Nome'] = $_POST['stNomeFuncao'];

        //verifica se o nome da funcao não é uma palavra reservada do banco de dados
        $boVerificaPalavras = $arPalavrasReservadas->verificaPalavrasReservadas($arFuncao['Nome']);
        if ($boVerificaPalavras == true) {
             SistemaLegado::executaFrameOculto("alertaAviso('@Palavra Reservada do banco de dados - ".$arFuncao['Nome']." - tente outro nome.','form','aviso','".Sessao::getId()."');");
             exit();

        }

        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        //-->
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        Sessao::write('Funcao',$arFuncao);
        //<--
        SistemaLegado::executaFrameOculto("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
    break;
    case "RetornoFuncao":
        $arFuncao = Sessao::read('Funcao');
        if( $arFuncao['Retorno'] != $_POST['stRetorno'] )
            $arFuncao['RetornoVar'] = '';

        $arFuncao['Retorno'] = $_POST['stRetorno'];

        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();
        //-->
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);

        Sessao::write('Funcao',$arFuncao);
        //<--
        SistemaLegado::executaFrameOculto("d.getElementById('spnCorpoLN').innerHTML = '".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';");
    break;
    case "montaValorVariavel":
        $arFuncao = Sessao::read('Funcao');
        $obFormulario = new Formulario;
        if ($_POST['stTipoVariavel']) {
            switch ($_POST['stTipoVariavel']) {
                case "TEXTO":
                    $obTxtValorVariavel = new TextBox;
                    $obTxtValorVariavel->setRotulo        ( "Valor  " );
                    $obTxtValorVariavel->setName          ( "stValorVariavel" );
                    $obTxtValorVariavel->setSize          ( 60 );
                    $obTxtValorVariavel->setMaxLength     ( 60 );
                    $obFormulario->addComponente( $obTxtValorVariavel );
                break;
                case "INTEIRO":
                    $obTxtValorVariavel = new TextBox;
                    $obTxtValorVariavel->setRotulo        ( "Valor  " );
                    $obTxtValorVariavel->setName          ( "stValorVariavel" );
                    $obTxtValorVariavel->setSize          ( 60 );
                    $obTxtValorVariavel->setMaxLength     ( 60 );
                    $obTxtValorVariavel->setInteiro       ( true );
                    $obFormulario->addComponente( $obTxtValorVariavel );
                break;
                case "NUMERICO":
                    $obTxtValorVariavel = new TextBox;
                    $obTxtValorVariavel->setRotulo        ( "Valor  " );
                    $obTxtValorVariavel->setName          ( "stValorVariavel" );
                    $obTxtValorVariavel->setSize          ( 60 );
                    $obTxtValorVariavel->setMaxLength     ( 14 );
                    $obTxtValorVariavel->obEvento->setOnKeyPress("return tfloatPonto(this, event);");
                    $obFormulario->addComponente( $obTxtValorVariavel );
                break;
                case "BOOLEANO":
                    $obRdbVerdadeiro = new Radio;
                    $obRdbVerdadeiro->setRotulo ( "Valor  " );
                    $obRdbVerdadeiro->setLabel  ("Verdadeiro");
                    $obRdbVerdadeiro->setName   ("stValorVariavel");
                    $obRdbVerdadeiro->setValue  ("VERDADEIRO");
                    $obRdbVerdadeiro->setChecked(false);
                    $obRdbFalso = new Radio;
                    $obRdbFalso->setRotulo ( "Valor  " );
                    $obRdbFalso->setLabel  ("Falso");
                    $obRdbFalso->setName   ("stValorVariavel");
                    $obRdbFalso->setValue  ("FALSO");
                    $obRdbFalso->setChecked(false);
                    $obFormulario->addComponenteComposto( $obRdbVerdadeiro , $obRdbFalso );
                break;
                case "DATA":
                    $obTxtValorVariavel = new Data;
                    $obTxtValorVariavel->setRotulo        ( "Data  " );
                    $obTxtValorVariavel->setName          ( "stValorVariavel" );
                    $obFormulario->addComponente( $obTxtValorVariavel );
                break;
            }

            Sessao::write('Funcao',$arFuncao);

            $obFormulario->montaInnerHTML();
            $js = "d.getElementById('spnValorVariavel').innerHTML = '".$obFormulario->getHTML()."';";
        } else {
            $js = "d.getElementById('spnValorVariavel').innerHTML = '';";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case "MontaParametrosTipo":
        $arFuncao = Sessao::read('Funcao');
        $arParametrosTipo = Sessao::read('ParametrosTipo');
        $boVerifica = false;

        //verifica se o nome do parametro é uma palavra reservada do banco de dados
        $boVerificaPalavras = $arPalavrasReservadas->verificaPalavrasReservadas($_POST['stNomeParametro']);
        if ($boVerificaPalavras == true) {

             SistemaLegado::executaFrameOculto("alertaAviso('@Palavra Reservada do banco de dados - ".$_POST['stNomeParametro']." - tente outro nome.','form','aviso','".Sessao::getId()."');");
             exit();

        }

        if ( count($arParametrosTipo)>0) {
            foreach ($arParametrosTipo as $campo => $valor) {
                if ($arParametrosTipo[$campo]['stNomeParametro'] == $_POST['stNomeParametro']) {
                    $boVerifica = true;
                    $stMensagem = $_POST['stNomeParametro']." - já existe.";
                }
            }
        }
        if ($boVerifica == false) {
            if ( $obRegra->convertePalavraReservada( $_POST['stNomeParametro'] ) ) {
                $boVerifica = true;
                $stMensagem = $_POST['stNomeParametro']." é uma palavra reservada";
            }
        }
        if ($_POST['stNomeParametro'] == $arFuncao['Nome']) {
            $boVerifica = true;
            $stMensagem = " Nome do Parâmetro igual ao nome da Função!";
        }
        if ($boVerifica == false) {
            // recupera o Id do ultimo valor de confrontacao inserido
            $rsRecordSet = new Recordset;
            $rsRecordSet->preenche( $arParametrosTipo );
            $rsRecordSet->setUltimoElemento();
            $inUltimoId = $rsRecordSet->getCampo("inId");
            if (!$inUltimoId) {
                $inProxId = 1;
            } else {
                $inProxId = $inUltimoId + 1;
            }

            // Insere novos valores no vetor
            $arElementos['inId']               = $inProxId;
            $arElementos['stNomeParametro']  = $_POST['stNomeParametro'];
            $arElementos['stTipoParametro']  = $_POST['stTipoParametro'];

            $arFuncao['Parametro'][] = $_POST['stNomeParametro'].':'.$_POST['stTipoParametro'];

            $arParametrosTipo[] = $arElementos;
            Sessao::write('Funcao',$arFuncao);
            Sessao::write('ParametrosTipo',$arParametrosTipo);
            listaParametrosTipo($arParametrosTipo);
        } else {
            $js = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "MontaVariaveisTipo":
        $arFuncao = Sessao::read('Funcao');
        $arVariaveisTipo = Sessao::read('VariaveisTipo');
        $boVerifica = false;

        //verifica se o nome da variavel é uma palavra reservada do banco de dados
        $boVerificaPalavras = $arPalavrasReservadas->verificaPalavrasReservadas($_POST['stNomeVariavel'] );
        if ($boVerificaPalavras == true) {

             SistemaLegado::executaFrameOculto("alertaAviso('@Palavra Reservada do banco de dados - ".$_POST['stNomeVariavel'] ." - tente outro nome.','form','aviso','".Sessao::getId()."');");
             exit();

        }

        foreach ($arVariaveisTipo as $campo => $valor) {
            if ($arVariaveisTipo[$campo]['stNomeVariavel'] == $_POST['stNomeVariavel']) {
                $boVerifica = true;
                $stMensagem = $_POST['stNomeVariavel']." - já existe.";
            }
        }
        if ($boVerifica == false) {
            if ( $obRegra->convertePalavraReservada( $_POST['stNomeVariavel'] ) ) {
                $boVerifica = true;
                $stMensagem = $_POST['stNomeVariavel']." é uma palavra reservada";
            }
        }
        if ($boVerifica == false) {
            // recupera o Id do ultimo valor de confrontacao inserido
            $rsRecordSet = new Recordset;
            $rsRecordSet->preenche( $arVariaveisTipo );
            $rsRecordSet->setUltimoElemento();
            $inUltimoId = $rsRecordSet->getCampo("inId");
            if (!$inUltimoId) {
                $inProxId = 1;
            } else {
                $inProxId = $inUltimoId + 1;
            }

            // Insere novos valores no vetor
            $arElementos['inId']            = $inProxId;
            $arElementos['stNomeVariavel']  = $_POST['stNomeVariavel'];
            $arElementos['stTipoVariavel']  = $_POST['stTipoVariavel'];
            $arElementos['stValorVariavel'] = $_POST['stValorVariavel'];

            // Formata para o padrão do banco Postgres.
            if ($arElementos['stTipoVariavel'] == "DATA") {
                list($dia, $mes, $ano) = explode("/",$_POST['stValorVariavel']);

                if ( ($dia != "") && ($mes != "") && ($ano != "") ) {
                    $arElementos['stValorVariavel'] = $ano."-".$mes."-".$dia;
                } else {
                    $arElementos['stValorVariavel'] = "";
                }
            } else {
                $arElementos['stValorVariavel'] = $_POST['stValorVariavel'];
            }

            $stVariavel  = $_POST['stNomeVariavel'].' '.$_POST['stTipoVariavel'];
            switch ($_POST['stTipoVariavel']) {
                case "TEXTO":
                    $stVariavel .= ' <- ';
                    $_POST['stValorVariavel'] = str_replace('"','',$_POST['stValorVariavel']);
                    $stVariavel .= '"'.$_POST['stValorVariavel'].'"';
                break;
                case "DATA":
                    if ($_POST['stValorVariavel'] != '') {
                        $stVariavel .= ' <- ';
                        $data = explode ( '/', $_POST['stValorVariavel'] );

                        $dia = $data[0];
                        $mes = $data[1];
                        $ano = $data[2];
                        $_POST['stValorVariavel'] = $ano."-".$mes."-".$dia;
                        $stVariavel .= '"'.$_POST['stValorVariavel'].'"';
                    }
                break;
                default:
                    if($_POST['stValorVariavel'] != "")
                        $stVariavel .= ' <- ';
                    $stVariavel .= $_POST['stValorVariavel'];
                    break;
            }
            $arFuncao['Variavel'][] = $stVariavel;

            $arVariaveisTipo[] = $arElementos;
            Sessao::write('Funcao',$arFuncao);
            Sessao::write('VariaveisTipo',$arVariaveisTipo);
            listaVariaveisTipo( $arVariaveisTipo);
        } else {
            $js = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "excluiParametrosTipo":
        $arFuncao = Sessao::read('Funcao');
        $arParametrosTipo = Sessao::read('ParametrosTipo');
        $boVerifica = false;
        $arParametros = $arTMP = array();
        $arFuncao['Parametro'] = array();
        $id = $_REQUEST['inId'];

        foreach ($arFuncao['Corpo'] as $campo => $valor) {
            if ( strstr($arFuncao['Corpo'][$campo]['Conteudo'],'#'.$arParametrosTipo[($id-1)]["stNomeParametro"]) ) {
                $boVerifica = true;
                $stMensagem = $arParametrosTipo[($id-1)]["stNomeParametro"]." - já está sendo utilizado no corpo do programa.";
            }
        }
        if ( strstr($arFuncao['RetornoVar'],'#'.$arParametrosTipo[($id-1)]["stNomeParametro"]) ) {
            $boVerifica = true;
            $stMensagem = $arParametrosTipo[($id-1)]["stNomeParametro"]." - já está sendo utilizado no retorno.";
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
            while ( list( $arId ) = each( $arParametrosTipo ) ) {
                if ($arParametrosTipo[$arId]["inId"] != $id) {

                    $arElementos['inId']            = $arParametrosTipo[$arId]["inId"];
                    $arElementos['stNomeParametro'] = $arParametrosTipo[$arId]["stNomeParametro"];
                    $arElementos['stTipoParametro'] = $arParametrosTipo[$arId]["stTipoParametro"];
                    $arTMP[] = $arElementos;

                    //$arFuncao['Parametro'][] = $arElementos['stNomeParametro'].':'.$arElementos['stTipoParametro'];
                    $arParametros[] = $arElementos['stNomeParametro'].':'.$arElementos['stTipoParametro'];
                }
            }
            $arFuncao['Parametro'] = $arParametros;
            Sessao::write('ParametrosTipo',$arTMP);
            listaParametrosTipo( $arTMP );
        } else {
            $js = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "excluiVariaveisTipo":
        $arFuncao = Sessao::read('Funcao');
        $arVariaveisTipo= Sessao::read('VariaveisTipo');

        $boVerifica = false;
        $arVariaveis = $arTMP = array();
        $id = $_GET['inId']-1;

        foreach ($arFuncao['Corpo'] as $campo => $valor) {
            if ( strstr($arFuncao['Corpo'][$campo]['Conteudo'],'#'.$arVariaveisTipo[($id)]["stNomeVariavel"]) ) {
                $boVerifica = true;
                $stMensagem = $arVariaveisTipo[($id)]["stNomeVariavel"]." - já está sendo utilizada no corpo do programa.";
            }
        }

        if ($boVerifica == false) {
            // Lista o Array e remonta sem o ID selecionado
        $IdMenos = 0; //seta quantos ele removeu para decrementar o Id
            while ( list( $arId ) = each( $arVariaveisTipo ) ) {
                if ($arVariaveisTipo[$arId]["inId"] != $id) {

                    $arElementos['inId']           = $arVariaveisTipo[$arId]["inId"] - $IdMenos;
                    $arElementos['stNomeVariavel'] = $arVariaveisTipo[$arId]["stNomeVariavel"];
                    $arElementos['stTipoVariavel'] = $arVariaveisTipo[$arId]["stTipoVariavel"];
                    $arElementos['stValorVariavel']= $arVariaveisTipo[$arId]["stValorVariavel"];
                    $arTMP[] = $arElementos;

                    $arFuncao['Variavel'][] = $arElementos['stNomeVariavel'].':'.$arElementos['stTipoVariavel'];
//                    $stVariavel  = $arElementos['stNomeVariavel'].':'.$arElementos['stTipoVariavel'];
                    $stVariavel  = $arElementos['stNomeVariavel'].' '.$arElementos['stTipoVariavel'];
                    if ($arElementos['stValorVariavel']) {
                        $stVariavel .= ' <- ';
                        if (is_numeric(trim($arElementos['stValorVariavel']))) {
                            $stVariavel .= $arElementos['stValorVariavel'];
                        } else {
                            $arElementos['stValorVariavel'] = str_replace('"','',$arElementos['stValorVariavel']);
                            $stVariavel .= '"'.$arElementos['stValorVariavel'].'"';
                        }
                    }
                    $arVariaveis[] = $stVariavel;
                } else {
                    $IdMenos = $IdMenos + 1;
                }
            }
            $arFuncao['Variavel'] = $arVariaveis;
            Sessao::write('Funcao',$arFuncao);
            Sessao::write('VariaveisTipo',$arTMP);
            listaVariaveisTipo( $arTMP );
        } else {
            $js = "alertaAviso('@($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;
    case "ordenaDado":
        $arFuncao = Sessao::read('Funcao');
        $arParametrosTipo = Sessao::read('ParametrosTipo');
        $arParametros = array();
        $boVerifica = false;
        $inCount = 1;

        if ( count($arParamentrosTipo)>0) {
            foreach ($arParametrosTipo as $campo => $valor) {
                if ($arParametrosTipo[$campo]['stNomeParametro'] == $_GET['stNomeParametro']) {
                    if ($inCount==1 && $_GET['stOrdem']=='Acima') {
                        $boVerifica = true;
                        $stMensagem    = $_GET['stNomeParametro']." - já está na Primeira posição.";
                    } elseif ($inCount==count($arParametrosTipo) && $_GET['stOrdem']=='Abaixo') {
                        $boVerifica = true;
                        $stMensagem    = $_GET['stNomeParametro']." - já está na Última posição.";
                    } else {
                        if ($_GET['stOrdem']=='Abaixo') {
                            $arAtual = $arParametrosTipo[$campo];
                            $arParametrosTipo[$campo] = $arParametrosTipo[$campo+1];
                            $arParametrosTipo[$campo+1] = $arAtual;
                            break;
                        } elseif ($_GET['stOrdem']=='Acima') {
                            $arAtual = $arParametrosTipo[$campo];
                            $arParametrosTipo[$campo] = $arParametrosTipo[$campo-1];
                            $arParametrosTipo[$campo-1] = $arAtual;
                            break;
                        }
                    }
                }
                $inCount++;
            }
            foreach ($arParametrosTipo as $campo => $valor) {
                $arParametros[] = $arParametrosTipo[$campo]['stNomeParametro'].':'.$arParametrosTipo[$campo]['stTipoParametro'];
            }
        }
        $arFuncao['Parametro'] = $arParametros;

        Sessao::write('Funcao',$arFuncao);
        Sessao::write('ParametrosTipo',$arParametrosTipo);

        if ($boVerifica == false) {
            listaParametrosTipo( $arParametrosTipo );
        } else {
            $js = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
            SistemaLegado::executaFrameOculto($js);
        }
    break;
    case "excluiLinhasCorpo":
        $arFuncao = Sessao::read('Funcao');
        $inCountElementos = 0;
        $arTMP = array();
        $arPosicao   = explode('-',$_GET['stPosicao']);
        $stConteudo  = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

        if (substr($stConteudo,0,2)=='SE' && substr($stConteudo,0,3)!='SEN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,5)=='FIMSE' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }
            for ($inCount=0; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ($inCount < $arPosicao[0] || $inCount > $inPosicaoFinal) {
                    $arTMP[$inCountElementos++] = $arFuncao['Corpo'][$inCount];
                }
            }
            $arFuncao['Corpo'] = $arTMP;
        } elseif (substr($stConteudo,0,2)=='EN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,11)=='FIMENQUANTO' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }
            for ($inCount=0; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ($inCount < $arPosicao[0] || $inCount > $inPosicaoFinal) {
                    $arTMP[$inCountElementos++] = $arFuncao['Corpo'][$inCount];
                }
            }
            $arFuncao['Corpo'] = $arTMP;
        } elseif (substr($stConteudo,0,1)=='#') {
            for ($inCount=0; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ($inCount != $arPosicao[0]) {
                    $arTMP[$inCountElementos++] = $arFuncao['Corpo'][$inCount];
                }
            }
            $arFuncao['Corpo'] = $arTMP;
        }

        Sessao::write('Funcao',$arFuncao);
        $stCorpoLN = $obRegra->montaCorpoFuncao();
        $stCorpoPL = $obRegra->ln2pl();

        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);

        $js = "d.getElementById('spnCorpoLN').innerHTML ='".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';";
        SistemaLegado::executaFrameOculto($js);
    break;

    case "subir":
        $arFuncao = Sessao::read('Funcao');
        $inCountElementos = 0;
        $arTMP = array();
        $arPosicao   = explode('-',$_GET['stPosicao']);
        $stConteudo  = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

        if (substr($stConteudo,0,2)=='SE' && substr($stConteudo,0,3)!='SEN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,5)=='FIMSE' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }
            for ($inCount=0; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( $inCount == ( $arPosicao[0] - 1 )  ) {
                    $inCountElementos = $inPosicaoFinal;
                }
                if ($inCount >= $arPosicao[0] and $inCount <= $inPosicaoFinal) {
                    continue;
                }
                $arTMP[$inCountElementos++] = $arFuncao['Corpo'][$inCount];
            }
            for ( $inCount = ($arPosicao[0] - 1) ;  $inCount < $inPosicaoFinal; $inCount++ ) {
                 $arTMP[$inCount] = $arFuncao['Corpo'][$inCount + 1];
            }
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;
        } elseif (substr($stConteudo,0,2)=='EN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,11)=='FIMENQUANTO' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }
            for ($inCount=0; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( $inCount == ( $arPosicao[0] - 1 )  ) {
                    $inCountElementos = $inPosicaoFinal;
                }
                if ($inCount >= $arPosicao[0] and $inCount <= $inPosicaoFinal) {
                    continue;
                }
                $arTMP[$inCountElementos++] = $arFuncao['Corpo'][$inCount];
            }
            for ( $inCount = ($arPosicao[0] - 1) ;  $inCount < $inPosicaoFinal; $inCount++ ) {
                 $arTMP[$inCount] = $arFuncao['Corpo'][$inCount + 1];
            }
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;

        } elseif (substr($stConteudo,0,1)=='#') {
            $arTMP = $arFuncao['Corpo'];
            $arTMP[($arPosicao[0] - 1)] = $arFuncao['Corpo'][$arPosicao[0]];
            $arTMP[$arPosicao[0]] = $arFuncao['Corpo'][($arPosicao[0] - 1)];
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;
        }

        Sessao::write('Funcao',$arFuncao);

        $stCorpoLN = $obRegra->montaCorpoFuncao($arPosicao[0]);
        $stCorpoPL = $obRegra->ln2pl();
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        $js  = "d.getElementById('spnCorpoLN').innerHTML ='".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';";
        $js .= "d.getElementById('td_".($arPosicao[0])."').style.backgroundColor = '#F4F4F4';";
        SistemaLegado::executaFrameOculto($js);
    break;

    case "baixar":
        $arFuncao = Sessao::read('Funcao');
        $inCountElementos = 0;
        $arTMP = array();
        $arPosicao   = explode('-',$_GET['stPosicao']);
        $stConteudo  = $arFuncao['Corpo'][ $arPosicao[0] ]['Conteudo'];

        if (substr($stConteudo,0,2)=='SE' && substr($stConteudo,0,3)!='SEN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,5)=='FIMSE' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }
            $arTMP = $arFuncao['Corpo'];
            $arTMP[ $arPosicao[0] ] = $arFuncao['Corpo'][$inPosicaoFinal + 1];
            for ( $inCont = ($arPosicao[0] + 1 ); $inCont <= $inPosicaoFinal + 1; $inCont++ ) {
                 $arTMP[$inCont] = $arFuncao['Corpo'][$inCont - 1];
            }
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;
        } elseif (substr($stConteudo,0,2)=='EN') {
            $inPosicaoFinal = count($arFuncao['Corpo']);
            for ($inCount=$arPosicao[0]; $inCount<count($arFuncao['Corpo']); $inCount++) {
                if ( substr($arFuncao['Corpo'][$inCount]['Conteudo'],0,11)=='FIMENQUANTO' && $arFuncao['Corpo'][$inCount]['Nivel']==$arPosicao[1]) {
                    $inPosicaoFinal = $inCount;
                    break;
                }
            }

            $arTMP = $arFuncao['Corpo'];
            $arTMP[ $arPosicao[0] ] = $arFuncao['Corpo'][$inPosicaoFinal + 1];
            for ( $inCont = ($arPosicao[0] + 1 ); $inCont <= $inPosicaoFinal + 1; $inCont++ ) {
                 $arTMP[$inCont] = $arFuncao['Corpo'][$inCont - 1];
            }
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;
        } elseif (substr($stConteudo,0,1)=='#') {
            $arTMP = $arFuncao['Corpo'];
            $arTMP[($arPosicao[0] + 1)] = $arFuncao['Corpo'][$arPosicao[0]];
            $arTMP[$arPosicao[0]] = $arFuncao['Corpo'][($arPosicao[0] + 1)];
            ksort( $arTMP );
            $arFuncao['Corpo'] = $arTMP;
        }

        Sessao::write('Funcao',$arFuncao);

        $stCorpoLN = $obRegra->montaCorpoFuncao($arPosicao[0]+2);
        $stCorpoPL = $obRegra->ln2pl();
        $stCorpoPL = str_replace("\\\'","\'",$stCorpoPL);
        $stCorpoPL = str_replace('\"','"',$stCorpoPL);
        $js = "d.getElementById('spnCorpoLN').innerHTML ='".$stCorpoLN."';d.getElementById('spnCorpoPL').innerHTML = '".$stCorpoPL."';";
        $js .= "d.getElementById('td_".($arPosicao[0]+2)."').style.backgroundColor = '#F4F4F4';";
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'preencheInner':
        $arParametrosTipo = Sessao::read('ParametrosTipo');
        $arVariaveisTipo = Sessao::read('VariaveisTipo');

        $js = '';
        if ( count( $arVariaveisTipo ) ) {
            $js .= listaVariaveisTipo( $arVariaveisTipo, false );
        }
        if ( count( $arParametrosTipo ) ) {
            $js .= listaParametrosTipo( $arParametrosTipo, false );
        }

        SistemaLegado::executaFrameOculto($js);
    break;
    case "buscaCadastro":
        $stJs = "";
        if ($_POST["inCodModulo"]) {
            $obRBiblioteca->roRModulo->setCodModulo( $_POST["inCodModulo"] );
            $obErro = $obRBiblioteca->listarBibliotecasPorModulo( $rsBiblioteca );
            if ( !$obErro->ocorreu() ) {
                $i = 0;
                while ( !$rsBiblioteca->eof() ) {
                    $stJs .= "f.inCodBiblioteca.options[".++$i."] = new Option('".$rsBiblioteca->getCampo("nom_biblioteca")."','".$rsBiblioteca->getCampo("cod_biblioteca")."');\n";
                    $rsBiblioteca->proximo();
                }
            }
            if ( !$obErro->ocorreu() ) {
                $stJs .= " erro = true;\n";
                $stJs .= " mensagem = '".$obErro->getDescricao()."';\n";
            }
         }
         SistemaLegado::executaFrameOculto( $stJs );
    break;
}
?>

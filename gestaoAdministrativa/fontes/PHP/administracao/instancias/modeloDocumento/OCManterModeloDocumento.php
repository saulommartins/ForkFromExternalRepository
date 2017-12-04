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
    * Página de Formulario para Modelo de Documentos
    * Data de Criação   : 20/02/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @ignore

    $Revision: 27629 $
    $Name$
    $Autor: $
    $Date: 2008-01-18 17:37:57 -0200 (Sex, 18 Jan 2008) $

    * Casos de uso: uc-01.03.100
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$pgJs = "JSManterModeloDocumento.js";
include_once($pgJs);

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function listaArquivos($stJs="", $arRecordSet="")
{
if ( is_array($arRecordSet)) {
    $rsRecordSet = new Recordset;
    $rsRecordSet->preenche( $arRecordSet );
} else {
    include_once(CAM_GA_ADM_NEGOCIO."RModeloDocumento.class.php");
    $obRModeloDocumento = new RModeloDocumento;
    $obRModeloDocumento->obRAdministracaoAcao->setCodigoAcao($_REQUEST["inCodAcao"]);
    $obRModeloDocumento->setCodDocumento($_REQUEST["inCodDocumento"]);
    $obRModeloDocumento->listarArquivosPorDocumentoAcao($rsRecordSet);
}
$artmp = $rsRecordSet->arElementos;
$inCount = 0;
$arNovo = array();
foreach ($artmp as $valor) {
    if ($valor["padrao"] == "f" )
        $valor["padrao"] = false;

    $valor["dir"] = "../../anexos/";
    if ($valor["sistema"] == "t") {
        $valor["dir"] .= "modelos_sistema";
    } else {
        $valor["dir"] .= "modelos_usuario";
    }

    $valor["inId"] = $inCount++;
    $arNovo[] = $valor;
}
// guarda lista na sessao

Sessao::write("arArquivos",$arNovo);
$rsRecordSet->preenche($arNovo);
$rsRecordSet->setPrimeiroElemento();

    if ($rsRecordSet->getNumLinhas() != 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Arquivos" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Nome do Arquivo" );
        $obLista->ultimoCabecalho->setWidth( 70 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Padrão" );
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nome_arquivo_template" );
        $obLista->commitDado();

        $obChkPadrao = new Radio;
        $obChkPadrao->setName           ( "boPadrao"   );
        $obChkPadrao->setValue          ( "checksum");
        $obChkPadrao->obEvento->setOnClick("this.checked=true, this.value = this.value;");

        $obLista->addDadoComponente( $obChkPadrao , false);
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->ultimoDado->setCampo( "padrao" );
        $obLista->commitDadoComponente();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "SALVAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:baixaArquivo();" );
        $obLista->ultimaAcao->addCampo("1","cod_documento");
        $obLista->ultimaAcao->addCampo("2","nome_arquivo_template");
        $obLista->ultimaAcao->addCampo("3","dir");
        $obLista->commitAcao();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluiDado('excluiArquivo');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $html = $obLista->getHTML();
        $html = str_replace("\n","",$html);
        $html = str_replace("  ","",$html);
        $html = str_replace("'","\\'",$html);
    }
    // preenche a lista com innerHTML
    $js .= "d.getElementById('spnLista').innerHTML = '".$html."';";

    if ( strlen($stJs) > 1) {
        $stJs .= $js;

        return $stJs;
    } else {
        SistemaLegado::executaFrameOculto($js);
    }

}

// Acoes por pagina
switch ($stCtrl) {
    case "montaModulos":
        if ($_POST['inCodGestao']) {
            $obRAdministracaoGestao = new RAdministracaoGestao;
            $obRAdministracaoGestao->setCodigoGestao( $_POST['inCodGestao'] );
            $obErro = $obRAdministracaoGestao->consultarGestao();
            if ( !$obErro->ocorreu() ) {
                $obErro = $obRAdministracaoGestao->listarModulos();
                if ( !$obErro->ocorreu() ) {
                    $inContador = 1;
                    $stJs .= "limpaSelect(f.inCodModulo,1); \n";
                    $stJs .= "limpaSelect(f.inCodFuncionalidade,1); \n";
                    $stJs .= "limpaSelect(f.inCodAcao,1); \n";
                    $stJs .= "f.inCodModuloTxt.value = ''; \n";
                    $stJs .= "f.inCodFuncionalidadeTxt.value = ''; \n";
                    $stJs .= "f.inCodAcaoTxt.value = ''; \n";
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
            $stJs .= "limpaSelect(f.inCodFuncionalidade,1); \n";
            $stJs .= "limpaSelect(f.inCodAcao,1); \n";
            $stJs .= "f.inCodModuloTxt.value = ''; \n";
            $stJs .= "f.inCodFuncionalidadeTxt.value = ''; \n";
            $stJs .= "f.inCodAcaoTxt.value = ''; \n";
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;
    case "montaFuncionalidade":
        if ($_REQUEST["inCodModulo"]) {
            include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoFuncionalidade.class.php");
            include_once(CAM_GA_ADM_NEGOCIO."RModulo.class.php");
            $obRAdministracaoFuncionalidade = new RAdministracaoFuncionalidade ( new RModulo);
            $obRAdministracaoFuncionalidade->roAdministracaoModulo->setCodModulo( $_REQUEST["inCodModulo"] );
            $obErro = $obRAdministracaoFuncionalidade->listarFuncionalidades( $rsFuncionalidade );
            if ( !$obErro->ocorreu() ) {
                $js .= "limpaSelect(f.inCodAcao,1); \n";
                $js .= "f.inCodFuncionalidadeTxt.value = ''; \n";
                $js .= "f.inCodAcaoTxt.value = ''; \n";
                $js .= "limpaSelect(f.inCodFuncionalidade,0); \n";
                $js .= "f.inCodFuncionalidade[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsFuncionalidade->eof() ) {
                    $inCodFuncionalidade = $rsFuncionalidade->getCampo( "cod_funcionalidade" );
                    $stNomFuncionalidade = $rsFuncionalidade->getCampo( "nom_funcionalidade" );
                    $js .= "f.inCodFuncionalidade.options[$inContador] = new Option('".$stNomFuncionalidade."','".$inCodFuncionalidade."');\n";
                    $inContador++;
                    $rsFuncionalidade->proximo();
                }
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando as funcionalidades: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $js .= "limpaSelect(f.inCodFuncionalidade,0); \n";
            $js .= "limpaSelect(f.inCodAcao,1); \n";
            $js .= "f.inCodFuncionalidadeTxt.value = ''; \n";
            $js .= "f.inCodAcaoTxt.value = ''; \n";
            $js .= "f.inCodFuncionalidade[0] = new Option('Selecione','', 'selected');\n";
            SistemaLegado::executaFrameOculto($js);
        }

    break;
    case "montaAcao":
        if ($_REQUEST["inCodFuncionalidade"]) {
            include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoAcao.class.php");
            include_once(CAM_GA_ADM_NEGOCIO."RAdministracaoFuncionalidade.class.php");
            include_once(CAM_GA_ADM_NEGOCIO."RModulo.class.php");
            $obRAdministracaoAcao= new RAdministracaoAcao ();
            $obRAdministracaoAcao->roRAdministracaoFuncionalidade = new RAdministracaoFuncionalidade( new RModulo);
            $stFiltro = " and F.cod_funcionalidade=".$_REQUEST["inCodFuncionalidade"];
            $stOrdem = " ORDER BY nom_acao";
            $obErro = $obRAdministracaoAcao->listar( $rsAcao , $stFiltro , $stOrdem);
            if ( !$obErro->ocorreu() ) {
                $js .= "limpaSelect(f.inCodAcao,0); \n";
                $js .= "f.inCodAcaoTxt.value = ''; \n";
                $js .= "f.inCodAcao[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsAcao->eof() ) {
                    $inCodAcao = $rsAcao->getCampo( "cod_acao" );
                    $stNomAcao = $rsAcao->getCampo( "nom_acao" );
                    $js .= "f.inCodAcao.options[$inContador] = new Option('".$stNomAcao."','".$inCodAcao."');\n";
                    $inContador++;
                    $rsAcao->proximo();
                }
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando as funcionalidades: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $js .= "limpaSelect(f.inCodAcao,0); \n";
            $js .= "f.inCodAcaoTxt.value = ''; \n";
            $js .= "f.inCodAcao[0] = new Option('Selecione','', 'selected');\n";
            SistemaLegado::executaFrameOculto($js);
        }

    break;
    case "montaDocumento":
        if ($_REQUEST["inCodAcao"]) {
            include_once(CAM_GA_ADM_NEGOCIO."RModeloDocumento.class.php");
            $obRModeloDocumento = new RModeloDocumento;
            $obRModeloDocumento->obRAdministracaoAcao->setCodigoAcao($_REQUEST["inCodAcao"]);
            $obErro = $obRModeloDocumento->listarPorAcao( $rsDocumento );
            $js .= "d.getElementById('spnLista').innerHTML='';\n";
            if ( !$obErro->ocorreu() ) {
                $js .= "limpaSelect(f.inCodDocumento,0); \n";
                $js .= "f.inCodDocumentoTxt.value = ''; \n";
                $js .= "f.inCodDocumento[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsDocumento->eof() ) {
                    $inCodDocumento = $rsDocumento->getCampo( "cod_documento" );
                    $stNomDocumento = $rsDocumento->getCampo( "nome_documento" );
                    $js .= "f.inCodDocumento.options[$inContador] = new Option('".$stNomDocumento."','".$inCodDocumento."');\n";
                    $inContador++;
                    $rsDocumento->proximo();
                }
                SistemaLegado::executaFrameOculto($js);
            } else {
                SistemaLegado::exibeAviso(urlencode("Ocorreu o seguinte erro buscando as funcionalidades: ".$obErro->getDescricao()),"unica","erro");
            }
        } else {
            $js .= "f.inCodDocumentoTxt.value = ''; \n";
            $js .= "limpaSelect(f.inCodDocumento,0); \n";
            $js .= "f.inCodDocumento[0] = new Option('Selecione','', 'selected');\n";
            SistemaLegado::executaFrameOculto($js);
        }

    break;
    case "montaListaArquivos":
        if ($_REQUEST["inCodDocumento"]) {
            listaArquivos();
        } else {
            Sessao::write('arArquivos',array());
            Sessao::write('arArquivosIncluidos',array());
            Sessao::write('arArquivosExcluidos',array());

            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML='';\n");
        }

    break;
    case 'incluirArquivo':
        $stErro = null;
        // VALIDANDO
        // verifica antes de tudo, se foi setado o documento
        if (!$_REQUEST["inCodDocumento"]) {
            $stErro = "Voce deve setar o documento ao qual vai ser inserido o arquivo/modelo!";
        }
        // validando o upload
        if ($_FILES["aqArquivo"]["error"] > 0) {
            if ($_FILES["aqArquivo"]["error"] == 1 )
                $stErro = "Arquivo ultrapassa o valor maxímo de ".ini_get("upload_max_filesize");
            else
                $stErro = "Erro no upload do arquivo.";
        }

        // validar tipo/mime do arquivo
        $arMimeValido = array("application/vnd.sun.xml.writer" , "application/x-zip","application/vnd.oasis.opendocument.text");
        if (!in_array($_FILES["aqArquivo"]["type"], $arMimeValido)) {
            $stErro = "Arquivo informado é deve ser de tipo OpenOffice Writer(.sxw|.odt)!";
        }

        // se tivermos erros
        if ($stErro) {
            SistemaLegado::exibeAviso(urlencode("Erro ao Incluir Arquivo: $stErro"),"unica","erro");
        } else { // se nao, continuamos
            $dirTmp = ini_get("session.save_path")."/";
            copy($_FILES["aqArquivo"]["tmp_name"],$dirTmp.$_FILES["aqArquivo"]["name"]);
            echo $_FILES["aqArquivo"]["tmp_name"]."   ,   ".$dirTmp;
            //gera md5 do arquivo temporario
            $arArquivosUpload = $_FILES["aqArquivo"];
            $arArquivosUpload["checksum"] = md5_file($_FILES["aqArquivo"]["tmp_name"]);

            // preenchendo array com dados necessarios
            $arArquivosUpload["nome_arquivo_template"] = $arArquivosUpload["name"];
            $arArquivosUpload["padrao"] = false;

            // atualiza array de arquivos
            $arTemp = Sessao::read("arArquivos"); // adiciona no array de lista
            $arTemp[] = $arArquivosUpload;
            $arTempIncluidos = Sessao::read("arArquivosIncluidos"); // adiciona no array de arquivos incluidos
            $arTempIncluidos[] = $arArquivosUpload;

            Sessao::write("arArquivos",$arTemp);
            Sessao::write("arArquivosIncluidos",$arTempIncluidos);
            listaArquivos("",$arTemp);

// só pra guardar, como pegar caminho absoluto dos anexos
//            $stCaminhoReal = realpath(CAM_GA_ADM_ANEXOS);
        }
    break;
    case "excluiArquivo":
        // verifica se não é arquivo de sistema
        $arTemp =  Sessao::read("arArquivos");
        if ($arTemp[$_REQUEST["inId"]]["sistema"] == 't') {
            $stErro = "Este Arquivo pertence ao Sistema, e não pode ser excluído!";
        }
        if ($stErro) {
            SistemaLegado::exibeAviso(urlencode("Erro ao Excluir Arquivo: $stErro"),"unica","erro");
        } else { // se nao, continuamos

            $arNovos = array();
            $arExcluidos = array();
            foreach ($arTemp as $valor) {
                if ( $valor["inId"] != $_REQUEST["inId"])
                    $arNovos[] = $valor;
                else
                    $arExcluidos[] = $valor;
            }
            reset($arNovos);
            reset($arExcluidos);
            Sessao::write('arArquivos',$arNovos);
            Sessao::write('arArquivosExcluidos',$arExcluidos);
            listaArquivos("",$arNovos);
        }
    break;
}
?>

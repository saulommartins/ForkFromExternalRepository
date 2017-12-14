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
    * Página de Frame Oculto Remissao
    * Data de Criação   : 20/08/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCConcederRemissao.php 64290 2016-01-08 18:28:54Z evandro $

    * Casos de uso: uc-05.04.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_ARR_NEGOCIO.'RARRGrupo.class.php';
include_once CAM_GT_MON_NEGOCIO.'RMONCredito.class.php';
include_once CAM_GA_CGM_MAPEAMENTO.'TCGM.class.php';
include_once CAM_FW_COMPONENTES.'Table/TableTree.class.php';

function montaListaGrupoCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();

    if ( !$rsLista->eof() ) {
        $obTableTree = new TableTree;
        $obTableTree->setRecordset            ($rsLista);
        $obTableTree->setArquivo              ("OCConcederRemissao.php");
        $obTableTree->setParametros           (array('cod_grupo' => 'stCodGrupo', 'descricao' => 'stGrupoDescricao'));
        $obTableTree->setComplementoParametros('stCtrl=montaTableGrupoCredito');
        $obTableTree->setSummary              ('Lista de Grupos de Créditos');
        $obTableTree->Head->addCabecalho      ('Código',10);
        $obTableTree->Head->addCabecalho      ('Descrição',40);
        $obTableTree->Head->addCabecalho      ('&nbsp;',40);
        $obTableTree->Body->addCampo          ('stCodGrupo','C');
        $obTableTree->Body->addCampo          ('stGrupoDescricao', 'E');
        $obTableTree->Body->addAcao           ('excluir', 'javascript: excluirGrupoCredito(%s);', array('stCodGrupo'));
        $obTableTree->montaHTML();

        // $obLista = new Lista;
        // $obLista->setMostraPaginacao( false );
        // $obLista->setRecordSet( $rsLista );

        // $obLista->setTitulo ("Lista de Grupos de Créditos");

        // $obLista->addCabecalho();
        //     $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        //     $obLista->ultimoCabecalho->setWidth( 2 );
        // $obLista->commitCabecalho();

        // $obLista->addCabecalho();
        //     $obLista->ultimoCabecalho->addConteudo("Código");
        //     $obLista->ultimoCabecalho->setWidth( 20 );
        // $obLista->commitCabecalho();

        // $obLista->addCabecalho();
        //     $obLista->ultimoCabecalho->addConteudo( "Descrição" );
        //     $obLista->ultimoCabecalho->setWidth( 80 );
        // $obLista->commitCabecalho();

        // $obLista->addCabecalho();
        //     $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        //     $obLista->ultimoCabecalho->setWidth( 2 );
        // $obLista->commitCabecalho();

        // $obLista->addDado();
        //     $obLista->ultimoDado->setCampo( "stCodGrupo" );
        // $obLista->commitDado();

        // $obLista->addDado();
        //     $obLista->ultimoDado->setCampo( "stGrupoDescricao" );
        // $obLista->commitDado();

        // $obLista->addAcao();
        //     $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        //     $obLista->ultimaAcao->setFuncao( true );
        //     $obLista->ultimaAcao->addCampo( "1", "stCodGrupo" );
        //     $obLista->ultimaAcao->setLink( "javascript:excluirGrupoCredito();" );
        // $obLista->commitAcao();

        // $obLista->montaHTML();
        $stHTML = $obTableTree->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = "&nbsp;";
    }

    $stJs = "d.getElementById('spnListaGrupos').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaCredito(&$rsLista)
{
    $rsLista->setPrimeiroElemento();
    if (!$rsLista->eof()) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);

        $obLista->setTitulo ('Lista de Créditos');

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Exercício');
        $obLista->ultimoCabecalho->setWidth(7);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Código');
        $obLista->ultimoCabecalho->setWidth(13);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição');
        $obLista->ultimoCabecalho->setWidth(80);
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(2);
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stExercicio');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stCodCredito');
        $obLista->ultimoDado->setAlinhamento('CENTRO');
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo('stCreditoDescricao');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao('EXCLUIR');
        $obLista->ultimaAcao->setFuncao(true);
        $obLista->ultimaAcao->addCampo('1', 'stCodCredito');
        $obLista->ultimaAcao->setLink("javascript:excluirCredito();");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace("\n","",$stHTML);
        $stHTML = str_replace("  ","",$stHTML);
        $stHTML = str_replace("'","\\'",$stHTML);
    } else {
        $stHTML = '&nbsp;';
    }

    $stJs = "d.getElementById('spnListaGrupos').innerHTML = '".$stHTML."';";

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case "buscaProcesso":
        include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
        $obRProcesso  = new RProcesso;
        if ($_REQUEST['inProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_REQUEST['inProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();
            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inProcesso.value = "";';
                $stJs .= 'f.inProcesso.focus();';
                $stJs .= "alertaAviso('@Processo não encontrado. (".$_REQUEST["inProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaContribuinteInicio":
        if ($_REQUEST["inCodContribuinteInicial"]) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_REQUEST["inCodContribuinteInicial"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'f.inCodContribuinteInicial.value = "";';
                $stJs .= 'f.inCodContribuinteInicial.focus();';
                $stJs .= "alertaAviso('@Contribuinte não encontrado. (".$_REQUEST["inCodContribuinteInicial"].")','form','erro','".Sessao::getId()."');";
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaContribuinteFinal":
        if ($_REQUEST["inCodContribuinteFinal"]) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_REQUEST["inCodContribuinteFinal"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'f.inCodContribuinteFinal.value = "";';
                $stJs .= 'f.inCodContribuinteFinal.focus();';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_REQUEST["inCodContribuinteFinal"].")','form','erro','".Sessao::getId()."');";
            }
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "atualizarRemissao":
        $stJs = "f.submit();";
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "ExcluirGrupoCredito":
        if ($_REQUEST["inIndice1"]) {
            $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
            $arListaGrupoCreditoTMP = array();
            $inTotalDados = count( $arListaGrupoCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] != $_GET["inIndice1"]) {
                    $arListaGrupoCreditoTMP[] = $arListaGrupoCreditoSessao[$inX];
                }
            }

            Sessao::write( "arListaGrupoCredito", $arListaGrupoCreditoTMP );

            $rsListaGrupoCredito = new RecordSet;
            $rsListaGrupoCredito->preenche( $arListaGrupoCreditoTMP );

            $stJs = montaListaGrupoCredito( $rsListaGrupoCredito );
            sistemaLegado::executaFrameOculto( $stJs );
        }
        break;

    case 'ExcluirCredito':
        if ($_REQUEST['inIndice1']) {
            $arListaCreditoSessao = Sessao::read('arListaCredito');
            $arListaCreditoTMP = array();
            $inTotalDados = count($arListaCreditoSessao);
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arListaCreditoSessao[$inX]['stCodCredito'] != $_GET['inIndice1']) {
                    $arListaCreditoTMP[] = $arListaCreditoSessao[$inX];
                }
            }

            Sessao::write('arListaCredito', $arListaCreditoTMP);

            $rsListaCredito = new RecordSet;
            $rsListaCredito->preenche($arListaCreditoTMP);

            $stJs = montaListaCredito($rsListaCredito);
            sistemaLegado::executaFrameOculto($stJs);
        }
        break;

    case "IncluirGrupoCredito":
        if ($request->get("inCodGrupo")) {            
            $arListaGrupoCreditoSessao = Sessao::read( "arListaGrupoCredito" );
            $boIncluir = true;
            $inTotalDados = count( $arListaGrupoCreditoSessao );
            for ($inX=0; $inX<$inTotalDados; $inX++) {
                if ($arListaGrupoCreditoSessao[$inX]["stCodGrupo"] == $request->get("inCodGrupo")) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arDados = explode( "/", $request->get("inCodGrupo") );
                $obRARRGrupo = new RARRGrupo;
                $obRARRGrupo->setCodGrupo( $arDados[0] );
                $obRARRGrupo->setExercicio( $arDados[1] );
                $obRARRGrupo->consultarGrupo();
                $obRARRGrupo->listarCreditos($rsCreditos);
                $arListaGrupoCreditoSessao[$inTotalDados]["stCodGrupo"] = $request->get("inCodGrupo");
                $arListaGrupoCreditoSessao[$inTotalDados]["stGrupoDescricao"] = $obRARRGrupo->getDescricao();
                
                foreach ($rsCreditos->getElementos() as $creditos) {
                    $creditos['selecionado'] = true;
                    $arCreditosGrupo[] = $creditos;
                }

                Sessao::write($request->get("inCodGrupo"), $arCreditosGrupo);
                Sessao::write( "arListaGrupoCredito", $arListaGrupoCreditoSessao );

                $rsListaGrupoCredito = new RecordSet;
                $rsListaGrupoCredito->preenche( $arListaGrupoCreditoSessao );

                $stJs = montaListaGrupoCredito( $rsListaGrupoCredito );
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Grupo de crédito já está na lista. (".$_GET["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodGrupo.value = '';";
                $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;'";
            }

            echo $stJs;
        }
        break;

    case 'IncluirCredito':
        if ($_GET['inCodCredito']) {
            $arListaCreditoSessao = Sessao::read('arListaCredito');
            $boIncluir = true;
            $inTotalDados = count($arListaCreditoSessao);
            for ($inX=0; $inX < $inTotalDados; $inX++) {
                if ($arListaCreditoSessao[$inX]['stExercicio'].$arListaCreditoSessao[$inX]['stCodCredito'] == $_GET['stExercicio'].$_GET['inCodCredito']) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $obRMONCredito = new RMONCredito;
                $arDados = explode('.', $_GET['inCodCredito']);
                $obRMONCredito->setCodCredito($arDados[0]);
                $obRMONCredito->setCodEspecie($arDados[1]);
                $obRMONCredito->setCodGenero($arDados[2]);
                $obRMONCredito->setCodNatureza($arDados[3]);
                $obRMONCredito->listarCreditos($rsCreditos);

                $arListaCreditoSessao[$inTotalDados]['stCodCredito'] = $_GET['inCodCredito'];
                $arListaCreditoSessao[$inTotalDados]['stExercicio']  = $_GET['stExercicio'];
                $arListaCreditoSessao[$inTotalDados]['stCreditoDescricao'] = $rsCreditos->getCampo('descricao_credito');

                Sessao::write('arListaCredito', $arListaCreditoSessao);

                $rsListaCredito = new RecordSet;
                $rsListaCredito->preenche($arListaCreditoSessao);

                $stJs = montaListaCredito($rsListaCredito);
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            } else {
                $stJs = "alertaAviso('@Crédito já está na lista. (".$_GET['stExercicio'].' / '.$_GET["inCodCredito"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodCredito.value = '';";
                $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;'";
            }

            echo $stJs;
        }
        break;
    case 'montaTableGrupoCredito':
        $arCreditosGrupo = Sessao::read($_REQUEST['stCodGrupo']);

        if (empty($arCreditosGrupo)) {
            $arGrupoCredito = explode('/', $_REQUEST['stCodGrupo']);

            $obRARRGrupo = new RARRGrupo;
            $obRARRGrupo->setCodGrupo( $arGrupoCredito[0] );
            $obRARRGrupo->setExercicio( $arGrupoCredito[1] );
            $obRARRGrupo->listarCreditos($rsCreditos);

            foreach ($rsCreditos->arElementos as $creditos) {
                $creditos['selecionado'] = true;
                $arCreditosGrupo[] = $creditos;
            }

            Sessao::write($_REQUEST['stCodGrupo'], $arCreditosGrupo);
        } else {
            $stJs = '';
            //cria o script para os que não estão selecionados
            foreach ($arCreditosGrupo as $creditos) {
                if (!$creditos['selecionado']) {
                    $stJs .= "d.getElementById('chkAcao_".$creditos['cod_credito'].".".$creditos['cod_genero'].".".$creditos['cod_especie'].".".$creditos['cod_natureza']."').removeAttribute('checked', 0); ";
                }
            }
        }

        $obChkAcao = new Checkbox;
        $obChkAcao->setId('chkAcao_[cod_credito].[cod_genero].[cod_especie].[cod_natureza]');
        $obChkAcao->setName('chkAcao_[cod_credito].[cod_genero].[cod_especie].[cod_natureza]');
        $obChkAcao->setValue('[cod_credito].[cod_genero].[cod_especie].[cod_natureza]');
        $obChkAcao->setChecked('true');
        $obChkAcao->obEvento->setOnClick("selecionaCreditoGrupoRemir(this, '".$_REQUEST['stCodGrupo']."');");

        $rsRecordCreditos = new RecordSet;
        $rsRecordCreditos->preenche($arCreditosGrupo);

        $obTable = new Table();
        $obTable->setRecordset($rsRecordCreditos);

        $obTable->Head->addCabecalho('Código',10);
        $obTable->Head->addCabecalho('Descrição',40);
        $obTable->Head->addCabecalho('Remir',2);

        $obTable->Body->addCampo('[cod_credito].[cod_genero].[cod_especie].[cod_natureza]','C');
        $obTable->Body->addCampo('[descricao_credito]','E');
        $obTable->Body->addComponente($obChkAcao);

        $obTable->montaHTML(true);

        $obSpnListaCreditosGrupo = new Span;
        $obSpnListaCreditosGrupo->setStyle("height: 200px; overflow: scroll");
        $obSpnListaCreditosGrupo->setId("listaCreditosGrupo");
        $obSpnListaCreditosGrupo->setValue($obTable->getHtml());

        $obSpnListaCreditosGrupo->montaHTML();

        $stHTML = $obSpnListaCreditosGrupo->getHTML();
        $stHTML = str_replace("\'","'",$stHTML);

        if ($stJs) SistemaLegado::executaFrameOculto($stJs);
        echo $stHTML;
        break;

    case 'selecionaCreditoGrupoRemir':
        $arCreditosGrupo = Sessao::read($_REQUEST['stCodGrupo']);

        $arTMP = array();
        foreach ($arCreditosGrupo as $creditos) {
            if ($creditos['cod_credito'].".".$creditos['cod_genero'].".".$creditos['cod_especie'].".".$creditos['cod_natureza'] == $_REQUEST['stCredito']) {
                $creditos['selecionado'] = $_REQUEST['isChecked'] == 'true' ? true : false;
            }

            $arTMP[] = $creditos;
        }

        Sessao::write($_REQUEST['stCodGrupo'], $arTMP);
        break;
}

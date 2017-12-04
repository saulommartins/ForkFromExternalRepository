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

    * Página de Frame Oculto de Conceder Licenca
    * Data de Criação   : 17/03/2008

    * @author Analista: Fábio Bertoldi
    * @author Programador: Fernando Piccini Cercato

    * $Id: OCConcederLicenca.php 59845 2014-09-15 19:32:00Z carolina $

    * Casos de uso: uc-05.01.28
*/



include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_COMPONENTES."ITextBoxSelectTipoEdificacao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMResponsavelTecnico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoEdificacao.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMParcelamentoSolo.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteamentoLoteOrigem.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicencaResponsavelTecnico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLicenca.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );

function montaListaResponsaveis($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista );
        $obLista->setTitulo ( "Lista de Responsáveis Técnicos" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Código" );
        $obLista->ultimoCabecalho->setWidth ( 6 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Nome" );
        $obLista->ultimoCabecalho->setWidth ( 20 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Registro" );
        $obLista->ultimoCabecalho->setWidth ( 15 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Profissão" );
        $obLista->ultimoCabecalho->setWidth ( 10 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "num_cgm" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "nom_cgm" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "[nom_registro] - [num_registro]" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setCampo ( "nom_profissao" );
        $obLista->commitDado ();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirResponsavel();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1", "num_cgm" );
        $obLista->ultimaAcao->addCampo ( "inIndice2", "num_profissao" );
        $obLista->commitAcao ();

        $obLista->montaHTML ();
        $stHTML =  $obLista->getHtml ();
        $stHTML = str_replace ( "\n","",$stHTML );
        $stHTML = str_replace ( "  ","",$stHTML );
        $stHTML = str_replace ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaRespTec').innerHTML = '".$stHTML."';\n";

    return $js;
}

switch ($_REQUEST['stCtrl']) {
    case "montaLicenca":
        if ( strlen($_GET["stLicenca"]) ) {
            $obTCIMLicenca = new TCIMLicenca;
            $obTCIMLicenca->recuperaMaxLicenca( $rsMaxLicenca );
            if ( $rsMaxLicenca->Eof() )
                $inTamanho = 1;
            else
                $inTamanho = strlen( $rsMaxLicenca->getCampo( "cod_licenca" ) );

            $stDados = str_replace( "/", "", $_GET["stLicenca"] );

            $inTamanhoOrigem = strlen( $stDados );
            if ($inTamanhoOrigem >= 5) {
                $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
                $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );
                $obTCIMLicenca->setDado('cod_licenca', $stCodigo );
                $obTCIMLicenca->setDado('exercicio', $stAno );
                $obTCIMLicenca->recuperaPorChave( $rsInscricao );
                if ( $rsInscricao->getNumLinhas() < 1 )
                    $boErro= true;
                else {
                    $stValor = "";
                    $inTamanhoOrigem -= 4;
                    for ($inX=0; $inX<$inTamanho-$inTamanhoOrigem; $inX++) {
                        $stValor .= "0";
                    }

                    $stValor .= $stCodigo;
                    $stJs = 'f.stLicenca.value = "'.$stValor.'/'.$stAno.'";';
                    echo $stJs;
                }
            }else
                $boErro= true;

            if ($boErro) {
                $stJs = 'f.stLicenca.value = "";';
                $stJs .= 'f.stLicenca.focus();';
                $stJs .= "alertaAviso('@Código Licença ativa inválido (".$_GET["stLicenca"].").','form','erro','".Sessao::getId()."');";
                echo $stJs;
            }
        }
        break;

    case "BuscaLocalizacao":
        if (!$_GET['stChaveLocalizacao']) {
            $stJs  = 'f.stChaveLocalizacao.value = "";';
            $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '&nbsp;';\n";
        } else {
            $obRCIMLocalizacao = new RCIMLocalizacao;
            $obRCIMLocalizacao->setValorComposto( $_GET['stChaveLocalizacao'] );
            $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
            if ( $rsLocalizacao->getNumLinhas() > 0 ) {
                $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
                $stCodigo = $rsLocalizacao->getCampo("cod_localizacao");
                $stJs = "d.getElementById('stNomeChaveLocalizacao').innerHTML = '".$stDescricao."';\n";
                
                $obRCIMLote = new RCIMLote;

                $inTipoLicenca= Sessao::read('inTipoLicenca');
                if ($inTipoLicenca == 7) {
                    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $stCodigo );
                    $obRCIMLote->mostrarLotes( $rsListaLote );
                    $rsListaLote->ordena( "valor" );
                } else  if ($inTipoLicenca == 8) {
                    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $stCodigo );
                    $obRCIMLote->mostrarLotesParcelamento( $rsListaLote );
                    $rsListaLote->ordena( "valor" );
                } else  if ($inTipoLicenca == 9) {
                    $obRCIMLote->obRCIMLocalizacao->setCodigoLocalizacao( $stCodigo );
                    $obRCIMLote->mostrarLotesParcelamento( $rsListaLote );
                    $rsListaLote->ordena( "valor" );
                }
              
                $stJs .= "limpaSelect( f.cmbLotes, 1 ); \n";
                $stJs .= "f.cmbLotes[0] = new Option('Selecione', '', 'selected');\n";
                $inContador = 1;
                while ( !$rsListaLote->eof() ) {
                    $stJs .= "f.cmbLotes.options[$inContador] = new Option('".$rsListaLote->getCampo("valor")."','".$rsListaLote->getCampo("cod_lote")."'); \n";

                    $rsListaLote->proximo();
                    $inContador++;
                }
            } else {
                $stJs  = 'f.stChaveLocalizacao.value = "";';
                $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '&nbsp;';\n";
                $stJs .= "limpaSelect( f.cmbLotes, 1 ); \n";
                $stJs .= "f.cmbLotes[0] = new Option('Selecione', '', 'selected');\n";
                $stJs .= "alertaAviso('@Localização inválida. (".$_GET["stChaveLocalizacao"].")', 'form','erro','".Sessao::getId()."');";
            }
        }

        echo $stJs;
        break;

    case "LoteSelecionado": //lote selecionado deve atualizar o combo do loteamento/desmembramento/aglutinacao
        if ($_GET["cmbLotes"]) {
            if ($_GET["inTipoLicenca"] == 7) {
                $obTCIMLoteamentoLoteOrigem = new TCIMLoteamentoLoteOrigem;
                $stFiltro = " WHERE loteamento_lote_origem.cod_lote = ".$_GET["cmbLotes"];
                $obTCIMLoteamentoLoteOrigem->listaLoteamentosPorLote( $rsLista, $stFiltro );

                $stJs = "limpaSelect(f.cmbLoteamento,1); \n";
                $stJs .= "f.cmbLoteamento[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;

                while ( !$rsLista->eof() ) {
                    $stJs .= "f.cmbLoteamento.options[$inContador] = new Option('".$rsLista->getCampo("cod_loteamento")." - ".$rsLista->getCampo("nom_loteamento")."','".$rsLista->getCampo("cod_loteamento")."'); \n";

                    $rsLista->proximo();
                    $inContador++;
                }

                echo $stJs;
            }else
            if ($_GET["inTipoLicenca"] == 8) { //desmembramento
                $obTCIMParcelamentoSolo = new TCIMParcelamentoSolo;
                $stFiltro = " WHERE cod_tipo = 2 AND cod_lote = ".$_GET["cmbLotes"];
                $obTCIMParcelamentoSolo->recuperaTodos( $rsLista, $stFiltro );
                $stJs = "limpaSelect(f.cmbDesmembramento,1); \n";
                $stJs .= "f.cmbDesmembramento[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;

                while ( !$rsLista->eof() ) {
                    $stJs .= "f.cmbDesmembramento.options[$inContador] = new Option('".$rsLista->getCampo("cod_parcelamento")." - ".$rsLista->getCampo("dt_parcelamento")."','".$rsLista->getCampo("cod_parcelamento")."'); \n";

                    $rsLista->proximo();
                    $inContador++;
                }

                echo $stJs;
            }else
            if ($_GET["inTipoLicenca"] == 9) { //aglutinacao
                $obTCIMParcelamentoSolo = new TCIMParcelamentoSolo;
                $stFiltro = " WHERE cod_tipo = 1 AND cod_lote = ".$_GET["cmbLotes"];
                $obTCIMParcelamentoSolo->recuperaTodos( $rsLista, $stFiltro );
                $stJs = "limpaSelect(f.cmbAglutinação,1); \n";
                $stJs .= "f.cmbAglutinação[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;

                while ( !$rsLista->eof() ) {
                    $stJs .= "f.cmbAglutinação.options[$inContador] = new Option('".$rsLista->getCampo("cod_parcelamento")." - ".$rsLista->getCampo("dt_parcelamento")."','".$rsLista->getCampo("cod_parcelamento")."'); \n";

                    $rsLista->proximo();
                    $inContador++;
                }

                echo $stJs;
            }
        }
        break;

    case "PreencheImovel":
        if ($_GET["inCodImovel"]) {
            $obTCIMImovel = new TCIMImovel;

            $stFiltro = " AND I.inscricao_municipal = ".$_GET["inCodImovel"];
            $obTCIMImovel->recuperaInscricaoImobiliario( $rsImoveis, $stFiltro );
            if ( !$rsImoveis->eof() ) {
                $stEnderecoImovel = $rsImoveis->getCampo("logradouro");
                if ( $rsImoveis->getCampo("numero") )
                    $stEnderecoImovel .= ", ".$rsImoveis->getCampo("numero");

                if ( $rsImoveis->getCampo("complemento") )
                    $stEnderecoImovel .= " - ".$rsImoveis->getCampo("complemento");

                $stEnderecoImovel = str_replace ("'", "\'", $stEnderecoImovel );
                $stJs = "d.getElementById('stImovel').innerHTML = '".$stEnderecoImovel."';\n";

                $obTCIMConstrucaoEdificacao = new TCIMConstrucaoEdificacao;
                $stFiltro = " where ii.inscricao_municipal = ".$_GET["inCodImovel"];
                $obTCIMConstrucaoEdificacao->listaDadosEdificacaoImovel( $rsListaEdifConst, $stFiltro );
                $rsListaEdifConst->addFormatacao ( 'area_lote', 'NUMERIC_BR' );

                $stJs .= "limpaSelect(f.cmbEdifConst,1); \n";
                $stJs .= "f.cmbEdifConst[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;

                while ( !$rsListaEdifConst->eof() ) {
                    $stJs .= "f.cmbEdifConst.options[$inContador] = new Option('".$rsListaEdifConst->getCampo("cod_construcao")."-".$rsListaEdifConst->getCampo("nome_tipo_edificacao")."-".$rsListaEdifConst->getCampo("area_edificacao")."','".$rsListaEdifConst->getCampo("cod_construcao")."-".$rsListaEdifConst->getCampo("cod_tipo")."-".$rsListaEdifConst->getCampo("autodep")."-".$rsListaEdifConst->getCampo("cod_construcao_dependente")."'); \n";

                    $rsListaEdifConst->proximo();
                    $inContador++;
                }

            } else {
                $stJs = "limpaSelect(f.cmbEdifConst,1); \n";
                $stJs .= "f.cmbEdifConst[0] = new Option('Selecione','', 'selected');\n";

                $stJs .= "f.inCodImovel.value ='';\n";
                $stJs .= "f.inCodImovel.focus();\n";
                $stJs .= "d.getElementById('stImovel').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Imóvel informado não existe. (".$_GET["inCodImovel"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs = "limpaSelect(f.cmbEdifConst,1); \n";
            $stJs .= "f.cmbEdifConst[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "d.getElementById('stImovel').innerHTML = '&nbsp;';\n";
        }

        echo $stJs;
        break;

    case "carregaResponsavelEdificao":
        $js = "";
        $stFiltro = " WHERE cod_licenca = ".$_GET["inCodLicenca"]." AND exercicio = '".$_GET["inExercicio"]."' AND timestamp = ( SELECT max(timestamp) from imobiliario.licenca_responsavel_tecnico where cod_licenca = ".$_GET["inCodLicenca"]." AND exercicio = '".$_GET["inExercicio"]."' ) ";
        $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
        $obTCIMLicencaResponsavelTecnico->recuperaTodos( $rsListaResps, $stFiltro );
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        $nregistros = count ( $arResponsaveisSessao );
        $obRResponsavel = new RCEMResponsavelTecnico;
        while ( !$rsListaResps->Eof() ) {
            $obRResponsavel->setNumCgm( $rsListaResps->getCampo("numcgm") );
            $arProfissoesSessao = Sessao::read('arProfissoes');
            if ( $arProfissoesSessao )
                $obRResponsavel->setProfissoes( $arProfissoesSessao );

            $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
            if ( !$rsListaResponsavel->eof() ) {
                $arResponsaveisSessao[$nregistros]['num_profissao'] = $rsListaResponsavel->getCampo("cod_profissao");
                $arResponsaveisSessao[$nregistros]['num_cgm']       = $rsListaResps->getCampo("numcgm");
                $arResponsaveisSessao[$nregistros]['nom_cgm']       = $rsListaResponsavel->getCampo("nom_cgm");
                $arResponsaveisSessao[$nregistros]['nom_profissao'] = $rsListaResponsavel->getCampo("nom_profissao");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['num_registro']  = $rsListaResponsavel->getCampo("num_registro");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['sequencia']     = $rsListaResponsavel->getCampo("sequencia");
                $nregistros++;
            }

            $rsListaResps->proximo();
        }

        Sessao::write('arResponsaveis', $arResponsaveisSessao);
        $rsListaResponsaveis = new RecordSet;
        $rsListaResponsaveis->preenche ( $arResponsaveisSessao );
        $rsListaResponsaveis->ordena("num_cgm");
        $js .= montaListaResponsaveis ( $rsListaResponsaveis );
        echo $js;
        break;

    case "carregaResponsavel":
        $js = "";
        if ($_GET["inTipoNovaEdificacao"]) {
            $obITextBoxSelectTipoEdificacao = new ITextBoxSelectTipoEdificacao;
            $obITextBoxSelectTipoEdificacao->setNull( true );
            $obITextBoxSelectTipoEdificacao->obTxtTipoEdificacao->setValue( $_GET["inTipoNovaEdificacao"] );
            $obITextBoxSelectTipoEdificacao->obCmbTipoEdificacao->setValue( $_GET["inTipoNovaEdificacao"] );
            $obITextBoxSelectTipoEdificacao->setTitle( "Informe o tipo de edificação da nova edificação." );

            $obFormulario = new Formulario;
            $obITextBoxSelectTipoEdificacao->geraFormulario ( $obFormulario );
            $obFormulario->montaInnerHTML();

            $js .= "d.getElementById('spnTipoEdificacao').innerHTML = '".$obFormulario->getHTML()."';\n";
        }

        $stFiltro = " WHERE cod_licenca = ".$_GET["inCodLicenca"]." AND exercicio = '".$_GET["inExercicio"]."' AND timestamp = ( SELECT max(timestamp) from imobiliario.licenca_responsavel_tecnico where cod_licenca = ".$_GET["inCodLicenca"]." AND exercicio = '".$_GET["inExercicio"]."' ) ";
        $obTCIMLicencaResponsavelTecnico = new TCIMLicencaResponsavelTecnico;
        $obTCIMLicencaResponsavelTecnico->recuperaTodos( $rsListaResps, $stFiltro );
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        $nregistros = count ( $arResponsaveisSessao );
        $obRResponsavel = new RCEMResponsavelTecnico;
        while ( !$rsListaResps->Eof() ) {
            $obRResponsavel->setNumCgm( $rsListaResps->getCampo("numcgm") );
            $arProfissoesSessao = Sessao::read('arProfissoes');
            if ( $arProfissoesSessao )
                $obRResponsavel->setProfissoes( $arProfissoesSessao );

            $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
            if ( !$rsListaResponsavel->eof() ) {
                $arResponsaveisSessao[$nregistros]['num_profissao'] = $rsListaResponsavel->getCampo("cod_profissao");
                $arResponsaveisSessao[$nregistros]['num_cgm']       = $rsListaResps->getCampo("numcgm");
                $arResponsaveisSessao[$nregistros]['nom_cgm']       = $rsListaResponsavel->getCampo("nom_cgm");
                $arResponsaveisSessao[$nregistros]['nom_profissao'] = $rsListaResponsavel->getCampo("nom_profissao");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['num_registro']  = $rsListaResponsavel->getCampo("num_registro");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['sequencia']     = $rsListaResponsavel->getCampo("sequencia");
                $nregistros++;
            }

            $rsListaResps->proximo();
        }
        Sessao::write('arResponsaveis', $arResponsaveisSessao);
        $rsListaResponsaveis = new RecordSet;
        $rsListaResponsaveis->preenche ( $arResponsaveisSessao );
        $rsListaResponsaveis->ordena("num_cgm");
        $js .= montaListaResponsaveis ( $rsListaResponsaveis );
        echo $js;
        break;

    case "IncluirResponsavel":
        $inCGM = $_GET["inRespTecnico"];

        $obRResponsavel = new RCEMResponsavelTecnico;
        $obRResponsavel->setNumCgm( $inCGM );
        $arProfissoesSessao   = Sessao::read('arProfissoes');
        $arResponsaveisSessao = Sessao::read('arResponsaveis');
        if ( $arProfissoesSessao )
            $obRResponsavel->setProfissoes( $arProfissoesSessao );

        $obRResponsavel->listarResponsavelTecnico( $rsListaResponsavel );
        if ( !$rsListaResponsavel->eof() ) {
            $nregistros = count ( $arResponsaveisSessao );
            $cont = 0;
            $insere = true;
            while ($cont < $nregistros) {
                if ( ( $arResponsaveisSessao[$cont]['num_cgm'] == $inCGM ) && ( $arResponsaveisSessao[$cont]['num_profissao'] == $rsListaResponsavel->getCampo("cod_profissao") ) ) {
                    //codigo ja estava na lista!
                    $js = 'f.inRespTecnico.value = "";';
                    $js .= 'f.inRespTecnico.focus();';
                    $js .= 'd.getElementById("stRespTecnico").innerHTML = "&nbsp;";';
                    $js .= "alertaAviso('@Código do Responsável já está na lista. (".$inCGM.")','form','erro','".Sessao::getId()."');";

                    echo $js;
                    $insere = false;
                    break;
                } else {
                    $cont++;
                }
            }

            if ($insere) {
                $arResponsaveisSessao[$nregistros]['num_profissao'] = $rsListaResponsavel->getCampo("cod_profissao");
                $arResponsaveisSessao[$nregistros]['num_cgm']       = $inCGM;
                $arResponsaveisSessao[$nregistros]['nom_cgm']       = $rsListaResponsavel->getCampo("nom_cgm");
                $arResponsaveisSessao[$nregistros]['nom_profissao'] = $rsListaResponsavel->getCampo("nom_profissao");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['num_registro']  = $rsListaResponsavel->getCampo("num_registro");
                $arResponsaveisSessao[$nregistros]['nom_registro']  = $rsListaResponsavel->getCampo("nom_registro");
                $arResponsaveisSessao[$nregistros]['sequencia']     = $rsListaResponsavel->getCampo("sequencia");

                Sessao::write('arResponsaveis', $arResponsaveisSessao);
                $rsListaResponsaveis = new RecordSet;
                $rsListaResponsaveis->preenche ( $arResponsaveisSessao );
                $rsListaResponsaveis->ordena("num_cgm");
                $js = montaListaResponsaveis ( $rsListaResponsaveis );
                $js .= 'f.inRespTecnico.value = "";';
                $js .= 'f.inRespTecnico.focus();';
                $js .= 'd.getElementById("stRespTecnico").innerHTML = "&nbsp;";';
                echo $js;
            }
        }
        break;

    case "LimparSessao":
        Sessao::remove('arResponsaveis');
        break;

    case "limpaResponsavel":
        Sessao::remove('arResponsaveis');
        $rsResponsaveis = new RecordSet;
        $stJs = montaListaResponsaveis( $rsResponsaveis );
        echo $stJs;
        break;

    case "NovaUnidadeConstrucao":

        /*$obConstrucaoOutros = new RCIMConstrucaoOutros;
        $obConstrucaoOutros->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributosConstrucao );

        $obMontaAtributosConstrucao = new MontaAtributos;
        $obMontaAtributosConstrucao->setTitulo     ( "Atributos"            );
        $obMontaAtributosConstrucao->setName       ( "AtributoConstrucao_"  );
        $obMontaAtributosConstrucao->setRecordSet  ( $rsAtributosConstrucao );

        foreach ($obMontaAtributosConstrucao->getRecordSet()->arElementos as $key => $value) {
            $obMontaAtributosConstrucao->getRecordSet()->arElementos[$key]['nao_nulo'] = 't';
            $obMontaAtributosConstrucao->getRecordSet()->arElementos[$key]['nom_atributo'] = '*'.$value[nom_atributo];
        }*/
 
        $stDescricao= Sessao::read('stDescricao');
        $obTxtDescricao = new TextArea;
        $obTxtDescricao->setName ("stDescricao");
        $obTxtDescricao->setTitle ("Descrição da nova construção.");
        $obTxtDescricao->setRotulo ("*Descrição");
        $obTxtDescricao->setValue ($stDescricao);
        $obTxtDescricao->setNull (true);

        $obFormulario = new Formulario;
        $obFormulario->addComponente ($obTxtDescricao);
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnTipoEdificacao').innerHTML = '".$obFormulario->getHTML()."';\n";
        $js .= "f.stEval.value = '';\n";
        //$js .= "d.getElementById('lsAtributos').innerHTML = '';\n";
        $js .= "montaAtributosEdificacao();\n";

        echo $js;
        break;

    case "NovaUnidadeEdificacao":
        $obITextBoxSelectTipoEdificacao = new ITextBoxSelectTipoEdificacao;
        $obITextBoxSelectTipoEdificacao->setTitle( "Informe o tipo de edificação da nova edificação." );
        $obITextBoxSelectTipoEdificacao->obTxtTipoEdificacao->obEvento->setOnChange("montaAtributosEdificacao();");
        $obITextBoxSelectTipoEdificacao->obCmbTipoEdificacao->obEvento->setOnChange("montaAtributosEdificacao();");
        $obITextBoxSelectTipoEdificacao->setNull (true);

        $obFormulario = new Formulario;

        $obITextBoxSelectTipoEdificacao->geraFormulario ( $obFormulario );
        $obFormulario->montaInnerHTML();

        $js = "d.getElementById('spnTipoEdificacao').innerHTML = '".$obFormulario->getHTML()."';\n";
        $js .= "d.getElementById('lsAtributos').innerHTML = '';\n";

        echo $js;
        break;

    case "montaAtributosEdificacao":

        if ($_REQUEST["stNovaUnidade"] == 'construcao') {
            $obRCIMConstrucaoOutros = new RCIMConstrucaoOutros;
            $obRCIMConstrucaoOutros->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
            $obUnidade = true;
        } elseif ($_REQUEST["inTipoEdificacao"]) {
                $obRCIMEdificacao = new RCIMEdificacao;
                $obRCIMEdificacao->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo" => $_REQUEST["inTipoEdificacao"] ) );
                $obRCIMEdificacao->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
                $obUnidade = true;
            } else {
                $obUnidade = false;
            }

            if ($obUnidade == true) {
                $obMontaAtributos = new MontaAtributos;
                $obMontaAtributos->setTitulo     ( "Atributos"  );
                $obMontaAtributos->setName       ( "Atributo_"  );
                $obMontaAtributos->setRecordSet  ( $rsAtributos );

                foreach ($obMontaAtributos->getRecordSet()->arElementos as $key => $value) {
                    $obMontaAtributos->getRecordSet()->arElementos[$key]['nao_nulo'] = 't';
                    $obMontaAtributos->getRecordSet()->arElementos[$key]['nom_atributo'] = '*'.$value[nom_atributo];
                }

                $obFormulario = new Formulario;
                $obMontaAtributos->geraFormulario ( $obFormulario );
                $obFormulario->montaInnerHTML();
                $obFormulario->obJavaScript->montaJavaScript();
                $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
                $stEval = str_replace("\n","",$stEval);
                $stHTML = $obFormulario->getHTML();
            } else {
                $stEval = "&nbsp;";
                $stHTML = "&nbsp;";
                //$stEval = "&'';";
                //$stHTML = "&'';";
            }

        $stJs  = "f.stEval.value = '$stEval'; \n";
        $stJs .= "d.getElementById('lsAtributos').innerHTML = '".$stHTML."';\n";
        SistemaLegado::executaFrameOculto($stJs);

            /*$obMontaAtributosEdificacao = new MontaAtributos;
            $obMontaAtributosEdificacao->setTitulo     ( "Atributos"            );
            $obMontaAtributosEdificacao->setName       ( "AtributoEdificacao_"  );
            $obMontaAtributosEdificacao->setRecordSet  ( $rsAtributosEdificacao );

            foreach ($obMontaAtributosEdificacao->getRecordSet()->arElementos as $key => $value) {
                $obMontaAtributosEdificacao->getRecordSet()->arElementos[$key]['nao_nulo'] = 't';
                $obMontaAtributosEdificacao->getRecordSet()->arElementos[$key]['nom_atributo'] = '*'.$value[nom_atributo];
            }

            $obFormulario = new Formulario;
            $obMontaAtributosEdificacao->geraFormulario ( $obFormulario );
            $obFormulario->montaInnerHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
            $stHTML = $obFormulario->getHTML();
        } else {
            $stEval = "&nbsp;";
            $stHTML = "&nbsp;";
        }

        $stJs  = "f.stEval.value = '$stEval'; \n";
        $stJs .= "d.getElementById('lsAtributosEdificacao').innerHTML = '".$stHTML."';";
        SistemaLegado::executaFrameOculto($stJs);*/

        echo $js;
        break;
}

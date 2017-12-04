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
    * Página de Frame Oculto da nota avulsa
    * Data de Criação   : 19/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.03.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php" );
include_once ( CAM_GA_ADM_COMPONENTES."ITextBoxSelectDocumento.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMServico.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );

function montaListaServicos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Serviços" );
        $obLista->setTotaliza                  ( "flValorLancado,Valor Total,right,8" );

        $obLista->setMostraPaginacao           ( false );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Serviços"             );
        $obLista->ultimoCabecalho->setWidth    ( 20                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Aliquota (%)"         );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Declarado"      );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Dedução Incondicional");
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Dedução Legal"        );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Lançado"        );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[stServico] - [stServicoNome]"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flAliquota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorDeclarado"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flDeducao"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flDeducaoLegal"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorRetido"        );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorLancado"       );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:alterarServico();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2", "flAliquota" );
        $obLista->ultimaAcao->addCampo         ( "inIndice3", "flValorDeclarado" );
        $obLista->ultimaAcao->addCampo         ( "inIndice4", "flDeducao" );
        $obLista->ultimaAcao->addCampo         ( "inIndice5", "flValorLancado" );
        $obLista->ultimaAcao->addCampo         ( "inIndice6", "flValorRetido" );
        $obLista->ultimaAcao->addCampo         ( "inIndice7", "flDeducaoLegal" );

        $obLista->commitAcao                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirServico();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2", "flAliquota" );
        $obLista->ultimaAcao->addCampo         ( "inIndice3", "flValorDeclarado" );
        $obLista->ultimaAcao->addCampo         ( "inIndice4", "flDeducao" );
        $obLista->ultimaAcao->addCampo         ( "inIndice5", "flValorLancado" );
        $obLista->ultimaAcao->addCampo         ( "inIndice6", "flValorRetido" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }

    $js = "d.getElementById('spnListaServico').innerHTML = '".$stHTML."';\n";

    return $js;
}

$obMontaServico = new MontaServico;
$obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
$obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );

$boSetaData = false;
switch ($_REQUEST['stCtrl']) {
    case "Download":
        $content_type = 'application/sxw';
        $arDadosSessao = Sessao::read( "dados" );
        $stDocumento = $arDadosSessao[0]["nome_arquivo_tmp"];
        $download = $arDadosSessao[0]["nome_arquivo"];
        $download .= ".odt";
        header ("Content-Length: " . filesize( $stDocumento ));
        header("Content-type: $content_type");
        header("Content-Disposition: attachment; filename=\"$download\"");
        readfile( $stDocumento );
        break;

    case "PreencheCGM": //cgm do tomador
        if ($_REQUEST["inCGM"]) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_REQUEST["inCGM"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'f.inCGM.value = "";';
                $stJs .= 'f.inCGM.focus();';
                $stJs .= 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_REQUEST["inCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                //listar empresas para preenxer combo empresa
                $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
                $obRCEMInscricaoEconomica->obRCGM->setNumCGM( $_REQUEST["inCGM"] );
                $obRCEMInscricaoEconomica->listarInscricao( $rsListaInscricao );

                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
                if ( !$rsListaInscricao->Eof() ) {
                    $obCmbEmpresaTomador = new Select;
                    $obCmbEmpresaTomador->setRotulo ( "Empresa do Tomador" );
                    $obCmbEmpresaTomador->setTitle ( "Empresa do tomador de serviços." );
                    $obCmbEmpresaTomador->setName ( "cmbEmpresaTomador" );
                    $obCmbEmpresaTomador->addOption ( "", "Selecione" );
                    $obCmbEmpresaTomador->setCampoId ( "inscricao_economica" );
                    $obCmbEmpresaTomador->setCampoDesc ( "inscricao_economica" );
                    $obCmbEmpresaTomador->preencheCombo ( $rsListaInscricao );
                    $obCmbEmpresaTomador->setNull ( true );
                    $obCmbEmpresaTomador->setStyle ( "width: 220px" );

                    $obFormulario = new Formulario;
                    $obFormulario->addComponente( $obCmbEmpresaTomador );

                    $obFormulario->montaInnerHTML();
                    $stJs .= "d.getElementById('spnEmpresaTomador').innerHTML = '". $obFormulario->getHTML(). "';\n";
                }else
                    $stJs .= "d.getElementById('spnEmpresaTomador').innerHTML = '&nbsp;';\n";
            }
        } else {
            $stJs = 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
        }

        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limpaServico":
        $stJs .= 'f.stChaveServico.value = "";';
        $stJs .= 'f.flAliquota.value = "";';
        $stJs .= 'f.flValorDeclarado.value = "";';
        $stJs .= 'f.flDeducao.value = "";';
        $stJs .= 'f.flDeducaoLegal.value = "0,00";';

        $inX = 0;
        while ($_REQUEST) {
            $inX++;
            $stNome = "inCodServico_".$inX;
            if ($_REQUEST[ $stNome ]) {
                if ($inX > 1) {
                    $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                    $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                }

                $stJs .= 'f.'.$stNome.'.value = "";';
            }else
                break;
        }

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "validaData":
        if ($_REQUEST["stCompetencia"] == "") {

            $stJs = "alertaAviso('@Campo Competência deve ser preenchido antes de setar data.','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.dtEmissao.value = "";';
            sistemaLegado::executaFrameOculto( $stJs );

        } else {

            $dtEmissao = $_REQUEST["dtEmissao"];
            $arData = explode( "/", $dtEmissao );

            if ($arData[1] != $_REQUEST["stCompetencia"] || $arData[2] != $_REQUEST["stExercicio"]) {
                $stJs = "alertaAviso('@Campo Data da Emissão inválido.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.dtEmissao.value = "";';
                sistemaLegado::executaFrameOculto( $stJs );
            }
        }
        break;

    case "alteraCompetencia":
        if ( $_REQUEST["stExercicio"] > Sessao::getExercicio() + 1 ) {

            $stJs = 'f.stCompetencia.value = ""; ';
            $stJs .= "alertaAviso('@Valor inválido no campo Exercicio.','form','erro','".Sessao::getId()."'); ";
            $stJs .= 'f.stExercicio.value = ""; ';
            $stJs .= 'f.stExercicio.focus(); ';
            sistemaLegado::executaFrameOculto( $stJs );
            exit;

        } elseif ( $_REQUEST["stExercicio"] == Sessao::getExercicio() ) {

            $inMes = date ("m");

            if ($_REQUEST["stCompetencia"] <= $inMes) {

                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
                $arGrupoCreditoEscrituracao = preg_split( '/\//', $stCodGrupoCreditoEscrituracao );

                $obTARRVencimentoParcela = new TARRVencimentoParcela;
                $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = '".$arGrupoCreditoEscrituracao[1]."' AND cod_parcela = ".$_REQUEST["stCompetencia"];
                $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );
                
                if ( $rsListaParcela->Eof() ) {
                    $stJs = "alertaAviso('Nenhum calendário fiscal foi definido para o grupo de credito da escrituração.','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto( $stJs );
                    exit;
                }

                $arData = explode( "/", $rsListaParcela->getCampo("data_vencimento") );
                if ( ($_REQUEST["stCompetencia"] < $inMes-1) || (date ("d") >= $arData[0] ) )
                    $boSetaData = true;
            } else {
                $stJs = "alertaAviso('@Valor inválido no campo Competência.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.stCompetencia.value = "";';
                sistemaLegado::executaFrameOculto( $stJs );
            }
        } else {
            $boSetaData = true;
        }

        $stJs = 'f.dtEmissao.value = "";';
        sistemaLegado::executaFrameOculto($stJs);

    case "LimparFormulario":

        Sessao::write( "servicos_retencao", array() );
        Sessao::write( "servicos_retencao_alterando", "" );

        Sessao::write( "servicos_retencao_semrt", array() );
        Sessao::write( "servicos_retencao_alterando_semrt", "" );

    case "montaRetencao":

        $rsUF = new RecordSet;
        $obRCIMLogradouro = new RCIMLogradouro;
        $obRCIMLogradouro->listarUF( $rsUF );

        $rsMunicipios = new RecordSet;

        $obFormulario = new Formulario;

        $obTxtAliquota = new Numerico;
        $obTxtAliquota->setRotulo ( "Alíquota (%)" );
        $obTxtAliquota->setName ( "flAliquota" );
        $obTxtAliquota->setId ( "flAliquota" );
        $obTxtAliquota->setDecimais ( 2 );
        $obTxtAliquota->setNull ( false );
        $obTxtAliquota->setNegativo ( false );
        $obTxtAliquota->setNaoZero ( true );
        $obTxtAliquota->setSize ( 6 );
        $obTxtAliquota->setMaxLength ( 6 );

        $obTxtValorDeclarado = new Numerico;
        $obTxtValorDeclarado->setRotulo ( "Valor Declarado" );
        $obTxtValorDeclarado->setName ( "flValorDeclarado" );
        $obTxtValorDeclarado->setId ( "flValorDeclarado" );
        $obTxtValorDeclarado->setDecimais ( 2 );
        $obTxtValorDeclarado->setMaxValue ( 99999999999999.99 );
        $obTxtValorDeclarado->setNull ( false );
        $obTxtValorDeclarado->setNegativo ( false );
        $obTxtValorDeclarado->setNaoZero ( true );
        $obTxtValorDeclarado->setSize ( 20 );
        $obTxtValorDeclarado->setMaxLength ( 20 );

        $obTxtDeducao = new Numerico;
        $obTxtDeducao->setRotulo ( "Dedução Incondicional" );
        $obTxtDeducao->setTitle ( "Descontos." );
        $obTxtDeducao->setName ( "flDeducao" );
        $obTxtDeducao->setId ( "flDeducao" );
        $obTxtDeducao->setDecimais ( 2 );
        $obTxtDeducao->setMaxValue ( 99999999999999.99 );
        $obTxtDeducao->setNull ( true );
        $obTxtDeducao->setNegativo ( false );
        $obTxtDeducao->setNaoZero ( true );
        $obTxtDeducao->setSize ( 20 );
        $obTxtDeducao->setMaxLength ( 20 );

        $obTxtDeducaoLegal = new Moeda;
        $obTxtDeducaoLegal->setName ( "flDeducaoLegal" );
        $obTxtDeducaoLegal->setRotulo ( "Dedução Legal" );
        $obTxtDeducaoLegal->setTitle ( "Valor em Mercadoria/Material." );
        $obTxtDeducaoLegal->setMaxLength ( 15 );
        $obTxtDeducaoLegal->setSize ( 15 );
        $obTxtDeducaoLegal->setValue ( '0,00' );
        $obTxtDeducaoLegal->setNULL ( false );

        $obMontaServico = new MontaServico;
        $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
        $obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );
        $obMontaServico->geraFormulario( $obFormulario );

        $obFormulario->addComponente( $obTxtAliquota );
        $obFormulario->addComponente( $obTxtValorDeclarado );
        $obFormulario->addComponente( $obTxtDeducao );
        $obFormulario->addComponente( $obTxtDeducaoLegal );

        $obFormulario2 = new Formulario;

        Sessao::write( "setar_data", $boSetaData );

        $obFormulario->montaInnerHTML();
        $stJs = "d.getElementById('spn1').innerHTML = '". $obFormulario->getHTML(). "';\n";

        if ($boSetaData) {
            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->consultar();
            $stCodGrupoNotaAvulsa = $obRARRConfiguracao->getCodigoGrupoNotaAvulsa();
            $arGrupoNotaAvulsa = preg_split( '/\//', $stCodGrupoNotaAvulsa );
            if($stCodGrupoNotaAvulsa == ''){
                $stJs.= "alertaAviso('@Campo Grupo Nota Avulsa precisa ser configurado.','form','erro','".Sessao::getId()."');";
            } else {
                $obTARRVencimentoParcela = new TARRVencimentoParcela;
                $stFiltro = " WHERE cod_grupo = ".$arGrupoNotaAvulsa[0]." AND ano_exercicio = '".$arGrupoNotaAvulsa[1]."' AND cod_parcela = ".$_REQUEST["stCompetencia"];
                $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );

                $obFormulario3 = new Formulario;

                $obDtVencimento = new Data;
                $obDtVencimento->setName ( "dtVencimento" );
                $obDtVencimento->setRotulo ( "*Vencimento" );
                $obDtVencimento->setMaxLength ( 20 );
                $obDtVencimento->setSize ( 10 );
                $obDtVencimento->setNull ( true );
                $obDtVencimento->setValue ( $rsListaParcela->getCampo("data_vencimento") );
                $obDtVencimento->obEvento->setOnChange( "buscaValor('validaData');" );

                $obFormulario3->addComponente ( $obDtVencimento );

                $obFormulario3->montaInnerHTML();
                $stJs .= "d.getElementById('spnData').innerHTML = '". $obFormulario3->getHTML(). "';\n";
            }
        }

        $boTemValores = false;
        $arServicosRentecao = Sessao::read( "servicos_retencao" );
        for ( $inContaRetencao=0; $inContaRetencao<count($arServicosRentecao ); $inContaRetencao++ ) {
            if ($arServicosRentecao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

        $rsListaServicos = new RecordSet;
        $arServicosRetencaoSemRTSessao = Sessao::read( "servicos_retencao_semrt" );
        if ( $arServicosRetencaoSemRTSessao)
            $rsListaServicos->preenche ( $arServicosRetencaoSemRTSessao );

        $stJs2 = null;
        $stJs2 = montaListaServicos( $rsListaServicos );

        sistemaLegado::executaFrameOculto($stJs);
        sistemaLegado::executaFrameOculto($stJs2);

        $obRCEMServico = new RCEMServico;
        $obRCEMServico->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );

        $obRCEMServico->recuperaUltimoNivel( $rsListaNivel );

        $obRCEMServico->setCodigoNivel( 1 );
        $obRCEMServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
        $obRCEMServico->listarServico( $rsListaServico );

        if ( $rsListaServico->getNumLinhas() > 0 ) {

            $obRCEMServico->setValorreduzido( $rsListaServico->getCampo("valor_reduzido") );
            $obRCEMServico->setCodigoNivel( $rsListaNivel->getCampo("cod_nivel") );
            $obRCEMServico->listarServico( $rsListaServicoTMP );

            $stJs = 'f.stChaveServico.value = "'.$rsListaServicoTMP->getCampo("valor_reduzido").'";';
            sistemaLegado::executaFrameOculto($stJs);

            $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
            $obMontaServico->obRCEMServico->setCodigoNivel ( NULL  );
            $obMontaServico->setValorReduzidoServico ( $rsListaServicoTMP->getCampo("valor_reduzido") );
            $obMontaServico->preencheCombos();
        }
        #echo '<h1>FIM</h1>'; exit;
    break;

    case "alterarServico":
        $stServico = $_REQUEST['inIndice1'];
        $flAliquota = $_REQUEST['inIndice2'];
        $flValorDeclarado = $_REQUEST['inIndice3'];
        $flDeducao = $_REQUEST['inIndice4'];
        $flValorLancado = $_REQUEST['inIndice5'];
        $flValorRetido = $_REQUEST['inIndice6'];
        $flDeducaoLegal = $_REQUEST['inIndice7'];
        $arServicosRetencaoSessao = Sessao::read( "servicos_retencao_semrt" );

        $nregistros = count ( $arServicosRetencaoSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if (
                ( $arServicosRetencaoSessao[$inCount]["stServico"] == $stServico ) &&
                ( $arServicosRetencaoSessao[$inCount]["flAliquota"] == $flAliquota ) &&
                ( $arServicosRetencaoSessao[$inCount]["flValorDeclarado"] == $flValorDeclarado ) &&
                ( $arServicosRetencaoSessao[$inCount]["flValorLancado"] == $flValorLancado ) &&
                ( $arServicosRetencaoSessao[$inCount]["flDeducao"] == $flDeducao ) &&
                ( $arServicosRetencaoSessao[$inCount]["flDeducaoLegal"] == $flDeducaoLegal )
            ) {
                $obTCGM = new TCGM;
                $obTCGM->setDado( "numcgm", $arServicosRetencaoSessao[$inCount]["inCGM"] );
                $obTCGM->recuperaPorChave( $rsCGM );
                if ( !$rsCGM->Eof() ) {
                    $stNomCgm = $rsCGM->getCampo("nom_cgm");
                    $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
                }

                Sessao::write( "servicos_retencao_alterando_semrt", $inCount+1 );

                $stJs .= 'f.stChaveServico.value = "'.$arServicosRetencaoSessao[$inCount]["stServico"].'";';
                $stJs .= 'f.flAliquota.value = "'.$arServicosRetencaoSessao[$inCount]["flAliquota"].'";';
                $stJs .= 'f.flValorDeclarado.value = "'.$arServicosRetencaoSessao[$inCount]["flValorDeclarado"].'";';
                $stJs .= 'f.flDeducao.value = "'.$arServicosRetencaoSessao[$inCount]["flDeducao"].'";';
                $stJs .= 'f.flDeducaoLegal.value = "'.$arServicosRetencaoSessao[$inCount]["flDeducaoLegal"].'";';

                sistemaLegado::executaFrameOculto( $stJs );

                $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
                $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
                $obMontaServico->setValorReduzidoServico ( $arServicosRetencaoSessao[$inCount]["stServico"] );
                $obMontaServico->preencheCombos();
                break;
            }
        }
        break;

    case "excluirServico":
        $stServico = $_REQUEST['inIndice1'];
        $flAliquota = $_REQUEST['inIndice2'];
        $flValorDeclarado = $_REQUEST['inIndice3'];
        $flDeducao = $_REQUEST['inIndice4'];
        $flValorLancado = $_REQUEST['inIndice5'];
        $flValorRetido = $_REQUEST['inIndice6'];

        $arTmpServico = array();
        $inCountArray = 0;

        $arServicosRetencaoSemRTSessao = Sessao::read( "servicos_retencao_semrt" );
        $nregistros = count ( $arServicosRetencaoSemRTSessao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arServicosRetencaoSemRTSessao[$inCount]["stServico"] != $stServico ) ||
                ( $arServicosRetencaoSemRTSessao[$inCount]["flAliquota"] != $flAliquota ) ||
                ( $arServicosRetencaoSemRTSessao[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicosRetencaoSemRTSessao[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicosRetencaoSemRTSessao[$inCount]["flDeducao"] != $flDeducao )
               ) {
                $arTmpServico[$inCountArray] = $arServicosRetencaoSemRTSessao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "servicos_retencao_semrt", $arTmpServico );

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arTmpServico );

        $stJs = montaListaServicos ( $rsListaServicos );
        if ( !$boTemValores && (count( Sessao::read( "servicos_retencao_semrt") ) == 0 ) && (count(Sessao::read("notas_retencao_semrt")) == 0 ) ) {
            $stJs .= "d.getElementById('spnCarne').innerHTML = '&nbsp;';\n";
        }
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "incluirServico":
        if (!$_REQUEST["stChaveServico"]) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["flDeducaoLegal"]) {
            $stJs = "alertaAviso('@Campo Dedução Legal inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["flAliquota"]) {
            $stJs = "alertaAviso('@Campo Alíquota inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        } else {
            $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );
            if ($flAliquota <= 0 || $flAliquota > 100) {
                $stJs = "alertaAviso('@Valor da Aliquota inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        }

        if (!$_REQUEST["flValorDeclarado"]) {
            $stJs = "alertaAviso('@Campo Valor Declarado inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arServicosRetencaoSemRTSessao = Sessao::read( "servicos_retencao_semrt" );
        for ( $inX=0; $inX<count ( $arServicosRetencaoSemRTSessao ); $inX++) {
            if ( Sessao::read( "servicos_retencao_alterando_semrt" ) == ($inX+1) )
                continue;

            //if ($arServicosRetencaoSemRTSessao[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
            //    $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
            //    $stJs .= 'f.stChaveServico.focus();';
            //    sistemaLegado::executaFrameOculto( $stJs );
            //    exit;
            //}
        }

        if ( Sessao::read( "servicos_retencao_alterando_semrt" ) ) {
            $inTotalElementos = Sessao::read( "servicos_retencao_alterando_semrt" ) - 1;
            Sessao::write( "servicos_retencao_alterando_semrt", "" );

            unset($arServicosRetencaoSemRTSessao[$inTotalElementos]["flAliquota"]);
            unset($arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorDeclarado"]);
            unset($arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorLancado"]);
            unset($arServicosRetencaoSemRTSessao[$inTotalElementos]["flDeducao"]);
            unset($arServicosRetencaoSemRTSessao[$inTotalElementos]["flDeducaoLegal"]);

            Sessao::write( "servicos_retencao_semrt", $arServicosRetencaoSemRTSessao );
        }else
            $inTotalElementos = count ( $arServicosRetencaoSemRTSessao );

        $obTCEMServico = new TCEMServico;
        $stFiltro = " WHERE es.cod_estrutural = '".$_REQUEST["stChaveServico"]."' AND esa.cod_atividade = ".$_REQUEST["inCodAtividade"];
        $obTCEMServico = new TCEMServico;
        $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );

        if ( $rsListaServico->Eof() ) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arServicosRetencaoSemRTSessao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
        $arServicosRetencaoSemRTSessao[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
        $arServicosRetencaoSemRTSessao[$inTotalElementos]["flAliquota"] = $_REQUEST["flAliquota"];
        $arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorDeclarado"] = $_REQUEST["flValorDeclarado"];
        $arServicosRetencaoSemRTSessao[$inTotalElementos]["flDeducaoLegal"] = $_REQUEST["flDeducaoLegal"];
        if ($_REQUEST["flDeducao"])
            $arServicosRetencaoSemRTSessao[$inTotalElementos]["flDeducao"] = $_REQUEST["flDeducao"];

        $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flValorDeclarado"] ) );
        $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducao"] ) );
        $flDeducaoLegal = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducaoLegal"] ) );
        $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );

        $arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorLancado"] = (( $flValorDeclarado - $flDeducao - $flDeducaoLegal ) * $flAliquota ) / 100;
        $arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorLancado"] = number_format( $arServicosRetencaoSemRTSessao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );

        $stJs = 'f.stChaveServico.value = "";';
        $stJs .= 'f.flAliquota.value = "";';
        $stJs .= 'f.flValorDeclarado.value = "";';
        $stJs .= 'f.flDeducao.value = "";';
        $stJs .= 'f.flDeducaoLegal.value = "0,00";';

        $inX = 0;
        while ($_REQUEST) {
            $inX++;
            $stNome = "inCodServico_".$inX;
            if ($_REQUEST[ $stNome ]) {
                if ($inX > 1) {
                    $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                    $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                }

                $stJs .= 'f.'.$stNome.'.value = "";';
            }else
                break;
        }

        Sessao::write( "servicos_retencao_semrt", $arServicosRetencaoSemRTSessao );

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arServicosRetencaoSemRTSessao );

        $stJs .= montaListaServicos ( $rsListaServicos );

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "preencheProxComboServico":
        $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboServico];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboServico = "inCodServico_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboServico];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }

        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaServico->setCodigoVigenciaServico    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaServico->setCodigoNivelServico       ( $arChaveLocal[0] );
        $obMontaServico->setCodigoServico            ( $arChaveLocal[1] );
        $obMontaServico->setValorReduzidoServico     ( $arChaveLocal[3] );
        $obMontaServico->preencheProxCombo           ( $inPosicao , $_REQUEST["inNumNiveisServico"] );
        break;

    case "preencheCombosServico":
        $stFiltro = " WHERE es.cod_estrutural = '".$_REQUEST["stChaveServico"]."' AND esa.cod_atividade = ".$_REQUEST["inCodAtividade"];
        $obTCEMServico = new TCEMServico;
        $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
        if ( $rsListaServico->Eof() ) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.stChaveServico.focus();';

            $inX = 0;
            while ($_REQUEST) {
                $inX++;
                $stNome = "inCodServico_".$inX;
                if ($_REQUEST[ $stNome ]) {
                    if ($inX > 1) {
                        $stJs .= "limpaSelect(f.".$stNome.",1); \n";
                        $stJs .= "f.".$stNome."[0] = new Option('Selecione Sub Grupo','', 'selected');\n";
                    }

                    $stJs .= 'f.'.$stNome.'.value = "";';
                }else
                    break;
            }

            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
        $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaServico->setValorReduzidoServico ( $_REQUEST["stChaveServico"] );

        $obMontaServico->preencheCombos();
        break;
}

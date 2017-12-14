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
    * Página de processamento oculto para o Parcelamento de Créditos
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * $Id: OCRelatorioValoresLancados.php 63839 2015-10-22 18:08:07Z franver $

* Casos de uso: uc-05.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RUsuario.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRRelatorioValoresLancados.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRParametroCalculo.class.php" );

//INSTANCIA OBJETOS
$obRARRGrupo = new RARRGrupo;
$inCodModulo   = $obRARRGrupo->getCodModulo() ;
$obRRelatorio   = new RRelatorio;
$obRARRRelatorioValoresLancados = new RARRRelatorioValoresLancados;
$stNull = '&nbsp;';

//Define o nome dos arquivos PHP
$stPrograma          = "RelatorioValoresLancados";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";

//################################################################################
if (!$_REQUEST ["stCtrl"]) {
    $arFiltro = Sessao::read( 'filtroRelatorio' );
    // SETA ELEMENTOS DO FILTRO
    if ($arFiltro['stTipoRelatorio'] == 'analitico' || $arFiltro['stTipoRelatorio'] == 'sintetico') {
        if ($arFiltro['stTipoRelatorio'] == 'sintetico') {
            $obRARRRelatorioValoresLancados = new RARRRelatorioValoresLancados;
            $obRARRRelatorioValoresLancados->setTipoRelatorio ( $arFiltro['stTipoRelatorio'] );

            $obRARRRelatorioValoresLancados->setCodGrupoCreditoInicio ( $arFiltro['inCodGrupoInicio'] );
            $obRARRRelatorioValoresLancados->setCodGrupoCreditoTermino ( $arFiltro['inCodGrupoTermino'] );

            $obRARRRelatorioValoresLancados->setCodCreditoInicio ( $arFiltro['inCodCreditoInicio'] );
            $obRARRRelatorioValoresLancados->setCodCreditoTermino ( $arFiltro['inCodCreditoTermino'] );

            $obRARRRelatorioValoresLancados->setNumCGMInicio ( $arFiltro['inCodContribuinteInicial'] );
            $obRARRRelatorioValoresLancados->setNumCGMTermino ( $arFiltro['inCodContribuinteFinal'] );

            $obRARRRelatorioValoresLancados->setInscricaoImobiliariaInicio ( $arFiltro['inNumInscricaoImobiliariaInicial'] );
            $obRARRRelatorioValoresLancados->setInscricaoImobiliariaTermino ( $arFiltro['inNumInscricaoImobiliariaFinal'] );

            $obRARRRelatorioValoresLancados->setInscricaoEconomicaInicio ( $arFiltro['inNumInscricaoEconomicaInicial'] );
            $obRARRRelatorioValoresLancados->setInscricaoEconomicaTermino ( $arFiltro['inNumInscricaoEconomicaFinal'] );

            $obRARRRelatorioValoresLancados->setExercicio ( $arFiltro['inExercicio']   );

            $obRARRRelatorioValoresLancados->setCodLogradouro ( $arFiltro['inNumLogradouro'] );
            $obRARRRelatorioValoresLancados->setNomLogradouro ( $arFiltro['stNomLogradouro'] );

            $obRARRRelatorioValoresLancados->setCodEstAtivInicial ( $arFiltro["inCodInicio"] );
            $obRARRRelatorioValoresLancados->setCodEstAtivFinal ( $arFiltro["inCodTermino"] );

            $obRARRRelatorioValoresLancados->setCodCondominioInicial ( $arFiltro['inCodCondominioInicial']);
            $obRARRRelatorioValoresLancados->setCodCondominioFinal ( $arFiltro['inCodCondominioFinal'] );

            // corrigir formatação
            $nuValorInicial = str_replace   ( '.' , '' , $arFiltro['nuValorInicial'] ) ;
            $nuValorInicial = str_replace   ( ',' , '.', $nuValorInicial );
            $nuValorFinal = str_replace     ( '.' , '' , $arFiltro['nuValorFinal'] ) ;
            $nuValorFinal = str_replace     ( ',' , '.', $nuValorFinal );

            $obRARRRelatorioValoresLancados->setValorInicial    ( $nuValorInicial );
            $obRARRRelatorioValoresLancados->setValorFinal      ( $nuValorFinal );

            $obRARRRelatorioValoresLancados->setSituacao        ( $arFiltro['boSituacao'] );

            if (!$stErro) {
                $obErro = $obRARRRelatorioValoresLancados->geraRecordSetRelatorio ( $rsRecordSet, $rsRecordSetSomas, $arCabecalho, $stOrder );
                $stTipoRelatorio = Sessao::read( 'stTipoRelatorio' );
                if (!$stTipoRelatorio) {
                    Sessao::write( 'stTipoRelatorio', $_REQUEST['stTipoRelatorio'] );
                }

                if ( $rsRecordSet->getNumLinhas() < 1 ) {
                    $sessao_transf6[0]['erro'] = 'Não foram encontrados registros com o filtro utilizado.';
                    Sessao::write( 'sessao_transf6', $sessao_transf6 );
                } else {
                    $sessao_transf5[0] = $rsRecordSet;
                    $sessao_transf5[1] = $rsRecordSetSomas;
                    Sessao::write( 'sessao_transf5', $sessao_transf5 );
                }
            }
        }

        $obRRelatorio->executaFrameOculto( "OCGeraRelatorioValoresLancados.php" );
    }
}
//################################################################################
switch ($_REQUEST ["stCtrl"]) {
    case "limpar":
        Sessao::write( 'creditos', array() );
        Sessao::write( 'acrescimos', array() );
        break;

    case "buscaCredito":
        $stJs = '';
        $arValores = explode('.',$_REQUEST["inCodCredito"]);
        // array [0]> cod_credito [1]> cod_especie [2]> cod_genero [3]> cod_natureza
        $obRARRGrupo->obRMONCredito->setCodCredito  ($arValores[0]);
        $obRARRGrupo->obRMONCredito->setCodEspecie  ($arValores[1]);
        $obRARRGrupo->obRMONCredito->setCodGenero   ($arValores[2]);
        $obRARRGrupo->obRMONCredito->setCodNatureza ($arValores[3]);
        // VERIFICAR PERMISSAO
        //$obRARRGrupo->obRMONCredito->consultarCreditoPermissao();
        $obRARRGrupo->obRMONCredito->consultarCredito();

        $inCodCredito = $obRARRGrupo->obRMONCredito->getCodCredito();
        $stDescricao = $obRARRGrupo->obRMONCredito->getDescricao() ;

        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stCredito').innerHTML = '".$stDescricao."';\n";
            $stJs .= "f.inCodigoCredito.value ='".$inCodCredito."';\n";
            if ($stAcao == 'incluir') {
                $stJs .= "d.getElementById('stTipoCalculo').checked = true;\n";
            }
        } else {
            $stJs .= "f.inCodCredito.value ='';\n";
            $stJs .= "f.inCodCredito.focus();\n";
            $stJs .= "d.getElementById('stCredito').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado nao existe. (".$_REQUEST["inCodCredito"].")','form','erro','".Sessao::getId()."');";
        }

    break;

    case "buscaGrupo":
        $stJs = '';
        $obRARRGrupo->setCodGrupo($_REQUEST["inCodGrupo"]);
        $obRARRGrupo->consultarGrupo();

        $inCodGrupo     = $obRARRGrupo->getCodGrupo () ;
        $stDescricao    = $obRARRGrupo->getDescricao() ;
        $inCodModulo    = $obRARRGrupo->getCodModulo() ;
        $stExercicio    = $obRARRGrupo->getExercicio() ;
        if ( !empty($stDescricao) ) {
            $stJs .= "d.getElementById('stGrupo').innerHTML = '".$stDescricao." / ".$stExercicio."';\n";
            $stJs .= "d.getElementById('spnEmissao').innerHTML = '';\n";
            $stJs .= "f.inCodModulo.value = '".$inCodModulo."';\n";
            $stJs .= "d.getElementById('stTipoEmissao').checked = false;\n";
            $stJs .= "f.inCodGrupo.focus();\n";
        } else {
            $stJs .= "f.inCodGrupo.value ='';\n";
            $stJs .= "f.inCodGrupo.focus();\n";
            $stJs .= "d.getElementById('stGrupo').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Grupo informado nao existe. (".$_REQUEST["inCodGrupo"].")','form','erro','".Sessao::getId()."');";
        }
    break;
    case "buscaIE":
        $js = '';
        if ($_REQUEST["inInscricaoEconomica"]) {
            include_once(CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php");
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica($_REQUEST["inInscricaoEconomica"]);
            $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);
            if ( !$rsInscricao->eof()) {
                $js .= "f.inInscricaoEconomica.value = '".$_REQUEST["inInscricaoEconomica"]."';\n";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '".$rsInscricao->getCampo("nom_cgm")."' ;\n";
            } else {
                $stMsg = "Inscrição Econômica ".$_REQUEST["inInscricaoEconomica"]."  não encontrada!";
                $js = "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
                $js .= "f.inInscricaoEconomica.value = '".$null ."';\n";
            }
        } else {
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;';\n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case "procuraImovel":
        $stJs = '';
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"       );
        $obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( new RCIMImovel( new RCIMLote) );
        $stJs = "";
        $stNull = "&nbsp;";
        if ($_REQUEST["inInscricaoImobiliaria"]) {
            $obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
            $obRCIMUnidadeAutonoma->roRCIMImovel->listarImoveisConsulta( $rsImoveis );

            if ( $rsImoveis->eof() ) {
                //nao encontrada
                $stJs .= 'f.inInscricaoImobiliaria.value = "";';
                $stJs .= 'f.inInscricaoImobiliaria.focus();';
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
                $stJs .= "alertaAviso('@Código de inscrição imobiliária inválido. (".$_REQUEST['inInscricaoImobiliaria'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$rsImoveis->getCampo("endereco").'";';
            }
        } else {
            $stJs .= 'd.getElementById("stInscricaoInscricaoImobiliaria").innerHTML = "'.$stNull.'";';
        }

        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaContribuinte":
        $stJs = '';
        $obRCGM = new RCGM;
        if ($_REQUEST[ 'inCodContribuinte' ] != "") {
            $obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );
            $obRCGM->consultar( $rsCGM );
            $stNull = "&nbsp;";
            if ( $rsCGM->getNumLinhas() <= 0) {
                $stJs .= 'f.inCodContribuinte.value = "";';
                $stJs .= 'f.inCodContribuinte.focus();';
                //$stJs .= 'd.getElementById("innerCGM").innerHTML = "'.$stNull.'";';
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST['inCodContribuinte'].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
                $stJs .= 'f.stNomeContribuinte.value = "'. $rsCGM->getCampo('nom_cgm') .'";';
            }
        } else {
            $stJs .= 'f.inCodContribuinte.value = "'. $stNull .'";';
            $stJs .= 'f.stNomeContribuinte.value = "'. $stNull.'";';
            $stJs .= 'd.getElementById("stContribuinte").innerHTML = "'.$stNull.'";';
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "CalculaValorParcelas":
        $stJs = '';
        if ( ( $_REQUEST['inNumParcelas'] < 1 ) || ($_REQUEST['inNumParcelas'] > 12 )) {
            $stJs .= "alertaAviso('@Número de parcelas inválido [$inNumParcelas]', 'form','erro','".Sessao::getId()."');";
            $stJs .= 'f.inNumParcelas.value="1";';
        } else {
            $valorParcelas = ( $_REQUEST['flTotalApurado'] / $_REQUEST['inNumParcelas']);
            $valorParcelas = number_format( $valorParcelas, 2 );
            $stJs  = 'f.flValorPorParcela.value="'. $valorParcelas .'";';
            $stJs .= 'd.getElementById("stValorPorParcela").innerHTML = "R$ '.str_replace(".",",",$valorParcelas).'";';

            montaParcelas ( $inNumParcelas, $_REQUEST['dtVencimento'], $valorParcelas );
        }
    break;

    case "buscaLogradouro":
        $stJs = '';
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"                );
        $obRCIMTrecho  = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $stJs .= 'f.inNumLogradouro.value = "";';
                $stJs .= 'f.stNomLogradouro.value = "";';
                $stJs .= 'f.inNumLogradouro.focus();';
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo("tipo_nome");
                $stJs .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
                $stJs .= 'f.stNomLogradouro.value = "'. $stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;

    case "buscaCondominio":
        $stJs = '';
        include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"            );
        $obRCIMCondominio  = new RCIMCondominio;
        if ($_POST['inCodCondominio'] != '') {
            $obRCIMCondominio->setCodigoCondominio( $_POST['inCodCondominio']  );
            $obErro = $obRCIMCondominio->consultarCondominio( $rsCondominio );
            if ( $obErro->ocorreu() or $rsCondominio->eof() ) {
                $stJs .= 'f.inCodCondominio.value = "";';
                $stJs .= 'f.stNomCondominio.value = "";';
                $stJs .= 'f.inCodCondominio.focus();';
                $stJs .= 'd.getElementById("innerCondominio").innerHTML = "&nbsp;";';
                $stJs .= "SistemaLegado::alertaAviso('@Condomínio não encontrado. (".$_POST["inCodCondominio"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs .= 'd.getElementById("innerCondominio").innerHTML = "'.$rsCondominio->getCampo('nom_condominio').'";';
                $stJs .= 'f.stNomCondominio.value = "'.$rsCondominio->getCampo('nom_condominio').'";';
            }
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;

    case "montaFiltro":
        $js = '';
        if ( ( $_REQUEST['stTipoRelatorio']  == 'analitico' ) || ( $_REQUEST['stTipoRelatorio']  == 'sintetico' ) ) {
            // pegar mascara de credito
            $obRARRParametroCalculo = new RARRParametroCalculo;
            $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->consultarMascaraCredito();
            $stMascaraCredito = $obRARRParametroCalculo->obRARRGrupo->obRMONCredito->getMascaraCredito();

            //mascara grupo de credito
            $obRARRGrupo = new RARRGrupo;
            $stMascaraGrupoCredito = "";
            $obRARRGrupo->RecuperaMascaraGrupoCredito( $stMascaraGrupoCredito );
            $stMascaraGrupoCredito .= "/9999";

            //mascara insc econ
            $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
            $obTAdministracaoConfiguracao->setDado( "cod_modulo", 14 );
            $obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
            $obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica");
            $obTAdministracaoConfiguracao->recuperaPorChave( $rsMascaraInscEco );
            if ( !$rsMascaraInscEco->Eof() ) {
                $stMascaraInscricaoEconomica = $rsMascaraInscEco->getCampo( "valor" ) ;
            }else
                $stMascaraInscricaoEconomica = "99";

            //if ( $_REQUEST['stTipoRelatorio']  == 'analitico' )
            $obBscGrupoCredito = new BuscaInnerIntervalo;
            $obBscGrupoCredito->setRotulo           ( "Grupo de Crédito"    );
            $obBscGrupoCredito->setTitle            ( "Informe o intervalo de Grupo de Crédito");
            $obBscGrupoCredito->obLabelIntervalo->setValue ( "até"          );
            $obBscGrupoCredito->obCampoCod->setName     ("inCodGrupoInicio"  );
            $obBscGrupoCredito->obCampoCod->setMascara ( $stMascaraGrupoCredito );
            $obBscGrupoCredito->obCampoCod->setMaxLength ( strlen($stMascaraGrupoCredito) );
            $obBscGrupoCredito->obCampoCod->setMinLength ( strlen($stMascaraGrupoCredito) );
            $obBscGrupoCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
            $obBscGrupoCredito->obCampoCod2->setName        ("inCodGrupoTermino"  );
            $obBscGrupoCredito->obCampoCod2->setMascara ( $stMascaraGrupoCredito );
            $obBscGrupoCredito->obCampoCod2->setMaxLength ( strlen($stMascaraGrupoCredito) );
            $obBscGrupoCredito->obCampoCod2->setMinLength ( strlen($stMascaraGrupoCredito) );
            $obBscGrupoCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));

            $obBscLogradouro = new BuscaInner;
            $obBscLogradouro->setRotulo          ( "Logradouro"      );
            $obBscLogradouro->setNull            ( true              );
            $obBscLogradouro->setId              ( "campoInnerLogr" );
            $obBscLogradouro->obCampoCod->setName( "inNumLogradouro" );
            $obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaValor('buscaLogradouro');" );
            $obBscLogradouro->setFuncaoBusca( "abrePopUp('".CAM_GT_CIM_POPUPS."logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro','campoInnerLogr','' ,'".Sessao::getId()."','800','550')" );

            include_once ( CAM_GT_CIM_COMPONENTES."IPopUpCondominioIntervalo.class.php" );
            $obIPopUpCondominio = new IPopUpCondominioIntervalo;
            $obIPopUpCondominio->setVerificaCondominio ( true );

            $obBscCredito = new BuscaInnerIntervalo;
            $obBscCredito->setRotulo           ( "Crédito" );
            $obBscCredito->setTitle            ( "Informe o intervalo de Crédito");
            $obBscCredito->obLabelIntervalo->setValue ( "até"          );
            $obBscCredito->obCampoCod->setName     ("inCodCreditoInicio"  );
            $obBscCredito->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoInicio','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
            $obBscCredito->obCampoCod2->setName        ("inCodCreditoTermino"  );
            $obBscCredito->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','inCodCreditoTermino','stNomeGrupo','','".Sessao::getId()."','800','450');" ));
            $obBscCredito->obCampoCod->setMaxLength ( strlen($stMascaraCredito) );
            $obBscCredito->obCampoCod->setMinLength ( strlen($stMascaraCredito) );
            $obBscCredito->obCampoCod->setMascara   ( $stMascaraCredito         );
            $obBscCredito->obCampoCod2->setMaxLength ( strlen($stMascaraCredito) );
            $obBscCredito->obCampoCod2->setMinLength ( strlen($stMascaraCredito) );
            $obBscCredito->obCampoCod2->setMascara   ( $stMascaraCredito         );

            $obNumCGMInicio = new TextBox;
            $obNumCGMInicio->setName          ( "inNumCGMInicio"        );
            $obNumCGMInicio->setRotulo         ( "Contribuinte"   );
            $obNumCGMInicio->setTitle             ( "Informe o Número de CGM inicial" ) ;
            $obNumCGMInicio->setInteiro          ( true                );

            $obNumCGMTermino = new TextBox;
            $obNumCGMTermino->setName       ( "inNumCGMTermino"       );
            $obNumCGMTermino->setRotulo      ( "Contribuinte"   );
            $obNumCGMTermino->setTitle          ( "Informe o Número de CGM final" );
            $obNumCGMTermino->setInteiro       ( true                 );

            $obHdnNomCGM = new Hidden;
            $obHdnNomCGM->setName ( "stNaoExiste" );

            $obBscContribuinte = new BuscaInnerIntervalo;
            $obBscContribuinte->setRotulo           ( "Contribuinte"    );
            $obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
            $obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
            $obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNaoExiste','','".Sessao::getId()."','800','450');" ));
            $obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
            $obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNaoExiste','','".Sessao::getId()."','800','450');" ));

            $obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
            $obBscInscricaoImobiliaria->setRotulo           ( "Inscrição Imobiliária"   );
            $obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"          );
            $obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"  );
            $obBscInscricaoImobiliaria->setFuncaoBusca      ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
            $obBscInscricaoImobiliaria->obCampoCod2->setName        ( "inNumInscricaoImobiliariaFinal" );
            $obBscInscricaoImobiliaria->setFuncaoBusca2     ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

            $obBscInscricaoEconomica = new BuscaInnerIntervalo;
            $obBscInscricaoEconomica->setRotulo         ( "Inscrição Econômica"    );
            $obBscInscricaoEconomica->obLabelIntervalo->setValue ( "até"            );
            $obBscInscricaoEconomica->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
            $obBscInscricaoEconomica->obCampoCod->setSize     ( strlen( $stMascaraInscricaoEconomica ) );
            $obBscInscricaoEconomica->obCampoCod->setMaxLength( strlen( $stMascaraInscricaoEconomica ) );
            $obBscInscricaoEconomica->obCampoCod->setMascara  ( $stMascaraInscricaoEconomica   );
            $obBscInscricaoEconomica->setFuncaoBusca("abrePopUp(&quot;".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php&quot;,&quot;frm&quot;,&quot;inNumInscricaoEconomicaInicial&quot;,&quot;stCampo&quot;,&quot;todos&quot;,&quot;".Sessao::getId()."&quot;,&quot;800&quot;,&quot;550&quot;);");
            $obBscInscricaoEconomica->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
            $obBscInscricaoEconomica->obCampoCod2->setSize     ( strlen( $stMascaraInscricaoEconomica ) );
            $obBscInscricaoEconomica->obCampoCod2->setMaxLength( strlen( $stMascaraInscricaoEconomica ) );
            $obBscInscricaoEconomica->obCampoCod2->setMascara  ( $stMascaraInscricaoEconomica   );
            $obBscInscricaoEconomica->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stCampo','todos','".Sessao::getId()."','800','550');"));

            $obTxtExercicio = new TextBox ;
            $obTxtExercicio->setName       ( "inExercicio"     );
            $obTxtExercicio->setId             ( "inExercicio"     );
            $obTxtExercicio->setMaxLength   ( 4                 );
            $obTxtExercicio->setSize          ( 6 );
            $obTxtExercicio->setRotulo      ( "Exercício"       );
            $obTxtExercicio->setTitle         ( "Exercício"       );
            $obTxtExercicio->setNull          ( true              );
            $obTxtExercicio->setValue       ( Sessao::getExercicio() );

            $obNumValor = new Numerico ;
            $obNumValor->setName       ( "nuValorInicial"  );
            $obNumValor->setId         ( "nuValorInicial"  );
            $obNumValor->setRotulo     ( "Valor"       );
            $obNumValor->setTitle      ( "Informe Intervalor de Valor a ser filtrado<br> <i>Exemplo: 150,00 até 300 <br> Pegara todos os registros com valores a pagar maior que 150,00 e menor que 300</i>");

            $obLblValor = new Label;
            $obLblValor->setValue( '&nbsp; até &nbsp;' );

            $obNumValorFinal = new Numerico ;
            $obNumValorFinal->setName       ( "nuValorFinal"  );
            $obNumValorFinal->setId         ( "nuValorFinal"  );
            $obNumValorFinal->setRotulo     ( "Valor"       );

            $obRadioFiltroPago = new Radio;
            $obRadioFiltroPago->setName         ( "boSituacao"              );
            $obRadioFiltroPago->setTitle        ( "Situação do Lançamento"  );
            $obRadioFiltroPago->setRotulo       ( "Situação"                );
            $obRadioFiltroPago->setValue        ( "Pago"                    );
            $obRadioFiltroPago->setLabel        ( "Pago"                    );
            $obRadioFiltroPago->setNull         ( false                     );
            $obRadioFiltroPago->setChecked      ( false                     );

            $obRadioFiltroAberto = new Radio;
            $obRadioFiltroAberto->setName     ( "boSituacao"                );
            $obRadioFiltroAberto->setValue    ( "Aberto"                    );
            $obRadioFiltroAberto->setLabel    ( "Aberto"                    );
            $obRadioFiltroAberto->setNull     ( false                       );
            $obRadioFiltroAberto->setChecked  ( false                       );

            $obRadioFiltroTodos = new Radio;
            $obRadioFiltroTodos->setName     ( "boSituacao"                );
            $obRadioFiltroTodos->setValue    ( "Todos"                     );
            $obRadioFiltroTodos->setLabel    ( "Todos"                     );
            $obRadioFiltroTodos->setNull     ( false                       );
            $obRadioFiltroTodos->setChecked  ( true                        );

            //novo filtro atividades de para
            $obRCEMAtividade = new RCEMAtividade;
            $obRCEMAtividade->recuperaVigenciaAtual( $rsVigenciaAtual );

            Sessao::write( "CodigoVigencia", $rsVigenciaAtual->getCampo( "cod_vigencia" ) );

            $obTCEMAtividade = new TCEMAtividade;
            $obTCEMAtividade->recuperaMaxCodEstrutural( $rsListaEstrutura );

            $arEstrutura = explode( ".", $rsListaEstrutura->getCampo("cod_estrutural") );
            $stEstrutura = "";
            for ( $inX=0; $inX<count($arEstrutura); $inX++ ) {
                if ( $inX )
                    $stEstrutura .= ".";

                for ( $inY=0; $inY<strlen($arEstrutura[$inX]); $inY++ ) {
                    $stEstrutura .= "9";
                }
            }

            $obHdnCampoInner = new Hidden;
            $obHdnCampoInner->setName ( "campoInner" );
            $obHdnCampoInner->setID ( "campoInner" );

            $obHdnInCodigoAtividade = new Hidden;
            $obHdnInCodigoAtividade->setName ( "inCodigoAtividade" );
            $obHdnInCodigoAtividade->setID ( "inCodigoAtividade" );

            $obBscAtividade = new BuscaInnerIntervalo;
            $obBscAtividade->setRotulo ( "Atividade" );
            $obBscAtividade->setTitle ( "Atividade Econômica" );
            $obBscAtividade->setNull ( true );
            $obBscAtividade->obLabelIntervalo->setValue ( "até" );
            $obBscAtividade->obCampoCod->setName ( "inCodInicio" );
            $obBscAtividade->obCampoCod->setInteiro ( false );
            $obBscAtividade->obCampoCod->setMascara ( $stEstrutura );
            $stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','inCodInicio','campoInner',''";
            $stBusca .= " ,'".Sessao::getId()."&campoFoco=inCodInicio','800','550')";
            $obBscAtividade->setFuncaoBusca ( $stBusca );
            $obBscAtividade->obCampoCod2->setName ( "inCodTermino" );
            $obBscAtividade->obCampoCod2->setInteiro ( false );
            $obBscAtividade->obCampoCod2->setMascara ( $stEstrutura );
            $stBusca  = "abrePopUp('".CAM_GT_CEM_POPUPS."atividadeeconomica/FLProcurarAtividade.php','frm','inCodTermino','campoInner',''";
            $stBusca .= " ,'".Sessao::getId()."&campoFoco=inCodTermino','800','550')";
            $obBscAtividade->setFuncaoBusca2 ( $stBusca );
            //---

            $obForm = new Form;
            $obForm->setAction( '');
            $obForm->setTarget( "oculto" );

            $obFormulario = new Formulario;
            $obFormulario->addForm( $obForm );
            #$obFormulario->addComponente ( $obHdnNomLogradouro );
            if ($_REQUEST['stTipoRelatorio']  == 'analitico') {
                $obFormulario->addHidden( $obHdnCampoInner );
                $obFormulario->addHidden( $obHdnInCodigoAtividade );
                $obFormulario->addComponente ( $obBscCredito    );
                $obFormulario->addComponente    ( $obBscGrupoCredito            );
                $obFormulario->addComponente    ( $obBscInscricaoImobiliaria    );
                $obFormulario->addComponente    ( $obBscInscricaoEconomica      );
                $obFormulario->addComponente    ( $obBscContribuinte            );
                $obFormulario->addComponente    ( $obBscLogradouro              );
                #$obFormulario->addComponente    ( $obBscCondominio              );
                $obIPopUpCondominio->geraFormulario    ( $obFormulario );
                $obFormulario->addComponente    ( $obTxtExercicio               );
                $obFormulario->addComponente    ( $obBscAtividade               );

                $obFormulario->agrupaComponentes ( array ($obNumValor,$obLblValor,$obNumValorFinal) );
                $obFormulario->agrupaComponentes ( array ( $obRadioFiltroPago, $obRadioFiltroAberto, $obRadioFiltroTodos) );
            } elseif ($_REQUEST['stTipoRelatorio']  == 'sintetico') {
                $obFormulario->addComponente ( $obBscCredito    );
                $obFormulario->addComponente ( $obBscLogradouro );
                #$obFormulario->addComponente    ( $obBscCondominio              );
                $obIPopUpCondominio->geraFormulario    ( $obFormulario );
                $obFormulario->addComponente ( $obTxtExercicio  );
            }

            $obFormulario->montaInnerHTML() ;
            $stHTML = $obFormulario->getHtml();
        }else
            $stHTML = '&nbsp;';

        $js .= "d.getElementById('spnFiltro').innerHTML = '".$stHTML ."';\n";

        echo $js;

    break;
}
/*SistemaLegado::executaFrameOculto($stJs);
SistemaLegado::LiberaFrames();*/
?>

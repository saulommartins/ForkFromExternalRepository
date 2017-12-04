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
    * Página de Frame Oculto da Retencao de Fonte
    * Data de Criação   : 26/10/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCReterFonte.php 30621 2008-07-01 12:04:24Z cercato $

    * Casos de uso: uc-05.03.22
*/

/*
$Log$
Revision 1.1  2006/10/30 13:00:16  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once ( CAM_GT_FIS_VISAO."VFISManterRetido.class.php" );
include_once ( CAM_GT_FIS_NEGOCIO."RFISManterRetido.class.php" );

$stMetodo = $_REQUEST['stCtrl'];

$obRegra = new RFISManterRetido();
$obVisao = new VFISManterRetido( $obRegra );

print $obVisao->$stMetodo( $_REQUEST )

/*function montaListaNotas($rsLista) {
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Notas" );
        $obLista->setTotaliza                  ( "flValorRetido,Total Valor Retido,right,8" );

        $obLista->setMostraPaginacao           ( false );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Prestador"            );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Série"                );
        $obLista->ultimoCabecalho->setWidth    ( 8                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Número da Nota"       );
        $obLista->ultimoCabecalho->setWidth    ( 8                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Emissão"      );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Alíquotas (%)"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Declarado"      );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[inCGM] - [stCGM]" );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inSerie"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "inNumeroNota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "dtEmissao"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flAliquota"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorDeclarado"     );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorRetido"        );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirNota();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "inSerie" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2", "inNumeroNota" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaNota').innerHTML = '".$stHTML."';\n";

    return $js;
}

function montaListaServicos($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Serviços" );

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
        $obLista->ultimoCabecalho->addConteudo ( "Dedução"              );
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
        $obLista->ultimoDado->setCampo         ( "flValorLancado"  );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:alterarServico();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
        $obLista->commitAcao                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirServico();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1", "stServico" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaServico').innerHTML = '".$stHTML."';\n";

    return $js;
}

$obMontaServico = new MontaServico;
$obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
$obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );

switch ($_REQUEST['stCtrl']) {
    case "validaData":
        if ($_REQUEST["stCompetencia"] == "") {
            $stJs = "alertaAviso('@Campo Competência deve ser preenxido antes de setar data.','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.dtEmissao.value = "";';
            sistemaLegado::executaFrameOculto( $stJs );
        } else {
            $dtEmissao = $_REQUEST["dtEmissao"];
            if ($dtEmissao) {
                $arData = explode( "/", $dtEmissao );
                if ($arData[1] != $_REQUEST["stCompetencia"] || $arData[2] != $_REQUEST["stExercicio"]) {
                    $stJs = "alertaAviso('@Campo Data da Emissão inválido.','form','erro','".Sessao::getId()."');";
                    $stJs .= 'f.dtEmissao.value = "";';
                    sistemaLegado::executaFrameOculto( $stJs );
                }
            }
        }
        break;

    case "alteraCompetencia":
        $rsListaNotas = new RecordSet;
        Sessao::write( "notas_retencao", array() );

        $stJs .= montaListaNotas ( $rsListaNotas );

        $boSetaData = false;
        if ( $_REQUEST["stExercicio"] > Sessao::getExercicio() + 1 ) {

            $stJs .= 'f.stCompetencia.value = ""; ';
            $stJs .= "alertaAviso('@Valor inválido no campo Exercicio.','form','erro','".Sessao::getId()."'); ";
            $stJs .= 'f.stExercicio.value = ""; ';
            $stJs .= 'f.stExercicio.focus(); ';
            sistemaLegado::executaFrameOculto( $stJs );
            exit;

        } elseif ( $_REQUEST["stExercicio"] == Sessao::getExercicio() ) {
            $inMes = date ("m");

            if ($_REQUEST["stCompetencia"] < $inMes) {
                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
                $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

                $obTARRVencimentoParcela = new TARRVencimentoParcela;
                $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = ".$arGrupoCreditoEscrituracao[1];
                $obTARRVencimentoParcela->recuperaTodos( $rsListaParcela, $stFiltro );
                if ( $rsListaParcela->Eof() ) {
                    $stJs .= "alertaAviso('Nenhum calendário fiscal foi definido para o grupo de credito da escrituração.','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto( $stJs );
                    exit;
                }

                $arData = explode( "/", $rsListaParcela->getCampo("data_vencimento") );
                if ( ($_REQUEST["stCompetencia"] < $inMes-1) || (date ("d") >= $arData[0] ) )
                    $boSetaData = true;
            } else {
                $stJs .= "alertaAviso('@Valor inválido no campo Competência.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.stCompetencia.value = "";';
                sistemaLegado::executaFrameOculto( $stJs );
            }
        } else {
            $boSetaData = true;
        }

        $stJs .= 'f.dtEmissao.value = "";';
        Sessao::write( "setar_data", $boSetaData );

        if ($boSetaData) {
            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->consultar();
            $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
            $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

            $obTARRVencimentoParcela = new TARRVencimentoParcela;
            $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = ".$arGrupoCreditoEscrituracao[1];
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

        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "PreencheCGM":
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
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
            }
        } else {
            $stJs = 'd.getElementById("stCGM").innerHTML = "&nbsp;";';
        }

        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limpaNota":
        $rsListaNotas = new RecordSet;
        Sessao::write( "notas_retencao", array() );

        $stJs = montaListaNotas ( $rsListaNotas );
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limpaServico":
        $stJs .= 'f.stChaveServico.value = "";';
        $stJs .= 'f.flAliquota.value = "";';
        $stJs .= 'f.flValorDeclarado.value = "";';
        $stJs .= 'f.flDeducao.value = "";';

        $inX = 0;
        while ($_REQUEST) {
            $inX++;
            $stNome = "inCodServico_".$inX;
            if ($_REQUEST[ $stNome ]) {
                $stJs .= 'f.'.$stNome.'.value = "";';
            }else
                break;
        }

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "preencheMunicipio":
        $js .= "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodigoUF"]) {
            $obRCIMBairro = new RCIMBairro;
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodigoUF"] );
            $obRCIMBairro->listarMunicipios( $rsMunicipios );
            $inContador = 1;

            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = str_replace( "'", "", $rsMunicipios->getCampo( "nom_municipio" ));
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodMunicipio."'); \n";

                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        sistemaLegado::executaFrameOculto($js);
        break;

    case "alterarServico":
        $stServico = $_REQUEST['inIndice1'];
        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arServicoRetencao[$inCount]["stServico"] == $stServico) {
                Sessao::write( "servicos_retencao_alterando", $inCount+1 );

                $stJs .= 'f.stChaveServico.value = "'.$arServicoRetencao[$inCount]["stServico"].'";';
                $stJs .= 'f.flAliquota.value = "'.$arServicoRetencao[$inCount]["flAliquota"].'";';
                $stJs .= 'f.flValorDeclarado.value = "'.$arServicoRetencao[$inCount]["flValorDeclarado"].'";';
                $stJs .= 'f.flDeducao.value = "'.$arServicoRetencao[$inCount]["flDeducao"].'";';

                sistemaLegado::executaFrameOculto( $stJs );
        $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
                $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
                $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
                $obMontaServico->setValorReduzidoServico ( $arServicoRetencao[$inCount]["stServico"] );

                $obMontaServico->preencheCombos();
                break;
            }
        }
        break;

    case "excluirNota":
        $inSerie = $_REQUEST['inIndice1'];
        $inNumeroNota = $_REQUEST['inIndice2'];

        $arTmpServico = array();
        $inCountArray = 0;
        $arNotasRetencao = Sessao::read( "notas_retencao" );
        $nregistros = count ( $arNotasRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arNotasRetencao[$inCount]["inNumeroNota"] != $inNumeroNota ||
                $arNotasRetencao[$inCount]["inSerie"] != $inSerie) {
                $arTmpServico[$inCountArray] = $arNotasRetencao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "notas_retencao", $arTmpServico );

        $rsListaNotas = new RecordSet;
        $rsListaNotas->preenche ( $arTmpServico );

        $stJs = montaListaNotas ( $rsListaNotas );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "excluirServico":
        $stServico = $_REQUEST['inIndice1'];

        $arTmpServico = array();
        $inCountArray = 0;

        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ($arServicoRetencao[$inCount]["stServico"] != $stServico) {
                $arTmpServico[$inCountArray] = $arServicoRetencao[$inCount];
                $inCountArray++;
            }
        }

        Sessao::write( "servicos_retencao", $arTmpServico );

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arTmpServico );

        $stJs = montaListaServicos ( $rsListaServicos );
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "incluirNota":
        if (!$_REQUEST["inCGM"]) {
            $stJs = "alertaAviso('@Campo CGM do Prestador inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inCodigoUF"]) {
            $stJs = "alertaAviso('@Campo Estado inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inCodigoMunicipio"]) {
            $stJs = "alertaAviso('@Campo Município inválido.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["dtEmissao"]) {
            $stJs = "alertaAviso('@Campo Data da Emissão vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inNumeroNota"]) {
            $stJs = "alertaAviso('@Campo Número da Nota vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inSerie"]) {
            $stJs = "alertaAviso('@Campo Série vazio.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $nregistros = count ( Sessao::read('servicos_retencao') );
        if ($nregistros <= 0) {
            $stJs = "alertaAviso('@Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arNotasRetencao = Sessao::read("notas_retencao");
        $nroNotas = count ( $arNotasRetencao );

        $boIncluir = true;
        for ($inX=0; $inX<$nroNotas; $inX++) {
            if ($arNotasRetencao[$inX]["inNumeroNota"] == $_REQUEST["inNumeroNota"]
                && $arNotasRetencao[$inX]["inSerie"] == $_REQUEST["inSerie"]) {
                $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                $boIncluir = false;
                break;
            }
        }

        if ($boIncluir) {
            $flTotalRetido = 0;
            $flTotalDeclarado = 0;
            $flTotalDeducao = 0;
            $stAliquota = "";
            $arServicoRetencao = Sessao::read( "servicos_retencao");
            for ($inX=0; $inX<$nregistros; $inX++) {
                $flDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                $flTotalDeclarado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencao[$inX]["flValorDeclarado"] ) );
                $flTotalRetido += ( $flDeclarado - $arServicoRetencao[$inX]["flDeducao"] );
                $flTotalDeducao += $arServicoRetencao[$inX]["flDeducao"];
                if ($arServicoRetencao[$inX]["flAliquota"]) {
                    $stAliquota .= $arServicoRetencao[$inX]["flAliquota"];
                    if ( $nregistros )
                        $stAliquota .= ";";
                }
            }

            $rsListaServicos = new RecordSet;
            $rsListaNotas = new RecordSet;

            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_REQUEST["inCGM"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( !$rsCGM->Eof() ) {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $arNotasRetencao[$nroNotas]["stCGM"] = $stNomCgm;
            }

            $arNotasRetencao[$nroNotas]["inCGM"] = $_REQUEST["inCGM"];
            $arNotasRetencao[$nroNotas]["stEstado"] = $_REQUEST["inCodigoUF"];
            $arNotasRetencao[$nroNotas]["stMunicipio"] = $_REQUEST["inCodigoMunicipio"];
            $arNotasRetencao[$nroNotas]["inNumeroNota"] = $_REQUEST["inNumeroNota"];
            $arNotasRetencao[$nroNotas]["inSerie"] = $_REQUEST["inSerie"];
            $arNotasRetencao[$nroNotas]["dtEmissao"] = $_REQUEST["dtEmissao"];
            $arNotasRetencao[$nroNotas]["flValorDeclarado"] = number_format( $flTotalDeclarado, 2, ',', '.' );
            $arNotasRetencao[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );

            $arNotasRetencao[$nroNotas]["flValorDeclaradoEUA"] = number_format( $flTotalDeclarado, 2, '.', '' );
            $arNotasRetencao[$nroNotas]["flValorRetidoEUA"] = number_format( $flTotalRetido, 2, '.', '' );
            $arNotasRetencao[$nroNotas]["flValorDeducaoEUA"] = number_format( $flTotalDeducao, 2, '.', '' );

            $arNotasRetencao[$nroNotas]["flAliquota"] = $stAliquota;
            $arNotasRetencao[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao' );

            Sessao::write( 'servicos_retencao', array() );
            Sessao::write( 'notas_retencao', $arNotasRetencao );

            $rsListaServicos->preenche ( array() );
            $rsListaNotas->preenche ( $arNotasRetencao );

            $stJs = montaListaServicos ( $rsListaServicos );
            $stJs .= montaListaNotas ( $rsListaNotas );
            $stJs .= 'f.inNumeroNota.value = "";';
            $stJs .= 'f.inSerie.value = "";';
            $stJs .= 'f.dtEmissao.value = "";';
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';
        }

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "incluirServico":
        if (!$_REQUEST["stChaveServico"]) {
            $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
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

        $arServicoRetencao = Sessao::read( "servicos_retencao" );
        for ( $inX=0; $inX<count ( $arServicoRetencao ); $inX++) {
            if ( Sessao::read( "servicos_retencao_alterando" ) == ($inX+1) )
                continue;

            if ($arServicoRetencao[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
                $stJs = "alertaAviso('O serviço já está na lista.','form','erro','".Sessao::getId()."');";
                $stJs .= 'f.stChaveServico.focus();';
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }
        }

        if ( Sessao::read( "servicos_retencao_alterando" ) ) {
            $inTotalElementos = Sessao::read( "servicos_retencao_alterando" ) - 1;
            Sessao::write( "servicos_retencao_alterando", "" );
            unset( $arServicoRetencao[$inTotalElementos]["flAliquota"] );
            unset( $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] );
            unset( $arServicoRetencao[$inTotalElementos]["flDeducao"] );
            Sessao::write( "servicos_retencao", $arServicoRetencao );
        }else
            $inTotalElementos = count ( $arServicoRetencao );

        $obTCEMServico = new TCEMServico;
        $stFiltro = " WHERE cod_estrutural = '".$_REQUEST["stChaveServico"]."'";
        $obTCEMServico->recuperaTodos( $rsListaServico, $stFiltro );
        if ( $rsListaServico->Eof() ) {
            $stJs = "alertaAviso('Código de serviço inválido (".$_REQUEST["stChaveServico"].").','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.stChaveServico.focus();';
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
        $arServicoRetencao[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
        $arServicoRetencao[$inTotalElementos]["flAliquota"] = $_REQUEST["flAliquota"];
        $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] = $_REQUEST["flValorDeclarado"];
        if ($_REQUEST["flDeducao"])
            $arServicoRetencao[$inTotalElementos]["flDeducao"] = $_REQUEST["flDeducao"];

        $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flValorDeclarado"] ) );
        $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducao"] ) );
        $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );

        $arServicoRetencao[$inTotalElementos]["flValorLancado"] = ( $flValorDeclarado - $flDeducao );
        $arServicoRetencao[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );
        Sessao::write( "servicos_retencao", $arServicoRetencao );

        $stJs = 'f.stChaveServico.value = "";';
        $stJs .= 'f.flAliquota.value = "";';
        $stJs .= 'f.flValorDeclarado.value = "";';
        $stJs .= 'f.flDeducao.value = "";';

        $inX = 0;
        while ($_REQUEST) {
            $inX++;
            $stNome = "inCodServico_".$inX;
            if ($_REQUEST[ $stNome ]) {
                $stJs .= 'f.'.$stNome.'.value = "";';
            }else
                break;
        }

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arServicoRetencao );

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
    $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
        $obMontaServico->setCodigoVigenciaServico    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaServico->setCodigoNivelServico       ( $arChaveLocal[0] );
        $obMontaServico->setCodigoServico            ( $arChaveLocal[1] );
        $obMontaServico->setValorReduzidoServico     ( $arChaveLocal[3] );
        $obMontaServico->preencheProxCombo           ( $inPosicao , $_REQUEST["inNumNiveisServico"] );
        break;

    case "preencheCombosServico":
    $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
        $obMontaServico->setCodigoVigenciaServico( $_REQUEST["inCodigoVigencia"]   );
        $obMontaServico->setCodigoNivelServico   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaServico->setValorReduzidoServico ( $_REQUEST["stChaveServico"] );
        $obMontaServico->preencheCombos();
        break;
}
*/

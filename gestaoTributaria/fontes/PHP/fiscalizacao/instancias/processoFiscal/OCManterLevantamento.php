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
    * Página de Frame Oculto levantamento fiscal
    * Data de Criação   : 13/08/2008

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Jânio Eduardo Vasconcellos de Magalhães

    * @ignore

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
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRVencimentoParcela.class.php" );
include_once ( CAM_GT_FIS_NEGOCIO."RFISManterLevantamento.class.php" );
include_once ( CAM_GT_FIS_VISAO."VFISManterLevantamento.class.php" );

function montaListaNotas($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {

        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Notas" );
        $obLista->setTotaliza                  ( "flValorLancado,Valor em Serviço,right,8" );

        $obLista->setMostraPaginacao           ( false );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Série"                );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nº da Nota"           );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Emissão"      );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Alíquotas (%)"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Total de Serviços"    );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Retido"         );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor em Mercadoria"  );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Valor Lançado"        );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

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
        $obLista->ultimoDado->setCampo         ( "flTotalServico"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorRetido"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorMercadoria"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flValorLancado"  );
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
        $stHTML = "&nbsp;";
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
        $obLista->setTotaliza                  ( "flValorLancado,Valor Total,right,7" );

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
        if ($_REQUEST["boReterFonte"]) {
            Sessao::write( 'notas_retencao_comrt', array() );
        } else {
            Sessao::write( 'notas_retencao_semrt', array() );
        }

        $stJs = montaListaNotas ( $rsListaNotas );
        $stJs .= "d.getElementById('spnCarne').innerHTML = '&nbsp;';\n";
        sistemaLegado::executaFrameOculto($stJs);
        break;

    case "limpaServico":
        if ($_REQUEST["boReterFonte"]) {
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.flValorRetido.value = "";';

            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';

            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";

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
        } else {
            $stJs .= 'f.stChaveServico.value = "";';
            $stJs .= 'f.flAliquota.value = "";';
            $stJs .= 'f.flValorDeclarado.value = "";';
            $stJs .= 'f.flDeducao.value = "";';

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
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodMunicipio."'); \n";
                $inContador++;
                $rsMunicipios->proximo();
            }
        }

        sistemaLegado::executaFrameOculto($js);
        break;

    case "validaData":

        if ($_REQUEST["stCompetencia"] == "") {

            $stJs = "alertaAviso('@Campo Competência deve ser preenxido antes de setar data.','form','erro','".Sessao::getId()."');";
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

            if ($_REQUEST["stCompetencia"] < $inMes) {

                $obRARRConfiguracao = new RARRConfiguracao;
                $obRARRConfiguracao->consultar();
                $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
                $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

                $obTARRVencimentoParcela = new TARRVencimentoParcela;
                $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = ".$arGrupoCreditoEscrituracao[1]." AND cod_parcela = ".$_REQUEST["stCompetencia"];
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
        Sessao::write( 'servicos_retencao', array() );
        Sessao::write( 'servicos_retencao_alterando', "" );
        Sessao::write( 'servicos_retencao_comrt', array() );
        Sessao::write( 'notas_retencao_comrt', array() );
        Sessao::write( 'servicos_retencao_alterando_comrt', "" );
        Sessao::write( 'servicos_retencao_semrt', array() );
        Sessao::write( 'notas_retencao_semrt', array() );
        Sessao::write( 'servicos_retencao_alterando_semrt', "" );

    case "montaRetencao":
        $rsUF = new RecordSet;
        $obRCIMLogradouro = new RCIMLogradouro;
        $obRCIMLogradouro->listarUF( $rsUF );

        $rsMunicipios = new RecordSet;

        $obFormulario = new Formulario;

        if ($_REQUEST["boReterFonte"]) {
            //com retencao
            $obTxtCodUF = new TextBox;
            $obTxtCodUF->setRotulo             ( "Estado"                );
            $obTxtCodUF->setName               ( "inCodigoUF"            );
            $obTxtCodUF->setValue              ( $inCodigoUF             );
            $obTxtCodUF->setSize               ( 8                       );
            $obTxtCodUF->setMaxLength          ( 8                       );
            $obTxtCodUF->setNull               ( false                   );
            $obTxtCodUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obCmbUF = new Select;
            $obCmbUF->setName               ( "inCodUF"               );
            $obCmbUF->addOption             ( "", "Selecione"         );
            $obCmbUF->setCampoId            ( "cod_uf"                );
            $obCmbUF->setCampoDesc          ( "nom_uf"                );
            $obCmbUF->preencheCombo         ( $rsUF                   );
            $obCmbUF->setValue              ( $inCodigoUF             );
            $obCmbUF->setNull               ( false                   );
            $obCmbUF->setStyle              ( "width: 220px"          );
            $obCmbUF->obEvento->setOnChange ( "buscaValor('preencheMunicipio')" );

            $obTxtCodMunicipio = new TextBox;
            $obTxtCodMunicipio->setRotulo    ( "Munic&iacute;pio"  );
            $obTxtCodMunicipio->setName      ( "inCodigoMunicipio" );
            $obTxtCodMunicipio->setValue     ( $inCodigoMunicipio  );
            $obTxtCodMunicipio->setSize      ( 8                   );
            $obTxtCodMunicipio->setMaxLength ( 8                   );
            $obTxtCodMunicipio->setNull      ( false               );

            $obCmbMunicipio = new Select;
            $obCmbMunicipio->setName       ( "inCodMunicipio"   );
            $obCmbMunicipio->addOption     ( "", "Selecione"    );
            $obCmbMunicipio->setCampoId    ( "cod_municipio"    );
            $obCmbMunicipio->setCampoDesc  ( "nom_municipio"    );
            $obCmbMunicipio->setValue      ( $inCodigoMunicipio );
            $obCmbMunicipio->preencheCombo ( $rsMunicipios      );
            $obCmbMunicipio->setNull       ( false              );
            $obCmbMunicipio->setStyle      ( "width: 220px"     );

            $obBscCGM = new BuscaInner;
            $obBscCGM->setRotulo         ( "CGM - Retentor" );
            $obBscCGM->setId             ( "stCGM" );
            $obBscCGM->setNull ( false );
            $obBscCGM->obCampoCod->setName       ( "inCGM" );
            $obBscCGM->obCampoCod->obEvento->setOnChange( "buscaValor('PreencheCGM');" );
            $obBscCGM->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCGM','stCGM','','".Sessao::getId()."','800','450');" );

            $obTxtValorRetido = new Numerico;
            $obTxtValorRetido->setRotulo ( "Valor Retido" );
            $obTxtValorRetido->setName ( "flValorRetido" );
            $obTxtValorRetido->setId ( "flValorRetido" );
            $obTxtValorRetido->setDecimais ( 2 );
            $obTxtValorRetido->setMaxValue ( 99999999999999.99 );
            $obTxtValorRetido->setNull ( false );
            $obTxtValorRetido->setNegativo ( false );
            $obTxtValorRetido->setNaoZero ( true );
            $obTxtValorRetido->setSize ( 20 );
            $obTxtValorRetido->setMaxLength ( 20 );

            $obFormulario->addComponenteComposto ( $obTxtCodUF, $obCmbUF );
            $obFormulario->addComponenteComposto ( $obTxtCodMunicipio, $obCmbMunicipio );
            $obFormulario->addComponente ( $obBscCGM );

            $obMontaServico = new MontaServico;
            $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
            $obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );
            $obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente ( $obTxtValorRetido );

        } else {
            //sem retencao

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

            $obMontaServico = new MontaServico;
            $obMontaServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
            $obMontaServico->setCodigoVigenciaServico ( $_REQUEST["inCodigoVigencia"] );
            $obMontaServico->geraFormulario( $obFormulario );

            $obFormulario->addComponente( $obTxtAliquota );
            $obFormulario->addComponente( $obTxtValorDeclarado );
            $obFormulario->addComponente( $obTxtDeducao );
        }

        $obFormulario2 = new Formulario;

        Sessao::write( 'setar_data', $boSetaData );

        $obFormulario->montaInnerHTML();
        $stJs = "d.getElementById('spn1').innerHTML = '". $obFormulario->getHTML(). "';\n";

        if ($boSetaData) {
            $obRARRConfiguracao = new RARRConfiguracao;
            $obRARRConfiguracao->consultar();
            $stCodGrupoCreditoEscrituracao = $obRARRConfiguracao->getCodigoGrupoCreditoEscrituracao();
            $arGrupoCreditoEscrituracao = preg_split( "/\//", $stCodGrupoCreditoEscrituracao );

            $obTARRVencimentoParcela = new TARRVencimentoParcela;
            $stFiltro = " WHERE cod_grupo = ".$arGrupoCreditoEscrituracao[0]." AND ano_exercicio = ".$arGrupoCreditoEscrituracao[1]." AND cod_parcela = ".$_REQUEST["stCompetencia"];
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

        $boTemValores = false;
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
            if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

        $rsListaServicos = new RecordSet;
        $rsListaNotas = new RecordSet;
        if ($_REQUEST["stEscrituracao"] == "nota") {
            if ($_REQUEST["boReterFonte"]) { //com retencao
                if ( Sessao::read( 'servicos_retencao_comrt' ) )
                    $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_comrt' ) );

                if ( Sessao::read( 'notas_retencao_comrt' ) )
                    $rsListaNotas->preenche ( Sessao::read( 'notas_retencao_comrt' ) );
            } else {
                if ( Sessao::read( 'servicos_retencao_semrt' ) )
                    $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_semrt' ) );

                if ( Sessao::read( 'notas_retencao_semrt' ) )
                    $rsListaNotas->preenche ( Sessao::read( 'notas_retencao_semrt' ) );
            }
        } else { //por servico
            if ( Sessao::read( 'servicos_retencao' ) )
                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao' ) );
        }
        $stJs2 = null;
        $stJs2 = montaListaServicos( $rsListaServicos );
        $stJs2 .= montaListaNotas ( $rsListaNotas );

        sistemaLegado::executaFrameOculto($stJs);
        sistemaLegado::executaFrameOculto($stJs2);

        $obRCEMServico = new RCEMServico;
        $obRCEMServico->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );

        $obRCEMServico->recuperaUltimoNivel( $rsListaNivel );

        $obRCEMServico->setCodigoNivel( 1 );
        $obRCEMServico->setCodigoAtividade( $_REQUEST["inCodAtividade"] );
        $obRCEMServico->listarServico( $rsListaServico );

        if ( $rsListaServico->getNumLinhas() > 0 ) {

            //echo '<h2>Dentro do IF</h2>'; exit;
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
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        $nregistros = count ( $arServicoRetencao );
        for ($inCount = 0; $inCount < $nregistros; $inCount++) {
            if ( ( $arServicoRetencao[$inCount]["stServico"] == $stServico ) &&
                ( $arServicoRetencao[$inCount]["flAliquota"] == $flAliquota ) &&
                ( $arServicoRetencao[$inCount]["flValorDeclarado"] == $flValorDeclarado ) && ( $arServicoRetencao[$inCount]["flValorLancado"] == $flValorLancado ) && ( $arServicoRetencao[$inCount]["flDeducao"] == $flDeducao )
                ) {

                $obTCGM = new TCGM;
                $obTCGM->setDado( "numcgm", $arServicoRetencao[$inCount]["inCGM"] );
                $obTCGM->recuperaPorChave( $rsCGM );
                if ( !$rsCGM->Eof() ) {
                    $stNomCgm = $rsCGM->getCampo("nom_cgm");
                    $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
                }

                Sessao::write( 'servicos_retencao_alterando', $inCount+1 );

                $stJs .= 'f.stChaveServico.value = "'.$arServicoRetencao[$inCount]["stServico"].'";';

                if ($arServicoRetencao[$inCount]["flValorDeclarado"]) {
                    $stJs .= 'f.flAliquota.value = "'.$arServicoRetencao[$inCount]["flAliquota"].'";';

                    $stJs .= 'f.flValorDeclarado.value = "'.$arServicoRetencao[$inCount]["flValorDeclarado"].'";';

                    $stJs .= 'f.flDeducao.value = "'.$arServicoRetencao[$inCount]["flDeducao"].'";';
                } else {
                    $stJs .= 'f.flValorRetido.value = "'.$arServicoRetencao[$inCount]["flValorRetido"].'";';

                    $stJs .= 'f.inCodigoUF.value = "'.$arServicoRetencao[$inCount]["stEstado"].'";';

                    $stJs .= 'f.inCodUF.value = "'.$arServicoRetencao[$inCount]["stEstado"].'";';

                    $stJs .= 'f.inCGM.value = "'.$arServicoRetencao[$inCount]["inCGM"].'";';

                    $stJs .= 'f.inCodigoMunicipio.value = "'.$arServicoRetencao[$inCount]["stMunicipio"].'";';

                    $stJs .= 'f.inCodMunicipio.value = "'.$arServicoRetencao[$inCount]["stMunicipio"].'";';
                }

                sistemaLegado::executaFrameOculto( $stJs );

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
        if ($_REQUEST["boReterFonte"]) { //com retencao
            $arTmpServico = array();
            $inCountArray = 0;
            $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
            $nregistros = count ( $arNotasRetencaoComRT );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ($arNotasRetencaoComRT[$inCount]["inNumeroNota"] != $inNumeroNota ||
                    $arNotasRetencaoComRT[$inCount]["inSerie"] != $inSerie) {
                    $arTmpServico[$inCountArray] = $arNotasRetencaoComRT[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'notas_retencao_comrt', $arTmpServico );
        } else {
            $arTmpServico = array();
            $inCountArray = 0;
            $arNotasRetencaoSemRT = Sessao::read( 'notas_retencao_semrt' );
            $nregistros = count ( $arNotasRetencaoSemRT );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ($arNotasRetencaoSemRT[$inCount]["inNumeroNota"] != $inNumeroNota ||
                    $arNotasRetencaoSemRT[$inCount]["inSerie"] != $inSerie) {
                    $arTmpServico[$inCountArray] = $arNotasRetencaoSemRT[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'notas_retencao_semrt', $arTmpServico );
        }

        $rsListaNotas = new RecordSet;
        $rsListaNotas->preenche ( $arTmpServico );

        $stJs = montaListaNotas ( $rsListaNotas );

        $boTemValores = false;
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
            if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

        if ( !$boTemValores && (count( Sessao::read( 'servicos_retencao_semrt' ) ) == 0 ) && (count( Sessao::read( 'notas_retencao_semrt' ) ) == 0 ) ) {
            $stJs .= "d.getElementById('spnCarne').innerHTML = '&nbsp;';\n";
        }

        sistemaLegado::executaFrameOculto( $stJs );
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

        if ($_REQUEST["stEscrituracao"] == "nota") {
            if ($_REQUEST["boReterFonte"]) { //com retencao
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                $nregistros = count ( $arServicoRetencaoComRT );
                for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                    if ( ( $arServicoRetencaoComRT[$inCount]["stServico"] != $stServico ) ||
                        ( $arServicoRetencaoComRT[$inCount]["flAliquota"] != $flAliquota ) ||
                        ( $arServicoRetencaoComRT[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicoRetencaoComRT[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicoRetencaoComRT[$inCount]["flDeducao"] != $flDeducao )
                        ) {
                        $arTmpServico[$inCountArray] = $arServicoRetencaoComRT[$inCount];
                        $inCountArray++;
                    }
                }

                Sessao::write( 'servicos_retencao_comrt', $arTmpServico );
            } else { //sem retencao
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                $nregistros = count ( $arServicoRetencaoSemRT );
                for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                    if ( ( $arServicoRetencaoSemRT[$inCount]["stServico"] != $stServico ) ||
                        ( $arServicoRetencaoSemRT[$inCount]["flAliquota"] != $flAliquota ) ||
                        ( $arServicoRetencaoSemRT[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicoRetencaoSemRT[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicoRetencaoSemRT[$inCount]["flDeducao"] != $flDeducao )
                        ) {
                        $arTmpServico[$inCountArray] = $arServicoRetencaoSemRT[$inCount];
                        $inCountArray++;
                    }
                }

                Sessao::write( 'servicos_retencao_semrt', $arTmpServico );
            }
        } else {
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            $nregistros = count ( $arServicoRetencao );
            for ($inCount = 0; $inCount < $nregistros; $inCount++) {
                if ( ( $arServicoRetencao[$inCount]["stServico"] != $stServico ) ||
                    ( $arServicoRetencao[$inCount]["flAliquota"] != $flAliquota ) ||
                    ( $arServicoRetencao[$inCount]["flValorDeclarado"] != $flValorDeclarado ) || ( $arServicoRetencao[$inCount]["flValorLancado"] != $flValorLancado ) || ( $arServicoRetencao[$inCount]["flDeducao"] != $flDeducao )
                    ) {
                    $arTmpServico[$inCountArray] = $arServicoRetencao[$inCount];
                    $inCountArray++;
                }
            }

            Sessao::write( 'servicos_retencao', $arTmpServico );
        }

        $rsListaServicos = new RecordSet;
        $rsListaServicos->preenche ( $arTmpServico );

        $stJs = montaListaServicos ( $rsListaServicos );
        if ( !$boTemValores && (count( Sessao::read( 'servicos_retencao_semrt' ) ) == 0 ) && (count( Sessao::read( 'notas_retencao_semrt' ) ) == 0 ) ) {
            $stJs .= "d.getElementById('spnCarne').innerHTML = '&nbsp;';\n";
        }
        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "incluirNota":
        if (!$_REQUEST["dtEmissao"]) {
            $stJs = "alertaAviso('@Campo Data da Emissão vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["flValorMercadoria"]) {
            SistemaLegado::exibeAviso( "Campo Dedução Legal inválido.", "n_incluir", "erro");
            exit;
        }

        if (!$_REQUEST["inNumeroNota"]) {
            $stJs = "alertaAviso('@Campo Número da Nota vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if (!$_REQUEST["inSerie"]) {
            $stJs = "alertaAviso('@Campo Série vazia.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $stJs );
            exit;
        }

        if ($_REQUEST["boReterFonte"]) {
            $nregistros = count ( Sessao::read('servicos_retencao_comrt') );
            if ($nregistros <= 0) {
                $stJs = "alertaAviso('@Campo Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
            $nroNotas = count ( $arNotasRetencaoComRT );
            $boIncluir = true;
            for ($inX=0; $inX<$nroNotas; $inX++) {
                if ($arNotasRetencaoComRT[$inX]["inNumeroNota"] == $_REQUEST["inNumeroNota"]
                    && $arNotasRetencaoComRT[$inX]["inSerie"] == $_REQUEST["inSerie"]) {
                    $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                    $boIncluir = false;
                    break;
                }
            }
        } else { //sem rentecao
            $nregistros = count ( Sessao::read( 'servicos_retencao_semrt' ) );
            if ($nregistros <= 0) {
                $stJs = "alertaAviso('@Campo Lista de Serviços vazia.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            $arNotasRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
            $nroNotas = count ( $arNotasRetencaoSemRT );
            $boIncluir = true;
            for ($inX=0; $inX<$nroNotas; $inX++) {
                if ($arNotasRetencaoSemRT[$inX]["inNumeroNota"] == $_REQUEST["inNumeroNota"] && $arNotasRetencaoSemRT[$inX]["inSerie"] == $_REQUEST["inSerie"]) {
                    $stJs = "alertaAviso('@A nota já está na lista.','form','erro','".Sessao::getId()."');";
                    $boIncluir = false;
                    break;
                }
            }
        }

        if ($boIncluir) {
            $flTotalLancado = 0;
            $flTotalServico = 0;
            $flTotalRetido = 0;
            $stAliquota = "";
            $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
            $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
            for ($inX=0; $inX<$nregistros; $inX++) {
                if ($_REQUEST["boReterFonte"]) { //com retencao
                    $flTotalLancado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoComRT[$inX]["flValorLancado"] ) );
                    $flTotalServico += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoComRT[$inX]["flValorDeclarado"] ) );
                    $flTotalRetido += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoComRT[$inX]["flValorRetido"] ));
                } else { //sem retencao
                    $flTotalLancado += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRT[$inX]["flValorLancado"] ) );
                    $flTotalServico += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRT[$inX]["flValorDeclarado"] ) );
                    $flTotalRetido += str_replace ( ',', '.', str_replace ( '.', '', $arServicoRetencaoSemRT[$inX]["flValorRetido"] ));

                    if ($arServicoRetencaoSemRT[$inX]["flAliquota"]) {
                        $stAliquota .= $arServicoRetencaoSemRT[$inX]["flAliquota"];
                        if ( $nregistros )
                            $stAliquota .= ";";
                    }
                }
            }

            $rsListaServicos = new RecordSet;
            $rsListaNotas = new RecordSet;

            if ($_REQUEST["boReterFonte"]) { //com retencao
                $arNotasRetencaoComRT = Sessao::read( 'notas_retencao_comrt' );
                $arNotasRetencaoComRT[$nroNotas]["flValorMercadoria"] = $_REQUEST["flValorMercadoria"];
                $arNotasRetencaoComRT[$nroNotas]["inNumeroNota"] = $_REQUEST["inNumeroNota"];
                $arNotasRetencaoComRT[$nroNotas]["inSerie"] = $_REQUEST["inSerie"];
                $arNotasRetencaoComRT[$nroNotas]["dtEmissao"] = $_REQUEST["dtEmissao"];
                $arNotasRetencaoComRT[$nroNotas]["flTotalServico"] = number_format( $flTotalServico, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["flValorLancado"] = number_format( $flTotalLancado, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );
                $arNotasRetencaoComRT[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao_comrt' );
                Sessao::write( 'notas_retencao_comrt', $arNotasRetencaoComRT );
                Sessao::write( 'servicos_retencao_comrt', array() );

                $rsListaServicos->preenche ( array() );
                $rsListaNotas->preenche ( $arNotasRetencaoComRT );
            } else { //sem retencao
                $arNotasRetencaoSemRT = Sessao::read( 'notas_retencao_semrt' );
                $arNotasRetencaoSemRT[$nroNotas]["flValorMercadoria"] = $_REQUEST["flValorMercadoria"];
                $arNotasRetencaoSemRT[$nroNotas]["inNumeroNota"] = $_REQUEST["inNumeroNota"];
                $arNotasRetencaoSemRT[$nroNotas]["inSerie"] = $_REQUEST["inSerie"];
                $arNotasRetencaoSemRT[$nroNotas]["dtEmissao"] = $_REQUEST["dtEmissao"];
                $arNotasRetencaoSemRT[$nroNotas]["flAliquota"] = $stAliquota;
                $arNotasRetencaoSemRT[$nroNotas]["flTotalServico"] = number_format( $flTotalServico, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["flValorLancado"] = number_format( $flTotalLancado, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["flValorRetido"] = number_format( $flTotalRetido, 2, ',', '.' );
                $arNotasRetencaoSemRT[$nroNotas]["arServicos"] = Sessao::read( 'servicos_retencao_semrt' );

                Sessao::write( 'notas_retencao_semrt', $arNotasRetencaoSemRT );
                Sessao::write( 'servicos_retencao_semrt', array() );
                $rsListaServicos->preenche ( array() );
                $rsListaNotas->preenche ( $arNotasRetencaoSemRT );
            }

            $stJs = montaListaServicos ( $rsListaServicos );
            $stJs .= montaListaNotas ( $rsListaNotas );
            $stJs .= 'f.flValorMercadoria.value = "0,00";';
            $stJs .= 'f.dtEmissao.value = "";';
            $stJs .= 'f.inNumeroNota.value = "";';
            $stJs .= 'f.inSerie.value = "";';

            $boTemValores = false;
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
                if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                    $boTemValores = true;
                    break;
                }
            }

        }

        sistemaLegado::executaFrameOculto( $stJs );
        break;

    case "incluirServico":
        if ($_REQUEST["boReterFonte"]) {
            if (!$_REQUEST["stChaveServico"]) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$_REQUEST["flValorRetido"]) {
                $stJs = "alertaAviso('@Campo Valor Retido inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if (!$_REQUEST["inCGM"]) {
                $stJs = "alertaAviso('@Campo CGM - Retentor inválido.','form','erro','".Sessao::getId()."');";
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
        } else { //sem retencao
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
        }

        if ($_REQUEST["stEscrituracao"] == "nota") {
            if ($_REQUEST["boReterFonte"]) { //com retencao
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                for ( $inX=0; $inX<count( $arServicoRetencaoComRT ); $inX++) {
                    if ( Sessao::read( 'servicos_retencao_alterando_comrt' ) == ($inX+1) )
                        continue;

                    if ($arServicoRetencaoComRT[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
                        $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                        $stJs .= 'f.stChaveServico.focus();';
                        sistemaLegado::executaFrameOculto( $stJs );
                        exit;
                    }
                }

                if ( Sessao::read( 'servicos_retencao_alterando_comrt' ) ) {
                    $inTotalElementos = Sessao::read( 'servicos_retencao_alterando_comrt' ) - 1;
                    Sessao::write( 'servicos_retencao_alterando_comrt', "" );

                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flAliquota"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flValorDeclarado"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flValorLancado"]);
                    unset( $arServicoRetencaoComRT[$inTotalElementos]["flDeducao"]);
                    Sessao::write( 'servicos_retencao_comrt', $arServicoRetencaoComRT );
                }else
                    $inTotalElementos = count ( $arServicoRetencaoComRT );
            } else { //sem retencao
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                for ( $inX=0; $inX<count ( $arServicoRetencaoSemRT ); $inX++) {
                    if ( Sessao::read( 'servicos_retencao_alterando_semrt' ) == ($inX+1) )
                        continue;

                    if ($arServicoRetencaoSemRT[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
                        $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                        $stJs .= 'f.stChaveServico.focus();';
                        sistemaLegado::executaFrameOculto( $stJs );
                        exit;
                    }
                }

                if ( Sessao::read( 'servicos_retencao_alterando_semrt' ) ) {
                    $inTotalElementos = Sessao::read( 'servicos_retencao_alterando_semrt' ) - 1;
                    Sessao::write( 'servicos_retencao_alterando_semrt', "" );

                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flAliquota"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flValorDeclarado"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] );
                    unset( $arServicoRetencaoSemRT[$inTotalElementos]["flDeducao"] );

                    Sessao::write( 'servicos_retencao_semrt', $arServicoRetencaoSemRT );
                }else
                    $inTotalElementos = count ( $arServicoRetencaoSemRT );
            }
        } else { //por servico
            $arServicoRetencao = Sessao::read( 'servicos_retencao' );
            for ( $inX=0; $inX<count ( $arServicoRetencao ); $inX++) {
                if ( Sessao::read( 'servicos_retencao_alterando' ) == ($inX+1) )
                    continue;

                if ($arServicoRetencao[$inX]["stServico"] == $_REQUEST["stChaveServico"]) {
                    if ($_REQUEST["flValorDeclarado"]) { //sem retencao
                        if ($arServicoRetencao[$inX]["flValorDeclarado"]) {
                            $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                            $stJs .= 'f.stChaveServico.focus();';
                            sistemaLegado::executaFrameOculto( $stJs );
                            exit;
                        }
                    } else { //com retencao
                        if (!$arServicoRetencao[$inX]["flValorDeclarado"]) {
                            $stJs = "alertaAviso('O servico já está na lista.','form','erro','".Sessao::getId()."');";
                            $stJs .= 'f.stChaveServico.focus();';
                            sistemaLegado::executaFrameOculto( $stJs );
                            exit;
                        }
                    }
                }
            }

            if ( Sessao::read( 'servicos_retencao_alterando' ) ) {
                $inTotalElementos = Sessao::read( 'servicos_retencao_alterando' ) - 1;
                Sessao::write( 'servicos_retencao_alterando', "" );

                unset( $arServicoRetencao[$inTotalElementos]["flAliquota"] );
                unset( $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] );
                unset( $arServicoRetencao[$inTotalElementos]["flValorLancado"] );
                unset( $arServicoRetencao[$inTotalElementos]["flDeducao"] );

                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }else
                $inTotalElementos = count ( $arServicoRetencao );
        }

        $obTCEMServico = new TCEMServico;
        if ($_REQUEST["boReterFonte"]) {
            $stFiltro = " WHERE es.cod_estrutural = '".$_REQUEST["stChaveServico"]."' AND esa.cod_atividade = ".$_REQUEST["inCodAtividade"];
            $obTCEMServico = new TCEMServico;
            $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
            if ( $rsListaServico->Eof() ) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if ($_REQUEST["stEscrituracao"] == "nota") {
                $arServicoRetencaoComRT = Sessao::read( 'servicos_retencao_comrt' );
                $arServicoRetencaoComRT[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencaoComRT[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
                $arServicoRetencaoComRT[$inTotalElementos]["flValorRetido"] = $_REQUEST["flValorRetido"];
                $arServicoRetencaoComRT[$inTotalElementos]["inCGM"] = $_REQUEST["inCGM"];
                $arServicoRetencaoComRT[$inTotalElementos]["stEstado"] = $_REQUEST["inCodigoUF"];
                $arServicoRetencaoComRT[$inTotalElementos]["stMunicipio"] = $_REQUEST["inCodigoMunicipio"];
                Sessao::write( 'servicos_retencao_comrt', $arServicoRetencaoComRT );
            } else {
                $arServicoRetencao = Sessao::read( 'servicos_retencao' );
                $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencao[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
                $arServicoRetencao[$inTotalElementos]["flValorRetido"] = $_REQUEST["flValorRetido"];
                $arServicoRetencao[$inTotalElementos]["inCGM"] = $_REQUEST["inCGM"];
                $arServicoRetencao[$inTotalElementos]["stEstado"] = $_REQUEST["inCodigoUF"];
                $arServicoRetencao[$inTotalElementos]["stMunicipio"] = $_REQUEST["inCodigoMunicipio"];
                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }

            $stJs = 'f.stChaveServico.value = "";';
            $stJs .= 'f.flValorRetido.value = "";';
            $stJs .= 'f.inCodigoUF.value = "";';
            $stJs .= 'f.inCodUF.value = "";';
            $stJs .= 'f.inCGM.value = "";';
            $stJs .= "d.getElementById('stCGM').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoMunicipio.value = "";';
            $stJs .= 'f.inCodMunicipio.value = "";';

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
        } else { //sem retencao
            $stFiltro = " WHERE es.cod_estrutural = '".$_REQUEST["stChaveServico"]."' AND esa.cod_atividade = ".$_REQUEST["inCodAtividade"];
            $obTCEMServico = new TCEMServico;
            $obTCEMServico->verificaServico( $rsListaServico, $stFiltro );
            if ( $rsListaServico->Eof() ) {
                $stJs = "alertaAviso('@Campo Serviço inválido.','form','erro','".Sessao::getId()."');";
                sistemaLegado::executaFrameOculto( $stJs );
                exit;
            }

            if ($_REQUEST["stEscrituracao"] == "nota") {
                $arServicoRetencaoSemRT = Sessao::read( 'servicos_retencao_semrt' );
                $arServicoRetencaoSemRT[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencaoSemRT[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
                $arServicoRetencaoSemRT[$inTotalElementos]["flAliquota"] = $_REQUEST["flAliquota"];
                $arServicoRetencaoSemRT[$inTotalElementos]["flValorDeclarado"] = $_REQUEST["flValorDeclarado"];

                if ($_REQUEST["flDeducao"])
                    $arServicoRetencaoSemRT[$inTotalElementos]["flDeducao"] = $_REQUEST["flDeducao"];

                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flValorDeclarado"] ) );
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducao"] ) );
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );

                $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] = (( $flValorDeclarado - $flDeducao ) * $flAliquota ) / 100;
                $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencaoSemRT[$inTotalElementos]["flValorLancado"], 2, ',', '.' );

                Sessao::write( 'servicos_retencao_semrt', $arServicoRetencaoSemRT );
            } else {
                $arServicoRetencao = Sessao::read( 'servicos_retencao' );

                $arServicoRetencao[$inTotalElementos]["stServicoNome"] = $rsListaServico->getCampo( "nom_servico" );
                $arServicoRetencao[$inTotalElementos]["stServico"] = $_REQUEST["stChaveServico"];
                $arServicoRetencao[$inTotalElementos]["flAliquota"] = $_REQUEST["flAliquota"];
                $arServicoRetencao[$inTotalElementos]["flValorDeclarado"] = $_REQUEST["flValorDeclarado"];

                if ($_REQUEST["flDeducao"])
                    $arServicoRetencao[$inTotalElementos]["flDeducao"] = $_REQUEST["flDeducao"];

                $flValorDeclarado = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flValorDeclarado"] ) );
                $flDeducao = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flDeducao"] ) );
                $flAliquota = str_replace ( ',', '.', str_replace ( '.', '', $_REQUEST["flAliquota"] ) );
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = (( $flValorDeclarado - $flDeducao ) * $flAliquota ) / 100;
                $arServicoRetencao[$inTotalElementos]["flValorLancado"] = number_format( $arServicoRetencao[$inTotalElementos]["flValorLancado"], 2, ',', '.' );
                Sessao::write( 'servicos_retencao', $arServicoRetencao );
            }

            $stJs = 'f.stChaveServico.value = "";';
            $stJs .= 'f.flAliquota.value = "";';
            $stJs .= 'f.flValorDeclarado.value = "";';
            $stJs .= 'f.flDeducao.value = "";';

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
        }

        $rsListaServicos = new RecordSet;
        if ($_REQUEST["stEscrituracao"] == "nota") {
            if ($_REQUEST["boReterFonte"]) {

                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_comrt' ) );
            } else {
                $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao_semrt' ) );
            }
        } else {
            $rsListaServicos->preenche ( Sessao::read( 'servicos_retencao' ) );
        }

        $stJs .= montaListaServicos ( $rsListaServicos );
        $boTemValores = false;
        $arServicoRetencao = Sessao::read( 'servicos_retencao' );
        for ( $inContaRetencao=0; $inContaRetencao<count( $arServicoRetencao ); $inContaRetencao++ ) {
            if ($arServicoRetencao[$inContaRetencao]["flValorDeclarado"]) {
                $boTemValores = true;
                break;
            }
        }

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

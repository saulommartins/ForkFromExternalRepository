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
  * Pagina Oculta para Credito
  * Data de criacao : 02/06/2005

    * @author Analista: Fabio Bertoldi
    * @author Programador: Diego Bueno Coelho

    * $Id: OCManterCredito.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.05.10
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php"           );
include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php"         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRPermissao.class.php"     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMNivel.class.php"         );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"              );
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php"       );
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php"      );
include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php"      );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCredito.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONCreditoNorma.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/negocio/RNorma.class.php';

$stCtrl = $_REQUEST['stCtrl'];

$stJs = "";

//Define o nome dos arquivos PHP
$stPrograma      = "ManterCredito";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgFormGrupo     = "FM".$stPrograma.".php";
$pgFormCredito   = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

$obRMONCredito = new RMONCredito ;
$obRARRGrupo = new RARRGrupo ;
$obRFuncao   = new RFuncao   ;

//--------------------------------------------------------- FUNCOES
function montaListaFundamentações( $rsLista ) {
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsLista );
        $obLista->setTitulo                    ( "Lista de Fundamentações"  );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Norma"                );
        $obLista->ultimoCabecalho->setWidth    ( 80                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Vigência"             );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[inCodNorma] - [stNorma]"  );
        $obLista->commitDado                   (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "[dtVigenciaInicio]"   );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirFundamentacao();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice1","inCodNorma" );
        $obLista->ultimaAcao->addCampo         ( "inIndice2","dtVigenciaInicio" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }

    return $stHTML;
}

function montaListaAcrescimos($rsListaAcrescimos, $boRetorna = false)
{
    if ( $rsListaAcrescimos->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 (   $rsListaAcrescimos   );
        $obLista->setTitulo                    ( "Lista de Acréscimos"  );
        $obLista->setMostraPaginacao           ( false                  );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Acréscimo"            );
        $obLista->ultimoCabecalho->setWidth    ( 80                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "descricao_acrescimo"  );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink          ( "JavaScript:excluirAcrescimo();" );
        $obLista->ultimaAcao->addCampo         ( "inIndice","cod_acrescimo" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }

    $js .= "d.getElementById('spnListaAcrescimo').innerHTML = '".$stHTML."';\n";
    $js .= "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
    $js .= "f.inCodAcrescimo.value ='';\n";
    //$js .= "f.inCodAcrescimo.focus();\n";

    sistemaLegado::executaFrameOculto($js);

}
/*
        FIM DAS FUNÇÕES
*/
switch ($_REQUEST ["stCtrl"]) {
    case "apagarDados":
        Sessao::write( "acrescimos", array() );
        Sessao::write( "listaFundamentacao", array() );
        break;

    case "excluirFundamentacao":
        $arDados = Sessao::read( "listaFundamentacao" );
        $arTMP = array();
        for ( $inX=0; $inX<count($arDados); $inX++ ) {
            if (!( ( $_REQUEST["inIndice1"] == $arDados[$inX]["inCodNorma"] ) && ( $_REQUEST["stIndice2"] == $arDados[$inX]["dtVigenciaInicio"] ) ) ) {
                $arTMP[] = $arDados[$inX];
            }
        }

        Sessao::write( "listaFundamentacao", $arTMP );
        $rsDados = new RecordSet;
        $rsDados->preenche( $arTMP );
        $stJs = montaListaFundamentações( $rsDados );
        SistemaLegado::executaFrameOculto( "d.getElementById('spnFund').innerHTML = '".$stJs."';" );
        break;

    case "LimparFundamentacao":
        $stJs = "f.inCodNorma.value ='';\n";
        $stJs .= "f.dtVigenciaInicio.value ='';\n";
        $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
        echo $stJs;
        break;

    case "IncluirFundamentacao":
        if (!$_REQUEST["inCodNorma"]) {
            $stJs = "alertaAviso('@Campo Norma vazio!','form','erro','".Sessao::getId()."');";
            echo $stJs;
            exit;
        }

        if (!$_REQUEST["dtVigenciaInicio"]) {
            $stJs = "alertaAviso('@Campo Vigência vazio!','form','erro','".Sessao::getId()."');";
            echo $stJs;
            exit;
        }

        $arDados = Sessao::read( "listaFundamentacao" );
        for ( $inX=0; $inX<count($arDados); $inX++ ) {
            if ( ( $_REQUEST["inCodNorma"] == $arDados[$inX]["inCodNorma"] ) && ( $_REQUEST["dtVigenciaInicio"] == $arDados[$inX]["dtVigenciaInicio"] ) ) {
                $stJs = "alertaAviso('@Norma já se encontra na lista!. (".$_REQUEST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
                echo $stJs;
                exit;
            }
        }

        $obRMONNorma = new RNorma;
        $obRMONNorma->setCodNorma ( trim( $_REQUEST["inCodNorma"] ) );
        $obRMONNorma->Consultar( $rsDados );

        $inCodigo = $obRMONNorma->getCodNorma();
        $stNomeTipoNorma = $obRMONNorma->obRTipoNorma->getNomeTipoNorma();
        $stNumNorma = $obRMONNorma->getNumNorma();
        $stExercicio = $obRMONNorma->getExercicio();
        $stNomeNorma = $obRMONNorma->getNomeNorma();

        $frase = $stNomeTipoNorma.' '.$stNumNorma.'/'.$stExercicio.' - '.$stNomeNorma;

        $arTMP = array();

        if ( count( $arDados ) ) {
            $arData = explode( "/", $_REQUEST["dtVigenciaInicio"] );
            $inData = $arData[2].$arData[1].$arData[0];
            for ( $inX=0; $inX<count( $arDados ); $inX++ ) {
                $arData = explode( "/", $arDados[$inX]["dtVigenciaInicio"] );
                $inData2 = $arData[2].$arData[1].$arData[0];
                if ($inData) {
                    if ($inData < $inData2) {
                        $arTMP[] = array(
                            "inCodNorma" => $_REQUEST["inCodNorma"],
                            "dtVigenciaInicio" => $_REQUEST["dtVigenciaInicio"],
                            "stNorma" => $frase
                        );

                        $inData = null;
                    }
                }

                $arTMP[] = $arDados[$inX];
            }

            if ($inData) {
                $arTMP[] = array(
                    "inCodNorma" => $_REQUEST["inCodNorma"],
                    "dtVigenciaInicio" => $_REQUEST["dtVigenciaInicio"],
                    "stNorma" => $frase
                );
            }
        } else {
            $arTMP[] = array(
                "inCodNorma" => $_REQUEST["inCodNorma"],
                "dtVigenciaInicio" => $_REQUEST["dtVigenciaInicio"],
                "stNorma" => $frase
            );
        }

        Sessao::write( "listaFundamentacao", $arTMP );
        $rsDados = new RecordSet;
        $rsDados->preenche( $arTMP );
        $stJs = montaListaFundamentações( $rsDados );
        $stJs = "d.getElementById('spnFund').innerHTML = '".$stJs."';\n";
        $stJs .= "f.inCodNorma.value ='';\n";
        $stJs .= "f.dtVigenciaInicio.value ='';\n";
        $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
        echo $stJs;
        break;

    case "buscaFuncao":
        include_once ( CAM_GA_ADM_NEGOCIO."RFuncao.class.php" );
        $obRFuncao = new RFuncao;

        if ($_REQUEST['inCodigoFormula'] != "") {
            $arCodFuncao = explode('.',$_REQUEST["inCodigoFormula"]);
            if ( ($arCodFuncao[0] != 25) OR ($arCodFuncao[1] != 2) ) {
               $stJs .= "f.inCodigoFormula.value ='';\n";
               $stJs .= "f.inCodigoFormula.focus();\n";
               $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
               $stJs .= "alertaAviso('@Função inválida. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
               SistemaLegado::executaFrameOculto($stJs);
               break;
            }

            $obRFuncao->setCodFuncao                           ( $arCodFuncao[2] );
            $obRFuncao->obRBiblioteca->setCodigoBiblioteca     ( $arCodFuncao[1] );
            $obRFuncao->obRBiblioteca->roRModulo->setCodModulo ( $arCodFuncao[0] );
            $obRFuncao->consultar();

            $inCodFuncao = $obRFuncao->getCodFuncao () ;
            $stDescricao = "&nbsp;";
            $stDescricao = $obRFuncao->getComentario() ;
            $stNomeFuncao = $obRFuncao->getNomeFuncao();
        }

        if ($stDescricao || $stNomeFuncao) {
            $stJs .= "d.getElementById('stFormula').innerHTML = '".$inCodFuncao." - ".$stNomeFuncao."';\n";
        } else {
            if ($_REQUEST['inCodigoFormula'] != "") {
                $stJs .= "alertaAviso('@Função informada não existe. (".$_REQUEST["inCodigoFormula"].")','form','erro','".Sessao::getId()."');";
                $stJs .= "f.inCodigoFormula.value ='';\n";
                $stJs .= "f.inCodigoFormula.focus();\n";
            }

            $stJs .= "d.getElementById('stFormula').innerHTML = '&nbsp;';\n";
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "PreencheCredito":

        $obTMONCredito = new TMONCredito;
        $obTMONCredito->recuperaMascaraCredito( $rsMascara );

        if ( !$rsMascara->eof() ) {
            $stMascaraCredito = $rsMascara->getCampo("mascara_credito");
        }
        $stNomeCampoCod = $_GET["stNomCampoCod"];
        $stIdCampoDesc = $_GET['stIdCampoDesc'];
        $stCodEntidade = $_GET['stCodEntidade'];

        if ( strlen($_REQUEST[$stNomeCampoCod]) >= strlen($stMascaraCredito) ) {
            #echo '<h1>a</h1>';
            $inCodCreditoComposto = explode('.', $_REQUEST[$stNomeCampoCod] );
            $stFiltro = "WHERE ";

            if ($stCodEntidade) {
                $stFiltro .= " \n cod_entidade   = ".$stCodEntidade." AND ";
                $stFiltro .= " \n exercicio      = ".Sessao::getExercicio()." AND ";
            }
            $stFiltro .= " \n mc.cod_credito = ".$inCodCreditoComposto[0]." AND ";
            $stFiltro .= " \n me.cod_especie = ".$inCodCreditoComposto[1]." AND ";
            $stFiltro .= " \n mg.cod_genero  = ".$inCodCreditoComposto[2]." AND ";
            $stFiltro .= " \n mn.cod_natureza = ".$inCodCreditoComposto[3];

            if ($_REQUEST['stTipoReceita']) {
                $obTMONCredito->recuperaRelacionamentoGF( $rsGrupos, $stFiltro );
            } else {
                $obTMONCredito->recuperaRelacionamento( $rsGrupos, $stFiltro );
            }

            if ( ($rsGrupos->getCampo('cod_entidade')) ) {

                include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
                $obREntidade = new ROrcamentoEntidade;

                $obFormulario = new Formulario();

                $obREntidade->setExercicio( Sessao::getExercicio() )  ;
                $obREntidade->setCodigoEntidade( $rsGrupos->getCampo('cod_entidade') ) ;
                $obREntidade->consultarnomes( $rsLista );
                $obErro = $obREntidade->consultarNomes( $rsLista );

                // Define objeto Label para codigo da entidade da conta
                $obLblCodEntidade = new Label();
                $obLblCodEntidade->setRotulo( 'Código Entidade'            );
                $obLblCodEntidade->setValue ( $rsGrupos->getCampo('cod_entidade')." - ".$rsLista->getCampo('entidade') );

                $obFormulario->addComponente      ( $obLblCodEntidade       );

                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();

            }

            if ( !$rsGrupos->eof() ) {

              $stJs  =" if (document.frm.inCodEntidade) {  \n";
              $stJs .="    document.frm.inCodEntidade.value='".$rsGrupos->getCampo('cod_entidade')."'; } \n";
              $stJs .="d.getElementById('".$stIdCampoDesc."').innerHTML = '".$rsGrupos->getCampo("descricao_credito")."';\n";
              if ($stHTML)
              $stJs .="d.getElementById('spnEntidade').innerHTML = '".$stHTML."';\n";

            } else {

                $stJs = "f.".$stNomeCampoCod.".value ='';\n";
                $stJs .= "f.".$stNomeCampoCod.".focus();\n";
                $stJs .= "d.getElementById('".$stIdCampoDesc."').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Crédito informado não existe ou não esta vinculado com uma Conta-Corrente. (".$_GET[$stNomeCampoCod].")','form','erro','".Sessao::getId()."');";
                $stJs .= " document.frm.inCodCredito.value='';
                           document.getElementById('stCredito').innerHTML='&nbsp;';
                           if ( document.getElementById('spnAcrescimo') ) {
                                document.getElementById('spnAcrescimo').innerHTML='';
                           }
                           if ( document.getElementById('btnIncluir') ) {
                                document.getElementById('btnIncluir').disabled = true;
                           }  ";

            }

        } else {

            $stJs = "f.".$stNomeCampoCod.".value ='';\n";
            $stJs .= "f.".$stNomeCampoCod.".focus();\n";
            $stJs .= "d.getElementById('".$stIdCampoDesc."').innerHTML = '&nbsp;';\n";
            $stJs .= "alertaAviso('@Crédito informado não existe ou não esta vinculado com uma Conta-Corrente. (".$_GET[$stNomeCampoCod].")','form','erro','".Sessao::getId()."');";
            $stJs .= " document.frm.inCodCredito.value='';
                       document.getElementById('stCredito').innerHTML='&nbsp;';
                       if ( document.getElementById('spnAcrescimo') ) {
                            document.getElementById('spnAcrescimo').innerHTML='';
                       }

                       if ( document.getElementById('btnIncluir') ) {
                            document.getElementById('btnIncluir').disabled = true;
                       }  ";

        }

        if( $stJs)
            echo $stJs;

        exit;

    break;

    case "buscaConvenio":
        $obRMONConvenio = new RMONConvenio;
        if ($_REQUEST['inNumConvenio']) {
            $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
            $obRMONConvenio->listarConvenioBancoGF( $rsConvenios );
            if ( $rsConvenios->eof() ) {
                //convenio nao foi encontrado
                $js = "f.inNumConvenio.value ='';\n";
                $js .= "f.inNumConvenio.focus();\n";
                $js .= "alertaAviso('@Convênio informado não existe. (".$_REQUEST["inNumConvenio"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js = "f.cmbContaCorrente.value=''; \n";
                $js .= "limpaSelect(f.cmbContaCorrente,1); \n";
                $js .= "f.cmbContaCorrente[0] = new Option('Selecione','', 'selected');\n";
                $inContador = 1;
                while ( !$rsConvenios->eof() ) {
                    $js .= "f.cmbContaCorrente.options[$inContador] = new Option('".$rsConvenios->getCampo("num_conta_corrente")."','".$rsConvenios->getCampo("cod_conta_corrente")."-".$rsConvenios->getCampo("cod_banco")."-".$rsConvenios->getCampo("cod_agencia")."'); \n";

                    $rsConvenios->proximo();
                    $inContador++;
                }

                $obRMONCarteira = new RMONCarteira;
                $obRMONCarteira->obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
                $obRMONCarteira->listarCarteira( $rsCarteira );
                if ( $rsCarteira->eof() ) {
                    $js .= "f.cmbCarteira.value=''; \n";
                    $js .= "limpaSelect(f.cmbCarteira,1); \n";
                    $js .= "f.cmbCarteira[0] = new Option('Selecione','', 'selected');\n";
                } else {
                    $js .= "f.cmbCarteira.value=''; \n";
                    $js .= "limpaSelect(f.cmbCarteira,1); \n";
                    $js .= "f.cmbCarteira[0] = new Option('Selecione','', 'selected');\n";
                    $inContador = 1;
                    while ( !$rsCarteira->eof() ) {
                        $js .= "f.cmbCarteira.options[$inContador] = new Option('".$rsCarteira->getCampo("num_carteira")."','".$rsCarteira->getCampo("cod_carteira")."'); \n";

                        $rsCarteira->proximo();
                        $inContador++;
                    }
                }
            }

            sistemaLegado::executaFrameOculto($js);
        }
        break;

    case "BuscaDados":
        $obRMONCredito = new RMONCredito;
        $obRMONCredito->setCodCredito   ( $_REQUEST['inCodCredito']  );
        $obRMONCredito->setCodEspecie   ( $_REQUEST['inCodEspecie']  );
        $obRMONCredito->setCodNatureza  ( $_REQUEST['inCodNatureza'] );
        $obRMONCredito->setCodGenero    ( $_REQUEST['inCodGenero']   );
        $obRMONCredito->BuscaCreditoCarteira( $rsCreditoCarteira );

        $obRMONConvenio = new RMONConvenio;
        $obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
        $obRMONConvenio->listarConvenioBanco( $rsConvenios );

        $js .= "f.cmbContaCorrente.value=''; \n";
        $js .= "limpaSelect(f.cmbContaCorrente,1); \n";
        $js .= "f.cmbContaCorrente[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        $inSelecionado = 0;
        while ( !$rsConvenios->eof() ) {
            $js .= "f.cmbContaCorrente.options[$inContador] = new Option('".$rsConvenios->getCampo("num_conta_corrente")."','".$rsConvenios->getCampo("cod_conta_corrente")."-".$rsConvenios->getCampo("cod_banco")."-".$rsConvenios->getCampo("cod_agencia")."'); \n";
            if ( $rsConvenios->getCampo("cod_conta_corrente") == $_REQUEST["inCodConta"] ) {
                $inSelecionado = $inContador;
            }

            $rsConvenios->proximo();
            $inContador++;
        }

        $js .= "f.cmbContaCorrente.options[".$inSelecionado."].selected = true;\n";

        $obRMONCarteira = new RMONCarteira;
        $obRMONCarteira->obRMONConvenio->setNumeroConvenio( $_REQUEST['inNumConvenio'] );
        $obRMONCarteira->listarCarteira( $rsCarteira );
        if ( $rsCarteira->eof() ) {
            $js .= "f.cmbCarteira.value=''; \n";
            $js .= "limpaSelect(f.cmbCarteira,1); \n";
            $js .= "f.cmbCarteira[0] = new Option('Selecione','', 'selected');\n";
        } else {
            $js .= "limpaSelect(f.cmbCarteira,1); \n";
            $js .= "f.cmbCarteira[0] = new Option('Selecione','', 'selected');\n";
            $inContador = 1;
            while ( !$rsCarteira->eof() ) {
                $js .= "f.cmbCarteira.options[$inContador] = new Option('".$rsCarteira->getCampo("num_carteira")."','".$rsCarteira->getCampo("cod_carteira")."'); \n";

                $rsCarteira->proximo();
                $inContador++;
            }
            $js .= "f.cmbCarteira.value='".$rsCreditoCarteira->getCampo("cod_carteira")."'; \n";
        }

    //----------------- busca indexacao igual ao codigo de um case, logo abaixo
    $obRMONCredito = new RMONCredito;
    $obRMONCredito->setCodCredito ( $_REQUEST['inCodCredito'] );

    $obFormulario = new Formulario;
    if ( ( $_REQUEST["boIndexacao"] == "Moeda" ) || !$_REQUEST["boIndexacao"] ) {

        $obRMONCredito->buscaMoedaCredito( $rsRecordSetA, $boTransacao );

        $obBscMoeda = new BuscaInner;
        $obBscMoeda->setRotulo ( "*Moeda" );
        $obBscMoeda->setTitle  ( "Moeda que indexa o acréscimo"  );
        $obBscMoeda->setId     ( "stMoeda"  );
        $obBscMoeda->obCampoCod->setName   ( "inCodMoeda" );
        $obBscMoeda->obCampoCod->obEvento->setOnChange("buscaValor('buscaMoeda');");
        $obBscMoeda->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."moeda/FLProcurarMoeda.php','frm','inCodMoeda','stMoeda','todos','".Sessao::getId()."','800','550');" );

        $obBscMoeda->obCampoCod->setValue  ( $rsRecordSetA->getCampo('cod_moeda') );
        $obFormulario->addComponente ( $obBscMoeda );
        $jsNome .= "d.getElementById('stMoeda').innerHTML = '".$rsRecordSetA->getCampo('descricao_plural'). "';\n";

    }
    if ($_REQUEST["boIndexacao"] == "Indicador Economico") {

        $obRMONCredito->buscaIndicadorCredito( $rsRecordSetA, $boTransacao );

        $obBscIndicador = new BuscaInner;
        $obBscIndicador->setRotulo ( "*Indicador Econômico" );
        $obBscIndicador->setTitle  ( "Indicador Econômico que indexa o acréscimo"  );
        $obBscIndicador->setId     ( "stIndicador"  );
        $obBscIndicador->obCampoCod->setName   ( "inCodIndicador" );
        $obBscIndicador->obCampoCod->obEvento->setOnChange("buscaValor('buscaIndicador');");
        $obBscIndicador->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."indicadorEconomico/FLProcurarIndicador.php','frm','inCodIndicador','stIndicador','todos','".Sessao::getId()."','800','550');" );

        $obBscIndicador->obCampoCod->setValue  ( $rsRecordSetA->getCampo('cod_indicador') );
        $obFormulario->addComponente ( $obBscIndicador );
        $jsNome .= "d.getElementById('stIndicador').innerHTML = '".$rsRecordSetA->getCampo('descricao'). "';\n";

    }

    $obFormulario->montaInnerHTML();
    $js .= "d.getElementById('spnIndexacao').innerHTML = '". $obFormulario->getHTML(). "';\n";

    SistemaLegado::executaFrameOculto($js);
    SistemaLegado::executaFrameOculto($jsNome);
    //----------------- fim do busca INDEXACAO (o mesmo do case)

    //----------------- recupera Acrescimos (mesma funcao do case)

    include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php" );
    $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;

    $rsAcrescimos = new RecordSet;
    $obRMONCreditoAcrescimo->setCodCredito ( $_REQUEST['inCodCredito'] );
    $obRMONCreditoAcrescimo->setCodEspecie( $_REQUEST['inCodEspecie'] );
    $obRMONCreditoAcrescimo->setCodGenero( $_REQUEST['inCodGenero'] );
    $obRMONCreditoAcrescimo->setCodNatureza( $_REQUEST['inCodNatureza'] );

    $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito($rsAcrescimos);

    if ( $rsAcrescimos->getNumLinhas () > 0 ) {

        $rsAcrescimos->ordena ('cod_acrescimo');
        $nregistros = $rsAcrescimos->getNumLinhas();

        $cont =0;
        while ($cont < $nregistros) {
            $arAcrescimosSessao = Sessao::read( "acrescimos" );
            $stInsere = false;
            if ($arAcrescimosSessao) {
                $inCountSessao = count ( $arAcrescimosSessao );
            } else {
                $inCountSessao = 0;
                $stInsere = true;
            }

            for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {

                if ( $arAcrescimosSessao[$iCount]['cod_acrescimo'] == $rsAcrescimos->getCampo('cod_acrescimo') ) {
                    $stInsere = false;
                    $iCount = $inCountSessao;
                } else {
                    $stInsere = true;
                }
            }

            if ($stInsere) {

                if ($arAcrescimosSessao) {
                    $inLast = count ($arAcrescimosSessao);
                } else {
                    $inLast = 0;
                    $arAcrescimosSessao = array();
                }
                $arAcrescimosSessao[$inLast]['cod_acrescimo'] = $rsAcrescimos->getCampo ('cod_acrescimo');
                $arAcrescimosSessao[$inLast]['descricao_acrescimo'] = $rsAcrescimos->getCampo ('descricao_acrescimo');
                $arAcrescimosSessao[$inLast]['cod_tipo'] = $rsAcrescimos->getCampo ('cod_tipo');
                Sessao::write( "acrescimos", $arAcrescimosSessao );
            }

            $rsAcrescimos->proximo();
            $cont++;
        }
    }

    $rsListaAcrescimos = new RecordSet;

    if ( count ( $arAcrescimosSessao ) > 0  ) {
        $rsListaAcrescimos->preenche ( $arAcrescimosSessao );
        $rsListaAcrescimos->ordena("cod_acrescimo");
    }

    montaListaAcrescimos ( $rsListaAcrescimos );
    //------------------ fim do case do recuperaAcrescimos (o mesmo do case)

    //fundamentacao
    $obTMONCreditoNorma = new TMONCreditoNorma;
    $stFiltro = " WHERE cm.cod_credito = ".$_REQUEST['inCodCredito']." AND cm.cod_especie = ".$_REQUEST['inCodEspecie']." AND cm.cod_genero = ".$_REQUEST['inCodGenero']." AND cm.cod_natureza = ".$_REQUEST['inCodNatureza'];
    $obTMONCreditoNorma->recuperaRelacionamentoBuscaNorma( $rsLista, $stFiltro, " ORDER BY dt_inicio_vigencia ASC " );
    $arTMP = array();
    while ( !$rsLista->Eof() ) {
        $arData = explode( "-", $rsLista->getCampo( "dt_inicio_vigencia" ) );
        $frase = $rsLista->getCampo( "nom_tipo_norma" ).' '.$rsLista->getCampo( "num_norma" ).'/'.$rsLista->getCampo( "exercicio" ).' - '.$rsLista->getCampo( "nom_norma" );
        $arTMP[] = array(
            "inCodNorma" => $rsLista->getCampo( "cod_norma" ),
            "dtVigenciaInicio" => $arData[2]."/".$arData[1]."/".$arData[0],
            "stNorma" => $frase
        );

        $rsLista->proximo();
    }

    Sessao::write( "listaFundamentacao", $arTMP );
    $rsDados = new RecordSet;
    $rsDados->preenche( $arTMP );
    $stJs = montaListaFundamentações( $rsDados );
    $stJs = "d.getElementById('spnFund').innerHTML = '".$stJs."';\n";
    SistemaLegado::executaFrameOculto($stJs);
    break;

 case "buscaMoeda":

    include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php"     );

    $obRMONMoeda = new RMONMoeda;
    $obRMONMoeda->setCodMoeda ( trim ($_REQUEST["inCodMoeda"]) );
    $obRMONMoeda->ConsultarMoeda();

    $inCodMoeda = $obRMONMoeda->getCodMoeda();
    $stMoeda = $obRMONMoeda->getDescPlural();

    if ($stMoeda != '') {
        $js .= "d.getElementById('stMoeda').innerHTML = '".$stMoeda."';\n";
    } else {
        $js .= "f.inCodMoeda.value ='';\n";
        $js .= "f.inCodMoeda.focus();\n";
        $js .= "d.getElementById('stMoeda').innerHTML = '&nbsp;';\n";
        $js .= "alertaAviso('@Moeda informada não existe. (".$_REQUEST["inCodMoeda"].")','form','erro','".Sessao::getId()."');";
    }
    sistemaLegado::executaFrameOculto($js);
 break;

 case "buscaIndicador":

    include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"     );

    $obRMONIndicador = new RMONIndicadorEconomico;
    $obRMONIndicador->setCodIndicador ( trim ($_REQUEST["inCodIndicador"]) );
    $obRMONIndicador->ListarIndicadores( $rsIndicador );

    $inCodigo = $rsIndicador->getCampo('cod_indicador');
    $stDescricao = $rsIndicador->getCampo('descricao');

    if ( $rsIndicador->getNumLinhas() > 0 ) {
        $stJs .= "d.getElementById('stIndicador').innerHTML = '".$stDescricao."';\n";
    } else {
        $stJs .= "f.inCodIndicador.value ='';\n";
        $stJs .= "f.inCodIndicador.focus();\n";
        $stJs .= "d.getElementById('stIndicador').innerHTML = '&nbsp;';\n";
        $stJs .= "alertaAviso('@Indicador informado não existe. (".$_REQUEST["inCodIndicador"].")','form','erro','".Sessao::getId()."');";
    }
    SistemaLegado::executaFrameOculto($stJs);
 break;

 case "buscaNorma":
    $obRMONNorma = new RNorma;
    $obRMONNorma->setCodNorma ( trim ($_REQUEST["inCodNorma"]) );
    $obRMONNorma->Consultar( $rsDados );

    $inCodigo = $obRMONNorma->getCodNorma();
    $stNomeTipoNorma = $obRMONNorma->obRTipoNorma->getNomeTipoNorma();
    $stNumNorma = $obRMONNorma->getNumNorma();
    $stExercicio = $obRMONNorma->getExercicio();
    $stNomeNorma = $obRMONNorma->getNomeNorma();

    $frase= $stNomeTipoNorma.' '.$stNumNorma.'/'.$stExercicio.' - '.$stNomeNorma;

    if ( !$rsDados->Eof() ) {
        $stJs .= "d.getElementById('stNorma').innerHTML = '".$frase."';\n";
    } else {
        $stJs .= "f.inCodNorma.value ='';\n";
        $stJs .= "f.inCodNorma.focus();\n";
        $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
        $stJs .= "alertaAviso('@Norma informada não existe. (".$_REQUEST["inCodNorma"].")','form','erro','".Sessao::getId()."');";
    }

    SistemaLegado::executaFrameOculto($stJs);
    sistemaLegado::LiberaFrames();

 break;

 case "BuscaIndexacao":

    include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );
    $obRMONCredito = new RMONCredito;

    $qualinicial = $_REQUEST['TipoIndexacao'];
    $indexacaoM = $_REQUEST['inIndexacaoM'];
    $indexacao = $_REQUEST['inIndexacao'];
    $NomeIndexacao = $_REQUEST['stIndexacao'];

    $obFormulario = new Formulario;
    if ( ( $_REQUEST["boIndexacao"] == "Moeda" ) || !$_REQUEST["boIndexacao"] ) {

        $obBscMoeda = new BuscaInner;
        $obBscMoeda->setRotulo ( "*Moeda" );
        $obBscMoeda->setTitle  ( "Moeda que indexa o acréscimo"  );
        $obBscMoeda->setId     ( "stMoeda"  );
        $obBscMoeda->obCampoCod->setName   ( "inCodMoeda" );
        $obBscMoeda->obCampoCod->obEvento->setOnChange("buscaValor('buscaMoeda');");
        $obBscMoeda->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."moeda/FLProcurarMoeda.php','frm','inCodMoeda','stMoeda','todos','".Sessao::getId()."','800','550');" );

        if ($qualinicial == 2) {
            $obBscMoeda->obCampoCod->setValue  ( $indexacao );
        }

        $obFormulario->addComponente ( $obBscMoeda );

        if ($qualinicial == 2) {
            $jsNome .= "d.getElementById('stMoeda').innerHTML = '".$NomeIndexacao. "';\n";
        }
    }
    if ($_REQUEST["boIndexacao"] == "Indicador Economico") {

        $obBscIndicador = new BuscaInner;
        $obBscIndicador->setRotulo ( "*Indicador Econômico" );
        $obBscIndicador->setTitle  ( "Indicador Econômico que indexa o acréscimo"  );
        $obBscIndicador->setId     ( "stIndicador"  );
        $obBscIndicador->obCampoCod->setName   ( "inCodIndicador" );
        $obBscIndicador->obCampoCod->obEvento->setOnChange("buscaValor('buscaIndicador');");
        $obBscIndicador->setFuncaoBusca ( "abrePopUp('".CAM_GT_MON_POPUPS."indicadorEconomico/FLProcurarIndicador.php','frm','inCodIndicador','stIndicador','todos','".Sessao::getId()."','800','550');" );

        if ($qualinicial == 1) {
            $obBscIndicador->obCampoCod->setValue  ( $indexacao );
        }
        $obFormulario->addComponente ( $obBscIndicador );
        if ($qualinicial == 1) {
            $jsNome .= "d.getElementById('stIndicador').innerHTML = '".$NomeIndexacao. "';\n";
        }

    }

    $obFormulario->montaInnerHTML();
    $js .= "d.getElementById('spnIndexacao').innerHTML = '". $obFormulario->getHTML(). "';\n";

    SistemaLegado::executaFrameOculto($js);
    SistemaLegado::executaFrameOculto($jsNome);

break;

case "incluirAcrescimo":

    include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php" );
    include_once ( CAM_GT_MON_NEGOCIO."RMONAcrescimo.class.php" );

    $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;
    $obRMONAcrescimo = new RMONAcrescimo;

    $rsAcrescimos = new RecordSet;

    $newCredito = $_REQUEST['inCodCredito'];
    $arDados = explode( ".", $_REQUEST['inCodAcrescimo'] );

    $obRMONCreditoAcrescimo->setCodCredito ( $newCredito );
    $obRMONCreditoAcrescimo->setCodAcrescimo ( $arDados[0] );
    $obRMONAcrescimo->setCodAcrescimo ( $arDados[0] );
    $obRMONAcrescimo->setCodTipo ( $arDados[1] );

    $rsLista = new Lista;
    $obRMONAcrescimo->ConsultarAcrescimo( $rsLista );

    if ( $rsLista->getNumLinhas() < 1  ) { /* ACRESCIMO INEXISTENTE';*/

        $stJs .= "f.inCodAcrescimo.value ='';\n";
        $stJs .= "f.inCodAcrescimo.focus();\n";
        $stJs .= "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
        $stJs .= "alertaAviso('@Acréscimo informado não existe. (".$_REQUEST["inCodAcrescimo"].")','form','erro','".Sessao::getId()."');";
        SistemaLegado::executaFrameOculto($stJs);


    } else {
        $arAcrescimosSessao = Sessao::read( "acrescimos" );
        $nregistros = count ( $arAcrescimosSessao );
        $cont = 0; $insere = true;
        while ($cont < $nregistros) {
            if ( $arAcrescimosSessao[$cont]['cod_acrescimo'] == $obRMONAcrescimo->getCodAcrescimo () ) {
                //INCLUIR ACRESCIMO -> Acrescimo jah existente
                $obErro = new Erro;
                $obErro->setDescricao("O Acrescimo ". $_REQUEST["inCodAcrescimo"]." já está na lista de acréscimos para este crédito!");
                $js .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".$sessao->id."');";

                $js .= "d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
                $js .= "f.inCodAcrescimo.value ='';\n";
                $js .= "f.inCodAcrescimo.focus();\n";

                sistemaLegado::executaFrameOculto( $js );

                $insere = false;
            } elseif ( $arAcrescimosSessao[$cont]['cod_tipo'] == $obRMONAcrescimo->getCodTipo () ) {
                //INCLUIR ACRESCIMO -> TIPO DE Acrescimo jah existente


                $obErro = new Erro;
                $obErro->setDescricao("Este tipo de Acrescimo (". $obRMONAcrescimo->getNomTipo () .") já está na lista de acréscimos para este crédito!");
                $js .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".$sessao->id."');";

                $js .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";
                $js .= "f.inCodAcrescimo.value ='';\n";
                $js .= "f.inCodAcrescimo.focus();\n";

                sistemaLegado::executaFrameOculto( $js );

                $insere = false;
            }

            $cont++;
        }
        if ($insere) {
            $nregistros = count ( $arAcrescimosSessao );
            $arAcrescimosSessao[$nregistros]['cod_acrescimo']       = $obRMONAcrescimo->getCodAcrescimo ();
            $arAcrescimosSessao[$nregistros]['descricao_acrescimo'] = $obRMONAcrescimo->getDescricao ();
            $arAcrescimosSessao[$nregistros]['cod_tipo']            = $obRMONAcrescimo->getCodTipo ();
            Sessao::write( "acrescimos", $arAcrescimosSessao );
        }
    }

//fecha se ja tem
    $rsListaAcrescimos = new RecordSet;
    $rsListaAcrescimos->preenche ( $arAcrescimosSessao );

    $rsListaAcrescimos->ordena("cod_acrescimo");
    montaListaAcrescimos ( $rsListaAcrescimos );

    $js .= " d.getElementById('stDescricaoAcrescimo').innerHTML = '&nbsp;';\n";
    $Js .= "f.inCodAcrescimo.value ='';\n";
    $Js .= "f.inCodAcrescimo.focus();\n";
    SistemaLegado::executaFrameOculto($js);

break;

case "excluirAcrescimo":

        $arTmpAtividade = array ();
        $arAcrescimosSessao = Sessao::read( "acrescimos" );
        $inCountSessao = count ($arAcrescimosSessao);
        $inCountArray = 0;

        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arAcrescimosSessao[$inCount][ "cod_acrescimo" ] != $_REQUEST[ "inIndice" ]) {
                $arTmpAtividade[$inCountArray]["cod_acrescimo"]  = $arAcrescimosSessao[$inCount][ "cod_acrescimo"  ];
                $arTmpAtividade[$inCountArray]["descricao_acrescimo"] = $arAcrescimosSessao[$inCount][ "descricao_acrescimo"        ];
                 $arTmpAtividade[$inCountArray]["cod_tipo"] = $arAcrescimosSessao[$inCount][ "cod_tipo" ];
                $inCountArray++;
            }
        }

        Sessao::write( "acrescimos", $arTmpAtividade );

        $rsListaAtividades = new RecordSet;
        $rsListaAtividades->preenche ( $arTmpAtividade );

        $rsListaAtividades->ordena("cod_acrescimo");

        montaListaAcrescimos( $rsListaAtividades );

break;


case "recuperaAcrescimos":

  include_once ( CAM_GT_MON_NEGOCIO."RMONCreditoAcrescimo.class.php" );
  $obRMONCreditoAcrescimo = new RMONCreditoAcrescimo;

  $rsAcrescimos = new RecordSet;

  $newCredito = $_REQUEST['inCodCredito'];
  $obRMONCreditoAcrescimo->setCodCredito ( $newCredito );

  $obRMONCreditoAcrescimo->ListarAcrescimosDoCredito($rsAcrescimos);
  if ( $rsAcrescimos->getNumLinhas () > 0 ) {

    $rsAcrescimos->ordena ('cod_acrescimo');
    $nregistros = $rsAcrescimos->getNumLinhas();

    $cont =0;
    $arAcrescimosSessao = Sessao::read( "acrescimos" );
    while ($cont < $nregistros) {
        $stInsere = false;
        if ($arAcrescimosSessao) {
            $inCountSessao = count ( $arAcrescimosSessao );
        } else {
            $inCountSessao = 0;
            $stInsere = true;
        }

        for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
            if ( $arAcrescimosSessao[$iCount]['cod_acrescimo'] == $rsAcrescimos->getCampo('cod_acrescimo') ) {
                $stInsere = false;
                $iCount = $inCountSessao;
            } else {
                $stInsere = true;
            }
        }
        if ($stInsere) {
            if ($arAcrescimosSessao) {
                $inLast = count( $arAcrescimosSessao );
            } else {
                $inLast = 0;
                $arAcrescimosSessao = array();
            }

            $arAcrescimosSessao[$inLast]['cod_acrescimo'] = $rsAcrescimos->getCampo ('cod_acrescimo');
            $arAcrescimosSessao[$inLast]['descricao_acrescimo'] = $rsAcrescimos->getCampo ('descricao_acrescimo');
            $arAcrescimosSessao[$inLast]['cod_tipo'] = $rsAcrescimos->getCampo ('cod_tipo');
        }
        $rsAcrescimos->proximo();
        $cont++;
     }

     Sessao::write( "acrescimos", $arAcrescimosSessao );
  }

    $rsListaAcrescimos = new RecordSet;
    if ( count ( $arAcrescimosSessao )  > 0  ) {
        $rsListaAcrescimos->preenche ( $arAcrescimosSessao );
        $rsListaAcrescimos->ordena("cod_acrescimo");
    }

    montaListaAcrescimos ( $rsListaAcrescimos );

break;

case "limparAcrescimo":

    $js .= "f.inCodAcrescimo.value ='';\n";
    $js .= "f.inCodAcrescimo.focus();\n";
    $js .= "d.getElementById('stAcrescimo').innerHTML = '&nbsp;';\n";

    sistemaLegado::executaFrameOculto($js);

break;

    case "preencheGenero":

        $js .= "f.inCodGenero.value=''; \n";
        $js .= "limpaSelect(f.cmbGenero,1); \n";
        $js .= "f.cmbGenero[0] = new Option('Selecione','', 'selected');\n";

        $js .= "f.inCodEspecie.value=''; \n";
        $js .= "limpaSelect(f.cmbEspecie,1); \n";
        $js .= "f.cmbEspecie[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST['inCodNatureza']) {

            $obRMONCredito->setCodNatureza( $_REQUEST["inCodNatureza"] );
            $obRMONCredito->ListarGenero ( $rsGenero );

                $inContador = 1;
                while ( !$rsGenero->eof() ) {

                    $inCodGenero = $rsGenero->getCampo( "cod_genero" );
                    $stNomGenero = $rsGenero->getCampo( "nom_genero" );
                    $js .= "f.cmbGenero.options[$inContador] = new Option('".$stNomGenero."','".$inCodGenero."'); \n";
                    $inContador++;
                    $rsGenero->proximo();

                }

                if ($_REQUEST["stLimpar"] == "limpar") {
                    $js .= "f.inCodGenero.value='".$_REQUEST["inCodGenero"]."'; \n";
                    $js .= "f.cmbGenero.options[".$_REQUEST["inCodGenero"]."].selected = true; \n";
                }
        }
        sistemaLegado::executaFrameOculto($js);

    break;

    case "preencheEspecie":

        $js .= "f.inCodEspecie.value=''; \n";
        $js .= "limpaSelect(f.cmbEspecie,1); \n";
        $js .= "f.cmbEspecie[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST['inCodGenero']) {

            $obRMONCredito->setCodNatureza( $_REQUEST["inCodNatureza"] );
            $obRMONCredito->setCodGenero( $_REQUEST["inCodGenero"] );
            $obRMONCredito->listarEspecie( $rsEspecie );

            $inContador = 1;
            while ( !$rsEspecie->eof() ) {

                $inCodEspecie = $rsEspecie->getCampo( "cod_especie" );
                $stNomEspecie = $rsEspecie->getCampo( "nom_especie" );
                $js .= "f.cmbEspecie.options[$inContador] = new Option('".$stNomEspecie."','".$inCodEspecie."'); \n";
                $inContador++;
                $rsEspecie->proximo();

            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodGenero.value='".$_REQUEST["inCodGenero"]."'; \n";
            $js .= "f.cmbGenero.options[".$_REQUEST["inCodGenero"]."].selected = true; \n";
        }
        sistemaLegado::executaFrameOculto($js);
    break;
}

?>

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
    * Página Oculto de Definiçao de Atividade para Inscrição Ecônomica
    * Data de Criação   : 30/12/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: OCDefinirAtividades.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.23  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DefinirAtividades";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

// VERIFICA A INTERSECCAO ENTRE OS INTERVALOS DE HORARIOS
function checaHorario($ini, $fim , $novoini, $novofim)
{
    if ($novoini < $ini && $novofim < $ini) {
        $retorno = true;
    } elseif ($novoini > $fim && $novofim > $fim) {
        $retorno = true;
    } else {
        $retorno = false;
    }

    return $retorno;
}

function montaListaAtividade($arListaAtividade)
{
     $rsListaAtividade = new Recordset;
     $rsListaAtividade->preenche( is_array($arListaAtividade) ? $arListaAtividade : array() );

     if ( !$rsListaAtividade->eof() ) {

         $obLista = new Lista;
         $obLista->setMostraPaginacao( false );
         $obLista->setRecordSet( $rsListaAtividade );
         $obLista->setTitulo ("Listas de Atividade Econômicas");
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("Código");
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Descrição" );
         $obLista->ultimoCabecalho->setWidth( 40 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Data de Início" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Data de Término" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();
         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo( "Principal" );
         $obLista->ultimoCabecalho->setWidth( 10 );
         $obLista->commitCabecalho();

         $obLista->addCabecalho();
         $obLista->ultimoCabecalho->addConteudo("&nbsp;");
         $obLista->ultimoCabecalho->setWidth( 2 );
         $obLista->commitCabecalho();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stChaveAtividade" );
         $obLista->ultimoDado->setAlinhamento( "DIREITA" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stNomeAtividade" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "dtDataInicio" );
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "dtDataTermino" );
         $obLista->ultimoDado->setAlinhamento( "CENTRO" );
         $obLista->commitDado();
         $obLista->addDado();
         $obLista->ultimoDado->setCampo( "stPrincipal" );
         $obLista->commitDado();

         $obLista->addAcao();
         $obLista->ultimaAcao->setAcao( "EXCLUIR" );
         $obLista->ultimaAcao->setFuncao( true );
         $obLista->ultimaAcao->addCampo( "1","inId" );
         $obLista->ultimaAcao->setLink( "javascript: excluirDado('excluirAtividade');" );
         $obLista->commitAcao();
         $obLista->montaHTML();
         $stHTML = $obLista->getHTML();
         $stHTML = str_replace("\n","",$stHTML);
         $stHTML = str_replace("  ","",$stHTML);
         $stHTML = str_replace("'","\\'",$stHTML);

     } else {
         $stHTML = "&nbsp;";
     }

     global $pgOcul;
     global $pgProc;
     ;

     $stJs = "d.getElementById('lsListaAtividade').innerHTML = '".$stHTML."';\n";
     $stJs.= "f.stChaveAtividade.value = '';\n";
     //$stJs.= "f.dtDataInicio.value = '';\n";
     $stJs.= "f.dtDataTermino.value = '';\n";
     $stJs.= "f.inCodAtividade_1.selectedIndex = 0;\n";
     $stJs.= "f.inCodAtividade_2.selectedIndex = 0;\n";
     $stJs.= "f.inCodAtividade_3.selectedIndex = 0;\n";
     /*$stJs.= "f.stCtrl.value = 'preencheCombosAtividade'\n";
     $stJs.= "f.target = 'oculto'\n";
     $stJs.= "f.action = '".$pgOcul."?".Sessao::getId()."'\n";
     $stJs.= "f.submit()\n";
     $stJs.= "f.action = '".$pgProc."?".Sessao::getId()."'\n";
     */
     $stJs.= "f.stChaveAtividade.focus() ;\n";
     $stJs.= "f.dtDataInicio.value = '".date('d/m/Y')."'\n";
     sistemaLegado::executaFrameOculto($stJs);

     return $stJs;
}

function montaListaHorario($rsListaDias)
{
    if ( $rsListaDias->getNumLinhas() != 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaDias         );
        $obLista->setTitulo                    ( "Lista de Horários"  );
        $obLista->setMostraPaginacao           ( false                );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista->ultimoCabecalho->setWidth    ( 5                    );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Dia da Semana"      );
        $obLista->ultimoCabecalho->setWidth    ( 30                   );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Horário de Início"  );
        $obLista->ultimoCabecalho->setWidth    ( 30                   );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "Horário de Término" );
        $obLista->ultimoCabecalho->setWidth    ( 30                   );
        $obLista->commitCabecalho              (                      );
        $obLista->addCabecalho                 (                      );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista->ultimoCabecalho->setWidth    ( 5                    );
        $obLista->commitCabecalho              (                      );

        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "stDia"              );
        $obLista->commitDado                   (                      );
        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "hrInicio"           );
        $obLista->commitDado                   (                      );
        $obLista->addDado                      (                      );
        $obLista->ultimoDado->setCampo         ( "hrTermino"          );
        $obLista->commitDado                   (                      );

        $obLista->addAcao                      (                      );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"            );
        $obLista->ultimaAcao->setFuncao        ( true                 );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirHorario();" );
        $obLista->ultimaAcao->addCampo         ( "1","inId"    );
        $obLista->commitAcao                   (                      );

        $obLista->montaHTML                    (                      );
        $stHTML =  $obLista->getHtml           (                      );
        $stHTML = str_replace                  ( "\n","",$stHTML      );
        $stHTML = str_replace                  ( "  ","",$stHTML      );
        $stHTML = str_replace                  ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }
    $js .= "d.getElementById('lsListaHorario').innerHTML = '".$stHTML."';\n";
    $js .= "f.dia1.checked = false;\n ";
    $js .= "f.dia2.checked = false;\n";
    $js .= "f.dia3.checked = false;\n";
    $js .= "f.dia4.checked = false;\n";
    $js .= "f.dia5.checked = false;\n";
    $js .= "f.dia6.checked = false;\n";
    $js .= "f.dia7.checked = false;\n";
    $js .= "f.hrHorarioInicio.value = '';\n";
    $js .= "f.hrHorarioTermino.value = '';\n";
    //sistemaLegado::executaFrameOculto($js);
    return $js;
}

function BuscaAtividade()
{
    global $_REQUEST;
    $obRCEMAtividade = new RCEMAtividade;

    $stText = "stValorCompostoAtividade";
    $stSpan = "stNomeAtividade";
    if ($_REQUEST[ $stText ] != "") {
        $obRCEMAtividade->setCodigoAtividade( $_REQUEST[ 'inCodigoAtividade' ] );
        $obRCEMAtividade->setValorComposto( $_REQUEST[ $stText ] );
        $obRCEMAtividade->listarAtividade( $rsAtividade );
        $stNull = "&nbsp;";

        if ( $rsAtividade->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "';
           $stJs .= ($rsAtividade->getCampo('nom_atividade')?$rsAtividade->getCampo('nom_atividade'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

switch ($_REQUEST["stCtrl"]) {
    case "buscaAtividade":
        sistemaLegado::executaFrameOculto( BuscaAtividade() );
        break;

    case "montaAtividadeAlterar":
        montaListaAtividade( Sessao::read( 'Atividades' ) );
        break;

    case "montaAtividade":
    $boInsereAtividade = true;
    if ( empty($_REQUEST['dtDataInicio']) ) {
            $stJs = " alertaAviso('@Valor inválido. (A data de de início não pode ser nula)','form','aviso','".Sessao::getId()."');";
        } else {
            $stMensagem = false;
            if ($_REQUEST['stChaveAtividade'] == "") {
                $stJs  = " alertaAviso('@Valor inválido. (Nenhuma atividade informada.)','form','aviso','".Sessao::getId()."'); \n";
                $stJs .= " f.inCodAtividade_1.selectedIndex = 0 ;\n";
                $boInsereAtividade = false;
            }

            if ( sistemaLegado::comparaDatas( $_REQUEST['dtDataInicio'] , $_REQUEST['dtDataTermino'] ) && !empty($_REQUEST['dtDataTermino']) ) {
                $stJs = " alertaAviso('@Valor inválido. (A data de término deve ser maior que a data de início)','form','aviso','".Sessao::getId()."');";
                $boInsereAtividade = false;
            }

            // VERIFICA DE A DATA DE ABERTURA DA INSCRICAO NAO E SUPERIOR A DATA DE INICIO DA ATIVIDADE
            if (sistemaLegado::comparaDatas( $_REQUEST['stDtAbertura'], $_REQUEST['dtDataInicio'] ) ) {
                $stJs = " alertaAviso('@Valor inválido. (A data de início deve ser maior que a data de abertura da inscrição)','form','aviso','".Sessao::getId()."');";
                $boInsereAtividade = false;
            }
            if ($boInsereAtividade == true) {
                $arAtividadesSessao = Sessao::read( "Atividades" );
                if ($arAtividadesSessao) {
                    foreach ($arAtividadesSessao as $campo => $valor) {
                        if ($arAtividadesSessao[$campo]['stChaveAtividade'] == $_REQUEST['stChaveAtividade']) {
                            $stJs  = " alertaAviso('@Valor inválido. (Atividade ".$_REQUEST['stChaveAtividade']." já existe.)','form','aviso','".Sessao::getId()."'); \n";
                            $stJs .= " f.inCodAtividade_1.selectedIndex = 0 ;\n";
                            $boInsereAtividade = false;
                        } elseif ($arAtividadesSessao[$campo]['stPrincipal'] == "sim" && $_REQUEST["stPrincipal"] == "sim") {
                            $stJs  = " alertaAviso('@Só pode ser cadastrada uma atividade principal.','form','aviso','".Sessao::getId()."'); \n";
                            $stJs .= " f.inCodAtividade_1.selectedIndex = 0 ;\n";
                            $boInsereAtividade = false;
                        }
                    }
                }
            }
        if ($stMensagem == "" AND $boInsereAtividade == true) {
                $stTemp = 'inCodAtividade_';
                $inTemp = $_REQUEST['inNumNiveis'] - 1;
                $stTemp .= $inTemp;
                $arTemp = explode('§',$_REQUEST[$stTemp] );
        if ($arTemp[1] == "") {
                    $stMensagem = "Atividade ".$_REQUEST['stChaveAtividade']." não existe.";
                    $stJs .= "f.stChaveAtividade.value = '';\n";
                    $stJs .= "f.inCodAtividade_1.selectedIndex = 0;\n";
                    $stJs .= "f.stChaveAtividade.focus() ;\n";
                    $stJs .= "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
                    sistemaLegado::executaFrameOculto($stJs);
                } else {
            $obRCEMAtividade = new RCEMAtividade;
                    $rsRecordSet = new Recordset;
                    $rsAtividade = new Recordset;
                    $rsRecordSet->preenche( $arAtividadesSessao );
            $rsRecordSet->setUltimoElemento();

                    $obRCEMAtividade->setCodigoAtividade( $arTemp[1] );
                    $obRCEMAtividade->setValorComposto( $_REQUEST['stChaveAtividade'] );

                    $obRCEMAtividade->listarAtividade( $rsAtividade );

            if ($rsRecordSet->getNumLinhas() > 0) {
            $inUltimoId = $rsRecordSet->getCampo("inId");
            } else {
            $inUltimoId = "";
            }

                    if (!$inUltimoId) {
                        $inProxId = 1;
                    } else {
                        $inProxId = $inUltimoId + 1;
                    }

                    $arElementos['inId']                     = $inProxId;
                    $arElementos['stChaveAtividade']         = $_REQUEST['stChaveAtividade'];
                    $arElementos['stNomeAtividade']          = $rsAtividade->getCampo( 'nom_atividade' );
                    $arElementos['inCodigoAtividade']        = $rsAtividade->getCampo( 'cod_atividade' );
                    $arElementos['dtDataInicio']                 = $_REQUEST[ 'dtDataInicio'  ];
                    $dtTermino = "&nbsp;";   if ($_REQUEST[ 'dtDataTermino' ]) {  $dtTermino = $_REQUEST[ 'dtDataTermino' ];  }
                    $arElementos['dtDataTermino']            = $dtTermino;
                    $arElementos['stPrincipal']              = $_REQUEST[ 'stPrincipal'   ];
                    $arAtividadesSessao[]          = $arElementos;
                    Sessao::write( "Atividades", $arAtividadesSessao );
                    $stJs = montaListaAtividade( $arAtividadesSessao );
                }
            } else {
                //$stJs = " alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
                //$stJs .= " f.inCodAtividade_1.selectedIndex = 0 ;\n";
            }
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;

    case "incluirHorario":
        $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
        $rsDiasSemana  = new RecordSet;
        $obRCEMInscricaoEconomica->listarDiasSemana( $rsDiasSemana );
        $arHorariosSessao = Sessao::read( "horarios" );
        while ( !$rsDiasSemana->eof() ) {
            $stDia = $rsDiasSemana->getCampo("nom_dia");
            $inDia = $rsDiasSemana->getCampo("cod_dia");
            $aval = "dia".$inDia;
            if ($_REQUEST["$aval"]) {
                $arDiasSemana["inDia"]  = $inDia;
                $arDiasSemana["stDia"]  = $stDia;
                $arDiasSemana["hrInicio"]  = $_REQUEST["hrHorarioInicio"];
                $arDiasSemana["hrTermino"] = $_REQUEST["hrHorarioTermino"];
                $stInsere = false;
                if ($arHorariosSessao) {
                    $inCountSessao = count ( $arHorariosSessao );
                } else {
                    $inCountSessao = 0;
                    $stInsere = true;
                }

                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    $hrComparaInicio = str_replace(':','',$arDiasSemana["hrInicio"]);
                    $hrComparaFim    = str_replace(':','',$arDiasSemana["hrTermino"]);
                    $hrAtualInicio   = str_replace(':','',$arHorariosSessao[$iCount]["hrInicio"]);
                    $hrAtualFim      = str_replace(':','',$arHorariosSessao[$iCount]["hrTermino"]);
                    if ($arHorariosSessao[$iCount]["inDia"] == $arDiasSemana["inDia"]) {
                        if ( checaHorario($hrAtualInicio, $hrAtualFim, $hrComparaInicio, $hrComparaFim) ) {
                            $stInsere = true;
                        } else {
                            $stInsere = false;
                            $iCount = $inCountSessao;
                        }
                    } else { $stInsere = true; }
                }
                if ($stInsere) {
                    if ($arHorariosSessao) {
                        $inLast = count ($arHorariosSessao);
                    } else {
                        $inLast = 0;
                        $arHorariosSessao = array ();
                    }
                    $arHorariosSessao[$inLast]["inId"      ] = $inCountSessao;
                    $arHorariosSessao[$inLast]["inDia"     ] = $arDiasSemana["inDia"     ];
                    $arHorariosSessao[$inLast]["stDia"     ] = $arDiasSemana["stDia"     ];
                    $arHorariosSessao[$inLast]["hrInicio"  ] = $arDiasSemana["hrInicio"  ];
                    $arHorariosSessao[$inLast]["hrTermino" ] = $arDiasSemana["hrTermino" ];

                    $crtl=true;
                    foreach (Sessao::read("horarios") as $horario) {
                        if($horario['inDia'] == $inDia) {
                            $crtl=false;
                            $js .= " alertaAviso('Você deve primeiro deletar o dia da semana no qual está tentando inserir. Somente depois disso você deve incluir novamente o dia.','form','erro','".Sessao::getId()."');\n";
                        }
                    }
                    
                    if($crtl) {
                        Sessao::write( "horarios", $arHorariosSessao );    
                    }
                    
                } else {
                    $inErro++;
                }
            }
            $rsDiasSemana->proximo();
        }

        $rsListaDias = new RecordSet;
        $rsListaDias->preenche(Sessao::read("horarios"));
        $rsListaDias->ordena("inDia");
        
        if ($inErro > 0) {
            $js .= " alertaAviso('Você está tentando inserir horários já cadastrados para esta atividade.','form','erro','".Sessao::getId()."');\n";
        }
        $js .= montaListaHorario ( $rsListaDias );

        sistemaLegado::executaFrameOculto($js);
    break;

    case "excluirHorario":
        $arTmpHorarios = array ();
        $arHorariosSessao = Sessao::read( "horarios" );
        $inCountSessao = count( $arHorariosSessao );
        $inCountArray = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arHorariosSessao[$inCount][ "inId" ] != $_REQUEST[ "inIndice" ]) {
                $arTmpHorarios[$inCountArray]["inId"]      = $inCount;
                //$arTmpHorarios[$inCountArray]["inId"]      = $sessao->transf4[ "horarios" ][$inCount][ "inId"      ];
                $arTmpHorarios[$inCountArray]["inDia"]     = $arHorariosSessao[$inCount][ "inDia"     ];
                $arTmpHorarios[$inCountArray]["stDia"]     = $arHorariosSessao[$inCount][ "stDia"     ];
                $arTmpHorarios[$inCountArray]["hrInicio"]  = $arHorariosSessao[$inCount][ "hrInicio"  ];
                $arTmpHorarios[$inCountArray]["hrTermino"] = $arHorariosSessao[$inCount][ "hrTermino" ];
                $inCountArray++;
            }
        }

        Sessao::write( "horarios", $arTmpHorarios );
        $rsListaDias = new RecordSet;
        $rsListaDias->preenche ( $arTmpHorarios );
        $rsListaDias->ordena("inDia");
        $js = montaListaHorario ( $rsListaDias );
        sistemaLegado::executaFrameOculto($js);
    break;
    case "limparHorario":
        Sessao::write( "horarios", array() );
        $rsListaDias = new RecordSet;
    break;
    case "preencheProxCombo":
        $inCodVigencia = $_REQUEST["inCodigoVigencia"];
        if (!$inCodVigencia) {
            $obRCEMAtivi = new RCEMAtividade;
            $obRCEMAtivi->recuperaVigenciaAtual( $rsVigencia );
            unset( $obRCEMAtivi );
            $inCodVigencia = $rsVigencia->getCampo("cod_vigencia");
        }

        if ($_REQUEST["stMascara"] == "Z.99.9.9-9.99") {
            $stNomeComboCnae = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
            $stChaveLocal = $_REQUEST[$stNomeComboCnae];
            $inPosicao = $_REQUEST["inPosicao"];

            if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
                $stNomeComboCnae = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
                $stChaveLocal = $_REQUEST[$stNomeComboCnae];
                $inPosicao = $_REQUEST["inPosicao"] - 1;
            }

            $arChaveLocal = explode("§" , $stChaveLocal );

            $obMontaAtividade->setCodigoVigencia    ( $inCodVigencia );
            $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
            $obMontaAtividade->boPopUp = false;
            if ($arChaveLocal[0] == 1) {
                $obMontaAtividade->setValorReduzido   ( $arChaveLocal[2] );
            }else
                if ($arChaveLocal[0] == 2) {
                    $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 4 ) );
                }else
                    if ($arChaveLocal[0] == 3) {
                        $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 6 ) );
                    }else
                        if ($arChaveLocal[0] == 4) {
                            $obMontaAtividade->setValorReduzido ( substr( $arChaveLocal[3], 0, 10 ) );
                        } else {
                            $obMontaAtividade->setValorReduzido ( $arChaveLocal[3] );
                        }

            $obMontaAtividade->preencheProxComboCNAE ( $inPosicao , $_REQUEST["inNumNiveis"] );
        } else {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"];
            if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
                $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
                $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
                $inPosicao = $_REQUEST["inPosicao"] - 1;
            }
            $arChaveLocal = explode("§" , $stChaveLocal );
            $obMontaAtividade->setCodigoVigencia    ( $inCodAtividade );
            $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
            $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
            $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
        }
        break;

    case "preencheCombosAtividade":
        $inCodVigencia = $_REQUEST["inCodigoVigencia"];
        if (!$inCodVigencia) {
            $obRCEMAtivi = new RCEMAtividade;
            $obRCEMAtivi->recuperaVigenciaAtual( $rsVigencia );
            unset( $obRCEMAtivi );
            $inCodVigencia = $rsVigencia->getCampo("cod_vigencia");
        }

        if ($_REQUEST["stMascara"] == "Z.99.9.9-9.99") {
            $obMontaAtividade->setCodigoVigencia( $inCodVigencia   );
            $obMontaAtividade->setValorReduzido ( $_REQUEST["stChaveAtividade"] );
            $obMontaAtividade->preencheCombos2();
        } else {
            $obMontaAtividade->setCodigoVigencia   ( $inCodVigencia  );
            $obMontaAtividade->setCodigoNivel         ( $_REQUEST["inCodigoNivel"]      );
            $obMontaAtividade->setValorReduzido     ( $_REQUEST["stChaveAtividade"] );
            $obMontaAtividade->setMascara              ( $_REQUEST['stMascara'] );
            $obMontaAtividade->preencheCombosAtividade();
        }
    break;
    case "excluirAtividade":
        $id = $_REQUEST['inId'];
        $stMensagem = false;

        if ($stMensagem==false) {
            $arAtividadesSessao = Sessao::read( "Atividades" );
        reset( $arAtividadesSessao );
            while ( list( $arId ) = each( $arAtividadesSessao ) ) {
                if ($arAtividadesSessao[$arId]["inId"] != $id) {
                    $arElementos['inId']              = $arAtividadesSessao[$arId]["inId"];
                    $arElementos['inCodigoAtividade'] = $arAtividadesSessao[$arId]["inCodigoAtividade"];
                    $arElementos['stChaveAtividade']  = $arAtividadesSessao[$arId]["stChaveAtividade"];
                    $arElementos['stNomeAtividade']   = $arAtividadesSessao[$arId]["stNomeAtividade"];
                    $arElementos['dtDataInicio']      = $arAtividadesSessao[$arId]["dtDataInicio"];
                    $arElementos['dtDataTermino']     = $arAtividadesSessao[$arId]["dtDataTermino"];
                    $arElementos['stPrincipal']       = $arAtividadesSessao[$arId]["stPrincipal"];
                    $arTMP[] = $arElementos;
                }
            }
            Sessao::write( "Atividades", $arTMP );
            $stJs = montaListaAtividade( $arTMP );
       } else {
           $stJs = "alertaAviso('@ ($stMensagem)','form','erro','".Sessao::getId()."');";
       }
       sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limpar":

        $stJs .= "d.getElementById('lsListaHorario').innerHTML = '';\n";
        $stJs .= "d.getElementById('lsListaAtividade').innerHTML = '';\n";
        sistemaLegado::executaFrameOculto( $stJs );
        Sessao::write( 'Atividades', array() );
        Sessao::write( 'horarios', array() );
    break;

    case "limparAtividade":
/*
        $rsListaDias = new RecordSet;
        $rsListaDias->preenche( $sessao->transf4['horarios'] );
        $rsListaDias->ordena('inDia');
        $stJs .= montaListaHorario( $rsListaDias );
        $stJs .= montaListaAtividade( $sessao->transf2['Atividades'] );
        sistemaLegado::executaFrameOculto( $stJs );
    */
    break;

    case "buscaProcesso":
        $obRProcesso  = new RProcesso;
        if ($_POST['inNumProcesso'] != '') {
            list($inProcesso,$inExercicio) = explode("/",$_POST['inNumProcesso']);
            $obRProcesso->setCodigoProcesso( $inProcesso  );
            $obRProcesso->setExercicio     ( $inExercicio );
            $obErro = $obRProcesso->validarProcesso();

            if ( $obErro->ocorreu() ) {
                $stJs .= 'f.inNumProcesso.value = "";';
                $stJs .= 'f.inNumProcesso.focus();';
                $stJs .= "alertaAviso('@Processo nao encontrado. (".$_POST["inNumProcesso"].")','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "recuperarHorario":

        $rsListaDias = new RecordSet;
        $rsListaDias->preenche( Sessao::read( 'horarios' ) );
        $rsListaDias->ordena('inDia');
        $js = montaListaHorario( $rsListaDias );
        sistemaLegado::executaFrameOculto($js);

    break;

    case "recuperaAtividadeHorario":

        $rsListaDias = new RecordSet;
        $rsListaDias->preenche( Sessao::read( 'horarios' ) );
        $rsListaDias->ordena('inDia');
        $js = montaListaHorario( $rsListaDias );
        $js .= montaListaAtividade( Sessao::read( 'Atividades' ) );
        $js.= "f.dtDataInicio.value = '".date('d/m/Y')."'\n";
        sistemaLegado::executaFrameOculto($js);

    break;
}

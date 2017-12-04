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
    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criaï¿½ï¿½o   : 02/12/2004
    * @author Analista: Ricardo Lopes
    * @author Desenvolvedor: Fï¿½bio Bertoldi Rodrigues
    * @package URBEM
    * @subpackage Regra

    * $Id: OCManterLicenca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"            );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaAtividade.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaEspecial.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

function BuscaCGM()
{
    global $request;
    $obRCGM = new RCGM;

    $stText = "inNumCGM";
    $stSpan = "inNomCGM";
    if ( $request->get( $stText ) != "" ) {
        $obRCGM->setNumCGM( $request->get( $stText ) );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";

        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor invï¿½lido. (".$request->get( $stText ).")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}
function montaListaHorarioAtividade($rsListaDias, $rsListaAtividades, $boRetorna = false)
{
    if ( $rsListaDias->getNumLinhas() > 0 ) {
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
        $obLista->ultimaAcao->addCampo         ( "inIndice","inDia"   );
        $obLista->commitAcao                   (                      );

        $obLista->montaHTML                    (                      );
        $stHTML =  $obLista->getHtml           (                      );
        $stHTML = str_replace                  ( "\n","",$stHTML      );
        $stHTML = str_replace                  ( "  ","",$stHTML      );
        $stHTML = str_replace                  ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }
    $js .= "d.getElementById('spnListaHorario').innerHTML = '".$stHTML."';\n";

    //MONTA LISTA ATIVIDADES
    if ( $rsListaAtividades->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaAtividades     );
        $obLista->setTitulo                    ( "Lista de Atividades"  );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Cï¿½digo"               );
        $obLista->ultimoCabecalho->setWidth    ( 15                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                 );
        $obLista->ultimoCabecalho->setWidth    ( 75                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "cod_atividade"        );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "nom_atividade"        );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirAtividade();" );
        $obLista->ultimaAcao->addCampo     ( "inIndice","cod_atividade" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTMLAtiv =  $obLista->getHtml       (                        );
        $stHTMLAtiv = str_replace              ( "\n","",$stHTMLAtiv    );
        $stHTMLAtiv = str_replace              ( "  ","",$stHTMLAtiv    );
        $stHTMLAtiv = str_replace              ( "'","\\'",$stHTMLAtiv  );
    } else {
        $stHTML = "&nbsp";
    }
    $js .= "d.getElementById('spnListaAtividade').innerHTML = '".$stHTMLAtiv."';\n";

    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}

function montaListaHorario($rsListaDias, $boRetorna = false)
{
    if ( $rsListaDias->getNumLinhas() > 0 ) {
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
        $obLista->ultimaAcao->addCampo         ( "inIndice","inDia"   );
        $obLista->commitAcao                   (                      );

        $obLista->montaHTML                    (                      );
        $stHTML =  $obLista->getHtml           (                      );
        $stHTML = str_replace                  ( "\n","",$stHTML      );
        $stHTML = str_replace                  ( "  ","",$stHTML      );
        $stHTML = str_replace                  ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }
    $js = "d.getElementById('spnListaHorario').innerHTML = '".$stHTML."';\n";

    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}

function montaListaAtividade($rsListaAtividades, $boRetorna = false)
{
    if ( $rsListaAtividades->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaAtividades     );
        $obLista->setTitulo                    ( "Lista de Atividades"  );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Código"               );
        $obLista->ultimoCabecalho->setWidth    ( 10                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Nome"                 );
        $obLista->ultimoCabecalho->setWidth    ( 51                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data Início"          );
        $obLista->ultimoCabecalho->setWidth    ( 12                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data Término"         );
        $obLista->ultimoCabecalho->setWidth    ( 12                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Principal"	        );
        $obLista->ultimoCabecalho->setWidth    ( 5                     );
        $obLista->commitCabecalho              (                        );

        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "cod_atividade"        );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "nom_atividade"        );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( "CENTRO"				);
        $obLista->ultimoDado->setCampo         ( "dt_inicio"            );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( "CENTRO"				);
        $obLista->ultimoDado->setCampo         ( "dt_termino"           );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( "CENTRO"				);
        $obLista->ultimoDado->setCampo         ( "principal_sn"         );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirAtividade();" );
        $obLista->ultimaAcao->addCampo     ( "inIndice","cod_atividade" );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp";
    }
    $js = "d.getElementById('spnListaAtividade').innerHTML = '".$stHTML."';\n";
    if ($boRetorna) {
        return $js;
    } else {
        sistemaLegado::executaFrameOculto($js);
    }
}
switch ( $request->get("stCtrl")) {
    case "buscaLicencaComponente":
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTConfiguracao->setDado ( 'cod_modulo', 14 );
        $obTConfiguracao->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTConfiguracao->setDado ( 'parametro', 'numero_licenca' );
        $obTConfiguracao->recuperaPorChave ( $rsNumeroLicenca );

        $inNumeroLicenca = $rsNumeroLicenca->getCampo('valor');

        $obTConfiguracao->setDado ( 'parametro', 'mascara_licenca' );
        $obTConfiguracao->recuperaPorChave ( $rsMascaraLicenca );
        $contNumLicenca = strlen ( $rsMascaraLicenca->getCampo('valor') );

        $i = 0;
        $stMascara = null;
        while ($i < $contNumLicenca) {
            $stMascara .= "9";
            $i++;
        }

        if ( $contNumLicenca <= 0 )
            $stMascara .= "9";

        if ( $inNumeroLicenca != 0 )
            $stMascara .= '/9999';

        $inTamanho = strlen ( $stMascara )-5;
        $stDados = str_replace( "/", "", $_GET[ $_GET["stNomCampoCod"] ] );
        $inTamanhoOrigem = strlen( $stDados );
        if ($inTamanhoOrigem >= 5) {
            $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
            $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );

            $stValor = "";
            $inTamanhoOrigem -= 4;
            for ($inX=0; $inX<$inTamanho-$inTamanhoOrigem; $inX++) {
                $stValor .= "0";
            }

            $stValor .= $stCodigo;
            $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "'.$stValor.'/'.$stAno.'";';
            echo $stJs;
        } else {
            $boErro= true;
        }

        break;

    case "buscaInscricao":
    $js = '';
    $js2 = '';
    if ( $request->get("inInscricaoEconomica") ) {
        $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
        $obRCEMInscricaoEconomica->setInscricaoEconomica($request->get("inInscricaoEconomica"));
        $obRCEMInscricaoEconomica->consultarNomeInscricaoEconomica($rsInscricao);

        if ( $rsInscricao->getNumLinhas() > 0 ) {
            $stNomCgm = str_replace ( "'", "\'", $rsInscricao->getCampo("nom_cgm") );
            $js .= "f.inInscricaoEconomica.value = '".$request->get("inInscricaoEconomica")."';\n";
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML='".$stNomCgm."';\n";
            $stAcao = $request->get('stAcao');
            if ($stAcao == 'incEsp' || $stAcao == 'incAtiv') {
                $js .= "d.getElementById('spnListaAtividade').innerHTML = '';\n";
                $obMontaAtividade = new MontaAtividade;
                $obMontaAtividade->setInscricaoEconomica ($request->get("inInscricaoEconomica"));
                $obMontaAtividade->geraFormularioRestrito( $js2, "cmbAtividade");
            }

            Sessao::write( "atividades", array() );
        } else {

            $stMsg = "Inscrição Econômica ".$request->get("inInscricaoEconomica")."  não encontrada!";

            $js = "f.inInscricaoEconomica.value = '';\n";
            $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
            $js .= "f.inInscricaoEconomica.focus();\n";

            $js .= "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";

        }
    } else {
        $js = "f.inInscricaoEconomica.value = '';\n";
        $js .= "d.getElementById('stInscricaoEconomica').innerHTML= '&nbsp;' ;\n";
    }

    sistemaLegado::executaFrameOculto($js);
    sistemaLegado::executaFrameOculto($js2);
    break;
    case "buscaProcesso":
        $obRCEMLicenca = new RCEMLicenca;
        $js = "";
        if ( $request->get("inCodigoProcesso") ) {
            $arProcesso = explode("/",$request->get("inCodigoProcesso"));
            $obRCEMLicenca->obRProcesso->setCodigoProcesso($arProcesso[0]);
            $obRCEMLicenca->obRProcesso->consultarProcesso($arProcesso[1]);
            $anoExercicio = $obRCEMLicenca->obRProcesso->getExercicio();
            $inCodProcesso = $obRCEMLicenca->obRProcesso->getCodigoProcesso();
            if ($anoExercicio) {
                $js .= "f.hdnExercicioProcesso.value = '".$anoExercicio."';\n";
                $js .= "d.getElementById('stNomProcesso').innerHTML = '".$inCodProcesso."/".$anoExercicio."';\n";
            } else {
                $stMsg = "Processo ".$request->get("inCodigoProcesso")." não cadastrado! ";
                $js = "alertaAviso('@".$stMsg."','form','erro','".Sessao::getId()."');";
                $js .= "f.inCodigoProcesso.value = '';\n";
                $js .= "f.inCodigoProcesso.focus();\n";
            }
        }
        sistemaLegado::executaFrameOculto($js);
    break;
    case "incluirHorario":
        $obRCEMLicenca = new RCEMLicenca;
        $rsDiasSemana  = new RecordSet;
        $obRCEMLicenca->listarDiasSemana( $rsDiasSemana );
        $arHorariosSessao = Sessao::read( "horarios" );
        while ( !$rsDiasSemana->eof() ) {
            $stDia = $rsDiasSemana->getCampo("nom_dia");
            $inDia = $rsDiasSemana->getCampo("cod_dia");
            $aval = "dia".$inDia;
            if ( $request->get("$aval") ) {
                $arDiasSemana["inDia"]  = $inDia;
                $arDiasSemana["stDia"]  = $stDia;
                $arDiasSemana["hrInicio"]  = $request->get("hrHorarioInicio");
                $arDiasSemana["hrTermino"] = $request->get("hrHorarioTermino");
                $stInsere = false;
                if ($arHorariosSessao) {
                    $inCountSessao = count ( $arHorariosSessao );
                } else {
                    $inCountSessao = 0;
                    $stInsere = true;
                }

                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    if ($arHorariosSessao[$iCount]["inDia"] == $arDiasSemana["inDia"]) {
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } else {
                        $stInsere = true;
                    }
                }
                if ($stInsere) {
                    if ($arHorariosSessao) {
                        $inLast = count ($arHorariosSessao);
                    } else {
                        $inLast = 0;
                        $arHorariosSessao = array ();
                    }

                    $arHorariosSessao[$inLast]["inDia"     ] = $arDiasSemana["inDia"     ];
                    $arHorariosSessao[$inLast]["stDia"     ] = $arDiasSemana["stDia"     ];
                    $arHorariosSessao[$inLast]["hrInicio"  ] = $arDiasSemana["hrInicio"  ];
                    $arHorariosSessao[$inLast]["hrTermino" ] = $arDiasSemana["hrTermino" ];
                }
            }
            $rsDiasSemana->proximo();
        }

        Sessao::write( "horarios", $arHorariosSessao );
        $rsListaDias = new RecordSet;
        $rsListaDias->preenche ( $arHorariosSessao );
        $rsListaDias->ordena("inDia");
        montaListaHorario ( $rsListaDias );
        exit (0);
    break;
    case "excluirHorario":
        $arTmpHorarios = array ();
        $arHorariosSessao = Sessao::read( "horarios" );
        $inCountSessao = count( $arHorariosSessao );
        $inCountArray = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ( $arHorariosSessao[$inCount][ "inDia" ] != $request->get( "inIndice" )) {
                $arTmpHorarios[$inCountArray]["inDia"]     = $arHorariosSessao[$inCount][ "inDia" ];
                $arTmpHorarios[$inCountArray]["stDia"]     = $arHorariosSessao[$inCount][ "stDia" ];
                $arTmpHorarios[$inCountArray]["hrInicio"]  = $arHorariosSessao[$inCount][ "hrInicio" ];
                $arTmpHorarios[$inCountArray]["hrTermino"] = $arHorariosSessao[$inCount][ "hrTermino" ];
                $inCountArray++;
            }
        }

        Sessao::write( "horarios", $arTmpHorarios );
        $rsListaDias = new RecordSet;
        $rsListaDias->preenche ( $arTmpHorarios );
        $rsListaDias->ordena("inDia");
        montaListaHorario ( $rsListaDias );
        exit (0);
    break;
    case "recuperaHorario":
        $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
        $obRCEMLicenca  = new RCEMLicenca;
        $rsHorarios     = new RecordSet;
        $rsDiasSemana   = new RecordSet;
        $arStDiasSemana = array();

        $countDias = 1;
        $obRCEMLicenca->listarDiasSemana( $rsDiasSemana );
        while ( !$rsDiasSemana->eof() ) {
            $arStDiasSemana[ $countDias ] = $rsDiasSemana->getCampo("nom_dia");
            $countDias++;
            $rsDiasSemana->proximo();
        }
        $obRCEMLicenca->setCodigoLicenca( $newLicenca[0] );
        $obRCEMLicenca->setExercicio    ( $newLicenca[1] );
        $obRCEMLicenca->consultarHorarios( $rsHorarios );
        $rsHorarios->ordena("cod_dia");
        $arHorariosSessao = Sessao::read( "horarios" );
        while ( !$rsHorarios->eof() ) {
            $rsDia     = $rsHorarios->getCampo("cod_dia");
            $rsInicio  = $rsHorarios->getCampo("hr_inicio");
            $rsTermino = $rsHorarios->getCampo("hr_termino");
            if ($inDia = $rsDia) {
                $arDiasSemana["inDia"]  = $rsDia;
                $arDiasSemana["stDia"]  = $arStDiasSemana[$rsDia];
                $arDiasSemana["hrInicio"]  = $rsInicio;
                $arDiasSemana["hrTermino"] = $rsTermino;
                $stInsere = false;
                if ($arHorariosSessao) {
                    $inCountSessao = count ($arHorariosSessao);
                } else {
            $inCountSessao = 0;
            $stInsere = true;
                }
                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    if ($arHorariosSessao[$iCount]["inDia"] == $arDiasSemana["inDia"]) {
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } else {
                        $stInsere = true;
                    }
                }
                if ($stInsere) {
                    if ($arHorariosSessao) {
                        $inLast = count ($arHorariosSessao);
                    } else {
                        $inLast = 0;
                        $arHorariosSessao = array ();
                    }

                    $arHorariosSessao[$inLast]["inDia"     ] = $arDiasSemana["inDia"     ];
                    $arHorariosSessao[$inLast]["stDia"     ] = $arDiasSemana["stDia"     ];
                    $arHorariosSessao[$inLast]["hrInicio"  ] = $arDiasSemana["hrInicio"  ];
                    $arHorariosSessao[$inLast]["hrTermino" ] = $arDiasSemana["hrTermino" ];
                }
            }
            $rsHorarios->proximo();
        }

        Sessao::write( "horarios", $arHorariosSessao );
        $rsListaDias = new RecordSet;
        $rsListaDias->preenche ( $arHorariosSessao );
        $rsListaDias->ordena("inDia");
        montaListaHorario ( $rsListaDias );
        exit (0);
    break;
    case "incluirAtividade":
        $stTmp  = 'inCodAtividade_';
        $stTmp .= ($request->get('inNumNiveis') - 1 );
        //$arNewAtividade = preg_split( "/[^a-zA-Z0-9]/",  $$stTmp );
        $arNewAtividade = $request->get("cmbAtividade") ;

        $obRCEMLicencaEspecial    = new RCEMLicencaEspecial;
        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
        $obRCEMInscricaoAtividade->addAtividade();
        $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $arNewAtividade );
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica($request->get("inInscricaoEconomica"));
        $obRCEMInscricaoAtividade->listarAtividadesInscricao( $rsAtividades );
        $arAtividadeSessao = Sessao::read( "atividades" );
        if ( $rsAtividades->getNumLinhas() > 0 ) {
            while ( !$rsAtividades->eof() ) {
                $stInsere = false;
                if ($arAtividadeSessao) {
                    $inCountSessao = count ($arAtividadeSessao);
                } else {
                    $inCountSessao = 0;
                    $stInsere = true;
                }
                for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                    if ($arAtividadeSessao[$iCount]["cod_atividade"] == $rsAtividades->getCampo("cod_atividade")) {
                        $stInsere = false;
                        $iCount = $inCountSessao;
                    } else {
                        $stInsere = true;
                    }
                }
                if ($stInsere) {
                    if ($arAtividadeSessao) {
                        $inLast = count ($arAtividadeSessao);
                    } else {
                        $inLast = 0;
                        $arAtividadeSessao = array ();
                    }

                    $arAtividadeSessao[$inLast]["inscricao_economica" ] = $rsAtividades->getCampo("inscricao_economica" );
                    $arAtividadeSessao[$inLast]["cod_atividade"       ] = $rsAtividades->getCampo("cod_atividade"       );
                    $arAtividadeSessao[$inLast]["nom_atividade"       ] = $rsAtividades->getCampo("nom_atividade"       );
                    $arAtividadeSessao[$inLast]["ocorrencia_atividade"] = $rsAtividades->getCampo("ocorrencia_atividade");
                    $arAtividadeSessao[$inLast]["principal"           ] = $rsAtividades->getCampo("principal"           );
                    if ( $rsAtividades->getCampo("principal" ) == 't' )
                        $arAtividadeSessao[$inLast]["principal_sn" ] = 's';
                    else
                        $arAtividadeSessao[$inLast]["principal_sn" ] = 'n';

                    $arAtividadeSessao[$inLast]["dt_inicio"           ] = $request->get( "dtDataInicio" );
                    $arAtividadeSessao[$inLast]["dt_termino"          ] = $request->get( "dtDataTermino" );
                }

                $rsAtividades->proximo();
            }

            Sessao::write( "atividades", $arAtividadeSessao );
        } else {
            $obErro = new Erro;
            $obErro->setDescricao("A Inscrição Econômica ".$request->get("inInscricaoEconomica")." não possui a atividade informada!");
            $js .= "alertaAviso('@".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto( $js );
            exit (0);
        }

        $rsListaAtividades = new RecordSet;
        $rsListaAtividades->preenche ( $arAtividadeSessao );
        $rsListaAtividades->ordena("cod_atividade");
        montaListaAtividade ( $rsListaAtividades );
        exit (0);
    break;
    case "excluirAtividade":
        $arTmpAtividade = array ();
        $arAtividadeSessao = Sessao::read( "atividades" );
        $inCountSessao = count ($arAtividadeSessao);
        $inCountArray = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arAtividadeSessao[$inCount][ "cod_atividade" ] != $request->get("inIndice")) {
                $arTmpAtividade[$inCountArray]["inscricao_economica"]  = $arAtividadeSessao[$inCount][ "inscricao-economica"  ];
                $arTmpAtividade[$inCountArray]["cod_atividade"]        = $arAtividadeSessao[$inCount][ "cod_atividade"        ];
                $arTmpAtividade[$inCountArray]["nom_atividade"]        = $arAtividadeSessao[$inCount][ "nom_atividade"        ];
                $arTmpAtividade[$inCountArray]["ocorrencia_atividade"] = $arAtividadeSessao[$inCount][ "ocorrencia_atividade" ];
                $arTmpAtividade[$inCountArray]["principal"]            = $arAtividadeSessao[$inCount][ "principal"            ];
                $arTmpAtividade[$inCountArray]["principal_sn"]         = $arAtividadeSessao[$inCount][ "principal" ]?'s':'n';
                $arTmpAtividade[$inCountArray]["dt_inicio"]            = $arAtividadeSessao[$inCount][ "dt_inicio"            ];
                $arTmpAtividade[$inCountArray]["dt_termino"]           = $arAtividadeSessao[$inCount][ "dt_termino"           ];
                $inCountArray++;
            }
        }

        Sessao::write( "atividades", $arTmpAtividade );
        $rsListaAtividades = new RecordSet;
        $rsListaAtividades->preenche ( $arTmpAtividade );
        $rsListaAtividades->ordena("cod_atividade");
        montaListaAtividade( $rsListaAtividades );
        exit (0);
    break;
    case "recuperaAtividades":
        $rsAtividades = new RecordSet;
        $newLicenca   = explode ( "/" , $request->get("inCodigoLicenca") );
        if ( $request->get("stEspecieLicenca") == "Atividade" ) {
            $obRCEMLicencaAtividade = new RCEMLicencaAtividade();
            $obRCEMLicencaAtividade->setCodigoLicenca   ( $newLicenca[0] );
            $obRCEMLicencaAtividade->setExercicio       ( $newLicenca[1] );
            $obRCEMLicencaAtividade->setOcorrenciaLicenca  ( $request->get("inOcorrenciaLicenca") );
            $obRCEMLicencaAtividade->consultarAtividades( $rsAtividades  );
        } elseif ( $request->get("stEspecieLicenca") == "Especial" ) {
            $obRCEMLicencaEspecial = new RCEMLicencaEspecial();
            $obRCEMLicencaEspecial->setCodigoLicenca   ( $newLicenca[0] );
            $obRCEMLicencaEspecial->setExercicio       ( $newLicenca[1] );
            $obRCEMLicencaEspecial->consultarAtividades( $rsAtividades  );
        }
        $rsAtividades->ordena("cod_atividade");
        while ( !$rsAtividades->eof() ) {
            $arAtividadeSessao = Sessao::read( "atividades" );
            $stInsere = false;
            if ($arAtividadeSessao) {
                $inCountSessao = count ( $arAtividadeSessao );
            } else {
                $inCountSessao = 0;
                $stInsere = true;
            }
            for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                if ( $arAtividadeSessao[$iCount]["cod_atividade"] == $rsAtividades->getCampo("cod_atividade")) {
                    $stInsere = false;
                    $iCount = $inCountSessao;
                } else {
                    $stInsere = true;
                }
            }
            if ($stInsere) {
                if ($arAtividadeSessao) {
                    $inLast = count ( $arAtividadeSessao );
                } else {
                    $inLast = 0;
                    $arAtividadeSessao = array ();
                }

                $arAtividadeSessao[$inLast]["inscricao_economica" ] = $rsAtividades->getCampo("inscricao_economica" );
                $arAtividadeSessao[$inLast]["cod_atividade"       ] = $rsAtividades->getCampo("cod_atividade"       );
                $arAtividadeSessao[$inLast]["nom_atividade"       ] = $rsAtividades->getCampo("nom_atividade"       );
                $arAtividadeSessao[$inLast]["ocorrencia_atividade"] = $rsAtividades->getCampo("ocorrencia_atividade");
                $arAtividadeSessao[$inLast]["principal"           ] = $rsAtividades->getCampo("principal"           );
                if ( $rsAtividades->getCampo("principal") == 't' )
                    $arAtividadeSessao[$inLast]["principal_sn"    ] = 's';
                else
                    $arAtividadeSessao[$inLast]["principal_sn"    ] = 'n';

                $arAtividadeSessao[$inLast]["dt_inicio"           ] = $rsAtividades->getCampo("dt_inicio_atividade" );
                $arAtividadeSessao[$inLast]["dt_termino"          ] = $rsAtividades->getCampo("dt_termino_atividade");
            }
            $rsAtividades->proximo();
        }

        Sessao::write( "atividades", $arAtividadeSessao );
        if ( $request->get("stEspecieLicenca") == "Especial" ) {
            $newLicenca = explode ( "/" , $request->get("inCodigoLicenca") );
            $obRCEMLicenca  = new RCEMLicenca;
            $rsHorarios     = new RecordSet;
            $rsDiasSemana   = new RecordSet;
            $arStDiasSemana = array();

            $countDias = 1;
            $obRCEMLicenca->listarDiasSemana( $rsDiasSemana );
            while ( !$rsDiasSemana->eof() ) {
                $arStDiasSemana[ $countDias ] = $rsDiasSemana->getCampo("nom_dia");
                $countDias++;
                $rsDiasSemana->proximo();
            }
            $obRCEMLicenca->setCodigoLicenca( $newLicenca[0] );
            $obRCEMLicenca->setExercicio    ( $newLicenca[1] );
            $obRCEMLicenca->consultarHorarios( $rsHorarios );
            $rsHorarios->ordena("cod_dia");

            while ( !$rsHorarios->eof() ) {
                $rsDia     = $rsHorarios->getCampo("cod_dia");
                $rsInicio  = $rsHorarios->getCampo("hr_inicio");
                $rsTermino = $rsHorarios->getCampo("hr_termino");
                if ($inDia = $rsDia) {
                    $arDiasSemana["inDia"]  = $rsDia;
                    $arDiasSemana["stDia"]  = $arStDiasSemana[$rsDia];
                    $arDiasSemana["hrInicio"]  = $rsInicio;
                    $arDiasSemana["hrTermino"] = $rsTermino;
                    $stInsere = false;
                    $arHorariosSessao = Sessao::read( "horarios" );
                    if ($arHorariosSessao) {
                        $inCountSessao = count ( $arHorariosSessao );
                    } else {                                                                                                                        $inCountSessao = 0;
                        $stInsere = true;
                    }

                    for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
                        if ($arHorariosSessao[$iCount]["inDia"] == $arDiasSemana["inDia"]) {
                            $stInsere = false;
                            $iCount = $inCountSessao;
                        } else {
                            $stInsere = true;
                        }
                    }
                    if ($stInsere) {
                        if ($arHorariosSessao) {
                            $inLast = count ( $arHorariosSessao );
                        } else {
                            $inLast = 0;
                            $arHorariosSessao = array ();
                        }

                        $arHorariosSessao[$inLast]["inDia"     ] = $arDiasSemana["inDia"     ];
                        $arHorariosSessao[$inLast]["stDia"     ] = $arDiasSemana["stDia"     ];
                        $arHorariosSessao[$inLast]["hrInicio"  ] = $arDiasSemana["hrInicio"  ];
                        $arHorariosSessao[$inLast]["hrTermino" ] = $arDiasSemana["hrTermino" ];
                    }
                }
                $rsHorarios->proximo();
            }

            Sessao::write( "horarios", $arHorariosSessao );

            $rsListaDias = new RecordSet;
            $rsListaDias->preenche ( $arHorariosSessao );
            $rsListaDias->ordena("inDia");

            $rsListaAtividades = new RecordSet;
            $rsListaAtividades->preenche ( Sessao::read( "atividades" ) );
            $rsListaAtividades->ordena("cod_atividade");

            montaListaHorarioAtividade ( $rsListaDias , $rsListaAtividades );

        } else {
            $rsListaAtividades = new RecordSet;
            $rsListaAtividades->preenche ( Sessao::read( "atividades" ) );
            $rsListaAtividades->ordena("cod_atividade");
            montaListaAtividade ( $rsListaAtividades );
        }
        exit (0);
    break;
    case "preencheProxCombo":
        $obMontaAtividade       = new MontaAtividade;
        $stNomeComboAtividade = "inCodAtividade_".( $request->get("inPosicao") - 1);
        $stChaveLocal = $request->get($stNomeComboAtividade);
        $inPosicao = $request->get("inPosicao");
        if ( empty( $stChaveLocal ) and $request->get("inPosicao") > 2 ) {
            $stNomeComboAtividade = "inCodigoAtividade_".( $request->get("inPosicao") - 2);
            $stChaveLocal = $request->get($stNomeComboAtividade);
            $inPosicao = $request->get("inPosicao") - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $request->get("inCodigoVigencia") );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaAtividade->preencheProxCombo( $inPosicao , $request->get("inNumNiveis") );
    break;
    case "preencheCombos":
        $obMontaAtividade       = new MontaAtividade;
        $obMontaAtividade->setCadastroAtividade( false );
        $obMontaAtividade->setCodigoVigencia( $request->get("inCodigoVigencia") );
        $obMontaAtividade->setCodigoNivel   ( $request->get("inCodigoNivel")    );
        $obMontaAtividade->setValorReduzido ( $request->get("stChaveAtividade") );
        $obMontaAtividade->preencheCombosAtividade();
    break;
    case "limparSessaoAtividade":
        $obMontaAtividade = new MontaAtividade;
        $stJs = $obMontaAtividade->geraLimpaComboAtividade("cmbAtividade");
        sistemaLegado::executaFrameOculto($stJs);
        Sessao::write( 'atividades', array() );
    break;
    case "limparSessaoEspecial":
        $obMontaAtividade = new MontaAtividade;
        $stJs = $obMontaAtividade->geraLimpaComboAtividade("cmbAtividade");
        sistemaLegado::executaFrameOculto($stJs);
        Sessao::write( 'atividades', array() );
        Sessao::write( 'horarios', array() );
    break;

}

?>

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
    * Página Oculto da Consulta de Cadastro Economico
    * Data de Criação   : 16/09/2005

    * @author Marcelo B. Paulino
    * @ignore

    * $Id: OCConsultarCadastroEconomico.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.21
*/

/*
$Log$
Revision 1.21  2007/04/23 15:49:18  dibueno
Finalizado arquivo com '?>'

Revision 1.20  2007/03/15 14:26:24  cercato
alterando formulario para apresentar a situacao na lista de licencas.

Revision 1.19  2007/03/05 13:12:11  dibueno
Bug #7676#

Revision 1.18  2007/03/02 14:45:34  dibueno
Bug #7676#

Revision 1.17  2006/11/22 10:42:44  cercato
bug #7355#

Revision 1.16  2006/11/20 16:08:31  cercato
bug #7438#

Revision 1.15  2006/11/20 13:13:04  dibueno
Bug #7519#

Revision 1.14  2006/11/20 09:54:18  cercato
bug #7438#

Revision 1.13  2006/11/13 12:57:35  cercato
bug #7355#

Revision 1.12  2006/09/15 14:32:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php"  );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"           );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"  );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"        );
include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"      );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaAtividade.class.php"    );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicencaEspecial.class.php"     );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeInscricao.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"            );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"      );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarCadastroEconomico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function BuscaCGM()
{
    global $_REQUEST;

    if ($_REQUEST[ 'inCodigoEnquadramento' ] == 2) {
        $obRCGM = new RCGMPessoaJuridica;
    } elseif ($_REQUEST[ 'inCodigoEnquadramento' ] == "") {
        $obRCGM = new RCGM;
    } else {
        $obRCGM = new RCGMPessoaFisica;
    }

    $stText = "inNumCGM";
    $stSpan = "stNomCGM";

    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        if ($_REQUEST[ 'inCodigoEnquadramento' ] != "") {
            $obRCGM->consultarCGM( $rsCGM );
        } else {
            $obRCGM->consultar( $rsCGM );
        }
        $stNull = "&nbsp;";
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function BuscaSocio()
{
    global $_REQUEST;
    $obRCGM = new RCGM;

    $stText = "inCodigoSocio";
    $stSpan = "stNomeSocio";

    if ($_REQUEST[ $stText ] != "") {
        $obRCGM->setNumCGM( $_REQUEST[ $stText ] );
        $obRCGM->consultar( $rsCGM );
        $stNull = "&nbsp;";
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function BuscaDomicilio()
{
    global $_REQUEST;
    $obRCIMImovel = new RCIMImovel( new RCIMLote );
    $stText = "inCodigoDomicilio";
    if ($_REQUEST[ $stText ] != "") {
        $obRCIMImovel->setNumeroInscricao( $_REQUEST[ $stText ] );
        $obRCIMImovel->addProprietario();
        $obRCIMImovel->listarImoveisConsulta( $rsImovel );
        $stNull = "&nbsp;";
        if ( $rsImovel->getNumLinhas() <= 0 ) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido para domicilio fiscal.(".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
            $stJs .= 'f.'.$stText.'.focus();';
        } else {
            $stJs .= 'f.inCodigoDomicilio.value = '.$rsImovel->getCampo('inscricao_municipal').';';

            $stEndereco = $rsImovel->getCampo('logradouro');
            if ( $rsImovel->getCampo('numero') != "" ) {
                $stEndereco .= ", ".$rsImovel->getCampo('numero');
            }
            if ( $rsImovel->getCampo('complemento') != "" ) {
                $stEndereco .= " - ".$rsImovel->getCampo('complemento');
            }
            $stJs .= 'd.getElementById("stEndereco").innerHTML = "'.$stEndereco.'";';
        }
    } else {
        $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
    }

    return $stJs;
}
function BuscaNatureza()
{
    global $_REQUEST;
    $obRCEMNaturezaJuridica = new RCEMNaturezaJuridica;

    $stText = "inCodigoNatureza";
    $stSpan = "stNomeNatureza";
    if ($_REQUEST[ $stText ] != "") {
        $inCodNatureza = str_replace("-","",$_REQUEST[ $stText ]);
        $obRCEMNaturezaJuridica->setCodigoNatureza( $inCodNatureza);
        $obRCEMNaturezaJuridica->listarNaturezaJuridica( $rsNatureza );
        $stNull = "&nbsp;";

        if ( $rsNatureza->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
           $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsNatureza->getCampo('nom_natureza')?$rsNatureza->getCampo('nom_natureza'):$stNull).'";';
        }
    } else {
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "&nbsp;";';
    }

    return $stJs;
}

function montaListaSocios($rsListaSocios)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsListaSocios );
    $obLista->setTitulo ("Listas de Sócios");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 65 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quota" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNumCGM" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomeCGM" );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "flQuota" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaSocios').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaHorarios($rsListaHorarios)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsListaHorarios );
    $obLista->setTitulo ("Lista de Horários");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Dia da Semana");
    $obLista->ultimoCabecalho->setWidth( 41 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Horário de Início" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Horário de Término" );
    $obLista->ultimoCabecalho->setWidth( 27 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomDia" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "hrInicio" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "hrTermino" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaHorarios').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaRespTecnico($rsRespTecnico)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsRespTecnico );
    $obLista->setTitulo ("Listas de Responsáveis Técnicos");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("CGM");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Profissão" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Registro Profissional" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNumCGM" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomCGM" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stProfissao" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stRegistro" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaRespTecnico').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaProcessos($rsAtividades)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAtividades );
    $obLista->setTitulo ("Listas de Processos");

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Processo");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Hora" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "&nbsp;" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_processo]/[ano_exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stData" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stHora" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:visualizarProcesso();" );
    $obLista->ultimaAcao->addCampo("1", "cod_processo");
    $obLista->ultimaAcao->addCampo("2", "ano_exercicio");
    $obLista->ultimaAcao->addCampo("3", "ocorrencia_atividade");
    $obLista->ultimaAcao->addCampo("4", "inscricao_economica");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaProcessos').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaAtividadesPorProcesso($rsAtividades)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAtividades );
    $obLista->setTitulo ("Listas de Atividades");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 60 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Principal" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_atividade" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_atividade" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "principal" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:visualizarAtividade( 'visualizarAtividadeProcesso' );" );
    $obLista->ultimaAcao->addCampo("1","cod_atividade");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('spnVisualizarProcesso').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaAtividades($rsAtividades)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsAtividades );
    $obLista->setTitulo ("Listas de Atividades");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 55 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Principal" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stCodigoEstrutural" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNome" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "boPrincipal" );
    $obLista->ultimoDado->setAlinhamento( 'CENTER' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:visualizarAtividade( 'visualizarAtividade' );" );
    $obLista->ultimaAcao->addCampo("1","inCodigo");
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaAtividades').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaElementos($rsElementos)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsElementos );
    $obLista->setTitulo ("Listas de Elementos");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 55 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodigo" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNome" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink ( "JavaScript:visualizarElemento( 'visualizarElemento' );" );
    $obLista->ultimaAcao->addCampo( "1","inCodigo" );
    $obLista->ultimaAcao->addCampo( "2","stNome" );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaElementos').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaLicencas($rsLicencas)
{
    $table = new Table();
    $table->setRecordset( $rsLicencas );
    $table->setSummary('Lista de Licenças');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Exercício' , 10 );
    $table->Head->addCabecalho( 'Código'    , 10 );
    $table->Head->addCabecalho( 'Espécie'   , 30 );
    $table->Head->addCabecalho( 'Modelo'    , 35 );
    $table->Head->addCabecalho( 'Processo'  , 15 );
    $table->Head->addCabecalho( 'Situação'  , 15 );

    $table->Body->addCampo( 'inExercicio'  , "C", $stTitleLanc );
    $table->Body->addCampo( 'inCodigo'  , "C", $stTitleLanc );
    $table->Body->addCampo( 'stEspecie' , "E", $stTitleLanc );
    $table->Body->addCampo( 'nome_documento' , "E", $stTitleLanc );
    $table->Body->addCampo( 'stProcesso', "C", $stTitleLanc );
    $table->Body->addCampo( 'stSituacao', "C", $stTitleLanc );

    $table->Body->addAcao( 'CONSULTAR', 'visualizarLicenca( %s, %s, %s )' , array( 'visualizarLicenca' , 'inCodigo' , 'stEspecie') );

    $table->Body->addAcao( 'IMPRIMIR', 'imprimirLicenca( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )' , array( 'Download', 'inInscricaoEconomica', 'inCodigo', 'inCodDocumento', 'inCodTipoDocumento', 'inExercicio', 'stEspecie', 'nome_arquivo_template', 'nome_documento', 'stSituacao' ) );

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);
    $stJs = "d.getElementById('lsListaLicencas').innerHTML = '".$stHTML."';";

    return $stJs;
}

function montaListaAtividadesLicenca($rsAtividadesLicenca)
{
    $table = new Table();
    $table->setRecordset( $rsAtividadesLicenca );
    $table->setSummary('Lista de Atividades');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Código' , 15 );
    $table->Head->addCabecalho( 'Nome'   , 85 );

    $table->Body->addCampo( 'cod_atividade' , "C", $stTitleLanc );
    $table->Body->addCampo( 'nom_atividade' , "E", $stTitleLanc );

    $table->montaHTML();

    $stHTML = $table->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    return $stHTML;
}

function montaListaHorariosLicenca($rsAtividadesLicenca)
{
    $table = new Table();
    $table->setRecordset( $rsAtividadesLicenca );
    $table->setSummary('Lista de Atividades');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Dia da Semana'     , 20 );
    $table->Head->addCabecalho( 'Horário de Início' , 40 );
    $table->Head->addCabecalho( 'Horário de Término', 40 );

    $table->Body->addCampo( 'nom_dia'   , "C", $stTitleLanc );
    $table->Body->addCampo( 'hr_inicio' , "C", $stTitleLanc );
    $table->Body->addCampo( 'hr_termino', "C", $stTitleLanc );

    $table->montaHTML();

    $stHTML = $table->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    return $stHTML;
}

function montaListaModalidades($rsModalidades)
{
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsModalidades );
    $obLista->setTitulo ("Listas de Modalidades");
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Código");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodigo" );
    $obLista->ultimoDado->setAlinhamento( 'RIGHT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stModalidade" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink ( "JavaScript:visualizarLicenca( 'visualizarModalidade' );" );
    $obLista->ultimaAcao->addCampo( "1","inCodigo" );
    $obLista->ultimaAcao->addCampo( "2","stEspecie" );
    $obLista->commitAcao();

    $obLista->montaHTML();

    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('lsListaModalidades').innerHTML = '".$stHTML."';";

    return $stJs;
}

$boVisualizarProcesso = false;
switch ($_REQUEST["stCtrl"]) {
    case "visualizarProcesso":
        $inCodProcesso = $_REQUEST["inCodProcesso"];
        $inAnoExercicio = $_REQUEST["inAnoExercicio"];
        $inOcorrenciaAtividade = $_REQUEST["inOcorrenciaAtividade"];
        $inInscricaoEconomica = $_REQUEST["inInscricaoEconomica"];

        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $inInscricaoEconomica );
        $obRCEMInscricaoAtividade->obRCEMElemento->roCEMAtividade->setOcorrenciaAtividade( $inOcorrenciaAtividade );
        $obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsListaAtividades );
        while ( !$rsListaAtividades->Eof() ) {
            $rsListaAtividades->setCampo( "principal", $rsListaAtividades->getCampo( "principal" ) == 't' ? 'sim':'não' );
            $rsListaAtividades->proximo();
        }

        $rsListaAtividades->setPrimeiroElemento();

        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo" );
        $obLblProcesso->setValue ( $inCodProcesso."/".$inAnoExercicio );

        $obFormularioProcesso = new Formulario;
        $obFormularioProcesso->addComponente( $obLblProcesso );

        $obFormularioProcesso->montaInnerHTML();
        $stHtml = $obFormularioProcesso->getHTML();

        $stJs = "d.getElementById('spnVisualizarProcesso').innerHTML = '".$stHtml."';";
        $stJs .= montaListaAtividadesPorProcesso( $rsListaAtividades );

        SistemaLegado::executaFrameOculto( $stJs );
        break;

    case "buscaInscricao":
        if ($_REQUEST["inInscricaoEconomica"]) {
            $obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
            $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
            $obRCEMInscricaoEconomica->listarInscricaoConsulta( $rsLista );
            if ( $rsLista->eof() ) {
                $js = "alertaAviso('@Número de inscrição inválido. (".$_REQUEST["inInscricaoEconomica"].")','form','erro','".Sessao::getId()."');";
                $js .= "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
                $js .= "f.inInscricaoEconomica.value ='';\n";
                $js .= "f.inInscricaoEconomica.focus();\n";
            } else {
                $stNomeCGM = str_replace ("'", "\'", $rsLista->getCampo("nom_cgm") );
                $js = "d.getElementById('stInscricaoEconomica').innerHTML = '".$stNomeCGM."';\n";
            }
        } else {
            $js = "d.getElementById('stInscricaoEconomica').innerHTML = '&nbsp;';\n";
        }

        sistemaLegado::executaFrameOculto( $js );
        break;

    case "buscaAtividade":
        sistemaLegado::executaFrameOculto( BuscaAtividade() );
    break;
    case "buscaCGM":
        sistemaLegado::executaFrameOculto( BuscaCGM() );
    break;
    case "buscaSocio":
        sistemaLegado::executaFrameOculto( BuscaSocio() );
    break;
    case "buscaNatureza":
        sistemaLegado::executaFrameOculto( BuscaNatureza() );
    break;
    case "buscaDomicilio":
        sistemaLegado::executaFrameOculto( BuscaDomicilio() );
        break;

    case "visualizarAtividadeProcesso":
        $boVisualizarProcesso = true;
    case "visualizarAtividade":
        $obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );

        $obRCEMInscricaoAtividade->addAtividade();
        $obRCEMInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $_REQUEST['inCodAtividade'] );

        $obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );

        $obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );

        $obLblNomAtividade = new Label;
        $obLblNomAtividade->setRotulo( "Atividade" );
        $obLblNomAtividade->setValue ( $rsAtividades->getCampo('cod_estrutural') );

        $obLblAtividade = new Label;
        $obLblAtividade->setRotulo( "Atividade" );
        $obLblAtividade->setValue ( $rsAtividades->getCampo('nom_atividade') );

        if ( $rsAtividades->getCampo('principal') == 't' ) {
            $boPrincipal = "Sim";
        } else {
            $boPrincipal = "Não";
        }
        $obLblPrincipal = new Label;
        $obLblPrincipal->setRotulo( "Principal" );
        $obLblPrincipal->setValue ( $boPrincipal );

        $obLblDtInicio = new Label;
        $obLblDtInicio->setRotulo ( "Data de Início" );
        $obLblDtInicio->setValue  ( $rsAtividades->getCampo('dt_inicio')    );

        $obLblDtFinal = new Label;
        $obLblDtFinal->setRotulo  ( "Data de Término" );
        $obLblDtFinal->setValue   ( $rsAtividades->getCampo('dt_termino')    );

        $obFormularioAtividade = new Formulario;
        $obFormularioAtividade->addTitulo( "Dados da Atividade" );
        $obFormularioAtividade->addComponente( $obLblNomAtividade );

        $obFormularioAtividade->addComponente( $obLblAtividade );

        $obFormularioAtividade->addComponente( $obLblPrincipal );
        $obFormularioAtividade->addComponente( $obLblDtInicio  );
        $obFormularioAtividade->addComponente( $obLblDtFinal   );
        $obFormularioAtividade->montaInnerHTML();
        $stHtml = $obFormularioAtividade->getHTML();

        if ($boVisualizarProcesso) {
            $stJs = "d.getElementById('spnVisualizarAtividadeProcesso').innerHTML = '".$stHtml."';";
        } else {
            $stJs = "d.getElementById('spnVisualizarAtividade').innerHTML = '".$stHtml."';";
        }

        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "visualizarElemento":
        $obRCEMElemento = new RCEMElemento( new RCEMAtividade );
        $obRCEMElemento->obRCadastroDinamico->setChavePersistenteValores (
            array(
                "cod_elemento" => $_REQUEST['inCodElemento'],
                "inscricao_economica" => $_REQUEST['inCodInscricao']
            )
        );
        $obRCEMElemento->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

        $obLblElemento = new Label;
        $obLblElemento->setRotulo( "Elemento" );
        $obLblElemento->setValue ( $_REQUEST['inCodElemento']." - ".$_REQUEST['stNomElemento'] );

        $obMontaAtributos = new MontaAtributos;
        $obMontaAtributos->setTitulo     ( "Atributos"  );
        $obMontaAtributos->setName       ( "Atributo_"  );
        $obMontaAtributos->setLabel      ( TRUE         );
        $obMontaAtributos->setRecordSet  ( $rsAtributos );

        $obFormularioElemento = new Formulario;
        $obFormularioElemento->addTitulo( "Dados do Elemento" );
        $obFormularioElemento->addComponente( $obLblElemento        );
        $obMontaAtributos->geraFormulario   ( $obFormularioElemento );
        $obFormularioElemento->montaInnerHTML();
        $stHtml = $obFormularioElemento->getHTML();

        $stJs = "d.getElementById('spnVisualizarElemento').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "visualizarLicenca":
        $obRCEMModalidadeInscricao = new RCEMModalidadeInscricao;
        $obRCEMModalidadeInscricao->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inCodInscricao'] );
        $obRCEMModalidadeInscricao->listarModalidadeAtividadeInscricao( $rsModalidades, $boTransacao, false );

        if ($_REQUEST['stEspecie'] == 'Atividade') {
            $obRCEMLicenca = new RCEMLicencaAtividade;
        }
        if ($_REQUEST['stEspecie'] == 'Especial') {
            $obRCEMLicenca = new RCEMLicencaEspecial;
        }
        $obRCEMLicenca->setCodigoLicenca( $_REQUEST['inCodLicenca'] );
        $obRCEMLicenca->listarLicencasConsulta( $rsLicenca );
        $obRCEMLicenca->consultarAtividades( $rsAtividades );
        $lsAtividadesLicenca = montaListaAtividadesLicenca( $rsAtividades );
        $obRCEMLicenca->consultarHorarios( $rsHorarios );
        $lsHorarios = "";
        if ($_REQUEST['stEspecie'] == 'Especial') {
            $lsHorarios = montaListaHorariosLicenca( $rsHorarios );
        }

        $obLblCodigo = new Label;
        $obLblCodigo->setRotulo( "Licença" );
        $obLblCodigo->setValue ( $_REQUEST['inCodLicenca'] );

        $obLblEspecie = new Label;
        $obLblEspecie->setRotulo( "Espécie" );
        $obLblEspecie->setValue ( $_REQUEST['stEspecie'] );

//        if ( $rsLicenca->getCampo('cod_processo') AND $rsLicenca->getCampo('exercicio_processo') ) {
//            $stProcesso = $rsLicenca->getCampo('cod_processo')."/".$rsLicenca->getCampo('exercicio_processo');
        if ( $rsLicenca->getCampo('processo') ) {
            $stProcesso = $rsLicenca->getCampo('processo');
        } else {
            $stProcesso = "&nbsp;";
        }
        $obLblProcesso = new Label;
        $obLblProcesso->setRotulo( "Processo"  );
        $obLblProcesso->setValue ( $stProcesso );

        $obLblDtInicio = new Label;
        $obLblDtInicio->setRotulo( "Data de Início" );
        $obLblDtInicio->setValue ( $rsLicenca->getCampo("dt_inicio") );

        $obLblDtTermino = new Label;
        $obLblDtTermino->setRotulo( "Data de Término" );
        $obLblDtTermino->setValue ( $rsLicenca->getCampo("dt_termino") );

        $stSituacao = $rsLicenca->getCampo("situacao");

    // mudar label da data
    switch ($stSituacao) {
        case "Cassada":
            $stLblData = "Data da Cassação";
            break;
        case "Suspensa":
            $stLblData = "Início Suspensão";
            break;
        case "Baixada":
            $stLblData = "Data da Baixa";
            break;
    }
        $obLblSituacao = new Label;
        $obLblSituacao->setRotulo( "Situação" );
        $obLblSituacao->setValue ( $stSituacao );

        $obLblData = new Label;
        $obLblData->setRotulo( $stLblData );
        $obLblData->setValue ( $rsLicenca->getCampo("baixa_inicio") );

        $obLblDataTermino = new Label;
        $obLblDataTermino->setRotulo( "Término Suspensão" );
        $obLblDataTermino->setValue ( $rsLicenca->getCampo("baixa_termino") );

        $obLblMotivo = new Label;
        $obLblMotivo->setRotulo( "Motivo" );
        $obLblMotivo->setValue ( $rsLicenca->getCampo("motivo") );

        $obFormularioAtividade = new Formulario;
        $obFormularioAtividade->addTitulo( "Dados da Licença" );
        $obFormularioAtividade->addComponente( $obLblCodigo   );
        $obFormularioAtividade->addComponente( $obLblEspecie  );
        $obFormularioAtividade->addComponente( $obLblProcesso );
        $obFormularioAtividade->addComponente( $obLblDtInicio );
        $obFormularioAtividade->addComponente( $obLblDtTermino);
        $obFormularioAtividade->addComponente( $obLblSituacao );
        if ($stSituacao == "Cassada" || $stSituacao == "Baixada") {
            $obFormularioAtividade->addComponente( $obLblData   );
            $obFormularioAtividade->addComponente( $obLblMotivo );
        } elseif ($stSituacao == "Suspensa") {
            $obFormularioAtividade->addComponente( $obLblData   	);
            $obFormularioAtividade->addComponente( $obLblDataTermino   	);
            $obFormularioAtividade->addComponente( $obLblMotivo 	);
        }
        $obFormularioAtividade->montaInnerHTML();
        $stHtml = $obFormularioAtividade->getHTML();

        $stHtml .= $lsAtividadesLicenca;
        $stHtml .= $lsHorarios;

        $stJs = "d.getElementById('spnVisualizarLicenca').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "visualizarModalidade":
        $obRCEMModalidadeInscricao = new RCEMModalidadeInscricao;
        $obRCEMModalidadeInscricao->setCodModalidade( $_REQUEST['inCodLicenca'] );
        $obRCEMModalidadeInscricao->listarModalidadeAtividadeInscricao( $rsModalidades, $boTransacao, false );

        $obLblAtividade = new Label;
        $obLblAtividade->setRotulo( "Atividade" );
        $obLblAtividade->setValue ( $rsModalidades->getCampo('cod_atividade')." - ".$rsModalidades->getCampo('nom_atividade') );

        $obLblModalidade = new Label;
        $obLblModalidade->setRotulo( "Modalidade" );
        $obLblModalidade->setValue ( $rsModalidades->getCampo('nom_modalidade') );

        $obLblDtInicio = new Label;
        $obLblDtInicio->setRotulo( "Data de Início" );
        $obLblDtInicio->setValue ( $rsModalidades->getCampo("dt_vigencia_modalidade") );

        if ( $rsModalidades->getCampo("dt_baixa_modalidade") != "" ) {
            $stSituacao = "Baixada";
        } else {
            $stSituacao = "Ativa";
        }
        $obLblSituacao = new Label;
        $obLblSituacao->setRotulo( "Situação" );
        $obLblSituacao->setValue ( $stSituacao );

        $obLblData = new Label;
        $obLblData->setRotulo( "Data de Baixa" );
        $obLblData->setValue ( $rsModalidades->getCampo("dt_baixa_modalidade") );

        $obLblMotivo = new Label;
        $obLblMotivo->setRotulo( "Motivo" );
        $obLblMotivo->setValue ( $rsModalidades->getCampo("motivo_baixa_modalidade") );

        $obFormularioModalidade = new Formulario;
        $obFormularioModalidade->addTitulo( "Dados da Modalidade" );
        $obFormularioModalidade->addComponente( $obLblAtividade   );
        $obFormularioModalidade->addComponente( $obLblModalidade  );
        $obFormularioModalidade->addComponente( $obLblDtInicio    );
        $obFormularioModalidade->addComponente( $obLblSituacao    );
        if ( $rsModalidades->getCampo("dt_baixa_modalidade") != "" ) {
            $obFormularioModalidade->addComponente( $obLblData    );
            $obFormularioModalidade->addComponente( $obLblMotivo  );
        }
        $obFormularioModalidade->montaInnerHTML();
        $stHtml = $obFormularioModalidade->getHTML();

        $stHtml .= $lsAtividadesLicenca;
        $stHtml .= $lsHorarios;

        $stJs = "d.getElementById('spnVisualizarModalidade').innerHTML = '".$stHtml."';";
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "preencheProxCombo":
        $obMontaAtividade = new MontaAtividade;
        $obMontaAtividade->setCadastroAtividade( false );
        $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboAtividade = "inCodAtividade_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboAtividade];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("§" , $stChaveLocal );
        $obMontaAtividade->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaAtividade->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaAtividade->setCodigoAtividade   ( $arChaveLocal[1] );
        $obMontaAtividade->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaAtividade->preencheProxCombo    ( $inPosicao , $_REQUEST["inNumNiveis"] );
    break;
}
?>

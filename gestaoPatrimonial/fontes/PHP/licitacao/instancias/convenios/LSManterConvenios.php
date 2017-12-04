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
/*
rio de Convenio
    * Data de Criação   : 17/01/2007

    * @author Analista:
    * @author Desenvolvedor: Rodrigo
    * @ignore

    $Id: LSManterConvenios.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-03.05.14
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterConvenios";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgFormConsulta= "FMConsultaConvenios.php";
$pgFormAnular  = "FMAnularConvenios.php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$stCaminho = CAM_GP_LIC_INSTANCIAS."convenios/";
Sessao::remove('rsVeiculos');

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'anular':
        $pgProx = $pgForm; break;
    case 'consultar':
        $pgProx = $pgForm; break;
    case 'rescindir':
        $pgProx = $pgForm; break;
}

function montaListaAlteracao($rsLista , $stJs = null , $stAcao = 'alterar')
{
    global $stCaminho;
    global $pgFormConsulta;
    global $pgFormAnular;
    global $pgProc;
    global $pgForm;

    $stLink = "";

     $rsLista->addFormatacao ( 'valor' , 'NUMERIC_BR' );

     $rsLista->setPrimeiroElemento();
     $obLista = new Lista;
     $obLista->setRecordSet ( $rsLista );
     $obLista->setTitulo ( "Resultados da Busca" );
     $obLista->setMostraPaginacao ( false );

    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Número do Convênio" );
        $obLista->ultimoCabecalho->setWidth ( 15 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Tipo do Convênio" );
        $obLista->ultimoCabecalho->setWidth ( 30);
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Objeto do Convênio" );
        $obLista->ultimoCabecalho->setWidth ( 40 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "Valor" );
        $obLista->ultimoCabecalho->setWidth ( 15 );
        $obLista->commitCabecalho ();

        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth ( 5 );
        $obLista->commitCabecalho ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "DIREITA" );
        $obLista->ultimoDado->setCampo ( "[num_convenio]/[exercicio]" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obLista->ultimoDado->setCampo ( "descricao_tipo" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "ESQUERDA" );
        $obLista->ultimoDado->setCampo ( "descricao_objeto" );
        $obLista->commitDado ();

        $obLista->addDado ();
        $obLista->ultimoDado->setAlinhamento ( "DIREITA" );
        $obLista->ultimoDado->setCampo ( "valor" );
        $obLista->commitDado ();
        if ($stAcao == 'alterar') {
            $obLista->addAcao ();
            $obLista->ultimaAcao->setAcao ( "ALTERAR" );
            $obLista->ultimaAcao->addCampo("inNumConvenio","num_convenio");
            $obLista->ultimaAcao->addCampo("inExercicio"  ,"exercicio"   );
            $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&stAcao=alterar&" );
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( 'excluir' );
            $obLista->ultimaAcao->addCampo("&inNumConvenio" , "num_convenio" );
            $obLista->ultimaAcao->addCampo("&stDescQuestao" , "num_convenio" );
            $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&nomAcao=Excluir%20Convênio&stAcao=excluirConvenio" );
            $obLista->commitAcao();
        } elseif ($stAcao == 'consultar') {
            $obLista->addAcao ();

            $obLista->ultimaAcao->setAcao ( "consultar" );
            $obLista->ultimaAcao->addCampo("inNumConvenio","num_convenio");
            $obLista->ultimaAcao->addCampo("inExercicio"  ,"exercicio"   );
            $obLista->ultimaAcao->setLink( $stCaminho.$pgFormConsulta."?".Sessao::getId().$stLink."&stAcao=alterar&" );

            $obLista->commitAcao ();
        } elseif ($stAcao == 'anular') {
            $obLista->addAcao ();
            $obLista->ultimaAcao->setAcao ( "anular" );
            $obLista->ultimaAcao->addCampo("&inNumConvenio" , "num_convenio" );
            $obLista->ultimaAcao->addCampo("&stDescQuestao" , "num_convenio" );
            $obLista->ultimaAcao->setLink( $stCaminho.$pgFormAnular."?".Sessao::getId().$stLink."&stAcao=anular" );

            $obLista->commitAcao ();
        } elseif ($stAcao == "rescindir") {
            $obLista->addAcao ();
            $obLista->ultimaAcao->setAcao ( "rescindir" );

            $obLista->ultimaAcao->addCampo("&inNumConvenio" , "num_convenio" );
            $obLista->ultimaAcao->addCampo("&inExercicio" , "exercicio" );
            $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink."&stAcao=rescindir" );
            $obLista->commitAcao ();
        }
    } else {
        $obLista->addCabecalho ();
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;" );
        $obLista->ultimoCabecalho->setWidth    ( 5 );
        $obLista->commitCabecalho ();
    }
    $obLista->Show();
}

require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenio.class.php' );
$obTLicConvenio = new TLicitacaoConvenio;
$stFiltro  = "";

$arFiltro = Sessao::read('filtro');
if ($request->get('inNumConvenio') || $request->get('inExercicio')) {
    foreach ($_REQUEST as $key => $value) {
        $arFiltro[$key] = $value;
    }
} else {
    if ($arFiltro) {
        foreach ($arFiltro as $key => $value) {
            $_REQUEST[$key] = $value;
        }
    }
}

Sessao::write('filtro', $arFiltro);

if ($arFiltro[ 'inExercicio' ]) {
    $stFiltro .= ' AND convenio.exercicio = \'' . $arFiltro[ 'inExercicio' ] . '\'';
}

if ($arFiltro[ 'inNumConvenio' ]) {
    $stFiltro .= ' AND convenio.num_convenio = ' . $arFiltro[ 'inNumConvenio' ] . '';
}

if ($arFiltro[ 'inCodTipoConvenio' ]) {
    $stFiltro .= ' AND  convenio.cod_tipo_convenio = ' . $arFiltro[ 'inCodTipoConvenio' ] . '';
}

if ($arFiltro[ 'stObjeto' ]) {
    $stFiltro .= ' AND convenio.cod_objeto = ' . $arFiltro[ 'stObjeto' ] . '';
}

if ($arFiltro[ 'inCgmParticipante' ]) {
    $stFiltro .= ' AND participante_convenio.cgm_fornecedor = ' . $arFiltro[ 'inCgmParticipante' ] . '';
}

if ($arFiltro['stAcao'] != 'consultar') {
    $stFiltro = ' AND convenio_anulado.num_convenio is null ' . $stFiltro;
}

$arCodRescisaoConvenio = array();
if ($arFiltro['stAcao'] != 'consultar') {
    require_once ( CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoRescisaoConvenio.class.php');

    $obTLicitacaoRescisaoConvenio = new TLicitacaoRescisaoConvenio();
    $obTLicitacaoRescisaoConvenio->setDado("exercicio_convenio",Sessao::read('exercicio'));
    $obTLicitacaoRescisaoConvenio->recuperaMontaRecuperaDadosRescisao($rsRescisaoConvenio);

    foreach ($rsRescisaoConvenio->arElementos as $arRescisaoConvenio) {
        foreach ($arRescisaoConvenio as $chave => $valor) {
            if ($chave == 'num_convenio') {
                array_push($arCodRescisaoConvenio,$valor);
            }
        }
    }

    if (count($arCodRescisaoConvenio) > 0) {
        $stFiltro .= ' AND convenio.num_convenio NOT IN (';
        $stFiltro .= implode(',',$arCodRescisaoConvenio).')';
    }
}

$obTLicConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');

montaListaAlteracao ( $rsConvenio  , '' , $_REQUEST['stAcao'] );

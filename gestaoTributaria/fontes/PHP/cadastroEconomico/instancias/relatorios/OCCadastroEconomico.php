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
    * Página Oculto do Relatório de Cadastro Economico
    * Data de Criação   : 16/09/2005

    * @author Diego Bueno
    * @ignore

    * $Id: OCCadastroEconomico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

// INSTANCIA OBJETO
$arFiltroSessao = Sessao::read( "filtroRelatorio" );
if ( isset($arFiltroSessao['stTipoRelatorioSubmit']) ) {

    include_once( CAM_FW_PDF."RRelatorio.class.php"                  );
    include_once( CAM_GT_CEM_NEGOCIO."RCEMRelatorioCadastroEconomico.class.php" );

    $obRRelatorio              = new RRelatorio;
    $obRCEMRelatorioCadEconomico = new RCEMRelatorioCadastroEconomico;

    $obRCEMRelatorioCadEconomico->setInscricaoInicial ( $arFiltroSessao['inNumInscricaoEconomicaInicial'] );
    $obRCEMRelatorioCadEconomico->setInscricaoFinal ( $arFiltroSessao['inNumInscricaoEconomicaFinal'] );
    $obRCEMRelatorioCadEconomico->setCodInicio ( $arFiltroSessao['inCodInicio'] );
    $obRCEMRelatorioCadEconomico->setCodTermino ( $arFiltroSessao['inCodTermino'] );
    $obRCEMRelatorioCadEconomico->setDtInicio ( $arFiltroSessao['dtInicio'] );
    $obRCEMRelatorioCadEconomico->setCodSocio ( $arFiltroSessao['inCodigoSocio'] );
    $obRCEMRelatorioCadEconomico->setTipoInscricao ( $arFiltroSessao['stTipoInscricao'] );
    $obRCEMRelatorioCadEconomico->setTipoRelatorio ( $arFiltroSessao['stTipoRelatorioSubmit'] );
    $obRCEMRelatorioCadEconomico->setCodLicencaInicial ( $arFiltroSessao['stLicencaInicio'] );
    $obRCEMRelatorioCadEconomico->setCodLicencaFinal ( $arFiltroSessao['stLicencaFim'] );
    $obRCEMRelatorioCadEconomico->setCodLogradouroInicial ( $arFiltroSessao['inCodInicioLogradouro'] );
    $obRCEMRelatorioCadEconomico->setCodLogradouroFinal ( $arFiltroSessao['inCodTerminoLogradouro'] );

    // GERA RELATORIO ATRAVES DO FILTRO SETADO
    $arTodasInformacoesEntidade = array();

    if ($arFiltroSessao['stTipoRelatorio'] == 'analitico') {
        $obRCEMRelatorioCadEconomico->geraRecordSet ( $rsResultado );

        $arTodasInformacoesEntidade[] = array ( 'dados' => $rsResultado );
        $arTodasInformacoes = array ();
        while ( !$rsResultado->eof() ) {
            $obRCEMRelatorioCadEconomico->setInscricaoAtual  ( $rsResultado->getCampo('inscricao_economica') );
            $obRCEMRelatorioCadEconomico->geraAtividades  ( $rsAtividades );
            $obRCEMRelatorioCadEconomico->geraSocios ( $rsSocios, $rsResultado->getCampo('sociedade') );
            $obRCEMRelatorioCadEconomico->geraAtributosDinamicos ( $rsAtributos );
            $arTodasInformacoes[] = array (
                    'atividades' => $rsAtividades,
                    'socios'       => $rsSocios,
                    'atributos'    => $rsAtributos
            );

            $rsResultado->proximo();
        }

    } elseif ($arFiltroSessao['stTipoRelatorio'] == 'sintetico') {

        $obRCEMRelatorioCadEconomico->geraRecordSet ( $rsResultado );
        $arTodasInformacoesEntidade[] = array (   'dados' => $rsResultado );
        $obRCEMRelatorioCadEconomico->setInscricaoAtual  ( $rsResultado->getCampo('inscricao_economica') );
        $obRCEMRelatorioCadEconomico->geraSocios ( $rsSocios, $rsResultado->getCampo('sociedade') );

        $arTodasInformacoes[] = array (
            'socios'    => $rsSocios,
        );

    }

    $rsTodasInformacoesEntidade = new RecordSet;
    $rsTodasInformacoesEntidade->preenche ( $arTodasInformacoesEntidade );
    $rsTodasInformacoes = new RecordSet;
    $rsTodasInformacoes->preenche ( $arTodasInformacoes );

    Sessao::write( "sessao_transf7", $rsTodasInformacoesEntidade );
    Sessao::write( "sessao_transf6", $rsTodasInformacoes );

    $obRRelatorio->executaFrameOculto( "OCGeraRelatorioCadastroEconomico.php" );
}

switch ($_REQUEST["stCtrl"]) {

    case "buscaSocio":

        include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaFisica.class.php"        );
        include_once ( CAM_GA_CGM_NEGOCIO."RCGMPessoaJuridica.class.php"      );
        include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                    );

        $obRCGM = new RCGM;

        $stText = "inCodigoSocio";
        $stSpan = "stNomeSocio";

        if ($_REQUEST[$stText] != "") {
            $obRCGM->setNumCGM( $_REQUEST[ $stText] );
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
        SistemaLegado::executaFrameOculto( $stJs );
    break;

}

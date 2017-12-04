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
 * Página de processamento oculto para o relatório de logradouros
 * Data de Criação   : 28/03/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: OCLogradouros.php 63656 2015-09-24 19:44:19Z evandro $

 * Casos de uso: uc-05.01.20
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMRelatorioLogradouros.class.php";
include_once CAM_FW_PDF."RRelatorio.class.php";

//Define o nome dos arquivos PHP
$stPrograma  = "Logradouros";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJs        = "JS".$stPrograma.".js";

include_once $pgJs;

// INSTANCIA OBJETO
$obRCIMLogradouro = new RCIMLogradouro;
$obRRelatorio     = new RRelatorio;
$obRCIMRelatorioLogradouros = new RCIMRelatorioLogradouros;

$arFiltro = Sessao::read('filtroRelatorio');
if (!$arFiltro['inCEPTermino']) {
    $arFiltro['inCEPTermino'] = $arFiltro['inCEPInicio'];
}

if (!$arFiltro['inCodTermino']) {
    $arFiltro['inCodTermino'] = $arFiltro['inCodInicio'];
}

if (!$arFiltro['inCodTerminoBairro']) {
    $arFiltro['inCodTerminoBairro'] = $arFiltro['inCodInicioBairro'];
}

// SETA ELEMENTOS DO FILTRO
$stFiltro = "";
$obRCIMRelatorioLogradouros->obRCIMLogradouro->setCodigoMunicipio( $arFiltro['inCodigoMunicipio']    );
$obRCIMRelatorioLogradouros->obRCIMLogradouro->setCodigoUF       ( $arFiltro['inCodigoUF']           );
$obRCIMRelatorioLogradouros->obRCIMLogradouro->setCodigoTipo     ( $arFiltro['inCodTipoLogradouro']  );
$obRCIMRelatorioLogradouros->obRCIMLogradouro->setNomeLogradouro ( $arFiltro['stNomLogradouro']      );
$obRCIMRelatorioLogradouros->obRCIMLogradouro->obRCIMBairro->setNomeBairro( $arFiltro['stNomBairro'] );
$obRCIMRelatorioLogradouros->setCodInicio       ( $arFiltro['inCodInicio']        );
$obRCIMRelatorioLogradouros->setCodInicioBairro ( $arFiltro['inCodInicioBairro']  );
$obRCIMRelatorioLogradouros->setCodInicioCEP    ( str_replace("-", "", $arFiltro['inCEPInicio'])        );
$obRCIMRelatorioLogradouros->setCodTermino      ( $arFiltro['inCodTermino']       );
$obRCIMRelatorioLogradouros->setCodTerminoBairro( $arFiltro['inCodTerminoBairro'] );
$obRCIMRelatorioLogradouros->setCodTerminoCEP   ( str_replace("-", "", $arFiltro['inCEPTermino'])       );
$obRCIMRelatorioLogradouros->setOrder           ( $arFiltro['stOrder']            );
$obRCIMRelatorioLogradouros->setMostrarHistorico( $arFiltro['boHistorico'] );
$obRCIMRelatorioLogradouros->setMostrarNorma    ( $arFiltro['boNorma'] );

// GERA RELATORIO A PARTIR DO FILTRO SETADO
$obRCIMRelatorioLogradouros->geraRecordSet( $rsLogradouros );
Sessao::write('mostrar_historico', $arFiltro['boHistorico'] );
Sessao::write('mostrar_norma'    , $arFiltro['boNorma'] );
Sessao::write('dados_relatorio'  , $rsLogradouros );
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioLogradouros.php" );

// SELECIONA ACAO
switch ($request->get("stCtrl")) {
    case "preencheMunicipio":
        $arNomFiltro = Sessao::read( "NomFiltro" );
        unset( $arNomFiltro );
        $js = "";
        $js .= "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodigoUF"]) {

            $obRCIMLogradouro->setCodigoUF( $_REQUEST["inCodUF"] );
            $obRCIMLogradouro->listarMunicipios( $rsMunicipios );
            $inContador = 1;

            while ( !$rsMunicipios->eof() ) {

                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = str_replace ("'", "\'", $rsMunicipios->getCampo( "nom_municipio" ) );
                #$stNomMunicipio = 'e'; #$rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new Option('".$stNomMunicipio."','".$inCodMunicipio."'); \n";
                $inContador++;

                //carrega municipos na sessao para a exibição de filtro no rodapé do relatorio
                $arNomFiltro['municipio'][$rsMunicipios->getCampo( 'cod_municipio' )] = $stNomMunicipio;

                $rsMunicipios->proximo();
            }
        }

        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodigoMunicipio.value='".$_REQUEST["inCodigoMunicipio"]."'; \n";
            $js .= "f.inCodMunicipio.options[".$_REQUEST["inCodigoMunicipio"]."].selected = true; \n";
        }

        Sessao::write( "NomFiltro", $arNomFiltro );
        SistemaLegado::executaFrameOculto($js);

    break;
}

?>

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
    * Página de Frame Oculto de Definir Caracteristicas para Tipo de Lecenca
    * Data de Criação   : 04/04/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: OCDefinirCaracteristicas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.28
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicenca.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicencaDocumento.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

;

switch ($_REQUEST['stCtrl']) {
    case "CarregaAtributos":
        $obRCadastroDinamico  = new RCadastroDinamico;
        $obRCadastroDinamico->setCodCadastro( 10 );
        $obRCadastroDinamico->setPersistenteAtributos       ( new TCIMAtributoTipoLicenca() );
        $obRCadastroDinamico->setChavePersistenteValores    ( array( "cod_tipo" => $_GET["inTipoLicenca"] ) );
        $obRCadastroDinamico->recuperaAtributosDisponiveis  ( $rsAtributosDisponiveis  );
        $obRCadastroDinamico->recuperaAtributosSelecionados ( $rsAtributosSelecionados );

        $stJs = "limpaSelect(f.inAtribOrdemDisponivel,0); \n";
        $stJs .= "limpaSelect(f.inAtribOrdemSelecionados,0); \n";
        $inContador = 0;
        while ( !$rsAtributosDisponiveis->eof() ) {
            $stJs .= "f.inAtribOrdemDisponivel.options[$inContador] = new Option('".$rsAtributosDisponiveis->getCampo("nom_atributo")."','".$rsAtributosDisponiveis->getCampo("cod_atributo")."'); \n";
            $rsAtributosDisponiveis->proximo();
            $inContador++;
        }

        $inContador = 0;
        while ( !$rsAtributosSelecionados->eof() ) {
            $stJs .= "f.inAtribOrdemSelecionados.options[$inContador] = new Option('".$rsAtributosSelecionados->getCampo("nom_atributo")."','".$rsAtributosSelecionados->getCampo("cod_atributo")."'); \n";
            $rsAtributosSelecionados->proximo();
            $inContador++;
        }

        $stFiltro = " WHERE modelo_arquivos_documento.cod_acao = ".Sessao::read('acao')." AND tipo_licenca_documento.cod_documento IS NULL ";
        $obTCIMTipoLicencaDocumento = new TCIMTipoLicencaDocumento;
        $obTCIMTipoLicencaDocumento->retornaListadeDocumentosDisponiveis( $rsDocumentosDisponiveis, $stFiltro, $_GET["inTipoLicenca"] );

        $stFiltro = " WHERE tipo_licenca_documento.cod_tipo = ".$_GET["inTipoLicenca"];
        $obTCIMTipoLicencaDocumento->retornaListadeDocumentosLicenca( $rsDocumentosSelecionados, $stFiltro );

        $stJs .= "limpaSelect(f.inModDocDisponivel,0); \n";
        $inContador = 0;
        while ( !$rsDocumentosDisponiveis->eof() ) {
            $stJs .= "f.inModDocDisponivel.options[$inContador] = new Option('".$rsDocumentosDisponiveis->getCampo("nome_documento")."','".$rsDocumentosDisponiveis->getCampo("cod_documento")."-".$rsDocumentosDisponiveis->getCampo("cod_tipo_documento")."'); \n";
            $rsDocumentosDisponiveis->proximo();
            $inContador++;
        }

        $stJs .= "limpaSelect(f.inModDocSelecionados,0); \n";
        $inContador = 0;
        while ( !$rsDocumentosSelecionados->eof() ) {
            $stJs .= "f.inModDocSelecionados.options[$inContador] = new Option('".$rsDocumentosSelecionados->getCampo("nome_documento")."','".$rsDocumentosSelecionados->getCampo("cod_documento")."-".$rsDocumentosSelecionados->getCampo("cod_tipo_documento")."'); \n";
            $rsDocumentosSelecionados->proximo();
            $inContador++;
        }

        echo $stJs;
        break;
}

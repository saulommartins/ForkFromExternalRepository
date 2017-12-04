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
  * Página de Processamento da Definir Caracteristicas para Conceder Licenca
  * Data de criação : 07/04/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRDefinirCaracteristicas.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso: uc-05.01.28
**/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoLicencaDocumento.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoTipoLicenca.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "DefinirCaracteristicas";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "definir_tp":
        $obTCIMTipoLicencaDocumento = new TCIMTipoLicencaDocumento;
        $obRCadastroDinamico = new RCadastroDinamico;
        $obRCadastroDinamico->setCodCadastro( 10 );
        $obRCadastroDinamico->setPersistenteAtributos ( new TCIMAtributoTipoLicenca() );
        $obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo" => $_REQUEST["inTipoLicenca"] ) );

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTCIMTipoLicencaDocumento );

            $arCodDocumentosSelecionados = $_REQUEST["inModDocSelecionados"];

            $obTCIMTipoLicencaDocumento->setDado( "cod_tipo", $_REQUEST["inTipoLicenca"] );
            $obTCIMTipoLicencaDocumento->exclusao();
            for ( $inCount=0; $inCount < count($arCodDocumentosSelecionados); $inCount++ ) {
                $arCodigos = explode( "-", $arCodDocumentosSelecionados[ $inCount ] );
                $obTCIMTipoLicencaDocumento->setDado( "cod_tipo", $_REQUEST["inTipoLicenca"] );
                $obTCIMTipoLicencaDocumento->setDado( "cod_tipo_documento", $arCodigos[1] );
                $obTCIMTipoLicencaDocumento->setDado( "cod_documento", $arCodigos[0] );
                $obTCIMTipoLicencaDocumento->inclusao();
            }

            $inCodAtributosSelecionados = $_REQUEST["inAtribOrdemSelecionados"];
            if ( count($inCodAtributosSelecionados) > 0 ) {
                for ( $inCount=0; $inCount < count($inCodAtributosSelecionados); $inCount++ ) {
                    $obRCadastroDinamico->addAtributosDinamicos( $inCodAtributosSelecionados[ $inCount ] );
                }

                $obRCadastroDinamico->salvar( true );
            }

        Sessao::encerraExcecao();

        sistemaLegado::alertaAviso( $pgForm, "Características definidas com sucesso!", $stAcao, "aviso", Sessao::getId(), "../");
        break;
}

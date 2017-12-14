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

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMTipoLicencaDiversa.class.php"         );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMTipoLicencaModeloDocumento.class.php"   );

switch ($request->get('stAcao')) {
    case "alterar" :
        $obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
        $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa( $request->get("inCodigoTipo"));
        $obRCEMTipoLicencaDiversa->listarTipoLicencaDiversa($rsTiposLicenca);
        $_REQUEST["cod_documento"] = $rsTiposLicenca->getCampo("cod_documento");

        if ( $rsTiposLicenca->getCampo("cod_utilizacao") == 1 ) {
            include_once(CAM_GT_ECONOMICO."instancias/licenca/FMAlterarLicencaGeralTipo.php");
        } else {
            include_once(CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeralUsoSolo.php");
        }
    break;

    default:
        $obRCEMTipoLicencaDiversa = new RCEMTipoLicencaDiversa;
        $obRCEMTipoLicencaDiversa->setCodigoTipoLicencaDiversa($request->get('inCodigoTipoLicenca'));
        $obRCEMTipoLicencaDiversa->listarTipoLicencaDiversa($rsTiposLicenca);

        $obTCEMTipoLicencaModeloDocumento = new TCEMTipoLicencaModeloDocumento;
        $stFiltro =  " WHERE COD_TIPO = ".$rsTiposLicenca->getCampo("cod_tipo");

        $obTCEMTipoLicencaModeloDocumento->recuperaLicencaDiversaModeloDocumento($rsModeloDoc, $stFiltro);

        Sessao::write( "inCodDocumento", $rsModeloDoc->getCampo("cod_documento") );
        Sessao::write( "inCodUtilizacao", $rsTiposLicenca->getCampo("cod_utilizacao") );
        Sessao::write( "inCodigoTipoLicenca", $rsTiposLicenca->getCampo("cod_tipo") );
        Sessao::write( "inTipoDocumento", $rsModeloDoc->getCampo("cod_tipo_documento") );

        if ( $rsTiposLicenca->getCampo("cod_utilizacao") == 1 ) {
            include_once(CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeralTipo.php");
        } else {
            include_once(CAM_GT_ECONOMICO."instancias/licenca/FMConcederLicencaGeralUsoSolo.php");
        }

}

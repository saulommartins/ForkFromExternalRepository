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
    * Oculto da PopUp de Localizacao
    * Data de Criação   : 14/02/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou;

    * @ignore

    * $Id: OCBuscaLocalizacao.php 63781 2015-10-09 20:50:07Z arthur $

    * Casos de uso: uc-05.01.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php");
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacaoCombos.class.php" );

if ($request->get('stCtrl')) {
    $obMontaLocalizacao = new MontaLocalizacaoCombos;
    $obMontaLocalizacao->setCadastroLocalizacao( false );
    $obMontaLocalizacao->setPopup( true );
    
    switch ($request->get('stCtrl')) {
        case "preencheProxCombo":
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $request->get('inPosicao') - 1);
            $stChaveLocal = $request->get($stNomeComboLocalizacao);
            $inPosicao = $request->get('inPosicao');
            if ( empty( $stChaveLocal ) and $request->get('inPosicao') > 2 ) {
                $stNomeComboLocalizacao = "inCodLocalizacao_".( $request->get('inPosicao') - 2);
                $stChaveLocal = $request->get($stNomeComboLocalizacao);
                $inPosicao = $request->get('inPosicao') - 1;
            }
            $arChaveLocal = explode("-" , $stChaveLocal );
            $obMontaLocalizacao->setCodigoVigencia    ( $request->get('inCodigoVigencia') );
            $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
            $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
            $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
            $obMontaLocalizacao->preencheProxCombo( $inPosicao , $request->get('inNumNiveis') );
        break;
        case "preencheCombos":
            $obMontaLocalizacao->setCodigoVigencia( $request->get('inCodigoVigencia')   );
            $obMontaLocalizacao->setCodigoNivel   ( $request->get('inCodigoNivel')      );
            $obMontaLocalizacao->setValorReduzido ( $request->get('stChaveLocalizacao') );
            $obMontaLocalizacao->preencheCombos();
        break;
    }
}
    if ($request->get('stTipoBusca') == "nomLocalizacao") {
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorComposto( $request->get('stChaveLocalizacao') );
        if ( $request->get('stChaveLocalizacaoLoteamento') )
            $obRCIMLocalizacao->setValorComposto( $request->get('stChaveLocalizacaoLoteamento') );
        $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
        $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
        $stCodigo = $rsLocalizacao->getCampo("cod_localizacao");
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$request->get('stNomCampoCod')."', '".$request->get('stIdCampoDesc')."', '".$request->get('stNomForm')."', '".$stDescricao."')");
    } elseif ($request->get('stTipoBusca') == "buscaReduzido") {
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setValorReduzido( $request->get('stChaveLocalizacao') );
        $obRCIMLocalizacao->setCodigoNivel  ( $request->get('inCodigoNivel')-1);
        $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );
        $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
        SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$request->get('stNomCampoCod')."', '".$request->get('stIdCampoDesc')."', '".$request->get('stNomForm')."', '".$stDescricao."')");
    }

?>
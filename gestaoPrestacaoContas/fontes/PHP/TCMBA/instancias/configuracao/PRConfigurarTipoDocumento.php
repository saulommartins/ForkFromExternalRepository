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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GPC_TCMBA_MAPEAMENTO."TTCMBADocumentoDePara.class.php");

$stPrograma = "ConfigurarTipoDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao','configurar');

foreach ($_REQUEST as $key => $value) {
    if ($value) {
        $arKey = explode('_', $key);
        if (substr($key,0,15) == 'inCodDocumento_') {
            $arDocumentos[$arKey[1]]['tipo_urbem'] = $arKey[1];
            $arDocumentos[$arKey[1]]['tipo_tcm'] = $value;
        }
    }
}

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($stAcao) {
    case 'configurar' :
        if (count($arDocumentos) > 0) {
            $obTTCMBADocumentoDePara = new TTCMBADocumentoDePara();
            $obTTCMBADocumentoDePara->recuperaDocumentos( $rsDocumentos, "", "", $boTransacao);

            while (!$rsDocumentos->eof()) {
                $obTTCMBADocumentoDePara->setDado('cod_documento', $rsDocumentos->getCampo('cod_documento'));
                $obTTCMBADocumentoDePara->setDado('cod_documento_tcm', $rsDocumentos->getCampo('cod_documento_tcm'));
                $obTTCMBADocumentoDePara->exclusao($boTransacao);
                $rsDocumentos->proximo();
            }

            foreach ($arDocumentos as $arDocumentosTMP) {
                if ($arDocumentosTMP['tipo_tcm'] && $arDocumentosTMP['tipo_urbem']) {
                    $obTTCMBADocumentoDePara->setDado('cod_documento_tcm', $arDocumentosTMP['tipo_tcm']);
                    $obTTCMBADocumentoDePara->setDado('cod_documento', $arDocumentosTMP['tipo_urbem']);
                    $obErro = $obTTCMBADocumentoDePara->inclusao($boTransacao);
                }
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário vincular ao menos um documento!'),"n_incluir","erro");
        }
    break;
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMBADocumentoDePara );

?>
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
    * Titulo do arquivo : Formulário de Vínculo do Tipo de Documento Certidao do TCEAM com o do Urbem
    * Data de Criação   : 23/05/2014

    * @author Analista      Gelson
    * @author Desenvolvedor Evandro Noguez Melos

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRConfigurarTipoCertidao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GPC_TCEAM_MAPEAMENTO."TTCEAMTipoCertidaoDocumento.class.php");

$stPrograma = "ConfigurarTipoCertidao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$boTransacao = new Transacao();

foreach ($_REQUEST as $key => $value) {
    if ($value) {
        $arKey = explode('_', $key);
        if (substr($key,0,15) == 'inCodDocumento_') {
            $arDocumentos[$arKey[1]]['tipo_urbem'] = $arKey[1];
            $arDocumentos[$arKey[1]]['tipo_tce'] = $value;
        }
    }
}

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($stAcao) {
    case 'configurar' :
        if (count($arDocumentos) > 0) {
            $obTTCEAMTipoCertidaoDocumento = new TTCEAMTipoCertidaoDocumento();
            $obTTCEAMTipoCertidaoDocumento->recuperaDocumentosTipoCertidao( $rsDocumentosTipoCertidao, "", "", $boTransacao);

            while (!$rsDocumentosTipoCertidao->eof()) {
                $obTTCEAMTipoCertidaoDocumento->setDado('cod_tipo_certidao'     , $rsDocumentosTipoCertidao->getCampo('cod_tipo_certidao'));
                $obTTCEAMTipoCertidaoDocumento->setDado('cod_documento'   , $rsDocumentosTipoCertidao->getCampo('cod_documento'));
                $obTTCEAMTipoCertidaoDocumento->exclusao($boTransacao);
                $rsDocumentosTipoCertidao->proximo();
            }

            foreach ($arDocumentos as $arDocumentosTMP) {
                if ($arDocumentosTMP['tipo_tce'] && $arDocumentosTMP['tipo_urbem']) {
                    $obTTCEAMTipoCertidaoDocumento->setDado('cod_tipo_certidao'     , $arDocumentosTMP['tipo_tce']);
                    $obTTCEAMTipoCertidaoDocumento->setDado('cod_documento'   , $arDocumentosTMP['tipo_urbem']);
                    $obErro = $obTTCEAMTipoCertidaoDocumento->inclusao($boTransacao);
                }
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário vincular ao menos um documento!'),"n_incluir","erro");
        }
}//fim do switch

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCEAMTipoCertidaoDocumento );

?>
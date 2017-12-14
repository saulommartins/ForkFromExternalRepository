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
    * Titulo do arquivo : Processo do Tipo de Documento Credor do TCM para o URBEM
    * Data de Criação   : 19/05/2014

    * @author Analista      Gelson
    * @author Desenvolvedor Evandro Noguez Melos

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: PRConfigurarTipoDocumentoCredor.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once "../../../../../../gestaoPatrimonial/fontes/PHP/licitacao/classes/mapeamento/TLicitacaoDocumento.class.php";
include_once (CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGdeParaDocumento.class.php");

$stPrograma = "ConfigurarTipoDocumentoCredor";
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
            $obTTCEMGdeParaDocumento = new TTCEMGdeParaDocumento();
            $obTTCEMGdeParaDocumento->recuperaDocumentosDePara( $rsDocumentosDePara, "", "", $boTransacao);

            while (!$rsDocumentosDePara->eof()) {
                $obTTCEMGdeParaDocumento->setDado('cod_doc_tce'     , $rsDocumentosDePara->getCampo('cod_doc_tce'));
                $obTTCEMGdeParaDocumento->setDado('cod_doc_urbem'   , $rsDocumentosDePara->getCampo('cod_doc_urbem'));
                $obTTCEMGdeParaDocumento->exclusao($boTransacao);
                $rsDocumentosDePara->proximo();
            }

            foreach ($arDocumentos as $arDocumentosTMP) {
                if ($arDocumentosTMP['tipo_tce'] && $arDocumentosTMP['tipo_urbem']) {
                    $obTTCEMGdeParaDocumento->setDado('cod_doc_tce'     , $arDocumentosTMP['tipo_tce']);
                    $obTTCEMGdeParaDocumento->setDado('cod_doc_urbem'   , $arDocumentosTMP['tipo_urbem']);
                    $obErro = $obTTCEMGdeParaDocumento->inclusao($boTransacao);
                }
            }
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode('É necessário vincular ao menos um documento!'),"n_incluir","erro");
        }
}//fim do switch

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCEMGdeParaDocumento );

?>
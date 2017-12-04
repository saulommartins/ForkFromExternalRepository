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
 * Classe de regra de negócio de BAIXA DE DOCUMENTOS FISCAIS
 * Data de Criação: 12/02/2009
 *
 *
 * @author Janilson Mendes P. da Silva <janilson.silva@cnm.org.br>
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * @package URBEM
 * @subpackage Regra
 *
 * Casos de uso:

 * $Id: $

 */
require_once( CAM_GT_FIS_NEGOCIO    . 'RFISEmitirDocumento.class.php');
include_once( CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoModeloDocumento.class.php');
require_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutorizacaoDocumento.class.php');

class RFISEmitirBaixaDocumentoFiscal
{

    /**
     * Método para emitir BAIXA DE DOCUMENTOS FISCAIS
     *
     * @param  array $arParametros
     * @return array
     */
    public function emitirBaixaDocFiscal(array $arParametros)
    {
        $arData = array();
        // último valor ferado por fiscalizacao.baixa_autorizacao -> cod_baixa
        $arData['#baixa']['cod_baixa']        = $arParametros['cod_baixa'];
        // Dados postados pelo form
        $arData['#form']['cod_autorizacao']  = $arParametros['cod_autorizacao'];
        $arData['#form']['observacao']       = $arParametros['stObservacoes'];
        $arData['#form']['notaInicial']      = $arParametros['inNotaFiscalInicial'];
        $arData['#form']['notaFiscal']       = $arParametros['inCodNotaFiscal'];

        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obDocumento = new TAdministracaoModeloDocumento;
        $rsDocumento = new RecordSet;
        $stFiltro  = ' WHERE cod_documento = ' . $arParametros['stCodDocumento'] . "\n";
        $stFiltro .= '   AND cod_tipo_documento = ' . $arParametros['inCodTipoDocumento'];
        $obDocumento->recuperaTodos($rsDocumento, $stFiltro);

        $arData["#data"]["dia"] = date("d");
        $arData["#data"]["mes"]	= date("m");
        $arData["#data"]["ano"]	= date("Y");

        $obTFISAutorizacaoDocumento = new TFISAutorizacaoDocumento;

        // DADOS ESTABELECIMENTO
        $obRsDadosEstabUsuario = new RecordSet;
        $stFiltroEstabUsuario = ' WHERE aeed.inscricao_economica = ' . $arParametros['inInscricaoEconomica'];
        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosEstabUsuario, $stFiltroEstabUsuario);
        // Dados para o documento
        $arData["#cgm"]["nom_cgm"]             = $obRsDadosEstabUsuario->getCampo('nom_cgm');
        $arData["#cgm"]["logradouro"]          = $obRsDadosEstabUsuario->getCampo('logradouro');
        $arData["#cgm"]['bairro']              = $obRsDadosEstabUsuario->getCampo('bairro');
        $arData["#cgm"]['cep']                 = $obRsDadosEstabUsuario->getCampo('cep');
        $arData["#cgm"]['nom_municipio']       = $obRsDadosEstabUsuario->getCampo('nom_municipio');
        $arData["#cgm"]['nom_uf']              = $obRsDadosEstabUsuario->getCampo('nom_uf');
        $arData["#cgm"]['cnpj']                = $obRsDadosEstabUsuario->getCampo('cnpj');
        $arData["#cgm"]['inscricao_economica'] = $obRsDadosEstabUsuario->getCampo('inscricao_economica');
        $arData["#cgm"]['insc_estadual']       = $obRsDadosEstabUsuario->getCampo('insc_estadual');
        $arData["#cgm"]['nom_responsavel']     = $obRsDadosEstabUsuario->getCampo('nom_responsavel');
        $arData["#cgm"]['cpf']                 = $obRsDadosEstabUsuario->getCampo('cpf');

        $arNotas = Sessao::read('arValores');
        $inCount = count($arNotas);
        for ($i = 0; $i < $inCount; $i++) {
             // "stSerie" Vem do formulário campo único, então repete.
            $arDocumentos["serie"] 	        = (string) $arParametros['stSerie'];
            $arDocumentos["numero"] 	    = (string) $arNotas[$i]['nr_nota'];
            $arDocumentos["inutilizacao"] 	= (string) $arNotas[$i]['inutilizacao'];
            $arData["#notas"][$i] = $arDocumentos;
        }
        // Emitir o documento
        $stNomDocumento = 'baixa_nota.odt';
        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $stNomDocumento, $arData);
        $arDocumento['nome_label'] = $stNomDocumento . $arParametros['inInscricaoEconomica'] . '.odt';
        $obRFISEmitirDocumento->abrir($arDocumento);

        return $arDocumento;
    }

}
?>

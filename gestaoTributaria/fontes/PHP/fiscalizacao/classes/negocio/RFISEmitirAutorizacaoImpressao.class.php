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
 * Classe de regra de negócio de AUTORIZACÃO DE IMPRESSÃO DE DOCUMENTOS FISCAIS
 * Data de Criação: 10/02/2009
 *
 *
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * @package URBEM
 * @subpackage Regra
 *
 * Casos de uso:
 *
 * $Id: $
 *
 */
require_once( CAM_GT_FIS_NEGOCIO    . 'RFISEmitirDocumento.class.php');
include_once( CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoModeloDocumento.class.php');
require_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutorizacaoDocumento.class.php');
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php' );

class RFISEmitirAutorizacaoImpressao
{

    /**
     * Método para emitir AUTORIZACÃO DE IMPRESSÃO DE DOCUMENTOS FISCAIS
     *
     * @param  array $arParametros
     * @return array
     */
    public function emitirAutorizacao(array $arParametros)
    {
        $arData = array();
        $obTFISDocumento = new TFISDocumento;
        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $arData['#form']['serie']       = $arParametros['stSerie'];
        $arData['#form']['qtdTaloes']   = $arParametros['inQuantidadeTaloes'];
        $arData['#form']['qtdVias']     = $arParametros['inQuantidadeVias'];
        $arData['#form']['observacao']  = $arParametros['stObservacoes'];
        $arData['#form']['notaInicial'] = $arParametros['inNotaFiscalInicial'];
        $arData['#form']['notaFinal']   = $arParametros['inNotaFiscalFinal'];

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

        // ESTABELECIMENTO USUARIO
        $obRsDadosEstabUsuario = new RecordSet;
        $stFiltroEstabUsuario = ' WHERE aeed.inscricao_economica = ' . $arParametros['inInscricaoEconomica'];
        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosEstabUsuario, $stFiltroEstabUsuario);
        // Dados para o documento
        $arData["#usuario"]["nom_cgm"]             = $obRsDadosEstabUsuario->getCampo('nom_cgm');
        $arData["#usuario"]["logradouro"]          = $obRsDadosEstabUsuario->getCampo('logradouro');
        $arData["#usuario"]['bairro']              = $obRsDadosEstabUsuario->getCampo('bairro');
        $arData["#usuario"]['cep']                 = $obRsDadosEstabUsuario->getCampo('cep');
        $arData["#usuario"]['nom_municipio']       = $obRsDadosEstabUsuario->getCampo('nom_municipio');
        $arData["#usuario"]['nom_uf']              = $obRsDadosEstabUsuario->getCampo('nom_uf');
        $arData["#usuario"]['cnpj']                = $obRsDadosEstabUsuario->getCampo('cnpj');
        $arData["#usuario"]['inscricao_economica'] = $obRsDadosEstabUsuario->getCampo('inscricao_economica');
        $arData["#usuario"]['insc_estadual']       = $obRsDadosEstabUsuario->getCampo('insc_estadual');
        $arData["#usuario"]['nom_responsavel']     = $obRsDadosEstabUsuario->getCampo('nom_responsavel');
        $arData["#usuario"]['cpf']                 = $obRsDadosEstabUsuario->getCampo('cpf');

        // ESTABELECIMENTO GRÁFICO
        $obRsDadosEstabGrafica = new RecordSet;
        $stFiltroEstabGrafica = ' WHERE cgm.numcgm = ' . $arParametros['inCGM'];
        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosEstabGrafica, $stFiltroEstabGrafica);
        // Dados para o documento
        $arData["#grafica"]["nom_cgm"]             = $obRsDadosEstabGrafica->getCampo('nom_cgm');
        $arData["#grafica"]["logradouro"]          = $obRsDadosEstabGrafica->getCampo('logradouro');
        $arData["#grafica"]['bairro']              = $obRsDadosEstabGrafica->getCampo('bairro');
        $arData["#grafica"]['cep']                 = $obRsDadosEstabGrafica->getCampo('cep');
        $arData["#grafica"]['nom_municipio']       = $obRsDadosEstabGrafica->getCampo('nom_municipio');
        $arData["#grafica"]['nom_uf']              = $obRsDadosEstabGrafica->getCampo('nom_uf');
        $arData["#grafica"]['cnpj']                = $obRsDadosEstabGrafica->getCampo('cnpj');
        $arData["#grafica"]['inscricao_economica'] = $obRsDadosEstabGrafica->getCampo('inscricao_economica');
        $arData["#grafica"]['insc_estadual']       = $obRsDadosEstabGrafica->getCampo('insc_estadual');
        $arData["#grafica"]['nom_responsavel']     = $obRsDadosEstabGrafica->getCampo('nom_responsavel');
        $arData["#grafica"]['cpf']                 = $obRsDadosEstabGrafica->getCampo('cpf');

        $arData["#grafica"]['aidf'] = $arParametros['inNumAutorizacao'];

        // Emitir o documento
        $stNomDocumento = 'autorizacao_impressao_doc_fiscal.odt';
        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $stNomDocumento, $arData);
        $arDocumento['nome_label'] = $stNomDocumento . $arParametros['inInscricaoEconomica'] . '.odt';
        $obRFISEmitirDocumento->abrir($arDocumento);

        return $arDocumento;
    }

}
?>

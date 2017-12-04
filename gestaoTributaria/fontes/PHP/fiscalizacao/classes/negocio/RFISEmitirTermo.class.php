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
 * Classe de Regra para Emitir Termo
 * Data de Criação: 11/11/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Marcio Medeiros

 * @package URBEM
 * @subpackage Regra

 * $Id: RFISEmitirTermo.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISTipoTermo.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISNotificacaoTermo.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISNotificacaoTermoInfracao.class.php' );
include_once( CAM_GT_FIS_NEGOCIO . 'RFISNotificarProcesso.class.php' );

class RFISEmitirTermo
{

    /**
    * @var Transacao
    */
    private $obTransacao;

    /**
    * Armazena os erros da camada
    *
    * @var string
    */
    private $erro;

    /**
     * Objeto TFISNotificacaoTermo
     *
     * @var object
     */
    private $obTFISNotificacaoTermo;

    /**
     * Objeto TFISNotificacaoTermoInfracao
     *
     * @var object
     */
    private $obTFISNotificacaoTermoInfracao;

    /**
     * Método construtor
     */
    public function __construct()
    {
        $this->obTransacao = new Transacao();
        $this->obTFISNotificacaoTermo = new TFISNotificacaoTermo;
        $this->obTFISNotificacaoTermoInfracao = new TFISNotificacaoTermoInfracao;
    }

    /**
     * Retorna os erros da camada Negócio
     *
     * @return string
    */
    public function getErro()
    {
        return $this->erro;
    }

    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stCriterio   o critério que delimita a busca
     * @return RecordSet
     */
    protected function callMapeamento($stMapeamento, $stMetodo, $stCriterio = "", $stOrdem = "")
    {
        $obMapeamento = new $stMapeamento();
        $obMapeamento->$stMetodo( $obRecordSet, $stCriterio, $stOrdem );

        return $obRecordSet;
    }

    /**
     * Recupera os tipos de Termo
     *
     * @param  int       $inCodtipoTermo
     * @return recordSet
     */
    public function recuperaTipoTermo($inCodtipoTermo = 0)
    {
        $stMapeamento = "TFISTipoTermo";
        $stMetodo     = "recuperaTipoTermo";
        $stCriterio   = "";
        $stOrdem      = " ORDER BY nom_termo DESC";

        if ($inCodtipoTermo > 0) {
            $stCriterio = " WHERE cod_termo = " . $inCodtipoTermo;
        }

        return $this->callMapeamento( $stMapeamento, $stMetodo, $stCriterio, $stOrdem );
    }

    /**
     * Inclui um Termo de Embargo, Demolição ou Interdição
     *
     * @param  array $arParametros
     * @return bool
     */
    public function incluir(array $arParametros)
    {
        $arInfracoes = Sessao::read( 'arInfracoes' );
        if (count($arInfracoes) == 0) {
            $this->erro = 'Informe pelo menos uma Infração!';

            return false;
        }

        $obTFISFiscal = new TFISFiscal;
        $obTFISFiscal->recuperaListaFiscais( $rsListaFiscal, " AND fiscal.numcgm = ".Sessao::read('numCgm') );
        unset( $obTFISFiscal );

        if ( $rsListaFiscal->Eof() ) {
            $obErro = new Erro;
            $obErro->setDescricao( "Fiscal não encontrado!" );
        } else {
            $this->obTFISNotificacaoTermo->setDado('cod_processo', $arParametros['hdnCodProcessoFiscal']);
            $this->obTFISNotificacaoTermo->setDado('cod_fiscal', $rsListaFiscal->getCampo("cod_fiscal") );
            $this->obTFISNotificacaoTermo->setDado('cod_tipo_documento', $arParametros['inCodTipoDocumento']);
            $this->obTFISNotificacaoTermo->setDado('cod_documento', $arParametros['stCodDocumento']);
            $this->obTFISNotificacaoTermo->setDado('dt_notificacao', $arParametros['stDataNotificacao']);
            $this->obTFISNotificacaoTermo->setDado('observacao', $arParametros['stObservacoes']);
            $this->obTFISNotificacaoTermo->setDado('timestamp', date('Y-m-d h:m:s'));
            // Incluir o Termo
            Sessao::setTrataExcecao( true );
            Sessao::getTransacao()->setMapeamento( $this->obTFISNotificacaoTermo );
                $obErro = $this->obTFISNotificacaoTermo->inclusao();
                if ( !$obErro->ocorreu() ) {
                    if ( !$this->incluirNotificacaoTermoInfracao($arParametros, $arInfracoes ) ) {
                        $obErro = new Erro;
                        $obErro->setDescricao( "Rotina 'IncluirNotificacaoTermoInfracao'" );
                    }
                }
            Sessao::encerraExcecao();
        }

        if ($obErro->ocorreu()) {
            $this->erro  = 'Erro ao incluir o Termo: ';
            $this->erro .= $obErro->getDescricao();
            SistemaLegado::exibeAviso(urlencode($this->erro), 'n_' . __FUNCTION__, 'erro');

            return false;
        } else {
            SistemaLegado::alertaAviso('LSEmitirTermo.php?stAcao='.$_REQUEST['hdnTipoTermo'], $arParametros['hdnCodProcessoFiscal'], __FUNCTION__, 'aviso', Sessao::getID(), '../');
            //vou trazer pra ca a parte de imprimir o termo
            // realiza o download dos termos...
            $this->emitirTermo($arParametros);

            return true;
        }

    }

    /**
     * Grava a associação Notificção Termo -> Intração
     *
     * @param  array $arParametros
     * @return bool
     */
    private function incluirNotificacaoTermoInfracao(array &$arParametros, array $arInfracoes)
    {
        $rsNumNotificacao = null;
        $this->obTFISNotificacaoTermo->recuperaUltimoNumNotificacao($rsNumNotificacao);
        $inNumNotificacao = (int) $rsNumNotificacao->arElementos[0]['num_notificacao'];
        $arParametros['inCodNotificacaoProcesso'] = $inNumNotificacao;
        if ($inNumNotificacao == 0) {
            $this->erro = 'não foi possível recuperar o número da notificação';

            return false;
        }
        // Gravar lista de Infrações
        foreach ($arInfracoes as $arCamposInfracao) {
            // Incluir dados do relacionamento
            $this->obTFISNotificacaoTermoInfracao->setDado('cod_processo', $arParametros['hdnCodProcessoFiscal']);
            $this->obTFISNotificacaoTermoInfracao->setDado('cod_infracao', $arCamposInfracao['cod_infracao']);
            $this->obTFISNotificacaoTermoInfracao->setDado('num_notificacao', $inNumNotificacao);
            $this->obTFISNotificacaoTermoInfracao->setDado('observacao', $arCamposInfracao['observacao']);

            $obErro = $this->obTFISNotificacaoTermoInfracao->inclusao();
            if ( $obErro->ocorreu() ) {
                $this->erro  = 'Erro ao incluir as infrações: ';
                $this->erro .= $obErro->getDescricao();

                return false;
            }
        }

        return true;
    }

    public function emitirTermo($param)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obRFISProcessoFiscal = new RFISProcessoFiscal;
        $obTFISDocumento = new TFISDocumento;
        $arData = $obRFISProcessoFiscal->emitirTermo($param);
        //dados fiscal
        $obRFISProcessoFiscal->setCriterio("sw_cgm.numcgm = ".Sessao::read('numCgm'));
        $rsFiscal = $obRFISProcessoFiscal->getFiscal();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $arData["#fiscal"]['nom_fiscal']        = (string) $rsFiscal->getCampo('nom_cgm');
        $arData["#fiscal"]['nom_cargo']         = (string) $rsFiscal->getCampo('descricao');
        $arData["#fiscal"]['num_matricula']     = (string) $rsFiscal->getCampo('registro');

        //dados data
        $arData["#data"]["dia"]                 = date("d");
        $arData["#data"]["mes"]	                = date("m");
        $arData["#data"]["ano"]	                = date("Y");

        $obRFISProcessoFiscal->setCriterio("cod_documento = ".$param['stCodDocumentoTxt']);
        $rsDocumento = $obRFISProcessoFiscal->getDocumento();
        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $rsDocumento->getCampo('nome_arquivo_agt'), $arData);
        $arDocumento["nome_label"] = $rsDocumento->getCampo('nome_arquivo_agt');
        $obRFISEmitirDocumento->abrir($arDocumento);
    }

}

?>

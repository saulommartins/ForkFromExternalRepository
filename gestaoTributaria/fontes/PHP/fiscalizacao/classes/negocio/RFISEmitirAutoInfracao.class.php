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
 * Classe de Regra para Emitir Auto de Infração
 * Data de Criação: 21/08/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Regra

 $Id: RFISEmitirAutoInfracao.class.php 65763 2016-06-16 17:31:43Z evandro $

 * Casos de uso:
 */

include_once( CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoModeloDocumento.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidade.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISInfracao.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutoInfracao.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutoInfracaoMulta.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutoInfracaoOutros.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutoFiscalizacao.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISDocumento.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISFiscal.class.php' );
include_once( CAM_GT_FIS_MAPEAMENTO . 'TFISProcessoFiscal.class.php' );
include_once( CAM_GT_FIS_NEGOCIO    . 'RFISConfiguracao.class.php' );
include_once( CAM_GT_FIS_NEGOCIO    . 'RFISEmitirDocumento.class.php' );
include_once( CAM_GT_FIS_NEGOCIO    . 'RFISProcessoFiscal.class.php' );

class RFISEmitirAutoInfracao
{
    private $inCodAutoFiscalizacao;

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

    public function recuperaListaNotificacaoInfracao($inCodProcesso)
    {
        $stMapeamento = "TFISInfracao";
        $stMetodo     = "recuperaListaNotificacaoInfracao";
        $stCriterio   = "";
        $stOrdem      = "cod_infracao DESC";

        if ($inCodProcesso) {
            $stCriterio = " cod_processo = " . $inCodProcesso;
        }

        return $this->callMapeamento( $stMapeamento, $stMetodo, $stCriterio, $stOrdem );
    }

    public function recuperaAutoFiscalizacao($inCodProcesso)
    {
        $stMapeamento = "TFISAutoFiscalizacao";
        $stMetodo     = "recuperaAutoFiscalizacao";
        $stCriterio   = " WHERE ainf.cod_processo = " . $inCodProcesso;

        return $this->callMapeamento( $stMapeamento, $stMetodo, $stCriterio);
    }

    private function incluirAutoFiscalizacao($dtNotificacao, $inCodProcesso, $stObservacoes, $inCodTipoDocumento, $inCodDocumento, &$boTransacao)
    {
        $timestamp = date('Y-m-d h:m:s');
        $obTFISFiscal = new TFISFiscal();
        $obErro = $obTFISFiscal->recuperaTodos( $rsFiscal, " WHERE numcgm = " . Sessao::read('numCgm') );

        if ( $obErro->ocorreu() ) {
            return $obErro;
        }

        if (! $rsFiscal->eof() ) {
            $inCodFiscal = $rsFiscal->getCampo( "cod_fiscal" );
        }

        # auto_fiscalizacao.
        $obTFISAutoFiscalizacao = new TFISAutoFiscalizacao();
        $obTFISAutoFiscalizacao->proximoCod( $this->inCodAutoFiscalizacao, $boTransacao );
        $obTFISAutoFiscalizacao->setDado( "cod_auto_fiscalizacao", $this->inCodAutoFiscalizacao );
        $obTFISAutoFiscalizacao->setDado( "cod_processo", $inCodProcesso );
        $obTFISAutoFiscalizacao->setDado( "cod_fiscal", $inCodFiscal );
        $obTFISAutoFiscalizacao->setDado( "cod_tipo_documento", $inCodTipoDocumento );
        $obTFISAutoFiscalizacao->setDado( "cod_documento", $inCodDocumento );
        $obTFISAutoFiscalizacao->setDado( "dt_notificacao", $dtNotificacao );
        $obTFISAutoFiscalizacao->setDado( "observacao", $stObservacoes );
        $obTFISAutoFiscalizacao->setDado( "timestamp", $timestamp);
        $obTFISAutoFiscalizacao->inclusao( $boTransacao );
        $obTFISAutoFiscalizacao->debug();

        return $obErro;
    }

    private function incluirAutoInfracao($inCodProcesso, array $arCamposInfracao, array &$arPenalidades, $inCodTipoDocumento, $inCodDocumento, &$boTransacao)
    {
        $obErro = new Erro();
        $inCodInfracao = $arCamposInfracao['cod_infracao'];

        foreach ($arPenalidades as $arCamposPenalidade) {
            $inCodPenalidade = $arCamposPenalidade['cod_penalidade'];

            # auto_infracao.
            $obTFISAutoInfracao = new TFISAutoInfracao();
            $obTFISAutoInfracao->setDado( "cod_processo", $inCodProcesso );
            $obTFISAutoInfracao->setDado( "cod_auto_fiscalizacao", $this->inCodAutoFiscalizacao );
            $obTFISAutoInfracao->setDado( "cod_penalidade", $inCodPenalidade );
            $obTFISAutoInfracao->setDado( "cod_infracao", $inCodInfracao );
            $obTFISAutoInfracao->setDado( "observacao", $arCamposInfracao['observacao'] );
            $obTFISAutoInfracao->setDado( "cod_tipo_documento", $inCodTipoDocumento );
            $obTFISAutoInfracao->setDado( "cod_documento", $inCodDocumento );
            $obErro = $obTFISAutoInfracao->inclusao( $boTransacao );
            $obTFISAutoInfracao->debug();

            if ( $obErro->ocorreu() ) {
                return $obErro;
            }

            if ($arCamposPenalidade['cod_tipo_penalidade'] == 1) {
                # auto_infracao_multa
                $obTFISAutoInfracaoMulta = new TFISAutoInfracaoMulta();
                $obTFISAutoInfracaoMulta->setDado( "cod_processo", $inCodProcesso );
                $obTFISAutoInfracaoMulta->setDado( "cod_auto_fiscalizacao", $this->inCodAutoFiscalizacao );
                $obTFISAutoInfracaoMulta->setDado( "cod_penalidade", $inCodPenalidade );
                $obTFISAutoInfracaoMulta->setDado( "cod_infracao", $inCodInfracao );
                $obTFISAutoInfracaoMulta->setDado( "valor", $arCamposPenalidade['valor'] );
                $obTFISAutoInfracaoMulta->setDado( "quantidade", $arCamposPenalidade['quantidade'] );
                $obErro = $obTFISAutoInfracaoMulta->inclusao( $boTransacao );
                $obTFISAutoInfracaoMulta->debug();
            } else {
                # auto_infracao_outros
                $obTFISAutoInfracaoOutros = new TFISAutoInfracaoOutros();
                $obTFISAutoInfracaoOutros->setDado( "cod_processo", $inCodProcesso );
                $obTFISAutoInfracaoOutros->setDado( "cod_auto_fiscalizacao", $this->inCodAutoFiscalizacao );
                $obTFISAutoInfracaoOutros->setDado( "cod_penalidade", $inCodPenalidade );
                $obTFISAutoInfracaoOutros->setDado( "cod_infracao", $inCodInfracao );
                $obTFISAutoInfracaoOutros->setDado( "dt_ocorrencia", $arCamposPenalidade['dt_ocorrencia'] );
                $obTFISAutoInfracaoOutros->setDado( "observacao", $arCamposPenalidade['observacao'] );
                $obErro = $obTFISAutoInfracaoOutros->inclusao( $boTransacao );
                $obTFISAutoInfracaoOutros->debug();
            }

            if ( $obErro->ocorreu() ) {
                return $obErro;
            }
        }

        return $obErro;
    }

    public function tramitaAutoInfracao($arParametros)
    {
        $arInfracoes = Sessao::read( 'arInfracoes' );
        $arPenalidades = Sessao::read( 'arPenalidades' );

        # Parâmetros do formulário.
        $dtNotificacao = $arParametros['dtNotificacao'];
        $inCodProcesso = $arParametros['inCodProcesso'];
        $stObservacoes = $arParametros['stObservacoes'];
        $inCodTipoDocumento = $arParametros['inCodTipoDocumento'];
        $stCodDocumento = $arParametros['stCodDocumento'];

        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( $obErro->ocorreu() ) {
            return $obErro;
        }

        $obErro = $this->incluirAutoFiscalizacao( $dtNotificacao, $inCodProcesso, $stObservacoes, $inCodTipoDocumento, $stCodDocumento, $boTransacao );

        if (! $obErro->ocorreu() ) {
            foreach ($arInfracoes as $arCamposInfracao) {
                $inCodInfracao = $arCamposInfracao['cod_infracao'];
                $arPenalidadesInfracao = $arPenalidades[$inCodInfracao];
                $obErro = $this->incluirAutoInfracao( $inCodProcesso, $arCamposInfracao, $arPenalidadesInfracao, $inCodTipoDocumento, $stCodDocumento, $boTransacao );

                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }

        if (! $obErro->ocorreu() ) {
            $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, new TFISAutoInfracao() );
            $arParametros["inCodAutoFiscalizacao"] = $this->inCodAutoFiscalizacao;
            $this->emitirAutoInfracao( $arParametros );
        }
    }

    public function emitirAutoInfracao($arParametros)
    {
        $obTFISDocumento = new TFISDocumento;
        $obRFISProcessoFiscal  = new RFISProcessoFiscal;
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $stCriterio = 'where pf.cod_processo = '.$arParametros["inCodProcesso"];
        $rsProcesso = $this->callMapeamento("TFISProcessoFiscal", "recuperaEnderecoProcesso", $stCriterio);

        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        //dados processo
        $arData["#processo"]["num_processo"]    = (string) $arParametros['inCodProcesso'];

        //dados cgm
        $arData["#cgm"]["nom_cgm"]              = (string) $rsProcesso->getCampo('nom_cgm');
        $arData["#cgm"]["logradouro"]           = (string) $rsProcesso->getCampo('logradouro_i');
        $arData["#cgm"]["nom_bairro"]           = (string) $rsProcesso->getCampo('nom_bairro');
        $arData["#cgm"]["cep"]                  = (string) $rsProcesso->getCampo('cep');
        $arData["#cgm"]["nom_municipio"]        = (string) $rsProcesso->getCampo('nom_municipio');
        $arData["#cgm"]["nom_uf"]               = (string) $rsProcesso->getCampo('sigla_uf');

        if ($rsProcesso->getCampo('cpf') == '') {
           $arData["#cgm"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cnpj');
        } else {
           $arData["#cgm"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cpf');
        }

        $arData["#cgm"]["inscricao_economica"]  = (string) $rsProcesso->getCampo('inscricao_economica');
        $arData["#cgm"]["nom_atividade"]        = (string) $rsProcesso->getCampo('nom_atividade');

        $stCriterio = ' where cod_processo = '.$arParametros["inCodProcesso"];
        $rsAutoFiscalizacao = $this->callMapeamento("TFISAutoFiscalizacao", "recuperaTodos", $stCriterio);

        //dados data
        $arDate = explode(' ', $rsAutoFiscalizacao->getCampo("timestamp"));
        $arData["#autoinfracao"]["data"]        = sistemaLegado::dataExtenso($arDate[0]);

        $arHora = explode(':', $arDate[1]);
        $arData["#autoinfracao"]["hora"]        = $arHora[0];
        $arData["#autoinfracao"]["minu"]        = $arHora[1];
        $arData["#autoinfracao"]["cod_auto_fiscalizacao"] = $arParametros['inCodAutoFiscalizacao'];
        $arData["#autoinfracao"]["ano_auto_fiscalizacao"] = Sessao::read("exercicio");

        //dados infrações
        $stCriterio = "ninf.cod_processo = ".$arParametros['inCodProcesso'];
        $stCriterio.= " AND afis.cod_auto_fiscalizacao = ".$arParametros['inCodAutoFiscalizacao'];
        $obRFISProcessoFiscal->setCriterio($stCriterio);
        $rsInfracoes = $obRFISProcessoFiscal->getInfracoes();

        $valorTotal = 0;
        for ($j = 0; $j < count($rsInfracoes->arElementos); $j++) {
            $arInfracoes["nom_penalidade"]      = (string) $rsInfracoes->arElementos[$j]["nom_penalidade"];
            $arInfracoes["nom_norma"]           = (string) $rsInfracoes->arElementos[$j]["nom_norma"];
            $arInfracoes["nom_infracao"]        = (string) $rsInfracoes->arElementos[$j]["nom_infracao"];
            $arInfracoes["num_penalidade"]      = (string) $rsInfracoes->arElementos[$j]["cod_penalidade"];
            $arInfracoes["data_infracao"]       = (string) $rsInfracoes->arElementos[$j]["dt_ocorrencia"];
            $arInfracoes["valor"]               = (string) $rsInfracoes->arElementos[$j]["valor"];

            $valorTotal                         = $valorTotal + $rsInfracoes->arElementos[$j]["valor"];

            $arData["#infracao"][$j]            = $arInfracoes;
        }

        $arData["#valor"]['total']              = $valorTotal;

        //dados fiscal
        $obRFISProcessoFiscal->setCriterio("sw_cgm.numcgm = ".Sessao::read('numCgm'));
        $rsFiscal = $obRFISProcessoFiscal->getFiscal();

        $arData["#fiscal"]['nom_fiscal']        = (string) $rsFiscal->getCampo('nom_cgm');
        $arData["#fiscal"]['nom_cargo']         = (string) $rsFiscal->getCampo('descricao');
        $arData["#fiscal"]['num_matricula']     = (string) $rsFiscal->getCampo('registro');

        //dados data
        $arData["#data"]["dia"]                 = date("d");
        $arData["#data"]["mes"]	                = date("m");
        $arData["#data"]["ano"]	                = date("Y");

        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . "auto_infracao.odt", $arData);
        $arDocumento["nome_label"] = "auto_de_infracao_".$arParametros['inCodAutoFiscalizacao'].".odt";

        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obRFISEmitirDocumento->abrir($arDocumento);

        $stMensagem = 'Auto de infração emitido com sucesso! ('.$arParametros['inCodAutoFiscalizacao'].') ';

        return sistemaLegado::alertaAviso("FLEmitirAutoInfracao.php?stAcao=emitir",$stMensagem, "emitir", "aviso", Sessao::getId());
    }

    public function verificarAutoFiscalizacao($inCodProcesso, $inCodAutoFiscalizacao = null)
    {
        $obTFISAutoFiscalizacao = new TFISAutoFiscalizacao();
        $stCriterio = " WHERE cod_processo = " . $inCodProcesso;

        if ($inCodAutoFiscalizacao)
            $stCriterio.= " AND cod_auto_fiscalizacao = " . $inCodAutoFiscalizacao;

        $obTFISAutoFiscalizacao->recuperaTodos( $rsAutoFiscalizacao, $stCriterio );

        if (! $rsAutoFiscalizacao->eof() ) {
            return true;
        }

        return false;
    }

}

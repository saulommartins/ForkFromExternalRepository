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
 * Classe de Regra para Notificar Processo
 * Data de Criação: 11/11/2008

 * @author Analista    : Heleno Menezes dos Santos
 * @author Programador : Jânio Eduardo Vasconcellos de Magalhaes

 * @package URBEM
 * @subpackage Regra

 $Id: RFISNotificarProcesso.class.php 65763 2016-06-16 17:31:43Z evandro $

 * Casos de uso:
 */

include_once CAM_GA_ADM_MAPEAMENTO . 'TAdministracaoModeloDocumento.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISPenalidade.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISInfracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISNotificacaoInfracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISNotificacaoFiscalizacao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISFiscal.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISServicoComRetencao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO . 'TFISDocumento.class.php';
include_once CAM_GT_FIS_NEGOCIO . 'RFISProcessoFiscal.class.php';
include_once CAM_GT_FIS_NEGOCIO . 'RFISConfiguracao.class.php';
include_once CAM_GT_FIS_NEGOCIO . 'RFISEmitirDocumento.class.php';

class RFISNotificarProcesso
{
    /**
     * Invoca classe de mapeamento com chamada e critério em uma só etapa.
     * @param  string    $stMapeamento o nome da classe de mapeamento
     * @param  string    $stMetodo     o método invocado para a classe de mapeamento
     * @param  string    $stCriterio   o critério que delimita a busca
     * @return RecordSet
     */
private $codDocumento;
private $codTipoDocumento;
private $codProcesso;

public function getCodDocumento() {return $this->codDocumento;}
public function setCodDocumento($valor) {return $this->codDocumento = $valor;}
public function getCodProcesso() {return $this->codProcesso;}
public function setCodProcesso($valor) {return $this->codProcesso = $valor;}

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

    private function incluirNotificacaoFiscalizacao($numCGM, $dtNotificacao, $inCodProcesso, $stObservacoes, &$boTransacao)
    {
        # Obtém código do fiscal.
        $obTFISFiscal = new TFISFiscal();
        $obErro = $obTFISFiscal->recuperaTodos( $rsFiscal, " WHERE numcgm = " . $numCGM );

        if ( $obErro->ocorreu() ) {
            return $obErro;
        }

        if (! $rsFiscal->eof() ) {
            $inCodFiscal = $rsFiscal->getCampo( "cod_fiscal" );
        }

        # Obtém código do documento e tipo.
        $obRFISConfiguracao = new RFISConfiguracao();
        $obRFISConfiguracao->consultar();
        $inCodDocumento = $this->getCodDocumento();

        if ( is_numeric( $inCodDocumento )) {
            $obTAdministracaoModeloDocumento = new TAdministracaoModeloDocumento();
            $obErro = $obTAdministracaoModeloDocumento->recuperaTodos( $rsModeloDocumento, " WHERE cod_documento = " . $inCodDocumento );

            if ( $obErro->ocorreu() ) {
                return $obErro;
            }

            if (! $rsModeloDocumento->Eof() ) {
                $inCodTipoDocumento = $rsModeloDocumento->getCampo( "cod_tipo_documento" );
            }
        }

        if (! ( $inCodTipoDocumento && $inCodDocumento ) ) {
            $obErro->setDescricao( "documento e tipo de documento não configurado. ()" );

            return $obErro;
        }

        # Inclui notificacao_fiscalizacao.
        $obTFISNotificacaoFiscalizacao = new TFISNotificacaoFiscalizacao();
        $stFiltro = " WHERE exercicio = ".Sessao::read('exercicio');
        $obTFISNotificacaoFiscalizacao->recuperaProximoNumNotificacao( $rsLista );

        if ( $rsLista->Eof() )
            $inNumNotificacao = 1;
        else
            $inNumNotificacao = $rsLista->getCampo( "num_notificacao" ) + 1;

        $obTFISNotificacaoFiscalizacao->setDado( "cod_processo", $inCodProcesso );
        $obTFISNotificacaoFiscalizacao->setDado( "cod_fiscal", $inCodFiscal );
        $obTFISNotificacaoFiscalizacao->setDado( "cod_tipo_documento", $inCodTipoDocumento );
        $obTFISNotificacaoFiscalizacao->setDado( "cod_documento", $inCodDocumento );
        $obTFISNotificacaoFiscalizacao->setDado( "dt_notificacao", $dtNotificacao );
        $obTFISNotificacaoFiscalizacao->setDado( "observacao", $stObservacoes );
        $obTFISNotificacaoFiscalizacao->setDado( "num_notificacao", $inNumNotificacao );
        $obTFISNotificacaoFiscalizacao->setDado( "exercicio", Sessao::read('exercicio') );
        $obTFISNotificacaoFiscalizacao->inclusao( $boTransacao );

        return $obErro;
    }

    private function incluirNotificacaoInfracao($inCodProcesso, $stCodDocumento, $stCodTipDoc, array $arCamposInfracao,&$boTransacao)
    {
        $obErro = new Erro();
        $inCodInfracao = $arCamposInfracao['cod_infracao'];
        $arPenalidades = $arCamposInfracao["arPenalidades"];
        # Cria elemento notificacao_infracao.
        $obTFISNotificacaoInfracao = new TFISNotificacaoInfracao();
        for ( $inX=0; $inX<count($arPenalidades); $inX++ ) {
            $obTFISNotificacaoInfracao->setDado( "cod_penalidade", $arPenalidades[$inX]["cod_penalidade"] );
            $obTFISNotificacaoInfracao->setDado( "cod_infracao", $inCodInfracao );
            $obTFISNotificacaoInfracao->setDado( "cod_processo", $inCodProcesso );
            $obTFISNotificacaoInfracao->setDado( "cod_tipo_documento", $stCodTipDoc );
            $obTFISNotificacaoInfracao->setDado( "cod_documento", $stCodDocumento );
            $obTFISNotificacaoInfracao->setDado( "observacao", $arCamposInfracao['observacao'] );
            $obTFISNotificacaoInfracao->setDado( "valor", $arPenalidades[$inX]["valor"] );
            $obTFISNotificacaoInfracao->setDado( "quantidade", $arPenalidades[$inX]["quantidade"] );
            $obErro = $obTFISNotificacaoInfracao->inclusao( $boTransacao );
            if ( $obErro->ocorreu() )
                return $obErro;
        }

        return $obErro;
    }

    public function tramitaNotificacao($arParametros)
    {
        $arInfracoes = Sessao::read( 'arInfracoes' );

        if (count($arInfracoes)==0) {
             return sistemaLegado::exibeAviso("É necessário vincular infrações a esta notificacao",'n_incluir','aviso' );
        }

        $arPenalidades = Sessao::read( 'arPenalidades' );
        for ( $inX=0; $inX<count($arInfracoes); $inX++ ) {
            $arInfracoes[$inX]["arPenalidades"] = $arPenalidades[ $arInfracoes[$inX]["cod_infracao"] ];
        }

        # Parâmetros do formulário.
        $dtNotificacao = $arParametros['dtNotificacao'];
        $inCodProcesso = $arParametros['inCodProcesso'];
        $stObservacoes = $arParametros['stObservacoes'];
        $stCodDocumento = $arParametros['stCodDocumento'];
        $stCodTipDoc = $arParametros['inCodTipoDocumento'];

        $this->setCodDocumento($arParametros['stCodDocumentoTxt']);

        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
        $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( $obErro->ocorreu() ) {
            return $obErro;
        }

        $obErro = $this->incluirNotificacaoFiscalizacao( Sessao::read( 'numCgm' ), $dtNotificacao, $inCodProcesso, $stObservacoes, $boTransacao );

        if (! $obErro->ocorreu() ) {
            foreach ($arInfracoes as $arCamposInfracao) {

                $obErro = $this->incluirNotificacaoInfracao( $inCodProcesso, $stCodDocumento, $stCodTipDoc, $arCamposInfracao, $boTransacao );

                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }

        $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, new TFISNotificacaoInfracao() );

        $arData = $this->emitirNotificacao($arParametros);
        sistemaLegado::alertaAviso("FLManterProcesso.php?stAcao=notificar",$this->getCodProcesso(), "incluir", "aviso", Sessao::getId());
        $this->imprimir($arData);
    }

    public function verificarNotificacao($inCodProcesso)
    {
        $obTNotificacaoFiscalizacao = new TFISNotificacaoFiscalizacao;
        $rsNotificacaoFiscalizacao = new RecordSet;
        $obTNotificacaoFiscalizacao->recuperaTodos($rsNotificacaoFiscalizacao,' where cod_processo = '.$inCodProcesso );

        if (!$rsNotificacaoFiscalizacao->Eof() ) {
            return true;
        }

        return false;
    }

    public function emitirNotificacao($param)
    {
        $this->setCodProcesso($param['inCodProcesso']);
        $obRFISProcessoFiscal  = new RFISProcessoFiscal;
        $stCriterio = 'where pf.cod_processo = '.$this->getCodProcesso();
        $rsProcesso = $this->callMapeamento("TFISProcessoFiscal", "recuperaEnderecoProcesso", $stCriterio);

        $obTFISDocumento = new TFISDocumento;

        $obTNotificacaoFiscalizacao = new TFISNotificacaoFiscalizacao;
        $rsNotificacaoFiscalizacao = new RecordSet;
        $obTNotificacaoFiscalizacao->recuperaTodos($rsNotificacaoFiscalizacao,' where cod_processo = '.$this->getCodProcesso() );
        $this->setCodDocumento($rsNotificacaoFiscalizacao->getCampo('cod_documento'));
        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        //dados processo
        $arData["#processo"]["num_processo"]    = (string) $this->getCodProcesso();

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

        //dados data
        $arDate = explode(' ', $rsNotificacaoFiscalizacao->getCampo("timestamp"));
        $arDate["#notificacao"]["data"]        = sistemaLegado::dataExtenso($arDate[0]);

        $arHora = explode(':', $atDate[1]);
        $arData["#notificacao"]["hora"]        = $arHora[0];
        $arData["#notificacao"]["minu"]        = $arHora[1];

        //dados infrações
        $stFiltro = "where ninf.cod_processo = ".$this->getCodProcesso();
        $obTNotificacaoFiscalizacao->recuperaNotificacaoInfracao($rsInfracoes,$stFiltro);

        $valorTotal = 0;
        for ($j = 0; $j < count($rsInfracoes->arElementos); $j++) {
            $arInfracoes["nom_infracao"]        = (string) $rsInfracoes->arElementos[$j]["nom_infracao"];
            $arInfracoes["num_infracao"]        = (string) $rsInfracoes->arElementos[$j]["num_infracao"];
            $arObservacoes["observacao"]        = (string) $rsInfracoes->arElementos[$j]["infracao_observacao"];
            $arNorma["norma"]                   = (string) $rsInfracoes->arElementos[$j]["nom_norma"];
            $arData["#infracao_observacao"][$j] = $arObservacoes;
            $arData["#infracao"][$j]            = $arInfracoes;
            $arData["#norma"][$j]               = $arNorma;
        }

        $arData["#valor"]['total']              = $valorTotal;
        $arData["#notificacao"]['observacao']         = $rsNotificacaoFiscalizacao->getCampo("observacao");
        $arData["#notificacao"]['num_notificacao']    = $rsNotificacaoFiscalizacao->getCampo("num_notificacao");
        $arData["#notificacao"]['exercicio_notificacao']    = $rsNotificacaoFiscalizacao->getCampo("exercicio");
        $arData["#penalidade"]['prazo']    = $rsNotificacaoFiscalizacao->getCampo("prazo");
        $arData["#penalidade"]['desconto']    = $rsNotificacaoFiscalizacao->getCampo("desconto");
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

        $obTNotificacaoFiscalizacao->recuperaTotalLevantamento($rsLevantamentoFiscal,"where fl.cod_processo = ".$this->getCodProcesso());

        $arData["#montante"]["tributo"]  = $rsLevantamentoFiscal->getCampo('max_pagar');
        $arData["#montante"]["correcao"] = $rsLevantamentoFiscal->getCampo('max_valor_c');
        $arData["#montante"]["multa"]    = $rsLevantamentoFiscal->getCampo('max_valor_mora');
        $arData["#montante"]["juros"]    = $rsLevantamentoFiscal->getCampo('max_valor_juros');
        $arData["#montante"]["total"]    = $rsLevantamentoFiscal->getCampo('max_pagar')+$inDescontomulta+$rsLevantamentoFiscal->getCampo('max_valor_mora')+$rsLevantamentoFiscal->getCampo('max_valor_juros');

        #Formula de desconto

        return $arData;
    }

    public function imprimir($arData)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obDocumento = new TAdministracaoModeloDocumento;
        $rsDocumento = new RecordSet;
        $obDocumento->recuperaTodos($rsDocumento,' where cod_documento = '.$this->getCodDocumento());
        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $rsDocumento->getCampo('nome_arquivo_agt'), $arData);
        $arDocumento['nome_label'] = 'Notificacao_fiscal_processo'.$this->getCodProcesso().".odt";

        $obRFISEmitirDocumento->abrir($arDocumento);
    }

    public function montaDocumentoInfracao()
    {
        $obTNotificacaoFiscalizacao = new TFISNotificacaoFiscalizacao;
        $rsNotificacaoFiscalizacao = new RecordSet;
        $obTNotificacaoFiscalizacao->recuperaTodos($rsNotificacaoFiscalizacao,' where cod_processo = '.$this->getCodProcesso() );
        $this->setCodDocumento($rsNotificacaoFiscalizacao->getCampo('cod_documento'));
        $arData["#processo"]["testa"] = 'Teste processo fiscal';
    }

    public function verificaServicoComRetencao($inCodProcesso)
    {
        $obTFISServicoComRetencao = new TFISServicoComRetencao();
        $rsServicos = new RecordSet();
        $obTFISServicoComRetencao->recuperaTodos($rsServicos, ' WHERE cod_processo = ' . $inCodProcesso);

        return !$rsServicos->eof();
    }
}

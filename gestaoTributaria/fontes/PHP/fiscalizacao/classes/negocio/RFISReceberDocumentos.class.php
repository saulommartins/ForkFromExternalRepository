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
    * Classe de regra de negócio para Prorrogar Recebimento de Documentos
    * Data de Criação: 13/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Zainer Cruz dos Santos Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacao.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacaoDocumentos.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProrrogacaoEntrega.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.'RFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumentosEntrega.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.   'RFISIniciarProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutorizacaoDocumento.class.php');
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php' );

class RFISReceberDocumentos extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListaInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperaListaInicioFiscalizacaoEconomica";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getListaInicioFiscalizacaoEconomicaObra()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperaListaInicioFiscalizacaoEconomicaObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaInicioFiscalizacaoObra()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperaListaInicioFiscalizacaoObra";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperarInicioFiscalizacaoEconomica";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    public function getInicioFiscalizacaoObra()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperarInicioFiscalizacaoObra";

        return parent::CallMap( $mapeamento, $metodo, parent::$this->CriterioSql );
    }

    // Busca documentos cadastrados em fiscalizacao.inicio_fiscalizacao_documentos.
    public function buscaDocumentos($cod_processo)
    {
        $stFiltro = " where fde.cod_processo = ".$cod_processo ;
        $rsDocumentos = new RecordSet ;
        $obTFISDocumentosEntrega = new TFISDocumentosEntrega;
        $obTFISDocumentosEntrega->recuperaListaDocumentosDevolvidos( $rsDocumentos, $stFiltro);

        return $rsDocumentos ;
    }

    public function receberDocumentos($parametros)
    {
        $pgList   = "FLReceberDocumentos.php";
        $pgForm   = "FMReceberDocumentos.php";

        $obTFISDocumentosEntrega = new TFISDocumentosEntrega();
        $obTransacao = new Transacao();
        $boFlagTransacao = false;
        $boTransacao = "";

        # Inicia nova transação
        $inCodProcesso = $parametros['inCodProcesso'];
        $inCodFiscal = $parametros['inCodFiscal'];
        $inSequencia = $parametros['inSequencia'];
        $stObservacao = $parametros['stObservacao'];

        if ($parametros['checkAbilitado']) {
            $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
            foreach ($parametros['checkAbilitado'] as $ch => $vlr) {

                $inCodDocumento = $ch;
                $stSituacao = $vlr;

                $obTFISDocumentosEntrega->setDado( "situacao", $stSituacao );
                $obTFISDocumentosEntrega->setDado( "cod_processo", $inCodProcesso );
                $obTFISDocumentosEntrega->setDado( "cod_documento", $inCodDocumento );
                $obTFISDocumentosEntrega->setDado( "cod_fiscal", $inCodFiscal );
                $obTFISDocumentosEntrega->setDado( "observacao", $stObservacao );

                $obErro = $obTFISDocumentosEntrega->inclusao( $boTransacao );
                # Ocorreu erro?
                if ($obErro->ocorreu() ) {
                    $return = sistemaLegado::exibeAviso( "Erro ao receber documentos.(".$inCodProcesso.")","pp","erro" );
                    $boTermina = true;
                    break;
                }
            }
            # Ocorreu erro?
            if (!$obErro->ocorreu() ) {
                # Termina transação
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFISInicioFiscalizacao );

                $return = sistemaLegado::alertaAviso($pgList , $inCodProcesso ,"incluir","aviso", Sessao::getId(), "../");
                //EMISSÃO DO TERMO DE RECEBIMENTO
                $this->emitir($parametros);
                $boTermina = true;
            } else {
                $return = sistemaLegado::exibeAviso( "Erro ao receber documentos.(".$inCodProcesso.")","pp","erro" );
                $boTermina = true;
            }
        } else {
            $return = sistemaLegado::exibeAviso( "Selecione um ou mais Documentos à Receber","pp","aviso" );
        }

        return $return;
    }

    public function emitir($arParametros)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obRFISIniciarProcessoFiscal = new RFISIniciarProcessoFiscal;
        $obTFISProcessoFiscal  = new TFISProcessoFiscal;
        $obTFISDocumento = new TFISDocumento;
        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $stFiltro = 'where pf.cod_processo = '.$arParametros["inCodProcesso"];

        if ($arParametros['inTipoFiscalizacao'] == '1') {
            $obTFISProcessoFiscal->recuperaEnderecoProcesso($rsProcesso,$stFiltro);

            $arData["#processo"]["inscricao_tipo"]	 = 'econômica';
            $arData["#processo"]["inscricao_numero"] = (string) $rsProcesso->getCampo('inscricao_economica');

            $arData["#processo"]["nom_cgm"]              = (string) $rsProcesso->getCampo('nom_cgm');
            $arData["#processo"]["logradouro"]           = (string) $rsProcesso->getCampo('logradouro_i');
            $arData["#processo"]["nom_bairro"]           = (string) $rsProcesso->getCampo('nom_bairro');
            $arData["#processo"]["cep"]                  = (string) $rsProcesso->getCampo('cep');
            $arData["#processo"]["nom_municipio"]        = (string) $rsProcesso->getCampo('nom_municipio');
            $arData["#processo"]["nom_uf"]               = (string) $rsProcesso->getCampo('sigla_uf');
        } else {
            $obTFISProcessoFiscal->recuperaEnderecoProcessoObra($rsProcesso,$stFiltro);

            $arData["#processo"]["inscricao_tipo"]	 = 'imobiliária';
            $arData["#processo"]["inscricao_numero"] = (string) $rsProcesso->getCampo('inscricao_municipal');

            $arEndereco = explode( "§", $rsProcesso->getCampo("endereco") );
            $arData["#processo"]["logradouro"]           = $arEndereco[2];
            $arData["#processo"]["nom_bairro"]           = $arEndereco[5];
            $arData["#processo"]["cep"]                  = $arEndereco[6];
            $arData["#processo"]["nom_municipio"]        = $arEndereco[8];
            $arData["#processo"]["nom_uf"]               = $arEndereco[10];
        }

        $arData["#processo"]["num_processo"]         = (string) $arParametros["inCodProcesso"];
        $arData["#processo"]["periodo_inicio"]       = (string) $rsProcesso->getCampo('periodo_inicio');
        $arData["#processo"]["periodo_termino"]      = (string) $rsProcesso->getCampo('periodo_termino');
        $arData["#processo"]["previsao_termino"]     = (string) $rsProcesso->getCampo('previsao_termino');
        $arData["#processo"]["prazo_entrega"]        = (string) $rsProcesso->getCampo('prazo_entrega');
        $arData["#processo"]["dt_inicio"]            = (string) $rsProcesso->getCampo('dt_inicio');

        $arData["#processo"]["inscricao_estadual"]   = (string) $rsProcesso->getCampo('inscricao_estadual');
        $arData["#processo"]["nom_cgm"]              = (string) $rsProcesso->getCampo('nom_cgm');

        if ($rsProcesso->getCampo('cpf') == '') {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cnpj');
        } else {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cpf');
        }

        //recuperando dados responsavel
        $obTFISAutorizacaoDocumento = new TFISAutorizacaoDocumento;
        $obRsDadosEstabUsuario = new RecordSet;
        $stFiltroEstabUsuario = ' WHERE aeed.inscricao_economica = ' . $arData["#processo"]["inscricao_numero"];

        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosEstabUsuario, $stFiltroEstabUsuario);

        $arData["#usuario"]['nom_responsavel']     = $obRsDadosEstabUsuario->getCampo('nom_responsavel');
        $arData["#usuario"]['cpf']                        = $obRsDadosEstabUsuario->getCampo('cpf');

        if ($arParametros['checkAbilitado']) {
            $i = 0;
            foreach ($arParametros['checkAbilitado'] as $stKey => $stValue) {
                if ($stValue == "R") {
                    $stFiltro = " AND documento.cod_documento = ".$stKey;
                    $rsDocumento = new RecordSet ;
                    $obTFISDocumento = new TFISDocumento;
                    $obTFISDocumento->recuperaDocumento( $rsDocumento, $stFiltro);

                    $arDocumentos["num_documento"] 	= (string) $rsDocumento->arElementos[0]["cod_documento"];
                    $arDocumentos["nom_documento"] 	= (string) $rsDocumento->arElementos[0]["nom_documento"];

                    $arData["#documento"][$i] = $arDocumentos;

                    $i++;
                }
            }
        }

        $arData["#processo"]["observacao"]	    = $arParametros['stObservacao'];

        $stFiltroCGM = ' WHERE cgm.numcgm = ' . Sessao::read('numCgm');

        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosCGM, $stFiltroCGM);
        $arData["#processo"]["local_recebimento"] =   $obRsDadosCGM->getCampo('nom_municipio');
        $arData["#processo"]["data_recebimento"]  = date("d")." de ".$obRFISIniciarProcessoFiscal->converteMes(date("m")). " de ".date("Y");
        $arData["#data"]["dia"]	            = date("d");
        $arData["#data"]["mes"]	            = date("m");
        $arData["#data"]["ano"]	            = date("Y");

        $this->setCriterio("cod_documento = ".$arParametros['stCodDocumento'] . " AND cod_tipo_documento = ". $arParametros['inCodTipoDocumento']);
        $rsDocumento = $this->getDocumento();

        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . "termo_recebimento.odt", $arData);
        $arDocumento["nome_label"] = "receber_documento_".$arParametros['inCodProcesso'].".odt";

        $obRFISEmitirDocumento->abrir($arDocumento);
    }
}
?>

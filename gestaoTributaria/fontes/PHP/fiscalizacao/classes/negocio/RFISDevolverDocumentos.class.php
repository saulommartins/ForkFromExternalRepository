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
<?php /**
    * Classe de regra de negócio para Prorrogar Recebimento de Documentos
    * Data de Criação: 26/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Aldo Jean Soares Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacao.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacaoDocumentos.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISProrrogacaoEntrega.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.   'RFISProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_NEGOCIO.   'RFISIniciarProcessoFiscal.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumentosEntrega.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php' );
require_once( CAM_GT_FIS_MAPEAMENTO . 'TFISAutorizacaoDocumento.class.php');

class RFISDevolverDocumentos extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getListaInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperaListaInicioFiscalizacaoEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
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

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getInicioFiscalizacaoEconomica()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperarInicioFiscalizacaoEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getInicioFiscalizacaoObra()
    {
        $mapeamento = "TFISDocumentosEntrega";
        $metodo = "recuperarInicioFiscalizacaoObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    // Busca documentos cadastrados em fiscalizacao.inicio_fiscalizacao_documentos.
    public function buscaDocumentos($cod_processo)
    {
        $stFiltro = " where fde.cod_processo = ".$cod_processo ;
        $rsDocumentos = new RecordSet ;
        $obTFISDocumentosEntrega = new TFISDocumentosEntrega;
        $obTFISDocumentosEntrega->recuperaListaDocumentosEntrega($rsDocumentos, $stFiltro);

        return $rsDocumentos ;
    }

    // Busca documentos cadastrados em fiscalizacao.inicio_fiscalizacao_documentos.
    public function buscaDocumentosD($cod_processo)
    {
        $stFiltro = " where fifd.cod_processo = ".$cod_processo;
        $rsDocumentos = new RecordSet ;
        $obTFISDocumentosEntrega = new TFISDocumentosEntrega;
        $obTFISDocumentosEntrega->recuperaListaDocumentosD( $rsDocumentos, $stFiltro);

        return $rsDocumentos ;
    }

    // Inicia a transação de alteração em fiscalização.documentos_entrega.
    public function devolver($arParam)
    {
        $pgList   = "FLDevolverDocumentos.php";
        $pgForm   = "FMDevolverDocumentos.php" ;

        if (is_array($arParam)) {

            if ($arParam['stSituacao'] != '') {

                $rsRecordSet = new RecordSet();
                $obTFISDocumentosEntrega = new TFISDocumentosEntrega();

                $obTransacao = new Transacao();
                $boFlagTransacao = false;
                 $boTransacao = "";

                # Inicia nova transação
                $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

                $arSituacaoTotal = count($arParam['stSituacao']);
                foreach ($arParam['stSituacao'] as $ch => $vlr) {
                    if ($vlr == "D") {
                        $obTFISDocumentosEntrega->setDado("situacao", $vlr);
                        $obTFISDocumentosEntrega->setDado("cod_processo", $arParam['inCodProcessoLista']);
                        $obTFISDocumentosEntrega->setDado("cod_documento", $ch);
                        $obTFISDocumentosEntrega->setDado("cod_fiscal", $arParam['inCodFiscal']);
                        $obTFISDocumentosEntrega->setDado("observacao", $arParam['stObservacaoLista']);
                        $obErro = $obTFISDocumentosEntrega->inclusao( $boTransacao);
                    }
                }

                if ($obErro->ocorreu() ) {
                    return sistemaLegado::exibeAviso("Houve erro ao Iniciar definir documento como devolvido!.(".$arParam['inCodProcessoLista'].")","n_incluir","erro");
                    $boTermina = true;
                } else {
                    $boTermina = true;
                }

                if ($boTermina) {
                    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFISDocumentosEntrega );

                    sistemaLegado::alertaAviso("FLDevolverDocumentos.php",$arParam['inCodProcessoLista'],"incluir","aviso",Sessao::getId());
                    $this->emitir($arParam);
                }

            } else {
                return sistemaLegado::exibeAviso("Caso tenha documentos a devolver, selecione pelo menos um!","n_incluir","aviso");
                $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFISDocumentosEntrega );
            }
        }

    }

    public function emitir($arParametros) {require_once( CAM_GT_FIS_NEGOCIO.   'RFISProcessoFiscal.class.php' );

        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obRFISIniciarProcessoFiscal = new RFISIniciarProcessoFiscal;
        $obTFISProcessoFiscal  = new TFISProcessoFiscal;
        $obTFISDocumento = new TFISDocumento;
        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $stFiltro = 'where pf.cod_processo = '.$arParametros["inCodProcesso"];

        $arData["#processo"]["num_processo"]         = $arParametros["inCodProcesso"];
        $arData["#processo"]["data_entrega"]         = $arParametros["stDataEntregaLista"];

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

        $arData["#processo"]["inscricao_estadual"]   = (string) $rsProcesso->getCampo('inscricao_estadual');
        $arData["#processo"]["nom_cgm"]              = (string) $rsProcesso->getCampo('nom_cgm');

        if ($rsProcesso->getCampo('cpf') == '') {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cnpj');
        } else {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cpf');
        }

        if ($arParametros['stSituacao']) {
            $i = 0;
            foreach ($arParametros['stSituacao'] as $stKey => $stValue) {
                if ($stValue == "D") {
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

        $arData["#termo"]["observacao"]	    = $arParametros['stObservacoes'];

        $stFiltroCGM = ' WHERE cgm.numcgm = ' . Sessao::read('numCgm');
        $obTFISAutorizacaoDocumento = new TFISAutorizacaoDocumento;
        $obTFISAutorizacaoDocumento->recuperaDadosEstabelecimento($obRsDadosCGM, $stFiltroCGM);

        $arData["#data"]["local_devolucao"]	= $obRsDadosCGM->getCampo('nom_municipio');
        $arData["#data"]["data_devolucao"]	= date("d")." de ".$obRFISIniciarProcessoFiscal->converteMes(date("m")). " de ".date("Y");
        $arData["#data"]["dia"]	            = date("d");
        $arData["#data"]["mes"]	            = date("m");
        $arData["#data"]["ano"]	            = date("Y");

        $this->setCriterio("cod_documento = ".$arParametros['stCodDocumento'] . " AND cod_tipo_documento = ". $arParametros['inCodTipoDocumento']);
        $rsDocumento = $this->getDocumento();
        $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $rsDocumento->getCampo('nome_arquivo_agt'), $arData);
        $arDocumento["nome_label"] = "devolver_documento_".$arParametros['inCodProcesso'].".odt";

        $obRFISEmitirDocumento->abrir($arDocumento);
    }
}
?>

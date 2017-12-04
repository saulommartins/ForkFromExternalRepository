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
    * Classe de regra de negócio para Inicio do Processo Fiscal
    * Data de Criação: 28/07/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage Regra

    * Casos de uso:

    $Id:$
*/
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISProcessoFiscal.class.php');
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php');
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISLevantamento.class.php');
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacao.class.php');
require_once(CAM_GT_FIS_MAPEAMENTO.'TFISInicioFiscalizacaoDocumentos.class.php');
require_once(CAM_GT_FIS_NEGOCIO.'RFISProcessoFiscal.class.php');
require_once(CAM_GT_FIS_NEGOCIO. 'RFISEmitirDocumento.class.php');
require_once(CAM_GT_FIS_VISAO. 'VFISProcessoFiscal.class.php');
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php");

class RFISIniciarProcessoFiscal extends RFISProcessoFiscal
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getIniciarProcesso()
    {
        $mapeamento = "TFISInicioFiscalizacao";
        $metodo = "recuperarInicioFiscalizacao";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function iniciarProcesso($parametros)
    {
        $pgList  = "FLIniciarProcessoFiscal.php";
        $pgForm  = "FMIniciarProcessoFiscal.php";

        if (is_array($parametros['documento'])) {

            $inCodProcesso = $parametros['inCodProcesso'];

            $inCodFiscal = $parametros['inCodFiscal'];
            $inCodTipoDocumento = $parametros['inCodTipoDocumento'];
            $inCodDocumento = $parametros['stCodDocumento'];
            $stDtInicio = $parametros['dtDataInicio'];
            $stLocalEntrega = $parametros['stLocalEntrega'];
            $stPrazoEntrega = $parametros['dtPrazoEntrega'];
            $stObservacao = $parametros['stObservacao'];

            $dtNew = explode("/",$stPrazoEntrega);
            $dtPrazoEntrega = strtotime($dtNew[2]."-".$dtNew[1]."-".$dtNew[0]);

            $dtOld = explode("/",$stDtInicio);
            $dtAnterior = strtotime($dtOld[2]."-".$dtOld[1]."-".$dtOld[0]);

            $rsRecordSet = new RecordSet();

            if ($dtPrazoEntrega >= $dtAnterior) {

                $obTFISInicioFiscalizacao = new TFISInicioFiscalizacao();
                $obTFISInicioFiscalizacaoDocumentos = new TFISInicioFiscalizacaoDocumentos();
                $obTransacao = new Transacao();
                $boFlagTransacao = false;
                   $boTransacao = "";

                # Inicia nova transação
                $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

                if ($rsRecordSet->Eof()) {
                    $obTFISInicioFiscalizacao->setDado("cod_processo", $inCodProcesso);

                    $obTFISInicioFiscalizacao->setDado("cod_fiscal", $inCodFiscal);
                    $obTFISInicioFiscalizacao->setDado("cod_tipo_documento", $inCodTipoDocumento);
                    $obTFISInicioFiscalizacao->setDado("cod_documento", $inCodDocumento);
                    $obTFISInicioFiscalizacao->setDado("dt_inicio", $stDtInicio);
                    $obTFISInicioFiscalizacao->setDado("local_entrega", $stLocalEntrega);
                    $obTFISInicioFiscalizacao->setDado("prazo_entrega", $stPrazoEntrega);
                    $obTFISInicioFiscalizacao->setDado("observacao", $stObservacao);

                    $obErro = $obTFISInicioFiscalizacao->inclusao($boTransacao);

                    # Ocorreu erro?
                    if (!$obErro->ocorreu()) {

                        foreach ($parametros['documento'] as $ch => $docs) {
                            $obTFISInicioFiscalizacaoDocumentos->setDado("cod_processo", $inCodProcesso);
                            $obTFISInicioFiscalizacaoDocumentos->setDado("cod_documento", $docs);
                            $obErro = $obTFISInicioFiscalizacaoDocumentos->inclusao($boTransacao);
                            if ($obErro->ocorreu()) {
                                return  sistemaLegado::exibeAviso("Houve erro ao definir documento como devolvido!.(".$inCodProcesso.")","erro","erro");
                                $boTermina = true;
                                break;
                            }
                        }

                    } else {
                        $return = sistemaLegado::exibeAviso("Houve erro ao Iniciar Processo Fiscal.(".$inCodProcesso.")","erro","erro");
                        $boTermina = true;
                    }

                    $return = sistemaLegado::alertaAviso($pgList , $inCodProcesso ,"incluir","aviso", Sessao::getId(), "../");
                    $boTermina = true;

                } else {
                        $return = sistemaLegado::exibeAviso("Já existe uma Fiscalização em Andamento.(".$inCodProcesso.")","erro","erro");
                    $boTermina = true;
                }
            } else {
                $return = sistemaLegado::exibeAviso("Prazo de Entrega menor que Data de Início.(".$inCodProcesso.")","erro","erro");
            }

        } else {
            $return = sistemaLegado::exibeAviso("Necessário incluir pelo menos um Documento!","erro","erro");

        }

        if ($boTermina) {
            # Termina transação
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTFISInicioFiscalizacao);
            $this->emitir($parametros);
        }

        return $return;
    }

    public function getProcessoFiscalEconomica()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getProcessoFiscalObra()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaProcessoFiscalEconomica()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaListaProcessoFiscalEconomica";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaProcessoFiscalObra()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaListaProcessoFiscalObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getListaProcessoFiscalEconomicaObra()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaListaProcessoFiscalEconomicaObra";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getProcessoFiscalGrupo()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaGrupo";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getProcessoFiscalCredito()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaCredito";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getProcessoFiscalCreditoGrupo()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaCreditoGrupo";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function getDocumentoEmpresa()
    {
        $mapeamento = "TFISDocumento";
        $metodo = "recuperaTodos";

        return parent::CallMap($mapeamento, $metodo, parent::$this->CriterioSql);
    }

    public function converteMes($numData)
    {
        switch ($numData) {
            case '01': $mes = 'Janeiro';  break;
            case '02': $mes = 'Fevereiro';  break;
            case '03': $mes = 'Março';  break;
            case '04': $mes = 'Abril';  break;
            case '05': $mes = 'Maio';  break;
            case '06': $mes = 'Junho';  break;
            case '07': $mes = 'Julho';  break;
            case '08': $mes = 'Agosto';  break;
            case '09': $mes = 'Setembro';  break;
            case '10': $mes = 'Outubro';  break;
            case '11': $mes = 'Novembro';  break;
            case '12': $mes = 'Dezembro';  break;
        }

    return $mes;
    }

    public function emitir($request)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obTFISDocumento = new TFISDocumento;
        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $arData["#processo"]["num_processo"]	= (string) $request["inCodProcesso"];
        $arData["#processo"]["ano_exercicio"]	= (string) Sessao::read("exercicio");
        $arData["#processo"]["data_entrega"]	= (string) $request["stDataEntregaLista"];

        $obTFISProcessoFiscal = new TFISProcessoFiscal;
        $obRSEndereco = new RecordSet;
        $stFiltro = 'where pf.cod_processo = '.$request["inCodProcesso"];

        if ($request["inTipoFiscalizacao"] == '1') {
            $obTFISProcessoFiscal->recuperaEnderecoProcesso($obRSEndereco,$stFiltro);

            $arData["#processo"]["inscricao_tipo"]	  = 'econômica';
            $arData["#processo"]["inscricao_numero"]  = (string) $obRSEndereco->getCampo('inscricao_economica');
            $arData["#processo"]["nom_cgm"]              = (string) $obRSEndereco->getCampo('nom_cgm');
            $arData["#processo"]["logradouro"]           = (string) $obRSEndereco->getCampo('logradouro_i');
            $arData["#processo"]["nom_bairro"]           = (string) $obRSEndereco->getCampo('nom_bairro');
            $arData["#processo"]["cep"]                  = (string) $obRSEndereco->getCampo('cep');
            $arData["#processo"]["nom_municipio"]        = (string) $obRSEndereco->getCampo('nom_municipio');
            $arData["#processo"]["nom_uf"]               = (string) $obRSEndereco->getCampo('sigla_uf');
        } else {
            $obTFISProcessoFiscal->recuperaEnderecoProcessoObra($obRSEndereco,$stFiltro);

            $arData["#processo"]["inscricao_tipo"]	  = 'imobiliária';
            $arData["#processo"]["inscricao_numero"]  = (string) $obRSEndereco->getCampo('inscricao_municipal');

            $arEndereco = explode( "§", $obRSEndereco->getCampo("endereco") );
            $arData["#processo"]["logradouro"]           = $arEndereco[2];
            $arData["#processo"]["nom_bairro"]           = $arEndereco[5];
            $arData["#processo"]["cep"]                  = $arEndereco[6];
            $arData["#processo"]["nom_municipio"]        = $arEndereco[8];
            $arData["#processo"]["nom_uf"]               = $arEndereco[10];
        }

        $arDataInicio = explode('/',$request["dtDataInicio"]);
        $mes = $this->converteMes($arDataInicio[1]);

        if ($obRSEndereco->getCampo('cpf') == '') {
           $arData["#processo"]["cpf_cnpj"]          = (string) $obRSEndereco->getCampo('cnpj');
        } else {
           $arData["#processo"]["cpf_cnpj"]          = (string) $obRSEndereco->getCampo('cpf');
        }

        $arData["#processo"]["nom_atividade"]        = (string) $obRSEndereco->getCampo('nom_atividade');
        $arData["#processo"]['inicio']["dia"]        = (string) $arDataInicio[0];
        $arData["#processo"]['inicio']["mes"]        = $mes;
        $arData["#processo"]['inicio']["ano"]        = (string) $arDataInicio[2];
        $arData["#processo"]['prazo'] = $request["dtPrazoEntrega"];
        $arData["#processo"]["localEntrega"]         = (string) $request["stLocalEntrega"];
        $arData["#processo"]["observacao"]           = (string) $request["stObservacao"];
        $arData["#processo"]["periodo_inicio"]       = (string) $obRSEndereco->getCampo('periodo_inicio');
        $arData["#processo"]["periodo_termino"]      = (string) $obRSEndereco->getCampo('periodo_termino');
        $arData["#processo"]["previsao_termino"]     = (string) $obRSEndereco->getCampo('previsao_termino');

        #Relação de Documentos
        if (is_array($request['documento'])) {
            $i = 0;
            foreach ($request['documento'] as $ch => $doc) {
                $stFiltro        = " and documento.cod_documento = ".$doc;
                $obTFISDocumento = new TFISDocumento;
                $obTFISDocumento->recuperaDocumento($obRSDocumento,$stFiltro);

                $arDocumentos["num_documento"] 	= (string) $obRSDocumento->arElementos[0]["cod_documento"];
                $arDocumentos["nom_documento"] 	= (string) $obRSDocumento->arElementos[0]["nom_documento"];
                $arData["#documento"][$i] = $arDocumentos;

                $i++;
            }
        }

        #Busca Fiscal
        #Busca fiscal que está na sessão sessao::read('numCgm') 581 definido
        $stFiltro = ' AND sw_cgm.numcgm = '.sessao::read('numCgm');
        $obRSFiscal = new RecordSet;
        $obTFISProcessoFiscal->recuperaDadosFiscal($obRSFiscal,$stFiltro);

        $arData["#processo"]["fiscal"]['nome']         = (string) $obRSFiscal->getCampo('nom_cgm');
        $arData["#processo"]["fiscal"]['cargo']        = (string) $obRSFiscal->getCampo('descricao');
        $arData["#processo"]["fiscal"]['matricula']    = (string) $obRSFiscal->getCampo('registro');

        $arData["#data"]["dia"] = date("d");
        $arData["#data"]["mes"]	= date("m");
        $arData["#data"]["ano"]	= date("Y");

        $obDocumento = new TAdministracaoModeloDocumento;
        $rsDocumento = new RecordSet;
        $obDocumento->recuperaTodos($rsDocumento,' where cod_documento = '.$request['stCodDocumentoTxt']);

        if ($rsDocumento->getCampo('nome_arquivo_agt')) {
            $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $rsDocumento->getCampo('nome_arquivo_agt'), $arData);
            $arDocumento['nome_label'] = 'termo_inicio_processo_'.$request["inCodProcesso"].".odt";
            $obRFISEmitirDocumento->abrir($arDocumento);

            return $arDocumento;
        }
    }
}
?>

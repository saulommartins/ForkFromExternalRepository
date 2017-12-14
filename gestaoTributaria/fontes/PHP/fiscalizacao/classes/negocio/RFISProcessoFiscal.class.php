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

include_once CAM_GT_FIS_MAPEAMENTO.'/TFISTipoFiscalizacao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscal.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISFiscalProcessoFiscal.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalEmpresa.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalObras.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalGrupo.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalCredito.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISInicioFiscalizacao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISInicioFiscalizacaoDocumentos.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalCancelado.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISFiscal.class.php';
require_once (CAM_GT_FIS_MAPEAMENTO.'TFISDocumento.class.php');
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISFiscalFiscalizacao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISInfracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISProcessoFiscalEncerrado.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISLevantamento.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'/TFISNotificacaoFiscalizacao.class.php';
include_once CAM_GT_MON_MAPEAMENTO.'/TMONCredito.class.php';
include_once CAM_GT_CEM_MAPEAMENTO.'/TCEMCadastroEconomico.class.php';
include_once CAM_GT_ARR_MAPEAMENTO.'/TARRGrupoCredito.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'/TAdministracaoModeloDocumento.class.php';
require_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GT_FIS_MAPEAMENTO.'TFISServicoComRetencao.class.php';
include_once CAM_GT_FIS_NEGOCIO.'/RFISDocumento.class.php';
require_once CAM_GT_FIS_NEGOCIO.'/RFISEmitirDocumento.class.php';

class RFISProcessoFiscal
{
    private $obTransacao;

    // attributos do mapeamento
    private $codProtocolo;
    private $anoExercicio;
    private $codProcesso = null;
    private $codGrupo;
    private $codNatureza;
    private $codGenero;
    private $codEspecie;
    private $codCredito;
    private $inscricaoEconomica;
    private $inscricaoMunicipal;
    private $codLocal = 1;
    private $vencimentoOriginal;

    //mapeamentos ultilizados
    private $RFISDocumento;
    private $TFISInfracao;
    private $TFISProcessoFiscal;
    private $TFISFiscalProcessoFiscal;
    private $TFISProcessoFiscalGrupo;
    private $TFISProcessoFiscalCredito;
    private $TFISProcessoFiscalEmpresa;
    private $TFISProcessoFiscalObras;
    private $TFISInicioFiscalizacao;
    private $TFISInicioFiscalizacaoDocumentos;
    private $TFISProcessoFiscalCancelado;
    private $TFISProcessoFiscalEncerrado;
    private $TFISNotificacaoFiscalizacao;
    private $TFISLevantamento;
    private $TCEMCadastroEconomico;
    private $TAdministracaoModeloDocumento;
    private $TAdministracaoConfiguracao;

    public function __construct()
    {
        $this->obTransacao                      = new Transacao;

        $this->RFISDocumento                    = new RFISDocumento;
        $this->TFISInfracao                     = new TFISInfracao;
        $this->TFISProcessoFiscal               = new TFISProcessoFiscal;
        $this->TFISFiscalProcessoFiscal         = new TFISFiscalProcessoFiscal;
        $this->TFISProcessoFiscalGrupo          = new TFISProcessoFiscalGrupo;
        $this->TFISProcessoFiscalCredito        = new TFISProcessoFiscalCredito;
        $this->TFISProcessoFiscalEmpresa        = new TFISProcessoFiscalEmpresa;
        $this->TFISProcessoFiscalObras          = new TFISProcessoFiscalObras;
        $this->TFISInicioFiscalizacao           = new TFISInicioFiscalizacao;
        $this->TFISInicioFiscalizacaoDocumentos = new TFISInicioFiscalizacaoDocumentos;
        $this->TFISProcessoFiscalCancelado      = new TFISProcessoFiscalCancelado;
        $this->TFISProcessoFiscalEncerrado      = new TFISProcessoFiscalEncerrado;
        $this->TFISNotificacaoFiscalizacao      = new TFISNotificacaoFiscalizacao;
        $this->TFISLevantamento                 = new TFISLevantamento;
        $this->TAdministracaoModeloDocumento    = new TAdministracaoModeloDocumento;
        $this->TAdministracaoConfiguracao       = new TAdministracaoConfiguracao;
        $this->TCEMCadastroEconomico            = new TCEMCadastroEconomico;
    }

    protected function CallMap($mapeamento,$metodo,$criterio,$filter = true, $transacao = null)
    {
        if ($filter)
            $where = " where ";
        else
            $where = " ";

        if($criterio)
            $criterio = $where.$criterio;

        $obRs = new RecordSet();
        $ob = new $mapeamento;
        $ob->$metodo($obRs,$criterio, null, $transacao);

        return $obRs;
    }
    public function getProcessoFiscal()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaTodos",$this->CriterioSql);
    }
        public function getFiscalFiscalizacao()
        {
        return $this->CallMap("TFISFiscalFiscalizacao","recuperaTodos",$this->CriterioSql);
    }

    public function getFiscais()
    {
        return $this->CallMap("TFISFiscal","recuperaListaFiscal",$this->CriterioSql);
    }
        public function getFiscalAtivo()
        {
        return $this->CallMap("TFISFiscal","recuperaTodos",$this->CriterioSql);
    }
    public function getFiscal()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaDadosFiscal",$this->CriterioSql);
    }

    public function getInscricaoEconomicaProcesso()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaListaProcessoFiscalEconomica",$this->CriterioSql);
    }

    public function getInscricaoEconomicaProcessoAlteracao()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaListaProcessoFiscalEconomicaAlteracao",$this->CriterioSql, true);
    }

    public function getInscricaoImobiliariaProcesso()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaListaProcessoFiscalObra",$this->CriterioSql);
    }

    public function getInscricao($inscricao)
    {
        if ($inscricao == 'imobiliaria') {
            return $this->CallMap("TCIMImovel","recuperaInscricaoImobiliario", $this->CriterioSql,false);
        } else {
            return $this->CallMap("TCEMCadastroEconomico","recuperaInscricao", $this->CriterioSql, false);
        }
    }

    public function getInfracoes()
    {
        return $this->CallMap("TFISNotificacaoFiscalizacao","recuperaInfracaoProcesso",$this->CriterioSql);
    }

    public function getCreditos()
    {
        return $this->CallMap("TMONCredito","recuperaRelacionamento", $this->CriterioSql);
    }

    public function getGrupoCreditos()
    {
        return $this->CallMap("TARRGrupoCredito","recuperaRelacionamento",$this->CriterioSql);
    }

    public function getDocumentos($condicao = '')
    {
                $this->TFISFiscalProcessoFiscal->setDado("cod_processo","");

        return $this->RFISDocumento->Todos("uso_interno = 't' and ativo = 't' ".$condicao);
    }

    public function getInicioFiscalizacaoDocumentos()
    {
        return $this->CallMap("TFISInicioFiscalizacaoDocumentos","recuperarInicioFiscalizacaoDocumentos",$this->CriterioSql);
    }

    public function getUltimoProcessoFiscal()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaUltimoCodProcessoFiscal",$this->CriterioSql);
    }

    public function getTipoFiscalizacao()
    {
        return $this->CallMap("TFISTipoFiscalizacao","recuperaTipoFiscalizacao",$this->CriterioSql);
    }

    public function getDocumento()
    {
        return $this->CallMap("TAdministracaoModeloDocumento","recuperaTodos",$this->CriterioSql);
    }

    public function pegaUsuarioSessao()
    {
        return $this->CallMap("TFISProcessoFiscal","recuperaServidorFiscal",$this->CriterioSql);
    }

    public function isServidor($numcgm)
    {
        $this->setCriterio(" sw.numcgm = ".$numcgm);
        $resp = $this->CallMap("TFISProcessoFiscal","recuperaServidor",$this->CriterioSql);

        return $resp;
    }

    public function isFiscal($numcgm)
    {
        $this->setCriterio(" f.numcgm =".$numcgm);

        return $this->CallMap("TFISProcessoFiscal","recuperaFiscal",$this->CriterioSql);
    }

    public function setCriterio($vlr)
    {
        $this->CriterioSql = $vlr;
    }

    public function verificaProcessoInfracaoEmitida($stConfiguracao, $boTransacao = null)
    {
        $this->TAdministracaoConfiguracao->setDado("cod_modulo", 34);
        $this->TAdministracaoConfiguracao->setDado("exercicio" , Sessao::getExercicio());
        $this->TAdministracaoConfiguracao->setDado("parametro" , $stConfiguracao);
        $this->TAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao, $boTransacao );

        $this->setCriterio("ninf.cod_processo = ".$this->codProcesso." AND inf.cod_infracao = ".$rsConfiguracao->getCampo("valor"));
        $rsInfracoes = $this->getInfracoes();

        return $rsInfracoes;
    }

    public function verificaProcessoPendenciaFiscal()
    {
        return $this->CallMap("TFISLevantamento","recuperaPendenciasProcessoFiscal",$this->CriterioSql);
    }

    public function verificaProcessoPendenciaDocumentoEntregue()
    {
        return $this->CallMap("TFISInicioFiscalizacaoDocumentos","recuperaPendenciasProcessoDocumentoEntregue",$this->CriterioSql);
    }

    public function verificaProcessoPendenciaDocumentoPrazo()
    {
        return $this->CallMap("TFISInicioFiscalizacaoDocumentos","recuperaPendenciasProcessoDocumentoPrazo",$this->CriterioSql, false);
    }

    public function verificaProcessoPendenciaPlanilha()
    {
        return $this->CallMap("TFISLevantamento","recuperaTodos",$this->CriterioSql, false);
    }

    public function verificaProcessoIniciado($codigo)
    {
        $this->setCriterio(" cod_processo = ".$codigo);
        $retorno = $this->CallMap("TFISInicioFiscalizacao","recuperaTodos",$this->CriterioSql);
        if (count($retorno->arElementos) > 0) {
            return true;
        } else {
            return 	false;
        }
    }

    public function verificaServicoComRetencao($inCodProcesso)
    {
        $obTFISServicoComRetencao = new TFISServicoComRetencao();
        $rsServicos = new RecordSet();
        $obTFISServicoComRetencao->recuperaTodos($rsServicos, ' WHERE cod_processo = ' . $inCodProcesso);

        return !$rsServicos->eof();
    }

    private function DefineCodProcessoFiscalAtual()
    {
        $this->setCriterio('');
        $inCodProcesso = $this->getUltimoProcessoFiscal();
        $this->codProcessoFiscal = $inCodProcesso;
    }

    public function Emitir($param)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $obTFISDocumento = new TFISDocumento;
        $arData = array();

        $obTFISDocumento->recuperaDadosGenericosConfiguracaoSW( $rsDadosGenericos );
        $arData["#dados"]["url_logo"] = $rsDadosGenericos->getCampo( "url_logo" );
        $arData["#dados"]["nom_pref"] = $rsDadosGenericos->getCampo( "nom_pref" );

        $arData["#processo"]["num_processo"]	= (string) $param["inCodProcesso"];
        $arData["#processo"]["ano_exercicio"]	= (string) Sessao::read("exercicio");

        $obTFISProcessoFiscal = new TFISProcessoFiscal;
        $this->setCriterio('cod_processo = ' . $param["inCodProcesso"]);
        $rsProcesso = $this->getProcessoFiscal();

        $stFiltro = 'where pf.cod_processo = '.$param["inCodProcesso"];

        if ($rsProcesso->getCampo('cod_tipo') == '1') {
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

        if ($rsProcesso->getCampo('cpf') == '') {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cnpj');
        } else {
           $arData["#processo"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cpf');
        }

        $arData["#processo"]["nom_atividade"]        = (string) $rsProcesso->getCampo('nom_atividade');

        //dados encerramento
        $arDataEncerramento                          =  explode('/', $param['dtEncerramento']);
        $arData["#encerramento"]["dia"]              = (string) $arDataEncerramento[0];
        $arData["#encerramento"]["mes"]              = $this->converteMes((string) $arDataEncerramento[1]);
        $arData["#encerramento"]["ano"]             = (string) $arDataEncerramento[2];

        //dados documentos
        $this->setCriterio("ifd.cod_processo = ".$param['inCodProcesso']);
        $rsDocumentos = $this->getInicioFiscalizacaoDocumentos();

        $k = 0;
        for ($i = 0; $i < count($rsDocumentos->arElementos); $i++) {
            $stFiltro        = " and documento.cod_documento = ".$rsDocumentos->arElementos[$i]['cod_documento'];
            $rsDocumento     = new RecordSet;
            $obTFISDocumento = new TFISDocumento;
            $obTFISDocumento->recuperaDocumento($rsDocumento, $stFiltro);

            if ($rsDocumento->getCampo("uso_interno") == 'f') {
                $arDocumentos["num_documento"] 	= (string) $rsDocumento->getCampo("cod_documento");
                $arDocumentos["nom_documento"] 	= (string) $rsDocumento->getCampo("nom_documento");
                $arData["#documento"][$k]       = $arDocumentos;
                $k++;
            }
        }

        //dados infrações
        $this->setCriterio("ninf.cod_processo = ".$param['inCodProcesso']);
        $rsInfracoes = $this->getInfracoes();

        $valorTotal = 0;
        if (count($rsInfracoes->arElementos)) {
            for ($j = 0; $j < count($rsInfracoes->arElementos); $j++) {
                $arDataInfracao = explode("-", $rsInfracoes->arElementos[$j]["dt_ocorrencia"]);

                $arInfracoes["nom_infracao"]        = (string) $rsInfracoes->arElementos[$j]["nom_infracao"];
                $arInfracoes["num_infracao"]        = (string) $rsInfracoes->arElementos[$j]["cod_infracao"];
                $arInfracoes["data"]                = (string) $arDataInfracao[2]."/".$arDataInfracao[1]."/".$arDataInfracao[0];
                $arInfracoes["valor"]               = (string) $rsInfracoes->arElementos[$j]["valor"];

                $valorTotal                         = $valorTotal + $rsInfracoes->arElementos[$j]["valor"];

                $arData["#infracao"][$j]            = $arInfracoes;
            }
        } else {
            $arInfracoes["nom_infracao"]        = " ";
            $arInfracoes["num_infracao"]        = " ";
            $arInfracoes["data"]                = " ";
            $arInfracoes["valor"]               = " ";

            $arData["#infracao"][0]            = $arInfracoes;
        }
        $arData["#valor"]['total']                  = $valorTotal;

        //dados fiscal
        $this->setCriterio("sw_cgm.numcgm = ".Sessao::read('numCgm'));
        $rsFiscal = $this->getFiscal();

        $arData["#fiscal"]['nom_fiscal']        = (string) $rsFiscal->getCampo('nom_cgm');
        $arData["#fiscal"]['nom_cargo']         = (string) $rsFiscal->getCampo('descricao');
        $arData["#fiscal"]['num_matricula']     = (string) $rsFiscal->getCampo('registro');

        //dados data
        $arData["#data"]["dia"]                 = date("d");
        $arData["#data"]["mes"]	                = date("m");
        $arData["#data"]["ano"]	                = date("Y");

        $this->setCriterio("cod_documento = ".$param['stCodDocumento']);
        $rsDocumento = $this->getDocumento();

        if ($rsDocumento->getCampo('nome_arquivo_agt')) {
             $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . $rsDocumento->getCampo('nome_arquivo_agt'), $arData);
            $arDocumento["nome_label"] = "encerrar_processo_fiscal_".$param['inCodProcesso'].".odt";
            $obRFISEmitirDocumento->abrir($arDocumento);
        } else {
             $arDocumento = $obRFISEmitirDocumento->construir("odt", CAM_GT_FIS_MODELOS . "termo_encerramento_fiscalizacao.odt", $arData);
            $arDocumento["nome_label"] = "encerrar_processo_fiscal_".$param['inCodProcesso'].".odt";
            $obRFISEmitirDocumento->abrir($arDocumento);
        }
    }

    public function Cancelar($param)
    {
        $this->codProcesso = $param["inCodProcesso"];

        $status = true;
        $boFlagTransacao = false;
        $obErro = new Erro;
        $timestamp = date('Y-m-d h:m:s');

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $this->TFISProcessoFiscalCancelado->setDado( "cod_processo",$this->codProcesso);
        $this->TFISProcessoFiscalCancelado->setDado( "timestamp",$timestamp);
        $this->TFISProcessoFiscalCancelado->setDado( "numcgm", $param['numcgm'] );
        $this->TFISProcessoFiscalCancelado->setDado( "justificativa",$param["stJustificativa"]);

        $obErro = $this->TFISProcessoFiscalCancelado->inclusao( $boTransacao );

        if (!$obErro->ocorreu()) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->TFISProcessoFiscalCancelado);

            return sistemaLegado::alertaAviso("LSManterProcesso.php?stAcao=cancelar",$this->codProcesso,"alterar","aviso",Sessao::getId());
        }
    }

    public function Encerrar($param)
    {
        $this->codProcesso  = $param["inCodProcesso"];

        $boErro             = false;
        $boFlagTransacao    = false;
        $obErro             = new Erro;
        $timestamp          = date('Y-m-d h:m:s');

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $stFiltro = ' fc.numcgm = '.Sessao::read('numCgm');
        $rsFiscal = $this->CallMap('TFISFiscal', 'recuperaListaFiscal', $stFiltro, true, $boTransacao);

        $this->TFISProcessoFiscalEncerrado->setDado( "cod_processo", $this->codProcesso);
        $this->TFISProcessoFiscalEncerrado->setDado( "cod_fiscal", $rsFiscal->getCampo("codigo"));
        $this->TFISProcessoFiscalEncerrado->setDado( "cod_documento", $param['stCodDocumento']);
        $this->TFISProcessoFiscalEncerrado->setDado( "cod_tipo_documento", $param['inCodTipoDocumento']);
        $this->TFISProcessoFiscalEncerrado->setDado( "dt_termino", $param['dtEncerramento']);
        $this->TFISProcessoFiscalEncerrado->setDado( "observacao", $param['stObeservacao']);
        $this->TFISProcessoFiscalEncerrado->setDado( "timestamp", $timestamp);

        //verficica pendencias fiscais
        $this->setCriterio("cod_processo = ".$this->codProcesso);
        $rsPendencias = $this->verificaProcessoPendenciaFiscal();

        if ($rsPendencias->getCampo('devido') > 0) {
            $rsInfracao = $this->verificaProcessoInfracaoEmitida("pagamento_menos", $boTransacao);

            if (!$rsInfracao->eof()) {
                $boErro     = true;
                $stMensagem = 'Foram encontradas pendências fiscais junto ao processo. (Pagamento a menor)';

                return sistemaLegado::alertaAviso("../termo/LSEmitirAutoInfracao.php?stAcao=imprimir&inCodProcesso=".$param['inCodProcesso'], $stMensagem, "erro", "aviso", Sessao::getId());
            }
        }

        if ($rsPendencias->getCampo('declarado_menor') > 0) {
            $rsInfracao = $this->verificaProcessoInfracaoEmitida("declaracao_menor", $boTransacao);

            if (!$rsInfracao->eof()) {
                $boErro     = true;
                $stMensagem = 'Foram encontradas pendências fiscais junto ao processo. (Declaração a menor)';

                return sistemaLegado::alertaAviso("../termo/LSEmitirAutoInfracao.php?stAcao=imprimir&inCodProcesso=".$param['inCodProcesso'], $stMensagem, "erro", "aviso", Sessao::getId());
            }
        }

        //verifica pendencias de documentos
        $this->setCriterio("inf.cod_processo = ".$param['inCodProcesso']);
        $rsDocumentos = $this->verificaProcessoPendenciaDocumentoEntregue();

        if (!$rsDocumentos->getCampo('documentos_entregue')) {
            $rsInfracao = $this->verificaProcessoInfracaoEmitida("documento_nao_entregue", $boTransacao);

            if ($rsInfracao->eof()) {
                $boErro     = true;
                $stMensagem = 'Foram encontradas pendências fiscais junto ao processo. (Documentos não entregue)';

                return sistemaLegado::alertaAviso("LSManterLevantamentoDocumentos.php?inCodProcesso=".$param['inCodProcesso']."", $stMensagem, "erro", "aviso", Sessao::getId());
            }
        }

        if ($rsDocumentos->getCampo('documentos_entregue') < $rsDocumentos->getCampo('documentos_existentes')) {
            $rsInfracao = $this->verificaProcessoInfracaoEmitida("documento_entregue_parcial", $boTransacao);

            if ($rsInfracao->eof()) {
                $boErro     = true;
                $stMensagem = 'Foram encontradas pendências fiscais junto ao processo. (Documentos entregue parcialmente)';

                return sistemaLegado::alertaAviso("LSManterLevantamentoDocumentos.php?inCodProcesso=".$param['inCodProcesso']."", $stMensagem, "erro", "aviso", Sessao::getId());
            }
        }

        # Verifica pendencias no prazo dos documentos.
        $this->setCriterio("inf.cod_processo = ".$param['inCodProcesso']);
        $rsDocumentos = $this->verificaProcessoPendenciaDocumentoPrazo();

        for ($i = 0; $i <count($rsDocumentos->arElementos); $i++) {
            if ($rsDocumentos->arElementos[0]["entregou"] > $rsDocumentos->arElementos[0]["prazo"]) {
                $rsInfracao = $this->verificaProcessoInfracaoEmitida("documento_entregue_fora_prazo", $boTransacao);

                if ($rsInfracao->eof()) {
                    $boErro     = true;
                    $stMensagem = 'Foram encontradas pendências fiscais junto ao processo. (Documentos fora do prazo)';

                    return sistemaLegado::alertaAviso("LSManterLevantamentoDocumentos.php?inCodProcesso=".$param['inCodProcesso']."", $stMensagem, "erro", "aviso", Sessao::getId());
                }
            }
        }

        # Verifica geração da planilha de lançamento para ISSQN somente sem faturamento retido na fonte.
        if (!$this->verificaServicoComRetencao($this->codProcesso)) {
            $this->setCriterio('cod_processo = ' . $this->codProcesso);
            $rsProcesso = $this->getProcessoFiscal();

            if ($rsProcesso->getCampo('cod_tipo') == 1) {
                $this->setCriterio(" WHERE cod_processo = ". $this->codProcesso);
                $rsLevantamento = $this->verificaProcessoPendenciaPlanilha();

                if ($rsLevantamento->eof()) {
                    $boErro     = true;
                    $stMensagem = 'Não foi gerado planilha de lançamento fiscal para o processo, favor gerar. (Planilha de lançamento fiscal pendente)';

                    return sistemaLegado::alertaAviso("FMEncerrarProcesso.php?stAcao=encerrar&inCodProcesso=".$param['inCodProcesso']."&inTipoFiscalizacao=".$rsProcesso->getCampo('cod_tipo')."&inInscricao=".$param['inInscricao']."&inCodFiscal=".$param['inCodFiscal'], $stMensagem, "erro", "aviso", Sessao::getId());
                }
            }
        }

        if (!$boErro)
            $obErro = $this->TFISProcessoFiscalEncerrado->inclusao($boTransacao);

        if (!$obErro->ocorreu()) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->TFISProcessoFiscalEncerrado);

            $this->Emitir($param);

            return sistemaLegado::alertaAviso("LSManterProcesso.php?stAcao=encerrar",$this->codProcesso,"alterar","aviso",Sessao::getId());
        }
    }

    public function Alterar($dados)
    {
        $this->codProcesso = $dados["inCodProcesso"];

        $status = true;
        $boFlagTransacao = false;
        $obErro = new Erro;
        $timestamp = date('Y-m-d h:m:s');
        $arExcluir = array();

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        $this->setCriterio("pf.cod_processo = ".$this->codProcesso);
        $arFiscais = $this->recuperaFiscalProcesso();

        if (count($arFiscais)) {
            for ($k=0;$k<count($arFiscais->arElementos);$k++) {
                array_push($arExcluir, $arFiscais->arElementos[$k]["codigo"]);
            }

            if (count($arExcluir) > 0) {
                $this->excluirFiscalProcesso($boTransacao, $arExcluir);
            }
        }

        $this->setCriterio("pf.cod_processo = ".$this->codProcesso);
        $arCreditos = $this->recuperaCreditoProcesso($boTransacao);

        if (count($arCreditos)) {
            for ($k=0;$k<count($arCreditos->arElementos);$k++) {
                $codigo  = $arCreditos->arElementos[$k]["cod_credito"].".";
                $codigo .= $arCreditos->arElementos[$k]["cod_especie"].".";
                $codigo .= $arCreditos->arElementos[$k]["cod_genero"].".";
                $codigo .= $arCreditos->arElementos[$k]["cod_natureza"];
                array_push($arExcluir, $codigo);
            }

            if (count($arExcluir) > 0) {
                $this->excluirCreditoProcesso($boTransacao, $arExcluir);
            }
        }

        $this->setCriterio("pf.cod_processo = ".$this->codProcesso);
        $arGrupos = $this->recuperaGrupoProcesso($boTransacao);

        if (count($arGrupos)) {
            for ($k=0; $k < count($arGrupos->arElementos); $k++) {
                $codigo   = $arGrupos->arElementos[$k]["cod_grupo"]."/";
                $codigo  .= $arGrupos->arElementos[$k]["ano_exercicio"];
                array_push($arExcluir, $codigo);
            }

            if (count($arExcluir) > 0) {
                $this->excluirGrupoProcesso($boTransacao, $arExcluir);
            }
        }

        if (!$obErro->ocorreu()) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->TFISProcessoFiscal);
            sistemaLegado::exibeAviso("Houve um erro ao alterar o processo(".$this->codProcesso.")",'n_incluir','aviso' );
        }

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $arDataInicio = explode("/", $dados["stDataInicial"]);
            $arDataFinal  = explode("/", $dados["stDataFinal"]);

            if ($dados["inTipoFiscalizacao"] == 1) {
                $stDataFinal = $arDataFinal[2].$arDataFinal[1];
                $stDataAtual = date("Ym");

                if ($stDataFinal >= $stDataAtual) {
                    $status = false;

                    return sistemaLegado::exibeAviso("Campo Período inválido, a Data Final deve ser pelo menos um mes anterior a data vigente.",'n_incluir','aviso' );
                }
            } else {
                $stDataFinal = $arDataFinal[2].$arDataFinal[1].$arDataFinal[0];
                $stDataAtual = date("Ymd");

                if ($stDataFinal > $stDataAtual) {
                    $status = false;

                    return sistemaLegado::exibeAviso("Campo Período inválido, a Data Final não pode ser maior que a data atual.",'n_incluir','aviso' );
                }
            }

            $arPrevisaoInicio = explode("/", $dados["bt_dtprevInicio"]);
            $arPrevisaoFinal  = explode("/", $dados["bt_dtprevEncerramento"]);

            $stPrevisaoInicio = $arPrevisaoInicio[2].$arPrevisaoInicio[1].$arPrevisaoInicio[0];
            $stPrevisaoFinal  = $arPrevisaoFinal[2].$arPrevisaoFinal[1].$arPrevisaoFinal[0];
            $stDataInicio     = $arDataInicio[2].$arDataInicio[1].$arDataInicio[0];
            $stDataFinal      = $arDataFinal[2].$arDataFinal[1].$arDataFinal[0];

            if ($stPrevisaoInicio > $stPrevisaoFinal) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão inválido, o campo Previsão de Início deve ser menor que o campo Previsão de Encerramento.",'n_incluir','aviso' );
            }

            if ($stPrevisaoInicio < $stDataInicio) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão de Início deve ser maior que data inicial do período de fiscalização.",'n_incluir','aviso' );
            }

            if ($stPrevisaoFinal < $stDataInicio) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão de Encerramento deve ser menor que data final do período de fiscalização.",'n_incluir','aviso' );
            }

            $this->preparaProcessoProtocolo($dados["stChaveProcesso"]);
            $this->TFISProcessoFiscal->setDado( "cod_processo", $this->codProcesso);
            $this->TFISProcessoFiscal->setDado( "cod_processo_protocolo", $this->codProtocolo );
            $this->TFISProcessoFiscal->setDado( "numcgm", 1 );
            $this->TFISProcessoFiscal->setDado( "cod_tipo", $dados["inTipoFiscalizacao"]);
            $this->TFISProcessoFiscal->setDado( "cod_natureza", $dados["inNaturezaFiscalizacao"]);
            $this->TFISProcessoFiscal->setDado( "ano_exercicio", $this->anoExercicio);
            $this->TFISProcessoFiscal->setDado( "periodo_inicio", $dados["stDataInicial"]);
            $this->TFISProcessoFiscal->setDado( "periodo_termino", $dados["stDataFinal"]);
            $this->TFISProcessoFiscal->setDado( "previsao_inicio", $dados["bt_dtprevInicio"]);
            $this->TFISProcessoFiscal->setDado( "previsao_termino", $dados["bt_dtprevEncerramento"]);
            $this->TFISProcessoFiscal->setDado( "observacao", $dados["bt_obs"]);

            $obErro = $this->TFISProcessoFiscal->alteracao( $boTransacao );
        }

        if (count($dados["fiscal"]) > 0) {
            $arIncluir = array();
            $arIncluir = $dados["fiscal"];

            if (count($arIncluir) > 0) {
                $this->incluirFiscalProcesso($boTransacao, $arIncluir);
            }
        } else {
            $status = false;
            sistemaLegado::exibeAviso("Deve existir pelo menos um fiscal inserido para o processo(".$this->codProcesso.")",'n_incluir','aviso' );
        }
        if ($dados["inTipoFiscalizacao"] == 1) {
            if (count($dados["credito"]) > 0) {
                $arIncluir = array();
                $arIncluir = $dados["credito"];

                if (count($arIncluir) > 0) {
                    $this->incluirCreditoProcesso($boTransacao, $arIncluir);
                }
            }

            if (count($dados["grupo"]) > 0) {
                $arIncluir = array();
                $arIncluir = $dados["grupo"];

                if (count($arIncluir) > 0) {
                    $this->incluirGrupoProcesso($boTransacao,$arIncluir);
                }
            }

            if (count($dados["credito"]) == 0 && count($dados["grupo"]) == 0) {
                $status = false;
                sistemaLegado::exibeAviso("Deve existir pelo menos um crédito/grupo de crédito inserido para o processo(".$this->codProcesso.")",'n_incluir','aviso' );
            }
        }

        switch ($dados["inTipoFiscalizacao"]) {
            case 1:
                $this->TFISProcessoFiscalEmpresa->setDado("cod_processo",$this->codProcesso);
                $this->TFISProcessoFiscalEmpresa->setDado("inscricao_economica",$dados["inInscricaoEconomica"]);
                $obInscricaoEconomica = $this->TFISProcessoFiscalEmpresa->alteracao( $boTransacao );

                if ($obInscricaoEconomica->ocorreu()) {
                    $status = false;
                    sistemaLegado::exibeAviso( "Houve erro ao incluir a inscrição economica para o processo(".$dados["inInscricaoEconomica"].")", 'n_incluir', 'aviso' );
                }
            break;

            case 2:
                $this->TFISProcessoFiscalObras->setDado("cod_processo",$this->codProcesso);
                $this->TFISProcessoFiscalObras->setDado("inscricao_municipal",$dados["inCodImovel"]);
                $this->TFISProcessoFiscalObras->setDado("cod_local",$this->codLocal);
                $obInscricaoImobiliaria = $this->TFISProcessoFiscalObras->alteracao( $boTransacao );

                if ($obInscricaoImobiliaria->ocorreu()) {
                    $status = false;
                    sistemaLegado::exibeAviso("Houve erro ao incluir a inscrição imobiliaria para o processo(".$dados["inCodImovel"].")", 'n_incluir', 'aviso');
                }
            break;
        }

        if (!$obErro->ocorreu() && $status) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->TFISProcessoFiscal);

            return sistemaLegado::alertaAviso("LSManterProcesso.php?stAcao=alterar",$this->codProcesso,"alterar","aviso",Sessao::getId());
        }
    }

    public function Incluir($mapeamento, $dados)
    {
        $status = true;
        $boFlagTransacao = false;
        $obErro = new Erro;
        $timestamp = date('Y-m-d h:m:s');

        $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $arDataInicio = explode("/", $dados["stDataInicial"]);
            $arDataFinal  = explode("/", $dados["stDataFinal"]);

            if ($dados["inTipoFiscalizacao"] == 1) {
                $stDataFinal = $arDataFinal[2].$arDataFinal[1];
                $stDataAtual = date("Ym");

                if ($stDataFinal >= $stDataAtual) {
                    $status = false;

                    return sistemaLegado::exibeAviso("Campo Período inválido, a Data Final deve ser pelo menos um mes anterior a data vigente.",'n_incluir','aviso' );
                }
            } else {
                $stDataFinal = $arDataFinal[2].$arDataFinal[1].$arDataFinal[0];
                $stDataAtual = date("Ymd");

                if ($stDataFinal > $stDataAtual) {
                    $status = false;

                    return sistemaLegado::exibeAviso("Campo Período inválido, a Data Final não pode ser maior que a data atual.",'n_incluir','aviso' );
                }
            }
            $arPrevisaoInicio = explode("/", $dados["bt_dtprevInicio"]);
            $arPrevisaoFinal  = explode("/", $dados["bt_dtprevEncerramento"]);

            $stPrevisaoInicio = $arPrevisaoInicio[2].$arPrevisaoInicio[1].$arPrevisaoInicio[0];
            $stPrevisaoFinal  = $arPrevisaoFinal[2].$arPrevisaoFinal[1].$arPrevisaoFinal[0];
            $stDataInicio     = $arDataInicio[2].$arDataInicio[1].$arDataInicio[0];
            $stDataFinal      = $arDataFinal[2].$arDataFinal[1].$arDataFinal[0];

            if ($stPrevisaoInicio > $stPrevisaoFinal) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão inválido, o campo Previsão de Início deve ser menor que o campo Previsão de Encerramento.",'n_incluir','aviso' );
            }

            if ($stPrevisaoInicio < $stDataInicio) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão de Início deve ser maior que data inicial do período de fiscalização.",'n_incluir','aviso' );
            }

            if ($stPrevisaoFinal < $stDataInicio) {
                $status = false;

                return sistemaLegado::exibeAviso("Campo Previsão de Encerramento deve ser menor que data final do período de fiscalização.",'n_incluir','aviso' );
            }

            $this->preparaProcessoProtocolo($dados["stChaveProcesso"]);
            $this->$mapeamento->proximoCod( $this->codProcesso, $boTransacao );
            $this->$mapeamento->setDado( "cod_processo", $this->codProcesso );
            $this->$mapeamento->setDado( "cod_processo_protocolo", $this->codProtocolo );
            $this->$mapeamento->setDado( "numcgm", 1 );
            $this->$mapeamento->setDado( "cod_tipo", $dados["inTipoFiscalizacao"]);
            $this->$mapeamento->setDado( "cod_natureza", $dados["inNaturezaFiscalizacao"]);
            $this->$mapeamento->setDado( "ano_exercicio", $this->anoExercicio);
            $this->$mapeamento->setDado( "periodo_inicio", $dados["stDataInicial"]);
            $this->$mapeamento->setDado( "periodo_termino", $dados["stDataFinal"]);
            $this->$mapeamento->setDado( "previsao_inicio", $dados["bt_dtprevInicio"]);
            $this->$mapeamento->setDado( "previsao_termino", $dados["bt_dtprevEncerramento"]);
            $this->$mapeamento->setDado( "observacao", $dados["bt_obs"]);

            $obProcessoFiscal = $this->$mapeamento->inclusao( $boTransacao );
        } else {
            $status = false;

            return sistemaLegado::alertaAviso("FMManterProcesso.php",$this->codProcesso,"incluir","aviso",Sessao::getId());
        }

        if ( $obProcessoFiscal->ocorreu() == false ) {
            if (count($dados["fiscal"]) > 0) {
                $arFiscais = $dados["fiscal"];

                for ($k=0;$k < count($arFiscais);$k++) {
                    $this->TFISFiscalProcessoFiscal->setDado("cod_fiscal",$arFiscais[$k]);
                    $this->TFISFiscalProcessoFiscal->setDado("cod_processo",$this->codProcesso);
                    $this->TFISFiscalProcessoFiscal->setDado("status","'A'");
                    $obFiscal = $this->TFISFiscalProcessoFiscal->inclusao( $boTransacao );

                    if ($obFiscal->ocorreu()) {
                        $status = false;
                        sistemaLegado::exibeAviso( "Houve erro ao incluir fiscal para o processo(".$arFiscais[$k].")",'n_incluir','aviso' );
                    }
                }
            } else {
                $status = false;
                sistemaLegado::exibeAviso("Deve existir pelo menos um fiscal inserido para o processo(".$this->codProcesso.")",'n_incluir','aviso' );
            }
        } else {
            $status = false;

            return sistemaLegado::alertaAviso("FMManterProcesso.php",$this->codProcesso,"incluir","aviso",Sessao::getId());
        }

        switch ($dados["inTipoFiscalizacao"]) {
                case 1:
                $this->TFISProcessoFiscalEmpresa->setDado("cod_processo",$this->codProcesso);
                $this->TFISProcessoFiscalEmpresa->setDado("inscricao_economica",$dados["inInscricaoEconomica"]);

                $obProcessoFiscal = $this->TFISProcessoFiscalEmpresa->inclusao( $boTransacao );

                if ($obProcessoFiscal->ocorreu() == false) {
                    if (count($dados["grupo"]) > 0) {
                        $arGrupo = $dados["grupo"];
                        for ($k=0;$k < count($arGrupo);$k++) {
                            $this->preparaGrupo($arGrupo[$k]);
                            $this->TFISProcessoFiscalGrupo->setDado("cod_processo",$this->codProcesso);
                            $this->TFISProcessoFiscalGrupo->setDado("ano_exercicio", $this->anoExercicio);
                            $this->TFISProcessoFiscalGrupo->setDado("cod_grupo",$this->codGrupo);

                            $obGrupoCredito = $this->TFISProcessoFiscalGrupo->inclusao( $boTransacao );
                            if ($obGrupoCredito->ocorreu()) {
                                $status = false;
                                sistemaLegado::exibeAviso( "Houve erro ao incluir o grupo de crédito para o processo(".$arGrupo[$k].")",'n_incluir','aviso' );
                            }
                        }
                    }
                } else {
                    $status = false;

                    return sistemaLegado::alertaAviso("FMManterProcesso.php",$this->codProcesso,"incluir","aviso",Sessao::getId());
                }

                if (count($dados["grupo"]) == 0) {
                    $status = false;
                    sistemaLegado::exibeAviso("Deve existir pelo menos um crédito/grupo de crédito inserido para o processo(".$this->codProcesso.")",'n_incluir','aviso' );
                }
                break;
                case 2:
                    $this->TFISProcessoFiscalObras->setDado("cod_processo",$this->codProcesso);
                    $this->TFISProcessoFiscalObras->setDado("inscricao_municipal",$dados["inCodImovel"]);
                    $this->TFISProcessoFiscalObras->setDado("cod_local",$this->codLocal);
                    $obInscricaoImobiliaria = $this->TFISProcessoFiscalObras->inclusao( $boTransacao );

                    if ($obInscricaoImobiliaria->ocorreu()) {
                        $status = false;
                        sistemaLegado::exibeAviso( "Houve erro ao incluir a inscrição imobiliaria para o processo(".$dados["inCodImovel"].")", 'n_incluir', 'aviso' );
                    }
                break;
        }

        if ($status) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->$mapeamento);

            return sistemaLegado::alertaAviso("FLManterProcesso.php?stAcao=incluir", $this->codProcesso, "incluir", "aviso", Sessao::getId());
        }
    }

    private function incluirFiscalProcesso($boTransacao, $arFiscais)
    {
        for ($k=0;$k < count($arFiscais);$k++) {
            $this->TFISFiscalProcessoFiscal->setDado("cod_fiscal",$arFiscais[$k]);
            $this->TFISFiscalProcessoFiscal->setDado("cod_processo",$this->codProcesso);
            $this->TFISFiscalProcessoFiscal->setDado("status","'A'");

            $obFiscal = $this->TFISFiscalProcessoFiscal->inclusao( $boTransacao );

            if ($obFiscal->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao incluir fiscal para o processo(".$arFiscais[$k].")",'n_incluir','aviso' );
            }
        }
    }

    private function incluirCreditoProcesso($boTransacao, $arCreditos)
    {
        for ($k=0;$k < count($arCreditos);$k++) {

            $this->preparaCredito($arCreditos[$k]);

            $this->TFISProcessoFiscalCredito->setDado("cod_processo",$this->codProcesso);
            $this->TFISProcessoFiscalCredito->setDado("cod_natureza",$this->codNatureza);
            $this->TFISProcessoFiscalCredito->setDado("cod_genero",$this->codGenero);
            $this->TFISProcessoFiscalCredito->setDado("cod_especie",$this->codEspecie);
            $this->TFISProcessoFiscalCredito->setDado("cod_credito",$this->codCredito);

            $obCredito = $this->TFISProcessoFiscalCredito->inclusao( $boTransacao );

            if ($obCredito->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao incluir o credito(".$arCreditos[$k].") para o processo(".$this->codProcesso.")",'n_incluir','aviso' );
            }
        }
    }

    private function incluirGrupoProcesso($boTransacao,$arGrupo)
    {
        for ($k=0;$k < count($arGrupo);$k++) {

            $this->preparaGrupo($arGrupo[$k]);

            $this->TFISProcessoFiscalGrupo->setDado("cod_grupo",$this->codGrupo);
            $this->TFISProcessoFiscalGrupo->setDado("cod_processo",$this->codProcesso);
            $this->TFISProcessoFiscalGrupo->setDado("ano_exercicio", $this->anoExercicio);

            $obGrupoCredito = $this->TFISProcessoFiscalGrupo->inclusao( $boTransacao );

            if ($obGrupoCredito->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao incluir o grupo de crédito para o processo(".$arGrupo[$k].")",'n_incluir','aviso' );
            }
        }
    }

    private function excluirFiscalProcesso($boTransacao,$arFiscais)
    {
        for ($k=0;$k < count($arFiscais);$k++) {
            $this->setCriterio("fpf.cod_processo = {$this->codProcesso} and fpf.cod_fiscal = {$arFiscais[$k]}");

            $rsFiscal = $this->recuperaFiscalProcesso($boTransacao);

            $this->TFISFiscalProcessoFiscal->setDado("cod_fiscal",$arFiscais[$k]);
            $this->TFISFiscalProcessoFiscal->setDado("cod_processo",$this->codProcesso);

            $obFiscal = $this->TFISFiscalProcessoFiscal->exclusao();

            if ($obFiscal->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao excluir fiscal do processo(".$arFiscais[$k].")",'n_incluir','aviso' );
            }
        }
    }

    private function excluirCreditoProcesso($boTransacao,$arCredito)
    {
        for ($k=0;$k < count($arCredito);$k++) {

            $this->preparaCredito($arCredito[$k]);

            $condicao  = $this->codProcesso                   ." AND ";
            $condicao .= "cod_credito  = ".$this->codCredito  ." AND ";
            $condicao .= "cod_especie  = ".$this->codEspecie  ." AND ";
            $condicao .= "cod_genero   = ".$this->codGenero   ." AND ";
            $condicao .= "cod_natureza = ".$this->codNatureza;

            $this->TFISProcessoFiscalCredito->setDado("cod_processo",$condicao);

            $obCredito = $this->TFISProcessoFiscalCredito->exclusao();

            if ($obCredito->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao excluir o credito do processo(".$this->codCredito.")",'n_incluir','aviso' );
            }
        }
    }

    private function excluirGrupoProcesso($boTransacao,$arGrupo)
    {
        for ($k=0;$k < count($arGrupo);$k++) {

            $this->preparaGrupo($arGrupo[$k]);

            $condicao = $this->codProcesso." AND ano_exercicio = ".$this->anoExercicio." AND cod_grupo = ".$this->codGrupo;
            $this->TFISProcessoFiscalGrupo->setDado("cod_processo",$condicao);

            $obGrupoCredito = $this->TFISProcessoFiscalGrupo->exclusao();

            if ($obGrupoCredito->ocorreu()) {
                sistemaLegado::exibeAviso( "Houve erro ao excluir o grupo de crédito do processo(".$arGrupo[$k].")",'n_incluir','aviso' );
            }
        }
    }

    public function recuperaGrupoProcesso($boTransacao = null)
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaGrupo";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql, true, $boTransacao);
    }

    public function recuperaCreditoProcesso($boTransacao = null)
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaCredito";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql, true, $boTransacao);
    }

    public function recuperaGrupoCreditoProcesso($boTransacao = null)
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaInicioProcessoFiscalListaCreditoGrupo";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql, true, $boTransacao);
    }

    public function recuperaFiscalProcesso($boTransacao = null)
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaFiscaisProcesso";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql, true, $boTransacao);
    }

    public function recuperaProcessoEmpresa($boTransacao = null)
    {
        $mapeamento = "TFISProcessoFiscalEmpresa";
        $metodo = "recuperaTodos";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql, true, $boTransacao);
    }

    private function preparaProcessoProtocolo($processoProtocolo)
    {
        $valor = explode("/",$processoProtocolo);
        $this->codProtocolo	= $valor[0];
        $this->anoExercicio	= $valor[1];
    }

    private function preparaGrupo($grupo)
    {
        $valor = explode("/",$grupo);
        $this->codGrupo 	= intval($valor[0]);
        $this->anoExercicio = intval($valor[1]);
    }

    private function preparaCredito($credito)
    {
        $credito = explode(".",$credito);
        $this->codCredito 	= intval($credito[0]);
        $this->codEspecie 	= intval($credito[1]);
        $this->codGenero 	= intval($credito[2]);
        $this->codNatureza 	= intval($credito[3]);
    }

    public function getFundamentacaoTributaria()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaFundamentacaoLegal";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql);
    }

    public function getFundamentacaoObras()
    {
        $mapeamento = "TFISProcessoFiscal";
        $metodo = "recuperaFundamentacaoLegalObra";

        return $this->CallMap($mapeamento, $metodo, $this->CriterioSql);
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

    public function getInfracoesTermo()
    {
        return $this->CallMap("TFISNotificacaoFiscalizacao","recuperaTermosInfracoes",$this->CriterioSql);
    }

    public function emitirTermo($param)
    {
        $obRFISEmitirDocumento = new RFISEmitirDocumento;
        $rsProcesso = $this->CallMap("TFISProcessoFiscal","recuperaEnderecoProcesso",$this->CriterioSql);
        $arData = array();
        //dados processo
        $arData["#processo"]["num_processo"]    = (string) $param['hdnCodProcessoFiscal'];
        //dados cgm
        $arData["#cgm"]["nom_cgm"]              = (string) $rsProcesso->getCampo('nom_cgm');
        $arData["#cgm"]["logradouro"]           = (string) $rsProcesso->getCampo('logradouro_i');
        $arData["#cgm"]["nom_bairro"]           = (string) $rsProcesso->getCampo('nom_bairro');
        $arData["#cgm"]["cep"]                  = (string) $rsProcesso->getCampo('cep');
        $arData["#cgm"]["nom_municipio"]        = (string) $rsProcesso->getCampo('nom_municipio');
        $arData["#cgm"]["nom_uf"]               = (string) $rsProcesso->getCampo('sigla_uf');
        $arData["#cgm"]["inscricao_imobiliaria"]= (string) $param['inCodInscricaoImobiliaria'];
        if ($rsProcesso->getCampo('cpf') == '') {
           $arData["#cgm"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cnpj');
        } else {
           $arData["#cgm"]["cpf_cnpj"]          = (string) $rsProcesso->getCampo('cpf');
        }

        //dados encerramento
        $arDataEncerramento =  explode('/', $param['dtEncerramento']);
        $arData["#encerramento"]["dia"]         = (string) $arDataEncerramento[0];
        $arData["#encerramento"]["mes"]         = $this->converteMes((string) $arDataEncerramento[1]);
        $arData["#encerramento"]["ano"]         = (string) $arDataEncerramento[2];

        //dados infrações e termos por infrações...
        $this->setCriterio("fpf.cod_processo = ".$param['hdnCodProcessoFiscal']." and afis.num_notificacao = ".$param['inCodNotificacaoProcesso']);

        $rsInfracoes = $this->getInfracoesTermo();
        $valorTotal = 0;
        for ($j = 0; $j < count($rsInfracoes->arElementos); $j++) {
            $arInfracoes["cod_infracao"]        = (string) $rsInfracoes->arElementos[$j]["cod_infracao"];
               $arInfracoes["nom_infracao"]        = (string) $rsInfracoes->arElementos[$j]["nom_infracao"];
               $arInfracoes["nom_norma"]           = (string) $rsInfracoes->arElementos[$j]["nom_norma"];
               $arInfracoes["observacao_fnt"]      = (string) $rsInfracoes->arElementos[$j]["observacao_fnt"];
               $arInfracoes["observacao_fnti"]     = (string) $rsInfracoes->arElementos[$j]["observacao_fnti"];
               $arInfracoes["cod_penalidade"]      = (string) $rsInfracoes->arElementos[$j]["cod_penalidade"];
               $arInfracoes["nom_penalidade"]      = (string) $rsInfracoes->arElementos[$j]["nom_penalidade"];
               $arInfracoes["dt_ocorrencia"]       = (string) $rsInfracoes->arElementos[$j]["dt_ocorrencia"];
               $arInfracoes["num_notificacao"]     = (string) $rsInfracoes->arElementos[$j]["num_notificacao"];
               $arMunicipal["inscricao_municipal"] = (string) $rsInfracoes->arElementos[$j]["inscricao_municipal"];

            $arData["#infracao"][$j]                = $arInfracoes;
        }

        for ($j = 0; $j < count($rsInfracoes->arElementos); $j++) {
              $arObservacoes["descricao"]             = (string) $rsInfracoes->arElementos[$j]["observacao_fnt"];
            $arData["#observacao"][$j]              = $arObservacoes;
        }

        $arData["#notificacao_termo"]["num_notificacao"]= (string) $param['inCodNotificacaoProcesso'];
        $arData["#notificacao_termo"]["ano_notificacao"]= (string) Sessao::read('exercicio');
        $arData["#notificacao_termo"]["inscricao"]= (string) $arMunicipal["inscricao_municipal"];

        return $arData;
    }

}

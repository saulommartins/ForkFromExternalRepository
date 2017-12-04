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
    * Classe para exportar arquivos XML com informações da GT
    * Data de Criação   : 05/06/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso: uc-06.01.22
*/

include_once(CAM_GPC_ITBI_IPTU_MAPEAMENTO."TExportacaoGT.class.php");
include_once(CLA_ARQUIVO_ZIP);

class RExportacaoGT
{
    private $obXML;
    private $obTExportacaoGT;
    private $obArquivoZip;
    private $nomeDocumento;
    private $inSemestre;

    public function __construct()
    {
        $this->obXML = new XMLWriter();
        $this->obTExportacaoGT = new TExportacaoGT();
        $this->obArquivoZip = new ArquivoZip();
    }

    public function iniciaDocumento()
    {
        # Cria memoria para armazenar a saida
        $this->obXML->openMemory();

        # Seta identação para visualizar melhor
        $this->obXML->setIndent(true);

        # Inicia o cabeçalho do documento XML
        $this->obXML->startDocument( '1.0' , 'UTF-8', 'yes' );
    }

    //seta o nome do documento xml que vai ser gerado
    private function setNomeDocumento($nomeDocumento)
    {
        $this->nomeDocumento = $nomeDocumento;
        if (!preg_match("/xml|XML/", $nomeDocumento)) {
            $this->nomeDocumento .= ".xml";
        }
    }

    //seta o bimestre ao qual os documentos vão ser gerados
    public function setSemestre($inSemestre)
    {
        $this->inSemestre = $inSemestre;
    }

    public function finalizaDocumento()
    {
        file_put_contents(CAM_FRAMEWORK."tmp/".$this->nomeDocumento, $this->obXML->outputMemory(true));
    }

    public function geraDocumentoXMLIPTU()
    {
        $arResult = $this->geraDadosXMLIPTU();

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "IPTU");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            $this->obXML->startElement("MUNICIPIO");
            $this->obXML->writeAttribute("codigo", $arResult[key($arResult)]['codigo']);
            $this->obXML->writeAttribute("nome", $arResult[key($arResult)]['nome']);
            $this->obXML->writeAttribute("ano", $arResult[key($arResult)]['ano']);
            $this->obXML->writeAttribute("semestre", $arResult[key($arResult)]['semestre']);
            $this->obXML->endElement();

            foreach ($arResult as $result) {
                $this->obXML->startElement("imovel");
                $this->obXML->writeAttribute("matricula", $result['matricula']);
                $this->obXML->writeAttribute("zona", utf8_encode($result['zona']));
                $this->obXML->writeAttribute("nro_registro_iptu", $result['nro_registro_iptu']);

                //proprietários
                $this->obXML->startElement("PROPRIETARIOS");
                foreach ($result['proprietarios'] as $proprietario) {
                    $this->obXML->startElement("proprietario");
                    $this->obXML->writeAttribute("nome", utf8_encode($proprietario['nome']));
                    $this->obXML->writeAttribute("cpf_cnpj", $proprietario['cpf_cnpj']);
                    // $this->obXML->writeAttribute("RG", $proprietario['rg']);
                    $this->obXML->Text('RG="'.$proprietario['rg'].'"');
                    $this->obXML->endElement();
                }
                // finaliza elemento PROPRIETARIOS
                $this->obXML->endElement();

                foreach ($result['logradouros'] as $logradouro) {
                    $this->obXML->startElement("logradouro");
                    $this->obXML->writeAttribute("tipo", utf8_encode($logradouro['tipo']));
                    $this->obXML->writeAttribute("nome", utf8_encode($logradouro['nome']));
                    $this->obXML->writeAttribute("nro", $logradouro['nro']);
                    $this->obXML->writeAttribute("compl", utf8_encode($logradouro['compl']));
                    $this->obXML->writeAttribute("lote", utf8_encode($logradouro['lote']));
                    $this->obXML->writeAttribute("bairro", utf8_encode($logradouro['bairro']));
                    $this->obXML->writeAttribute("vila", utf8_encode($logradouro['vila']));
                    $this->obXML->writeAttribute("quadra", utf8_encode($logradouro['quadra']));
                    $this->obXML->writeAttribute("setor", utf8_encode($logradouro['setor']));
                    $this->obXML->endElement();
                }

                foreach ($result['terrenos'] as $terreno) {
                    $this->obXML->startElement("terreno");
                    $this->obXML->writeAttribute("area_total", $terreno['area_total_terreno']);
                    $this->obXML->writeAttribute("testada", $terreno['testada']);
                    $this->obXML->writeAttribute("codigo_situacao_quadra", $terreno['codigo_situacao_quadra']);
                    $this->obXML->writeAttribute("valor", $terreno['valor_terreno']);
                    $this->obXML->endElement();
                }

                $this->obXML->startElement("BENFEITORIAS");
                foreach ($result['benfeitorias'] as $benfeitoria) {
                    $this->obXML->startElement("edificacao");
                    $this->obXML->writeAttribute("codigo_especie_urbana", $benfeitoria['cod_especie_urbana']);
                    $this->obXML->writeAttribute("area_total", $benfeitoria['area_total_edificacao']);
                    $this->obXML->writeAttribute("area_privativa", $benfeitoria['area_privativa_edificacao']);
                    $this->obXML->writeAttribute("codigo_tipo_material", $benfeitoria['cod_tipo_material']);
                    $this->obXML->writeAttribute("codigo_padrao_construtivo", $benfeitoria['cod_padrao_construtivo']);
                    $this->obXML->writeAttribute("ano_construcao", $benfeitoria['ano_construcao']);
                    $this->obXML->writeAttribute("valor", $benfeitoria['valor_edificacao']);
                    $this->obXML->writeAttribute("tipo_utilizacao", utf8_encode($benfeitoria['tipo_utilizacao']));
                    $this->obXML->endElement();
                }
                //finaliza elemento BENFEITORIAS
                $this->obXML->endElement();

                // finaliza elemento imovel
                $this->obXML->endElement();
            }
        }

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function geraDadosXMLIPTU()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        $this->obTExportacaoGT->listarExportacaoIPTU($rsRecordSet, $stFiltro);

        $this->setNomeDocumento("IPTU_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $arResult = array();
        while (!$rsRecordSet->eof()) {
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['codigo'] = $rsRecordSet->getCampo('codigo');
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['nome'] = $rsRecordSet->getCampo('nom_municipio');
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['ano'] = Sessao::getExercicio();
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['semestre'] = $this->inSemestre;

            ################INFORMAÇÕES DO IMÓVEL######################
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['matricula'] = $rsRecordSet->getCampo('matricula_imovel');
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['zona'] = $rsRecordSet->getCampo('zona');
            $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['nro_registro_iptu'] = $rsRecordSet->getCampo('nro_registro_iptu');
            ###########################################################

            ################INFORMAÇÕES DO PROPRIETÁRIO################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['proprietarios'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['proprietarios'] = array();
            }
            $arProprietario = array();
            $arProprietario['nome'] = $rsRecordSet->getCampo('nome_proprietario');
            $arProprietario['cpf_cnpj'] = $rsRecordSet->getCampo('cpf_cnpj');
            $arProprietario['rg'] = $rsRecordSet->getCampo('rg');

            if (!in_array($arProprietario, $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['proprietarios'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['proprietarios'][] = $arProprietario;
            }
            ###########################################################

            ################INFORMAÇÕES DO LOGRADOURO##################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['logradouros'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['logradouros'] = array();
            }
            $arLogradouro = array();
            $arLogradouro['tipo'] = $rsRecordSet->getCampo('nom_tipo');
            $arLogradouro['nome'] = $rsRecordSet->getCampo('nom_logradouro');
            $arLogradouro['nro'] = $rsRecordSet->getCampo('numero');
            $arLogradouro['compl'] = $rsRecordSet->getCampo('complemento');
            $arLogradouro['lote'] = $rsRecordSet->getCampo('lote');
            $arLogradouro['bairro'] = $rsRecordSet->getCampo('bairro');
            $arLogradouro['vila'] = $rsRecordSet->getCampo('vila');
            $arLogradouro['quadra'] = $rsRecordSet->getCampo('quadra');
            $arLogradouro['setor'] = $rsRecordSet->getCampo('setor');

            if (!in_array($arLogradouro, $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['logradouros'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['logradouros'][] = $arLogradouro;
            }
            ###########################################################

            ################INFORMAÇÕES DO TERRENO#####################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['terrenos'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['terrenos'] = array();
            }
            $arTerreno = array();
            $arTerreno['area_total_terreno'] = number_format($rsRecordSet->getCampo('area_total_terreno'), 2, ',', '');
            $arTerreno['testada'] = number_format($rsRecordSet->getCampo('testada'), 2, ',', '');
            $arTerreno['codigo_situacao_quadra'] = $rsRecordSet->getCampo('codigo_situacao_quadra');
            $arTerreno['valor_terreno'] = number_format($rsRecordSet->getCampo('valor_terreno'), 2, ',', '');

            if (!in_array($arTerreno, $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['terrenos'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['terrenos'][] = $arTerreno;
            }
            ###########################################################

            ################INFORMAÇÕES DA BENFEITORIA#################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['benfeitorias'] = array();
            }
            $arBenfeitorias = array();
            $arBenfeitorias['cod_especie_urbana'] = $rsRecordSet->getCampo('cod_especie_urbana');
            $arBenfeitorias['area_total_edificacao'] = number_format($rsRecordSet->getCampo('area_total_edificacao'), 2, ',', '');
            $arBenfeitorias['area_privativa_edificacao'] = number_format($rsRecordSet->getCampo('area_privativa_edificacao'), 2, ',', '');
            $arBenfeitorias['cod_tipo_material'] = $rsRecordSet->getCampo('cod_tipo_material');
            $arBenfeitorias['cod_padrao_construtivo'] = $rsRecordSet->getCampo('cod_padrao_construtivo');
            $arBenfeitorias['ano_construcao'] = $rsRecordSet->getCampo('ano_construcao');
            $arBenfeitorias['valor_edificacao'] = number_format($rsRecordSet->getCampo('valor_edificacao'), 2, ',', '');
            $arBenfeitorias['tipo_utilizacao'] = $rsRecordSet->getCampo('tipo_utilizacao');

            if (!in_array($arBenfeitorias, $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_registro_iptu')]['benfeitorias'][] = $arBenfeitorias;
            }
            ###########################################################

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDocumentoXMLITBIUrbano()
    {
        $arResult = $this->geraDadosXMLITBIUrbano();

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "ITBI-URBANO");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            $this->obXML->startElement("MUNICIPIO");
            $this->obXML->writeAttribute("codigo", $arResult[key($arResult)]['codigo']);
            $this->obXML->writeAttribute("nome", $arResult[key($arResult)]['nome']);
            $this->obXML->writeAttribute("ano", $arResult[key($arResult)]['ano']);
            $this->obXML->writeAttribute("semestre", $arResult[key($arResult)]['semestre']);
            $this->obXML->endElement();

            foreach ($arResult as $result) {
                $this->obXML->startElement("imovel");
                $this->obXML->writeAttribute("matricula", $result['matricula']);
                $this->obXML->writeAttribute("zona", utf8_encode($result['zona']));
                $this->obXML->writeAttribute("nro_guia_ITBI", $result['nro_guia_itbi']);
                $this->obXML->writeAttribute("utilizacao", utf8_encode($result['utilizacao']));

                //transmitentes
                $this->obXML->startElement("TRANSMITENTES");
                foreach ($result['transmitentes'] as $transmitentes) {
                    $this->obXML->startElement("transmitente");
                    $this->obXML->writeAttribute("nome", utf8_encode($transmitentes['nome']));
                    $this->obXML->writeAttribute("cpf_cnpj", $transmitentes['cpf_cnpj']);
                    $this->obXML->endElement();
                }
                // finaliza elemento TRANSMITENTES
                $this->obXML->endElement();

                //adquirentes
                $this->obXML->startElement("ADQUIRENTES");
                foreach ($result['adquirentes'] as $adquirentes) {
                    $this->obXML->startElement("adquirente");
                    $this->obXML->writeAttribute("nome", utf8_encode($adquirentes['nome']));
                    $this->obXML->writeAttribute("cpf_cnpj", $adquirentes['cpf_cnpj']);
                    $this->obXML->endElement();
                }
                // finaliza elemento ADQUIRENTES
                $this->obXML->endElement();

                foreach ($result['logradouros'] as $logradouro) {
                    $this->obXML->startElement("logradouro");
                    $this->obXML->writeAttribute("tipo", utf8_encode($logradouro['tipo']));
                    $this->obXML->writeAttribute("nome", utf8_encode($logradouro['nome']));
                    $this->obXML->writeAttribute("nro", $logradouro['nro']);
                    $this->obXML->writeAttribute("compl", utf8_encode($logradouro['compl']));
                    $this->obXML->writeAttribute("lote", utf8_encode($logradouro['lote']));
                    $this->obXML->writeAttribute("bairro", utf8_encode($logradouro['bairro']));
                    $this->obXML->writeAttribute("vila", utf8_encode($logradouro['vila']));
                    $this->obXML->writeAttribute("quadra", utf8_encode($logradouro['quadra']));
                    $this->obXML->writeAttribute("setor", utf8_encode($logradouro['setor']));
                    $this->obXML->endElement();
                }

                foreach ($result['terrenos'] as $terreno) {
                    $this->obXML->startElement("terreno");
                    $this->obXML->writeAttribute("area_total_m2", $terreno['area_total_m2']);
                    $this->obXML->writeAttribute("area_transmitida_m2", $terreno['area_transmitida_m2']);
                    $this->obXML->writeAttribute("testada", $terreno['testada']);
                    $this->obXML->writeAttribute("codigo_situacao_quadra", $terreno['codigo_situacao_quadra']);
                    $this->obXML->writeAttribute("valor_declarado", $terreno['valor_declarado']);
                    $this->obXML->writeAttribute("valor_avaliado", $terreno['valor_avaliado']);
                    $this->obXML->writeAttribute("data_avaliacao", $terreno['data_avaliacao']);
                    $this->obXML->endElement();
                }

                $this->obXML->startElement("BENFEITORIAS");
                foreach ($result['benfeitorias'] as $benfeitoria) {
                    $this->obXML->startElement("edificacao");
                    $this->obXML->writeAttribute("codigo_especie_urbana", $benfeitoria['codigo_especie_urbana']);
                    $this->obXML->writeAttribute("area_total_m2", $benfeitoria['area_total_m2']);
                    $this->obXML->writeAttribute("area_transmitida_m2", $benfeitoria['area_transmitida_m2']);
                    $this->obXML->writeAttribute("area_privativa", $benfeitoria['area_privativa']);
                    $this->obXML->writeAttribute("codigo_tipo_material", $benfeitoria['codigo_tipo_material']);
                    $this->obXML->writeAttribute("codigo_padrao_construtivo", $benfeitoria['codigo_padrao_construtivo']);
                    $this->obXML->writeAttribute("ano_construcao", $benfeitoria['ano_construcao']);
                    $this->obXML->writeAttribute("valor_declarado", $benfeitoria['valor_declarado']);
                    $this->obXML->writeAttribute("valor_avaliado", $benfeitoria['valor_avaliado']);
                    $this->obXML->writeAttribute("data_avaliacao", $benfeitoria['data_avaliacao']);
                    $this->obXML->writeAttribute("Tipo_utilizacao", utf8_encode($benfeitoria['tipo_utilizacao']));
                    $this->obXML->endElement();
                }
                //finaliza elemento BENFEITORIAS
                $this->obXML->endElement();

                // finaliza elemento imovel
                $this->obXML->endElement();
            }
        }

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    private function geraDadosXMLITBIUrbano()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        $this->obTExportacaoGT->listarExportacaoITBIUrbano($rsRecordSet, $stFiltro);

        $this->setNomeDocumento("ITBIU_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $arResult = array();
        while (!$rsRecordSet->eof()) {
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['codigo'] = $rsRecordSet->getCampo('codigo');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['nome'] = $rsRecordSet->getCampo('nom_municipio');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['ano'] = Sessao::getExercicio();
            // $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['semestre'] = $rsRecordSet->getCampo('semestre');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['semestre'] = $this->inSemestre;

            ################INFORMAÇÕES DO IMOVEL######################
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['matricula'] = $rsRecordSet->getCampo('matricula_imovel');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['zona'] = $rsRecordSet->getCampo('zona');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['nro_guia_itbi'] = $rsRecordSet->getCampo('nro_guia_itbi');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['utilizacao'] = $rsRecordSet->getCampo('utilizacao');
            ###########################################################

            ################INFORMAÇÕES DO TRANSMITENTES###############
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'] = array();
            }
            $arTransmitente = array();
            $arTransmitente['nome'] = $rsRecordSet->getCampo('nome_transmitente');
            $arTransmitente['cpf_cnpj'] = $rsRecordSet->getCampo('cpf_cnpj_transmitente');

            if (!in_array($arTransmitente, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'][] = $arTransmitente;
            }
            ###########################################################

            ################INFORMAÇÕES DO ADQUIRENTES#################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'] = array();
            }
            $arAdquirente = array();
            $arAdquirente['nome'] = $rsRecordSet->getCampo('nome_adquirente');
            $arAdquirente['cpf_cnpj'] = $rsRecordSet->getCampo('cpf_cnpj_adquirente');

            if (!in_array($arAdquirente, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'][] = $arAdquirente;
            }
            ###########################################################

            ################INFORMAÇÕES DO LOGRADOURO##################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['logradouros'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['logradouros'] = array();
            }
            $arLogradouro = array();
            $arLogradouro['tipo'] = $rsRecordSet->getCampo('nom_tipo');
            $arLogradouro['nome'] = $rsRecordSet->getCampo('nom_logradouro');
            $arLogradouro['nro'] = $rsRecordSet->getCampo('numero');
            $arLogradouro['compl'] = $rsRecordSet->getCampo('complemento');
            $arLogradouro['lote'] = $rsRecordSet->getCampo('lote');
            $arLogradouro['bairro'] = $rsRecordSet->getCampo('bairro');
            $arLogradouro['vila'] = $rsRecordSet->getCampo('vila');
            $arLogradouro['quadra'] = $rsRecordSet->getCampo('quadra');
            $arLogradouro['setor'] = $rsRecordSet->getCampo('setor');

            if (!in_array($arLogradouro, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['logradouros'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['logradouros'][] = $arLogradouro;
            }
            ###########################################################

            ################INFORMAÇÕES DO TERRENO#####################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terrenos'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terrenos'] = array();
            }
            $arTerreno = array();
            $arTerreno['area_total_m2'] = number_format($rsRecordSet->getCampo('area_total_terreno'), 2, ',', '');
            $arTerreno['area_transmitida_m2'] = number_format($rsRecordSet->getCampo('area_transmitida_terreno'), 2, ',', '');
            $arTerreno['testada'] = number_format($rsRecordSet->getCampo('testada'), 2, ',', '');
            $arTerreno['codigo_situacao_quadra'] = $rsRecordSet->getCampo('codigo_situacao_quadra');
            $arTerreno['valor_declarado'] = number_format($rsRecordSet->getCampo('valor_declarado_terreno'), 2, ',', '');
            $arTerreno['valor_avaliado'] = number_format($rsRecordSet->getCampo('valor_avaliado_terreno'), 2, ',', '');
            $arTerreno['data_avaliacao'] = $rsRecordSet->getCampo('data_avaliacao_terreno');

            if (!in_array($arTerreno, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terrenos'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terrenos'][] = $arTerreno;
            }
            ###########################################################

            ################INFORMAÇÕES DO BENFEITORIAS################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'] = array();
            }
            $arBenfeitorias = array();
            $arBenfeitorias['codigo_especie_urbana'] = $rsRecordSet->getCampo('codigo_especie_urbana');
            $arBenfeitorias['area_total_m2'] = number_format($rsRecordSet->getCampo('area_total_edificacao'), 2, ',', '');
            $arBenfeitorias['area_transmitida_m2'] = number_format($rsRecordSet->getCampo('area_transmitida_edificacao'), 2, ',', '');
            $arBenfeitorias['area_privativa'] = number_format($rsRecordSet->getCampo('area_privativa_edificacao'), 2, ',', '');
            $arBenfeitorias['codigo_tipo_material'] = $rsRecordSet->getCampo('codigo_tipo_material');
            $arBenfeitorias['codigo_padrao_construtivo'] = $rsRecordSet->getCampo('codigo_padrao_construtivo');
            $arBenfeitorias['ano_construcao'] = $rsRecordSet->getCampo('ano_construcao');
            $arBenfeitorias['valor_declarado'] = number_format($rsRecordSet->getCampo('valor_declarado_edificacao'), 2, ',', '');
            $arBenfeitorias['valor_avaliado'] = number_format($rsRecordSet->getCampo('valor_avaliado_edificacao'), 2, ',', '');
            $arBenfeitorias['data_avaliacao'] = $rsRecordSet->getCampo('data_avaliacao_edificacao');
            $arBenfeitorias['tipo_utilizacao'] = $rsRecordSet->getCampo('tipo_utilizacao');

            if (!in_array($arBenfeitorias, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'][] = $arBenfeitorias;
            }
            ###########################################################

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDocumentoXMLITBIRural()
    {
        $arResult = $this->geraDadosXMLITBIRural();

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "ITBI-RURAL");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            $this->obXML->startElement("MUNICIPIO");
            $this->obXML->writeAttribute("codigo", $arResult[key($arResult)]['codigo']);
            $this->obXML->writeAttribute("nome", $arResult[key($arResult)]['nome']);
            $this->obXML->writeAttribute("ano", $arResult[key($arResult)]['ano']);
            $this->obXML->writeAttribute("semestre", $arResult[key($arResult)]['semestre']);
            $this->obXML->endElement();

            foreach ($arResult as $result) {
                $this->obXML->startElement("imovel");
                $this->obXML->writeAttribute("matricula", $result['matricula']);
                $this->obXML->writeAttribute("zona", utf8_encode($result['zona']));
                $this->obXML->writeAttribute("nro_guia_ITBI", $result['nro_guia_itbi']);
                $this->obXML->writeAttribute("utilizacao", utf8_encode($result['utilizacao']));

                //transmitentes
                $this->obXML->startElement("TRANSMITENTES");
                foreach ($result['transmitentes'] as $transmitentes) {
                    $this->obXML->startElement("transmitente");
                    $this->obXML->writeAttribute("nome", utf8_encode($transmitentes['nome']));
                    $this->obXML->writeAttribute("cpf_cnpj", $transmitentes['cpf_cnpj']);
                    $this->obXML->endElement();
                }
                // finaliza elemento TRANSMITENTES
                $this->obXML->endElement();

                //adquirentes
                $this->obXML->startElement("ADQUIRENTES");
                foreach ($result['adquirentes'] as $adquirentes) {
                    $this->obXML->startElement("adquirente");
                    $this->obXML->writeAttribute("nome", utf8_encode($adquirentes['nome']));
                    $this->obXML->writeAttribute("cpf_cnpj", $adquirentes['cpf_cnpj']);
                    $this->obXML->endElement();
                }
                // finaliza elemento ADQUIRENTES
                $this->obXML->endElement();

                foreach ($result['enderecos'] as $endereco) {
                    $this->obXML->startElement("endereco");
                    $this->obXML->writeAttribute("localidade", utf8_encode($endereco['localidade']));
                    $this->obXML->writeAttribute("distrito", utf8_encode($endereco['distrito']));
                    $this->obXML->writeAttribute("lote", utf8_encode($endereco['lote']));
                    $this->obXML->writeAttribute("compl", utf8_encode($endereco['complemento']));
                    $this->obXML->writeAttribute("confrontacoes", utf8_encode($endereco['confrontacoes']));
                    $this->obXML->endElement();
                }

                foreach ($result['terras'] as $terra) {
                    $this->obXML->startElement("terra");
                    $this->obXML->writeAttribute("area_total_ha", $terra['area_total_ha']);
                    $this->obXML->writeAttribute("area_transmitida_ha", $terra['area_transmitida_ha']);
                    $this->obXML->writeAttribute("codigo_situacao_terra", $terra['codigo_situacao_terra']);
                    $this->obXML->writeAttribute("valor_declarado", $terra['valor_declarado']);
                    $this->obXML->writeAttribute("valor_avaliado", $terra['valor_avaliado']);
                    $this->obXML->writeAttribute("data_avaliacao", $terra['data_avaliacao']);
                    $this->obXML->writeAttribute("Tipo_utilizacao", utf8_encode($terra['tipo_utilizacao']));
                    $this->obXML->endElement();
                }

                $this->obXML->startElement("BENFEITORIAS");
                foreach ($result['benfeitorias'] as $benfeitoria) {
                    $this->obXML->startElement("edificacao");
                    $this->obXML->writeAttribute("codigo_especie_rural", $benfeitoria['codigo_especie_rural']);
                    $this->obXML->writeAttribute("area_total_m2", $benfeitoria['area_total_m2']);
                    $this->obXML->writeAttribute("area_transmitida_m2", $benfeitoria['area_transmitida_m2']);
                    $this->obXML->writeAttribute("area_privativa", $benfeitoria['area_privativa']);
                    $this->obXML->writeAttribute("codigo_tipo_material", $benfeitoria['codigo_tipo_material']);
                    $this->obXML->writeAttribute("codigo_padrao_construtivo", $benfeitoria['codigo_padrao_construtivo']);
                    $this->obXML->writeAttribute("ano_construcao", $benfeitoria['ano_construcao']);
                    $this->obXML->writeAttribute("valor_declarado", $benfeitoria['valor_declarado']);
                    $this->obXML->writeAttribute("valor_avaliado", $benfeitoria['valor_avaliado']);
                    $this->obXML->writeAttribute("data_avaliacao", $benfeitoria['data_avaliacao']);
                    $this->obXML->endElement();
                }
                //finaliza elemento BENFEITORIAS
                $this->obXML->endElement();

                // finaliza elemento imovel
                $this->obXML->endElement();
            }
        }

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    private function geraDadosXMLITBIRural()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        $this->obTExportacaoGT->listarExportacaoITBIRural($rsRecordSet, $stFiltro);

        $this->setNomeDocumento("ITBIR_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $arResult = array();
        while (!$rsRecordSet->eof()) {
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['codigo'] = $rsRecordSet->getCampo('codigo');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['nome'] = $rsRecordSet->getCampo('nom_municipio');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['ano'] = Sessao::getExercicio();
            // $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['semestre'] = $rsRecordSet->getCampo('semestre');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['semestre'] = $this->inSemestre;

            ################INFORMAÇÕES DO IMOVEL######################
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['matricula'] = $rsRecordSet->getCampo('matricula_imovel');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['zona'] = $rsRecordSet->getCampo('zona');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['nro_guia_itbi'] = $rsRecordSet->getCampo('nro_guia_itbi');
            $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['utilizacao'] = $rsRecordSet->getCampo('utilizacao');
            ###########################################################

            ################INFORMAÇÕES DO TRANSMITENTES###############
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'] = array();
            }
            $arTransmitente = array();
            $arTransmitente['nome'] = $rsRecordSet->getCampo('nome_transmitente');
            $arTransmitente['cpf_cnpj'] = $rsRecordSet->getCampo('cpf_cnpj_transmitente');

            if (!in_array($arTransmitente, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['transmitentes'][] = $arTransmitente;
            }
            ###########################################################

            ################INFORMAÇÕES DO ADQUIRENTES#################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'] = array();
            }
            $arAdquirente = array();
            $arAdquirente['nome'] = $rsRecordSet->getCampo('nome_adquirente');
            $arAdquirente['cpf_cnpj'] = $rsRecordSet->getCampo('cpf_cnpj_adquirente');

            if (!in_array($arAdquirente, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['adquirentes'][] = $arAdquirente;
            }
            ###########################################################

            ################INFORMAÇÕES DO ENDERECO####################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['enderecos'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['enderecos'] = array();
            }
            $arEndereco = array();
            $arEndereco['localidade'] = $rsRecordSet->getCampo('localidade');
            $arEndereco['distrito'] = $rsRecordSet->getCampo('distrito');
            $arEndereco['lote'] = $rsRecordSet->getCampo('lote');
            $arEndereco['complemento'] = $rsRecordSet->getCampo('complemento');
            $arEndereco['confrontacoes'] = $rsRecordSet->getCampo('confrontacoes');

            if (!in_array($arEndereco, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['enderecos'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['enderecos'][] = $arEndereco;
            }
            ###########################################################

            ################INFORMAÇÕES DO TERRA#######################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terras'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terras'] = array();
            }
            $arTerra = array();
            $arTerra['area_total_ha'] = number_format($rsRecordSet->getCampo('area_total_terreno'), 2, ',', '');
            $arTerra['area_transmitida_ha'] = number_format($rsRecordSet->getCampo('area_transmitida_terreno'), 2, ',', '');
            $arTerra['codigo_situacao_terra'] = $rsRecordSet->getCampo('codigo_situacao_terra');
            $arTerra['valor_declarado'] = number_format($rsRecordSet->getCampo('valor_declarado_terreno'), 2, ',', '');
            $arTerra['valor_avaliado'] = number_format($rsRecordSet->getCampo('valor_avaliado_terreno'), 2, ',', '');
            $arTerra['data_avaliacao'] = $rsRecordSet->getCampo('data_avaliacao_terreno');
            $arTerra['tipo_utilizacao'] = $rsRecordSet->getCampo('tipo_utilizacao');

            if (!in_array($arTerra, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terras'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['terras'][] = $arTerra;
            }
            ###########################################################

            ################INFORMAÇÕES DO BENFEITORIAS################
            if (!isset($arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'] = array();
            }
            $arBenfeitorias = array();
            $arBenfeitorias['codigo_especie_rural'] = $rsRecordSet->getCampo('codigo_especie_rural');
            $arBenfeitorias['area_total_m2'] = number_format($rsRecordSet->getCampo('area_total_edificacao'), 2, ',', '');
            $arBenfeitorias['area_transmitida_m2'] = number_format($rsRecordSet->getCampo('area_transmitida_edificacao'), 2, ',', '');
            $arBenfeitorias['area_privativa'] = number_format($rsRecordSet->getCampo('area_privativa_edificacao'), 2, ',', '');
            $arBenfeitorias['codigo_tipo_material'] = $rsRecordSet->getCampo('codigo_tipo_material');
            $arBenfeitorias['codigo_padrao_construtivo'] = $rsRecordSet->getCampo('codigo_padrao_construtivo');
            $arBenfeitorias['ano_construcao'] = $rsRecordSet->getCampo('ano_construcao');
            $arBenfeitorias['valor_declarado'] = number_format($rsRecordSet->getCampo('valor_declarado_edificacao'), 2, ',', '');
            $arBenfeitorias['valor_avaliado'] = number_format($rsRecordSet->getCampo('valor_avaliado_edificacao'), 2, ',', '');
            $arBenfeitorias['data_avaliacao'] = $rsRecordSet->getCampo('data_avaliacao_edificacao');

            if (!in_array($arBenfeitorias, $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'])) {
                $arResult[$rsRecordSet->getCampo('nro_guia_itbi')]['benfeitorias'][] = $arBenfeitorias;
            }
            ###########################################################

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDocumentoXMLPVUrbano()
    {
        $arResult = $this->geraDadosXMLPVUrbano();

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "ITBI-PVU");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            $this->obXML->startElement("MUNICIPIO");
            $this->obXML->writeAttribute("codigo", $arResult[key($arResult)]['codigo']);
            $this->obXML->writeAttribute("nome", $arResult[key($arResult)]['nome']);
            $this->obXML->writeAttribute("ano", $arResult[key($arResult)]['ano']);
            $this->obXML->writeAttribute("semestre", $arResult[key($arResult)]['semestre']);
            $this->obXML->endElement();

            foreach ($arResult as $result) {
                $this->obXML->startElement("local");
                $this->obXML->writeAttribute("tipo_logradouro", utf8_encode($result['tipo_logradouro']));
                $this->obXML->writeAttribute("logradouro", utf8_encode($result['logradouro']));
                $this->obXML->writeAttribute("nro_inicial", $result['nro_inicial']);
                $this->obXML->writeAttribute("nro_final", $result['nro_final']);
                $this->obXML->writeAttribute("vila", utf8_encode($result['vila']));
                $this->obXML->writeAttribute("quadra", utf8_encode($result['quadra']));
                $this->obXML->writeAttribute("bairro", utf8_encode($result['bairro']));
                $this->obXML->writeAttribute("valor_m2", number_format($result['valor_m2'], 2, ',', ''));

                // finaliza elemento local
                $this->obXML->endElement();
            }
        }

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    private function geraDadosXMLPVUrbano()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        $this->obTExportacaoGT->listarExportacaoPVUrbano($rsRecordSet, $stFiltro);

        $this->setNomeDocumento("ITBIPVU_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $arResult = array();
        while (!$rsRecordSet->eof()) {
            $arTMP = array();

            $arTMP['codigo'] = $rsRecordSet->getCampo('codigo');
            $arTMP['nome'] = $rsRecordSet->getCampo('nom_municipio');
            $arTMP['ano'] = Sessao::getExercicio();
            // $arTMP[]['semestre'] = $rsRecordSet->getCampo('semestre');
            $arTMP['semestre'] = $this->inSemestre;

            ################INFORMAÇÕES################################
            $arTMP['tipo_logradouro'] = $rsRecordSet->getCampo('tipo_logradouro');
            $arTMP['logradouro'] = $rsRecordSet->getCampo('logradouro');
            $arTMP['nro_inicial'] = $rsRecordSet->getCampo('nro_inicial');
            $arTMP['nro_final'] = $rsRecordSet->getCampo('nro_final');
            $arTMP['vila'] = $rsRecordSet->getCampo('vila');
            $arTMP['quadra'] = $rsRecordSet->getCampo('quadra');
            $arTMP['bairro'] = $rsRecordSet->getCampo('bairro');
            $arTMP['valor_m2'] = $rsRecordSet->getCampo('valor_m2');
            ###########################################################

            $arResult[] = $arTMP;

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDocumentoXMLPVRural()
    {
        $arResult = $this->geraDadosXMLPVRural();

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "ITBI-PVR");
        $this->obXML->writeAttribute("versao", "1.0");

        if (count($arResult)) {
            $this->obXML->startElement("MUNICIPIO");
            $this->obXML->writeAttribute("codigo", $arResult[key($arResult)]['codigo']);
            $this->obXML->writeAttribute("nome", $arResult[key($arResult)]['nome']);
            $this->obXML->writeAttribute("ano", $arResult[key($arResult)]['ano']);
            $this->obXML->writeAttribute("semestre", $arResult[key($arResult)]['semestre']);
            $this->obXML->endElement();

            foreach ($arResult as $result) {
                $this->obXML->startElement("local");
                $this->obXML->writeAttribute("distrito", utf8_encode($result['distrito']));
                $this->obXML->writeAttribute("localidade", utf8_encode($result['localidade']));
                $this->obXML->writeAttribute("valor_minimo_ha", number_format($result['valor_minimo_ha'], 2, ',', ''));
                $this->obXML->writeAttribute("valor_maximo_ha", number_format($result['valor_maximo_ha'], 2, ',', ''));
                $this->obXML->writeAttribute("Tipo_utilizacao", utf8_encode($result['tipo_utilizacao']));
                $this->obXML->writeAttribute("topografia", utf8_encode($result['topografia']));

                // finaliza elemento local
                $this->obXML->endElement();
            }
        }

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    private function geraDadosXMLPVRural()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        $this->obTExportacaoGT->listarExportacaoPVRural($rsRecordSet, $stFiltro);

        $this->setNomeDocumento("ITBIPVR_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $arResult = array();
        while (!$rsRecordSet->eof()) {
            $arTMP = array();

            $arTMP['codigo'] = $rsRecordSet->getCampo('codigo');
            $arTMP['nome'] = $rsRecordSet->getCampo('nom_municipio');
            $arTMP['ano'] = Sessao::getExercicio();
            // $arTMP[]['semestre'] = $rsRecordSet->getCampo('semestre');
            $arTMP['semestre'] = $this->inSemestre;

            ################INFORMAÇÕES################################
            $arTMP['distrito'] = $rsRecordSet->getCampo('distrito');
            $arTMP['localidade'] = $rsRecordSet->getCampo('localidade');
            $arTMP['valor_minimo_ha'] = $rsRecordSet->getCampo('valor_minimo_ha');
            $arTMP['valor_maximo_ha'] = $rsRecordSet->getCampo('valor_maximo_ha');
            $arTMP['tipo_utilizacao'] = $rsRecordSet->getCampo('tipo_utilizacao');
            $arTMP['topografia'] = $rsRecordSet->getCampo('topografia');
            ###########################################################

            $arResult[] = $arTMP;

            $rsRecordSet->proximo();
        }

        return $arResult;
    }

    public function geraDocumentoXMLCadastroLograodouros()
    {
        $stFiltro = " AND sw_municipio.cod_municipio = ".SistemaLegado::pegaConfiguracao('cod_municipio');
        //busca os logradouros
        $this->obTExportacaoGT->listarExportacaoCadastroLogradourosLogradouro($rsLogradouros, $stFiltro);
        //busca os bairros
        $this->obTExportacaoGT->listarExportacaoCadastroLogradourosBairros($rsBairros, $stFiltro);

        $this->setNomeDocumento("LOGR_".SistemaLegado::pegaConfiguracao('cod_municipio')."_".$this->inSemestre."_".Sessao::getExercicio());

        $this->iniciaDocumento();

        $this->obXML->startElement("INFORMACAO");
        $this->obXML->writeAttribute("tipo", "LOGRADOUROS");
        $this->obXML->writeAttribute("versao", "1.0");

        $this->obXML->startElement("MUNICIPIO");
        $this->obXML->writeAttribute("codigo", $rsLogradouros->getCampo('codigo'));
        $this->obXML->writeAttribute("nome", $rsLogradouros->getCampo('municipio'));
        $this->obXML->writeAttribute("ano", Sessao::getExercicio());
        $this->obXML->writeAttribute("semestre", $this->inSemestre);
        $this->obXML->endElement();

        ##########################LOGRADOUROS#############################
        $this->obXML->startElement("LOGRADOUROS");
        while (!$rsLogradouros->eof()) {
            $this->obXML->startElement("logradouro");
            $this->obXML->writeAttribute("tipo", utf8_encode($rsLogradouros->getCampo('tipo')));
            $this->obXML->writeAttribute("nome", utf8_encode($rsLogradouros->getCampo('nome')));

            // finaliza elemento logradouro
            $this->obXML->endElement();

            $rsLogradouros->proximo();
        }
        // finaliza elemento LOGRADOUROS
        $this->obXML->endElement();
        ##################################################################

        ##########################BAIRROS#################################
        $this->obXML->startElement("BAIRROS");
        while (!$rsBairros->eof()) {
            $this->obXML->startElement("bairro");
            $this->obXML->writeAttribute("nome", utf8_encode($rsBairros->getCampo('nome')));

            // finaliza elemento bairro
            $this->obXML->endElement();

            $rsBairros->proximo();
        }
        // finaliza elemento BAIRROS
        $this->obXML->endElement();
        ##################################################################

        ##########################VILAS###################################
        //o sistema não possui um cadastro de vilas
        $this->obXML->startElement("VILAS");
        $this->obXML->startElement("vila");
        $this->obXML->writeAttribute("nome", "");

        // finaliza elemento vila
        $this->obXML->endElement();
        // finaliza elemento VILAS
        $this->obXML->endElement();
        ##################################################################

        // finaliza elemento INFORMACAO
        $this->obXML->endDocument();

        $this->finalizaDocumento();

        //define a lista de arquivos para download
        $arArquivos = Sessao::read('arArquivosDownload');
        $arArquivos[] = array('stLink' => CAM_FRAMEWORK."tmp/".$this->nomeDocumento, 'stNomeArquivo' => $this->nomeDocumento);
        Sessao::write('arArquivosDownload', $arArquivos);
    }

    public function doZipArquivos()
    {
        $arArquivosDownload = Sessao::read('arArquivosDownload');
        $stLabelZip = 'ArquivosExportacao.zip';
        $stCaminho = CAM_FRAMEWORK.'tmp/';

        foreach ($arArquivosDownload as $arquivo) {
            $this->obArquivoZip->AdicionarArquivo($arquivo['stLink'],$arquivo['stNomeArquivo']);
        }

        $stNomeZip = $this->obArquivoZip->Show();
        $arArquivosDownload = array();
        $arArquivosDownload[0]['stNomeArquivo'] = $stLabelZip;
        $arArquivosDownload[0]['stLink'       ] = $stCaminho.$stNomeZip;
        // MAnda array de arquivos para a sessao
        Sessao::write('arArquivosDownload',$arArquivosDownload);
    }
}

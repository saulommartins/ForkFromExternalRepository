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
  * Função para montar cada linha do arquivo de carne para GRÁFICA

  * Data de Criação: 08/11/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho
  * @package URBEM
  * @subpackage Mapeamento

  * $Id: FARRMontaCarneGrafica.class.php 63867 2015-10-27 17:25:14Z evandro $

  * Casos de uso: uc-05.03.11
  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Data de Criação: 08/11/2006

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class FARRMontaCarneGrafica extends Persistente
{
    public $stTipoEmissao;

    /**
        * Método Construtor
        * @access Private
    */
    public function FARRMontaCarneGrafica()
    {
        parent::Persistente();
        //$this->setTabela('CalculoTributario');
        $this->stTipoEmissao            = null;
    }

    public function montaLinhaCarneGrafica($stTipoCodigoBarra, $rsCabecalho, $rsParcelas)
    {
        /* CONFIGURACAO DA LINHA DO CABECALHO */
        $arConfCabecalho = array (
            "0"     => "sigla_uf-2",
            "1"     => "nom_municipio-35",
            "2"     => "tipo_logradouro-15",
            "3"     => "logradouro-60",
            "4"     => "numero-6",
            "5"     => "complemento-160",
            "6"     => "bairro-30",
            "7"     => "cep-10",
            "8"     => "fone-10",
            "9"     => "email-160",
            "10"    => "cnpj-20"
        );

        $arConfCabecalhoCompensacao = array (
            "0"     => "local_pagamento-80",
            "1"     => "carteira-5",
            "2"     => "especie_doc-20",
            "3"     => "aceite-1",
            "4"     => "especie-20",
            "5"     => "quantidade-20",
            "6"     => "agencia-10",
            "7"     => "codigo_cedente-20"
        );

        /* CONFIGURACAO DA LINHA DO CARNE CARNE */
        if ($this->stTipoEmissao == "II" || !$this->stTipoEmissao) {

            $arConfLinha = array (
                "1" 	=> "numcgm-7",
                "2" 	=> "nom_cgm-200",
                
                // ENDEREÇO DE CORRESPONDENCIA
                "3" 	=> "c_nom_tipo_logradouro-15",
                "4" 	=> "c_cod_logradouro-7",
                "5" 	=> "c_nom_logradouro-60",
                "6" 	=> "c_numero-6",
                "7" 	=> "c_nom_bairro-30",
                "8" 	=> "c_nom_municipio-35",
                "9"	    => "c_sigla_uf-2",
                "10" 	=> "c_cep-8",
                "11" 	=> "c_complemento-160",
                "12" 	=> "c_caixa_postal-160",
                
                // ENDEREÇO DO IMOVEL
                "13" 	=> "nom_tipo_logradouro-15",
                "14" 	=> "cod_logradouro-7",
                "15" 	=> "nom_logradouro-60",
                "16" 	=> "numero-6",
                "17" 	=> "nom_bairro-30",
                "18" 	=> "nom_municipio-35",
                "19"	=> "sigla_uf-2",
                "20" 	=> "cep-8",
                "21" 	=> "complemento-160",
                "22"    => "nome_condominio-160",
                
                //DADOS DO IMOVEL
                "23" 	=> "inscricao-7",
                "24" 	=> "area_lote-17",
                "25" 	=> "area_construida-17",
                "26" 	=> "codigo_composto-100",
                "27" 	=> "nom_localizacao-80",
                
                //DIVIDA
                "28" 	=> "cod_grupo-7",
                "29" 	=> "nom_grupo-80",
                "30"    => "exercicio-4",
                
                //VALORES DOS CREDITOS
                "31" 	=> "cod_credito_1-7",
                "32" 	=> "descricao_1-80",
                "33" 	=> "valor_1-17",
                "34" 	=> "cod_credito_2-7",
                "35" 	=> "descricao_2-80",
                "36" 	=> "valor_2-17",
                "37" 	=> "cod_credito_3-7",
                "38" 	=> "descricao_3-80",
                "39" 	=> "valor_3-17",
                "40" 	=> "cod_credito_4-7",
                "41" 	=> "descricao_4-80",
                "42" 	=> "valor_4-17",
                "43" 	=> "cod_credito_5-7",
                "44" 	=> "descricao_5-80",
                "45" 	=> "valor_5-17",
                "46" 	=> "cod_credito_6-7",
                "47" 	=> "descricao_6-80",
                "48" 	=> "valor_6-17",
                "49" 	=> "cod_credito_7-7",
                "50" 	=> "descricao_7-80",
                "51" 	=> "valor_7-17",
                "52" 	=> "soma_creditos-17",
                
                //PARCELAS UNICAS
                "53" 	=> "valor_unica_1-17",
                "54" 	=> "vencimento_unica_1-10",
                "55" 	=> "desconto_unica_1-17",
                "56" 	=> "nosso_numero_unica_1-17",
                "57" 	=> "codigo_barra_unica_1-60",
                "58" 	=> "linha_digitavel_unica_1-120",
                "59" 	=> "valor_unica_2-17",
                "60" 	=> "vencimento_unica_2-10",
                "61" 	=> "desconto_unica_2-17",
                "62" 	=> "nosso_numero_unica_2-17",
                "63" 	=> "codigo_barra_unica_2-60",
                "64" 	=> "linha_digitavel_unica_2-120",
                "65" 	=> "valor_unica_3-17",
                "66" 	=> "vencimento_unica_3-10",
                "67" 	=> "desconto_unica_3-17",
                "68" 	=> "nosso_numero_unica_3-17",
                "69" 	=> "codigo_barra_unica_3-60",
                "70" 	=> "linha_digitavel_unica_3-120",
                "71" 	=> "valor_unica_4-17",
                "72" 	=> "vencimento_unica_4-10",
                "73" 	=> "desconto_unica_4-17",
                "74" 	=> "nosso_numero_unica_4-17",
                "75" 	=> "codigo_barra_unica_4-60",
                "76" 	=> "linha_digitavel_unica_4-120",
                "77" 	=> "valor_unica_5-17",
                "78" 	=> "vencimento_unica_5-10",
                "79" 	=> "desconto_unica_5-17",
                "80" 	=> "nosso_numero_unica_5-17",
                "81" 	=> "codigo_barra_unica_5-60",
                "82" 	=> "linha_digitavel_unica_5-120",
                
                //PARCELAS NORMAIS
                "83" 	=> "valor_normal_1-17",
                "84" 	=> "vencimento_normal_1-10",
                "85" 	=> "nosso_numero_normal_1-17",
                "86" 	=> "codigo_barra_normal_1-60",
                "87" 	=> "linha_digitavel_normal_1-120",

                "88" 	=> "valor_normal_2-17",
                "89" 	=> "vencimento_normal_2-10",
                "90" 	=> "nosso_numero_normal_2-17",
                "91" 	=> "codigo_barra_normal_2-60",
                "92" 	=> "linha_digitavel_normal_2-120",

                "93" 	=> "valor_normal_3-17",
                "94" 	=> "vencimento_normal_3-10",
                "95" 	=> "nosso_numero_normal_3-17",
                "96" 	=> "codigo_barra_normal_3-60",
                "97" 	=> "linha_digitavel_normal_3-120",

                "98" 	=> "valor_normal_4-17",
                "99" 	=> "vencimento_normal_4-10",
                "100" 	=> "nosso_numero_normal_4-17",
                "101" 	=> "codigo_barra_normal_4-60",
                "102" 	=> "linha_digitavel_normal_4-120",

                "103" 	=> "valor_normal_5-17",
                "104" 	=> "vencimento_normal_5-10",
                "105" 	=> "nosso_numero_normal_5-120",
                "106" 	=> "codigo_barra_normal_5-60",
                "107" 	=> "linha_digitavel_normal_5-120",

                "108" 	=> "valor_normal_6-17",
                "109" 	=> "vencimento_normal_6-10",
                "110" 	=> "nosso_numero_normal_6-17",
                "111" 	=> "codigo_barra_normal_6-60",
                "112" 	=> "linha_digitavel_normal_6-120",

                "113" 	=> "valor_normal_7-17",
                "114" 	=> "vencimento_normal_7-10",
                "115" 	=> "nosso_numero_normal_7-17",
                "116" 	=> "codigo_barra_normal_7-60",
                "117" 	=> "linha_digitavel_normal_7-120",

                "118" 	=> "valor_normal_8-17",
                "119" 	=> "vencimento_normal_8-10",
                "120" 	=> "nosso_numero_normal_8-17",
                "121" 	=> "codigo_barra_normal_8-60",
                "122" 	=> "linha_digitavel_normal_8-120",

                "123" 	=> "valor_normal_9-17",
                "124" 	=> "vencimento_normal_9-10",
                "125" 	=> "nosso_numero_normal_9-17",
                "126" 	=> "codigo_barra_normal_9-60",
                "127" 	=> "linha_digitavel_normal_9-120",

                "128" 	=> "valor_normal_10-17",
                "129" 	=> "vencimento_normal_10-10",
                "130" 	=> "nosso_numero_normal_10-17",
                "131" 	=> "codigo_barra_normal_10-60",
                "132" 	=> "linha_digitavel_normal_10-120",

                "133" 	=> "valor_normal_11-17",
                "134" 	=> "vencimento_normal_11-10",
                "135" 	=> "nosso_numero_normal_11-17",
                "136" 	=> "codigo_barra_normal_11-60",
                "137" 	=> "linha_digitavel_normal_11-120",

                "138" 	=> "valor_normal_12-17",
                "139" 	=> "vencimento_normal_12-10",
                "140" 	=> "nosso_numero_normal_12-17",
                "141" 	=> "codigo_barra_normal_12-60",
                "142" 	=> "linha_digitavel_normal_12-120",
                
                # VALORES VENAIS
                "143"   => "valor_venal_territorial-17",
                "144"   => "valor_venal_predial-17",
                "145"   => "valor_venal_total-17",
                
                # VALORES VUP
                "146"   => "valor_m2_territorial-17",
                "147"   => "valor_m2_predial-17",
                
                # NOME LOCALIZACAO PRIMEIRO NIVEL
                "148"   => "localizacao_primeiro_nivel-80",
                
                # VALOR IMPOSTO
                "149"   => "valor_imposto-17",
                "150"   => "area_limpeza-17",
                "151"   => "aliquota_limpeza-8",
                "152"   => "aliquota_imposto-8",
                
                # ATRIBUTOS DINAMICOS (MAXIMO 15)
                "153" 	=> "atributo_1-50",
                "154" 	=> "atributo_2-50",
                "155" 	=> "atributo_3-50",
                "156" 	=> "atributo_4-50",
                "157" 	=> "atributo_5-50",
                "158" 	=> "atributo_6-50",
                "159" 	=> "atributo_7-50",
                "160" 	=> "atributo_8-50",
                "161" 	=> "atributo_9-50",
                "162" 	=> "atributo_10-50",
                "163" 	=> "atributo_11-50",
                "164" 	=> "atributo_12-50",
                "165" 	=> "atributo_13-50",
                "166" 	=> "atributo_14-50",
                "167" 	=> "atributo_15-50",
                "168"   => "valor_m2_predial_descoberto-17",
                "169"   => "valor_venal_predial_descoberto-17",
                "170"   => "area_construida_total-17",
                "171"   => "area_descoberta-17",
                "172"   => "valor_venal_predial_coberto-17"
            );
        } else {
            # LAYOUT PARA CARNE DA INSCR. ECONÔMICA
            $arConfLinha = array (

                # DETALHES DO CARNE
                "1"   => "numcgm-7",
                "2"   => "nom_cgm-200",

                # ENDEREÇO DA EMPRESA
                "3"   => "c_nom_tipo_logradouro-15",
                "4"   => "c_cod_logradouro-7",
                "5"   => "c_nom_logradouro-60",
                "6"   => "c_numero-6",
                "7"   => "c_nom_bairro-30",
                "8"   => "c_nom_municipio-35",
                "9"   => "c_sigla_uf-2",
                "10"  => "c_cep-8",
                "11"  => "c_complemento-160",
                "12"  => "c_caixa_postal-160",

                # DADOS DA EMPRESA
                "13"  => "inscricao_economica-8",
                "14"  => "data_abertura-10",
                "15"  => "numcgm_responsavel-7",
                "16"  => "nome_responsavel-200",
                "17"  => "cod_natureza-5",
                "18"  => "natureza_juridica-200",
                "19"  => "cod_categoria-2",
                "20"  => "categoria-40",
                "21"  => "cod_atividade_principal-15",
                "22"  => "descricao_atividade_principal-240",
                "23"  => "data_inicio-10",
                "24"  => "cnpj-20",
                "25"  => "nom_fantasia-150",
                "26"  => "inscricao_municipal_economica-15",

                # RELAÇÃO SÓCIOS
                "27"  => "numcgm_socio_1-7",
                "28"  => "nome_socio_1-200",
                "29"  => "quota_socio_1-6",

                "30"  => "numcgm_socio_2-7",
                "31"  => "nome_socio_2-200",
                "32"  => "quota_socio_2-6",

                "33"  => "numcgm_socio_3-7",
                "34"  => "nome_socio_3-200",
                "35"  => "quota_socio_3-6",

                "36"  => "numcgm_socio_4-7",
                "37"  => "nome_socio_4-200",
                "38"  => "quota_socio_4-6",

                "39"  => "numcgm_socio_5-7",
                "40"  => "nome_socio_5-200",
                "41"  => "quota_socio_5-6",

                # DÍVIDA
                "42"  => "cod_grupo-7",
                "43"  => "nom_grupo-80",
                "44"  => "exercicio-4",

                # VALORES DOS CRÉDITOS
                "45"  => "cod_credito_1-7",
                "46"  => "descricao_1-80",
                "47"  => "valor_1-17",

                "48"  => "cod_credito_2-7",
                "49"  => "descricao_2-80",
                "50"  => "valor_2-17",

                "51"  => "cod_credito_3-7",
                "52"  => "descricao_3-80",
                "53"  => "valor_3-17",

                "54"  => "cod_credito_4-7",
                "55"  => "descricao_4-80",
                "56"  => "valor_4-17",

                "57"  => "cod_credito_5-7",
                "58"  => "descricao_5-80",
                "59"  => "valor_5-17",

                "60"  => "cod_credito_6-7",
                "61"  => "descricao_6-80",
                "62"  => "valor_6-17",

                "63"  => "cod_credito_7-7",
                "64"  => "descricao_7-80",
                "65"  => "valor_7-17",

                "66"  => "soma_creditos-17",

                # PARCELAS ÚNICAS
                "67"  => "valor_unica_1-17",
                "68"  => "vencimento_unica_1-10",
                "69"  => "desconto_unica_1-17",
                "70"  => "nosso_numero_unica_1-17",
                "71"  => "codigo_barra_unica_1-60",
                "72"  => "linha_digitavel_unica_1-120",

                "73"  => "valor_unica_2-17",
                "74"  => "vencimento_unica_2-10",
                "75"  => "desconto_unica_2-17",
                "76"  => "nosso_numero_unica_2-17",
                "77"  => "codigo_barra_unica_2-60",
                "78"  => "linha_digitavel_unica_2-120",

                "79"  => "valor_unica_3-17",
                "80"  => "vencimento_unica_3-10",
                "81"  => "desconto_unica_3-17",
                "82"  => "nosso_numero_unica_3-17",
                "83"  => "codigo_barra_unica_3-60",
                "84"  => "linha_digitavel_unica_3-120",

                "85"  => "valor_unica_4-17",
                "86"  => "vencimento_unica_4-10",
                "87"  => "desconto_unica_4-17",
                "88"  => "nosso_numero_unica_4-17",
                "89"  => "codigo_barra_unica_4-60",
                "90"  => "linha_digitavel_unica_4-120",

                "91"  => "valor_unica_5-17",
                "92"  => "vencimento_unica_5-10",
                "93"  => "desconto_unica_5-17",
                "94"  => "nosso_numero_unica_5-17",
                "95"  => "codigo_barra_unica_5-60",
                "96"  => "linha_digitavel_unica_5-120",

                # PARCELAS NORMAIS
                "97"  => "valor_normal_1-17",
                "98"  => "vencimento_normal_1-10",
                "99"  => "nosso_numero_normal_1-17",
                "100"  => "codigo_barra_normal_1-60",
                "101"  => "linha_digitavel_normal_1-120",

                "102" => "valor_normal_2-17",
                "103" => "vencimento_normal_2-10",
                "104" => "nosso_numero_normal_2-17",
                "105" => "codigo_barra_normal_2-60",
                "106" => "linha_digitavel_normal_2-120",

                "107" => "valor_normal_3-17",
                "108" => "vencimento_normal_3-10",
                "109" => "nosso_numero_normal_3-17",
                "110" => "codigo_barra_normal_3-60",
                "111" => "linha_digitavel_normal_3-120",

                "112" => "valor_normal_4-17",
                "113" => "vencimento_normal_4-10",
                "114" => "nosso_numero_normal_4-17",
                "115" => "codigo_barra_normal_4-60",
                "116" => "linha_digitavel_normal_4-120",

                "117" => "valor_normal_5-17",
                "118" => "vencimento_normal_5-10",
                "119" => "nosso_numero_normal_5-120",
                "120" => "codigo_barra_normal_5-60",
                "121" => "linha_digitavel_normal_5-120",

                "122" => "valor_normal_6-17",
                "123" => "vencimento_normal_6-10",
                "124" => "nosso_numero_normal_6-17",
                "125" => "codigo_barra_normal_6-60",
                "126" => "linha_digitavel_normal_6-120",

                "127" => "valor_normal_7-17",
                "128" => "vencimento_normal_7-10",
                "129" => "nosso_numero_normal_7-17",
                "130" => "codigo_barra_normal_7-60",
                "131" => "linha_digitavel_normal_7-120",

                "132" => "valor_normal_8-17",
                "133" => "vencimento_normal_8-10",
                "134" => "nosso_numero_normal_8-17",
                "135" => "codigo_barra_normal_8-60",
                "136" => "linha_digitavel_normal_8-120",

                "137" => "valor_normal_9-17",
                "138" => "vencimento_normal_9-10",
                "139" => "nosso_numero_normal_9-17",
                "140" => "codigo_barra_normal_9-60",
                "141" => "linha_digitavel_normal_9-120",

                "142" => "valor_normal_10-17",
                "143" => "vencimento_normal_10-10",
                "144" => "nosso_numero_normal_10-17",
                "145" => "codigo_barra_normal_10-60",
                "146" => "linha_digitavel_normal_10-120",

                "147" => "valor_normal_11-17",
                "148" => "vencimento_normal_11-10",
                "149" => "nosso_numero_normal_11-17",
                "150" => "codigo_barra_normal_11-60",
                "151" => "linha_digitavel_normal_11-120",

                "152" => "valor_normal_12-17",
                "153" => "vencimento_normal_12-10",
                "154" => "nosso_numero_normal_12-17",
                "155" => "codigo_barra_normal_12-60",
                "156" => "linha_digitavel_normal_12-120",

                "157"  => "quadra-80",
                "158"  => "lote-80",
                "159"  => "nom_localizacao-80",
                "160"  => "localizacao_primeiro_nivel-80",

                # ATRIBUTOS DINÂMICOS (MÁXIMO 15)
                "161" => "atributo_1-50",
                "163" => "atributo_2-50",
                "163" => "atributo_3-50",
                "164" => "atributo_4-50",
                "165" => "atributo_5-50",
                "166" => "atributo_6-50",
                "167" => "atributo_7-50",
                "168" => "atributo_8-50",
                "169" => "atributo_9-50",
                "170" => "atributo_10-50",
                "171" => "atributo_11-50",
                "172" => "atributo_12-50",
                "173" => "atributo_13-50",
                "174" => "atributo_14-50",
                "175" => "atributo_15-50"
            );

        }

        $contConf = count ($arConfLinha);

        $arRetorno  = array();

        $arLinhaCabecalho = array();
        $arRetorno[] = $stTipoCodigoBarra;
        $arRetorno[] = $rsCabecalho->getCampo('prefeitura');
        $arRetorno[] = $rsCabecalho->getCampo('cod_febraban');

        $stRetorno = null;
        $cont = 0;
        
        # Cabeçalho do arquivo exportado.
        while ($cont < count($arConfCabecalho)) {
        $artmp = explode ('-',$arConfCabecalho[$cont]);
        
        $colunaAtualConf = $artmp[0];
        $tamColunaAtual  = $artmp[1];

        $valorRecordSet = $rsCabecalho->getCampo($colunaAtualConf);

        $contTam = 0;

        $stRetorno .= $valorRecordSet.'§';
        $cont++;
        }

        $arRetorno[] = $stRetorno;

        $stRetorno = null;
        $cont = 0;

        while ($cont < count($arConfCabecalhoCompensacao)) {

            $artmp = explode ('-',$arConfCabecalhoCompensacao[$cont]);
            $colunaAtualConf = $artmp[0];
            $tamColunaAtual  = $artmp[1];

            $valorRecordSet = $rsCabecalho->getCampo( $colunaAtualConf );

            # Separador por colunas, solicitado para exportar como .csv
            $stRetorno .= $valorRecordSet.'§';
            $cont++;
        }

        $arRetorno[] = date("d/m/Y").'§'.$stRetorno;
        $arRetorno[] = "$$";

        # Quando for Inscr. Econ. monta a linha com esse cabeçalho.
        if ($this->stTipoEmissao == "IE") {
            $arRetorno[] = "numcgm§nom_cgm§c_nom_tipo_logradouro§c_cod_logradouro§c_nom_logradouro§c_numero§c_nom_bairro§c_nom_municipio§c_sigla_uf§c_cep§c_complemento§c_caixa_postal§inscricao_economica§data_abertura§numcgm_responsavel§nome_responsavel§cod_natureza§natureza_juridica§cod_categoria§categoria§cod_atividade_principal§descricao_atividade_principal§data_inicio§cnpj§nome_fantasia§inscricao_imobiliaria§numcgm_socio_1§nome_socio_1§quota_socio_1§numcgm_socio_2§nome_socio_2§quota_socio_2§numcgm_socio_3§nome_socio_3§quota_socio_3§numcgm_4§nome_socio_4§quota_socio_4§numcgm_5§nome_socio_5§quota_socio_5§cod_grupo§nom_grupo§Exercício§cod_credito_1§descricao_1§valor_1§cod_credito_2§descricao_2§valor_2§cod_credito_3§descricao_3§valor_3§cod_credito_4§descricao_4§valor_4§cod_credito_5§descricao_5§valor_5§cod_credito_6§descricao_6§valor_6§cod_credito_7§descricao_7§valor_7§soma_creditos§valor_unica_1§vencimento_unica_1§desconto_unica_1§nosso_numero_unica_1§codigo_barra_unica_1§linha_digitavel_unica_1§valor_unica_2§vencimento_unica_2§desconto_unica_2§nosso_numero_unica_2§codigo_barra_unica_2§linha_digitavel_unica_2§valor_unica_3§vencimento_unica_3§desconto_unica_3§nosso_numero_unica_3§codigo_barra_unica_3§linha_digitavel_unica_3§valor_unica_4§vencimento_unica_4§desconto_unica_4§nosso_numero_unica_4§codigo_barra_unica_4§linha_digitavel_unica_4§valor_unica_5§vencimento_unica_5§desconto_unica_5§nosso_numero_unica_5§codigo_barra_unica_5§linha_digitavel_unica_5§valor_normal_1§vencimento_normal_1§nosso_numero_normal_1§codigo_barra_normal_1§linha_digitavel_normal_1§valor_normal_2§vencimento_normal_2§nosso_numero_normal_2§codigo_barra_normal_2§linha_digitavel_normal_2§valor_normal_3§vencimento_normal_3§nosso_numero_normal_3§codigo_barra_normal_3§linha_digitavel_normal_3§valor_normal_4§vencimento_normal_4§nosso_numero_normal_4§codigo_barra_normal_4§linha_digitavel_normal_4§valor_normal_5§vencimento_normal_5§nosso_numero_normal_5§codigo_barra_normal_5§linha_digitavel_normal_5§valor_normal_6§vencimento_normal_6§nosso_numero_normal_6§codigo_barra_normal_6§linha_digitavel_normal_6§valor_normal_7§vencimento_normal_7§nosso_numero_normal_7§codigo_barra_normal_7§linha_digitavel_normal_7§valor_normal_8§vencimento_normal_8§nosso_numero_normal_8§codigo_barra_normal_8§linha_digitavel_normal_8§valor_normal_9§vencimento_normal_9§nosso_numero_normal_9§codigo_barra_normal_9§linha_digitavel_normal_9§valor_normal_10§vencimento_normal_10§nosso_numero_normal_10§codigo_barra_normal_10§linha_digitavel_normal_10§valor_normal_11§vencimento_normal_11§nosso_numero_normal_11§codigo_barra_normal_11§linha_digitavel_normal_11§valor_normal_12§vencimento_normal_12§nosso_numero_normal_12§codigo_barra_normal_12§linha_digitavel_normal_12§quadra§lote§distrito§regiao§atributo_1§atributo_2§atributo_3§atributo_4§atributo_5§atributo_6§atributo_7§atributo_8§atributo_9§atributo_10§atributo_11§atributo_12§atributo_13§atributo_14§atributo_15";
        } elseif ($this->stTipoEmissao == "II") {
            $arRetorno[] = "numcgm§nom_cgm§c_nom_tipo_logradouro§c_cod_logradouro§c_nom_logradouro§c_numero§c_nom_bairro§c_nom_municipio§c_sigla_uf§c_cep§c_complemento§c_caixa_postal§nom_tipo_logradouro§cod_logradouro§nom_logradouro§numero§bairro§nom_municipio§sigla_uf§cep§complemento§nome_condominio§inscricao_municipal§area_lote§area_construida§codigo_composto§nom_localizacao§cod_grupo§nom_grupo§Exercício§cod_credito_1§descricao_1§valor_1§cod_credito_2§descricao_2§valor_2§cod_credito_3§descricao_3§valor_3§cod_credito_4§descricao_4§valor_4§cod_credito_5§descricao_5§valor_5§cod_credito_6§descricao_6§valor_6§cod_credito_7§descricao_7§valor_7§soma_creditos§valor_unica_1§vencimento_unica_1§desconto_unica_1§nosso_numero_unica_1§codigo_barra_unica_1§linha_digitavel_unica_1§valor_unica_2§vencimento_unica_2§desconto_unica_2§nosso_numero_unica_2§codigo_barra_unica_2§linha_digitavel_unica_2§valor_unica_3§vencimento_unica_3§desconto_unica_3§nosso_numero_unica_3§codigo_barra_unica_3§linha_digitavel_unica_3§valor_unica_4§vencimento_unica_4§desconto_unica_4§nosso_numero_unica_4§codigo_barra_unica_4§linha_digitavel_unica_4§valor_unica_5§vencimento_unica_5§desconto_unica_5§nosso_numero_unica_5§codigo_barra_unica_5§linha_digitavel_unica_5§valor_normal_1§vencimento_normal_1§nosso_numero_normal_1§codigo_barra_normal_1§linha_digitavel_normal_1§valor_normal_2§vencimento_normal_2§nosso_numero_normal_2§codigo_barra_normal_2§linha_digitavel_normal_2§valor_normal_3§vencimento_normal_3§nosso_numero_normal_3§codigo_barra_normal_3§linha_digitavel_normal_3§valor_normal_4§vencimento_normal_4§nosso_numero_normal_4§codigo_barra_normal_4§linha_digitavel_normal_4§valor_normal_5§vencimento_normal_5§nosso_numero_normal_5§codigo_barra_normal_5§linha_digitavel_normal_5§valor_normal_6§vencimento_normal_6§nosso_numero_normal_6§codigo_barra_normal_6§linha_digitavel_normal_6§valor_normal_7§vencimento_normal_7§nosso_numero_normal_7§codigo_barra_normal_7§linha_digitavel_normal_7§valor_normal_8§vencimento_normal_8§nosso_numero_normal_8§codigo_barra_normal_8§linha_digitavel_normal_8§valor_normal_9§vencimento_normal_9§nosso_numero_normal_9§codigo_barra_normal_9§linha_digitavel_normal_9§valor_normal_10§vencimento_normal_10§nosso_numero_normal_10§codigo_barra_normal_10§linha_digitavel_normal_10§valor_normal_11§vencimento_normal_11§nosso_numero_normal_11§codigo_barra_normal_11§linha_digitavel_normal_11§valor_normal_12§vencimento_normal_12§nosso_numero_normal_12§codigo_barra_normal_12§linha_digitavel_normal_12§valor_venal_territorial§valor_venal_predial§valor_venal_total§valor_m2_territorial§valor_m2_predial§localizacao_primeiro_nivel§valor_imposto§area_limpeza§aliquota_limpeza§aliquota_imposto§atributo_1§atributo_2§atributo_3§atributo_4§atributo_5§atributo_6§atributo_7§atributo_8§atributo_9§atributo_10§atributo_11§atributo_12§atributo_13§atributo_14§atributo_15§valor_m2_predial_descoberto§valor_venal_predial_descoberto§area_construida_total§area_descoberta§valor_venal_predial_coberto";
        }

        # Percorre o RecordSet para preencher o arquivo com a consulta da PL.
        $rsParcelas->setPrimeiroElemento();
        while (!$rsParcelas->eof()) {

            $stRetorno = null;
            $cont = 1;
            while ($cont <= $contConf) {

                $artmp = explode ('-',$arConfLinha[$cont]);
                $colunaAtualConf = $artmp[0];
                $tamColunaAtual  = $artmp[1];

                if ( ( preg_match( '/codigo_barra_/i',$colunaAtualConf) ) || ( preg_match('/linha_digitavel_/i',$colunaAtualConf) ) ) {
                    $valorRecordSet = trim($rsParcelas->getCampo($colunaAtualConf));
                } else {
                    $valorRecordSet = $rsParcelas->getCampo($colunaAtualConf);
                }

                if (strlen($valorRecordSet) > $tamColunaAtual) {
                    $valorRecordSet  = mb_substr($valorRecordSet, 0, $tamColunaAtual,'UTF-8');
                }

                $valorRecordSet .= '§';

                # Separador por colunas, solicitado para exportar como .csv
                $stRetorno .= $valorRecordSet;

                $cont++;
            }

            $arRetorno[] = $stRetorno;
            $rsParcelas->proximo();
        }

        $rsRetorno = new RecordSet;
        $rsRetorno->preenche($arRetorno);

        return $rsRetorno;
    }
}

?>

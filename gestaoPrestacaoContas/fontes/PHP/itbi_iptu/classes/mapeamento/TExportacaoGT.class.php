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
    * Mapeamento da tabela stn.aporte_recurso_rpps
    * Data de Criação   : 10/06/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

include_once CLA_PERSISTENTE;

class TExportacaoGT extends Persistente
{
    /**
     * Método Construtor da classe TExportacaoGT
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     *
     * @return void
     */
    public function __construct()
    {
        parent::Persistente();
    }

    /**
     * Método que retorna os imóveis para informar no xml da exportação
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoIPTU(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY imovel.inscricao_municipal ",$boTransacao="")
    {
        $stSql = "
                SELECT
                      --informações do município
                       municipios_iptu.cod_sefaz as codigo
                     , sw_municipio.nom_municipio


                      --informações do imóvel
                     , matricula_imovel.mat_registro_imovel as matricula_imovel
                     , matricula_imovel.zona
                     , imovel.inscricao_municipal as nro_registro_iptu

                     --informações de proprietário
                     , sw_cgm_proprietario.nom_cgm as nome_proprietario
                     , CASE WHEN sw_cgm_pessoa_fisica_proprietario.cpf IS NULL THEN
                           sw_cgm_pessoa_juridica_proprietario.cnpj
                       ELSE
                           sw_cgm_pessoa_fisica_proprietario.cpf
                       END AS cpf_cnpj
                     , CASE WHEN sw_cgm_pessoa_fisica_proprietario.cpf IS NULL THEN
                           sw_cgm_pessoa_fisica_proprietario.rg
                       ELSE
                           ''
                       END AS rg

                      --informações do logradouro
                     , sw_tipo_logradouro.nom_tipo
                     , sw_nome_logradouro.nom_logradouro
                     , imovel.numero
                     , imovel.complemento
                     , lote_localizacao.valor as lote
                     , sw_bairro.nom_bairro as bairro
                     , vila.valor as vila
                     , quadra.valor as quadra
                     , setor.valor as setor

                     --informações do terreno
                     , area_lote.area_real as area_total_terreno
                     , confrontacao_extensao.valor as testada
                     , CASE WHEN lote_urbano.cod_lote IS NULL THEN
                          atributo_lote_rural_valor.valor
                       ELSE
                          atributo_lote_urbano_valor.valor
                       END AS codigo_situacao_quadra
                     , imovel_v_venal.venal_territorial_calculado as valor_terreno

                     --informações  das benfeitorias
                     , atributo_especie_urbana.valor as cod_especie_urbana
                     , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_total_edificacao
                     , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_privativa_edificacao
                     , atributo_tipo_material.valor as cod_tipo_material
                     , atributo_padrao_construtivo.valor as cod_padrao_construtivo
                     , EXTRACT(YEAR FROM data_construcao.data_construcao) as ano_construcao
                     , imovel_v_venal.venal_predial_calculado as valor
                     , administracao.valor_padrao_desc(atributo_utilizacao_edificacao.cod_atributo, atributo_utilizacao_edificacao.cod_modulo, atributo_utilizacao_edificacao.cod_cadastro, atributo_utilizacao_edificacao.valor) as tipo_utilizacao

                  FROM imobiliario.imovel
             LEFT JOIN imobiliario.matricula_imovel
                    ON matricula_imovel.inscricao_municipal = imovel.inscricao_municipal
                   AND matricula_imovel.timestamp           = (SELECT MAX(timestamp)
                                                                 FROM imobiliario.matricula_imovel t2
                                                                WHERE t2.inscricao_municipal = matricula_imovel.inscricao_municipal)
            INNER JOIN arrecadacao.imovel_v_venal
                    ON imovel_v_venal.inscricao_municipal = imovel.inscricao_municipal
                   AND imovel_v_venal.timestamp           = (SELECT MAX(timestamp)
                                                               FROM arrecadacao.imovel_v_venal t2
                                                              WHERE t2.inscricao_municipal = imovel_v_venal.inscricao_municipal)
             LEFT JOIN imobiliario.baixa_imovel
                    ON baixa_imovel.inscricao_municipal = imovel.inscricao_municipal
                   AND baixa_imovel.timestamp           = (SELECT MAX(timestamp)
                                                             FROM imobiliario.baixa_imovel t2
                                                            WHERE t2.inscricao_municipal = baixa_imovel.inscricao_municipal)

            --informações do logradouro
            INNER JOIN imobiliario.imovel_lote
                    ON imovel_lote.inscricao_municipal = imovel.inscricao_municipal
                   AND imovel_lote.timestamp           = (SELECT MAX(timestamp)
                                                            FROM imobiliario.imovel_lote t2
                                                           WHERE t2.inscricao_municipal = imovel_lote.inscricao_municipal)
            INNER JOIN imobiliario.confrontacao
                    ON confrontacao.cod_lote = imovel_lote.cod_lote
            INNER JOIN imobiliario.confrontacao_trecho
                    ON confrontacao_trecho.cod_confrontacao = confrontacao.cod_confrontacao
                   AND confrontacao_trecho.cod_lote         = confrontacao.cod_lote
                   AND confrontacao_trecho.principal        = true
            INNER JOIN sw_logradouro
                    ON sw_logradouro.cod_logradouro = confrontacao_trecho.cod_logradouro
            INNER JOIN sw_nome_logradouro
                    ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                   AND sw_nome_logradouro.timestamp      = (SELECT MAX(timestamp)
                                                              FROM sw_nome_logradouro t2
                                                             WHERE t2.cod_logradouro = sw_nome_logradouro.cod_logradouro)
            INNER JOIN sw_tipo_logradouro
                    ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo
            INNER JOIN imobiliario.lote_bairro
                    ON lote_bairro.cod_lote  = imovel_lote.cod_lote
                   AND lote_bairro.timestamp = (SELECT MAX(timestamp)
                                                  FROM imobiliario.lote_bairro t2
                                                 WHERE t2.cod_lote = lote_bairro.cod_lote)
            INNER JOIN sw_bairro
                    ON sw_bairro.cod_bairro    = lote_bairro.cod_bairro
                   AND sw_bairro.cod_municipio = lote_bairro.cod_municipio
                   AND sw_bairro.cod_uf        = lote_bairro.cod_uf
            INNER JOIN imobiliario.lote_localizacao
                    ON lote_localizacao.cod_lote = imovel_lote.cod_lote
            INNER JOIN imobiliario.localizacao_nivel vila
                    ON vila.cod_localizacao = lote_localizacao.cod_localizacao
                   AND vila.cod_nivel       = 1
            INNER JOIN imobiliario.localizacao_nivel setor
                    ON setor.cod_localizacao = lote_localizacao.cod_localizacao
                   AND setor.cod_nivel       = 2
            INNER JOIN imobiliario.localizacao_nivel quadra
                    ON quadra.cod_localizacao = lote_localizacao.cod_localizacao
                   AND quadra.cod_nivel       = 3

            --informações do terreno
            INNER JOIN imobiliario.area_lote
                    ON area_lote.cod_lote  = imovel_lote.cod_lote
                   AND area_lote.timestamp = (SELECT MAX(timestamp)
                                                FROM imobiliario.area_lote t2
                                               WHERE t2.cod_lote = area_lote.cod_lote)
            INNER JOIN imobiliario.confrontacao_extensao
                    ON confrontacao_extensao.cod_lote          = confrontacao.cod_lote
                   AND confrontacao_extensao.cod_confrontacao  = confrontacao.cod_confrontacao
                   AND confrontacao_extensao.timestamp         = (SELECT MAX(timestamp)
                                                                    FROM imobiliario.confrontacao_extensao t2
                                                                   WHERE confrontacao_extensao.cod_lote          = t2.cod_lote
                                                                     AND confrontacao_extensao.cod_confrontacao  = t2.cod_confrontacao)
             LEFT JOIN imobiliario.lote_urbano
                    ON lote_urbano.cod_lote = imovel_lote.cod_lote
             LEFT JOIN imobiliario.atributo_lote_urbano_valor
                    ON atributo_lote_urbano_valor.cod_lote     = lote_urbano.cod_lote
                   AND atributo_lote_urbano_valor.cod_atributo = 1004
                   AND atributo_lote_urbano_valor.timestamp    = (SELECT MAX(timestamp)
                                                                    FROM imobiliario.atributo_lote_urbano_valor t2
                                                                   WHERE t2.cod_modulo   = atributo_lote_urbano_valor.cod_modulo
                                                                     AND t2.cod_cadastro = atributo_lote_urbano_valor.cod_cadastro
                                                                     AND t2.cod_atributo = atributo_lote_urbano_valor.cod_atributo
                                                                     AND t2.cod_lote     = atributo_lote_urbano_valor.cod_lote)
             LEFT JOIN imobiliario.lote_rural
                    ON lote_rural.cod_lote = imovel_lote.cod_lote
             LEFT JOIN imobiliario.atributo_lote_rural_valor
                    ON atributo_lote_rural_valor.cod_lote     = lote_rural.cod_lote
                   AND atributo_lote_rural_valor.cod_atributo = 1004
                   AND atributo_lote_rural_valor.timestamp    = (SELECT MAX(timestamp)
                                                                    FROM imobiliario.atributo_lote_rural_valor t2
                                                                   WHERE t2.cod_modulo   = atributo_lote_rural_valor.cod_modulo
                                                                     AND t2.cod_cadastro = atributo_lote_rural_valor.cod_cadastro
                                                                     AND t2.cod_atributo = atributo_lote_rural_valor.cod_atributo
                                                                     AND t2.cod_lote     = atributo_lote_rural_valor.cod_lote)


            -- informações do município
            INNER JOIN sw_municipio
                    ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                   AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
            INNER JOIN sefazrs.municipios_iptu
                    ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                   AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

            --informações do proprietário
            INNER JOIN imobiliario.proprietario
                    ON proprietario.inscricao_municipal = imovel.inscricao_municipal
            INNER JOIN sw_cgm sw_cgm_proprietario
                    ON sw_cgm_proprietario.numcgm = proprietario.numcgm
             LEFT JOIN sw_cgm_pessoa_fisica sw_cgm_pessoa_fisica_proprietario
                    ON sw_cgm_pessoa_fisica_proprietario.numcgm = sw_cgm_proprietario.numcgm
             LEFT JOIN sw_cgm_pessoa_juridica sw_cgm_pessoa_juridica_proprietario
                    ON sw_cgm_pessoa_juridica_proprietario.numcgm = sw_cgm_proprietario.numcgm

            --informações da edificação
             LEFT JOIN imobiliario.unidade_autonoma
                    ON unidade_autonoma.inscricao_municipal = imovel.inscricao_municipal

             LEFT JOIN imobiliario.construcao_edificacao
                    ON construcao_edificacao.cod_construcao = unidade_autonoma.cod_construcao
                   AND construcao_edificacao.cod_tipo       = unidade_autonoma.cod_tipo
             LEFT JOIN imobiliario.data_construcao
                    ON data_construcao.cod_construcao = construcao_edificacao.cod_construcao
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_especie_urbana
                    ON atributo_especie_urbana.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_especie_urbana.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_especie_urbana.cod_atributo   = 1000
                   AND atributo_especie_urbana.timestamp      = (SELECT MAX(timestamp)
                                                                   FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                  WHERE t2.cod_atributo   = atributo_especie_urbana.cod_atributo
                                                                    AND t2.cod_modulo     = atributo_especie_urbana.cod_modulo
                                                                    AND t2.cod_cadastro   = atributo_especie_urbana.cod_cadastro
                                                                    AND t2.cod_construcao = atributo_especie_urbana.cod_construcao
                                                                    AND t2.cod_tipo       = atributo_especie_urbana.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_tipo_material
                    ON atributo_tipo_material.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_tipo_material.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_tipo_material.cod_atributo   = 1003
                   AND atributo_tipo_material.timestamp      = (SELECT MAX(timestamp)
                                                                  FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                 WHERE t2.cod_atributo   = atributo_tipo_material.cod_atributo
                                                                   AND t2.cod_modulo     = atributo_tipo_material.cod_modulo
                                                                   AND t2.cod_cadastro   = atributo_tipo_material.cod_cadastro
                                                                   AND t2.cod_construcao = atributo_tipo_material.cod_construcao
                                                                   AND t2.cod_tipo       = atributo_tipo_material.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_padrao_construtivo
                    ON atributo_padrao_construtivo.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_padrao_construtivo.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_padrao_construtivo.cod_atributo   = 1002
                   AND atributo_padrao_construtivo.timestamp      = (SELECT MAX(timestamp)
                                                                       FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                      WHERE t2.cod_atributo   = atributo_padrao_construtivo.cod_atributo
                                                                        AND t2.cod_modulo     = atributo_padrao_construtivo.cod_modulo
                                                                        AND t2.cod_cadastro   = atributo_padrao_construtivo.cod_cadastro
                                                                        AND t2.cod_construcao = atributo_padrao_construtivo.cod_construcao
                                                                        AND t2.cod_tipo       = atributo_padrao_construtivo.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_utilizacao_edificacao
                    ON atributo_utilizacao_edificacao.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_utilizacao_edificacao.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_utilizacao_edificacao.cod_atributo   = 1006
                   AND atributo_utilizacao_edificacao.timestamp      = (SELECT MAX(timestamp)
                                                                          FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                         WHERE t2.cod_atributo   = atributo_utilizacao_edificacao.cod_atributo
                                                                           AND t2.cod_modulo     = atributo_utilizacao_edificacao.cod_modulo
                                                                           AND t2.cod_cadastro   = atributo_utilizacao_edificacao.cod_cadastro
                                                                           AND t2.cod_construcao = atributo_utilizacao_edificacao.cod_construcao
                                                                           AND t2.cod_tipo       = atributo_utilizacao_edificacao.cod_tipo)

                 WHERE (baixa_imovel.inscricao_municipal IS NULL OR baixa_imovel.dt_termino IS NOT NULL)

             $stFiltro

              GROUP BY sw_municipio.nom_municipio
                     , municipios_iptu.cod_sefaz
                     , sw_nome_logradouro.nom_logradouro
                     , matricula_imovel.mat_registro_imovel
                     , matricula_imovel.zona
                     , imovel.inscricao_municipal
                     , sw_cgm_proprietario.nom_cgm
                     , sw_cgm_pessoa_fisica_proprietario.cpf
                     , sw_cgm_pessoa_juridica_proprietario.cnpj
                     , sw_cgm_pessoa_fisica_proprietario.rg
                     , sw_tipo_logradouro.nom_tipo
                     , imovel.numero
                     , imovel.complemento
                     , lote_localizacao.valor
                     , vila.valor
                     , setor.valor
                     , quadra.valor
                     , imovel_v_venal.venal_predial_calculado
                     , data_construcao.data_construcao
                     , sw_bairro.nom_bairro
                     , area_lote.area_real
                     , confrontacao_extensao.valor
                     , imovel_v_venal.venal_territorial_calculado
                     , atributo_lote_urbano_valor.valor
                     , lote_urbano.cod_lote
                     , lote_rural.cod_lote
                     , atributo_lote_rural_valor.valor
                     , atributo_especie_urbana.valor
                     , atributo_tipo_material.valor
                     , atributo_padrao_construtivo.valor
                     , atributo_utilizacao_edificacao.cod_modulo
                     , atributo_utilizacao_edificacao.cod_cadastro
                     , atributo_utilizacao_edificacao.cod_atributo
                     , atributo_utilizacao_edificacao.valor

             $stOrder
        ";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna os imóveis na área urbana com itbi para informar no xml da exportação
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoITBIUrbano(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY imovel.inscricao_municipal, carne.numeracao ",$boTransacao="")
    {
        $stSql = "
         SELECT
                      --informações do município
                       municipios_iptu.cod_sefaz as codigo
                     , sw_municipio.nom_municipio
                     , calculo_grupo_credito.ano_exercicio as ano


                      --informações do imóvel
                     , matricula_imovel.mat_registro_imovel as matricula_imovel
                     , matricula_imovel.zona as zona
                     , carne.numeracao as nro_guia_itbi
                     , administracao.valor_padrao_desc(atributo_utilizacao_imovel.cod_atributo, atributo_utilizacao_imovel.cod_modulo, atributo_utilizacao_imovel.cod_cadastro, atributo_utilizacao_imovel.valor) as utilizacao

                     --informações dos transmitentes
                     , sw_cgm_transmitente.nom_cgm as nome_transmitente
                     , CASE WHEN sw_cgm_pessoa_fisica_transmitente.cpf IS NULL THEN
                           sw_cgm_pessoa_juridica_transmitente.cnpj
                       ELSE
                           sw_cgm_pessoa_fisica_transmitente.cpf
                       END AS cpf_cnpj_transmitente

                     --informações dos adquirentes
                     , sw_cgm_adquirente.nom_cgm as nome_adquirente
                     , CASE WHEN sw_cgm_pessoa_fisica_adquirente.cpf IS NULL THEN
                           sw_cgm_pessoa_juridica_adquirente.cnpj
                       ELSE
                           sw_cgm_pessoa_fisica_adquirente.cpf
                       END AS cpf_cnpj_adquirente

                      --informações do logradouro
                     , sw_tipo_logradouro.nom_tipo
                     , sw_nome_logradouro.nom_logradouro
                     , imovel.numero
                     , imovel.complemento
                     , lote_localizacao.valor as lote
                     , sw_bairro.nom_bairro as bairro
                     , vila.valor as vila
                     , quadra.valor as quadra
                     , setor.valor as setor

                     --informações do terreno
                     , CASE WHEN area_lote.cod_unidade = 3 AND area_lote.cod_grandeza = 2 THEN
                          area_lote.area_real*10000
                       ELSE
                          area_lote.area_real
                       END as area_total_terreno
                     , CASE WHEN area_lote.cod_unidade = 3 AND area_lote.cod_grandeza = 2 THEN
                          area_lote.area_real*10000
                       ELSE
                          area_lote.area_real
                       END as area_transmitida_terreno
                     , confrontacao_extensao.valor as testada
                     , atributo_lote_urbano_valor.valor as codigo_situacao_quadra
                     , imovel_v_venal.venal_territorial_declarado as valor_declarado_terreno
                     , imovel_v_venal.venal_territorial_avaliado as valor_avaliado_terreno
                     , to_char(imovel_v_venal.timestamp, 'dd/mm/yyyy') as data_avaliacao_terreno

                     --informações  das benfeitorias
                     , atributo_especie_urbana.valor as codigo_especie_urbana
                     , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_total_edificacao
                     , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_transmitida_edificacao
                     , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_privativa_edificacao
                     , atributo_tipo_material.valor as codigo_tipo_material
                     , atributo_padrao_construtivo.valor as codigo_padrao_construtivo
                     , EXTRACT(YEAR FROM data_construcao.data_construcao) as ano_construcao
                     , imovel_v_venal.venal_predial_declarado as valor_declarado_edificacao
                     , imovel_v_venal.venal_predial_avaliado as valor_avaliado_edificacao
                     , to_char(imovel_v_venal.timestamp, 'dd/mm/yyyy') as data_avaliacao_edificacao
                     , administracao.valor_padrao_desc(atributo_utilizacao_edificacao.cod_atributo, atributo_utilizacao_edificacao.cod_modulo, atributo_utilizacao_edificacao.cod_cadastro, atributo_utilizacao_edificacao.valor) as tipo_utilizacao


                  FROM imobiliario.imovel
             LEFT JOIN imobiliario.matricula_imovel
                    ON matricula_imovel.inscricao_municipal = imovel.inscricao_municipal
                   AND matricula_imovel.timestamp           = (SELECT MAX(timestamp)
                                                                 FROM imobiliario.matricula_imovel t2
                                                                WHERE t2.inscricao_municipal = matricula_imovel.inscricao_municipal)
            INNER JOIN arrecadacao.imovel_v_venal
                    ON imovel_v_venal.inscricao_municipal = imovel.inscricao_municipal

             LEFT JOIN imobiliario.baixa_imovel
                    ON baixa_imovel.inscricao_municipal = imovel.inscricao_municipal
                   AND baixa_imovel.timestamp           = (SELECT MAX(timestamp)
                                                             FROM imobiliario.baixa_imovel t2
                                                            WHERE t2.inscricao_municipal = baixa_imovel.inscricao_municipal)
            INNER JOIN arrecadacao.imovel_calculo
                    ON imovel_calculo.inscricao_municipal = imovel_v_venal.inscricao_municipal
                   AND imovel_calculo.timestamp           = imovel_v_venal.timestamp
            INNER JOIN arrecadacao.lancamento_calculo
                    ON lancamento_calculo.cod_calculo = imovel_calculo.cod_calculo
            INNER JOIN arrecadacao.parcela
                    ON parcela.cod_lancamento = lancamento_calculo.cod_lancamento
            INNER JOIN arrecadacao.carne
                    ON carne.cod_parcela = parcela.cod_parcela
            INNER JOIN arrecadacao.calculo_grupo_credito
                    ON calculo_grupo_credito.cod_calculo = imovel_calculo.cod_calculo
                   AND calculo_grupo_credito.cod_grupo = (SELECT split_part(valor, '/', 1)
                                                            FROM administracao.configuracao
                                                           WHERE exercicio  = '".Sessao::getExercicio()."'
                                                             AND parametro  = 'grupo_credito_itbi'
                                                             AND cod_modulo = 25)
                   AND calculo_grupo_credito.ano_exercicio = (SELECT split_part(valor, '/', 2)
                                                                FROM administracao.configuracao
                                                               WHERE exercicio  = '".Sessao::getExercicio()."'
                                                                 AND parametro  = 'grupo_credito_itbi'
                                                                 AND cod_modulo = 25)

            INNER JOIN arrecadacao.pagamento
                    ON pagamento.numeracao    = carne.numeracao
                   AND pagamento.cod_convenio = carne.cod_convenio

            --informações do logradouro
            INNER JOIN imobiliario.imovel_lote
                    ON imovel_lote.inscricao_municipal = imovel.inscricao_municipal
                   AND imovel_lote.timestamp           = (SELECT MAX(timestamp)
                                                            FROM imobiliario.imovel_lote t2
                                                           WHERE t2.inscricao_municipal = imovel_lote.inscricao_municipal)
            INNER JOIN imobiliario.confrontacao
                    ON confrontacao.cod_lote = imovel_lote.cod_lote
            INNER JOIN imobiliario.confrontacao_trecho
                    ON confrontacao_trecho.cod_confrontacao = confrontacao.cod_confrontacao
                   AND confrontacao_trecho.cod_lote         = confrontacao.cod_lote
                   AND confrontacao_trecho.principal        = true
            INNER JOIN sw_logradouro
                    ON sw_logradouro.cod_logradouro = confrontacao_trecho.cod_logradouro
            INNER JOIN sw_nome_logradouro
                    ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                   AND sw_nome_logradouro.timestamp      = (SELECT MAX(timestamp)
                                                              FROM sw_nome_logradouro t2
                                                             WHERE t2.cod_logradouro = sw_nome_logradouro.cod_logradouro)
            INNER JOIN sw_tipo_logradouro
                    ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo
            INNER JOIN imobiliario.lote_bairro
                    ON lote_bairro.cod_lote  = imovel_lote.cod_lote
                   AND lote_bairro.timestamp = (SELECT MAX(timestamp)
                                                  FROM imobiliario.lote_bairro t2
                                                 WHERE t2.cod_lote = lote_bairro.cod_lote)
            INNER JOIN sw_bairro
                    ON sw_bairro.cod_bairro    = lote_bairro.cod_bairro
                   AND sw_bairro.cod_municipio = lote_bairro.cod_municipio
                   AND sw_bairro.cod_uf        = lote_bairro.cod_uf
            INNER JOIN imobiliario.lote_localizacao
                    ON lote_localizacao.cod_lote = imovel_lote.cod_lote
            INNER JOIN imobiliario.localizacao_nivel vila
                    ON vila.cod_localizacao = lote_localizacao.cod_localizacao
                   AND vila.cod_nivel       = 1
            INNER JOIN imobiliario.localizacao_nivel setor
                    ON setor.cod_localizacao = lote_localizacao.cod_localizacao
                   AND setor.cod_nivel       = 2
            INNER JOIN imobiliario.localizacao_nivel quadra
                    ON quadra.cod_localizacao = lote_localizacao.cod_localizacao
                   AND quadra.cod_nivel       = 3

            --pega as informações somente de lote urbano
            INNER JOIN imobiliario.lote_urbano
                    ON lote_urbano.cod_lote = imovel_lote.cod_lote

            --informações do terreno
            INNER JOIN imobiliario.area_lote
                    ON area_lote.cod_lote  = imovel_lote.cod_lote
                   AND area_lote.timestamp = (SELECT MAX(timestamp)
                                                FROM imobiliario.area_lote t2
                                               WHERE t2.cod_lote = area_lote.cod_lote)
            INNER JOIN imobiliario.confrontacao_extensao
                    ON confrontacao_extensao.cod_lote          = confrontacao.cod_lote
                   AND confrontacao_extensao.cod_confrontacao  = confrontacao.cod_confrontacao
                   AND confrontacao_extensao.timestamp         = (SELECT MAX(timestamp)
                                                                    FROM imobiliario.confrontacao_extensao t2
                                                                   WHERE confrontacao_extensao.cod_lote          = t2.cod_lote
                                                                     AND confrontacao_extensao.cod_confrontacao  = t2.cod_confrontacao)
             LEFT JOIN imobiliario.atributo_lote_urbano_valor
                    ON atributo_lote_urbano_valor.cod_lote     = lote_urbano.cod_lote
                   AND atributo_lote_urbano_valor.cod_atributo = 1004
                   AND atributo_lote_urbano_valor.timestamp    = (SELECT MAX(timestamp)
                                                                    FROM imobiliario.atributo_lote_urbano_valor t2
                                                                   WHERE t2.cod_modulo   = atributo_lote_urbano_valor.cod_modulo
                                                                     AND t2.cod_cadastro = atributo_lote_urbano_valor.cod_cadastro
                                                                     AND t2.cod_atributo = atributo_lote_urbano_valor.cod_atributo
                                                                     AND t2.cod_lote     = atributo_lote_urbano_valor.cod_lote)


            -- informações do município
            INNER JOIN sw_municipio
                    ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                   AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
            INNER JOIN sefazrs.municipios_iptu
                    ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                   AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

            --informações dos transmitentes
            INNER JOIN imobiliario.transferencia_imovel
                    ON transferencia_imovel.inscricao_municipal = imovel.inscricao_municipal
            INNER JOIN imobiliario.transferencia_efetivacao
                    ON transferencia_efetivacao.cod_transferencia = transferencia_imovel.cod_transferencia
                   AND transferencia_efetivacao.dt_efetivacao     = pagamento.data_baixa
            INNER JOIN imobiliario.ex_proprietario
                    ON ex_proprietario.inscricao_municipal = imovel.inscricao_municipal
                   AND ex_proprietario.timestamp           = (SELECT MAX(timestamp)
                                                                FROM imobiliario.ex_proprietario t2
                                                               WHERE t2.inscricao_municipal = ex_proprietario.inscricao_municipal
                                                                 AND TO_DATE(t2.timestamp ,'yyyy-mm-dd hh24:mi:ss') = transferencia_efetivacao.dt_efetivacao)
            INNER JOIN sw_cgm sw_cgm_transmitente
                    ON sw_cgm_transmitente.numcgm = ex_proprietario.numcgm
             LEFT JOIN sw_cgm_pessoa_fisica sw_cgm_pessoa_fisica_transmitente
                    ON sw_cgm_pessoa_fisica_transmitente.numcgm = sw_cgm_transmitente.numcgm
             LEFT JOIN sw_cgm_pessoa_juridica sw_cgm_pessoa_juridica_transmitente
                    ON sw_cgm_pessoa_juridica_transmitente.numcgm = sw_cgm_transmitente.numcgm

            --informações dos adquirentes
            INNER JOIN imobiliario.transferencia_adquirente
                    ON transferencia_imovel.cod_transferencia = transferencia_adquirente.cod_transferencia
            INNER JOIN sw_cgm sw_cgm_adquirente
                    ON sw_cgm_adquirente.numcgm = transferencia_adquirente.numcgm
             LEFT JOIN sw_cgm_pessoa_fisica sw_cgm_pessoa_fisica_adquirente
                    ON sw_cgm_pessoa_fisica_adquirente.numcgm = sw_cgm_adquirente.numcgm
             LEFT JOIN sw_cgm_pessoa_juridica sw_cgm_pessoa_juridica_adquirente
                    ON sw_cgm_pessoa_juridica_adquirente.numcgm = sw_cgm_adquirente.numcgm

            --informações da edificação
             LEFT JOIN imobiliario.unidade_autonoma
                    ON unidade_autonoma.inscricao_municipal = imovel.inscricao_municipal

             LEFT JOIN imobiliario.construcao_edificacao
                    ON construcao_edificacao.cod_construcao = unidade_autonoma.cod_construcao
                   AND construcao_edificacao.cod_tipo       = unidade_autonoma.cod_tipo
             LEFT JOIN imobiliario.data_construcao
                    ON data_construcao.cod_construcao = construcao_edificacao.cod_construcao
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_especie_urbana
                    ON atributo_especie_urbana.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_especie_urbana.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_especie_urbana.cod_atributo   = 1000
                   AND atributo_especie_urbana.timestamp      = (SELECT MAX(timestamp)
                                                                   FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                  WHERE t2.cod_atributo   = atributo_especie_urbana.cod_atributo
                                                                    AND t2.cod_modulo     = atributo_especie_urbana.cod_modulo
                                                                    AND t2.cod_cadastro   = atributo_especie_urbana.cod_cadastro
                                                                    AND t2.cod_construcao = atributo_especie_urbana.cod_construcao
                                                                    AND t2.cod_tipo       = atributo_especie_urbana.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_tipo_material
                    ON atributo_tipo_material.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_tipo_material.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_tipo_material.cod_atributo   = 1003
                   AND atributo_tipo_material.timestamp      = (SELECT MAX(timestamp)
                                                                   FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                  WHERE t2.cod_atributo   = atributo_tipo_material.cod_atributo
                                                                    AND t2.cod_modulo     = atributo_tipo_material.cod_modulo
                                                                    AND t2.cod_cadastro   = atributo_tipo_material.cod_cadastro
                                                                    AND t2.cod_construcao = atributo_tipo_material.cod_construcao
                                                                    AND t2.cod_tipo       = atributo_tipo_material.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_padrao_construtivo
                    ON atributo_padrao_construtivo.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_padrao_construtivo.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_padrao_construtivo.cod_atributo   = 1002
                   AND atributo_padrao_construtivo.timestamp      = (SELECT MAX(timestamp)
                                                                   FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                  WHERE t2.cod_atributo   = atributo_padrao_construtivo.cod_atributo
                                                                    AND t2.cod_modulo     = atributo_padrao_construtivo.cod_modulo
                                                                    AND t2.cod_cadastro   = atributo_padrao_construtivo.cod_cadastro
                                                                    AND t2.cod_construcao = atributo_padrao_construtivo.cod_construcao
                                                                    AND t2.cod_tipo       = atributo_padrao_construtivo.cod_tipo)
             LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_utilizacao_edificacao
                    ON atributo_utilizacao_edificacao.cod_construcao = construcao_edificacao.cod_construcao
                   AND atributo_utilizacao_edificacao.cod_tipo       = construcao_edificacao.cod_tipo
                   AND atributo_utilizacao_edificacao.cod_atributo   = 1006
                   AND atributo_utilizacao_edificacao.timestamp      = (SELECT MAX(timestamp)
                                                                          FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                         WHERE t2.cod_atributo   = atributo_utilizacao_edificacao.cod_atributo
                                                                           AND t2.cod_modulo     = atributo_utilizacao_edificacao.cod_modulo
                                                                           AND t2.cod_cadastro   = atributo_utilizacao_edificacao.cod_cadastro
                                                                           AND t2.cod_construcao = atributo_utilizacao_edificacao.cod_construcao
                                                                           AND t2.cod_tipo       = atributo_utilizacao_edificacao.cod_tipo)
             LEFT JOIN imobiliario.atributo_imovel_valor atributo_utilizacao_imovel
                    ON atributo_utilizacao_imovel.inscricao_municipal = imovel.inscricao_municipal
                   AND atributo_utilizacao_imovel.cod_atributo        = 124
                   AND atributo_utilizacao_imovel.timestamp           = (SELECT MAX(timestamp)
                                                                               FROM imobiliario.atributo_imovel_valor t2
                                                                              WHERE t2.cod_atributo        = atributo_utilizacao_imovel.cod_atributo
                                                                                AND t2.cod_modulo          = atributo_utilizacao_imovel.cod_modulo
                                                                                AND t2.cod_cadastro        = atributo_utilizacao_imovel.cod_cadastro
                                                                                AND t2.inscricao_municipal = atributo_utilizacao_imovel.inscricao_municipal)

                 WHERE (baixa_imovel.inscricao_municipal IS NULL OR baixa_imovel.dt_termino IS NOT NULL)

                 $stFiltro

              GROUP BY sw_municipio.nom_municipio
                     , municipios_iptu.cod_sefaz
                     , sw_nome_logradouro.nom_logradouro
                     , matricula_imovel.mat_registro_imovel
                     , imovel.inscricao_municipal
                     , sw_cgm_transmitente.nom_cgm
                     , sw_cgm_pessoa_fisica_transmitente.cpf
                     , sw_cgm_pessoa_juridica_transmitente.cnpj
                     , sw_cgm_pessoa_fisica_transmitente.rg
                     , sw_cgm_adquirente.nom_cgm
                     , sw_cgm_pessoa_fisica_adquirente.cpf
                     , sw_cgm_pessoa_juridica_adquirente.cnpj
                     , sw_cgm_pessoa_fisica_adquirente.rg
                     , sw_tipo_logradouro.nom_tipo
                     , imovel.numero
                     , imovel.complemento
                     , lote_localizacao.valor
                     , vila.valor
                     , setor.valor
                     , quadra.valor
                     , imovel_v_venal.venal_predial_calculado
                     , sw_bairro.nom_bairro
                     , area_lote.area_real
                     , confrontacao_extensao.valor
                     , imovel_v_venal.venal_territorial_declarado
                     , imovel_v_venal.venal_territorial_avaliado
                     , imovel_v_venal.timestamp
                     , carne.numeracao
                     , data_construcao.data_construcao
                     , imovel_v_venal.venal_predial_declarado
                     , imovel_v_venal.venal_predial_avaliado
                     , area_lote.cod_unidade
                     , area_lote.cod_grandeza
                     , calculo_grupo_credito.ano_exercicio
                     , atributo_lote_urbano_valor.valor
                     , matricula_imovel.zona
                     , atributo_especie_urbana.valor
                     , atributo_tipo_material.valor
                     , atributo_padrao_construtivo.valor
                     , atributo_utilizacao_edificacao.cod_modulo
                     , atributo_utilizacao_edificacao.cod_cadastro
                     , atributo_utilizacao_edificacao.cod_atributo
                     , atributo_utilizacao_edificacao.valor
                     , atributo_utilizacao_imovel.cod_modulo
                     , atributo_utilizacao_imovel.cod_cadastro
                     , atributo_utilizacao_imovel.cod_atributo
                     , atributo_utilizacao_imovel.valor

              $stOrder";

        return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna os imóveis na área rural com itbi para informar no xml da exportação
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoITBIRural(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY imovel.inscricao_municipal, carne.numeracao ",$boTransacao="")
    {
      $stSql = "
              SELECT
                    --informações do município
                     municipios_iptu.cod_sefaz as codigo
                   , sw_municipio.nom_municipio
                   , calculo_grupo_credito.ano_exercicio as ano


                    --informações do imóvel
                   , matricula_imovel.mat_registro_imovel as matricula_imovel
                   , matricula_imovel.zona
                   , carne.numeracao as nro_guia_itbi
                   , administracao.valor_padrao_desc(atributo_utilizacao_imovel.cod_atributo, atributo_utilizacao_imovel.cod_modulo, atributo_utilizacao_imovel.cod_cadastro, atributo_utilizacao_imovel.valor) as utilizacao

                   --informações dos transmitentes
                   , sw_cgm_transmitente.nom_cgm as nome_transmitente
                   , CASE WHEN sw_cgm_pessoa_fisica_transmitente.cpf IS NULL THEN
                         sw_cgm_pessoa_juridica_transmitente.cnpj
                     ELSE
                         sw_cgm_pessoa_fisica_transmitente.cpf
                     END AS cpf_cnpj_transmitente
                   , CASE WHEN sw_cgm_pessoa_fisica_transmitente.cpf IS NULL THEN
                         sw_cgm_pessoa_fisica_transmitente.rg
                     ELSE
                         ''
                     END AS rg_transmitente

                   --informações dos adquirentes
                   , sw_cgm_adquirente.nom_cgm as nome_adquirente
                   , CASE WHEN sw_cgm_pessoa_fisica_adquirente.cpf IS NULL THEN
                         sw_cgm_pessoa_juridica_adquirente.cnpj
                     ELSE
                         sw_cgm_pessoa_fisica_adquirente.cpf
                     END AS cpf_cnpj_adquirente
                   , CASE WHEN sw_cgm_pessoa_fisica_adquirente.cpf IS NULL THEN
                         sw_cgm_pessoa_fisica_adquirente.rg
                     ELSE
                         ''
                     END AS rg_adquirente

                    --informações do endereço
                   , distrito.valor as localidade
                   , setor.valor as distrito
                   , lote_localizacao.valor as lote
                   , imovel.complemento
                   , ARRAY_TO_STRING(ARRAY(SELECT TRIM(tipo_logradouro_confrontacoes.nom_tipo||' '||nome_logradouro_confrontacoes.nom_logradouro) as descricao
                                             FROM imobiliario.confrontacao_trecho confrontacoes
                                       INNER JOIN sw_logradouro logradouro_confrontacoes
                                               ON logradouro_confrontacoes.cod_logradouro = confrontacoes.cod_logradouro
                                       INNER JOIN sw_nome_logradouro nome_logradouro_confrontacoes
                                               ON nome_logradouro_confrontacoes.cod_logradouro = logradouro_confrontacoes.cod_logradouro
                                       INNER JOIN sw_tipo_logradouro tipo_logradouro_confrontacoes
                                               ON tipo_logradouro_confrontacoes.cod_tipo = nome_logradouro_confrontacoes.cod_tipo
                                            WHERE confrontacoes.cod_lote         = confrontacao.cod_lote
                                              AND confrontacoes.principal        = false
                                        UNION ALL
                                           SELECT descricao
                                             FROM imobiliario.confrontacao_diversa confrontacoes
                                            WHERE confrontacoes.cod_lote = confrontacao.cod_lote
                                        UNION ALL
                                           SELECT 'Lote: '||valor as descricao
                                             FROM imobiliario.lote_localizacao
                                            WHERE lote_localizacao.cod_lote = confrontacao.cod_lote
                                            ), ', ') as confrontacoes

                   --informações do terra
                   , CASE WHEN area_lote.cod_unidade = 1 AND area_lote.cod_grandeza = 2 THEN
                        area_lote.area_real/10000
                     ELSE
                        area_lote.area_real
                     END as area_total_terreno
                   , CASE WHEN area_lote.cod_unidade = 1 AND area_lote.cod_grandeza = 2 THEN
                        area_lote.area_real/10000
                     ELSE
                        area_lote.area_real
                     END as area_transmitida_terreno
                   , atributo_situacao_terras.valor as codigo_situacao_terra
                   , imovel_v_venal.venal_territorial_declarado as valor_declarado_terreno
                   , imovel_v_venal.venal_territorial_avaliado as valor_avaliado_terreno
                   , to_char(imovel_v_venal.timestamp, 'dd/mm/yyyy') as data_avaliacao_terreno
                   , administracao.valor_padrao_desc(atributo_utilizacao_terras.cod_atributo, atributo_utilizacao_terras.cod_modulo, atributo_utilizacao_terras.cod_cadastro, atributo_utilizacao_terras.valor) as tipo_utilizacao

                   --informações  das benfeitorias
                   , atributo_especie_rural.valor as codigo_especie_rural
                   , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_total_edificacao
                   , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_transmitida_edificacao
                   , imobiliario.fn_calcula_area_imovel(imovel.inscricao_municipal) as area_privativa_edificacao
                   , atributo_tipo_material.valor as codigo_tipo_material
                   , atributo_padrao_construtivo.valor as codigo_padrao_construtivo
                   , EXTRACT(YEAR FROM data_construcao.data_construcao) as ano_construcao
                   , imovel_v_venal.venal_predial_declarado as valor_declarado_edificacao
                   , imovel_v_venal.venal_predial_avaliado as valor_avaliado_edificacao
                   , to_char(imovel_v_venal.timestamp, 'dd/mm/yyyy') as data_avaliacao_edificacao






                FROM imobiliario.imovel
           LEFT JOIN imobiliario.matricula_imovel
                  ON matricula_imovel.inscricao_municipal = imovel.inscricao_municipal
                 AND matricula_imovel.timestamp           = (SELECT MAX(timestamp)
                                                               FROM imobiliario.matricula_imovel t2
                                                              WHERE t2.inscricao_municipal = matricula_imovel.inscricao_municipal)
          INNER JOIN arrecadacao.imovel_v_venal
                  ON imovel_v_venal.inscricao_municipal = imovel.inscricao_municipal
                 AND imovel_v_venal.timestamp           = (SELECT MAX(timestamp)
                                                             FROM arrecadacao.imovel_v_venal t2
                                                            WHERE t2.inscricao_municipal = imovel_v_venal.inscricao_municipal)
           LEFT JOIN imobiliario.baixa_imovel
                  ON baixa_imovel.inscricao_municipal = imovel.inscricao_municipal
                 AND baixa_imovel.timestamp           = (SELECT MAX(timestamp)
                                                           FROM imobiliario.baixa_imovel t2
                                                          WHERE t2.inscricao_municipal = baixa_imovel.inscricao_municipal)
          INNER JOIN arrecadacao.imovel_calculo
                  ON imovel_calculo.inscricao_municipal = imovel_v_venal.inscricao_municipal
                 AND imovel_calculo.timestamp           = imovel_v_venal.timestamp
          INNER JOIN arrecadacao.lancamento_calculo
                  ON lancamento_calculo.cod_calculo = imovel_calculo.cod_calculo
          INNER JOIN arrecadacao.parcela
                  ON parcela.cod_lancamento = lancamento_calculo.cod_lancamento
          INNER JOIN arrecadacao.carne
                  ON carne.cod_parcela = parcela.cod_parcela
          INNER JOIN arrecadacao.calculo_grupo_credito
                  ON calculo_grupo_credito.cod_calculo = imovel_calculo.cod_calculo
                 AND calculo_grupo_credito.cod_grupo = (SELECT split_part(valor, '/', 1)
                                                          FROM administracao.configuracao
                                                         WHERE exercicio  = '2013'
                                                           AND parametro  = 'grupo_credito_itbi'
                                                           AND cod_modulo = 25)
                 AND calculo_grupo_credito.ano_exercicio = (SELECT split_part(valor, '/', 2)
                                                              FROM administracao.configuracao
                                                             WHERE exercicio  = '2013'
                                                               AND parametro  = 'grupo_credito_itbi'
                                                               AND cod_modulo = 25)

          INNER JOIN arrecadacao.pagamento
                  ON pagamento.numeracao    = carne.numeracao
                 AND pagamento.cod_convenio = carne.cod_convenio

          --informações do endereço
          INNER JOIN imobiliario.imovel_lote
                  ON imovel_lote.inscricao_municipal = imovel.inscricao_municipal
                 AND imovel_lote.timestamp           = (SELECT MAX(timestamp)
                                                          FROM imobiliario.imovel_lote t2
                                                         WHERE t2.inscricao_municipal = imovel_lote.inscricao_municipal)
          INNER JOIN imobiliario.confrontacao
                  ON confrontacao.cod_lote = imovel_lote.cod_lote
          INNER JOIN imobiliario.confrontacao_trecho
                  ON confrontacao_trecho.cod_confrontacao = confrontacao.cod_confrontacao
                 AND confrontacao_trecho.cod_lote         = confrontacao.cod_lote
                 AND confrontacao_trecho.principal        = true
          INNER JOIN sw_logradouro
                  ON sw_logradouro.cod_logradouro = confrontacao_trecho.cod_logradouro
          INNER JOIN imobiliario.lote_localizacao
                  ON lote_localizacao.cod_lote = imovel_lote.cod_lote
          LEFT JOIN imobiliario.localizacao_nivel distrito
                  ON distrito.cod_localizacao = lote_localizacao.cod_localizacao
                 AND distrito.cod_nivel       = 1
          LEFT JOIN imobiliario.localizacao_nivel setor
                  ON setor.cod_localizacao = lote_localizacao.cod_localizacao
                 AND setor.cod_nivel       = 2
          LEFT JOIN imobiliario.localizacao_nivel quadra
                  ON quadra.cod_localizacao = lote_localizacao.cod_localizacao
                 AND quadra.cod_nivel       = 3

          --pega as informações somente de lote rural
          INNER JOIN imobiliario.lote_rural
                  ON lote_rural.cod_lote = imovel_lote.cod_lote

          --informações do terra
          INNER JOIN imobiliario.area_lote
                  ON area_lote.cod_lote  = imovel_lote.cod_lote
                 AND area_lote.timestamp = (SELECT MAX(timestamp)
                                              FROM imobiliario.area_lote t2
                                             WHERE t2.cod_lote = area_lote.cod_lote)
           LEFT JOIN imobiliario.atributo_lote_rural_valor atributo_situacao_terras
                  ON atributo_situacao_terras.cod_lote     = lote_rural.cod_lote
                 AND atributo_situacao_terras.cod_atributo = 1005
                 AND atributo_situacao_terras.timestamp    = (SELECT MAX(timestamp)
                                                                  FROM imobiliario.atributo_lote_rural_valor t2
                                                                 WHERE t2.cod_modulo   = atributo_situacao_terras.cod_modulo
                                                                   AND t2.cod_cadastro = atributo_situacao_terras.cod_cadastro
                                                                   AND t2.cod_atributo = atributo_situacao_terras.cod_atributo
                                                                   AND t2.cod_lote     = atributo_situacao_terras.cod_lote)
           LEFT JOIN imobiliario.atributo_lote_rural_valor atributo_utilizacao_terras
                  ON atributo_utilizacao_terras.cod_lote     = lote_rural.cod_lote
                 AND atributo_utilizacao_terras.cod_atributo = 1007
                 AND atributo_utilizacao_terras.timestamp    = (SELECT MAX(timestamp)
                                                                  FROM imobiliario.atributo_lote_rural_valor t2
                                                                 WHERE t2.cod_modulo   = atributo_utilizacao_terras.cod_modulo
                                                                   AND t2.cod_cadastro = atributo_utilizacao_terras.cod_cadastro
                                                                   AND t2.cod_atributo = atributo_utilizacao_terras.cod_atributo
                                                                   AND t2.cod_lote     = atributo_utilizacao_terras.cod_lote)


          -- informações do município
          INNER JOIN sw_municipio
                  ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                 AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
          INNER JOIN sefazrs.municipios_iptu
                  ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                 AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

          --informações dos transmitentes
          INNER JOIN imobiliario.transferencia_imovel
                  ON transferencia_imovel.inscricao_municipal = imovel.inscricao_municipal
          INNER JOIN imobiliario.transferencia_efetivacao
                  ON transferencia_efetivacao.cod_transferencia = transferencia_imovel.cod_transferencia
                 AND transferencia_efetivacao.dt_efetivacao     = pagamento.data_baixa
          INNER JOIN imobiliario.ex_proprietario
                  ON ex_proprietario.inscricao_municipal = imovel.inscricao_municipal
                 AND ex_proprietario.timestamp           = (SELECT MAX(timestamp)
                                                              FROM imobiliario.ex_proprietario t2
                                                             WHERE t2.inscricao_municipal = ex_proprietario.inscricao_municipal
                                                               AND TO_DATE(t2.timestamp ,'yyyy-mm-dd hh24:mi:ss') = transferencia_efetivacao.dt_efetivacao)
          INNER JOIN sw_cgm sw_cgm_transmitente
                  ON sw_cgm_transmitente.numcgm = ex_proprietario.numcgm
           LEFT JOIN sw_cgm_pessoa_fisica sw_cgm_pessoa_fisica_transmitente
                  ON sw_cgm_pessoa_fisica_transmitente.numcgm = sw_cgm_transmitente.numcgm
           LEFT JOIN sw_cgm_pessoa_juridica sw_cgm_pessoa_juridica_transmitente
                  ON sw_cgm_pessoa_juridica_transmitente.numcgm = sw_cgm_transmitente.numcgm

          --informações dos adquirentes
          INNER JOIN imobiliario.transferencia_adquirente
                  ON transferencia_imovel.cod_transferencia = transferencia_adquirente.cod_transferencia
          INNER JOIN sw_cgm sw_cgm_adquirente
                  ON sw_cgm_adquirente.numcgm = transferencia_adquirente.numcgm
           LEFT JOIN sw_cgm_pessoa_fisica sw_cgm_pessoa_fisica_adquirente
                  ON sw_cgm_pessoa_fisica_adquirente.numcgm = sw_cgm_adquirente.numcgm
           LEFT JOIN sw_cgm_pessoa_juridica sw_cgm_pessoa_juridica_adquirente
                  ON sw_cgm_pessoa_juridica_adquirente.numcgm = sw_cgm_adquirente.numcgm

          --informações da edificação
           LEFT JOIN imobiliario.unidade_autonoma
                  ON unidade_autonoma.inscricao_municipal = imovel.inscricao_municipal

           LEFT JOIN imobiliario.construcao_edificacao
                  ON construcao_edificacao.cod_construcao = unidade_autonoma.cod_construcao
                 AND construcao_edificacao.cod_tipo       = unidade_autonoma.cod_tipo
           LEFT JOIN imobiliario.data_construcao
                  ON data_construcao.cod_construcao = construcao_edificacao.cod_construcao

           LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_especie_rural
                  ON atributo_especie_rural.cod_construcao = construcao_edificacao.cod_construcao
                 AND atributo_especie_rural.cod_tipo       = construcao_edificacao.cod_tipo
                 AND atributo_especie_rural.cod_atributo   = 1001
                 AND atributo_especie_rural.timestamp      = (SELECT MAX(timestamp)
                                                                 FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                WHERE t2.cod_atributo   = atributo_especie_rural.cod_atributo
                                                                  AND t2.cod_modulo     = atributo_especie_rural.cod_modulo
                                                                  AND t2.cod_cadastro   = atributo_especie_rural.cod_cadastro
                                                                  AND t2.cod_construcao = atributo_especie_rural.cod_construcao
                                                                  AND t2.cod_tipo       = atributo_especie_rural.cod_tipo)
           LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_tipo_material
                  ON atributo_tipo_material.cod_construcao = construcao_edificacao.cod_construcao
                 AND atributo_tipo_material.cod_tipo       = construcao_edificacao.cod_tipo
                 AND atributo_tipo_material.cod_atributo   = 1003
                 AND atributo_tipo_material.timestamp      = (SELECT MAX(timestamp)
                                                                 FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                WHERE t2.cod_atributo   = atributo_tipo_material.cod_atributo
                                                                  AND t2.cod_modulo     = atributo_tipo_material.cod_modulo
                                                                  AND t2.cod_cadastro   = atributo_tipo_material.cod_cadastro
                                                                  AND t2.cod_construcao = atributo_tipo_material.cod_construcao
                                                                  AND t2.cod_tipo       = atributo_tipo_material.cod_tipo)
           LEFT JOIN imobiliario.atributo_tipo_edificacao_valor atributo_padrao_construtivo
                  ON atributo_padrao_construtivo.cod_construcao = construcao_edificacao.cod_construcao
                 AND atributo_padrao_construtivo.cod_tipo       = construcao_edificacao.cod_tipo
                 AND atributo_padrao_construtivo.cod_atributo   = 1002
                 AND atributo_padrao_construtivo.timestamp      = (SELECT MAX(timestamp)
                                                                 FROM imobiliario.atributo_tipo_edificacao_valor t2
                                                                WHERE t2.cod_atributo   = atributo_padrao_construtivo.cod_atributo
                                                                  AND t2.cod_modulo     = atributo_padrao_construtivo.cod_modulo
                                                                  AND t2.cod_cadastro   = atributo_padrao_construtivo.cod_cadastro
                                                                  AND t2.cod_construcao = atributo_padrao_construtivo.cod_construcao
                                                                  AND t2.cod_tipo       = atributo_padrao_construtivo.cod_tipo)
           LEFT JOIN imobiliario.atributo_imovel_valor atributo_utilizacao_imovel
                  ON atributo_utilizacao_imovel.inscricao_municipal = imovel.inscricao_municipal
                 AND atributo_utilizacao_imovel.cod_atributo        = 124
                 AND atributo_utilizacao_imovel.timestamp           = (SELECT MAX(timestamp)
                                                                             FROM imobiliario.atributo_imovel_valor t2
                                                                            WHERE t2.cod_atributo        = atributo_utilizacao_imovel.cod_atributo
                                                                              AND t2.cod_modulo          = atributo_utilizacao_imovel.cod_modulo
                                                                              AND t2.cod_cadastro        = atributo_utilizacao_imovel.cod_cadastro
                                                                              AND t2.inscricao_municipal = atributo_utilizacao_imovel.inscricao_municipal)

               WHERE (baixa_imovel.inscricao_municipal IS NULL OR baixa_imovel.dt_termino IS NOT NULL)
                 $stFiltro

            GROUP BY sw_municipio.nom_municipio
                   , municipios_iptu.cod_sefaz
                   , matricula_imovel.mat_registro_imovel
                   , imovel.inscricao_municipal
                   , sw_cgm_transmitente.nom_cgm
                   , sw_cgm_pessoa_fisica_transmitente.cpf
                   , sw_cgm_pessoa_juridica_transmitente.cnpj
                   , sw_cgm_pessoa_fisica_transmitente.rg
                   , sw_cgm_adquirente.nom_cgm
                   , sw_cgm_pessoa_fisica_adquirente.cpf
                   , sw_cgm_pessoa_juridica_adquirente.cnpj
                   , sw_cgm_pessoa_fisica_adquirente.rg
                   , imovel.numero
                   , imovel.complemento
                   , lote_localizacao.valor
                   , distrito.valor
                   , setor.valor
                   , quadra.valor
                   , imovel_v_venal.venal_predial_calculado
                   , area_lote.area_real
                   , imovel_v_venal.venal_territorial_declarado
                   , imovel_v_venal.venal_territorial_avaliado
                   , imovel_v_venal.timestamp
                   , carne.numeracao
                   , data_construcao.data_construcao
                   , imovel_v_venal.venal_predial_declarado
                   , imovel_v_venal.venal_predial_avaliado
                   , area_lote.cod_unidade
                   , area_lote.cod_grandeza
                   , matricula_imovel.zona
                   , atributo_situacao_terras.valor
                   , atributo_especie_rural.valor
                   , atributo_tipo_material.valor
                   , atributo_padrao_construtivo.valor
                   , calculo_grupo_credito.ano_exercicio
                   , confrontacao.cod_confrontacao
                   , confrontacao.cod_lote
                   , atributo_utilizacao_imovel.cod_modulo
                   , atributo_utilizacao_imovel.cod_cadastro
                   , atributo_utilizacao_imovel.cod_atributo
                   , atributo_utilizacao_imovel.valor
                   , atributo_utilizacao_terras.cod_modulo
                   , atributo_utilizacao_terras.cod_cadastro
                   , atributo_utilizacao_terras.cod_atributo
                   , atributo_utilizacao_terras.valor

            $stOrder
      ";

      return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna a planta de valores de imóveis urbanos
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoPVUrbano(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY sw_logradouro.cod_logradouro, vila.valor, quadra.valor ",$boTransacao="")
    {
      $stSql = "
              SELECT municipios_iptu.cod_sefaz as codigo
                   , sw_municipio.nom_municipio as nome
                   --, '2013' as ano
                   , sw_logradouro.cod_logradouro
                   , sw_tipo_logradouro.nom_tipo as tipo_logradouro
                   , sw_nome_logradouro.nom_logradouro||' - '||confrontacao_trecho.cod_trecho as logradouro
                   , MIN(sw_cep_logradouro.num_inicial) as nro_inicial
                   , MAX(sw_cep_logradouro.num_final) as nro_final
                   , vila.valor as vila
                   , quadra.valor as quadra
                   , sw_bairro.nom_bairro as bairro
                   , trecho_valor_m2.valor_m2_territorial as valor_m2

                FROM sw_logradouro
          INNER JOIN imobiliario.trecho
                  ON trecho.cod_logradouro   = sw_logradouro.cod_logradouro
          INNER JOIN imobiliario.confrontacao_trecho
                  ON confrontacao_trecho.cod_logradouro = trecho.cod_logradouro
                 AND confrontacao_trecho.cod_trecho     = trecho.cod_trecho
                 AND confrontacao_trecho.principal      = true
          INNER JOIN sw_nome_logradouro
                  ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                 AND sw_nome_logradouro.timestamp      = (SELECT MAX(timestamp)
                                                            FROM sw_nome_logradouro t2
                                                           WHERE t2.cod_logradouro = sw_nome_logradouro.cod_logradouro)
          INNER JOIN sw_tipo_logradouro
                  ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo
          INNER JOIN sw_bairro_logradouro
                  ON sw_bairro_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
          INNER JOIN sw_bairro
                  ON sw_bairro.cod_bairro    = sw_bairro_logradouro.cod_bairro
                 AND sw_bairro.cod_municipio = sw_bairro_logradouro.cod_municipio
                 AND sw_bairro.cod_uf        = sw_bairro_logradouro.cod_uf
          INNER JOIN imobiliario.lote_localizacao
                  ON lote_localizacao.cod_lote = confrontacao_trecho.cod_lote
           LEFT JOIN imobiliario.localizacao_nivel vila
                  ON vila.cod_localizacao = lote_localizacao.cod_localizacao
                 AND vila.cod_nivel       = 1
           LEFT JOIN imobiliario.localizacao_nivel quadra
                  ON quadra.cod_localizacao = lote_localizacao.cod_localizacao
                 AND quadra.cod_nivel       = 3
           LEFT JOIN sw_cep_logradouro
                  ON sw_cep_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
           LEFT JOIN imobiliario.trecho_valor_m2
                  ON trecho_valor_m2.cod_logradouro = confrontacao_trecho.cod_logradouro
                 AND trecho_valor_m2.cod_trecho     = confrontacao_trecho.cod_trecho
                 AND trecho_valor_m2.timestamp      = (SELECT MAX(timestamp)
                                                         FROM imobiliario.trecho_valor_m2 t2
                                                        WHERE t2.cod_logradouro = trecho_valor_m2.cod_logradouro
                                                          AND t2.cod_trecho     = trecho_valor_m2.cod_trecho)
          INNER JOIN imobiliario.lote_urbano
                  ON lote_urbano.cod_lote = confrontacao_trecho.cod_lote

          --informação do município
          INNER JOIN sw_municipio
                  ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                 AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
          INNER JOIN sefazrs.municipios_iptu
                  ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                 AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

               $stFiltro

            GROUP BY municipios_iptu.cod_sefaz
                   , sw_municipio.nom_municipio
                   , sw_logradouro.cod_logradouro
                   , sw_tipo_logradouro.nom_tipo
                   , sw_nome_logradouro.nom_logradouro
                   , confrontacao_trecho.cod_trecho
                   , vila.valor
                   , quadra.valor
                   , sw_bairro.nom_bairro
                   , trecho_valor_m2.valor_m2_territorial

               $stOrder
      ";

      return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna a planta de valores de imóveis rurais
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoPVRural(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY sw_logradouro.cod_logradouro, distrito.valor, setor.valor ",$boTransacao="")
    {
      $stSql = "
              SELECT municipios_iptu.cod_sefaz as codigo
                   , sw_municipio.nom_municipio as nome
                   -- , '2013' as ano
                   , sw_logradouro.cod_logradouro
                   , setor.valor as distrito
                   , distrito.valor as localidade
                   , trecho_valor_m2.valor_m2_territorial*10000 as valor_minimo_ha
                   , trecho_valor_m2.valor_m2_territorial*10000 as valor_maximo_ha
                   , administracao.valor_padrao_desc(atributo_utilizacao_terras.cod_atributo, atributo_utilizacao_terras.cod_modulo, atributo_utilizacao_terras.cod_cadastro, atributo_utilizacao_terras.valor) as tipo_utilizacao
                   , administracao.valor_padrao_desc(atributo_topografia.cod_atributo, atributo_topografia.cod_modulo, atributo_topografia.cod_cadastro, atributo_topografia.valor) as topografia

                FROM sw_logradouro
          INNER JOIN imobiliario.trecho
                  ON trecho.cod_logradouro   = sw_logradouro.cod_logradouro
          INNER JOIN imobiliario.confrontacao_trecho
                  ON confrontacao_trecho.cod_logradouro = trecho.cod_logradouro
                 AND confrontacao_trecho.cod_trecho     = trecho.cod_trecho
                 AND confrontacao_trecho.principal      = true
          INNER JOIN imobiliario.lote_localizacao
                  ON lote_localizacao.cod_lote = confrontacao_trecho.cod_lote
           LEFT JOIN imobiliario.localizacao_nivel distrito
                  ON distrito.cod_localizacao = lote_localizacao.cod_localizacao
                 AND distrito.cod_nivel       = 1
           LEFT JOIN imobiliario.localizacao_nivel setor
                  ON setor.cod_localizacao = lote_localizacao.cod_localizacao
                 AND setor.cod_nivel       = 2
           LEFT JOIN sw_cep_logradouro
                  ON sw_cep_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
           LEFT JOIN imobiliario.trecho_valor_m2
                  ON trecho_valor_m2.cod_logradouro = confrontacao_trecho.cod_logradouro
                 AND trecho_valor_m2.cod_trecho     = confrontacao_trecho.cod_trecho
                 AND trecho_valor_m2.timestamp      = (SELECT MAX(timestamp)
                                                         FROM imobiliario.trecho_valor_m2 t2
                                                        WHERE t2.cod_logradouro = trecho_valor_m2.cod_logradouro
                                                          AND t2.cod_trecho     = trecho_valor_m2.cod_trecho)
          INNER JOIN imobiliario.lote_rural
                  ON lote_rural.cod_lote = confrontacao_trecho.cod_lote
           LEFT JOIN imobiliario.atributo_lote_rural_valor atributo_utilizacao_terras
                  ON atributo_utilizacao_terras.cod_lote     = lote_rural.cod_lote
                 AND atributo_utilizacao_terras.cod_atributo = 1007
                 AND atributo_utilizacao_terras.timestamp    = (SELECT MAX(timestamp)
                                                                  FROM imobiliario.atributo_lote_rural_valor t2
                                                                 WHERE t2.cod_modulo   = atributo_utilizacao_terras.cod_modulo
                                                                   AND t2.cod_cadastro = atributo_utilizacao_terras.cod_cadastro
                                                                   AND t2.cod_atributo = atributo_utilizacao_terras.cod_atributo
                                                                   AND t2.cod_lote     = atributo_utilizacao_terras.cod_lote)
           LEFT JOIN imobiliario.atributo_lote_rural_valor atributo_topografia
                  ON atributo_topografia.cod_lote     = lote_rural.cod_lote
                 AND atributo_topografia.cod_atributo = 1008
                 AND atributo_topografia.timestamp    = (SELECT MAX(timestamp)
                                                                  FROM imobiliario.atributo_lote_rural_valor t2
                                                                 WHERE t2.cod_modulo   = atributo_topografia.cod_modulo
                                                                   AND t2.cod_cadastro = atributo_topografia.cod_cadastro
                                                                   AND t2.cod_atributo = atributo_topografia.cod_atributo
                                                                   AND t2.cod_lote     = atributo_topografia.cod_lote)

          --informação do município
          INNER JOIN sw_municipio
                  ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                 AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
          INNER JOIN sefazrs.municipios_iptu
                  ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                 AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

               $stFiltro

            GROUP BY municipios_iptu.cod_sefaz
                   , sw_municipio.nom_municipio
                   , sw_logradouro.cod_logradouro
                   , distrito.valor
                   , setor.valor
                   , trecho_valor_m2.valor_m2_territorial
                   , atributo_utilizacao_terras.cod_modulo
                   , atributo_utilizacao_terras.cod_cadastro
                   , atributo_utilizacao_terras.cod_atributo
                   , atributo_utilizacao_terras.valor
                   , atributo_topografia.cod_modulo
                   , atributo_topografia.cod_cadastro
                   , atributo_topografia.cod_atributo
                   , atributo_topografia.valor

            $stOrder
      ";

      return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna os logradouros para exportacao
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoCadastroLogradourosLogradouro(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY sw_nome_logradouro.nom_logradouro ",$boTransacao="")
    {
      $stSql = "
              SELECT municipios_iptu.cod_sefaz as codigo
                   , sw_municipio.nom_municipio as municipio
                   , sw_logradouro.cod_logradouro
                   , sw_tipo_logradouro.nom_tipo as tipo
                   , sw_nome_logradouro.nom_logradouro as nome
                FROM sw_logradouro
          INNER JOIN sw_nome_logradouro
                  ON sw_nome_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
                 AND sw_nome_logradouro.timestamp      = (SELECT MAX(timestamp)
                                                            FROM sw_nome_logradouro t2
                                                           WHERE t2.cod_logradouro = sw_nome_logradouro.cod_logradouro)
          INNER JOIN sw_tipo_logradouro
                  ON sw_tipo_logradouro.cod_tipo = sw_nome_logradouro.cod_tipo
          INNER JOIN sw_municipio
                  ON sw_municipio.cod_municipio = sw_logradouro.cod_municipio
                 AND sw_municipio.cod_uf        = sw_logradouro.cod_uf
          INNER JOIN sefazrs.municipios_iptu
                  ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
                 AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

               $stFiltro

            GROUP BY municipios_iptu.cod_sefaz
                   , sw_municipio.nom_municipio
                   , sw_logradouro.cod_logradouro
                   , sw_tipo_logradouro.nom_tipo
                   , sw_nome_logradouro.nom_logradouro

               $stOrder

      ";

      return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }

    /**
     * Método que retorna os bairros para exportacao
     *
     * @author      Desenvolvedor   Davi Ritter Aroldi
     * @param object  $rsRecordSet
     * @param string  $stFiltro    Filtros alternativos que podem ser passados
     * @param string  $stOrder     Ordenacao do SQL
     * @param boolean $boTransacao Usar transacao
     *
     * @return object $obErro
     */
    public function listarExportacaoCadastroLogradourosBairros(&$rsRecordSet,$stFiltro="",$stOrder=" ORDER BY sw_bairro.nom_bairro ",$boTransacao="")
    {
      $stSql = "
          SELECT municipios_iptu.cod_sefaz as codigo
               , sw_municipio.nom_municipio as municipio
               , sw_bairro.nom_bairro as nome
            FROM sw_logradouro
      INNER JOIN sw_bairro_logradouro
              ON sw_bairro_logradouro.cod_logradouro = sw_logradouro.cod_logradouro
      INNER JOIN sw_bairro
              ON sw_bairro.cod_bairro    = sw_bairro_logradouro.cod_bairro
             AND sw_bairro.cod_uf        = sw_bairro_logradouro.cod_uf
             AND sw_bairro.cod_municipio = sw_bairro_logradouro.cod_municipio
      INNER JOIN sw_municipio
              ON sw_municipio.cod_municipio = sw_bairro.cod_municipio
             AND sw_municipio.cod_uf        = sw_bairro.cod_uf
      INNER JOIN sefazrs.municipios_iptu
              ON municipios_iptu.cod_municipio = sw_municipio.cod_municipio
             AND municipios_iptu.cod_uf        = sw_municipio.cod_uf

             $stFiltro

        GROUP BY municipios_iptu.cod_sefaz
               , sw_municipio.nom_municipio
               , sw_bairro.nom_bairro

          $stOrder
      ";

      return $this->executaRecuperaSql($stSql,$rsRecordSet,"","",$boTransacao);
    }
}

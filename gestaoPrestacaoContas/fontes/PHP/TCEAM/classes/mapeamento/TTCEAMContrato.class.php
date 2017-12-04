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
    * Extensão da Classe de Mapeamento
    * Data de Criação: 28/03/2011
    *
    * @author: Eduardo Paculski Schitz
    *
    $Id: TTCEAMContrato.class.php 64212 2015-12-17 12:38:12Z michel $
    *
    * @package URBEM
    *
*/
class TTCEAMContrato extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMContrato()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
            SELECT 0 AS reservado_tc
                 , num_contrato
                 , contrato_superior
                 , valor_contratado
                 , TO_CHAR(dt_assinatura, 'dd/mm/yyyy') AS dt_assinatura
                 , objetivo_contrato
                 , processo_licitatorio
                 , responsavel_juridico
                 , '01' AS moeda
                 , tipo_pessoa_contratado
                 , cpf_cnpj
                 , nome_contratado
                 , TO_CHAR(dt_vencimento, 'dd/mm/yyyy') AS dt_vencimento
                 , diario_oficial
                 , TO_CHAR(dt_publicacao, 'dd/mm/yyyy') AS dt_publicacao
                 , numero_autorizacao_sea
                 , data_autorizacao_sea
                 , numero_inss
                 , TO_CHAR(dt_emissao_inss, 'dd/mm/yyyy') AS dt_emissao_inss
                 , TO_CHAR(dt_validade_inss, 'dd/mm/yyyy') AS dt_validade_inss
                 , numero_fgts
                 , TO_CHAR(dt_emissao_fgts, 'dd/mm/yyyy') AS dt_emissao_fgts
                 , TO_CHAR(dt_validade_fgts, 'dd/mm/yyyy') AS dt_validade_fgts
                 , numero_fazenda_estadual
                 , TO_CHAR(dt_emissao_fazenda_estadual, 'dd/mm/yyyy') AS dt_emissao_fazenda_estadual
                 , TO_CHAR(dt_validade_fazenda_estadual, 'dd/mm/yyyy') AS dt_validade_fazenda_estadual
                 , '' AS reservado_caracter_tc
                 , (SELECT array_to_string( ARRAY( SELECT empenho_contrato.cod_empenho
                                                        ||empenho_contrato.exercicio
                                                     FROM empenho.empenho_contrato
                                                    WHERE empenho_contrato.num_contrato       = num_contrato_empenho
                                                      AND empenho_contrato.cod_entidade       = cod_entidade
                                                      AND empenho_contrato.exercicio_contrato = exercicio
                                                    LIMIT 1), '' ) ) AS empenho_1
                 , arquivo_texto
                 , ajuste_contrato
                 , 'S' AS recebe_valor
                 , (SELECT array_to_string( ARRAY( SELECT RPAD(empenho_contrato.cod_empenho
                                                        ||empenho_contrato.exercicio, 10, '')
                                                     FROM empenho.empenho_contrato
                                                    WHERE empenho_contrato.num_contrato       = num_contrato_empenho
                                                      AND empenho_contrato.cod_entidade       = cod_entidade
                                                      AND empenho_contrato.exercicio_contrato = exercicio
                                                   OFFSET 1
                                                    LIMIT 5), '' ) ) AS empenho_2_ate_6

                 , numero_fazenda_municipal
                 , TO_CHAR(dt_emissao_fazenda_municipal, 'dd/mm/yyyy') AS dt_emissao_fazenda_municipal
                 , TO_CHAR(dt_validade_fazenda_municipal, 'dd/mm/yyyy') AS dt_validade_fazenda_municipal
                 , numero_fazenda_nacional
                 , TO_CHAR(dt_emissao_fazenda_nacional, 'dd/mm/yyyy') AS dt_emissao_fazenda_nacional
                 , TO_CHAR(dt_validade_fazenda_nacional, 'dd/mm/yyyy') AS dt_validade_fazenda_nacional
                 , numero_outras
                 , TO_CHAR(dt_emissao_outras, 'dd/mm/yyyy') AS dt_emissao_outras
                 , TO_CHAR(dt_validade_outras, 'dd/mm/yyyy') AS dt_validade_outras
                 , (SELECT array_to_string( ARRAY( SELECT RPAD(empenho_contrato.cod_empenho
                                                        ||empenho_contrato.exercicio, 10, '')
                                                     FROM empenho.empenho_contrato
                                                    WHERE empenho_contrato.num_contrato       = num_contrato_empenho
                                                      AND empenho_contrato.cod_entidade       = cod_entidade
                                                      AND empenho_contrato.exercicio_contrato = exercicio
                                                   OFFSET 7
                                                    LIMIT 6), '' ) ) AS empenho_7_ate_12

              FROM (
                     SELECT CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'CT'||contrato.num_contrato||'-'||contrato.exercicio
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'-'||contrato.exercicio
                            END AS num_contrato
                          , contrato.num_contrato AS num_contrato_empenho
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , '' AS contrato_superior
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    contrato.valor_contratado
                                 ELSE
                                    rescisao_contrato.vlr_indenizacao
                            END AS valor_contratado
                          , contrato.dt_assinatura
                          , objeto.descricao AS objetivo_contrato
                          , CASE WHEN licitacao.cod_modalidade = 1 THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 2 THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 3 THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 4 THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 5 THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 6 THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 7 THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 8 THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 9 THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            END AS processo_licitatorio
                          , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = (SELECT valor FROM administracao.configuracao WHERE parametro = 'CGMPrefeito' AND exercicio = '".$this->getDado('exercicio')."')::integer) AS responsavel_juridico
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     1
                                 ELSE
                                     2
                            END AS tipo_pessoa_contratado
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     sw_cgm_pessoa_fisica.cpf
                                 ELSE
                                     sw_cgm_pessoa_juridica.cnpj
                            END AS cpf_cnpj
                          , sw_cgm.nom_cgm AS nome_contratado
                          , contrato.vencimento AS dt_vencimento
                          , publicacao_contrato.observacao AS diario_oficial
                          , publicacao_contrato.dt_publicacao
                          , '' AS numero_autorizacao_sea
                          , '' AS data_autorizacao_sea
                          , documento_inss.num_documento AS numero_inss
                          , documento_inss.dt_emissao AS dt_emissao_inss
                          , documento_inss.dt_validade AS dt_validade_inss
                          , documento_fgts.num_documento AS numero_fgts
                          , documento_fgts.dt_emissao AS dt_emissao_fgts
                          , documento_fgts.dt_validade AS dt_validade_fgts
                          , documento_fazenda_estadual.num_documento AS numero_fazenda_estadual
                          , documento_fazenda_estadual.dt_emissao AS dt_emissao_fazenda_estadual
                          , documento_fazenda_estadual.dt_validade AS dt_validade_fazenda_estadual
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'CT'||contrato.num_contrato||'.pdf'
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'.pdf'
                            END AS arquivo_texto
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    1
                                 ELSE
                                    5
                            END AS ajuste_contrato
                          , documento_fazenda_municipal.num_documento AS numero_fazenda_municipal
                          , documento_fazenda_municipal.dt_emissao AS dt_emissao_fazenda_municipal
                          , documento_fazenda_municipal.dt_validade AS dt_validade_fazenda_municipal
                          , documento_fazenda_nacional.num_documento AS numero_fazenda_nacional
                          , documento_fazenda_nacional.dt_emissao AS dt_emissao_fazenda_nacional
                          , documento_fazenda_nacional.dt_validade AS dt_validade_fazenda_nacional
                          , documento_outras.num_documento AS numero_outras
                          , documento_outras.dt_emissao AS dt_emissao_outras
                          , documento_outras.dt_validade AS dt_validade_outras
                       FROM licitacao.contrato
                       JOIN licitacao.contrato_licitacao
                         ON contrato_licitacao.num_contrato = contrato.num_contrato
                        AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                        AND contrato_licitacao.exercicio    = contrato.exercicio
                       JOIN licitacao.licitacao
                         ON licitacao.cod_licitacao  = contrato_licitacao.cod_licitacao
                        AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                        AND licitacao.cod_entidade   = contrato_licitacao.cod_entidade
                        AND licitacao.exercicio      = contrato_licitacao.exercicio
                       JOIN compras.objeto
                         ON objeto.cod_objeto = licitacao.cod_objeto
                       JOIN sw_cgm
                         ON sw_cgm.numcgm = contrato.cgm_contratado
                  LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                  LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                  LEFT JOIN licitacao.publicacao_contrato
                         ON publicacao_contrato.num_contrato = contrato.num_contrato
                        AND publicacao_contrato.cod_entidade = contrato.cod_entidade
                        AND publicacao_contrato.exercicio    = contrato.exercicio
                  LEFT JOIN licitacao.rescisao_contrato
                         ON rescisao_contrato.num_contrato       = contrato.num_contrato
                        AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                        AND rescisao_contrato.exercicio_contrato = contrato.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_inss
                         ON documento_inss.cod_documento  = 12
                        AND documento_inss.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_inss.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_inss.cod_modalidade = licitacao.cod_modalidade
                        AND documento_inss.cod_entidade   = licitacao.cod_entidade
                        AND documento_inss.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fgts
                         ON documento_fgts.cod_documento  = 4
                        AND documento_fgts.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fgts.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fgts.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fgts.cod_entidade   = licitacao.cod_entidade
                        AND documento_fgts.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_estadual
                         ON documento_fazenda_estadual.cod_documento  = 13
                        AND documento_fazenda_estadual.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_estadual.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_estadual.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_estadual.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_estadual.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_municipal
                         ON documento_fazenda_municipal.cod_documento  = 14
                        AND documento_fazenda_municipal.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_municipal.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_municipal.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_municipal.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_municipal.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_nacional
                         ON documento_fazenda_nacional.cod_documento  = 7
                        AND documento_fazenda_nacional.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_nacional.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_nacional.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_nacional.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_nacional.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_outras
                         ON documento_outras.cod_documento  = ( SELECT cod_documento
                                                                  FROM licitacao.participante_documentos
                                                                 WHERE cod_documento NOT IN (4, 7, 12, 13, 14)
                                                                   AND cod_licitacao  = licitacao.cod_licitacao
                                                                   AND cod_modalidade = licitacao.cod_modalidade
                                                                   AND cod_entidade   = licitacao.cod_entidade
                                                                   AND exercicio      = licitacao.exercicio
                                                                   AND cgm_fornecedor = contrato.cgm_contratado
                                                                 LIMIT 1)

                      WHERE contrato.exercicio = '".$this->getDado('exercicio')."'
                        AND to_char(contrato.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                        AND contrato.cod_entidade IN (".$this->getDado('cod_entidade').")

                  UNION ALL

                     SELECT CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'TACT'||contrato.num_contrato||'-'||contrato.exercicio
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'-'||contrato.exercicio
                            END AS num_contrato
                          , contrato.num_contrato AS num_contrato_empenho
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , CAST(contrato.num_contrato AS VARCHAR) AS contrato_superior
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    contrato_aditivos.valor_contratado
                                 ELSE
                                    rescisao_contrato.vlr_indenizacao
                            END AS valor_contratado
                          , contrato_aditivos.dt_assinatura
                          , contrato_aditivos.objeto AS objetivo_contrato
                          , CASE WHEN licitacao.cod_modalidade = 1 THEN 'CC'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 2 THEN 'TP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 3 THEN 'CO'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 4 THEN 'LE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 5 THEN 'CP'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 6 THEN 'PR'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 7 THEN 'PE'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 8 THEN 'DL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                                 WHEN licitacao.cod_modalidade = 9 THEN 'IL'||licitacao.cod_licitacao||'-'||licitacao.exercicio
                            END AS processo_licitatorio
                          , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = (SELECT valor FROM administracao.configuracao WHERE parametro = 'CGMPrefeito' AND exercicio = '".$this->getDado('exercicio')."')::integer) AS responsavel_juridico
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     1
                                 ELSE
                                     2
                            END AS tipo_pessoa_contratado
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     sw_cgm_pessoa_fisica.cpf
                                 ELSE
                                     sw_cgm_pessoa_juridica.cnpj
                            END AS cpf_cnpj
                          , sw_cgm.nom_cgm AS nome_contratado
                          , contrato_aditivos.dt_vencimento
                          , publicacao_contrato_aditivos.observacao AS diario_oficial
                          , publicacao_contrato_aditivos.dt_publicacao
                          , '' AS numero_autorizacao_sea
                          , '' AS data_autorizacao_sea
                          , documento_inss.num_documento AS numero_inss
                          , documento_inss.dt_emissao AS dt_emissao_inss
                          , documento_inss.dt_validade AS dt_validade_inss
                          , documento_fgts.num_documento AS numero_fgts
                          , documento_fgts.dt_emissao AS dt_emissao_fgts
                          , documento_fgts.dt_validade AS dt_validade_fgts
                          , documento_fazenda_estadual.num_documento AS numero_fazenda_estadual
                          , documento_fazenda_estadual.dt_emissao AS dt_emissao_fazenda_estadual
                          , documento_fazenda_estadual.dt_validade AS dt_validade_fazenda_estadual
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'TACT'||contrato_aditivos.num_aditivo||'.pdf'
                                 ELSE
                                    'TRCT'||contrato_aditivos.num_aditivo||'.pdf'
                            END AS arquivo_texto
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    2
                                 ELSE
                                    5
                            END AS ajuste_contrato
                          , documento_fazenda_municipal.num_documento AS numero_fazenda_municipal
                          , documento_fazenda_municipal.dt_emissao AS dt_emissao_fazenda_municipal
                          , documento_fazenda_municipal.dt_validade AS dt_validade_fazenda_municipal
                          , documento_fazenda_nacional.num_documento AS numero_fazenda_nacional
                          , documento_fazenda_nacional.dt_emissao AS dt_emissao_fazenda_nacional
                          , documento_fazenda_nacional.dt_validade AS dt_validade_fazenda_nacional
                          , documento_outras.num_documento AS numero_outras
                          , documento_outras.dt_emissao AS dt_emissao_outras
                          , documento_outras.dt_validade AS dt_validade_outras
                       FROM licitacao.contrato
                       JOIN licitacao.contrato_aditivos
                         ON contrato_aditivos.num_contrato       = contrato.num_contrato
                        AND contrato_aditivos.cod_entidade       = contrato.cod_entidade
                        AND contrato_aditivos.exercicio_contrato = contrato.exercicio
                       JOIN licitacao.contrato_licitacao
                         ON contrato_licitacao.num_contrato = contrato.num_contrato
                        AND contrato_licitacao.cod_entidade = contrato.cod_entidade
                        AND contrato_licitacao.exercicio    = contrato.exercicio
                       JOIN licitacao.licitacao
                         ON licitacao.cod_licitacao  = contrato_licitacao.cod_licitacao
                        AND licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
                        AND licitacao.cod_entidade   = contrato_licitacao.cod_entidade
                        AND licitacao.exercicio      = contrato_licitacao.exercicio
                       JOIN compras.objeto
                         ON objeto.cod_objeto = licitacao.cod_objeto
                       JOIN sw_cgm
                         ON sw_cgm.numcgm = contrato.cgm_contratado
                  LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                  LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                  LEFT JOIN licitacao.publicacao_contrato_aditivos
                         ON publicacao_contrato_aditivos.num_contrato       = contrato_aditivos.num_contrato
                        AND publicacao_contrato_aditivos.num_aditivo        = contrato_aditivos.num_aditivo
                        AND publicacao_contrato_aditivos.cod_entidade       = contrato_aditivos.cod_entidade
                        AND publicacao_contrato_aditivos.exercicio_contrato = contrato_aditivos.exercicio_contrato
                        AND publicacao_contrato_aditivos.exercicio          = contrato_aditivos.exercicio
                  LEFT JOIN licitacao.rescisao_contrato
                         ON rescisao_contrato.num_contrato       = contrato.num_contrato
                        AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                        AND rescisao_contrato.exercicio_contrato = contrato.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_inss
                         ON documento_inss.cod_documento  = 12
                        AND documento_inss.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_inss.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_inss.cod_modalidade = licitacao.cod_modalidade
                        AND documento_inss.cod_entidade   = licitacao.cod_entidade
                        AND documento_inss.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fgts
                         ON documento_fgts.cod_documento  = 4
                        AND documento_fgts.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fgts.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fgts.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fgts.cod_entidade   = licitacao.cod_entidade
                        AND documento_fgts.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_estadual
                         ON documento_fazenda_estadual.cod_documento  = 13
                        AND documento_fazenda_estadual.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_estadual.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_estadual.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_estadual.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_estadual.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_municipal
                         ON documento_fazenda_municipal.cod_documento  = 14
                        AND documento_fazenda_municipal.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_municipal.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_municipal.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_municipal.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_municipal.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_nacional
                         ON documento_fazenda_nacional.cod_documento  = 7
                        AND documento_fazenda_nacional.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_nacional.cod_licitacao  = licitacao.cod_licitacao
                        AND documento_fazenda_nacional.cod_modalidade = licitacao.cod_modalidade
                        AND documento_fazenda_nacional.cod_entidade   = licitacao.cod_entidade
                        AND documento_fazenda_nacional.exercicio      = licitacao.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_outras
                         ON documento_outras.cod_documento  = ( SELECT cod_documento
                                                                  FROM licitacao.participante_documentos
                                                                 WHERE cod_documento NOT IN (4, 7, 12, 13, 14)
                                                                   AND cod_licitacao  = licitacao.cod_licitacao
                                                                   AND cod_modalidade = licitacao.cod_modalidade
                                                                   AND cod_entidade   = licitacao.cod_entidade
                                                                   AND exercicio      = licitacao.exercicio
                                                                   AND cgm_fornecedor = contrato.cgm_contratado
                                                                 LIMIT 1)

                      WHERE contrato_aditivos.exercicio_contrato = '".$this->getDado('exercicio')."'
                        AND to_char(contrato_aditivos.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                        AND contrato_aditivos.cod_entidade IN (".$this->getDado('cod_entidade').")

                UNION ALL

                     SELECT CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'CT'||contrato.num_contrato||'-'||contrato.exercicio
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'-'||contrato.exercicio
                            END AS num_contrato
                          , contrato.num_contrato AS num_contrato_empenho
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , '' AS contrato_superior
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    contrato.valor_contratado
                                 ELSE
                                    rescisao_contrato.vlr_indenizacao
                            END AS valor_contratado
                          , contrato.dt_assinatura
                          , objeto.descricao AS objetivo_contrato
                          , CASE WHEN compra_direta.cod_modalidade = 1 THEN 'CC'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 2 THEN 'TP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 3 THEN 'CO'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 4 THEN 'LE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 5 THEN 'CP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 6 THEN 'PR'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 7 THEN 'PE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 8 THEN 'DL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 9 THEN 'IL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                            END AS processo_licitatorio
                          , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = (SELECT valor FROM administracao.configuracao WHERE parametro = 'CGMPrefeito' AND exercicio = '".$this->getDado('exercicio')."')::integer) AS responsavel_juridico
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     1
                                 ELSE
                                     2
                            END AS tipo_pessoa_contratado
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     sw_cgm_pessoa_fisica.cpf
                                 ELSE
                                     sw_cgm_pessoa_juridica.cnpj
                            END AS cpf_cnpj
                          , sw_cgm.nom_cgm AS nome_contratado
                          , contrato.vencimento AS dt_vencimento
                          , publicacao_contrato.observacao AS diario_oficial
                          , publicacao_contrato.dt_publicacao
                          , '' AS numero_autorizacao_sea
                          , '' AS data_autorizacao_sea
                          , documento_inss.num_documento AS numero_inss
                          , documento_inss.dt_emissao AS dt_emissao_inss
                          , documento_inss.dt_validade AS dt_validade_inss
                          , documento_fgts.num_documento AS numero_fgts
                          , documento_fgts.dt_emissao AS dt_emissao_fgts
                          , documento_fgts.dt_validade AS dt_validade_fgts
                          , documento_fazenda_estadual.num_documento AS numero_fazenda_estadual
                          , documento_fazenda_estadual.dt_emissao AS dt_emissao_fazenda_estadual
                          , documento_fazenda_estadual.dt_validade AS dt_validade_fazenda_estadual
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'CT'||contrato.num_contrato||'.pdf'
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'.pdf'
                            END AS arquivo_texto
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    1
                                 ELSE
                                    5
                            END AS ajuste_contrato
                          , documento_fazenda_municipal.num_documento AS numero_fazenda_municipal
                          , documento_fazenda_municipal.dt_emissao AS dt_emissao_fazenda_municipal
                          , documento_fazenda_municipal.dt_validade AS dt_validade_fazenda_municipal
                          , documento_fazenda_nacional.num_documento AS numero_fazenda_nacional
                          , documento_fazenda_nacional.dt_emissao AS dt_emissao_fazenda_nacional
                          , documento_fazenda_nacional.dt_validade AS dt_validade_fazenda_nacional
                          , documento_outras.num_documento AS numero_outras
                          , documento_outras.dt_emissao AS dt_emissao_outras
                          , documento_outras.dt_validade AS dt_validade_outras
                       FROM licitacao.contrato
                       JOIN licitacao.contrato_compra_direta
                         ON contrato_compra_direta.num_contrato = contrato.num_contrato
                        AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                        AND contrato_compra_direta.exercicio    = contrato.exercicio
                       JOIN compras.compra_direta
                         ON compra_direta.cod_compra_direta  = contrato_compra_direta.cod_compra_direta
                        AND compra_direta.cod_modalidade     = contrato_compra_direta.cod_modalidade
                        AND compra_direta.cod_entidade       = contrato_compra_direta.cod_entidade
                        AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio
                       JOIN compras.objeto
                         ON objeto.cod_objeto = compra_direta.cod_objeto
                       JOIN sw_cgm
                         ON sw_cgm.numcgm = contrato.cgm_contratado
                  LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                  LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                  LEFT JOIN licitacao.publicacao_contrato
                         ON publicacao_contrato.num_contrato = contrato.num_contrato
                        AND publicacao_contrato.cod_entidade = contrato.cod_entidade
                        AND publicacao_contrato.exercicio    = contrato.exercicio
                  LEFT JOIN licitacao.rescisao_contrato
                         ON rescisao_contrato.num_contrato       = contrato.num_contrato
                        AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                        AND rescisao_contrato.exercicio_contrato = contrato.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_inss
                         ON documento_inss.cod_documento  = 12
                        AND documento_inss.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_inss.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_inss.cod_entidade   = compra_direta.cod_entidade
                        AND documento_inss.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fgts
                         ON documento_fgts.cod_documento  = 4
                        AND documento_fgts.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fgts.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fgts.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fgts.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_estadual
                         ON documento_fazenda_estadual.cod_documento  = 13
                        AND documento_fazenda_estadual.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_estadual.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_estadual.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_estadual.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_municipal
                         ON documento_fazenda_municipal.cod_documento  = 14
                        AND documento_fazenda_municipal.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_municipal.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_municipal.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_municipal.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_nacional
                         ON documento_fazenda_nacional.cod_documento  = 7
                        AND documento_fazenda_nacional.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_nacional.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_nacional.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_nacional.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_outras
                         ON documento_outras.cod_documento  = ( SELECT cod_documento
                                                                  FROM licitacao.participante_documentos
                                                                 WHERE cod_documento NOT IN (4, 7, 12, 13, 14)
                                                                   AND cod_modalidade = compra_direta.cod_modalidade
                                                                   AND cod_entidade   = compra_direta.cod_entidade
                                                                   AND exercicio      = compra_direta.exercicio_entidade
                                                                   AND cgm_fornecedor = contrato.cgm_contratado
                                                                 LIMIT 1)

                      WHERE contrato.exercicio = '".$this->getDado('exercicio')."'
                        AND to_char(contrato.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                        AND contrato.cod_entidade IN (".$this->getDado('cod_entidade').")

                   GROUP BY contrato.num_contrato
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , rescisao_contrato.num_contrato
                          , contrato.valor_contratado
                          , rescisao_contrato.vlr_indenizacao
                          , contrato.dt_assinatura
                          , objeto.descricao
                          , compra_direta.cod_modalidade
                          , compra_direta.cod_compra_direta
                          , compra_direta.exercicio_entidade
                          , sw_cgm_pessoa_fisica.numcgm
                          , sw_cgm_pessoa_fisica.cpf
                          , sw_cgm_pessoa_juridica.cnpj
                          , sw_cgm.nom_cgm
                          , contrato.vencimento
                          , publicacao_contrato.observacao
                          , publicacao_contrato.dt_publicacao
                          , documento_inss.num_documento
                          , documento_inss.dt_emissao
                          , documento_inss.dt_validade
                          , documento_fgts.num_documento
                          , documento_fgts.dt_emissao
                          , documento_fgts.dt_validade
                          , documento_fazenda_estadual.num_documento
                          , documento_fazenda_estadual.dt_emissao
                          , documento_fazenda_estadual.dt_validade
                          , documento_fazenda_municipal.num_documento
                          , documento_fazenda_municipal.dt_emissao
                          , documento_fazenda_municipal.dt_validade
                          , documento_fazenda_nacional.num_documento
                          , documento_fazenda_nacional.dt_emissao
                          , documento_fazenda_nacional.dt_validade
                          , documento_outras.num_documento
                          , documento_outras.dt_emissao
                          , documento_outras.dt_validade

                UNION ALL

                     SELECT CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'TACT'||contrato.num_contrato||'-'||contrato.exercicio
                                 ELSE
                                    'TRCT'||contrato.num_contrato||'-'||contrato.exercicio
                            END AS num_contrato
                          , contrato.num_contrato AS num_contrato_empenho
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , CAST(contrato.num_contrato AS VARCHAR) AS contrato_superior
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    contrato_aditivos.valor_contratado
                                 ELSE
                                    rescisao_contrato.vlr_indenizacao
                            END AS valor_contratado
                          , contrato_aditivos.dt_assinatura
                          , contrato_aditivos.objeto AS objetivo_contrato
                          , CASE WHEN compra_direta.cod_modalidade = 1 THEN 'CC'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 2 THEN 'TP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 3 THEN 'CO'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 4 THEN 'LE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 5 THEN 'CP'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 6 THEN 'PR'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 7 THEN 'PE'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 8 THEN 'DL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                                 WHEN compra_direta.cod_modalidade = 9 THEN 'IL'||compra_direta.cod_compra_direta||'-'||compra_direta.exercicio_entidade
                            END AS processo_licitatorio
                          , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = (SELECT valor FROM administracao.configuracao WHERE parametro = 'CGMPrefeito' AND exercicio = '".$this->getDado('exercicio')."')::integer) AS responsavel_juridico
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     1
                                 ELSE
                                     2
                            END AS tipo_pessoa_contratado
                          , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                                     sw_cgm_pessoa_fisica.cpf
                                 ELSE
                                     sw_cgm_pessoa_juridica.cnpj
                            END AS cpf_cnpj
                          , sw_cgm.nom_cgm AS nome_contratado
                          , contrato_aditivos.dt_vencimento
                          , publicacao_contrato_aditivos.observacao AS diario_oficial
                          , publicacao_contrato_aditivos.dt_publicacao
                          , '' AS numero_autorizacao_sea
                          , '' AS data_autorizacao_sea
                          , documento_inss.num_documento AS numero_inss
                          , documento_inss.dt_emissao AS dt_emissao_inss
                          , documento_inss.dt_validade AS dt_validade_inss
                          , documento_fgts.num_documento AS numero_fgts
                          , documento_fgts.dt_emissao AS dt_emissao_fgts
                          , documento_fgts.dt_validade AS dt_validade_fgts
                          , documento_fazenda_estadual.num_documento AS numero_fazenda_estadual
                          , documento_fazenda_estadual.dt_emissao AS dt_emissao_fazenda_estadual
                          , documento_fazenda_estadual.dt_validade AS dt_validade_fazenda_estadual
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    'TACT'||contrato_aditivos.num_aditivo||'.pdf'
                                 ELSE
                                    'TRCT'||contrato_aditivos.num_aditivo||'.pdf'
                            END AS arquivo_texto
                          , CASE WHEN rescisao_contrato.num_contrato IS NULL THEN
                                    2
                                 ELSE
                                    5
                            END AS ajuste_contrato
                          , documento_fazenda_municipal.num_documento AS numero_fazenda_municipal
                          , documento_fazenda_municipal.dt_emissao AS dt_emissao_fazenda_municipal
                          , documento_fazenda_municipal.dt_validade AS dt_validade_fazenda_municipal
                          , documento_fazenda_nacional.num_documento AS numero_fazenda_nacional
                          , documento_fazenda_nacional.dt_emissao AS dt_emissao_fazenda_nacional
                          , documento_fazenda_nacional.dt_validade AS dt_validade_fazenda_nacional
                          , documento_outras.num_documento AS numero_outras
                          , documento_outras.dt_emissao AS dt_emissao_outras
                          , documento_outras.dt_validade AS dt_validade_outras
                       FROM licitacao.contrato
                       JOIN licitacao.contrato_aditivos
                         ON contrato_aditivos.num_contrato       = contrato.num_contrato
                        AND contrato_aditivos.cod_entidade       = contrato.cod_entidade
                        AND contrato_aditivos.exercicio_contrato = contrato.exercicio
                       JOIN licitacao.contrato_compra_direta
                         ON contrato_compra_direta.num_contrato = contrato.num_contrato
                        AND contrato_compra_direta.cod_entidade = contrato.cod_entidade
                        AND contrato_compra_direta.exercicio    = contrato.exercicio
                       JOIN compras.compra_direta
                         ON compra_direta.cod_compra_direta  = contrato_compra_direta.cod_compra_direta
                        AND compra_direta.cod_modalidade     = contrato_compra_direta.cod_modalidade
                        AND compra_direta.cod_entidade       = contrato_compra_direta.cod_entidade
                        AND compra_direta.exercicio_entidade = contrato_compra_direta.exercicio
                       JOIN compras.objeto
                         ON objeto.cod_objeto = compra_direta.cod_objeto
                       JOIN sw_cgm
                         ON sw_cgm.numcgm = contrato.cgm_contratado
                  LEFT JOIN sw_cgm_pessoa_fisica
                         ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                  LEFT JOIN sw_cgm_pessoa_juridica
                         ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                  LEFT JOIN licitacao.publicacao_contrato_aditivos
                         ON publicacao_contrato_aditivos.num_contrato       = contrato_aditivos.num_contrato
                        AND publicacao_contrato_aditivos.num_aditivo        = contrato_aditivos.num_aditivo
                        AND publicacao_contrato_aditivos.cod_entidade       = contrato_aditivos.cod_entidade
                        AND publicacao_contrato_aditivos.exercicio_contrato = contrato_aditivos.exercicio_contrato
                        AND publicacao_contrato_aditivos.exercicio          = contrato_aditivos.exercicio
                  LEFT JOIN licitacao.rescisao_contrato
                         ON rescisao_contrato.num_contrato       = contrato.num_contrato
                        AND rescisao_contrato.cod_entidade       = contrato.cod_entidade
                        AND rescisao_contrato.exercicio_contrato = contrato.exercicio
                  LEFT JOIN licitacao.participante_documentos AS documento_inss
                         ON documento_inss.cod_documento  = 12
                        AND documento_inss.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_inss.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_inss.cod_entidade   = compra_direta.cod_entidade
                        AND documento_inss.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fgts
                         ON documento_fgts.cod_documento  = 4
                        AND documento_fgts.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fgts.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fgts.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fgts.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_estadual
                         ON documento_fazenda_estadual.cod_documento  = 13
                        AND documento_fazenda_estadual.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_estadual.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_estadual.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_estadual.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_municipal
                         ON documento_fazenda_municipal.cod_documento  = 14
                        AND documento_fazenda_municipal.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_municipal.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_municipal.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_municipal.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_fazenda_nacional
                         ON documento_fazenda_nacional.cod_documento  = 7
                        AND documento_fazenda_nacional.cgm_fornecedor = contrato.cgm_contratado
                        AND documento_fazenda_nacional.cod_modalidade = compra_direta.cod_modalidade
                        AND documento_fazenda_nacional.cod_entidade   = compra_direta.cod_entidade
                        AND documento_fazenda_nacional.exercicio      = compra_direta.exercicio_entidade
                  LEFT JOIN licitacao.participante_documentos AS documento_outras
                         ON documento_outras.cod_documento  = ( SELECT cod_documento
                                                                  FROM licitacao.participante_documentos
                                                                 WHERE cod_documento NOT IN (4, 7, 12, 13, 14)
                                                                   AND cod_modalidade = compra_direta.cod_modalidade
                                                                   AND cod_entidade   = compra_direta.cod_entidade
                                                                   AND exercicio      = compra_direta.exercicio_entidade
                                                                   AND cgm_fornecedor = contrato.cgm_contratado
                                                                 LIMIT 1)

                      WHERE contrato_aditivos.exercicio_contrato = '".$this->getDado('exercicio')."'
                        AND to_char(contrato_aditivos.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                        AND contrato_aditivos.cod_entidade IN (".$this->getDado('cod_entidade').")

                   GROUP BY contrato_aditivos.num_aditivo
                          , contrato.num_contrato
                          , contrato.cod_entidade
                          , contrato.exercicio
                          , rescisao_contrato.num_contrato
                          , contrato_aditivos.valor_contratado
                          , rescisao_contrato.vlr_indenizacao
                          , contrato_aditivos.dt_assinatura
                          , contrato_aditivos.objeto
                          , compra_direta.cod_modalidade
                          , compra_direta.cod_compra_direta
                          , compra_direta.exercicio_entidade
                          , sw_cgm_pessoa_fisica.numcgm
                          , sw_cgm_pessoa_fisica.cpf
                          , sw_cgm_pessoa_juridica.cnpj
                          , sw_cgm.nom_cgm
                          , contrato_aditivos.dt_vencimento
                          , publicacao_contrato_aditivos.observacao
                          , publicacao_contrato_aditivos.dt_publicacao
                          , documento_inss.num_documento
                          , documento_inss.dt_emissao
                          , documento_inss.dt_validade
                          , documento_fgts.num_documento
                          , documento_fgts.dt_emissao
                          , documento_fgts.dt_validade
                          , documento_fazenda_estadual.num_documento
                          , documento_fazenda_estadual.dt_emissao
                          , documento_fazenda_estadual.dt_validade
                          , documento_fazenda_municipal.num_documento
                          , documento_fazenda_municipal.dt_emissao
                          , documento_fazenda_municipal.dt_validade
                          , documento_fazenda_nacional.num_documento
                          , documento_fazenda_nacional.dt_emissao
                          , documento_fazenda_nacional.dt_validade
                          , documento_outras.num_documento
                          , documento_outras.dt_emissao
                          , documento_outras.dt_validade

                 ) AS registros
        ";

        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContratoEmpenho.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContratoEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContratoEmpenho()
    {
        $stSql  = " SELECT LC.num_contrato AS num_contrato
                    , EEC.cod_empenho AS num_empenho
                    , EEC.exercicio AS ano_empenho
                    , LPAD(despesa.num_orgao::varchar, 4, '0') || LPAD(despesa.num_unidade::varchar, 2, '0') AS unidade                       
                    FROM empenho.empenho_contrato AS EEC
                    INNER JOIN licitacao.contrato AS LC
                    ON LC.num_contrato=EEC.num_contrato
                    AND LC.cod_entidade=EEC.cod_entidade
                    AND LC.exercicio=EEC.exercicio_contrato
                    INNER JOIN empenho.empenho
                    ON empenho.cod_empenho=EEC.cod_empenho
                    AND empenho.exercicio=EEC.exercicio
                    INNER JOIN empenho.pre_empenho_despesa AS EMPDESP
                    ON EMPDESP.cod_pre_empenho=empenho.cod_pre_empenho
                    AND EMPDESP.exercicio=empenho.exercicio
                    INNER JOIN orcamento.despesa
                    ON despesa.exercicio=EMPDESP.exercicio
                    AND despesa.cod_despesa=EMPDESP.cod_despesa
                    
                    WHERE EEC.exercicio_contrato='".$this->getDado('exercicio')."'
                    AND to_char(LC.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                    AND EEC.cod_entidade IN (".$this->getDado('cod_entidade').")";

        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaContratoREM.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaContratoREM(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaContratoREM().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaContratoREM()
    {
        $stSql  = "SELECT (''||contrato.num_contrato||contrato.cod_entidade||contrato.exercicio) AS nro_contrato
                        , contrato.valor_contratado AS vl_contrato
                        , TO_CHAR(contrato.dt_assinatura, 'yyyymmdd') AS dt_assinatura
                        , objeto.descricao AS objeto_contrato
                        , CASE WHEN CL.cod_licitacao IS NOT NULL THEN
                                  CASE WHEN CL.cod_modalidade = 1  THEN 'CC'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 2  THEN 'TP'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 3  THEN 'CO'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 4  THEN 'LE'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 5  THEN 'CP'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 6  THEN 'PR'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 7  THEN 'PE'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 8  THEN 'DL'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 9  THEN 'IL'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 10 THEN 'OT'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  WHEN CL.cod_modalidade = 11 THEN 'RP'||CL.cod_licitacao||'-'||CL.exercicio_licitacao
                                  END
                          ELSE
                                  CASE WHEN CCD.cod_modalidade = 1  THEN 'CC'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 2  THEN 'TP'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 3  THEN 'CO'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 4  THEN 'LE'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 5  THEN 'CP'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 6  THEN 'PR'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 7  THEN 'PE'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 8  THEN 'DL'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 9  THEN 'IL'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 10 THEN 'OT'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  WHEN CCD.cod_modalidade = 11 THEN 'RP'||CCD.cod_compra_direta||'-'||CCD.exercicio_compra_direta
                                  END
                          END AS processo_licitatorio
                        , 1 AS tipo_moeda
                        , CASE WHEN CGM_PJ.cnpj IS NOT NULL THEN 2
                          WHEN CGM_PF.cpf IS NOT NULL THEN 1
                          ELSE 3
                          END AS tipo_juridico
                        , CASE WHEN CGM_PJ.cnpj IS NOT NULL THEN CGM_PJ.cnpj
                          WHEN CGM_PF.cpf 	IS NOT NULL THEN CGM_PF.cpf
                          END AS cnpj_cpf
                        , sw_cgm.nom_cgm AS nom_contratado
                        , TO_CHAR(contrato.vencimento, 'yyyymmdd') AS dt_vencimento
                        , ( SELECT valor
                              FROM administracao.configuracao
                             WHERE cod_modulo	= 2
                               AND parametro	= 'CGMDiarioOficial'
                          ORDER BY exercicio DESC LIMIT 1
                          ) AS diario_oficial
                        , TO_CHAR(publicacao_contrato.dt_publicacao, 'yyyymmdd') AS dt_publicacao
                        , 'S' AS recebe_valor
                        , INSS.num_documento AS certificado_inss
                        , TO_CHAR(INSS.dt_emissao, 'yyyymmdd') AS dt_emissao_inss
                        , TO_CHAR(INSS.dt_validade, 'yyyymmdd') AS dt_validade_inss
                        , FGTS.num_documento AS certificado_fgts
                        , TO_CHAR(FGTS.dt_emissao, 'yyyymmdd') AS dt_emissao_fgts
                        , TO_CHAR(FGTS.dt_validade, 'yyyymmdd') AS dt_validade_fgts
                        , ESTADUAL.num_documento AS certificado_estadual
                        , TO_CHAR(ESTADUAL.dt_emissao, 'yyyymmdd') AS dt_emissao_estadual
                        , TO_CHAR(ESTADUAL.dt_validade, 'yyyymmdd') AS dt_validade_estadual
                        , MUNICIPAL.num_documento AS certificado_municipal
                        , TO_CHAR(MUNICIPAL.dt_emissao, 'yyyymmdd') AS dt_emissao_municipal
                        , TO_CHAR(MUNICIPAL.dt_validade, 'yyyymmdd') AS dt_validade_municipal
                        , FEDERAL.num_documento AS certificado_federal
                        , TO_CHAR(FEDERAL.dt_emissao, 'yyyymmdd') AS dt_emissao_federal
                        , TO_CHAR(FEDERAL.dt_validade, 'yyyymmdd') AS dt_validade_federal
                        , CNDT.num_documento AS certificado_cndt
                        , TO_CHAR(CNDT.dt_emissao, 'yyyymmdd') AS dt_emissao_cndt
                        , TO_CHAR(CNDT.dt_validade, 'yyyymmdd') AS dt_validade_cndt
                        , OUTRAS.num_documento AS certificado_outras
                        , TO_CHAR(OUTRAS.dt_emissao, 'yyyymmdd') AS dt_emissao_outras
                        , TO_CHAR(OUTRAS.dt_validade, 'yyyymmdd') AS dt_validade_outras
                        , contrato.cod_tipo_contrato AS tipo_contrato
                     FROM licitacao.contrato
               INNER JOIN licitacao.contrato_licitacao AS CL
                       ON CL.num_contrato=contrato.num_contrato
                      AND CL.cod_entidade=contrato.cod_entidade
                      AND CL.exercicio=contrato.exercicio
                LEFT JOIN licitacao.licitacao
                       ON licitacao.cod_licitacao=CL.cod_licitacao
                      AND licitacao.cod_entidade=CL.cod_entidade
                      AND licitacao.cod_modalidade=CL.cod_modalidade
                      AND licitacao.exercicio=CL.exercicio_licitacao
                LEFT JOIN licitacao.contrato_compra_direta AS CCD
                       ON CCD.num_contrato=contrato.num_contrato
                      AND CCD.cod_entidade=contrato.cod_entidade
                      AND CCD.exercicio=contrato.exercicio
                LEFT JOIN compras.compra_direta
                       ON compra_direta.cod_compra_direta=CCD.cod_compra_direta
                      AND compra_direta.cod_entidade=CCD.cod_entidade
                      AND compra_direta.cod_modalidade=CCD.cod_modalidade
                      AND compra_direta.exercicio_entidade=CCD.exercicio_compra_direta
                     JOIN compras.objeto
                       ON (objeto.cod_objeto=licitacao.cod_objeto OR objeto.cod_objeto=compra_direta.cod_objeto)
                LEFT JOIN sw_cgm_pessoa_juridica AS CGM_PJ
                       ON CGM_PJ.numcgm=contrato.cgm_contratado
                LEFT JOIN sw_cgm_pessoa_fisica AS CGM_PF
                       ON CGM_PF.numcgm=contrato.cgm_contratado
                     JOIN sw_cgm
                       ON sw_cgm.numcgm=contrato.cgm_contratado
                     JOIN licitacao.publicacao_contrato
                       ON publicacao_contrato.exercicio=contrato.exercicio
                      AND publicacao_contrato.cod_entidade=contrato.cod_entidade
                      AND publicacao_contrato.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=1
                          ) AS INSS  
                       ON INSS.exercicio=contrato.exercicio
                      AND INSS.cod_entidade=contrato.cod_entidade
                      AND INSS.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=5
                          ) AS FGTS  
                       ON FGTS.exercicio=contrato.exercicio
                      AND FGTS.cod_entidade=contrato.cod_entidade
                      AND FGTS.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=3
                          ) AS ESTADUAL  
                       ON ESTADUAL.exercicio=contrato.exercicio
                      AND ESTADUAL.cod_entidade=contrato.cod_entidade
                      AND ESTADUAL.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=4
                          ) AS MUNICIPAL  
                       ON MUNICIPAL.exercicio=contrato.exercicio
                      AND MUNICIPAL.cod_entidade=contrato.cod_entidade
                      AND MUNICIPAL.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=2
                          ) AS FEDERAL  
                       ON FEDERAL.exercicio=contrato.exercicio
                      AND FEDERAL.cod_entidade=contrato.cod_entidade
                      AND FEDERAL.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=7
                          ) AS CNDT  
                       ON CNDT.exercicio=contrato.exercicio
                      AND CNDT.cod_entidade=contrato.cod_entidade
                      AND CNDT.num_contrato=contrato.num_contrato
                LEFT JOIN ( SELECT tipo_certidao_documento.cod_tipo_certidao,contrato_documento.*
                              FROM tceam.tipo_certidao_documento
                         LEFT JOIN licitacao.contrato_documento
                                ON contrato_documento.cod_documento=tipo_certidao_documento.cod_documento
                             WHERE tipo_certidao_documento.cod_tipo_certidao=99
                          ) AS OUTRAS  
                       ON OUTRAS.exercicio=contrato.exercicio
                      AND OUTRAS.cod_entidade=contrato.cod_entidade
                      AND OUTRAS.num_contrato=contrato.num_contrato
                    
                    WHERE contrato.exercicio='".$this->getDado('exercicio')."'
                      AND to_char(contrato.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                      AND contrato.cod_entidade IN (".$this->getDado('cod_entidade').")";

        return $stSql;
    }
}
?>

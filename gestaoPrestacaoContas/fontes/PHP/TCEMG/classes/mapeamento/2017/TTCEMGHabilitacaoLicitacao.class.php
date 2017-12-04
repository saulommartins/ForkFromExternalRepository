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
    * Classe de mapeamento da tabela licitacao.participante
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoParticipante.class.php 57380 2014-02-28 17:45:35Z diogo.zarpelon $

    * Casos de uso: uc-03.05.18
            uc-03.05.19
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Thiago La Delfa Cabelleira

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTCEMGHabilitacaoLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaExportacao10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        return $this->executaRecupera("montaRecuperaExportacao10", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }
    
    public function montaRecuperaExportacao10()
    {
      $stSql = "

              SELECT 10 AS tipo_registro
                   , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao
                   , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                   , ( SELECT exercicio_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade = licitacao.cod_entidade
                          AND cod_licitacao = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio = licitacao.exercicio 
                     ) AS exercicio_licitacao
                   , ( SELECT num_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade = licitacao.cod_entidade
                          AND cod_licitacao = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio = licitacao.exercicio 
                     ) AS num_processo_licitatorio
                   , documento_cgm.tipo AS tipo_documento
                   , documento_cgm.numero AS nro_documento
                   , sw_cgm_pessoa_juridica.objeto_social AS objeto_social
                   , CASE WHEN documento_cgm.tipo = 2
                          THEN sw_cgm_pessoa_juridica.cod_orgao_registro::VARCHAR
                          ELSE ''
                      END AS orgao_resp_registro
                   , CASE WHEN documento_cgm.tipo = 2
                          THEN sw_cgm_pessoa_juridica.num_registro::VARCHAR
                          ELSE ''
                      END AS nro_registro
                   , TO_CHAR(sw_cgm_pessoa_juridica.dt_registro,'ddmmyyyy') AS dt_registro
                   , TO_CHAR(sw_cgm_pessoa_juridica.dt_registro_cvm, 'ddmmyyyy') AS dt_registro_cvm
                   , sw_cgm_pessoa_juridica.num_registro_cvm
                   , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                   , sw_uf.sigla_uf AS uf_inscricao_estadual
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_certidao_regularidade_inss
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_certidao_regularidade_inss
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_certidao_regularidade_inss
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_certidao_regularidade_fgts
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_certidao_regularidade_fgts
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_certidao_regularidade_fgts
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_cndt
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_cndt
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_cndt
                   --, TO_CHAR(participante_certificacao_licitacao.dt_registro,'ddmmyyyy') AS dt_habilitacao
                   , TO_CHAR(licitacao.timestamp,'ddmmyyyy') AS dt_habilitacao
                   , CASE WHEN participante.cgm_fornecedor::VARCHAR <> ''
                          THEN 1
                          ELSE 2
                      END AS presenca_licitantes
                   , CASE WHEN participante.renuncia_recurso = 't'
                          THEN 1
                          ELSE 2
                      END AS renuncia_recurso
                FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
          INNER JOIN licitacao.cotacao_licitacao
                  ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
          INNER JOIN licitacao.adjudicacao
                  ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                 AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                 AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                 AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                 AND adjudicacao.lote                = cotacao_licitacao.lote
                 AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                 AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                 AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                 AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                 AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                 AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                 AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                 AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                 AND homologacao.cod_item            = adjudicacao.cod_item
                 AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                 AND homologacao.lote                = adjudicacao.lote
                 AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                 AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
           LEFT JOIN licitacao.homologacao_anulada
                  ON homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                 AND homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                 AND homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                 AND homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                 AND homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                 AND homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                 AND homologacao_anulada.lote                = homologacao.lote
                 AND homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                 AND homologacao_anulada.cod_item            = homologacao.cod_item
                 AND homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                 AND homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor
           LEFT JOIN (
                      SELECT cotacao_licitacao.cod_licitacao      
                           , cotacao_licitacao.cod_modalidade     
                           , cotacao_licitacao.cod_entidade       
                           , cotacao_licitacao.exercicio_licitacao
                           , cotacao_licitacao.cgm_fornecedor
                           , participante_certificacao.num_certificacao
                           , participante_certificacao.exercicio
                           , participante_certificacao.dt_registro
                        FROM licitacao.cotacao_licitacao
                  INNER JOIN compras.cotacao_fornecedor_item
                          ON cotacao_licitacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                         AND cotacao_licitacao.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                         AND cotacao_licitacao.cod_item          = cotacao_fornecedor_item.cod_item
                         AND cotacao_licitacao.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                         AND cotacao_licitacao.lote              = cotacao_fornecedor_item.lote
                  INNER JOIN compras.julgamento_item
                          ON julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                         AND julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                         AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                         AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                         AND julgamento_item.lote           = cotacao_fornecedor_item.lote
                         AND julgamento_item.ordem          = 1
                   LEFT JOIN licitacao.participante_certificacao_licitacao
                          ON participante_certificacao_licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                         AND participante_certificacao_licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                         AND participante_certificacao_licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                         AND participante_certificacao_licitacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                         AND participante_certificacao_licitacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
                   LEFT JOIN licitacao.participante_certificacao
                          ON participante_certificacao.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND participante_certificacao.exercicio        = participante_certificacao_licitacao.exercicio_certificacao
                         AND participante_certificacao.cgm_fornecedor   = participante_certificacao_licitacao.cgm_fornecedor
                       WHERE cotacao_licitacao.cod_entidade = ".$this->getDado('entidades')."
                         AND cotacao_licitacao.exercicio_licitacao = '".$this->getDado('exercicio')."'
                    GROUP BY cotacao_licitacao.cod_licitacao      
                           , cotacao_licitacao.cod_modalidade     
                           , cotacao_licitacao.cod_entidade       
                           , cotacao_licitacao.exercicio_licitacao
                           , cotacao_licitacao.cgm_fornecedor
                           , participante_certificacao.num_certificacao
                           , participante_certificacao.exercicio
                           , participante_certificacao.dt_registro
                     ) AS participante_certificacao_licitacao
                  ON participante_certificacao_licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                 AND participante_certificacao_licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                 AND participante_certificacao_licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                 AND participante_certificacao_licitacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                 AND participante_certificacao_licitacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN sw_uf
                  ON sw_cgm.cod_uf = sw_uf.cod_uf
           LEFT JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
                 AND participante.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN (
                      SELECT numcgm
                           , cpf AS numero
                           , 1 AS tipo
                        FROM sw_cgm_pessoa_fisica
                       UNION
                      SELECT numcgm
                           , cnpj AS numero
                           , 2 AS tipo
                        FROM sw_cgm_pessoa_juridica
                     ) AS documento_cgm
                  ON documento_cgm.numcgm = sw_cgm.numcgm
           LEFT JOIN sw_cgm_pessoa_juridica
                  ON sw_cgm_pessoa_juridica.numcgm = documento_cgm.numcgm
          INNER JOIN (
                      SELECT *
                        FROM administracao.configuracao_entidade
                       WHERE configuracao_entidade.cod_modulo = 55
                         AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                         AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                     ) AS orgao
                  ON orgao.cod_entidade = licitacao.cod_entidade
                 AND orgao.exercicio    = licitacao.exercicio
               WHERE licitacao.cod_entidade IN (".$this->getDado('entidades').")
                 AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                                                                            AND TO_DATE('".$this->getDado('dataFinal')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9,10)
                 AND licitacao_anulada.cod_licitacao IS NULL
                 AND homologacao_anulada.num_homologacao IS NULL
             GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , tipo_documento
                   , nro_documento
                   , objeto_social
                   , orgao_resp_registro
                   , nro_registro
                   , sw_cgm_pessoa_juridica.dt_registro
                   , dt_registro_cvm
                   , sw_cgm_pessoa_juridica.num_registro_cvm
                   , num_inscricao_estadual
                   , uf_inscricao_estadual
                   , num_certidao_regularidade_inss
                   , dt_emissao_certidao_regularidade_inss
                   , dt_validade_certidao_regularidade_inss
                   , num_certidao_regularidade_fgts
                   , dt_emissao_certidao_regularidade_fgts
                   , dt_validade_certidao_regularidade_fgts
                   , num_cndt
                   , dt_emissao_cndt
                   , dt_validade_cndt
                   , dt_habilitacao
                   , presenca_licitantes
                   , renuncia_recurso
                   , licitacao.cod_entidade
                   , licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , licitacao.exercicio
            ORDER BY licitacao.cod_entidade
                   , licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
        ";
        return $stSql;
    }
    
    public function recuperaExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao11()
    {
        $stSql = "
            SELECT
                    11 AS tipo_registro
                  , LPAD(orgao.valor,2,'0') AS cod_orgao
                  , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                  , ( SELECT exercicio_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade = licitacao.cod_entidade
                          AND cod_licitacao = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio = licitacao.exercicio 
                     ) AS exercicio_licitacao
                   , ( SELECT num_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade = licitacao.cod_entidade
                          AND cod_licitacao = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio = licitacao.exercicio 
                     ) AS num_processo_licitatorio
                   , documento_cgm_participante.tipo AS tipo_documento     
                   , documento_cgm_participante.numero AS cnpj_empresa_hablic
                   , documento_socio.tipo_documento_socio
                   , documento_socio.num_documento_socio
                  , fornecedor_socio.cod_tipo AS tipo_participacao
             FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
          INNER JOIN licitacao.cotacao_licitacao
                  ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
          INNER JOIN licitacao.adjudicacao
                  ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                 AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                 AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                 AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                 AND adjudicacao.lote                = cotacao_licitacao.lote
                 AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                 AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                 AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                 AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                 AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                 AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                 AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                 AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                 AND homologacao.cod_item            = adjudicacao.cod_item
                 AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                 AND homologacao.lote                = adjudicacao.lote
                 AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                 AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
           LEFT JOIN licitacao.homologacao_anulada
                  ON homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                 AND homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                 AND homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                 AND homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                 AND homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                 AND homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                 AND homologacao_anulada.lote                = homologacao.lote
                 AND homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                 AND homologacao_anulada.cod_item            = homologacao.cod_item
                 AND homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                 AND homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor
          INNER JOIN licitacao.participante_certificacao_licitacao
                  ON participante_certificacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND participante_certificacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND participante_certificacao_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND participante_certificacao_licitacao.exercicio_licitacao = licitacao.exercicio
          INNER JOIN licitacao.participante_certificacao
                  ON participante_certificacao.num_certificacao = participante_certificacao_licitacao.num_certificacao
                 AND participante_certificacao.exercicio        = participante_certificacao_licitacao.exercicio_certificacao
                 AND participante_certificacao.cgm_fornecedor   = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN licitacao.certificacao_documentos
                  ON participante_certificacao.num_certificacao = certificacao_documentos.num_certificacao
                 AND participante_certificacao.exercicio        = certificacao_documentos.exercicio
                 AND participante_certificacao.cgm_fornecedor   = certificacao_documentos.cgm_fornecedor
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN sw_uf
                  ON sw_cgm.cod_uf = sw_uf.cod_uf
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
                 AND participante.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN compras.fornecedor
                  ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
          INNER JOIN compras.fornecedor_socio
                  ON fornecedor_socio.cgm_fornecedor = fornecedor.cgm_fornecedor
          INNER JOIN( 
                      SELECT numcgm
                           , cpf AS num_documento_socio
                           , 1 AS tipo_documento_socio
                        FROM sw_cgm_pessoa_fisica
                       UNION
                      SELECT numcgm
                           , cnpj AS num_documento_socio
                           , 2 AS tipo_documento_socio
                        FROM sw_cgm_pessoa_juridica
                    ) AS documento_socio
                  ON documento_socio.numcgm = fornecedor_socio.cgm_socio
          INNER JOIN (
                      
                      SELECT numcgm
                           , cnpj AS numero
                           , 2 AS tipo
                        FROM sw_cgm_pessoa_juridica
                     ) AS documento_cgm_participante
                  ON documento_cgm_participante.numcgm = sw_cgm.numcgm
          INNER JOIN (
                     SELECT *
                       FROM administracao.configuracao_entidade
                      WHERE configuracao_entidade.cod_modulo = 55
                        AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                        AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                    ) AS orgao
                  ON orgao.valor::integer = licitacao.cod_entidade
                 AND orgao.exercicio = licitacao.exercicio
               WHERE licitacao.cod_entidade IN (".$this->getDado('entidades').")
                 AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                                                                            AND TO_DATE('".$this->getDado('dataFinal')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade NOT IN (8,9,10)
                 AND licitacao_anulada.cod_licitacao IS NULL
                 AND homologacao_anulada.num_homologacao IS NULL
               GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , tipo_documento
                   , cnpj_empresa_hablic
                   , documento_socio.tipo_documento_socio
                   , documento_socio.num_documento_socio
                   , tipo_participacao
                   , licitacao.cod_entidade
                   , licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , licitacao.exercicio 
            ORDER BY num_processo_licitatorio, cnpj_empresa_hablic
        ";
        return $stSql;
    }
    
    public function recuperaExportacao20(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaExportacao20",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaExportacao20()
    {
        $stSql = "

              SELECT 20 AS tipo_registro
                   , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao
                   , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                   , ( SELECT exercicio_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade = licitacao.cod_entidade
                          AND cod_licitacao = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio = licitacao.exercicio 
                     ) AS exercicio_licitacao
                   , ( SELECT num_licitacao
                         FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
                                                           VALUES (cod_licitacao       INTEGER
                                                                  ,cod_modalidade      INTEGER
                                                                  ,cod_entidade        INTEGER
                                                                  ,exercicio           CHAR(4)
                                                                  ,exercicio_licitacao VARCHAR
                                                                  ,num_licitacao       TEXT )
                        WHERE cod_entidade   = licitacao.cod_entidade
                          AND cod_licitacao  = licitacao.cod_licitacao
                          AND cod_modalidade = licitacao.cod_modalidade
                          AND exercicio      = licitacao.exercicio 
                     ) AS num_processo_licitatorio

                   , documento_cgm.tipo AS tipo_documento
                   , documento_cgm.numero AS nro_documento
                   , TO_CHAR (participante_certificacao_licitacao.dt_registro, 'ddmmyyyy') AS dt_credenciamento

                   , CASE WHEN cotacao_licitacao.lote = 0
                          THEN NULL
                          ELSE cotacao_licitacao.lote
                      END AS num_lote
                   , cotacao_licitacao.cod_item
                   , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                   , sw_uf.sigla_uf AS uf_inscricao_estadual
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_certidao_regularidade_inss
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_certidao_regularidade_inss
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 5
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_certidao_regularidade_inss
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_certidao_regularidade_fgts
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_certidao_regularidade_fgts
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 6
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_certidao_regularidade_fgts
                   , (
                      SELECT num_documento
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS num_cndt
                   , (
                      SELECT dt_emissao
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_emissao_cndt
                   , (
                      SELECT dt_validade
                        FROM licitacao.certificacao_documentos AS l_cd
                       WHERE l_cd.cod_documento = 7
                         AND l_cd.exercicio   = participante_certificacao_licitacao.exercicio
                         AND l_cd.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
                         AND l_cd.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND l_cd.timestamp = (
                                               SELECT MAX(timestamp)
                                                 FROM licitacao.certificacao_documentos AS CD
                                                WHERE CD.cgm_fornecedor = l_cd.cgm_fornecedor
                                                  AND CD.cod_documento  = l_cd.cod_documento
                                                  AND CD.exercicio      = l_cd.exercicio
                                              )
                     ) AS dt_validade_cndt
                FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio 
          INNER JOIN licitacao.cotacao_licitacao
                  ON cotacao_licitacao.cod_licitacao       = licitacao.cod_licitacao
                 AND cotacao_licitacao.cod_modalidade      = licitacao.cod_modalidade
                 AND cotacao_licitacao.cod_entidade        = licitacao.cod_entidade
                 AND cotacao_licitacao.exercicio_licitacao = licitacao.exercicio
          INNER JOIN licitacao.adjudicacao
                  ON adjudicacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                 AND adjudicacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                 AND adjudicacao.cod_entidade        = cotacao_licitacao.cod_entidade
                 AND adjudicacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                 AND adjudicacao.lote                = cotacao_licitacao.lote
                 AND adjudicacao.cod_cotacao         = cotacao_licitacao.cod_cotacao
                 AND adjudicacao.cod_item            = cotacao_licitacao.cod_item
                 AND adjudicacao.exercicio_cotacao   = cotacao_licitacao.exercicio_cotacao
                 AND adjudicacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.num_adjudicacao     = adjudicacao.num_adjudicacao
                 AND homologacao.cod_entidade        = adjudicacao.cod_entidade
                 AND homologacao.cod_modalidade      = adjudicacao.cod_modalidade
                 AND homologacao.cod_licitacao       = adjudicacao.cod_licitacao
                 AND homologacao.exercicio_licitacao = adjudicacao.exercicio_licitacao
                 AND homologacao.cod_item            = adjudicacao.cod_item
                 AND homologacao.cod_cotacao         = adjudicacao.cod_cotacao
                 AND homologacao.lote                = adjudicacao.lote
                 AND homologacao.exercicio_cotacao   = adjudicacao.exercicio_cotacao
                 AND homologacao.cgm_fornecedor      = adjudicacao.cgm_fornecedor
           LEFT JOIN licitacao.homologacao_anulada
                  ON homologacao_anulada.num_homologacao     = homologacao.num_homologacao
                 AND homologacao_anulada.cod_licitacao       = homologacao.cod_licitacao
                 AND homologacao_anulada.cod_modalidade      = homologacao.cod_modalidade
                 AND homologacao_anulada.cod_entidade        = homologacao.cod_entidade
                 AND homologacao_anulada.num_adjudicacao     = homologacao.num_adjudicacao
                 AND homologacao_anulada.exercicio_licitacao = homologacao.exercicio_licitacao
                 AND homologacao_anulada.lote                = homologacao.lote
                 AND homologacao_anulada.cod_cotacao         = homologacao.cod_cotacao
                 AND homologacao_anulada.cod_item            = homologacao.cod_item
                 AND homologacao_anulada.exercicio_cotacao   = homologacao.exercicio_cotacao
                 AND homologacao_anulada.cgm_fornecedor      = homologacao.cgm_fornecedor

           LEFT JOIN (
                      SELECT cotacao_licitacao.cod_licitacao      
                           , cotacao_licitacao.cod_modalidade     
                           , cotacao_licitacao.cod_entidade       
                           , cotacao_licitacao.exercicio_licitacao
                           , cotacao_licitacao.cgm_fornecedor
                           , participante_certificacao.num_certificacao
                           , participante_certificacao.exercicio
                           , participante_certificacao.dt_registro
                        FROM licitacao.cotacao_licitacao
                  INNER JOIN compras.cotacao_fornecedor_item
                          ON cotacao_licitacao.exercicio_cotacao = cotacao_fornecedor_item.exercicio
                         AND cotacao_licitacao.cod_cotacao       = cotacao_fornecedor_item.cod_cotacao
                         AND cotacao_licitacao.cod_item          = cotacao_fornecedor_item.cod_item
                         AND cotacao_licitacao.cgm_fornecedor    = cotacao_fornecedor_item.cgm_fornecedor
                         AND cotacao_licitacao.lote              = cotacao_fornecedor_item.lote
                  INNER JOIN compras.julgamento_item
                          ON julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                         AND julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                         AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                         AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                         AND julgamento_item.lote           = cotacao_fornecedor_item.lote
                         AND julgamento_item.ordem          = 1
                   LEFT JOIN licitacao.participante_certificacao_licitacao
                          ON participante_certificacao_licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                         AND participante_certificacao_licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                         AND participante_certificacao_licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                         AND participante_certificacao_licitacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                         AND participante_certificacao_licitacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
                   LEFT JOIN licitacao.participante_certificacao
                          ON participante_certificacao.num_certificacao = participante_certificacao_licitacao.num_certificacao
                         AND participante_certificacao.exercicio        = participante_certificacao_licitacao.exercicio_certificacao
                         AND participante_certificacao.cgm_fornecedor   = participante_certificacao_licitacao.cgm_fornecedor

                       WHERE cotacao_licitacao.cod_entidade = ".$this->getDado('entidades')."
                         AND cotacao_licitacao.exercicio_licitacao = '".$this->getDado('exercicio')."'
                    GROUP BY cotacao_licitacao.cod_licitacao      
                           , cotacao_licitacao.cod_modalidade     
                           , cotacao_licitacao.cod_entidade       
                           , cotacao_licitacao.exercicio_licitacao
                           , cotacao_licitacao.cgm_fornecedor
                           , participante_certificacao.num_certificacao
                           , participante_certificacao.exercicio
                           , participante_certificacao.dt_registro
                     ) AS participante_certificacao_licitacao
                  ON participante_certificacao_licitacao.cod_licitacao       = cotacao_licitacao.cod_licitacao
                 AND participante_certificacao_licitacao.cod_modalidade      = cotacao_licitacao.cod_modalidade
                 AND participante_certificacao_licitacao.cod_entidade        = cotacao_licitacao.cod_entidade
                 AND participante_certificacao_licitacao.exercicio_licitacao = cotacao_licitacao.exercicio_licitacao
                 AND participante_certificacao_licitacao.cgm_fornecedor      = cotacao_licitacao.cgm_fornecedor
                            
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN sw_uf
                  ON sw_cgm.cod_uf = sw_uf.cod_uf

           LEFT JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
                 AND participante.cgm_fornecedor = participante_certificacao_licitacao.cgm_fornecedor
          INNER JOIN (
                      SELECT numcgm
                           , cpf AS numero
                           , 1 AS tipo
                        FROM sw_cgm_pessoa_fisica
                       UNION
                      SELECT numcgm
                           , cnpj AS numero
                           , 2 AS tipo
                        FROM sw_cgm_pessoa_juridica
                     ) AS documento_cgm
                  ON documento_cgm.numcgm = sw_cgm.numcgm

           LEFT JOIN sw_cgm_pessoa_juridica
                  ON sw_cgm_pessoa_juridica.numcgm = documento_cgm.numcgm

          INNER JOIN (
                      SELECT *
                        FROM administracao.configuracao_entidade
                       WHERE configuracao_entidade.cod_modulo = 55
                         AND configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                         AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                     ) AS orgao
                  ON orgao.cod_entidade = licitacao.cod_entidade
                 AND orgao.exercicio    = licitacao.exercicio

               WHERE licitacao.cod_entidade IN (".$this->getDado('entidades').")
                 AND TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                                                                            AND TO_DATE('".$this->getDado('dataFinal')."','dd/mm/yyyy')
                 AND licitacao.cod_modalidade IN (10)
                 AND licitacao_anulada.cod_licitacao IS NULL
                 AND homologacao_anulada.num_homologacao IS NULL

            ORDER BY licitacao.cod_entidade
                   , licitacao.cod_licitacao
                   , licitacao.cod_modalidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , participante_certificacao_licitacao.cgm_fornecedor
                   , cotacao_licitacao.cod_item
        ";
        return $stSql;
    }

    public function __destruct(){}
    
}//fim da classe


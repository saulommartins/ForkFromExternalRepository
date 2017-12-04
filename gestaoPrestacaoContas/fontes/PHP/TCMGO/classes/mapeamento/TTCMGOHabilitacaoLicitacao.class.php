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
    * Página de Mapeamento - HABILITAÇÃO DA LICITAÇÃO

    * Data de Criação   : 27/01/2015

    * @author Analista:      Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Arthur Cruz

    * @ignore
    * $Id: TTCMGOHabilitacaoLicitacao.class.php 63215 2015-08-04 19:42:18Z franver $
    * $Rev: $
    * $Author: $
    * $Date: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMGOHabilitacaoLicitacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }

    public function recuperaExportacao10(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montarecuperaExportacao10().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montarecuperaExportacao10()
    {   
        $stSql = "
              SELECT 10 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , documento_cgm.tipo AS tipo_documento
                   , documento_cgm.numero AS num_documento
                   , sw_cgm.nom_cgm AS nome_razao_social
                   , sw_cgm_pessoa_juridica.objeto_social
                   , sw_cgm_pessoa_juridica.cod_orgao_registro orgao_resp_registro
                   , sw_cgm_pessoa_juridica.dt_registro
                   , sw_cgm_pessoa_juridica.num_registro
                   , sw_cgm_pessoa_juridica.dt_registro_cvm
                   , sw_cgm_pessoa_juridica.num_registro_cvm
                   , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                   , sw_uf.sigla_uf AS uf_inscricao_estadual
                   , certificacao_documentos_inss.num_documento AS num_certidao_regularidade_inss
                   , certificacao_documentos_inss.dt_emissao AS dt_emissao_certidao_regularidade_inss
                   , certificacao_documentos_inss.dt_validade AS dt_validade_certidao_regularida_inss
                   , certificacao_documentos_fgts.num_documento AS num_certidao_regularidade_fgts
                   , certificacao_documentos_fgts.dt_emissao AS dt_emissao_certidao_regularidade_fgts
                   , certificacao_documentos_fgts.dt_validade AS dt_validade_certidao_regularida_fgts
                   , certificacao_documentos_cndt.num_documento AS num_cndt
                   , certificacao_documentos_cndt.dt_emissao AS dt_emissao_cndt
                   , certificacao_documentos_cndt.dt_validade AS dt_validade_cndt
                   , participante_certificacao.dt_registro AS dt_habilitacao
                   , CASE WHEN participante_documentos.cgm_fornecedor::VARCHAR <> ''
                          THEN 1
                          ELSE 2
                      END AS presenca_licitantes
                   , CASE WHEN participante.renuncia_recurso = 't'
                          THEN 1
                          ELSE 2
                      END AS renuncia_recurso
                   , CASE WHEN tipo_objeto.cod_tipo_objeto = 1
                          THEN CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 15000)
                                    THEN 2
                                    ELSE 99
                                END
                          WHEN tipo_objeto.cod_tipo_objeto = 2
                          THEN CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 8000)
                                    THEN 1
                                    ELSE 99
                                END
                          WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 3
                          WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 3
                      END AS natureza_objeto
                FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
          INNER JOIN licitacao.participante_documentos
                  ON participante_documentos.cod_licitacao  = participante.cod_licitacao
                 AND participante_documentos.cgm_fornecedor = participante.cgm_fornecedor
                 AND participante_documentos.cod_modalidade = participante.cod_modalidade
                 AND participante_documentos.cod_entidade   = participante.cod_entidade
                 AND participante_documentos.exercicio      = participante.exercicio
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante.cgm_fornecedor
          INNER JOIN sw_uf
                  ON sw_cgm.cod_uf = sw_uf.cod_uf
          INNER JOIN (SELECT numcgm
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
          INNER JOIN compras.tipo_objeto
                  ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
          INNER JOIN licitacao.participante_certificacao_licitacao
                  ON licitacao.cod_licitacao  = participante_certificacao_licitacao.cod_licitacao      
                 AND licitacao.cod_modalidade = participante_certificacao_licitacao.cod_modalidade     
                 AND licitacao.cod_entidade   = participante_certificacao_licitacao.cod_entidade       
                 AND licitacao.exercicio      = participante_certificacao_licitacao.exercicio_licitacao
          INNER JOIN licitacao.participante_certificacao
                  ON participante_certificacao.cgm_fornecedor   = participante_certificacao_licitacao.cgm_fornecedor
                 AND participante_certificacao.exercicio        = participante_certificacao_licitacao.exercicio_certificacao
                 AND participante_certificacao.num_certificacao = participante_certificacao_licitacao.num_certificacao
           LEFT JOIN (SELECT *
                        FROM licitacao.certificacao_documentos
                       WHERE certificacao_documentos.cod_documento = 9
                         AND certificacao_documentos.exercicio   = '".$this->getDado('exercicio')."'
                         AND certificacao_documentos.timestamp = (SELECT MAX(timestamp)
                                                                    FROM licitacao.certificacao_documentos AS CD
                                                                   WHERE CD.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
                                                                     AND CD.cod_documento = certificacao_documentos.cod_documento
                                                                     AND CD.exercicio   = certificacao_documentos.exercicio)
                     ) AS certificacao_documentos_inss
                  ON participante_certificacao.num_certificacao = certificacao_documentos_inss.num_certificacao
                 AND participante_certificacao.exercicio        = certificacao_documentos_inss.exercicio
                 AND participante_certificacao.cgm_fornecedor   = certificacao_documentos_inss.cgm_fornecedor
           LEFT JOIN (SELECT *
                        FROM licitacao.certificacao_documentos
                       WHERE certificacao_documentos.cod_documento = 10
                         AND certificacao_documentos.exercicio = '".$this->getDado('exercicio')."'
                         AND certificacao_documentos.timestamp = (SELECT MAX(timestamp)
                                                                    FROM licitacao.certificacao_documentos AS CD
                                                                   WHERE CD.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
                                                                     AND CD.cod_documento = certificacao_documentos.cod_documento
                                                                     AND CD.exercicio   = certificacao_documentos.exercicio)
                     )  AS certificacao_documentos_fgts
                  ON participante_certificacao.num_certificacao = certificacao_documentos_fgts.num_certificacao
                 AND participante_certificacao.exercicio        = certificacao_documentos_fgts.exercicio
                 AND participante_certificacao.cgm_fornecedor   = certificacao_documentos_fgts.cgm_fornecedor
           LEFT JOIN (SELECT *
                        FROM licitacao.certificacao_documentos
                       WHERE certificacao_documentos.cod_documento = 11
                         AND certificacao_documentos.exercicio = '".$this->getDado('exercicio')."'
                         AND certificacao_documentos.timestamp = (SELECT MAX(timestamp)
                                                                    FROM licitacao.certificacao_documentos AS CD
                                                                   WHERE CD.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
                                                                     AND CD.cod_documento = certificacao_documentos.cod_documento
                                                                     AND CD.exercicio   = certificacao_documentos.exercicio)
                     )  AS certificacao_documentos_cndt
                  ON participante_certificacao.num_certificacao = certificacao_documentos_cndt.num_certificacao
                 AND participante_certificacao.exercicio        = certificacao_documentos_cndt.exercicio
                 AND participante_certificacao.cgm_fornecedor   = certificacao_documentos_cndt.cgm_fornecedor
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                 AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                 AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                 AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                 AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
          INNER JOIN compras.mapa_item_dotacao
                  ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
                 AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                 AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                 AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                 AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                 AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                 AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                 AND mapa_item_dotacao.lote                  = mapa_item.lote
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio   = mapa_item_dotacao.exercicio
                 AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa
          INNER JOIN compras.mapa_cotacao
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
          INNER JOIN compras.julgamento
                  ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                 AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
          INNER JOIN compras.julgamento_item
                  ON julgamento_item.exercicio      = julgamento.exercicio
                 AND julgamento_item.cod_cotacao    = julgamento.cod_cotacao
                 AND julgamento_item.ordem          = 1
                 AND julgamento_item.cgm_fornecedor = participante.cgm_fornecedor
          INNER JOIN compras.cotacao_fornecedor_item
                  ON julgamento_item.exercicio      = cotacao_fornecedor_item.exercicio
                 AND julgamento_item.cod_cotacao    = cotacao_fornecedor_item.cod_cotacao
                 AND julgamento_item.cod_item       = cotacao_fornecedor_item.cod_item
                 AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                 AND julgamento_item.lote           = cotacao_fornecedor_item.lote
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND homologacao.lote                = julgamento_item.lote
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
               WHERE homologacao.timestamp BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                               AND TO_DATE('".$this->getDado('dataFinal')."', 'dd/mm/yyyy')
                 AND licitacao.exercicio = '" . $this->getDado('exercicio') . "'
                 AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                 AND participante_certificacao.num_certificacao IN (SELECT num_certificacao FROM licitacao.participante_certificacao)
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL                          
                 AND homologacao_anulada.num_homologacao IS NULL
            GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , tipo_documento
                   , documento_cgm.numero
                   , nome_razao_social
                   , uf_inscricao_estadual
                   , objeto_social
                   , orgao_resp_registro
                   , sw_cgm_pessoa_juridica.dt_registro
                   , num_registro
                   , dt_registro_cvm
                   , num_registro_cvm
                   , num_inscricao_estadual
                   , uf_inscricao_estadual
                   , num_certidao_regularidade_inss
                   , dt_emissao_certidao_regularidade_inss
                   , dt_validade_certidao_regularida_inss
                   , num_certidao_regularidade_fgts
                   , dt_emissao_certidao_regularidade_fgts
                   , dt_validade_certidao_regularida_fgts
                   , num_cndt
                   , dt_emissao_cndt
                   , dt_validade_cndt 
                   , dt_habilitacao
                   , presenca_licitantes
                   , renuncia_recurso
                   , tipo_objeto.cod_tipo_objeto
            ORDER BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
        ";
        return $stSql;
    }

    function recuperaExportacao11(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montarecuperaExportacao11().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montarecuperaExportacao11()
    {
        $stSql = "
              SELECT 11 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , sw_cgm_pessoa_juridica.cnpj AS num_cnpj
                   , documento_socio.tipo_documento_socio
                   , documento_socio.num_documento_socio
                   , fornecedor_socio.cod_tipo AS tipo_participacao
                   , documento_socio.nom_cgm AS nome_socio
                FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante.cgm_fornecedor
          INNER JOIN sw_cgm_pessoa_juridica
                  ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
          INNER JOIN compras.fornecedor
                  ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
           LEFT JOIN compras.fornecedor_socio
                  ON fornecedor_socio.cgm_fornecedor = fornecedor.cgm_fornecedor
          INNER JOIN (SELECT CASE WHEN sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                                  THEN 1
                                  WHEN sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                                  THEN 2
                              END AS tipo_documento_socio  
                           , CASE WHEN sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm
                                  THEN sw_cgm_pessoa_fisica.cpf
                                  WHEN sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm
                                  THEN sw_cgm_pessoa_juridica.cnpj
                              END AS num_documento_socio 
                           , sw_cgm.numcgm
                           , sw_cgm.nom_cgm
                        FROM sw_cgm
                   LEFT JOIN sw_cgm_pessoa_juridica
                          ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                   LEFT JOIN sw_cgm_pessoa_fisica
                          ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                     ) AS documento_socio
                  ON documento_socio.numcgm = fornecedor_socio.cgm_socio
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.mapa_cotacao
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
          INNER JOIN compras.julgamento
                  ON julgamento.exercicio   = mapa_cotacao.exercicio_cotacao
                 AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
          INNER JOIN compras.julgamento_item
                  ON julgamento_item.exercicio      = julgamento.exercicio
                 AND julgamento_item.cod_cotacao    = julgamento.cod_cotacao
                 AND julgamento_item.ordem          = 1
                 AND julgamento_item.cgm_fornecedor = participante.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND homologacao.lote                = julgamento_item.lote
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
          INNER JOIN compras.mapa_solicitacao
                  ON mapa_solicitacao.exercicio = mapa.exercicio
                 AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                 AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                 AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                 AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                 AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
          INNER JOIN compras.mapa_item_dotacao
                  ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
                 AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                 AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                 AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                 AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                 AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                 AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                 AND mapa_item_dotacao.lote                  = mapa_item.lote
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio   = mapa_item_dotacao.exercicio
                 AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa
               WHERE homologacao.timestamp BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                               AND TO_DATE('".$this->getDado('dataFinal')."', 'dd/mm/yyyy')
                 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                 AND licitacao.cod_entidade IN (".$this->getDado('entidades').")
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL                          
                 AND homologacao_anulada.num_homologacao IS NULL
            GROUP BY tipo_registro
                   , cod_orgao
                   , cod_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_cnpj
                   , documento_socio.tipo_documento_socio
                   , documento_socio.num_documento_socio
                   , tipo_participacao
                   , nome_socio
            ORDER BY num_processo_licitatorio
        ";
        return $stSql;
    }

    /** Para descobrir o cod_item referente ao num_item, deverar pesquisar atravez dessa consulta. Terá que utilizar os filtros do : mapa_item.exercicio, mapa_item.cod_mapa, mapa_item.lote.
     *     SELECT DISTINCT exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item
     *          , ROW_NUMBER() OVER(PARTITION BY exercicio, cod_mapa, lote ORDER BY exercicio, cod_mapa, lote, cod_item) AS num_item
     *       FROM compras.mapa_item
     *   ORDER BY exercicio
     *          , cod_mapa
     *          , lote
     *          , cod_item;
     */    
    function recuperaExportacao20(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaExportacao20().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montaRecuperaExportacao20()
    {
        $stSql = "
              SELECT 20 AS tipo_registro
                   , LPAD(despesa.num_orgao::VARCHAR, 2, '0') AS cod_orgao
                   , LPAD(despesa.num_unidade::VARCHAR, 2, '0') AS cod_unidade
                   , licitacao.exercicio AS exercicio_licitacao
                   , licitacao.exercicio::VARCHAR||LPAD(licitacao.cod_entidade::VARCHAR,2,'0')||LPAD(licitacao.cod_modalidade::VARCHAR,2,'0')||LPAD(licitacao.cod_licitacao::VARCHAR,4,'0') AS num_processo_licitatorio
                   , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                          THEN 1
                          ELSE 2
                      END AS tipo_documento
                   , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL
                          THEN sw_cgm_pessoa_fisica.cpf
                          ELSE sw_cgm_pessoa_juridica.cnpj
                      END AS num_documento
                   , TO_CHAR (participante.dt_inclusao, 'ddmmyyyy') AS dt_credenciamento
                   , CASE WHEN mapa.cod_tipo_licitacao = 2
                          THEN mapa_cotacao.cod_cotacao::VARCHAR
                          ELSE ' '
                      END AS num_lote
                   , ROW_NUMBER() OVER(PARTITION BY mapa_item.exercicio, mapa_item.cod_mapa, participante.cgm_fornecedor, mapa_item.lote ORDER BY mapa_item.exercicio,mapa_item.cod_mapa,mapa_item.lote,mapa_item.cod_item, participante.cgm_fornecedor) AS num_item
                   , sw_cgm.nom_cgm AS nome_razao_social
                   , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                   , sw_uf.sigla_uf AS uf_inscricao_estadual
                   , (SELECT retorno.num_documento
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 4) ' 
                                                                ) AS retorno
                     ) AS num_certidao_inss
                   , (SELECT retorno.dt_emissao
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 4) ' 
                                                                ) AS retorno
                     ) AS dt_emissao_inss
                   , (SELECT retorno.dt_validade
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 4) ' 
                                                                ) AS retorno
                     ) AS dt_validade_inss
                   , (SELECT retorno.num_documento
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 5) ' 
                                                                ) AS retorno
                     ) AS num_certidao_fgts
                   , (SELECT retorno.dt_emissao
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 5) ' 
                                                                ) AS retorno
                     ) AS dt_emissao_fgts
                   , (SELECT retorno.dt_validade
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 5) ' 
                                                                ) AS retorno
                     ) AS dt_validade_fgts
                   , (SELECT retorno.num_documento
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 6) ' 
                                                                ) AS retorno
                     ) AS num_certidao_cndt
                   , (SELECT retorno.dt_emissao
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 6) ' 
                                                                ) AS retorno
                     ) AS dt_emissao_cndt
                   , (SELECT retorno.dt_validade
                        FROM fn_recupera_participante_documentos( participante.exercicio::VARCHAR
                                                                , participante.cod_entidade::VARCHAR
                                                                , ' participante_documentos.cod_licitacao = '|| participante.cod_licitacao ||'
                                                               AND participante_documentos.cgm_fornecedor = '|| participante.cgm_fornecedor ||'
                                                               AND participante_documentos.cod_modalidade = '|| participante.cod_modalidade ||'
                                                               AND participante_documentos.cod_documento = (SELECT cod_documento FROM tcmgo.documento_de_para WHERE cod_documento_tcm = 6) ' 
                                                                ) AS retorno
                     ) AS dt_validade_cndt
                FROM licitacao.licitacao
           LEFT JOIN licitacao.licitacao_anulada
                  ON licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                 AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                 AND licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                 AND licitacao_anulada.exercicio      = licitacao.exercicio
          INNER JOIN licitacao.participante
                  ON participante.cod_licitacao  = licitacao.cod_licitacao
                 AND participante.cod_modalidade = licitacao.cod_modalidade
                 AND participante.cod_entidade   = licitacao.cod_entidade
                 AND participante.exercicio      = licitacao.exercicio
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = participante.cgm_fornecedor
          INNER JOIN sw_uf
                  ON sw_cgm.cod_uf = sw_uf.cod_uf
           LEFT JOIN sw_cgm_pessoa_fisica
                  ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
           LEFT JOIN sw_cgm_pessoa_juridica
                  ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
          INNER JOIN compras.mapa
                  ON mapa.exercicio = licitacao.exercicio_mapa
                 AND mapa.cod_mapa  = licitacao.cod_mapa
          INNER JOIN compras.mapa_cotacao
                  ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                 AND mapa.cod_mapa  = mapa_cotacao.cod_mapa
          INNER JOIN compras.cotacao
                  ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
                 AND cotacao.exercicio   = mapa_cotacao.exercicio_cotacao
          INNER JOIN compras.cotacao_item
                  ON cotacao_item.exercicio   = cotacao.exercicio
                 AND cotacao_item.cod_cotacao = cotacao.cod_cotacao
          INNER JOIN compras.julgamento
                  ON julgamento.exercicio   = cotacao.exercicio
                 AND julgamento.cod_cotacao = cotacao.cod_cotacao
          INNER JOIN compras.julgamento_item
                  ON julgamento_item.exercicio      = julgamento.exercicio
                 AND julgamento_item.cod_cotacao    = julgamento.cod_cotacao
                 AND julgamento_item.ordem          = 1
                 AND julgamento_item.cgm_fornecedor = participante.cgm_fornecedor
          INNER JOIN licitacao.homologacao
                  ON homologacao.cod_licitacao       = licitacao.cod_licitacao
                 AND homologacao.cod_modalidade      = licitacao.cod_modalidade
                 AND homologacao.cod_entidade        = licitacao.cod_entidade
                 AND homologacao.exercicio_licitacao = licitacao.exercicio
                 AND homologacao.cod_item            = julgamento_item.cod_item
                 AND homologacao.lote                = julgamento_item.lote
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
          INNER JOIN compras.mapa_item
                  ON mapa_item.exercicio = mapa.exercicio
                 AND mapa_item.cod_mapa  = mapa.cod_mapa
          INNER JOIN compras.mapa_item_dotacao
                  ON mapa_item_dotacao.exercicio             = mapa_item.exercicio
                 AND mapa_item_dotacao.cod_mapa              = mapa_item.cod_mapa
                 AND mapa_item_dotacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                 AND mapa_item_dotacao.cod_entidade          = mapa_item.cod_entidade
                 AND mapa_item_dotacao.cod_solicitacao       = mapa_item.cod_solicitacao
                 AND mapa_item_dotacao.cod_centro            = mapa_item.cod_centro
                 AND mapa_item_dotacao.cod_item              = mapa_item.cod_item
                 AND mapa_item_dotacao.lote                  = mapa_item.lote
          INNER JOIN orcamento.despesa
                  ON despesa.exercicio   = mapa_item_dotacao.exercicio
                 AND despesa.cod_despesa = mapa_item_dotacao.cod_despesa
               WHERE homologacao.timestamp BETWEEN TO_DATE('".$this->getDado('dataInicial')."', 'dd/mm/yyyy')
                                               AND TO_DATE('".$this->getDado('dataFinal')."', 'dd/mm/yyyy')
                 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                 AND licitacao.cod_entidade IN (".$this->getDado('entidades').")
                 AND licitacao.cod_modalidade NOT IN (8,9)
                 AND licitacao_anulada.cod_licitacao IS NULL
                 AND homologacao_anulada.num_homologacao IS NULL
            GROUP BY tipo_registro
                   , despesa.num_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , tipo_documento
                   , num_documento
                   , dt_credenciamento
                   , num_lote
                   , mapa_item.exercicio
                   , mapa_item.cod_mapa
                   , mapa_item.lote
                   , mapa_item.cod_item
                   , nome_razao_social
                   , num_inscricao_estadual
                   , uf_inscricao_estadual
                   , participante.exercicio
                   , participante.cod_entidade
                   , participante.cod_licitacao
                   , participante.cgm_fornecedor
                   , participante.cod_modalidade
            ORDER BY tipo_registro
                   , despesa.num_orgao
                   , despesa.num_unidade
                   , licitacao.exercicio
                   , num_processo_licitatorio
                   , num_lote
                   , num_item
        ";
        return $stSql; 
    }
    
    public function __destruct(){}

}
?>
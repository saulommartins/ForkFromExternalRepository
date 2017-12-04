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
                    SELECT
                            10 AS tipo_registro
                          , LPAD(orgao.valor::VARCHAR, 2, '0') AS cod_orgao
                          , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0')||LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                          , config_licitacao.exercicio_licitacao
						  , config_licitacao.num_licitacao AS num_processo_licitatorio
                          , documento_cgm.tipo AS tipo_documento
                          , documento_cgm.numero AS num_documento
                          , sw_cgm_pessoa_juridica.objeto_social AS objeto_social
                          , sw_cgm_pessoa_juridica.cod_orgao_registro orgao_resp_registro
                          , TO_CHAR(sw_cgm_pessoa_juridica.dt_registro,'ddmmyyyy') AS dt_registro
                          , sw_cgm_pessoa_juridica.num_registro
                          , TO_CHAR(sw_cgm_pessoa_juridica.dt_registro_cvm, 'ddmmyyyy') AS dt_registro_cvm
                          , sw_cgm_pessoa_juridica.num_registro_cvm
                          , sw_cgm_pessoa_juridica.insc_estadual AS num_inscricao_estadual
                          , sw_uf.sigla_uf AS uf_inscricao_estadual
                          , CASE WHEN certificacao_documentos.cod_documento = 5 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_certidao_regularidade_inss
                          , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_inss
                          , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_inss
                          , CASE WHEN certificacao_documentos.cod_documento = 6 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_certidao_regularidade_fgts
                          , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                          , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_certidao_regularida_fgts
                          , CASE WHEN certificacao_documentos.cod_documento = 7 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_cndt
                          , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_emissao,'dd/mm/yyyy') ELSE '' END AS dt_emissao_cndt
                          , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_validade,'dd/mm/yyyy') ELSE '' END AS dt_validade_cndt
                          , TO_CHAR(participante_certificacao.dt_registro,'ddmmyyyy') AS dt_habilitacao
                          , CASE WHEN participante.cgm_fornecedor::VARCHAR <> '' THEN 1 ELSE 2 END AS presenca_licitantes
                          , CASE WHEN participante.renuncia_recurso = 't' THEN 1 ELSE 2 END AS renuncia_recurso
                          , CASE WHEN tipo_objeto.cod_tipo_objeto = 1 THEN 
				    CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 15000) THEN 2
											  ELSE 99
					  END
                               WHEN tipo_objeto.cod_tipo_objeto = 2 THEN 
				    CASE WHEN (SUM(cotacao_fornecedor_item.vl_cotacao) > 8000) THEN 1
											  ELSE 99
					 END
                               WHEN tipo_objeto.cod_tipo_objeto = 3 THEN 3
                               WHEN tipo_objeto.cod_tipo_objeto = 4 THEN 3
                          END AS natureza_objeto
                          
                    FROM licitacao.licitacao
                    
                    JOIN licitacao.participante
                      ON participante.cod_licitacao  = licitacao.cod_licitacao
                     AND participante.cod_modalidade = licitacao.cod_modalidade
                     AND participante.cod_entidade   = licitacao.cod_entidade
                     AND participante.exercicio      = licitacao.exercicio
                     
                    JOIN sw_cgm
                      ON sw_cgm.numcgm = participante.cgm_fornecedor
                      
                     JOIN sw_uf
                       ON sw_cgm.cod_uf = sw_uf.cod_uf 
                    JOIN (SELECT
                                  numcgm
                                , cpf AS numero
                                , 1 AS tipo
                                
                            FROM sw_cgm_pessoa_fisica
                            
                            UNION
                            
                            SELECT
                                    numcgm
                                    , cnpj AS numero
                                    , 2 AS tipo
                              FROM sw_cgm_pessoa_juridica
                        ) AS documento_cgm
                      ON documento_cgm.numcgm = sw_cgm.numcgm
                      
                    JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                            WHERE configuracao_entidade.cod_modulo = 55
                              AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) AS orgao
                      ON orgao.valor::integer = licitacao.cod_entidade
                      
               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = documento_cgm.numcgm
                      
                    JOIN compras.objeto
                      ON objeto.cod_objeto = licitacao.cod_objeto
            
              INNER JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
                      
                    JOIN compras.modalidade
                      ON modalidade.cod_modalidade = licitacao.cod_modalidade
                     
                    JOIN licitacao.participante_certificacao
                      ON participante_certificacao.cgm_fornecedor = participante.cgm_fornecedor
                     AND participante_certificacao.exercicio      = participante.exercicio
                    
                    JOIN licitacao.certificacao_documentos
                      ON participante_certificacao.num_certificacao = certificacao_documentos.num_certificacao
                     AND participante_certificacao.exercicio        = certificacao_documentos.exercicio
                     AND participante_certificacao.cgm_fornecedor   = certificacao_documentos.cgm_fornecedor
                     AND certificacao_documentos.timestamp = (SELECT MAX(timestamp) from licitacao.certificacao_documentos AS CD where cgm_fornecedor = certificacao_documentos.cgm_fornecedor)
                      
                    JOIN compras.mapa
                      ON mapa.exercicio = licitacao.exercicio_mapa
                     AND mapa.cod_mapa = licitacao.cod_mapa
                      
                    JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
                     
                    JOIN compras.mapa_item
                      ON mapa_item.exercicio = mapa_solicitacao.exercicio
                     AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
                     AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                     AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
                     AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
                     
                    JOIN compras.mapa_cotacao
	              ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa = mapa_cotacao.cod_mapa
                     
              INNER JOIN compras.julgamento
                      ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
                     AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
              INNER JOIN compras.julgamento_item
                      ON  julgamento_item.exercicio = julgamento.exercicio
                     AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
                     AND julgamento_item.ordem = 1
                     
             INNER JOIN compras.cotacao_fornecedor_item
                      ON julgamento_item.exercicio = cotacao_fornecedor_item.exercicio
                     AND julgamento_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
                     AND julgamento_item.cod_item = cotacao_fornecedor_item.cod_item
                     AND julgamento_item.cgm_fornecedor = cotacao_fornecedor_item.cgm_fornecedor
                     AND julgamento_item.lote = cotacao_fornecedor_item.lote

                    JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.cod_item=julgamento_item.cod_item
                     AND homologacao.lote=julgamento_item.lote
                     AND (
                             SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                             WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                             AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                             AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                             AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                             AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                             AND homologacao.cod_item=homologacao_anulada.cod_item
                             AND homologacao.lote=homologacao_anulada.lote
                         ) IS NULL

            JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio  								 
                      
                    WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
                      AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
                      AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
                      AND participante_certificacao.num_certificacao in (SELECT num_certificacao FROM licitacao.participante_certificacao)
                      AND licitacao.cod_modalidade NOT IN (8,9)
                      AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                     AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                                     AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                                     AND licitacao_anulada.exercicio        = licitacao.exercicio )
                      
                GROUP BY tipo_registro
                      , cod_orgao
                      , cod_unidade
                      , num_processo_licitatorio
                      , tipo_documento
                      , documento_cgm.numero
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
                      , licitacao.exercicio_processo
                      , participante.cgm_fornecedor
                      , tipo_objeto.cod_tipo_objeto
					  , config_licitacao.exercicio_licitacao
					  , config_licitacao.num_licitacao
                ORDER BY  num_processo_licitatorio, num_documento
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
                  , config_licitacao.exercicio_licitacao
                  , config_licitacao.num_licitacao AS num_processo_licitatorio
                  , 2 AS tipo_documento_cnpj
                  , sw_cgm_pessoa_juridica.cnpj AS cnpj_empresa_hablic
                  , documento_socio.tipo_documento_socio
                  , documento_socio.num_documento_socio
                  , fornecedor_socio.cod_tipo AS tipo_participacao
                  
            FROM licitacao.licitacao
            
            JOIN licitacao.participante
              ON participante.cod_licitacao  = licitacao.cod_licitacao
             AND participante.cod_modalidade = licitacao.cod_modalidade
             AND participante.cod_entidade   = licitacao.cod_entidade
             AND participante.exercicio      = licitacao.exercicio
             
            JOIN sw_cgm
              ON sw_cgm.numcgm = participante.cgm_fornecedor
                      
            JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
            
	    JOIN compras.fornecedor
              ON fornecedor.cgm_fornecedor = participante.cgm_fornecedor
        
       LEFT JOIN compras.fornecedor_socio
              ON fornecedor_socio.cgm_fornecedor = fornecedor.cgm_fornecedor
	    
       LEFT JOIN( Select CASE WHEN sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm   THEN 1
			      WHEN sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm  THEN 2
	                 END AS tipo_documento_socio  
                       , CASE WHEN sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm   THEN sw_cgm_pessoa_fisica.cpf
	                      WHEN sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm  THEN sw_cgm_pessoa_juridica.cnpj
	                 END AS num_documento_socio 
	               , sw_cgm.numcgm 
                    FROM sw_cgm
               LEFT JOIN sw_cgm_pessoa_juridica
                      ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
               LEFT JOIN sw_cgm_pessoa_fisica
                      ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
               ) AS documento_socio
              ON documento_socio.numcgm = fornecedor_socio.cgm_socio
             
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
            
            JOIN compras.mapa_cotacao
	              ON mapa.exercicio = mapa_cotacao.exercicio_mapa
                     AND mapa.cod_mapa = mapa_cotacao.cod_mapa
                     
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
           
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1

            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
              
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
              
            JOIN (SELECT *
                            FROM administracao.configuracao_entidade
                           WHERE configuracao_entidade.cod_modulo = 55
                             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        ) as orgao
              ON orgao.valor::integer = licitacao.cod_entidade
             AND orgao.exercicio = licitacao.exercicio
			 
            JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio  		 
             
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND licitacao.cod_modalidade NOT IN (8,9)
              AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                              WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                              AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                              AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                              AND licitacao_anulada.exercicio        = licitacao.exercicio )
            GROUP BY 1,2,3,4,5,6,7,8,9,10
            ORDER BY num_processo_licitatorio, cnpj_empresa_hablic, num_documento_socio
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
            SELECT
                    20 AS tipo_registro
                  , LPAD(orgao.valor,2,'0') AS cod_orgao
                  , LPAD(LPAD(licitacao.num_orgao::VARCHAR, 2, '0') || LPAD(licitacao.num_unidade::VARCHAR, 2, '0'),5,'0') AS cod_unidade
                  , config_licitacao.exercicio_licitacao
                  , config_licitacao.num_licitacao AS num_processo_licitatorio
                  , documento_cgm.tipo AS tipo_documento
                  , documento_cgm.numero AS num_documento
                  , TO_CHAR (participante_certificacao.dt_registro, 'ddmmyyyy') AS dt_credenciamento
                  , CASE WHEN mapa.cod_tipo_licitacao = 2 THEN mapa_cotacao.cod_cotacao::VARCHAR
                        ELSE ' '
                    END AS num_lote
                  , julgamento_item.cod_item
                  , documento_cgm.insc_estadual AS num_inscricao_estadual
                  , sw_uf.sigla_uf AS uf_inscricao_estadual
                  , CASE WHEN certificacao_documentos.cod_documento = 5 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_certidao_regularidade_inss
                  , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_emissao,'ddmmyyyy') ELSE '' END AS dt_emissao_certidao_regularidade_inss
                  , CASE WHEN certificacao_documentos.cod_documento = 5 THEN TO_CHAR(certificacao_documentos.dt_validade,'ddmmyyyy') ELSE '' END AS dt_validade_certidao_regularida_inss
                  , CASE WHEN certificacao_documentos.cod_documento = 6 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_certidao_regularidade_fgts
                  , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_emissao,'ddmmyyyy') ELSE '' END AS dt_emissao_certidao_regularidade_fgts
                  , CASE WHEN certificacao_documentos.cod_documento = 6 THEN TO_CHAR(certificacao_documentos.dt_validade,'ddmmyyyy') ELSE '' END AS dt_validade_certidao_regularida_fgts
                  , CASE WHEN certificacao_documentos.cod_documento = 7 THEN certificacao_documentos.num_certificacao ELSE NULL END AS num_cndt
                  , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_emissao,'ddmmyyyy') ELSE '' END AS dt_emissao_cndt
                  , CASE WHEN certificacao_documentos.cod_documento = 7 THEN TO_CHAR(certificacao_documentos.dt_validade,'ddmmyyyy') ELSE '' END AS dt_validade_cndt
                  
            FROM licitacao.licitacao
                    
            JOIN licitacao.participante
              ON participante.cod_licitacao  = licitacao.cod_licitacao
             AND participante.cod_modalidade = licitacao.cod_modalidade
             AND participante.cod_entidade   = licitacao.cod_entidade
             AND participante.exercicio      = licitacao.exercicio
             
            JOIN sw_cgm
              ON sw_cgm.numcgm = participante.cgm_fornecedor
            
            JOIN sw_uf
              ON sw_cgm.cod_uf = sw_uf.cod_uf
              
            JOIN (SELECT
                          numcgm
                        , cpf AS numero
                        , 1 AS tipo
                        , '' AS insc_estadual
                        
                    FROM sw_cgm_pessoa_fisica
                    
                   UNION
                    
                  SELECT
                            numcgm
                          , cnpj AS numero
                          , 2 AS tipo
                          , '' AS insc_estadual
                          
                    FROM sw_cgm_pessoa_juridica
                    
                ) AS documento_cgm
              ON documento_cgm.numcgm = sw_cgm.numcgm
              
            JOIN (SELECT *
                    FROM administracao.configuracao_entidade
                   WHERE configuracao_entidade.cod_modulo = 55
                     AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'
                ) as orgao
              ON orgao.valor::integer = licitacao.cod_entidade
             AND orgao.exercicio = licitacao.exercicio
              
            JOIN compras.objeto
              ON objeto.cod_objeto = licitacao.cod_objeto
              
            JOIN compras.modalidade
              ON modalidade.cod_modalidade = licitacao.cod_modalidade
              
            JOIN licitacao.licitacao_documentos
              ON licitacao_documentos.cod_licitacao  = licitacao.cod_licitacao
             AND licitacao_documentos.cod_modalidade = licitacao.cod_modalidade
             AND licitacao_documentos.cod_entidade   = licitacao.cod_entidade
             AND licitacao_documentos.exercicio      = licitacao.exercicio
        
           JOIN licitacao.documento
              ON documento.cod_documento = licitacao_documentos.cod_documento
              
            JOIN licitacao.certificacao_documentos
              ON certificacao_documentos.cod_documento = documento.cod_documento
              
            JOIN licitacao.participante_certificacao
              ON participante_certificacao.num_certificacao = certificacao_documentos.num_certificacao
             AND participante_certificacao.exercicio = certificacao_documentos.exercicio
             AND participante_certificacao.cgm_fornecedor = certificacao_documentos.cgm_fornecedor
              
            JOIN compras.mapa
              ON mapa.exercicio = licitacao.exercicio_mapa
             AND mapa.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.mapa_cotacao
              ON mapa.exercicio = mapa_cotacao.exercicio_mapa
             AND mapa.cod_mapa = mapa_cotacao.cod_mapa
              
      INNER JOIN compras.julgamento
              ON julgamento.exercicio = mapa_cotacao.exercicio_cotacao
             AND julgamento.cod_cotacao = mapa_cotacao.cod_cotacao
     
      INNER JOIN compras.julgamento_item
              ON  julgamento_item.exercicio = julgamento.exercicio
             AND julgamento_item.cod_cotacao = julgamento.cod_cotacao
             AND julgamento_item.ordem = 1
             AND julgamento_item.cgm_fornecedor = participante.cgm_fornecedor
     
            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
             AND homologacao.cod_item=julgamento_item.cod_item
             AND homologacao.lote=julgamento_item.lote
             AND (
                     SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                     WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                     AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                     AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                     AND homologacao.cod_item=homologacao_anulada.cod_item
                     AND homologacao.lote=homologacao_anulada.lote
                 ) IS NULL
              
            JOIN compras.mapa_solicitacao
              ON mapa_solicitacao.exercicio = mapa.exercicio
             AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
             
            JOIN compras.mapa_item
              ON mapa_item.exercicio = mapa_solicitacao.exercicio
             AND mapa_item.cod_entidade = mapa_solicitacao.cod_entidade
             AND mapa_item.cod_solicitacao = mapa_solicitacao.cod_solicitacao
             AND mapa_item.cod_mapa = mapa_solicitacao.cod_mapa
             AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao
			 
            JOIN (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('".$this->getDado('exercicio')."', '".$this->getDado('entidades')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao
              ON config_licitacao.cod_entidade = licitacao.cod_entidade
             AND config_licitacao.cod_licitacao = licitacao.cod_licitacao
             AND config_licitacao.cod_modalidade = licitacao.cod_modalidade
             AND config_licitacao.exercicio = licitacao.exercicio   			 
              
            WHERE TO_DATE(TO_CHAR(homologacao.timestamp,'dd/mm/yyyy'), 'dd/mm/yyyy') BETWEEN TO_DATE('01/" . $this->getDado('mes') . "/" . $this->getDado('exercicio') . "', 'dd/mm/yyyy')
              AND last_day(TO_DATE('" . $this->getDado('exercicio') . "' || '-' || '".$this->getDado('mes') . "' || '-' || '01','yyyy-mm-dd'))
              AND licitacao.cod_entidade IN (" . $this->getDado('entidades') . ")
              AND participante_certificacao.num_certificacao IN (SELECT num_certificacao FROM licitacao.participante_certificacao)
              AND licitacao.cod_modalidade = 10
              AND licitacao.cod_modalidade NOT IN (8,9)
              AND NOT EXISTS( SELECT 1 FROM licitacao.licitacao_anulada
                              WHERE licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                              AND licitacao_anulada.cod_modalidade   = licitacao.cod_modalidade
                              AND licitacao_anulada.cod_entidade     = licitacao.cod_entidade
                              AND licitacao_anulada.exercicio        = licitacao.exercicio )
               GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21

              ORDER BY num_processo_licitatorio, cod_item
        ";

        return $stSql;
    }

	public function __destruct(){}
    
}//fim da classe

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
    * Data de Criação: 15/04/2014

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Lisiane Morais

    $Id: TTCEMGRespLic.class.php 61907 2015-03-13 16:49:31Z michel $

   
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGRespLic extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEMGRespLic()
    {
        parent::Persistente();
        $this->setTabela('tcemg.resplic');
        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_entidade, cod_modalidade, cod_licitacao');

        $this->AddCampo('exercicio'                     , 'varchar',  true,     '4',  true, false);
        $this->AddCampo('cod_entidade'                  , 'integer',  true,     '',   true, false);
        $this->AddCampo('cod_modalidade'                , 'integer',  true,     '',   true, false);
        $this->AddCampo('cod_licitacao'                 , 'integer',  true,     '',   true, false);

        $this->AddCampo('cgm_resp_abertura_licitacao'   , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_edital'               , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_recurso_orcamentario' , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_conducao_licitacao'   , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_homologacao'          , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_adjudicacao'          , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_publicacao'           , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_avaliacao_bens'       , 'integer',  false,    '',   false, false);
        $this->AddCampo('cgm_resp_pesquisa'             , 'integer',  false,    '',   false, false);
    }
    
    public function recuperaResponsavel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaResponsavel($stFiltro).$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
      //  return $this->executaRecupera("montaRecuperaResponsaveis",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaResponsavel($stFiltro)
    {            
        $stSql = "
            SELECT nom_cgm
            FROM sw_cgm
           WHERE sw_cgm.numcgm = ".$stFiltro.";";

        return $stSql;
    }
    
    function recuperaResponsaveisLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaResponsaveisLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montaRecuperaResponsaveisLicitacao()
    {
        $stSql =
                 "SELECT 10 as tipo_registro
                       , LPAD(''||orgao_sicom.valor,2, '0') as cod_orgao
                       , LPAD((LPAD(''||licitacao.num_orgao,2, '0')||LPAD(''||licitacao.num_unidade,2, '0')), 5, '0') AS codunidadesub
                       , config_licitacao.exercicio_licitacao
                       , config_licitacao.num_licitacao AS num_processo_licitatorio
                       , dadosResponsaveis.tipo_responsabilidade
                       , dadosResponsaveis.cpf

                    FROM licitacao.licitacao	

              INNER JOIN tcemg.resplic
                      ON tcemg.resplic.exercicio      = licitacao.exercicio
                     AND tcemg.resplic.cod_licitacao  = licitacao.cod_licitacao
                     AND tcemg.resplic.cod_modalidade = licitacao.cod_modalidade
                     AND tcemg.resplic.cod_entidade   = licitacao.cod_entidade
		      
              INNER JOIN (
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 1 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_abertura_licitacao
                           
                       UNION ALL                        

                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 2 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_edital

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 3 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_pesquisa

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 4 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_recurso_orcamentario

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 5 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_conducao_licitacao

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 6 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm = resplic.cgm_resp_homologacao
                        UNION ALL
                     
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 7 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_adjudicacao

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 8 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_publicacao

                       UNION ALL
                       
                            SELECT sw_cgm_pessoa_fisica.cpf
                               , 9 AS tipo_responsabilidade
                               , resplic.exercicio
                               , resplic.cod_entidade
                               , resplic.cod_modalidade
                               , resplic.cod_licitacao
                           FROM tcemg.resplic
                     INNER JOIN sw_cgm_pessoa_fisica
                             ON sw_cgm_pessoa_fisica.numcgm =  resplic.cgm_resp_avaliacao_bens
                        ) AS dadosResponsaveis
		       ON tcemg.resplic.exercicio      = dadosResponsaveis.exercicio
                      AND tcemg.resplic.cod_licitacao  = dadosResponsaveis.cod_licitacao
                      AND tcemg.resplic.cod_modalidade = dadosResponsaveis.cod_modalidade
                      AND tcemg.resplic.cod_entidade   = dadosResponsaveis.cod_entidade
            
            JOIN compras.mapa_cotacao
              ON mapa_cotacao.exercicio_mapa = licitacao.exercicio
             AND mapa_cotacao.cod_mapa = licitacao.cod_mapa
             
            JOIN compras.cotacao
              ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
             AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
            JOIN compras.julgamento
              ON julgamento.exercicio = cotacao.exercicio
             AND julgamento.cod_cotacao = cotacao.cod_cotacao
            
            JOIN licitacao.homologacao
              ON homologacao.cod_licitacao=licitacao.cod_licitacao
             AND homologacao.cod_modalidade=licitacao.cod_modalidade
             AND homologacao.cod_entidade=licitacao.cod_entidade
             AND homologacao.exercicio_licitacao=licitacao.exercicio
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

                    
      INNER JOIN (SELECT valor::integer 
                        , configuracao_entidade.exercicio
                        , configuracao_entidade.cod_entidade
                    FROM tcemg.orgao 
              INNER JOIN administracao.configuracao_entidade
                      ON configuracao_entidade.valor::integer = orgao.num_orgao   
                   WHERE configuracao_entidade.cod_entidade IN (" . $this->getDado('entidades') . ")  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                )  AS orgao_sicom
              ON orgao_sicom.exercicio='".Sessao::getExercicio()."'
             AND orgao_sicom.cod_entidade = licitacao.cod_entidade

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
             AND NOT EXISTS( SELECT 1
                             FROM licitacao.licitacao_anulada
                             WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                               AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                               AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                               AND licitacao_anulada.exercicio = licitacao.exercicio
                         )
                         
        GROUP BY 1,2,3,4,5,6,7
        ORDER BY num_processo_licitatorio";
        return $stSql;
    }
    
    function recuperaComissaoLicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaComissaoLicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "" );
    }

    function montaRecuperaComissaoLicitacao()
    {
        $stSql = " SELECT 20 as tipo_registro
                        , LPAD(''||orgao_sicom.valor,2, '0') as cod_orgao
                        , LPAD((LPAD(''||licitacao.num_orgao,2, '0')||LPAD(''||licitacao.num_unidade,2, '0')), 5, '0') AS codunidadesub
                        , config_licitacao.exercicio_licitacao
                        , config_licitacao.num_licitacao AS num_processo_licitatorio
                        , CASE WHEN comissao.cod_tipo_comissao = 1 THEN 2
                               WHEN comissao.cod_tipo_comissao = 2 THEN 1
                               WHEN comissao.cod_tipo_comissao = 3 THEN 2
                          END AS cod_tipo_comissao
                        , CASE WHEN (norma.cod_tipo_norma = 4) THEN 
                                        1
                               ELSE 
                                        2
                          END AS cod_tipo_norma
                        , norma.num_norma
                        , to_char(norma.dt_assinatura,'ddmmyyyy') as dt_nomeacao
                        , to_char(norma.dt_publicacao,'ddmmyyyy') as ini_vigencia
                        , to_char(norma_data_termino.dt_termino,'ddmmyyyy') as fim_vigencia
                        , tipo_membro.cpf as cpf_membro
                        , tipo_membro.cod_tipo_membro as cod_atribuicao
                        , CASE WHEN (comissao_membros.numcgm = tipo_membro.numcgm) THEN 
                                        comissao_membros.cargo
                               WHEN (membro_adicional.numcgm = tipo_membro.numcgm) THEN 
                                        membro_adicional.cargo
                          END AS cargo
                        , CASE WHEN (comissao_membros.numcgm = tipo_membro.numcgm) THEN 
                                        comissao_membros.natureza_cargo
                               WHEN (membro_adicional.numcgm = tipo_membro.numcgm) THEN 
                                        membro_adicional.natureza_cargo
                          END AS natureza_cargo
                    FROM licitacao.licitacao	
			
		    JOIN licitacao.comissao_licitacao
                      ON comissao_licitacao.exercicio = licitacao.exercicio
                     AND comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
                     AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
                     AND comissao_licitacao.cod_entidade = licitacao.cod_entidade
		    JOIN licitacao.comissao
                      ON comissao.cod_comissao = comissao_licitacao.cod_comissao
                     AND ( comissao.cod_tipo_comissao = 1
                           OR comissao.cod_tipo_comissao = 2
                           OR comissao.cod_tipo_comissao = 3 )
                    JOIN normas.norma
                      ON norma.cod_norma = comissao.cod_norma
                     AND ( norma.cod_tipo_norma = 2
                           OR norma.cod_tipo_norma = 4 )
	            JOIN normas.norma_data_termino
	              ON norma_data_termino.cod_norma = norma.cod_norma
		    JOIN licitacao.comissao_membros
	              ON comissao_licitacao.cod_comissao = comissao_membros.cod_comissao
	       LEFT JOIN licitacao.membro_adicional
                      ON membro_adicional.exercicio = licitacao.exercicio
                     AND membro_adicional.cod_licitacao = licitacao.cod_licitacao
                     AND membro_adicional.cod_modalidade = licitacao.cod_modalidade
                     AND membro_adicional.cod_entidade = licitacao.cod_entidade
		    JOIN (SELECT  pf.numcgm
				, pf.CPF
				, CASE  WHEN (membro_adicional.numcgm = pf.numcgm) THEN 2
		           		WHEN (comissao_membros.cod_tipo_membro = 1) THEN 2
					WHEN (comissao_membros.cod_tipo_membro = 2) THEN 3
					WHEN (comissao_membros.cod_tipo_membro = 3) THEN 6
				  END AS cod_tipo_membro
		  	    FROM licitacao.licitacao
		  	    JOIN licitacao.comissao_licitacao --COD_COMISSAO
		  	      ON comissao_licitacao.exercicio = licitacao.exercicio
		  	     AND comissao_licitacao.cod_licitacao = licitacao.cod_licitacao
		  	     AND comissao_licitacao.cod_modalidade = licitacao.cod_modalidade
		  	     AND comissao_licitacao.cod_entidade = licitacao.cod_entidade
		  	    JOIN licitacao.comissao_membros
		  	      ON comissao_licitacao.cod_comissao = comissao_membros.cod_comissao
		       LEFT JOIN licitacao.membro_adicional
		  	      ON membro_adicional.exercicio = licitacao.exercicio
		  	     AND membro_adicional.cod_licitacao = licitacao.cod_licitacao
		  	     AND membro_adicional.cod_modalidade = licitacao.cod_modalidade
		  	     AND membro_adicional.cod_entidade = licitacao.cod_entidade
		       LEFT JOIN sw_cgm_pessoa_fisica as pf
		  	      ON pf.numcgm = comissao_membros.numcgm
		  	      OR pf.numcgm = membro_adicional.numcgm
		        GROUP BY 1,2,3
                        ) AS tipo_membro
		      ON comissao_membros.numcgm = tipo_membro.numcgm
		      OR membro_adicional.numcgm = tipo_membro.numcgm
                      
                    JOIN compras.mapa_cotacao
                      ON mapa_cotacao.exercicio_mapa = licitacao.exercicio
                     AND mapa_cotacao.cod_mapa = licitacao.cod_mapa
                     
                    JOIN compras.cotacao
                      ON cotacao.exercicio = mapa_cotacao.exercicio_cotacao
                     AND cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
            
                    JOIN compras.julgamento
                      ON julgamento.exercicio = cotacao.exercicio
                     AND julgamento.cod_cotacao = cotacao.cod_cotacao
                    
                    JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
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
                              
                    JOIN (  SELECT valor::integer 
                                 , configuracao_entidade.exercicio
                                 , configuracao_entidade.cod_entidade
                              FROM tcemg.orgao 
                        INNER JOIN administracao.configuracao_entidade
                                ON configuracao_entidade.valor::integer = orgao.num_orgao   
                             WHERE configuracao_entidade.cod_entidade IN (" . $this->getDado('entidades') . ")  AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
                        )  AS orgao_sicom
                      ON orgao_sicom.exercicio='".Sessao::getExercicio()."'
                     AND orgao_sicom.cod_entidade = licitacao.cod_entidade
                     
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
                     AND NOT EXISTS( SELECT 1
                                     FROM licitacao.licitacao_anulada
                                     WHERE licitacao_anulada.cod_licitacao = licitacao.cod_licitacao
                                         AND licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                         AND licitacao_anulada.cod_entidade = licitacao.cod_entidade
                                         AND licitacao_anulada.exercicio = licitacao.exercicio
                                 )
                    AND NOT EXISTS( SELECT 1
                                     FROM licitacao.membro_excluido
                                     WHERE membro_excluido.numcgm = tipo_membro.numcgm
                                 )
                  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15
                  ";
        return $stSql;
    }
    
    public function __destruct(){}

}
?>
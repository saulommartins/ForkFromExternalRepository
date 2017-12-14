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
    * Data de Criação: 11/04/2011
    *
    *
    * @author: Eduardo Paculski Schitz
    *
    * @package URBEM
    *
*/
class TTCEAMConvenio extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEAMConvenio()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaTodos()
    {
        $stSql  = "
                  SELECT *

                    FROM ( SELECT recebe_valor.recebe_valor
                                , convenio.num_convenio
                                , convenio.exercicio
                                , convenio.valor AS vl_convenio
                                , '001' AS moeda
                                , TO_CHAR(convenio.dt_assinatura, 'yyyymmdd') AS dt_assinatura
                                , objeto.descricao AS objeto
                                , TO_CHAR(convenio.dt_vigencia, 'yyyymmdd') AS dt_vencimento
                                , norma.num_norma AS lei_autorizativa
                                , TO_CHAR(norma.dt_assinatura, 'yyyymmdd') AS dt_lei_autorizativa
                                , publicacao_convenio.num_publicacao AS diario_oficial
                                , TO_CHAR(publicacao_convenio.dt_publicacao, 'yyyymmdd') AS dt_publicacao
                                , convenio.cod_tipo_convenio 
                
                             FROM licitacao.convenio
                             JOIN compras.objeto
                               ON objeto.cod_objeto = convenio.cod_objeto
                        LEFT JOIN tceam.esfera_convenio
                               ON esfera_convenio.num_convenio = convenio.num_convenio
                              AND esfera_convenio.exercicio    = convenio.exercicio
                        LEFT JOIN licitacao.publicacao_convenio
                               ON publicacao_convenio.num_convenio = convenio.num_convenio
                              AND publicacao_convenio.exercicio    = convenio.exercicio
                        LEFT JOIN licitacao.convenio_anulado
                               ON convenio_anulado.num_convenio = convenio.num_convenio
                              AND convenio_anulado.exercicio    = convenio.exercicio
	        	LEFT JOIN ( SELECT cgm_fornecedor
                                         , valor_participacao
                                         , entidade.numcgm
                                         , CASE WHEN (valor_participacao> 0) THEN 'S' 
                                                 ELSE 'N' 
	        			    END as recebe_valor 
	        			 , participante_convenio.num_convenio
	        		      FROM licitacao.participante_convenio 
	        		 LEFT JOIN orcamento.entidade 
	        		        ON participante_convenio.cgm_fornecedor = entidade.numcgm 
	        		     WHERE entidade.numcgm IS NULL 
	        		       AND valor_participacao > 0
	        		) AS recebe_valor
	        	       ON recebe_valor.num_convenio = convenio.num_convenio
                            JOIN normas.norma
		              ON norma.cod_norma = convenio.cod_norma_autorizativa
                            WHERE convenio.exercicio = '".$this->getDado('exercicio')."'
                              AND to_char(convenio.dt_assinatura,'mm') = '".$this->getDado('mes')."'
                
                        GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13

            UNION ALL

                          SELECT recebe_valor.recebe_valor
                               , convenio_aditivos.num_aditivo AS num_convenio
                               , convenio.exercicio
                               , convenio_aditivos.valor_convenio AS vl_convenio
                               , '001' AS moeda
                               , TO_CHAR(convenio_aditivos.dt_assinatura, 'yyyymmdd') AS dt_assinatura
                               , convenio_aditivos.objeto
                               , TO_CHAR(convenio_aditivos.dt_vigencia, 'yyyymmdd') AS dt_vencimento
                               , norma.num_norma AS lei_autorizativa
                               , TO_CHAR(norma.dt_assinatura, 'yyyymmdd') AS dt_lei_autorizativa
                               , convenio_aditivos_publicacao.num_publicacao AS diario_oficial
                               , TO_CHAR(convenio_aditivos_publicacao.dt_publicacao, 'yyyymmdd') AS dt_publicacao
                               , convenio.cod_tipo_convenio
                   
                            FROM licitacao.convenio
                            JOIN licitacao.convenio_aditivos
                              ON convenio_aditivos.num_convenio = convenio.num_convenio
                             AND convenio_aditivos.exercicio    = convenio.exercicio
                       LEFT JOIN licitacao.convenio_aditivos_publicacao
                              ON convenio_aditivos_publicacao.num_convenio = convenio.num_convenio
                             AND convenio_aditivos_publicacao.exercicio    = convenio.exercicio
                       LEFT JOIN licitacao.convenio_aditivos_anulacao
                              ON convenio_aditivos_anulacao.num_convenio = convenio.num_convenio
                             AND convenio_aditivos_anulacao.exercicio    = convenio.exercicio
                       LEFT JOIN licitacao.convenio_anulado
                              ON convenio_anulado.num_convenio = convenio.num_convenio
                             AND convenio_anulado.exercicio    = convenio.exercicio
		       LEFT JOIN ( SELECT cgm_fornecedor
				        , valor_participacao
				        , entidade.numcgm
				        , CASE WHEN (valor_participacao> 0) THEN 'S' 
                                               ELSE 'N' 
				           END as recebe_valor 
				        , participante_convenio.num_convenio
			            FROM licitacao.participante_convenio 
			       LEFT JOIN orcamento.entidade 
			              ON participante_convenio.cgm_fornecedor = entidade.numcgm 
			           WHERE entidade.numcgm IS NULL 
			             AND valor_participacao > 0
			        ) AS recebe_valor
			       ON recebe_valor.num_convenio = convenio.num_convenio
                             JOIN normas.norma
		               ON norma.cod_norma = convenio_aditivos.cod_norma_autorizativa
                       WHERE convenio.exercicio = '".$this->getDado('exercicio')."'
                         AND to_char(convenio.dt_assinatura,'mm') = '".$this->getDado('mes')."'

		    GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13
                 ) AS registros
        ";
        return $stSql;
    }
    
    public function recuperaConvenioEmpenho(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaConvenioEmpenho",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaConvenioEmpenho()
    {
        $stSql = "
        SELECT    convenio.num_convenio AS num_convenio
                 , empenho.cod_empenho AS num_nota_empenho
                 , empenho.exercicio AS ano_empenho
                 , lpad(despesa.num_orgao::varchar, 4, '0')||lpad(despesa.num_unidade::varchar, 2, '0') AS cod_unidade_orcamentaria
                 
            FROM licitacao.convenio
            
            JOIN empenho.empenho_convenio
              ON empenho_convenio.exercicio = convenio.exercicio
             AND empenho_convenio.num_convenio = convenio.num_convenio
             
            JOIN empenho.empenho
              ON empenho.exercicio = empenho_convenio.exercicio
             AND empenho.cod_entidade = empenho_convenio.cod_entidade
             AND empenho.cod_empenho = empenho_convenio.cod_empenho
             
            JOIN empenho.pre_empenho
              ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
             AND pre_empenho.exercicio = empenho.exercicio
             
            JOIN empenho.pre_empenho_despesa
              ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
             AND pre_empenho_despesa.exercicio = pre_empenho.exercicio
             
            JOIN orcamento.despesa
              ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
             AND despesa.exercicio = pre_empenho_despesa.exercicio
            
           WHERE convenio.exercicio = '".$this->getDado('exercicio')."'
             AND to_char(convenio.dt_assinatura,'mm') = '".$this->getDado('mes')."'
             AND empenho.cod_entidade IN (".$this->getDado('cod_entidade').")
        ";
        return $stSql;
    }
    
}
?>

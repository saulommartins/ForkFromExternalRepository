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
    * Extensão da Classe de Mapeamento TTCEALLdoMetasFiscaisAnexoI.class.php
    *
    * Data de Criação: 02/07/2014
    *
    * @author: Arthur Cruz
    *
    $Id: TTCEALLdoMetasFiscaisAnexoI.class.php 59612 2014-09-02 12:00:51Z gelson $
    *
*/
class TTCEALLdoMetasFiscaisAnexoI extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCEALLdoMetasFiscaisAnexoI()
    {
        parent::Persistente();
        $this->setDado('exercicio',Sessao::getExercicio());
    }
    
    /**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montarecuperaPPA.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
    */
    public function recuperaLdoMetasFiscaisAnexoI(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaLdoMetasFiscaisAnexoI().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaLdoMetasFiscaisAnexoI()
    {
        
	$stSql  = "       SELECT (SELECT PJ.cnpj
				    FROM orcamento.entidade
				    JOIN sw_cgm
				      ON sw_cgm.numcgm=entidade.numcgm
				    JOIN sw_cgm_pessoa_juridica AS PJ
				      ON sw_cgm.numcgm=PJ.numcgm
				   WHERE entidade.exercicio    = '".$this->getDado('exercicio')."'
                                     AND entidade.cod_entidade = ".$this->getDado('und_gestora')."
			       ) AS cod_und_gestora
			     , (SELECT CASE WHEN valor = ''
                                                    THEN '0000'
                                                    ELSE valor
                                                    END AS valor 
				  FROM administracao.configuracao_entidade
				 WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao_entidade.cod_entidade = ".$this->getDado('und_gestora')."
				   AND configuracao_entidade.cod_modulo = 62
				   AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
			       ) AS codigo_ua
			     , ".$this->getDado('bimestre')." AS bimestre
			     , '".$this->getDado('exercicio')."' AS exercicio 
			     , homologacao.cod_norma as num_ldo
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_metas_receitas_anuais'
			       ) AS metas_receitas_anuais
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_receitas_primarias'
				) AS receitas_primarias
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_metas_despesas_anuais'
				) AS metas_despesas_anuais
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_despesas_primarias'
				) AS despesas_primarias
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_resultado_primario'
				) AS resultado_primario
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'resultado_nominal'
				) AS resultado_nominal
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_divida_publica_consolidada'
				) AS divida_publica_consolidada
			     , (SELECT CASE WHEN valor = ''
                                                    THEN 0.00::numeric(14,2)
                                                    ELSE REPLACE(valor, ',', '.')::numeric(14,2) 
                                                    END AS valor 
				  FROM administracao.configuracao
				 WHERE configuracao.cod_modulo = 62
				   AND configuracao.exercicio = '".$this->getDado('exercicio')."'
				   AND configuracao.parametro like 'tceal_config_divida_publica_liquida'        
				) AS divida_consolidada_liquida
		          FROM ldo.homologacao
			 WHERE TO_CHAR(homologacao.timestamp,'yyyy') = '".$this->getDado('exercicio')."'
			   AND TO_CHAR(homologacao.timestamp,'dd/mm/yyyy') BETWEEN '".$this->getDado('dtInicial')."' AND '".$this->getDado('dtFinal')."'
                        GROUP BY cod_und_gestora, codigo_ua, bimestre, exercicio, num_ldo, metas_receitas_anuais, receitas_primarias, metas_despesas_anuais, despesas_primarias,
                                 resultado_primario, resultado_nominal, divida_publica_consolidada, divida_consolidada_liquida";
        return $stSql;
    }
}
?>

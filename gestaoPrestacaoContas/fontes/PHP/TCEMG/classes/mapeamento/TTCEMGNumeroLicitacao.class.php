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
    * Classe de mapeamento da função tcemg.fn_exercicio_numero_licitacao
    * Data de Criação: 17/03/2015

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Michel Teixeira

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGNumeroLicitacao.class.php 61947 2015-03-18 13:35:37Z michel $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );


class TTCEMGNumeroLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

    public function recuperaNumeroLicitacaoHomologado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;    
    
        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaNumeroLicitacaoHomologado().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaNumeroLicitacaoHomologado()
    {
        $stSql = "
             SELECT  config_licitacao.cod_licitacao
				  ,  config_licitacao.cod_modalidade
				  ,  config_licitacao.cod_entidade
				  ,  config_licitacao.exercicio
				  ,  config_licitacao.exercicio_licitacao
				  ,  CASE WHEN config_licitacao.num_licitacao = (config_licitacao.exercicio::varchar||LPAD(''||config_licitacao.cod_entidade::varchar,2, '0')||LPAD(''||config_licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||config_licitacao.cod_licitacao::varchar,4, '0'))::text THEN
									config_licitacao.cod_licitacao::TEXT
						  ELSE
									config_licitacao.num_licitacao
					 END AS num_licitacao
			      ,  CASE WHEN config_licitacao.num_licitacao = (config_licitacao.exercicio::varchar||LPAD(''||config_licitacao.cod_entidade::varchar,2, '0')||LPAD(''||config_licitacao.cod_modalidade::varchar,2, '0')||LPAD(''||config_licitacao.cod_licitacao::varchar,4, '0'))::text THEN
                                    config_licitacao.cod_licitacao::NUMERIC
                          ELSE
                                    config_licitacao.num_licitacao::NUMERIC
                     END AS num_licitacao_2

              FROM  (
                     SELECT * FROM tcemg.fn_exercicio_numero_licitacao ('', '".$this->getDado('cod_entidade')."')
																VALUES (cod_licitacao		INTEGER
																	   ,cod_modalidade		INTEGER
																	   ,cod_entidade		INTEGER
																	   ,exercicio			CHAR(4)
																	   ,exercicio_licitacao	VARCHAR
																	   ,num_licitacao		TEXT ) 
                 ) AS config_licitacao

        INNER JOIN  licitacao.licitacao
                ON  licitacao.cod_licitacao = config_licitacao.cod_licitacao
               AND  licitacao.cod_modalidade = config_licitacao.cod_modalidade
               AND  licitacao.cod_entidade = config_licitacao.cod_entidade
               AND  licitacao.exercicio = config_licitacao.exercicio

        INNER JOIN (       SELECT cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                                , MAX(num_homologacao) AS num_homologacao
                                , MAX(timestamp) AS timestamp
                                , MAX(cod_item) AS cod_item
                             FROM licitacao.homologacao
                         GROUP BY cod_licitacao
                                , cod_modalidade
                                , cod_entidade
                                , exercicio_licitacao
                          ) AS homologacao
                ON  homologacao.cod_licitacao       = licitacao.cod_licitacao
               AND  homologacao.cod_modalidade      = licitacao.cod_modalidade
               AND  homologacao.cod_entidade        = licitacao.cod_entidade
               AND  homologacao.exercicio_licitacao = licitacao.exercicio
               AND  (      SELECT homologacao_anulada.num_homologacao
                             FROM licitacao.homologacao_anulada
							WHERE homologacao_anulada.cod_licitacao       = licitacao.cod_licitacao
							  AND homologacao_anulada.cod_modalidade      = licitacao.cod_modalidade
							  AND homologacao_anulada.cod_entidade        = licitacao.cod_entidade
							  AND homologacao_anulada.exercicio_licitacao = licitacao.exercicio
							  AND homologacao.num_homologacao             = homologacao_anulada.num_homologacao
							  AND homologacao.cod_item                    = homologacao_anulada.cod_item
								) IS NULL
            
             WHERE  1=1
			 AND licitacao.cod_entidade IN (".$this->getDado('cod_entidade').")
			 AND licitacao.cod_modalidade IN (".$this->getDado('cod_modalidade').")
			 AND licitacao.exercicio = '".$this->getDado('exercicio')."'
	    ORDER BY config_licitacao.exercicio_licitacao, num_licitacao_2";
        
        return $stSql;
    }
    
    public function __destruct(){}
    
}
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
    * Data de Criação: 13/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25841 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-05 10:02:21 -0300 (Sex, 05 Out 2007) $

    * Casos de uso: uc-03.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioBemBaixado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TPatrimonioBemBaixado()
    {
        parent::Persistente();
        $this->setTabela('patrimonio.bem_baixado');
        $this->setCampoCod('cod_bem');
        $this->AddCampo('cod_bem'    ,'integer' ,true ,'' ,true  ,true );
        $this->AddCampo('dt_baixa'   ,'date'    ,true ,'' ,false ,false);
        $this->AddCampo('motivo'     ,'text'    ,true ,'' ,false ,false);
        $this->AddCampo('tipo_baixa' ,'integer' ,true ,'' ,false ,false);
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  bem.cod_bem
                 ,  bem.cod_natureza
                 ,  bem.cod_grupo
                 ,  bem.cod_especie
                 ,  bem.descricao
                 ,  TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
                 ,  bem_baixado.motivo
                 ,  CASE WHEN ( bem_baixado.cod_bem IS NOT NULL )
                         THEN 'baixado'
                         ELSE NULL
                    END AS status
                , natureza.nom_natureza
                , tipo_natureza.codigo
                , tipo_natureza.descricao AS descricao_natureza
              
              FROM  patrimonio.bem
         
         LEFT JOIN  patrimonio.bem_baixado
                ON  bem_baixado.cod_bem = bem.cod_bem
        
        INNER JOIN patrimonio.especie
	            ON especie.cod_especie  = bem.cod_especie
	           AND especie.cod_grupo    = bem.cod_grupo
	           AND especie.cod_natureza = bem.cod_natureza

        INNER JOIN patrimonio.grupo
	            ON grupo.cod_grupo    = especie.cod_grupo
	           AND grupo.cod_natureza = especie.cod_natureza
       
        INNER JOIN patrimonio.natureza
	            ON natureza.cod_natureza = grupo.cod_natureza

	    INNER JOIN patrimonio.tipo_natureza
                ON tipo_natureza.codigo = natureza.cod_tipo 

             WHERE ";
        if ( $this->getDado( 'cod_bem' ) ) {
            $stSql.= " bem.cod_bem = ".$this->getDado('cod_bem')."  AND  ";
        }

        return substr($stSql,0,-6);
    }
    
    public function recuperaRelacionamentoLancamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoLancamento().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaRelacionamentoLancamento()
    {
        $stSql = " SELECT bem.cod_bem
                        , natureza.cod_tipo
                        , natureza.cod_tipo
                        , natureza.cod_natureza              
                        , natureza.nom_natureza
                        , grupo.cod_grupo
                        , grupo.nom_grupo
                        , bem_baixado.tipo_baixa
                        , bem_baixado.dt_baixa
                        , lancamento_baixa_patrimonio.exercicio
                        , lancamento_baixa_patrimonio.estorno
                     
                     FROM patrimonio.bem
           
               INNER JOIN patrimonio.especie
                       ON especie.cod_natureza = bem.cod_natureza
                      AND especie.cod_grupo    = bem.cod_grupo
                      AND especie.cod_especie  = bem.cod_especie
           
               INNER JOIN patrimonio.grupo
                       ON grupo.cod_natureza = especie.cod_natureza
                      AND grupo.cod_grupo    = especie.cod_grupo
           
               INNER JOIN patrimonio.natureza
                       ON natureza.cod_natureza = grupo.cod_natureza
           
               INNER JOIN patrimonio.bem_baixado
                       ON bem_baixado.cod_bem = bem.cod_bem
                
                LEFT JOIN (
                            SELECT lancamento_baixa_patrimonio.cod_bem
                                 , lancamento_baixa_patrimonio.exercicio
                                 , lancamento_baixa_patrimonio.estorno
                                 , lancamento_baixa_patrimonio.timestamp

                              FROM contabilidade.lancamento_baixa_patrimonio

                        INNER JOIN patrimonio.bem
                                ON bem.cod_bem = lancamento_baixa_patrimonio.cod_bem

                             WHERE lancamento_baixa_patrimonio.timestamp = ( 
                                                                             SELECT MAX(lancamento_baixa.timestamp) AS timestamp
                                                                               FROM contabilidade.lancamento_baixa_patrimonio AS lancamento_baixa
                                                                              WHERE lancamento_baixa_patrimonio.cod_bem   = lancamento_baixa.cod_bem
                                                                                AND lancamento_baixa_patrimonio.exercicio = lancamento_baixa.exercicio
                                                                            )
				
                          ) AS lancamento_baixa_patrimonio
                       ON lancamento_baixa_patrimonio.cod_bem = bem.cod_bem ";
        return $stSql;
    }
	
	public function recuperaBemBaixadoResumo(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBemBaixadoResumo().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaBemBaixadoResumo()
    {
        $stSql = "
			   SELECT TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa 
					, bem_baixado.motivo
					, bem_baixado.tipo_baixa
					, tipo_baixa.cod_tipo || ' - ' || tipo_baixa.descricao AS descricao_baixa
					, TRIM(REPLACE(REPLACE(REPLACE(TO_CHAR( somatorio_bem_baixado.valor_atualizado , '9G999G990D99'),',','*' ),'.',','),'*','.')) AS valor_atualizado
					
				FROM patrimonio.bem_baixado
	   
		   INNER JOIN (
				   
				   SELECT bem_baixado.dt_baixa
						, bem_baixado.motivo
						, bem_baixado.tipo_baixa
						, SUM ((
						   SELECT vl_atualizado
							 FROM patrimonio.fn_depreciacao_acumulada(bem_baixado.cod_bem)
						   AS retorno (  cod_bem            INTEGER
								   , vl_acumulado       NUMERIC
								   , vl_atualizado      NUMERIC
								   , vl_bem             NUMERIC
								   , min_competencia    VARCHAR
								   , max_competencia    VARCHAR
								  )
					   WHERE retorno.cod_bem = bem_baixado.cod_bem
					   )) AS valor_atualizado
	   
					 FROM patrimonio.bem_baixado
	   
				GROUP BY bem_baixado.dt_baixa
				       , bem_baixado.motivo
				       , bem_baixado.tipo_baixa			  
	   
				) AS somatorio_bem_baixado
			  ON somatorio_bem_baixado.dt_baixa   = bem_baixado.dt_baixa
			 AND somatorio_bem_baixado.motivo     = bem_baixado.motivo
			 AND somatorio_bem_baixado.tipo_baixa = bem_baixado.tipo_baixa
	   
	  INNER JOIN patrimonio.tipo_baixa  
			  ON tipo_baixa.cod_tipo = bem_baixado.tipo_baixa
		";
		
        return $stSql;
    }
	
	public function recuperaBemBaixadoGeral(&$rsRecordSet, $stFiltro = "", $stGrupo = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBemBaixadoGeral().$stFiltro.$stGrupo.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaBemBaixadoGeral()
    {
        $stSql = "
			SELECT bem.cod_bem
                 , bem.descricao
                 , TO_CHAR(bem_baixado.dt_baixa,'dd/mm/yyyy') AS dt_baixa
                 , bem_baixado.motivo
                 , natureza.nom_natureza
                 , tipo_natureza.codigo
                 , tipo_natureza.descricao AS descricao_natureza
                 , tipo_baixa.cod_tipo || ' - ' || tipo_baixa.descricao AS descricao_baixa
                 , TRIM(REPLACE(REPLACE(REPLACE(TO_CHAR(
                   ( SELECT vl_atualizado
                       FROM patrimonio.fn_depreciacao_acumulada(bem.cod_bem)
                         AS retorno (  cod_bem            INTEGER
                                    , vl_acumulado       NUMERIC
                                    , vl_atualizado      NUMERIC
                                    , vl_bem             NUMERIC
                                    , min_competencia    VARCHAR
                                    , max_competencia    VARCHAR
                                   )
                       WHERE retorno.cod_bem = bem.cod_bem
                    ), '9G999G990D99'),',','*' ),'.',','),'*','.'))AS valor_atualizado
              
              FROM  patrimonio.bem
         
        INNER JOIN  patrimonio.bem_baixado
                ON  bem_baixado.cod_bem = bem.cod_bem

        INNER JOIN patrimonio.tipo_baixa
                ON tipo_baixa.cod_tipo = bem_baixado.tipo_baixa
        
        INNER JOIN patrimonio.especie
                ON especie.cod_especie  = bem.cod_especie
               AND especie.cod_grupo    = bem.cod_grupo
               AND especie.cod_natureza = bem.cod_natureza

        INNER JOIN patrimonio.grupo
                ON grupo.cod_grupo    = especie.cod_grupo
               AND grupo.cod_natureza = especie.cod_natureza
       
        INNER JOIN patrimonio.natureza
                ON natureza.cod_natureza = grupo.cod_natureza

        INNER JOIN patrimonio.tipo_natureza
                ON tipo_natureza.codigo = natureza.cod_tipo 
		";
		
        return $stSql;
    }

}

?>
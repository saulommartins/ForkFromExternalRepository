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
/*
 * Classe de mapeamento

 * @package Urbem
 * @subpackage Mapeamento

 * @author Arthur Cruz

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCEALOcorrenciasFuncionais extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEALOcorrenciasFuncionais()
    {
        parent::Persistente();
        $this->setDado('stExercicio', Sessao::getExercicio());
    }

    public function recuperaOcorrenciasFuncionais(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)){
            $stOrdem = (strpos($stOrdem,"ORDER BY") === false) ? " ORDER BY ".$stOrdem : $stOrdem;
        }
        
        $stSql = $this->montaRecuperaOcorrenciasFuncionais().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaOcorrenciasFuncionais()
    {
        
	$stSql = "
		SELECT (SELECT PJ.cnpj
			  FROM orcamento.entidade
		    INNER JOIN sw_cgm_pessoa_juridica as PJ
			    ON PJ.numcgm = entidade.numcgm
		         WHERE entidade.exercicio = '".$this->getDado('stExercicio')."'
			   AND entidade.cod_entidade = ".$this->getDado('entidade')."
		       ) AS cod_und_gestora
	             , ( SELECT CASE WHEN configuracao_entidade.valor != ''
			        THEN configuracao_entidade.valor
			        ELSE '0000'
			        END AS valor
		           FROM administracao.configuracao_entidade
		          WHERE configuracao_entidade.exercicio = '".$this->getDado('stExercicio')."'
		            AND configuracao_entidade.cod_modulo = 62
		            AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
		            AND configuracao_entidade.cod_entidade = ".$this->getDado('entidade')."
	                ) AS codigo_ua
		     , ".$this->getDado('bimestre')." AS bimestre
                     , '".$this->getDado('stExercicio')."' AS exercicio
	             , cpf 
	             , matricula
	             , cod_ocorrencia
	             , informacao_complementar
	             , data_inicio_ocorrencia
	             , data_termino_ocorrencia
	      FROM (
	      
		SELECT sw_cgm_pessoa_fisica.cpf
		     , contrato.registro AS matricula
		     , CASE WHEN assentamento_assentamento.cod_motivo = 5 OR assentamento_assentamento.cod_motivo = 6 THEN '11'
			    WHEN assentamento_assentamento.cod_motivo = 7 THEN '09'
		       ELSE LPAD(ocorrencia_funcional.cod_ocorrencia::VARCHAR ,2 ,'0') 
		       END AS cod_ocorrencia
		     , assentamento_gerado.observacao AS informacao_complementar
		     , TO_CHAR(assentamento_gerado.periodo_inicial, 'DD/MM/YYYY') AS data_inicio_ocorrencia
		     , TO_CHAR(assentamento_gerado.periodo_final, 'DD/MM/YYYY') AS data_termino_ocorrencia
		  FROM pessoal".$this->getDado('stEntidade').".servidor
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".servidor_contrato_servidor
		    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor
		    ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".contrato
		    ON contrato_servidor.cod_contrato = contrato.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".assentamento_gerado_contrato_servidor
		    ON contrato.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".assentamento_gerado
		    ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".assentamento_assentamento
		    ON assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
	
	     LEFT JOIN pessoal".$this->getDado('stEntidade').".assentamento_gerado_excluido
		    ON assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado
		   AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp
	
	     INNER JOIN tceal.ocorrencia_funcional_assentamento
		    ON assentamento_assentamento.cod_assentamento = ocorrencia_funcional_assentamento.cod_assentamento
	
	     INNER JOIN tceal.ocorrencia_funcional
		    ON ocorrencia_funcional_assentamento.cod_ocorrencia = ocorrencia_funcional.cod_ocorrencia
		    
	     INNER JOIN sw_cgm
		     ON servidor.numcgm = sw_cgm.numcgm
	
	     INNER JOIN sw_cgm_pessoa_fisica
		     ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
	 
		  WHERE assentamento_gerado.periodo_inicial BETWEEN TO_DATE('".$this->getDado('dtInicial')."','DD/MM/YYYY') AND TO_DATE('".$this->getDado('dtFinal')."','DD/MM/YYYY')
		     OR assentamento_gerado.periodo_final BETWEEN TO_DATE('".$this->getDado('dtInicial')."','DD/MM/YYYY') AND TO_DATE('".$this->getDado('dtFinal')."','DD/MM/YYYY')
		    AND assentamento_gerado.cod_assentamento_gerado NOT IN (assentamento_gerado_excluido.cod_assentamento_gerado)
		   
	   UNION 
	
		SELECT DISTINCT sw_cgm_pessoa_fisica.cpf
		     , contrato.registro AS matricula
		     , CASE WHEN lancamento_ferias.cod_tipo = 1 THEN '15'
		       END AS cod_ocorrencia
		     , assentamento_gerado.observacao AS informacao_complementar
		     , TO_CHAR(lancamento_ferias.dt_inicio, 'DD/MM/YYYY') AS data_inicio_ocorrencia
		     , TO_CHAR(lancamento_ferias.dt_fim, 'DD/MM/YYYY') AS data_termino_ocorrencia
		  FROM pessoal".$this->getDado('stEntidade').".servidor
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".servidor_contrato_servidor
		    ON servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".contrato_servidor
		    ON servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".contrato
		    ON contrato_servidor.cod_contrato = contrato.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".assentamento_gerado_contrato_servidor
		    ON contrato.cod_contrato = assentamento_gerado_contrato_servidor.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".assentamento_gerado
		    ON assentamento_gerado_contrato_servidor.cod_assentamento_gerado = assentamento_gerado.cod_assentamento_gerado
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".ferias
		    ON contrato_servidor.cod_contrato = ferias.cod_contrato
	
	    INNER JOIN pessoal".$this->getDado('stEntidade').".lancamento_ferias
		    ON ferias.cod_ferias = lancamento_ferias.cod_ferias        
	
	    INNER JOIN sw_cgm
		    ON servidor.numcgm = sw_cgm.numcgm
	
	    INNER JOIN sw_cgm_pessoa_fisica
		    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
	
		 WHERE lancamento_ferias.dt_inicio BETWEEN TO_DATE('".$this->getDado('dtInicial')."','DD/MM/YYYY') AND TO_DATE('".$this->getDado('dtFinal')."','DD/MM/YYYY')
		    OR lancamento_ferias.dt_fim BETWEEN TO_DATE('".$this->getDado('dtInicial')."','DD/MM/YYYY') AND TO_DATE('".$this->getDado('dtFinal')."','DD/MM/YYYY')
	
	) AS consulta ";

        return $stSql;
    }

}

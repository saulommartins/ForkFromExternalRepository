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
	* Classe de mapeamento da tabela tcemg.convenio
	* Data de Criação   : 11/03/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: TTCEMGConvenio.class.php 59719 2014-09-08 15:00:53Z franver $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGConvenio extends Persistente
{
/**
	* Método Construtor
	* @access Private
*/
function TTCEMGConvenio()
{
	parent::Persistente();
	$this->setTabela('tcemg.convenio');

	$this->setCampoCod('cod_convenio');
	$this->setComplementoChave('exercicio,cod_entidade');

	$this->AddCampo( 'cod_convenio'             , 'integer' , true  , ''    , true  , false );
	$this->AddCampo( 'cod_entidade'             , 'integer' , true  , ''    , true  , true  );
	$this->AddCampo( 'nro_convenio'             , 'integer' , true  , ''    , false , false );
	$this->AddCampo( 'exercicio'                , 'char'    , true  , '4'   , true  , true  );
	$this->AddCampo( 'data_assinatura'          , 'date'    , true  , ''    , false , false );
	$this->AddCampo( 'data_inicio'              , 'date'    , true  , ''    , false , false );
	$this->AddCampo( 'data_final'               , 'date'    , true  , ''    , false , false );
	$this->AddCampo( 'vl_convenio'              , 'numeric' , true  , '14,2', false , false );
	$this->AddCampo( 'vl_contra_partida'        , 'numeric' , true  , '14,2', false , false );
	$this->AddCampo( 'cod_objeto'               , 'integer' , true  , ''    , false , true  );
}

function recuperaProximoConvenio(&$rsRecordSet)
{
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;

	$stSql = $this->montaRecuperaProximoConvenio();
	$this->setDebug( $stSql );
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

	return $obErro;
}

function montaRecuperaProximoConvenio()
{
	$stSql  = " SELECT max(cod_convenio) + 1 as cod_convenio  \n";
	$stSql .= " FROM tcemg.convenio                           \n";

	return $stSql;
}

function recuperaConvenioFiltro(&$rsRecordSet, $stFiltro = "", $stOrdem = "")
{
	$obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;

	$stSql = $this->montaConvenioFiltro().$stFiltro.$stOrdem;
	$this->setDebug( $stSql );
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

	return $obErro;
}

function montaConvenioFiltro()
{
    $stSql  = " SELECT convenio.cod_convenio, convenio.cod_entidade, convenio.nro_convenio, convenio.exercicio,             \n";
    $stSql .= " convenio.vl_convenio, convenio.vl_contra_partida, objeto.descricao AS objeto, sw_cgm.nom_cgm AS Entidade    \n";
    $stSql .= " FROM tcemg.convenio                                                                                         \n";
    $stSql .= " INNER JOIN compras.objeto                                                                                   \n";
    $stSql .= " ON objeto.cod_objeto = convenio.cod_objeto                                                                  \n";
    $stSql .= " INNER JOIN orcamento.entidade                                                                               \n";
    $stSql .= " ON entidade.cod_entidade = convenio.cod_entidade                                                            \n";
    $stSql .= " AND entidade.exercicio = convenio.exercicio                                                                 \n";
    $stSql .= " INNER JOIN sw_cgm                                                                                           \n";
    $stSql .= " ON sw_cgm.numcgm = entidade.numcgm                                                                          \n";
    
    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaConvenio10.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function recuperaConvenio10(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;    

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaConvenio10().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaConvenio10()
{
    $stSql  = " SELECT 10 AS tipoRegistro
                     , (TC.exercicio||''||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.cod_convenio,9, '0'))) AS codConvenio
                     , (SELECT valor 
                          FROM administracao.configuracao_entidade
                         WHERE exercicio=TC.exercicio
                           AND parametro='tcemg_codigo_orgao_entidade_sicom'
                           AND cod_entidade=TC.cod_entidade) AS codOrgao
                     , TC.nro_convenio AS nroConvenio
                     , to_char(TC.data_assinatura, 'ddmmyyyy') AS dataAssinatura
                     , objeto.descricao AS objetoConvenio
                     , to_char(TC.data_inicio, 'ddmmyyyy') AS dataInicioVigencia
                     , to_char(TC.data_final, 'ddmmyyyy') AS dataFinalVigencia
                     , REPLACE(TC.vl_convenio::TEXT, '.', ',') AS vlConvenio
                     , REPLACE(TC.vl_contra_partida::TEXT, '.', ',') AS vlContrapartida
                  FROM tcemg.convenio AS TC
            INNER JOIN compras.objeto
                    ON objeto.cod_objeto=TC.cod_objeto  
                 WHERE TC.exercicio='".$this->getDado('exercicio')."' -- ENTRADA EXERCICIO
                   AND TC.cod_entidade IN (".$this->getDado('entidade').") -- ENTRADA ENTIDADE
		           AND TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
		                                  AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
              ORDER BY TC.cod_entidade
                     , TC.nro_convenio";
      
    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaConvenio11.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function recuperaConvenio11(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;    
    
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
    $stSql = $this->montaRecuperaConvenio11().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
}

public function montaRecuperaConvenio11()
{		
	$stSql  = " SELECT 11 AS tipoRegistro
                         , (TC.exercicio||''||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.cod_convenio,9, '0'))) AS codConvenio
                         , 2 AS tipoDocumento
                         , CGMPJ.cnpj AS nroDocumento
                         , CASE WHEN TCP.esfera = 'Federal' THEN
                             1
                           WHEN TCP.esfera = 'Estadual' THEN
                             2
                           WHEN TCP.esfera = 'Municipal' THEN
                             3
                           END AS esferaConcedente
                         , REPLACE(TCP.vl_concedido::TEXT, '.', ',') AS valorConcedido
                      FROM tcemg.convenio AS TC
		      
                INNER JOIN tcemg.convenio_participante AS TCP
                        ON TCP.cod_convenio = TC.cod_convenio
                       AND TCP.exercicio    = TC.exercicio
                       AND TCP.cod_entidade = TC.cod_entidade
		       
                INNER JOIN sw_cgm_pessoa_juridica AS CGMPJ
                        ON CGMPJ.numcgm = TCP.cgm_participante
                     
		     WHERE TC.exercicio = '".$this->getDado('exercicio')."'
                       AND TC.cod_entidade IN (".$this->getDado('entidade').")
                       AND TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
                                              AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
					      
                  ORDER BY TC.cod_entidade
                          ,TC.nro_convenio ";
    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaConvenio20.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
public function recuperaConvenio20(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;    
    
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
    $stSql = $this->montaRecuperaConvenio20().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
    return $obErro;
}


public function montaRecuperaConvenio20()
{    
	$stSql  = " SELECT 20 AS tipoRegistro
			 , (SELECT valor 
			      FROM administracao.configuracao_entidade
			     WHERE exercicio    = TC.exercicio
			       AND parametro    = 'tcemg_codigo_orgao_entidade_sicom'
			       AND cod_entidade = TC.cod_entidade) AS codOrgao
			 , TC.nro_convenio AS nroConvenio
			 , TO_CHAR(TC.data_assinatura, 'ddmmyyyy') AS dataAssinatura
			 , TCA.cod_aditivo AS nroAditivo
			 , TCA.descricao AS dscAlteracao
			 , TO_CHAR(TCA.data_assinatura, 'ddmmyyyy') AS dtAssinaturaAditivo
			 , CASE WHEN TCA.data_final IS NOT NULL THEN
			    TO_CHAR(TCA.data_final, 'ddmmyyyy')
			   ELSE
			     TO_CHAR(TC.data_final, 'ddmmyyyy')
			   END AS dataVigencia
			 , CASE WHEN TCA.vl_convenio > 0 THEN
			     REPLACE(TCA.vl_convenio::VARCHAR, '.', ',')
			   ELSE
			     REPLACE(TC.vl_convenio::VARCHAR, '.', ',')
			   END AS valorAtualizado
			 , CASE WHEN TCA.vl_contra > 0 THEN
			     REPLACE(TCA.vl_contra::VARCHAR, '.', ',')
			   ELSE
			     REPLACE(TC.vl_contra_partida::VARCHAR, '.', ',')
			   END AS valorContra
			 , (TC.exercicio||''||(LPAD(''||TC.cod_entidade,2, '0'))||(LPAD(''||TC.cod_convenio,9, '0'))) AS codConvenio
		      FROM tcemg.convenio AS TC
   
		INNER JOIN tcemg.convenio_aditivo AS TCA
			ON TCA.cod_convenio = TC.cod_convenio
		       AND TCA.exercicio    = TC.exercicio
		       AND TCA.cod_entidade = TC.cod_entidade
   
		     WHERE TC.exercicio = '".$this->getDado('exercicio')."'
		       AND TC.cod_entidade IN (".$this->getDado('entidade').")
		       AND TC.data_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') 
					      AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
		   
		  ORDER BY TC.cod_entidade
			  ,TC.nro_convenio ";
	return $stSql;
}

public function __destruct(){}

}
?>

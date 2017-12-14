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
  * Classe de mapeamento da tabela patrimonio.depreciacao_anulada
  * Data de Criação: 16/10/2014

  * @author Analista:      Gelson W. Gonçalves 
  * @author Desenvolvedor: Arthur Cruz

  * @ignore

  $Id: TPatrimonioDepreciacaoAnulada.class.php 63222 2015-08-05 14:25:57Z arthur $
  $Date: $
  $Author: $
  $Rev: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TPatrimonioDepreciacaoAnulada extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setTabela('patrimonio.depreciacao_anulada');
        $this->setCampoCod('');
        $this->setComplementoChave('cod_depreciacao,cod_bem, timestamp');
        
	$this->AddCampo('cod_depreciacao'     ,'integer'   ,true   ,'4' ,true  ,false);
        $this->AddCampo('cod_bem'             ,'integer'   ,true   ,''  ,true  ,false);
        $this->AddCampo('timestamp'           ,'timestamp' ,true   ,''  ,true  ,false);
        $this->AddCampo('timestamp_anulacao'  ,'timestamp' ,false  ,''  ,false ,false);
        $this->AddCampo('motivo'              ,'text'      ,false  ,''  ,false ,false);
    }

    function recuperaRelacionamento(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = ""){
        $obErro      = new Erro;
	$obConexao   = new Conexao;
	$rsRecordSet = new RecordSet;
	$stSql = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
	$this->stDebug = $stSql;
	$obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
	return $obErro;
    }
    
    function montaRecuperaRelacionamento(){
        $stSql = " SELECT * 
                    FROM patrimonio.depreciacao_anulada
              
              INNER JOIN patrimonio.depreciacao
                      ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                     AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                     AND depreciacao.timestamp       = depreciacao_anulada.timestamp \n";
        
        return $stSql;
    }
    
    public function recuperaMaxCodDepreciacaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaMaxCodDepreciacaoAnulada().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMaxCodDepreciacaoAnulada()
    {
        $stSql  = "  SELECT MAX(depreciacao_anulada.cod_depreciacao) AS max_cod_depreciacao_anulada
			FROM patrimonio.depreciacao_anulada
			
		  INNER JOIN patrimonio.depreciacao
                          ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                         AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                         AND depreciacao.timestamp       = depreciacao_anulada.timestamp \n ";
        return $stSql;
    }
    
    public function recuperaMaxCompetenciaAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
            
        $stSql = $this->montaRecuperaMaxCompetenciaAnulada().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaMaxCompetenciaAnulada()
    {
        $stSql  = " SELECT MAX(depreciacao.competencia) AS max_competencia
                         , TO_CHAR(TO_DATE(MAX(depreciacao.competencia), 'YYYYMM'), 'MM/YYYY') AS max_competencia_formatada
                     FROM patrimonio.depreciacao_anulada
                    
                    INNER JOIN patrimonio.depreciacao
                            ON depreciacao.cod_bem         = depreciacao_anulada.cod_bem
                           AND depreciacao.cod_depreciacao = depreciacao_anulada.cod_depreciacao
                           AND depreciacao.timestamp       = depreciacao_anulada.timestamp \n ";
        return $stSql;
    }
    
    public function executaFuncao($stParametros, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = $this->montaExecutaFuncao($stParametros);
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordset, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaExecutaFuncao($stParametros)
    {
        $stSql  = " SELECT patrimonio.fn_depreciacao_anulacao(".$stParametros.") AS valor";

        return $stSql;
    }

}

?>
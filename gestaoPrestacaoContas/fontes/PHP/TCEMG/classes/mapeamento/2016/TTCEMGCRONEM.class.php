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
	* Classe de mapeamento exportação CRONEM.csv
	* Data de Criação       : 18/02/2016
	* @author Analista      : Ane Caroline Fiegenbaum Pereira
	* @author Desenvolvedor : Jean da Silva
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGCRONEM extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TTCEMGCRONEM()
    {
        parent::Persistente();
    }
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaCronem.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaCronem(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaCronem().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaCronem()
    {
        $stSql  = " SELECT 10 AS tipo_registro
                         , LPAD(''||cronem.num_orgao,2,'0') AS cod_orgao
                         , LPAD((LPAD(''||cronem.num_orgao,2, '0')||LPAD(''||cronem.num_unidade,2, '0')), 5, '0') AS cod_unidade_sub
                         , cronem.valor AS vl_dot_mensal
                         , cronem.cod_grupo AS grupo_despesa
                         , cronem.periodo
                      FROM tcemg.cronograma_execucao_mensal_desembolso AS cronem
                     WHERE cronem.cod_entidade IN ('".$this->getDado('entidades')."')
                       AND cronem.exercicio = '".$this->getDado('exercicio')."'
                       AND cronem.periodo = ".$this->getDado('periodo')."
                  ORDER BY cod_orgao
				       , cod_unidade_sub
					   , grupo_despesa
					   , periodo
                ";
                    
        return $stSql;
    }
	
	public function __destruct(){}

}
?>

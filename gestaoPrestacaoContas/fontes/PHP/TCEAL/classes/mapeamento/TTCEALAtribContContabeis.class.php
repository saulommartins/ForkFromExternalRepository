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

class TTCEALAtribContContabeis extends Persistente
{

    /*
     * Método Construtor
     *
     * @return void
     */
    public function TTCEALAtribContContabeis()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio());
    }

    public function recuperaAtribContabeis(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem)){
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        }
        
        $stSql = $this->montaRecuperaAtribContabeis().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaAtribContabeis()
    {
        
	$stSql = "
	      SELECT (SELECT PJ.cnpj
                        FROM orcamento.entidade
                        JOIN sw_cgm
                          ON sw_cgm.numcgm = entidade.numcgm
                        JOIN sw_cgm_pessoa_juridica AS PJ
                          ON sw_cgm.numcgm = PJ.numcgm
                       WHERE entidade.exercicio = '".$this->getDado('exercicio')."'
                         AND entidade.cod_entidade = retorno.cod_entidade
                   ) AS cod_und_gestora
                 , (SELECT valor
                      FROM administracao.configuracao_entidade
                     WHERE configuracao_entidade.exercicio = '".$this->getDado('exercicio')."'
                       AND configuracao_entidade.cod_entidade = retorno.cod_entidade
                       AND configuracao_entidade.cod_modulo = 62
                       AND configuracao_entidade.parametro = 'tceal_configuracao_unidade_autonoma'
                   ) AS codigo_ua
                 , ".$this->getDado('bimestre')." AS bimestre
                 , '".$this->getDado('exercicio')."' AS exercicio
	         , RPAD(REPLACE(cod_estrutural,'.',''),17,'0') AS cod_conta_balancete
	         , nom_conta AS descricao
	         , CASE WHEN escrituracao = 'sintetica'
                       THEN 'S'
                       ELSE 'A'
                   END AS tipo_conta   
                 , nivel AS nivel_conta     
                 , CASE WHEN escrituracao = 'sintetica'
                        THEN 'N'
                        ELSE 'S'
                   END AS escrituracao 
		 , (SELECT CASE WHEN cod_sistema = 1 THEN 'P'
                                WHEN cod_sistema = 2 THEN 'O'
                                WHEN cod_sistema = 3 THEN 'C'
                           END AS cod_sistema 
                     FROM contabilidade.plano_conta
                    WHERE plano_conta.exercicio = '".$this->getDado('exercicio')."'  
                      AND plano_conta.cod_estrutural = retorno.cod_estrutural
                 ) AS natureza_informacao 
                 , CASE WHEN indicador_superavit = 'financeiro'
                       THEN 'F'
                       ELSE 'P'
                  END AS indicador_superavit
             FROM tceal.balancete_verificacao('".$this->getDado('exercicio')."','".$this->getDado('cod_entidade')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."')
               AS retorno( cod_estrutural               VARCHAR
			 , nivel                        INTEGER
			 , nom_conta                    VARCHAR
			 , cod_sistema                  INTEGER
			 , indicador_superavit          CHAR(12)
			 , escrituracao                 CHAR(9)
			 , vl_saldo_anterior            NUMERIC
			 , vl_saldo_debitos             NUMERIC
			 , vl_saldo_debitos_bimestre    NUMERIC
			 , vl_saldo_creditos            NUMERIC
			 , vl_saldo_creditos_bimestre   NUMERIC
			 , vl_saldo_atual               NUMERIC
			 , cod_entidade                 INTEGER ) ";
       
        return $stSql;
    }

}

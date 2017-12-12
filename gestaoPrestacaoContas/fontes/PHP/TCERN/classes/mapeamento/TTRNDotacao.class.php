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
 * Extensão da Classe de mapeamento
 * Data de Criação: 12/10/2007

 * @author Analista: Diego Barbosa Victoria
 * @author Desenvolvedor: Diego Barbosa Victoria

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-06.06.00

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  *
  * Data de Criação: 12/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNDotacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTRNDotacao()
    {
        $this->setEstrutura( array() );
        $this->setEstruturaAuxiliar( array() );
        $this->setDado('exercicio',Sessao::getExercicio());
    }

    public function montaRecuperaRelacionamento()
    {

        $stSql .= "
    SELECT CASE WHEN SUBSTR(cod_estrutural,1,1) = '9'
                THEN '999999'
                ELSE SUBSTR(REPLACE(cod_estrutural,'.',''),1,6)
            END AS estrutural
         , fonte_recurso
         , classificacao_institucional
         , classificacao_funcional
         , classificacao_programa
         , classificacao_projeto
         , REPLACE(SUM(COALESCE(vl_original,000))::VARCHAR, '.', '') AS vl_original
         , REPLACE(SUM(COALESCE(vl_atualizado,000))::VARCHAR, '.', '') AS vl_atualizado
         , REPLACE(SUM(COALESCE(vl_empenho_bimestre,000))::VARCHAR, '.', '') AS vl_empenho_bimestre
         , REPLACE(SUM(COALESCE(vl_empenho_ano,000))::VARCHAR, '.', '') AS vl_empenho_ano
         , REPLACE(SUM(COALESCE(vl_liquidacao_bimestre,000))::VARCHAR, '.', '') AS vl_liquidacao_bimestre
         , REPLACE(SUM(COALESCE(vl_liquidacao_ano,000))::VARCHAR, '.', '') AS vl_liquidacao_ano
         , REPLACE(SUM(COALESCE(vl_restos_pagar,000))::VARCHAR, '.', '') AS vl_restos_pagar
           FROM    tcern.fn_exportacao_despesa('".$this->getDado('exercicio')."','".$this->getDado('inCodEntidade')."','".$this->getDado('dtInicial')."','".$this->getDado('dtFinal')."') as tabela
                    (   cod_estrutural varchar
                        ,fonte_recurso integer
                        ,classificacao_institucional varchar
                        ,classificacao_funcional varchar
                        ,classificacao_programa integer
                        ,classificacao_projeto integer
                        ,vl_original numeric
                        ,vl_atualizado numeric
                        ,vl_empenho_bimestre numeric
                        ,vl_empenho_ano numeric
                        ,vl_liquidacao_bimestre numeric
                        ,vl_liquidacao_ano numeric
                        ,vl_restos_pagar numeric
                    )
          GROUP BY estrutural
                 , fonte_recurso
                 , classificacao_institucional
                 , classificacao_funcional
                 , classificacao_programa
                 , classificacao_projeto
        ";

        return $stSql;
    }

    public function recuperaDadosSaldoDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaRelacionamentoSaldoDotacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoSaldoDotacao()
    {
        $stSql .= " SELECT
                    lpad(replace(stn.fn_saldo_dotacao_atualizado('".$this->getDado('exercicio')."', '".$this->getDado('codEstrutural')."', '".$this->getDado('inCodEntidade')."', '".$this->getDado('dtInicial')."', '".$this->getDado('dtFinal')."')::varchar,'.',''),14,'0') as dotacao_acumulada   \n";

        return $stSql;
    }

}

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
    * Classe de mapeamento da tabela ORCAMENTO.PROGRAMA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.03 , uc-02.08.02
*/

/*
$Log$
Revision 1.9  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.PROGRAMA
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoPrograma extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoPrograma()
{
    parent::Persistente();
    $this->setTabela('orcamento.programa');

    $this->setCampoCod('cod_programa');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'4',true,false);
    $this->AddCampo('cod_programa','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'80',false,false);

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaMascarado.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaMascarado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaMascarado().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMascarado()
{
    $stSql  = "  SELECT                                   \n";
    $stSql .= "     sw_fn_mascara_dinamica                \n";
    $stSql .= "     (                                     \n";
    $stSql .= "     '".$this->getDado('stMascara')."',    \n";
    $stSql .= "     ''||orcamento.programa.cod_programa   \n";
    $stSql .= "     ) AS cod_programa,                    \n";
    $stSql .= "     orcamento.programa.exercicio,         \n";
    $stSql .= "     orcamento.programa.descricao,         \n";
    $stSql .= "     sw_fn_mascara_dinamica                \n";
    $stSql .= "     (                                     \n";
    $stSql .= "     '".$this->getDado('stMascara')."',    \n";
    $stSql .= "     ''||ppa.programa.num_programa         \n";
    $stSql .= "     ) AS num_programa                     \n";
    $stSql .= "  FROM                                     \n";
    $stSql .= "    ".$this->getTabela()."                 \n";
    $stSql .= "  JOIN orcamento.programa_ppa_programa     \n";
    $stSql .= "    ON programa_ppa_programa.cod_programa = orcamento.programa.cod_programa \n";
    $stSql .= "   AND programa_ppa_programa.exercicio    = orcamento.programa.exercicio \n";
    $stSql .= "  JOIN ppa.programa                        \n";
    $stSql .= "    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa \n";

    return $stSql;
}

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = "select valor from administracao.configuracao where cod_modulo = 2 and parametro = 'samlink_host'";
        $obErro = $obConexao->executaSQL( $rsSamLink, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("boTemSiam", !$rsSamLink->eof() );
            $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        }

        return $obErro;
    }

    public function MontaRecuperaDadosExportacao()
    {
        $stSql  = "SELECT * FROM (                                              \n";
        $stSql .= "  SELECT orcamento.programa.exercicio::INTEGER               \n";
        $stSql .= "       , CASE WHEN ppa.programa.num_programa IS NOT NULL THEN ppa.programa.num_programa \n";
        $stSql .= "         ELSE orcamento.programa.cod_programa END AS cod_programa \n";
        $stSql .= "       , CASE WHEN ppa.programa.num_programa IS NOT NULL THEN regexp_replace(programa_dados.identificacao, E'[\\n\\r]+', '', 'g' ) \n";
        $stSql .= "         ELSE regexp_replace(orcamento.programa.descricao, E'[\\n\\r]+', '', 'g' ) END AS descricao \n";
        $stSql .= "    FROM orcamento.programa                                  \n";
        $stSql .= "LEFT JOIN orcamento.programa_ppa_programa                     \n";
        $stSql .= "      ON programa_ppa_programa.cod_programa = orcamento.programa.cod_programa \n";
        $stSql .= "     AND programa_ppa_programa.exercicio    = orcamento.programa.exercicio \n";
        $stSql .= "LEFT JOIN ppa.programa                                        \n";
        $stSql .= "      ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa \n";
        $stSql .= "LEFT JOIN ppa.programa_dados                                  \n";
        $stSql .= "      ON programa_dados.cod_programa             = ppa.programa.cod_programa \n";
        $stSql .= "     AND programa_dados.timestamp_programa_dados = ppa.programa.ultimo_timestamp_programa_dados \n";
        
        if ( $this->getDado("boTemSiam") ) {
            $stSql .= "UNION                                                    \n";
            $stSql .= "  SELECT                                                 \n";
            $stSql .= "     2004 as exercicio,                                  \n";
            $stSql .= "     to_number(cod_programa,'9999') as cod_programa,     \n";
            $stSql .= "     'PROGRAMA' as descricao                             \n";
            $stSql .= "  FROM                                                   \n";
            $stSql .= "     samlink.vw_siam_programa_2004                       \n";
        }
        $stSql .= ") AS tabela                                                  \n";
        $stSql .= " WHERE tabela.exercicio <= ".$this->getDado('exercicio')."   \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaPrograma.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stGroup     String de Agrupamento do SQL (GROUP BY)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaProgramaExportacaoDespesa(&$rsRecordSet, $stCondicao = "", $stGroup = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaProgramaExportacaoDespesa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProgramaExportacaoDespesa()
    {
        $stSql   = " SELECT p_programa.num_programa as numero_programa                       \n";
        $stSql  .= "      , translate(programa.descricao,'ÁÀÂÃÄáàâãäÉÈÊËéèêëÍÌÎÏíìîïÓÒÕÔÖóòôõöÚÙÛÜúùûüÇç','AAAAAaaaaaEEEEeeeeIIIIiiiiOOOOOoooooUUUUuuuuCc') as descricao_programa \n";
        $stSql  .= "   FROM orcamento.despesa                                                \n";
        $stSql  .= "  INNER JOIN orcamento.conta_despesa                                     \n";
        $stSql  .= "     ON despesa.exercicio = conta_despesa.exercicio                      \n";
        $stSql  .= "    AND despesa.cod_conta = conta_despesa.cod_conta                      \n";
        $stSql  .= "  INNER JOIN orcamento.programa                                          \n";
        $stSql  .= "     ON despesa.exercicio = programa.exercicio                           \n";
        $stSql  .= "    AND despesa.cod_programa = programa.cod_programa                     \n";
        $stSql  .= "  INNER JOIN orcamento.programa_ppa_programa                             \n";
        $stSql  .= "     ON programa_ppa_programa.exercicio = programa.exercicio             \n";
        $stSql  .= "    AND programa_ppa_programa.cod_programa = programa.cod_programa       \n";
        $stSql  .= "  INNER JOIN ppa.programa AS p_programa                                  \n";
        $stSql  .= "     ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa \n";
        $stSql  .= "  WHERE programa.exercicio = '".$this->getDado('exercicio')."'           \n";
        $stSql  .= "    AND despesa.cod_entidade IN (".$this->getDado('inCodEntidade').")    \n";
        $stSql  .= "  GROUP BY p_programa.num_programa                                       \n";
        $stSql  .= "      , programa.descricao                                               \n";
        $stSql  .= "  ORDER BY p_programa.num_programa                                       \n";

        return $stSql;
    }

    public function recuperaProjetoAtividadeExportacaoDespesa(&$rsRecordSet, $stCondicao = "", $stGroup = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaProjetoAtividadeExportacaoDespesa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProjetoAtividadeExportacaoDespesa()
    {
        $stSql   = "  SELECT acao.num_acao as numero_projeto_atividade                         \n";
        $stSql  .= "      , translate(pao.nom_pao,'ÁÀÂÃÄáàâãäÉÈÊËéèêëÍÌÎÏíìîïÓÒÕÔÖóòôõöÚÙÛÜúùûüÇç','AAAAAaaaaaEEEEeeeeIIIIiiiiOOOOOoooooUUUUuuuuCc') as descricao \n";
        $stSql  .= "      , p_programa.num_programa as numero_programa                         \n";
        $stSql  .= "   FROM orcamento.despesa                                                  \n";
        $stSql  .= "  INNER JOIN orcamento.conta_despesa                                       \n";
        $stSql  .= "     ON despesa.exercicio = conta_despesa.exercicio                        \n";
        $stSql  .= "    AND despesa.cod_conta = conta_despesa.cod_conta                        \n";
        $stSql  .= "  INNER JOIN orcamento.programa                                            \n";
        $stSql  .= "     ON despesa.exercicio = programa.exercicio                             \n";
        $stSql  .= "    AND despesa.cod_programa = programa.cod_programa                       \n";
        $stSql  .= "  INNER JOIN orcamento.programa_ppa_programa                               \n";
        $stSql  .= "     ON programa_ppa_programa.exercicio = programa.exercicio               \n";
        $stSql  .= "    AND programa_ppa_programa.cod_programa = programa.cod_programa         \n";
        $stSql  .= "  INNER JOIN ppa.programa AS p_programa                                    \n";
        $stSql  .= "     ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa   \n";
        $stSql  .= " INNER JOIN orcamento.pao                                                  \n";
        $stSql  .= "    ON despesa.exercicio = pao.exercicio                                   \n";
        $stSql  .= "   AND despesa.num_pao = pao.num_pao                                       \n";
        $stSql  .= " INNER JOIN orcamento.pao_ppa_acao                                         \n";
        $stSql  .= "    ON pao_ppa_acao.exercicio = pao.exercicio                              \n";
        $stSql  .= "   AND pao_ppa_acao.num_pao = pao.num_pao                                  \n";
        $stSql  .= " INNER JOIN ppa.acao                                                       \n";
        $stSql  .= "    ON pao_ppa_acao.cod_acao = acao.cod_acao                               \n";
        $stSql  .= "       WHERE pao.exercicio = '".$this->getDado('exercicio')."'             \n";
        $stSql  .= "         AND despesa.cod_entidade IN (".$this->getDado('inCodEntidade').") \n";
        $stSql  .= "    GROUP BY acao.num_acao                                                 \n";
        $stSql  .= "           , pao.nom_pao                                                   \n";
        $stSql  .= "           , p_programa.num_programa                                       \n";
        $stSql  .= "    ORDER BY p_programa.num_programa                                       \n";
        $stSql  .= "           , acao.num_acao                                                 \n";

        return $stSql;
    }

    public function recuperaInstitucionalExportacaoDespesa(&$rsRecordSet, $stCondicao = "", $stGroup = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInstitucionalExportacaoDespesa();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInstitucionalExportacaoDespesa()
    {
        $stSql   = "      SELECT LPAD(despesa.cod_entidade::VARCHAR, 3, '0') || LPAD(despesa.num_orgao::VARCHAR, 3, '0') AS numero_institucional      \n";
        $stSql  .= "           , translate(orgao.nom_orgao,'ÁÀÂÃÄáàâãäÉÈÊËéèêëÍÌÎÏíìîïÓÒÕÔÖóòôõöÚÙÛÜúùûüÇç','AAAAAaaaaaEEEEeeeeIIIIiiiiOOOOOoooooUUUUuuuuCc') as nom_orgao                                                                                \n";
        $stSql  .= "        FROM orcamento.despesa                                                                              \n";
        $stSql  .= "  INNER JOIN orcamento.conta_despesa                                                                        \n";
        $stSql  .= "          ON despesa.exercicio = conta_despesa.exercicio                                                    \n";
        $stSql  .= "         AND despesa.cod_conta = conta_despesa.cod_conta                                                    \n";
        $stSql  .= "  INNER JOIN orcamento.unidade                                                                              \n";
        $stSql  .= "          ON despesa.exercicio = unidade.exercicio                                                          \n";
        $stSql  .= "         AND despesa.num_orgao = unidade.num_orgao                                                          \n";
        $stSql  .= "         AND despesa.num_unidade = unidade.num_unidade                                                      \n";
        $stSql  .= "  INNER JOIN orcamento.orgao                                                                                \n";
        $stSql  .= "          ON orgao.exercicio = unidade.exercicio                                                            \n";
        $stSql  .= "         AND orgao.num_orgao = unidade.num_orgao                                                            \n";
        $stSql  .= "       WHERE unidade.exercicio = '".$this->getDado('exercicio')."'                                          \n";
        $stSql  .= "         AND despesa.cod_entidade IN (".$this->getDado('inCodEntidade').")                                  \n";
        $stSql  .= "    GROUP BY despesa.num_orgao                                                                              \n";
        $stSql  .= "           , despesa.cod_entidade                                                                           \n";
        $stSql  .= "           , orgao.nom_orgao                                                                                \n";
        $stSql  .= "    ORDER BY despesa.num_orgao                                                                              \n";

        return $stSql;
    }
    
     public function recuperaProgramas(&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaProgramas();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaProgramas()
    {
        $stSql = " SELECT programa.exercicio 
                        , ppa.num_programa AS cod_programa
                        , programa.descricao
                        , CASE WHEN programa_objetivo_milenio.cod_tipo_objetivo IS NOT NULL THEN
                                programa_objetivo_milenio.cod_tipo_objetivo 
                            ELSE
                                0
                          END AS cod_tipo_objetivo
                    FROM orcamento.programa
                    
                    JOIN ppa.programa AS ppa
                      ON ppa.cod_programa = programa.cod_programa
                    
                    LEFT JOIN tcepb.programa_objetivo_milenio
                      ON programa_objetivo_milenio.cod_programa = programa.cod_programa
                     AND programa_objetivo_milenio.exercicio = programa.exercicio
                     
                   WHERE programa.exercicio= '".$this->getDado('exercicio')."'  
                   ORDER BY programa.cod_programa ";
        return $stSql;                   
    }
}

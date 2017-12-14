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
 * Classe de mapeamento da tabela FN_EXPORTACAO_LIQUIDACAO

 * @author Desenvolvedor: Diogo Zarpelon

 * @package URBEM
 * @subpackage Mapeamento

 * Casos de uso: uc-02.08.01
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTransparenciaLiquidacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTransparenciaLiquidacao()
    {
        parent::Persistente();
        $this->setTabela('tcers.fn_exportacao_liquidacao');
    }

    public function montaRecuperaDadosExportacao()
    {
        $stSql  = "SELECT                                                                       \n";
        $stSql .= "    lpad(tabela.exercicio::varchar,4,'0') as exercicio,                      \n";
        $stSql .= "    (tabela.exercicio || LPAD(tabela.cod_entidade::varchar,2,'0') || LPAD(tabela.cod_empenho::varchar,7,'0')) as cod_empenho  ,\n";
        $stSql .= "    lpad(tabela.cod_entidade::varchar,2,'0') as cod_entidade,                \n";
        $stSql .= "    tabela.cod_nota,                                                         \n";
        $stSql .= "    to_char(tabela.data_pagamento,'dd/mm/yyyy') as data_pagamento,           \n";
        $stSql .= "    replace(tabela.valor_liquidacao::varchar,'.','') as valor_liquidacao,    \n";
        $stSql .= "    tabela.sinal_valor,                                                      \n";
        $stSql .= "    tabela.observacao,                                                       \n";
        $stSql .= "    tabela.ordem,                                                            \n";
        $stSql .= "    ' ' as codigo_operacao                                                   \n";
        $stSql .= "FROM                                                                         \n";
        $stSql .= " ".$this->getTabela()."('".$this->getDado("stExercicio")     ."',            \n";
        $stSql .= "                        '".$this->getDado("dtInicial")       ."',            \n";
        $stSql .= "                        '".$this->getDado("dtFinal")         ."',            \n";
        $stSql .= "                        '".$this->getDado("stCodEntidades")  ."',            \n";
        $stSql .= "                        '".$this->getDado("stFiltro")        ."')            \n";
        $stSql .= "AS tabela              (   exercicio char(4),                                \n";
        $stSql .= "                           cod_empenho integer,                              \n";
        $stSql .= "                           cod_entidade integer,                             \n";
        $stSql .= "                           cod_nota integer,                                 \n";
        $stSql .= "                           data_pagamento date,                              \n";
        $stSql .= "                           valor_liquidacao numeric,                         \n";
        $stSql .= "                           sinal_valor text,                                 \n";
        $stSql .= "                           observacao varchar,                               \n";
        $stSql .= "                           ordem integer,                                    \n";
        $stSql .= "                           oid oid)                                          \n";

        return $stSql;
    }

    /**
        * Executa funcao fn_exportacao_liquidacao no banco de dados a partir do comando SQL montado no método montaRecuperaDadosLiquidacao.
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

        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;

        $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

}

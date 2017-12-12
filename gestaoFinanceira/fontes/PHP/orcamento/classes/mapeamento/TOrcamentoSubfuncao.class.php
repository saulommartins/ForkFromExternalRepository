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
    * Classe de mapeamento da tabela ORCAMENTO.SUBFUNCAO
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.8  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.SUBFUNCAO
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoSubfuncao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TOrcamentoSubfuncao()
    {
        parent::Persistente();
        $this->setTabela('orcamento.subfuncao');

        $this->setCampoCod('cod_subfuncao');
        $this->setComplementoChave('exercicio');

        $this->AddCampo('exercicio','char',true,'04',true,false);
        $this->AddCampo('cod_subfuncao','integer',true,'',true,false);
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
    public function recuperaMascarado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
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

    public function montaRecuperaMascarado()
    {
        $stSql  = "  SELECT                                   \n";
        $stSql .= "     sw_fn_mascara_dinamica                \n";
        $stSql .= "     (                                     \n";
        $stSql .= "     '".$this->getDado('stMascara')."',    \n";
        $stSql .= "     ''||cod_subfuncao                     \n";
        $stSql .= "     ) AS cod_subfuncao,                   \n";
        $stSql .= "     exercicio,                            \n";
        $stSql .= "     descricao                             \n";
        $stSql .= "  FROM                                     \n";
        $stSql .= "    ".$this->getTabela()."                 \n";

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
        $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosExportacao()
    {
        $stSql  = "SELECT * FROM (                                      \n";
        $stSql .= "  SELECT                                             \n";
        $stSql .= "     OS.exercicio,                                   \n";
        $stSql .= "     OS.cod_subfuncao,                               \n";
        $stSql .= "     OS.descricao                                    \n";
        $stSql .= "  FROM                                               \n";
        $stSql .= "     orcamento.subfuncao AS OS                   \n";
        $stSql .= "  WHERE                                              \n";
        $stSql .= "     OS.exercicio <= '".$this->getDado('exercicio')."' \n";
  /*      $stSql .= "UNION                                                \n";
        $stSql .= "SELECT DISTINCT                                      \n";
        $stSql .= "     exercicio,                                      \n";
        $stSql .= "     cod_subfuncao,                                  \n";
        $stSql .= "     'SUBFUNCAO' as descricao                        \n";
        $stSql .= "FROM                                                 \n";
        $stSql .= "     empenho.restos_pre_empenho                  \n";
        $stSql .= "WHERE                                                \n";
        $stSql .= "     TRIM(cod_subfuncao) <> ''                       \n";
     */   $stSql .= "UNION                                                \n";
        $stSql .= "  SELECT                                             \n";
        $stSql .= "     '2004' as exercicio,                            \n";
        $stSql .= "     OS.cod_subfuncao,                               \n";
        $stSql .= "     'SUBFUNCAO' as descricao                        \n";
        $stSql .= "  FROM                                               \n";
        $stSql .= "     orcamento.subfuncao AS OS                   \n";
        $stSql .= "  WHERE exercicio = '2005'                           \n";
        $stSql .= ") AS tabela                                          \n";

        return $stSql;
    }

}

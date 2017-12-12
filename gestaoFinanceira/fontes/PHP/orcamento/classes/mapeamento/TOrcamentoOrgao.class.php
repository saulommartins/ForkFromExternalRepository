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
    * Classe de mapeamento da tabela ORCAMENTO.ORGAO
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.ORGAO
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoOrgao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoOrgao()
{
    parent::Persistente();
    $this->setTabela('orcamento.orgao');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,num_orgao');

    $this->AddCampo('exercicio'          ,'char'    ,true ,'04',true ,false);
    $this->AddCampo('num_orgao'          ,'integer' ,true ,''  ,true ,false);
    $this->AddCampo('nom_orgao'          ,'varchar' ,true ,'60',false,false);
    $this->AddCampo('usuario_responsavel','integer' ,false,''  ,false,true);
}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT OO.exercicio,
                      OO.num_orgao,
                      OO.num_orgao_mask,
                      OO.nom_orgao
                 FROM ( SELECT *
                             , sw_fn_mascara_dinamica( '" . $this->getDado('stMascara') . "', ''||num_orgao ) as num_orgao_mask
                          FROM " . $this->getTabela() . ") as OO
                         WHERE true ";

    return $stSql;
}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaOrgaosOrganograma
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaOrgaosOrganograma(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaOrgaosOrganograma().$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaOrgaosOrganograma()
{
    $stSql = " SELECT OO.exercicio
                    , OO.num_orgao
                    , OO.num_orgao_mask
                    , OO.nom_orgao
                 FROM ( SELECT *
                             , sw_fn_mascara_dinamica( '" . $this->getDado('stMascara') . "', ''||num_orgao ) as num_orgao_mask
                          FROM " . $this->getTabela() . "
                    ) as OO
                WHERE OO.exercicio = '".$this->getDado('exercicio')."'
";

    return $stSql;
}

function montaRecuperaDadosExercicio()
{
    $stSql = "
        select orcamento.orgao.num_orgao
             , orgao.nom_orgao
        from orcamento.orgao
        where orcamento.orgao.exercicio = '".$this->getDado('exercicio')."' ";

    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExercicio.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosExercicio(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosExercicio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
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
function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
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

function MontaRecuperaDadosExportacao()
{
    $stSql  = "SELECT * FROM (                                              \n";
    $stSql .= "SELECT                                                       \n";
    $stSql .= "     OO.exercicio,                                           \n";
    $stSql .= "     OO.num_orgao,                                           \n";
    $stSql .= "     OO.nom_orgao                                            \n";
    $stSql .= "FROM                                                         \n";
    $stSql .= "     orcamento.orgao AS OO                             \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "     OO.exercicio  <='".$this->getDado('exercicio')."'       \n";
    $stSql .= "UNION                                                        \n";
    $stSql .= "SELECT                                                       \n";
    $stSql .= "     '2004' as exercicio,                                    \n";
    $stSql .= "     OO.num_orgao,                                           \n";
    $stSql .= "     'ORGAO' as nom_orgao                                    \n";
    $stSql .= "FROM                                                         \n";
    $stSql .= "     orcamento.orgao AS OO                             \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "     OO.exercicio = '2005'                                   \n";
  /*  $stSql .= "UNION                                                        \n";
    $stSql .= "SELECT DISTINCT                                              \n";
    $stSql .= "     exercicio,                                              \n";
    $stSql .= "     num_orgao,                                              \n";
    $stSql .= "     'ORGAO' as nom_orgao                                    \n";
    $stSql .= "FROM                                                         \n";
    $stSql .= "     empenho.restos_pre_empenho                          \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "     TRIM(num_orgao) <> ''                \n";  */
    $stSql .= ") as tabela                                                  \n";

    return $stSql;
}

}

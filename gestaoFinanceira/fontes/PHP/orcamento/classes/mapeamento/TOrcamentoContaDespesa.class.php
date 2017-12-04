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
    * Classe de mapeamento da tabela ORCAMENTO.CLASSIFICACAO_DESPESA
    * Data de Criação: 23/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.04 , uc-02.01.06 , uc-02.08.02
*/

/*
$Log$
Revision 1.10  2006/07/05 20:42:02  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ORCAMENTO.CLASSIFICACAO_DESPESA
  * Data de Criação: 23/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoContaDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoContaDespesa()
{
    parent::Persistente();
    $this->setTabela('orcamento.conta_despesa');

    $this->setCampoCod('cod_conta');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_conta','integer',true,'',true,false);
    $this->AddCampo('descricao','varchar',true,'',false,false);
    $this->AddCampo('cod_estrutural','varchar',true,'150',false,false);
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT DISTINCT                                                         \n";
    $stSql .= "    D.cod_conta,                                                        \n";
    $stSql .= "    CD.cod_estrutural,                                                  \n";
    $stSql .= "    D.dt_criacao,                                                       \n";
//    $stSql .= "    CD.exercicio,                                                       \n";
    $stSql .= "    publico.fn_mascarareduzida(CD.cod_estrutural) AS mascara_reduzida,  \n";
    $stSql .= "    CD.descricao                                                        \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "    orcamento.despesa       AS  D,                                  \n";
    $stSql .= "    orcamento.conta_despesa AS CD                                   \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "    D.cod_conta = CD.cod_conta AND                                      \n";
    $stSql .= "    D.exercicio = CD.exercicio                                          \n";

    return $stSql;
}

function montaRecuperaCodEstrutural()
{
    $stSql  = "SELECT conta_despesa.cod_estrutural                                                 \n";
    $stSql .= "     , conta_despesa.cod_conta                                                      \n";
    $stSql .= "     , publico.fn_mascarareduzida(conta_despesa.cod_estrutural) AS mascara_reduzida \n";
    $stSql .= "     , conta_despesa.descricao                                                      \n";
    $stSql .= "  FROM orcamento.conta_despesa                                                      \n";
    $stSql .= " WHERE ";

    return $stSql;
}
/**
    * Mesma função do recuperaRelacionamento, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaCodEstrutural.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaCodEstrutural(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaCodEstrutural().$stCondicao.$stOrdem;
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

    public function MontaRecuperaDadosExportacao2()
    {
        $stSql  = "";
        $stSql .= "  SELECT                                                                 \n";
        $stSql .= "     OD.exercicio,                                                       \n";
        $stSql .= "     OD.cod_estrutural,                                                  \n";
        $stSql .= "     OD.descricao,                                                       \n";
        $stSql .= "     tcers.tipo_conta_rubrica(OD.exercicio,OD.cod_estrutural) as tnc,    \n";
        $stSql .= "     publico.fn_nivel(OD.cod_estrutural) as nnc                          \n";
        $stSql .= "  FROM                                                                   \n";
        $stSql .= "     orcamento.conta_despesa AS OD                                   \n";

        return $stSql;
    }

        function MontaRecuperaDadosExportacao()
        {
        $stSql  = "SELECT * FROM (                                                          \n";
        $stSql .= "  SELECT                                                                 \n";
        $stSql .= "     OD.exercicio,                                                       \n";
        $stSql .= "     replace(OD.cod_estrutural,'.','') as cod_estrutural,                \n";
        $stSql .= "     OD.descricao,                                                       \n";
        $stSql .= "     tcers.tipo_conta_rubrica('OD.exercicio','OD.cod_estrutural') as tnc,    \n";
        $stSql .= "     publico.fn_nivel(OD.cod_estrutural) as nnc                          \n";
        $stSql .= "  FROM                                                                   \n";
        $stSql .= "     orcamento.conta_despesa AS OD                                   \n";
        $stSql .= "  WHERE                                                                  \n";
        $stSql .= "     OD.exercicio <= '".$this->getDado('exercicio')."'                   \n";
    //    $stSql .= "UNION                                                                    \n";
        if ( $this->getDado("boTemSiam") ) {
            $stSql .= "  SELECT                                                                 \n";
            $stSql .= "     '2004' as exercicio,                                                \n";
            $stSql .= "     cod_estrutural,                                                     \n";
            $stSql .= "     'RUBRICA' as descricao,                                             \n";
            $stSql .= "     tipo as tnc,                                                         \n";
            $stSql .= "      publico.fn_nivel(                                                  \n";
            $stSql .= "         substr(cod_estrutural,1,1) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,2,1) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,3,2) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,5,2) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,7,2) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,9,2) || '.' ||                            \n";
            $stSql .= "         substr(cod_estrutural,11,2)                                     \n";
            $stSql .= "      ) as nnc                                                           \n";
            $stSql .= "  FROM                                                                   \n";
            $stSql .= "     samlink.vw_siam_elemento_despesa_2004                           \n";
            $stSql .= "  where trim(cod_estrutural) <> '' and cod_estrutural is not null        \n";
          //  $stSql .= "UNION                                                                    \n";
        }
      /*  $stSql .= "  SELECT DISTINCT                                                        \n";
        $stSql .= "     exercicio,                                                          \n";
        $stSql .= "     cod_estrutural,                                                     \n";
        $stSql .= "     'RUBRICA' as descricao,                                             \n";
        $stSql .= "     'A' as tnc,                                                         \n";
        $stSql .= "      publico.fn_nivel(                                                  \n";
        $stSql .= "         substr(cod_estrutural,1,1) || '.' ||                            \n";
        $stSql .= "         substr(cod_estrutural,2,1) || '.' ||                            \n";
        $stSql .= "         substr(cod_estrutural,3,2) || '.' ||                            \n";
        $stSql .= "         substr(cod_estrutural,5,2) || '.' ||                            \n";
        $stSql .= "         substr(cod_estrutural,7,2) || '.' ||                            \n";
        $stSql .= "         substr(cod_estrutural,9,2) || '.' ||                            \n";
        $stSql .= "         CASE WHEN substr(cod_estrutural,11,2) <> ''                     \n";
        $stSql .= "             THEN substr(cod_estrutural,11,2)                            \n";
        $stSql .= "             ELSE '00'                                                   \n";
        $stSql .= "         END                                                             \n";
        $stSql .= "      ) as nnc                                                           \n";
        $stSql .= "  FROM                                                                   \n";
        $stSql .= "     empenho.restos_pre_empenho                                      \n";
        $stSql .= "  WHERE cod_estrutural<>0 and trim(cod_estrutural) <>''                  \n";*/
        $stSql .= ") AS tabela                                                              \n";

        return $stSql;
    }

}

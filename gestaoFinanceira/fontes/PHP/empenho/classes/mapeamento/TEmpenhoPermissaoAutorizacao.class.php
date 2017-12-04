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
    * Classe de mapeamento da tabela EMPENHO.PERMISSAO_AUTORIZACAO
    * Data de Criação: 30/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: jose.eduardo $
    $Date: 2006-07-06 14:52:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-02.03.01, uc-02.03.02
*/

/*
$Log$
Revision 1.8  2006/07/06 17:52:37  jose.eduardo
Bug #6457#

Revision 1.7  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  EMPENHO.PERMISSAO_AUTORIZACAO
  * Data de Criação: 30/11/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoPermissaoAutorizacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEmpenhoPermissaoAutorizacao()
{
    parent::Persistente();
    $this->setTabela('empenho.permissao_autorizacao');

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm,num_unidade,num_orgao,exercicio');

    $this->AddCampo('numcgm','integer',true,'',true,true);
    $this->AddCampo('num_unidade','integer',true,'',true,true);
    $this->AddCampo('num_orgao','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'04',true,true);

}
/**
    * Monta consulta para recuperar orgaos da despesa cfe entidade que o usuario tem acesso
    * @access Private
    * @return String $stSql
*/
function montaRecuperaOrgaoDespesaEntidadeUsuario()
{
    $stSQL  = " SELECT DISTINCT                                                     \n";
    $stSQL .= "        oo.num_orgao                                                 \n";
    $stSQL .= "       ,oo.nom_orgao                                                 \n";
    $stSQL .= "       ,ue.cod_entidade                                              \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "        orcamento.despesa          as de                             \n";
    $stSQL .= "       ,orcamento.entidade         as en                             \n";
    $stSQL .= "       ,orcamento.usuario_entidade as ue                             \n";
    $stSQL .= "       ,orcamento.unidade          as ou                             \n";
    $stSQL .= "       ,empenho.permissao_autorizacao as pa                          \n";
    $stSQL .= "       ,orcamento.orgao            as oo                             \n";
    $stSQL .= " WHERE  de.exercicio = en.exercicio                                  \n";
    $stSQL .= "        AND de.cod_entidade = en.cod_entidade                        \n";
    $stSQL .= "        AND en.exercicio    = ue.exercicio                           \n";
    $stSQL .= "        AND en.cod_entidade = ue.cod_entidade                        \n";
    $stSQL .= "        AND de.exercicio    = ou.exercicio                           \n";
    $stSQL .= "        AND de.num_orgao    = ou.num_orgao                           \n";
    $stSQL .= "        AND de.num_unidade  = ou.num_unidade                         \n";
    $stSQL .= "        AND ou.exercicio    = pa.exercicio                           \n";
    $stSQL .= "        AND ou.num_orgao    = pa.num_orgao                           \n";
    $stSQL .= "        AND ou.num_unidade  = pa.num_unidade                         \n";
    $stSQL .= "        AND ou.exercicio    = oo.exercicio                           \n";
    $stSQL .= "        AND ou.num_orgao    = oo.num_orgao                           \n";

    return $stSQL;
}
/**
    * Recupera os Orgãos conforme a Entidade que o usuario pode acessar.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaOrgaoDespesaEntidadeUsuario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaOrgaoDespesaEntidadeUsuario().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
/**
    * Monta consulta para recuperar unidades da despesa cfe entidade que usuario pode acessar
    * @access Private
    * @return String $stSql
*/
function montaRecuperaUnidadeDespesaEntidadeUsuario()
{
    $stSQL  = " SELECT DISTINCT                                                     \n";
    $stSQL .= "        ou.num_orgao                                                 \n";
    $stSQL .= "       ,ou.num_unidade                                               \n";
    $stSQL .= "       ,ou.nom_unidade                                               \n";
    $stSQL .= "       ,ue.cod_entidade                                              \n";
    $stSQL .= " FROM                                                                \n";
    $stSQL .= "        orcamento.despesa          as de                             \n";
    $stSQL .= "       ,orcamento.entidade         as en                             \n";
    $stSQL .= "       ,orcamento.usuario_entidade as ue                             \n";
    $stSQL .= "       ,empenho.permissao_autorizacao as pa                          \n";
    $stSQL .= "       ,orcamento.unidade          as ou                             \n";
    $stSQL .= " WHERE  de.exercicio = en.exercicio                                  \n";
    $stSQL .= "        AND     de.cod_entidade = en.cod_entidade                    \n";
    $stSQL .= "        AND     en.exercicio    = ue.exercicio                       \n";
    $stSQL .= "        AND     en.cod_entidade = ue.cod_entidade                    \n";
    $stSQL .= "        AND     de.exercicio    = ou.exercicio                       \n";
    $stSQL .= "        AND     de.num_orgao    = ou.num_orgao                       \n";
    $stSQL .= "        AND     de.num_unidade  = ou.num_unidade                     \n";
    $stSQL .= "        AND     ou.exercicio    = pa.exercicio                       \n";
    $stSQL .= "        AND     ou.num_orgao    = pa.num_orgao                       \n";
    $stSQL .= "        AND     ou.num_unidade  = pa.num_unidade                     \n";

    return $stSQL;
}
/**
    * Recupera as Unidade conforme a Entidade que o usuario pode acessar.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaUnidadeDespesaEntidadeUsuario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $this->setDado( "stFiltro", $stCondicao );

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaUnidadeDespesaEntidadeUsuario().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

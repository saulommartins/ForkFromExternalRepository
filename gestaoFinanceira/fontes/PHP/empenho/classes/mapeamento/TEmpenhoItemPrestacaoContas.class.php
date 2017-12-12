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
    * Classe de mapeamento da tabela empenho.item_prestacao_contas
    * Data de Criação: 26/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.31
*/
/*
$Log$
Revision 1.4  2007/08/10 21:19:49  luciano
uc adiantamentos

Revision 1.3  2007/05/30 13:08:41  luciano
#9090#

Revision 1.2  2007/05/15 14:38:23  luciano
#9104#

Revision 1.1  2006/11/01 12:00:50  rodrigo
*** empty log message ***

Revision 1.2  2006/09/27 17:42:43  souzadl
correção

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  empenho.item_prestacao_contas
  * Data de Criação: 26/10/2006

  * @author Analista: Gelson
  * @author Desenvolvedor: Rodrigo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoItemPrestacaoContas extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
  public function TEmpenhoItemPrestacaoContas()
  {
    parent::Persistente();
    $this->setTabela("empenho.item_prestacao_contas");

    $this->setCampoCod('num_item');
    $this->setComplementoChave('exercicio,cod_entidade,cod_empenho');

    $this->AddCampo('num_item'           ,'sequence',true  ,''      ,true,false);
    $this->AddCampo('exercicio'          ,'char'    ,true  ,'4'     ,true,true);
    $this->AddCampo('cod_entidade'       ,'integer' ,true  ,''      ,true,true);
    $this->AddCampo('cod_empenho'        ,'integer' ,true  ,''      ,true,true);
    $this->AddCampo('exercicio_conta'    ,'char'    ,true  ,'4'     ,false,true,'exercicio');
    $this->AddCampo('conta_contrapartida','integer' ,true  ,''      ,false,true);
    $this->AddCampo('cod_documento'      ,'integer' ,true  ,''      ,false,true);
    $this->AddCampo('data_item'          ,'date'    ,true  ,''      ,false,false);
    $this->AddCampo('valor_item'         ,'numeric' ,false ,'14,2'  ,false,false);
    $this->AddCampo('justificativa'      ,'varchar' ,true  ,'80'    ,false,false);
    $this->AddCampo('num_documento'      ,'integer' ,true  ,''      ,false,false);
    $this->AddCampo('credor'             ,'varchar' ,true  ,'30'    ,false,false);
  }

  public function recuperaListagemPrestacao(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaListagemPrestacao().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaListagemPrestacao()
  {
    $stSql = "SELECT prestacao_contas.cod_empenho                                                   \n";
    $stSql.= "      ,prestacao_contas.cod_entidade                                                  \n";
    $stSql.= "      ,prestacao_contas.exercicio                                                     \n";
    $stSql.= "      ,TO_CHAR(prestacao_contas.data,'dd/mm/yyyy') AS data                            \n";
    $stSql.= "      ,eipc.num_item                                                                  \n";
    $stSql.= "      ,eipc.cod_documento                                                             \n";
    $stSql.= "      ,TO_CHAR(eipc.data_item,'dd/mm/yyyy') AS data_item                              \n";
    $stSql.= "      ,eipc.num_documento                                                             \n";
    $stSql.= "      ,eipc.credor                                                                    \n";
    $stSql.= "      ,eipc.justificativa                                                             \n";
    $stSql.= "      ,eipc.conta_contrapartida                                                       \n";
    $stSql.= "      ,eipc.exercicio_conta                                                           \n";
    $stSql.= "      ,eipc.valor_item                                                                \n";
    $stSql.= "  FROM empenho.prestacao_contas                                                       \n";
    $stSql.= "      ,empenho.item_prestacao_contas as eipc                                          \n";
    $stSql.= " WHERE prestacao_contas.cod_entidade = eipc.cod_entidade                              \n";
    $stSql.= "   AND prestacao_contas.cod_empenho  = eipc.cod_empenho                               \n";
    $stSql.= "   AND prestacao_contas.exercicio    = eipc.exercicio                                 \n";
    $stSql.= "   AND NOT EXISTS ( SELECT num_item                                                   \n";
    $stSql.= "                    FROM empenho.item_prestacao_contas_anulado                        \n";
    $stSql.= "                    WHERE exercicio = eipc.exercicio                                  \n";
    $stSql.= "                          AND cod_empenho  = eipc.cod_empenho                         \n";
    $stSql.= "                          AND cod_entidade = eipc.cod_entidade                        \n";
    $stSql.= "                          AND num_item     = eipc.num_item                            \n";
    $stSql.= "                    )                                                                 \n";
    $stSql.= "   AND prestacao_contas.cod_entidade = ".$this->getDado('cod_entidade')."             \n";
    $stSql.= "   AND prestacao_contas.cod_empenho  = ".$this->getDado('cod_empenho')."              \n";
    $stSql.= "   AND prestacao_contas.exercicio    = '".$this->getDado('exercicio')."'              \n";

    return $stSql;
  }

  public function recuperaValorPrestado(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaValorPrestado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaValorPrestado()
  {
    $stSql .= " SELECT                                                                                   \n";
    $stSql .= "     empenho.fn_consultar_valor_prestado_nao_anulado( '".$this->getDado('exercicio')."'   \n";
    $stSql .= "                                                      ,".$this->getDado('cod_empenho')."  \n";
    $stSql .= "                                                      ,".$this->getDado('cod_entidade')." \n";
    $stSql .= "                                        ) as vl_prestado                                  \n";

    return $stSql;
  }

  public function recuperaValorPrestar(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaValorPrestar().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaValorPrestar()
  {
    $stSql .= " SELECT                                                                       \n";
    $stSql .= "     empenho.fn_consultar_valor_prestar( '".$this->getDado('exercicio')."'    \n";
    $stSql .= "                                         ,".$this->getDado('cod_empenho')."   \n";
    $stSql .= "                                         ,".$this->getDado('cod_entidade')."  \n";
    $stSql .= "                                       ) as vl_prestar                        \n";

    return $stSql;
  }

}
?>

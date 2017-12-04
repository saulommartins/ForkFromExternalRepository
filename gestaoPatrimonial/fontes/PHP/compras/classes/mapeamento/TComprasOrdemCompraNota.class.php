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
    * Classe de mapeamento da tabela compras.ordem_compra_nota
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18921 $
    $Name$
    $Author: thiago $
    $Date: 2006-12-21 13:16:27 -0200 (Qui, 21 Dez 2006) $

    * Casos de uso: uc-03.04.29
*/

/*
$Log$
Revision 1.12  2006/12/21 15:16:27  thiago
alterações necessárias para serem usadas no UC 03-04.29

Revision 1.11  2006/12/20 16:32:51  fernando
filtros para Pk da nota de compra

Revision 1.10  2006/12/20 11:53:55  thiago
Métodos para ser utilizados no UC 03.04.29

Revision 1.9  2006/12/15 14:56:37  thiago
componente de ordem de compra, diferenciando se tem ou não nota de compra.

Revision 1.8  2006/12/13 17:16:55  thiago
 nota de compra

Revision 1.7  2006/12/11 18:38:40  fernando
IpopUpOrdemCompra

Revision 1.6  2006/12/11 14:55:16  thiago
funções para nota da ordem de compra

Revision 1.5  2006/12/08 18:01:25  thiago
funções para nota da ordem de compra

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.ordem_compra_nota
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasOrdemCompraNota extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasOrdemCompraNota()
{
    parent::Persistente();
    $this->setTabela("compras.ordem_compra_nota");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_ordem');

    $this->AddCampo('exercicio','CHAR(4)',true,'',true,true);
    $this->AddCampo('cod_entidade','INTEGER',true,'',true,true);
    $this->AddCampo('cgm_fornecedor','INTEGER',true,'',false,true);
    $this->AddCampo('cod_nota','INTEGER',true,'',false,true);
    $this->AddCampo('cod_ordem','INTEGER',true,'',true,true);
}

  public function pesquisaNF(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaPesquisaNF().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }
  public function montaPesquisaNF()
  {
    $stSql  ="SELECT                                            \n";
    $stSql .="      *                                           \n";
    $stSql .="FROM                                              \n";
    $stSql .="      compras.ordem_compra_nota as ocn                  \n";
    $stSql .="WHERE                                             \n";
    if ($this->getDado('cod_ordem')) {
        $stSql .=" ocn.cod_ordem = ".$this->getDado('cod_ordem')."\n";
    }
    if ($this->getDado('exercicio')) {
        $stSql .=" AND ocn.exercicio = '".$this->getDado('exercicio')."'\n";
    }
    if ($this->getDado('cod_entidade')) {
        $stSql .=" AND ocn.cod_entidade =".$this->getDado('cod_entidade')."\n";
    }

    return $stSql;
  }

  public function recuperaDadosNota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDadosNota().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }
  public function montaRecuperaDadosNota()
  {
    $stSql  ="SELECT                                            \n";
    $stSql .="      oc.cod_ordem                                \n";
    $stSql .="     ,oc.cod_empenho                              \n";
    $stSql .="     ,oc.cod_entidade                             \n";
    $stSql .="     ,oc.exercicio                                \n";
    $stSql .="     ,oc.exercicio_empenho                        \n";
    $stSql .="     ,sw.nom_cgm                                  \n";
    $stSql .="     ,sw.numcgm                                   \n";
    $stSql .="     ,nf.num_nota                                 \n";
    $stSql .="     ,nf.num_serie                                \n";
    $stSql .="FROM                                              \n";
    $stSql .="      compras.ordem as oc                  \n";
    $stSql .="     ,sw_cgm               as sw                  \n";
    $stSql .="     ,empenho.empenho      as emp                 \n";
    $stSql .="     ,empenho.pre_empenho  as pre                 \n";
    $stSql .="     ,compras.nota_fiscal  as nf                  \n";
    $stSql .="WHERE                                             \n";
    $stSql .="        oc.cod_empenho = emp.cod_empenho                     \n";
    $stSql .="    and oc.cod_entidade = emp.cod_entidade                   \n";
    $stSql .="    and oc.exercicio_empenho = emp.exercicio        \n";

    $stSql .="    and emp.exercicio = pre.exercicio        \n";
    $stSql .="    and emp.cod_pre_empenho = pre.cod_pre_empenho        \n";

    $stSql .="    and pre.cgm_beneficiario = nf.cgm_fornecedor        \n";
    $stSql .="    and pre.cgm_beneficiario = sw.numcgm        \n";

    if ($this->getDado('cod_ordem')) {
    $stSql .=" and oc.cod_ordem =".$this->getDado('cod_ordem')."\n";
    }
//segunda etapa
    if ($this->getDado('cod_entidade')) {
    $stSql .=" and oc.cod_entidade =".$this->getDado('cod_entidade')."\n";
    }
    if ($this->getDado('exercicio')) {
    $stSql .=" and oc.exercicio ='".$this->getDado('exercicio')."'\n";
    }
    if ($this->getDado('cod_empenho')) {
    $stSql .=" and oc.cod_empenho =".$this->getDado('cod_empenho')."\n";
    }
    if ($this->getDado('num_nota')) {
    $stSql .=" and nf.num_nota =".$this->getDado('num_nota')."\n";
    $stSql .=" and nf.cgm_fornecedor =sw.numcgm                \n";
    }

    return $stSql;
  }

  public function recuperaCodNota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCodNota().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
//    echo $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

  public function montaRecuperaCodNota()
  {
    $stSql  ="SELECT                   \n";
    $stSql .="     cod_nota            \n";
    $stSql .="FROM                     \n";
    $stSql .="     compras.nota_fiscal \n";
    $stSql .="WHERE                    \n";
    if ($this->getDado('num_serie')) {
    $stSql .=" num_serie =".$this->getDado('num_serie')."\n";
    }
    if ($this->getDado('num_nota')) {
    $stSql .=" and num_nota =".$this->getDado('num_nota')."\n";
    }

    return $stSql;
  }

  public function recuperaItensNota(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaItensNota().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
//    echo $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

  public function montaRecuperaItensNota()
  {
    $stSql  ="SELECT                                              \n";
    $stSql .="      ipre.nom_item                                 \n";
    $stSql .="     ,ipre.quantidade as quantidade_emp             \n";
    $stSql .="     ,acc.descricao                                 \n";
    $stSql .="     ,oci.quantidade as quantidade_oc               \n";
    $stSql .="     ,ipre.vl_total                                 \n";
    $stSql .="FROM                                                \n";

    $stSql .="      compras.ordem      as oc              \n";
    $stSql .="      ,empenho.empenho           as emp             \n";
    $stSql .="      ,empenho.pre_empenho       as pre             \n";
    $stSql .="      ,empenho.item_pre_empenho  as ipre            \n";
    $stSql .="      ,almoxarifado.centro_custo_entidade as acce   \n";
    $stSql .="      ,almoxarifado.centro_custo as acc             \n";
    $stSql .="      ,compras.ordem_item as oci             \n";
    $stSql .="WHERE                                               \n";
    $stSql .="        oc.exercicio_empenho = emp.exercicio        \n";
    $stSql .="    and oc.cod_entidade      = emp.cod_entidade     \n";
    $stSql .="    and oc.cod_empenho       = emp.cod_empenho      \n";
    $stSql .="    and emp.exercicio        = pre.exercicio        \n";
    $stSql .="    and emp.cod_pre_empenho  = pre.cod_pre_empenho  \n";
    $stSql .="    and pre.cod_pre_empenho  = ipre.cod_pre_empenho \n";
    $stSql .="    and pre.exercicio        = ipre.exercicio       \n";
    $stSql .="    and oc.exercicio         = acce.exercicio       \n";
    $stSql .="    and oc.cod_entidade      = acce.cod_entidade    \n";
    $stSql .="    and acce.cod_centro      = acc.cod_centro       \n";

    $stSql .="    and oci.cod_entidade     = oc.cod_entidade      \n";
    $stSql .="    and oci.cod_ordem        = oc.cod_ordem         \n";
    $stSql .="    and oci.exercicio        = oc.exercicio_empenho \n";
    $stSql .="    and oci.cod_pre_empenho  = pre.cod_pre_empenho  \n";

    if ($this->getDado('cod_ordem')) {
    $stSql .=" and oc.cod_ordem =".$this->getDado('cod_ordem')."\n";
    }

    if ($this->getDado('exercicio')) {
    $stSql .=" and oc.exercicio =".$this->getDado('exercicio')."\n";
    }

    if ($this->getDado('cod_entidade')) {
    $stSql .=" and oc.cod_entidade =".$this->getDado('cod_entidade')."\n";
    }

    if ($this->getDado('exercicio_empenho')) {
    $stSql .=" and oc.exercicio_empenho =".$this->getDado('exercicio_empenho')."\n";
    }

    if ($this->getDado('cod_empenho')) {
    $stSql .=" and oc.cod_empenho =".$this->getDado('cod_empenho')." \n";
    }

    return $stSql;
  }

  public function recuperaNotas(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNotas().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
//    echo $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
  }

    public function montaRecuperaNotas()
    {
      $stSql  ="SELECT                                              \n";
      $stSql .="      nf.num_nota                                   \n";
      $stSql .="     ,to_char(nf.dt_nota,'dd/mm/yyyy') as dt_nota   \n";
      $stSql .="     ,ipre.vl_total                                 \n";
      $stSql .="     ,oc.cod_ordem                                  \n";
      $stSql .="     ,oc.cod_empenho                                \n";
      $stSql .="     ,oc.cod_entidade                               \n";
      $stSql .="     ,oc.exercicio                                  \n";
      $stSql .="FROM                                                \n";
      $stSql .="                compras.ordem_compra_nota as ocn    \n";
      $stSql .="   natural join compras.nota_fiscal       as nf     \n";
      $stSql .="   natural join compras.ordem      as oc     \n";
      $stSql .="   natural join empenho.item_pre_empenho  as ipre   \n";
      $stSql .="   natural join empenho.pre_empenho       as pre    \n";
      $stSql .="   natural join empenho.empenho           as emp    \n";
      if ($this->getDado('stAcao')== 'excluir') {
        $stSql .=" ,empenho.nota_liquidacao as enl      \n";
      }
      $stSql .="WHERE                                               \n";
      $stSql .="      ocn.cgm_fornecedor = nf.cgm_fornecedor        \n";
      $stSql .="  and ocn.cod_nota       = nf.cod_nota              \n";

      $stSql .="  and ocn.cod_entidade   = oc.cod_entidade          \n";
      $stSql .="  and ocn.cod_ordem      = oc.cod_ordem             \n";
      $stSql .="  and ocn.exercicio      = oc.exercicio             \n";

      $stSql .="  and ipre.cod_pre_empenho = pre.cod_pre_empenho    \n";
      $stSql .="  and ipre.exercicio       = pre.exercicio          \n";

      $stSql .="  and pre.exercicio        = emp.exercicio          \n";
      $stSql .="  and pre.cod_pre_empenho  = emp.cod_pre_empenho    \n";

      $stSql .="  and emp.cod_empenho = oc.cod_empenho               \n";
      $stSql .="  and emp.cod_entidade = oc.cod_entidade              \n";

      if ($this->getDado('cod_ordem')) {
          $stSql .=" and oc.cod_ordem =".$this->getDado('cod_ordem')." \n";
      }

      if ($this->getDado('cod_empenho')) {
          $stSql .=" and oc.cod_empenho =".$this->getDado('cod_empenho')." \n";
      }

      if ($this->getDado('num_nota')) {
          $stSql .=" and nf.num_nota =".$this->getDado('num_nota')." \n";
      }

      if ($this->getDado('stDataInicial')) {
        $stSql .= "  AND nf.dt_nota BETWEEN TO_DATE('".$this->getDado('stDataInicial')."','dd/mm/yyyy')   \n";
        $stSql .= "  AND TO_DATE('".$this->getDado('stDataFinal')."','dd/mm/yyyy')                                   \n";
      }

      if ($this->getDado('stAcao')== 'excluir') {
        $stSql .="    and enl.cod_empenho = emp.cod_empenho          \n";
        $stSql .="    and enl.exercicio_empenho= emp.exercicio       \n";
        $stSql .="    and enl.cod_entidade = emp.cod_entidade        \n";
        $stSql .="    and enl.cod_nota = nf.cod_nota         \n";
      }
      $stSql .="   ORDER BY nf.num_nota    \n";

    return $stSql;
    }

    public function recuperaOrdemCompra(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaOrdemCompra().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaOrdemCompra()
    {
        $stSql.=" SELECT                            \n";
        $stSql.="     oc.cod_ordem                  \n";
        $stSql.="     ,oc.cod_entidade              \n";
        $stSql.="     ,oc.exercicio                 \n";
        $stSql.="     ,oc.cod_empenho||'/'||oc.exercicio_empenho as empenho    \n";
        $stSql.=" FROM                              \n";
        $stSql.="      compras.ordem as oc   \n";
        $stSql.="      LEFT JOIN compras.ordem_compra_nota as ocn on ( oc.cod_ordem = ocn.cod_ordem  AND oc.cod_entidade = ocn.cod_entidade AND oc.exercicio = ocn.exercicio  ) \n";
        $stSql.=" WHERE                             \n";
        $stSql.="         ocn.cod_nota is null      \n";
        if ($this->getDado('exercicio'))
            $stSql.="     AND oc.exercicio = '".$this->getDado('exercicio')."'     \n";
        if ($this->getDado('cod_entidade'))
            $stSql.="     AND oc.cod_entidade = ".$this->getDado('cod_entidade')." \n";
        if ($this->getDado('cod_ordem'))
            $stSql.="     AND oc.cod_ordem = ".$this->getDado('cod_ordem')."       \n";

        return $stSql;
    }

    public function recuperaOrdemCompraNF(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
      $obErro      = new Erro;
      $obConexao   = new Conexao;
      $rsRecordSet = new RecordSet;
      $stSql = $this->montaRecuperaOrdemCompraNF().$stFiltro.$stOrdem;
      $this->stDebug = $stSql;
      $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaOrdemCompraNF()
    {
        $stSql.=" SELECT                            \n";
        $stSql.="     oc.cod_ordem                  \n";
        $stSql.="     ,oc.cod_entidade              \n";
        $stSql.="     ,oc.exercicio                 \n";
        $stSql.="     ,oc.cod_empenho||'/'||oc.exercicio_empenho as empenho    \n";
        $stSql.=" FROM                              \n";
        $stSql.="      compras.ordem as oc   \n";
        $stSql.="      LEFT JOIN compras.ordem_compra_nota as ocn on ( oc.cod_ordem = ocn.cod_ordem  AND oc.cod_entidade = ocn.cod_entidade AND oc.exercicio = ocn.exercicio  ) \n";
        $stSql.=" WHERE                             \n";
//        $stSql.="         ocn.cod_nota is null      \n";
        if ($this->getDado('exercicio'))
            $stSql.="     oc.exercicio = '".$this->getDado('exercicio')."'     \n";
        if ($this->getDado('cod_entidade'))
            $stSql.="     AND oc.cod_entidade = ".$this->getDado('cod_entidade')." \n";
        if ($this->getDado('cod_ordem'))
            $stSql.="     AND oc.cod_ordem = ".$this->getDado('cod_ordem')."       \n";

        return $stSql;
    }

}

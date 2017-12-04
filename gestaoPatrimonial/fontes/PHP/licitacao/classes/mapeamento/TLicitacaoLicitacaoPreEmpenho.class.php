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
    * Classe de mapeamento da tabela licitacao.licitacao_pre_empenho
    * Data de Criação: 20/11/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Alessandro La-Rocca Silveira

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18411 $
    $Name$
    $Author: fernando $
    $Date: 2006-11-30 15:18:21 -0200 (Qui, 30 Nov 2006) $

    * Casos de uso: uc-03.05.16
*/
/*
$Log$
Revision 1.2  2006/11/30 17:18:21  fernando
funcção para recuperar o numero da licitacao atráves do número do empenho

Revision 1.1  2006/11/23 16:23:53  larocca
Inclusão do mapeamento

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoLicitacaoPreEmpenho extends Persistente
{
  /**
      * Método Construtor
      * @access Private
  */
  public function TLicitacaoLicitacaoPreEmpenho()
  {
    parent::Persistente();
    $this->setTabela("licitacao.licitacao_pre_empenho");

    $this->setComplementoChave('cod_pre_empenho','cod_entidade, cod_modalidade, cod_licitacao, exercicio_licitacao, exercicio_pre_empenho');
    $this->AddCampo('cod_pre_empenho'         ,'integer' ,true  ,''   ,true ,'TEmpenhoEmpenho');
    $this->AddCampo('cod_entidade'            ,'integer' ,true  ,''   ,true ,'TLicitacaoLicitacao');
    $this->AddCampo('cod_modalidade'          ,'integer' ,true  ,''   ,true ,'TLicitacaoLicitacao');
    $this->AddCampo('cod_licitacao'           ,'integer' ,true  ,''   ,true ,'TLicitacaoLicitacao');
    $this->AddCampo('exercicio_licitacao'     ,'char'    ,true  ,'4'  ,true ,'TLicitacaoLicitacao' ,'exercicio');
    $this->AddCampo('exercicio_pre_empenho'   ,'char'    ,true  ,'4'  ,true ,'TEmpenhoEmpenho','exercicio');
  }

  public function recuperaListaLicitacaoPreEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaLicitacaoPreEmpenho().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaListaLicitacaoPreEmpenho()
  {
    $stSql  = " SELECT                                                                                      \n";
    $stSql .= "        licitacao_pre_empenho.cod_pre_empenho                                                \n";
    $stSql .= "      , licitacao_pre_empenho.exercicio_pre_empenho                                          \n";
    $stSql .= "      , licitacao_pre_empenho.exercicio_licitacao                                            \n";
    $stSql .= "      , licitacao_pre_empenho.cod_entidade                                                   \n";
    $stSql .= "      , licitacao_pre_empenho.cod_modalidade                                                 \n";
    $stSql .= "      , licitacao_pre_empenho.cod_licitacao                                                  \n";
    $stSql .= "      , modalidade.descricao AS nom_modalidade                                               \n";
    $stSql .= "      , sw_cgm.nom_cgm       AS nom_entidade                                                 \n";
    $stSql .= "   FROM                                                                                      \n";
    $stSql .= "        licitacao.licitacao_pre_empenho                                                      \n";
    $stSql .= "        INNER JOIN empenho.empenho                                                           \n";
    $stSql .= "                ON empenho.cod_pre_empenho     = licitacao_pre_empenho.cod_pre_empenho       \n";
    $stSql .= "               AND empenho.exercicio           = licitacao_pre_empenho.exercicio_pre_empenho \n";
    $stSql .= "               AND empenho.cod_entidade        = licitacao_pre_empenho.cod_entidade          \n";
    $stSql .= "        INNER JOIN licitacao.licitacao                                                       \n";
    $stSql .= "                ON licitacao.cod_licitacao     = licitacao_pre_empenho.cod_licitacao         \n";
    $stSql .= "               AND licitacao.exercicio         = licitacao_pre_empenho.exercicio_licitacao   \n";
    $stSql .= "               AND licitacao.cod_entidade      = licitacao_pre_empenho.cod_entidade          \n";
    $stSql .= "               AND licitacao.cod_modalidade    = licitacao_pre_empenho.cod_modalidade        \n";
    $stSql .= "        INNER JOIN orcamento.entidade                                                        \n";
    $stSql .= "                ON entidade.cod_entidade       = licitacao_pre_empenho.cod_entidade          \n";
    $stSql .= "               AND entidade.exercicio          = licitacao_pre_empenho.exercicio_pre_empenho \n";
    $stSql .= "        INNER JOIN compras.modalidade                                                        \n";
    $stSql .= "                ON modalidade.cod_modalidade   = licitacao_pre_empenho.cod_modalidade        \n";
    $stSql .= "        INNER JOIN sw_cgm                                                                    \n";
    $stSql .= "                ON sw_cgm.numcgm               = entidade.numcgm                             \n";

    return $stSql;

  }

  public function recuperaLicitacaoPorEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaLicitacaoPorEmpenho().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaLicitacaoPorEmpenho()
  {
      $stSql .="SELECT                                                    \n";
      $stSql .="     emp.cod_empenho                                      \n";
      $stSql .="    ,pre.cod_pre_empenho                                  \n";
      $stSql .="    ,lpre.cod_licitacao                                   \n";
      $stSql .="    ,emp.exercicio                                        \n";
      $stSql .="FROM                                                      \n";
      $stSql .="     empenho.empenho as emp                               \n";
      $stSql .="    ,empenho.pre_empenho as pre                           \n";
      $stSql .="    ,licitacao.licitacao_pre_empenho as lpre              \n";
      $stSql .="WHERE                                                     \n";
      $stSql .="        emp.exercicio       = pre.exercicio               \n";
      $stSql .="    AND emp.cod_pre_empenho = pre.cod_pre_empenho         \n";
      $stSql .="    AND pre.cod_pre_empenho = lpre.cod_pre_empenho        \n";
      $stSql .="    AND pre.exercicio       = lpre.exercicio_pre_empenho  \n";

     if ($this->getDado('cod_empenho'))
        $stSql .="    AND emp.cod_empenho = ".$this->getDado('cod_empenho')."\n";
     if ($this->getDado('cod_entidade'))
        $stSql .="    AND emp.cod_entidade = ".$this->getDado('cod_entidade')."\n";
     if ($this->getDado('exercicio'))
        $stSql .="    AND emp.exercicio = '".$this->getDado('exercicio')."'\n";

      return $stSql;

  }

}

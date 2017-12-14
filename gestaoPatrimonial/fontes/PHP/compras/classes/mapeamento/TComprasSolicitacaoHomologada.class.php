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
    * Classe de mapeamento da tabela compras.solicitacao_homologada
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 29118 $
    $Name$
    $Author: luiz $
    $Date: 2008-04-10 15:32:35 -0300 (Qui, 10 Abr 2008) $

    * Casos de uso: uc-03.04.02
*/

/*
$Log$
Revision 1.6  2006/12/12 12:43:31  rodrigo
7777,7772

Revision 1.5  2006/10/04 16:58:55  bruce
criado metodo para retornar os itens da solicitação junto com o saldo das dotações escolhidas

Revision 1.4  2006/10/03 10:49:50  bruce
Colocado número de UC

Revision 1.3  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.solicitacao_homologada
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacaoHomologada extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
  public function TComprasSolicitacaoHomologada()
  {
        parent::Persistente();
        $this->setTabela("compras.solicitacao_homologada");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao');

        $this->AddCampo('exercicio','CHAR(4)',true,'',true,true);
        $this->AddCampo('cod_entidade','INTEGER',true,'',true,true);
        $this->AddCampo('cod_solicitacao','INTEGER',true,'',true,true);
        $this->AddCampo('numcgm','INTEGER',true,'',false,true);
        $this->AddCampo('timestamp','TIMESTAMP',false,'',false,false);
  }

  public function recuperaboHomologada(&$rsRecordSet, $stFiltro)
  {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaboHomologada(). $stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
  }

  public function montaRecuperaboHomologada()
  {
        $stSql .= "SELECT exercicio ,
                          cod_entidade ,
                          cod_solicitacao ,
                          numcgm ,
                          timestamp
                     FROM compras.solicitacao_homologada
                    WHERE exercicio       = '". $this->getDado('exercicio') . "'
                      AND cod_entidade    = ". $this->getDado('cod_entidade')."
                      AND cod_solicitacao = ". $this->getDado('cod_solicitacao')."\n ";

        return $stSql.$stFiltro;

    }

  public function recupraSaldoDotacaoItensSolicitacao(&$rsRecordSet, $stFiltro = '')
  {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecupraSaldoDotacaoItensSolicitacao(). $stFiltro;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
  }

  public function montaRecupraSaldoDotacaoItensSolicitacao()
  {
        $stSql .= "select * from (                                                                                      \n";
        $stSql .= "               select solicitacao_item.cod_item                                                      \n";
        $stSql .= "                     ,solicitacao_item_dotacao.cod_centro                                            \n";
        $stSql .= "                     ,solicitacao_item_dotacao.cod_conta                                             \n";
        $stSql .= "                     ,solicitacao_item_dotacao.exercicio                                             \n";
        $stSql .= "                     ,empenho.fn_saldo_dotacao(solicitacao_item_dotacao.exercicio,                   \n";
        $stSql .= "                                               solicitacao_item_dotacao.cod_despesa) as saldo        \n";
        $stSql .= "               from compras.solicitacao                                                              \n";
        $stSql .= "               join compras.solicitacao_item                                                         \n";
        $stSql .= "                   on( solicitacao.exercicio      = solicitacao_item.exercicio                       \n";
        $stSql .= "                  and solicitacao.cod_entidade    = solicitacao_item.cod_entidade                    \n";
        $stSql .= "                  and solicitacao.cod_solicitacao = solicitacao_item.cod_solicitacao)                \n";
        $stSql .= "               join compras.solicitacao_item_dotacao                                                 \n";
        $stSql .= "                   on(solicitacao_item.exercicio        = solicitacao_item_dotacao.exercicio         \n";
        $stSql .= "                  and solicitacao_item.cod_entidade     = solicitacao_item_dotacao.cod_entidade      \n";
        $stSql .= "                  and solicitacao_item.cod_solicitacao  = solicitacao_item_dotacao.cod_solicitacao   \n";
        $stSql .= "                  and solicitacao_item.cod_item         = solicitacao_item_dotacao.cod_item          \n";
        $stSql .= "                  and solicitacao_item.cod_centro       = solicitacao_item_dotacao.cod_centro)       \n";
        $stSql .= "               where solicitacao_item.cod_solicitacao   = " . $this->getDado( 'cod_solicitacao' ) . "\n";
        $stSql .= "                        solicitacao_item.cod_entidade    = " . $this->getDado( 'cod_entidade')    . "\n";
        $stSql .= "                        solicitacao_item.exercicio       = '" . $this->getDado( 'exercicio')       . "' \n";
        $stSql .= "          ) as saldos                                                                                \n";
    }

    public function recuperaPermissaoAnularHomologacao(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPermissaoAnularHomologacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPermissaoAnularHomologacao()
    {
        $stSql  = "         SELECT  CASE WHEN COUNT(1) > 0 THEN 'true'                                                 \n";
        $stSql .= "                      ELSE 'false'                                                                  \n";
        $stSql .= "                 END as permissao_excluir                                                           \n";
        $stSql .= "           FROM  compras.solicitacao                                                                \n";
        $stSql .= "          WHERE  solicitacao.cod_solicitacao = ".$this->getDado('cod_solicitacao')."                \n";
        $stSql .= "            AND  solicitacao.cod_entidade    = ".$this->getDado('cod_entidade')."                   \n";
        $stSql .= "            AND  solicitacao.exercicio       ='".$this->getDado('exercicio')."'                     \n";
        $stSql .= "            AND                                                                                     \n";
        $stSql .= "             (                                                                                      \n";
        $stSql .= "                 NOT EXISTS                                                                         \n";
        $stSql .= "                     (                                                                              \n";
        $stSql .= "                         SELECT  1                                                                  \n";
        $stSql .= "                           FROM  compras.mapa_solicitacao                                           \n";
        $stSql .= "                          WHERE  mapa_solicitacao.cod_entidade    = solicitacao.cod_entidade        \n";
        $stSql .= "                            AND  mapa_solicitacao.exercicio       = solicitacao.exercicio           \n";
        $stSql .= "                            AND  mapa_solicitacao.cod_solicitacao = solicitacao.cod_solicitacao     \n";
        $stSql .= "                     )                                                                              \n";
        $stSql .= "                 OR                                                                                 \n";
        $stSql .= "                 (                                                                                  \n";
        $stSql .= "                     (                                                                              \n";
        $stSql .= "                         SELECT  coalesce(SUM(mapa_item_anulacao.quantidade),0)                     \n";
        $stSql .= "                           FROM  compras.mapa_item_anulacao                                         \n";
        $stSql .= "                          WHERE  mapa_item_anulacao.exercicio       = solicitacao.exercicio         \n";
        $stSql .= "                            AND  mapa_item_anulacao.cod_entidade    = solicitacao.cod_entidade      \n";
        $stSql .= "                            AND  mapa_item_anulacao.cod_solicitacao = solicitacao.cod_solicitacao   \n";
        $stSql .= "                     )                                                                              \n";
        $stSql .= "                     -                                                                              \n";
        $stSql .= "                     (                                                                              \n";
        $stSql .= "                         SELECT  coalesce(SUM(mapa_item.quantidade),0)                              \n";
        $stSql .= "                           FROM  compras.mapa_item                                                  \n";
        $stSql .= "                          WHERE  mapa_item.exercicio       = solicitacao.exercicio                  \n";
        $stSql .= "                            AND  mapa_item.cod_entidade    = solicitacao.cod_entidade               \n";
        $stSql .= "                            AND  mapa_item.cod_solicitacao = solicitacao.cod_solicitacao            \n";
        $stSql .= "                     )                                                                              \n";
        $stSql .= "                 ) = 0                                                                              \n";
        $stSql .= "             )                                                                                      \n";

        return $stSql;
    }

    public function recuperaHomologacaoSemMapa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaHomologacaoSemMapa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaHomologacaoSemMapa()
    {
        $stSql = " SELECT 1 as EXCLUIR \n ";
        $stSql .="   FROM compras.solicitacao \n ";
        $stSql .="  WHERE solicitacao.cod_solicitacao =".$this->getDado('cod_solicitacao')."\n ";
        $stSql .="    AND solicitacao.cod_entidade =".$this->getDado('cod_entidade')."\n ";
        $stSql .="    AND solicitacao.exercicio ='".$this->getDado('exercicio')."'\n ";
        $stSql .="    AND not exists( SELECT 1 \n ";
        $stSql .="                     FROM compras.mapa_solicitacao \n ";
        $stSql .="                    WHERE mapa_solicitacao.cod_solicitacao =".$this->getDado('cod_solicitacao')."\n ";
        $stSql .="                      AND mapa_solicitacao.cod_entidade =".$this->getDado('cod_entidade')."\n ";
        $stSql .="                      AND mapa_solicitacao.exercicio ='".$this->getDado('exercicio')."')\n ";

        return $stSql;
    }

    public function verificaExistenciaHomologacaoInclusa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaVerificaExistenciaHomologacaoInclusa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaVerificaExistenciaHomologacaoInclusa()
    {
        $stSql = " SELECT 1 as EXISTE \n ";
        $stSql .="   FROM compras.solicitacao_homologada \n ";
        $stSql .="  WHERE solicitacao_homologada.cod_solicitacao =".$this->getDado('cod_solicitacao')."\n ";
        $stSql .="    AND solicitacao_homologada.cod_entidade =".$this->getDado('cod_entidade')."\n ";
        $stSql .="    AND solicitacao_homologada.exercicio ='".$this->getDado('exercicio')."'\n ";

        return $stSql;
    }

    # Método que retorna somente solicitações que estejam homologadas e não tenham anulações.
    public function recuperaSolicitacaoHomologadaNaoAnulada(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaSolicitacaoHomologadaNaoAnulada().$stFiltro.$stOrder;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSolicitacaoHomologadaNaoAnulada()
    {
        $stSQL  = "     SELECT  solicitacao_homologada.*                                                                              \n";
        $stSQL .= "                                                                                                                   \n";
        $stSQL .= "       FROM  compras.solicitacao                                                                                   \n";
        $stSQL .= "                                                                                                                   \n";
        $stSQL .= " INNER JOIN  compras.solicitacao_homologada                                                                        \n";
        $stSQL .= "         ON  solicitacao_homologada.exercicio       = solicitacao.exercicio                                        \n";
        $stSQL .= "        AND  solicitacao_homologada.cod_solicitacao = solicitacao.cod_solicitacao                                  \n";
        $stSQL .= "        AND  solicitacao_homologada.cod_entidade    = solicitacao.cod_entidade                                     \n";
        $stSQL .= "                                                                                                                   \n";
        $stSQL .= "      WHERE  1=1                                                                                                   \n";
        $stSQL .= "                                                                                                                   \n";
        $stSQL .= "        AND  NOT EXISTS                                                                                            \n";
        $stSQL .= "             (                                                                                                     \n";
        $stSQL .= "                 SELECT  1                                                                                         \n";
        $stSQL .= "                   FROM  compras.solicitacao_homologada_anulacao                                                   \n";
        $stSQL .= "                  WHERE  solicitacao_homologada_anulacao.exercicio       = solicitacao_homologada.exercicio        \n";
        $stSQL .= "                    AND  solicitacao_homologada_anulacao.cod_entidade    = solicitacao_homologada.cod_entidade     \n";
        $stSQL .= "                    AND  solicitacao_homologada_anulacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao  \n";
        $stSQL .= "             )                                                                                                     \n";

        if ($this->getDado('exercicio')) {
            $stSQL .= " AND  solicitacao_homologada.exercicio = '".$this->getDado('exercicio')."' \n";
        }

        if ($this->getDado('cod_solicitacao')) {
            $stSQL .= " AND  solicitacao_homologada.cod_solicitacao = ".$this->getDado('cod_solicitacao')." \n";
        }

        if ($this->getDado('cod_entidade')) {
            $stSQL .= " AND  solicitacao_homologada.cod_entidade = ".$this->getDado('cod_entidade')." \n";
        }

        return $stSQL;
    }

}

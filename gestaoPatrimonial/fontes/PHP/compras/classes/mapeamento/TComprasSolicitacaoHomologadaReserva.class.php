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
  * Classe de mapeamento da tabela compras.solicitacao_homologada_reserva
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento

  * Casos de uso: uc-03.04.02

  $Id: TComprasSolicitacaoHomologadaReserva.class.php 63833 2015-10-22 13:05:17Z franver $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  compras.solicitacao_homologada_reserva
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacaoHomologadaReserva extends Persistente
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TComprasSolicitacaoHomologadaReserva()
    {
        parent::Persistente();
        $this->setTabela("compras.solicitacao_homologada_reserva");

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao,cod_centro,cod_item,cod_reserva');

        $this->AddCampo('exercicio'        ,'CHAR(4)', true, '', true,  true);
        $this->AddCampo('cod_entidade'     ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_solicitacao'  ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_centro'       ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_item'         ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_reserva'      ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_conta'        ,'INTEGER', true, '', true,  true);
        $this->AddCampo('cod_despesa'      ,'INTEGER', true, '', true,  true);
    }

    public function recuperaTodosNomEntidade(&$rsRecordSet, $stFiltro, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTodosNomEntidade().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
    }

    public function montaRecuperaTodosNomEntidade()
    {
        $stSql .= "
                  SELECT  solicitacao_homologada_reserva.exercicio
                       ,  solicitacao_homologada_reserva.cod_entidade
                       ,  sw_cgm.nom_cgm AS nom_entidade
                       ,  cod_solicitacao
                       ,  cod_centro
                       ,  cod_item
                       ,  cod_reserva
                       ,  cod_conta
                       ,  cod_despesa

                    FROM  compras.solicitacao_homologada_reserva

              INNER JOIN  orcamento.entidade
                      ON  solicitacao_homologada_reserva.cod_entidade = entidade.cod_entidade
                     AND  solicitacao_homologada_reserva.exercicio    = entidade.exercicio

              INNER JOIN  sw_cgm
                      ON  entidade.numcgm = sw_cgm.numcgm";

        return $stSql;
    }

    public function recuperaCodReservaPorItemSolicitacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaCodReservaPorItemSolicitacao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
    }

    public function montaCodReservaPorItemSolicitacao()
    {
        $stSql  = "   SELECT  cod_reserva 											  \n";
        $stSql .= " 																  \n";
        $stSql .= "     FROM  compras.solicitacao_homologada_reserva				  \n";
        $stSql .= " 																  \n";
        $stSql .= "    WHERE  1=1 													  \n";
        $stSql .= "      AND  exercicio       = ".$this->getDado('exercicio')."       \n";
        $stSql .= "      AND  cod_entidade    = ".$this->getDado('cod_entidade')."    \n";
        $stSql .= "      AND  cod_solicitacao = ".$this->getDado('cod_solicitacao')." \n";
        $stSql .= "      AND  cod_centro      = ".$this->getDado('cod_centro')."	  \n";
        $stSql .= "      AND  cod_item        = ".$this->getDado('cod_item')."		  \n";
        $stSql .= "      AND  cod_despesa     = ".$this->getDado('cod_despesa');

        return $stSql;
    }

    public function recuperaHomologacaoReservaSaldoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaHomologacaoReservaSaldoAnulada().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
    }

    public function montaRecuperaHomologacaoReservaSaldoAnulada()
    {
        $stFiltro  = "    SELECT                                                                                            \n";
        $stFiltro .= "           solicitacao_homologada_reserva.*                                                           \n";
        $stFiltro .= "      FROM                                                                                            \n";
        $stFiltro .= "           compras.solicitacao_homologada_reserva                                                     \n";
        $stFiltro .= "           JOIN orcamento.reserva_saldos                                                              \n";
        $stFiltro .= "             ON reserva_saldos.cod_reserva = solicitacao_homologada_reserva.cod_reserva               \n";
        $stFiltro .= "            AND reserva_saldos.exercicio   = solicitacao_homologada_reserva.exercicio                 \n";
        $stFiltro .= "           JOIN orcamento.reserva_saldos_anulada                                                      \n";
        $stFiltro .= "             ON reserva_saldos_anulada.cod_reserva = reserva_saldos.cod_reserva                       \n";
        $stFiltro .= "            AND reserva_saldos_anulada.exercicio   = reserva_saldos.exercicio                         \n";
        $stFiltro .= "     WHERE                                                                                            \n";
        $stFiltro .= "           solicitacao_homologada_reserva.cod_solicitacao  =  ".$this->getDado('cod_solicitacao')."   \n";
        $stFiltro .= "       AND solicitacao_homologada_reserva.exercicio        = '".$this->getDado('exercicio')."'        \n";
        $stFiltro .= "       AND solicitacao_homologada_reserva.cod_entidade     =  ".$this->getDado('cod_entidade')."      \n";
        $stFiltro .= "       AND solicitacao_homologada_reserva.cod_centro       =  ".$this->getDado('cod_centro')."        \n";
        $stFiltro .= "       AND solicitacao_homologada_reserva.cod_item         =  ".$this->getDado('cod_item')."          \n";

        return $stFiltro;
    }

}

?>

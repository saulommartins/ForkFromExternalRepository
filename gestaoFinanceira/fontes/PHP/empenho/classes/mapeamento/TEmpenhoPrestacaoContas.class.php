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
    * Classe de mapeamento da tabela empenho.prestacao_contas
    * Data de Criação: 26/10/2006

    * @author Analista: Gelson
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TEmpenhoPrestacaoContas.class.php 59612 2014-09-02 12:00:51Z gelson $
    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2008-03-26 16:20:04 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.03.31
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  empenho.prestacao_contas
  * Data de Criação: 26/10/2006

  * @author Analista: Gelson
  * @author Desenvolvedor: Rodrigo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEmpenhoPrestacaoContas extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TEmpenhoPrestacaoContas()
    {
        parent::Persistente();
        $this->setTabela("empenho.prestacao_contas");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_empenho,cod_entidade,exercicio');

        $this->AddCampo('cod_empenho'  ,'integer',true  ,''   ,true,'TEmpenhoEmpenho' );
        $this->AddCampo('cod_entidade' ,'integer',true  ,''   ,true,'TEmpenhoEmpenho' );
        $this->AddCampo('exercicio'    ,'char'   ,true  ,'4'  ,true,'TEmpenhoEmpenho' );
        $this->AddCampo('data'         ,'date'   ,true  ,''   ,false,false            );
    }

    public function recuperaDataPagamentoEmpenho(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaDataPagamentoEmpenho().$stCondicao;
    //echo $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaDataPagamentoEmpenho()
  {
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "     TO_CHAR(MAX(timestamp),'dd/mm/yyyy') as data                            \n";
    $stSql .= " FROM (                                                                      \n";
    $stSql .= "         SELECT                                                              \n";
    $stSql .= "              enlp.timestamp as timestamp                                    \n";
    $stSql .= "             ,enlp.cod_nota                                                  \n";
    $stSql .= "             ,coalesce(sum(enlp.vl_pago),0.00) as vl_pago                    \n";
    $stSql .= "             ,0.00 as vl_anulado                                             \n";
    $stSql .= "         FROM                                                                \n";
    $stSql .= "             empenho.nota_liquidacao as enl                                  \n";
    $stSql .= "             LEFT JOIN empenho.nota_liquidacao_paga as enlp                  \n";
    $stSql .= "             ON (    enlp.exercicio          = enl.exercicio                 \n";
    $stSql .= "                 AND enlp.cod_entidade   = enl.cod_entidade                  \n";
    $stSql .= "                 AND enlp.cod_nota       = enl.cod_nota                      \n";
    $stSql .= "                )                                                            \n";
    $stSql .= "             WHERE   enl.cod_entidade = ".$this->getDado('cod_entidade')."   \n";
    $stSql .= "                 AND enl.exercicio_empenho = '".$this->getDado('exercicio')."' \n";
    $stSql .= "                 AND enl.cod_empenho = ".$this->getDado('cod_empenho')."     \n";
    $stSql .= "            GROUP BY enlp.cod_nota,enlp.timestamp                            \n";
    $stSql .= "                                                                             \n";
    $stSql .= "         UNION                                                               \n";
    $stSql .= "                                                                             \n";
    $stSql .= "         SELECT                                                              \n";
    $stSql .= "              enlp.timestamp as timestamp                                    \n";
    $stSql .= "             ,enlp.cod_nota                                                  \n";
    $stSql .= "             ,0.00 as vl_pago                                                \n";
    $stSql .= "             ,coalesce(sum(enlpa.vl_anulado),0.00) as vl_anulado             \n";
    $stSql .= "        FROM                                                                 \n";
    $stSql .= "             empenho.nota_liquidacao as enl                                  \n";
    $stSql .= "             LEFT JOIN empenho.nota_liquidacao_paga as enlp                  \n";
    $stSql .= "             ON (    enlp.exercicio          = enl.exercicio                 \n";
    $stSql .= "                 AND enlp.cod_entidade   = enl.cod_entidade                  \n";
    $stSql .= "                 AND enlp.cod_nota       = enl.cod_nota                      \n";
    $stSql .= "                )                                                            \n";
    $stSql .= "            LEFT JOIN empenho.nota_liquidacao_paga_anulada as enlpa          \n";
    $stSql .= "            ON (    enlpa.exercicio      = enlp.exercicio                    \n";
    $stSql .= "                AND enlpa.cod_entidade   = enlp.cod_entidade                 \n";
    $stSql .= "                AND enlpa.cod_nota       = enlp.cod_nota                     \n";
    $stSql .= "                AND enlpa.timestamp      = enlp.timestamp                    \n";
    $stSql .= "               )                                                             \n";
    $stSql .= "                                                                             \n";
    $stSql .= "             WHERE   enl.cod_entidade = ".$this->getDado('cod_entidade')."   \n";
    $stSql .= "                 AND enl.exercicio_empenho ='".$this->getDado('exercicio')."'\n";
    $stSql .= "                 AND enl.cod_empenho = ".$this->getDado('cod_empenho')."     \n";
    $stSql .= "             GROUP BY enlp.cod_nota,enlp.timestamp                           \n";
    $stSql .= "      ) as tabela                                                            \n";

    return $stSql;
  }

  public function recuperaPrestacaoSemItem(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
  {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $stSql  = $this->montaRecuperaPrestacaoSemItem().$stCondicao;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
  }

  public function montaRecuperaPrestacaoSemItem()
  {
    $stSql  = " SELECT * FROM (                                                \n ";
    $stSql .= "   SELECT prestacao_contas.cod_empenho                          \n ";
    $stSql .= "        , prestacao_contas.cod_entidade                         \n ";
    $stSql .= "        , prestacao_contas.exercicio                            \n ";
    $stSql .= "        , TO_CHAR(TO_DATE(prestacao_contas.data, 'yyyy-mm-dd'), 'dd/mm/yyyy') AS data  \n ";
    $stSql .= "        , SUM(COALESCE(nota_liquidacao_paga.vl_pago, 0.00)) AS vl_pago \n ";
    $stSql .= "        , SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS vl_anulado \n ";
    $stSql .= "        , SUM(COALESCE(nota_liquidacao_paga.vl_pago, 0.00)) -       \n ";
    $stSql .= "          SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS vl_prestar \n ";
    $stSql .= "     FROM empenho.prestacao_contas                              \n ";
    $stSql .= "     JOIN empenho.empenho                                       \n ";
    $stSql .= "       ON empenho.cod_empenho = prestacao_contas.cod_empenho    \n ";
    $stSql .= "      AND empenho.exercicio = prestacao_contas.exercicio        \n ";
    $stSql .= "      AND empenho.cod_entidade = prestacao_contas.cod_entidade  \n ";
    $stSql .= "     JOIN empenho.nota_liquidacao                               \n ";
    $stSql .= "       ON nota_liquidacao.cod_empenho = empenho.cod_empenho     \n ";
    $stSql .= "      AND nota_liquidacao.exercicio = empenho.exercicio         \n ";
    $stSql .= "      AND nota_liquidacao.cod_entidade = empenho.cod_entidade   \n ";
    $stSql .= "     JOIN empenho.nota_liquidacao_paga                          \n ";
    $stSql .= "       ON nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota  \n ";
    $stSql .= "      AND nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio \n ";
    $stSql .= "      AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade \n ";
    $stSql .= "LEFT JOIN empenho.nota_liquidacao_paga_anulada                  \n ";
    $stSql .= "       ON nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota \n ";
    $stSql .= "      AND nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio \n ";
    $stSql .= "      AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade \n ";
    $stSql .= "      AND nota_liquidacao_paga_anulada.timestamp = nota_liquidacao_paga.timestamp \n ";
    $stSql .= " GROUP BY prestacao_contas.cod_empenho                          \n ";
    $stSql .= "        , prestacao_contas.cod_entidade                         \n ";
    $stSql .= "        , prestacao_contas.exercicio                            \n ";
    $stSql .= "        , prestacao_contas.data                                 \n ";
    $stSql .= " ORDER BY prestacao_contas.cod_empenho                          \n ";
    $stSql .= "        , prestacao_contas.data                                 \n ";
    $stSql .= "        , prestacao_contas.cod_entidade                         \n ";
    $stSql .= "        , prestacao_contas.exercicio                            \n ";
    $stSql .= ") AS tabela                                                     \n ";

    return $stSql;
  }

}
?>

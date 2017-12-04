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
    * Classe de mapeamento da tabela ALMOXARIFADO.LANCAMENTO_REQUISICAO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 28646 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-19 16:06:14 -0300 (Qua, 19 Mar 2008) $

    * Casos de uso: uc-03.03.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.LANCAMENTO_REQUISICAO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoLancamentoRequisicao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoLancamentoRequisicao()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.lancamento_requisicao');

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_requisicao,cod_almoxarifado,cod_marca,cod_centro,cod_item');

    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_requisicao','integer',true,'',true,true);
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,true);
    $this->AddCampo('cod_marca','integer',true,'',true,true);
    $this->AddCampo('cod_centro','integer',true,'',true,true);
    $this->AddCampo('cod_item','integer',true,'',true,true);
    $this->AddCampo('cod_lancamento','integer',true,'',false,true);

}

function recuperaSaldoAtendido(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoAtendido().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoAtendido()
{
    $stSql .= " SELECT                                                        \n";
    $stSql .= "     sum(alm.quantidade) as saldo_atendido \n";
    $stSql .= " FROM                                                          \n";
    $stSql .= "     almoxarifado.lancamento_material as alm                       \n";
    $stSql .= " JOIN                                               \n";
    $stSql .= "     almoxarifado.lancamento_requisicao as alr            \n";
    $stSql .= " ON (                                                          \n";
    $stSql .= "     alm.cod_lancamento = alr.cod_lancemento and                        \n";
    $stSql .= "     alm.cod_item = alr.cod_item and          \n";
    $stSql .= "     alm.cod_marca = alr.cod_marca and              \n";
    $stSql .= "     alm.cod_almoxarifado = alr.cod_almoxarifado and                        \n";
    $stSql .= "     alm.cod_centro = alr.cod_centro )                        \n";
    $stSql .= " WHERE                                                         \n";
    $stSql .= "     alm.cod_lancamento= ".$this->getDado('cod_lancamento')." and          \n";
    $stSql .= "     alm.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and          \n";
    $stSql .= "     alr.cod_requisicao = ".$this->getDado('cod_requisicao')." and              \n";
    $stSql .= "     alm.cod_item  = ".$this->getDado('cod_item')." and                        \n";
    $stSql .= "     alm.cod_marca = ".$this->getDado('cod_marca')." and                        \n";
    $stSql .= "     alm.cod_centro = ".$this->getDado('cod_centro')."                         \n";

    return $stSql;
}

    public function recuperaUltimoLancamentoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
            return $this->executaRecupera("montaRecuperaUltimoLancamentoItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function montaRecuperaUltimoLancamentoItem()
   {
          $stSql = "Select max(lancamento_requisicao.cod_lancamento) as lancamento
                            From  almoxarifado.lancamento_requisicao";

          return $stSql;
   }

    public function recuperaSaldoValorUnitarioRequisicao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoValorUnitarioRequisicao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaSaldoValorUnitarioRequisicao()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0             \n";
        $stSql .= "              THEN SUM(valor_mercado) / SUM(quantidade)  \n";
        $stSql .= "              END as valor_unitario                      \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";

        $stSql .= "  INNER JOIN  almoxarifado.lancamento_requisicao         \n";
        $stSql .= "          ON  lancamento_requisicao.cod_lancamento   = lancamento_material.cod_lancamento    \n";
        $stSql .= "         AND  lancamento_requisicao.cod_item         = lancamento_material.cod_item          \n";
        $stSql .= "         AND  lancamento_requisicao.cod_centro       = lancamento_material.cod_centro        \n";
        $stSql .= "         AND  lancamento_requisicao.cod_marca        = lancamento_material.cod_marca         \n";
        $stSql .= "         AND  lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado  \n";

        $stSql .= "       WHERE  lancamento_material.tipo_natureza = 'S'    \n";

        # Filtro pelo exercicio da requisição.
        if($this->getDado('exercicio') != '')
            $stSql .= "     AND  lancamento_requisicao.exercicio = '".$this->getDado('exercicio')."'  \n";

        # Filtro pelo código da requisição.
        if($this->getDado('cod_requisicao') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_requisicao = ".$this->getDado('cod_requisicao')."  \n";

        # Filtro pelo código do item.
        if($this->getDado('cod_item') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_item = ".$this->getDado('cod_item')."  \n";

        return $stSql;
    }

    public function recuperaSaldoValorUnitarioRequisicaoTruncado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoValorUnitarioRequisicaoTruncado().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSaldoValorUnitarioRequisicaoTruncado()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0             \n";
        $stSql .= "              THEN COALESCE(TRUNC(SUM(valor_mercado) / SUM(quantidade),2),0) \n";
        $stSql .= "              END as valor_unitario                      \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";

        $stSql .= "  INNER JOIN  almoxarifado.lancamento_requisicao         \n";
        $stSql .= "          ON  lancamento_requisicao.cod_lancamento   = lancamento_material.cod_lancamento    \n";
        $stSql .= "         AND  lancamento_requisicao.cod_item         = lancamento_material.cod_item          \n";
        $stSql .= "         AND  lancamento_requisicao.cod_centro       = lancamento_material.cod_centro        \n";
        $stSql .= "         AND  lancamento_requisicao.cod_marca        = lancamento_material.cod_marca         \n";
        $stSql .= "         AND  lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado  \n";

        $stSql .= "       WHERE  1=1    \n";

        # Filtro pelo exercicio da requisição.
        if($this->getDado('exercicio') != '')
            $stSql .= "     AND  lancamento_requisicao.exercicio = '".$this->getDado('exercicio')."'  \n";

        # Filtro pelo código da requisição.
        if($this->getDado('cod_requisicao') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_requisicao = ".$this->getDado('cod_requisicao')."  \n";

        # Filtro pelo código do item.
        if($this->getDado('cod_item') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_item = ".$this->getDado('cod_item')."  \n";

        return $stSql;
    }

    public function recuperaRestoValorDevolucao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestoValorDevolucao().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaRestoValorDevolucao()
    {
        $stSql  = "      SELECT                                                                                               \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0 THEN                                                          \n";
        $stSql .= "                   SUM(valor_mercado)-TRUNC(TRUNC(SUM(valor_mercado)/SUM(quantidade),2)*SUM(quantidade),2) \n";
        $stSql .= "              ELSE 0                                                                                       \n";
        $stSql .= "              END                                                                                          \n";
        $stSql .= "              AS resto                                                                                     \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material                                                             \n";
        $stSql .= "       INNER JOIN  almoxarifado.lancamento_requisicao                                                      \n";
        $stSql .= "          ON  lancamento_requisicao.cod_lancamento   = lancamento_material.cod_lancamento                  \n";
        $stSql .= "         AND  lancamento_requisicao.cod_item         = lancamento_material.cod_item                        \n";
        $stSql .= "         AND  lancamento_requisicao.cod_centro       = lancamento_material.cod_centro                      \n";
        $stSql .= "         AND  lancamento_requisicao.cod_marca        = lancamento_material.cod_marca                       \n";
        $stSql .= "         AND  lancamento_requisicao.cod_almoxarifado = lancamento_material.cod_almoxarifado                \n";
        $stSql .= "       WHERE  1=1                                                                                          \n";

        # Filtro pelo exercicio da requisição.
        if($this->getDado('exercicio') != '')
            $stSql .= "     AND  lancamento_requisicao.exercicio = ".$this->getDado('exercicio')."  \n";

        # Filtro pelo código da requisição.
        if($this->getDado('cod_requisicao') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_requisicao = ".$this->getDado('cod_requisicao')."  \n";

        # Filtro pelo código do item.
        if($this->getDado('cod_item') != '')
            $stSql .= "     AND  lancamento_requisicao.cod_item = ".$this->getDado('cod_item')."  \n";

         # Filtro pelo código do lançamento.
        if($this->getDado('cod_lancamento') != '')
            $stSql .= "     AND  lancamento_material.cod_lancamento = ".$this->getDado('cod_lancamento')."  \n";

        return $stSql;
    }

    public function proximoCod(&$inCodLancamento, $boTransacao = "")
    {
        ;
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = "SELECT MAX( cod_lancamento ) AS cod_lancamento FROM almoxarifado.lancamento_material ";

        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        if (!$obErro->ocorreu()) {
            $inCodLancamento = $rsRecordSet->getCampo("cod_lancamento") + 1;
        }

        return $obErro;
}

}

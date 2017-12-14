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
    * Classe de mapeamento da tabela ALMOXARIFADO.LANCAMENTO_MATERIAL
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.03.06
                    uc-03.03.17
                    uc-03.03.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.LANCAMENTO_MATERIAL
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoLancamentoMaterial extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoLancamentoMaterial()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.lancamento_material');

    $this->setCampoCod('cod_lancamento');
    $this->setComplementoChave('cod_item,cod_marca,cod_almoxarifado,cod_centro');

    $this->AddCampo('cod_lancamento','sequence',true,'',true,false);
    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('exercicio_lancamento','varchar',true,4,false,'TAlmoxarifadoNaturezaLancamento');
    $this->AddCampo('num_lancamento','integer',true,'',false,'TAlmoxarifadoNaturezaLancamento');
    $this->AddCampo('cod_natureza','integer',true,'',false,'TAlmoxarifadoNaturezaLancamento');
    $this->AddCampo('tipo_natureza','varchar',true,1,false,'TAlmoxarifadoNaturezaLancamento');
    $this->AddCampo('quantidade','numeric',true,'14.4',false,false);
    $this->AddCampo('complemento','varchar',true,'160',false,false);
    $this->AddCampo('valor_mercado', 'numeric', true, '14.4', false, false);
}

function proximoCod(&$inCodLancamento , $boTransacao = "")
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

function montaVerificaLancamento()
{
    $stSql .= "    select                                                             \n";
    $stSql .= "         count(alm.cod_lancamento) as count_lancamento                 \n";
    $stSql .= "    from                                                               \n";
    $stSql .= "         almoxarifado.lancamento_material alm                          \n";
    $stSql .= "    where                                                              \n";
    $stSql .= "         alm.cod_item = ". $this->getDado('cod_item') .               "\n";
    $stSql .= "     and alm.cod_marca = ". $this->getDado('cod_marca') .             "\n";
    $stSql .= "     and alm.cod_almoxarifado = ". $this->getDado('cod_almoxarifado')."\n";
    $stSql .= "     and alm.cod_centro = ". $this->getDado('cod_centro').            "\n";

    return $stSql;
}

function recuperaVerificaAtributosLancamento(&$rsRecordSet)
{
    $obConexao = new Conexao;

    $stSql = $this->montaVerificaAtributosLancamento();
    $this->setDebug($stSql);
    $obConexao->executaSQL( $rsRecordSet, $stSql );

    return true;
}

function montaVerificaAtributosLancamento()
{
    $stSql .= "    select atributo_estoque_material_valor.valor                                                   \n";
    $stSql .= "         , alm.cod_lancamento                                                                      \n";
    $stSql .= "         , alm.cod_item                                                                            \n";
    $stSql .= "         , atributo_estoque_material_valor.cod_atributo                                            \n";
    $stSql .= "         , atributo_dinamico.nom_atributo                                                          \n";
    $stSql .= "      from almoxarifado.lancamento_material alm                                                    \n";

    $stSql .= "     left join almoxarifado.atributo_estoque_material_valor                                        \n";
    $stSql .= "            on alm.cod_almoxarifado = atributo_estoque_material_valor.cod_almoxarifado             \n";
    $stSql .= "           and alm.cod_item = atributo_estoque_material_valor.cod_item                             \n";
    $stSql .= "           and alm.cod_centro = atributo_estoque_material_valor.cod_centro                         \n";
    $stSql .= "           and alm.cod_marca = atributo_estoque_material_valor.cod_marca                           \n";
    $stSql .= "           and alm.cod_lancamento = atributo_estoque_material_valor.cod_lancamento                 \n";

    $stSql .= "     left join administracao.atributo_dinamico                                                     \n";
    $stSql .= "            on atributo_estoque_material_valor.cod_atributo = atributo_dinamico.cod_atributo       \n";
    $stSql .= "           and atributo_estoque_material_valor.cod_modulo = atributo_dinamico.cod_modulo           \n";
    $stSql .= "           and atributo_estoque_material_valor.cod_cadastro = atributo_dinamico.cod_cadastro       \n";

    $stSql .= "    where alm.cod_item = ". $this->getDado('cod_item') .                                          "\n";
    $stSql .= "      and alm.cod_marca = ". $this->getDado('cod_marca') .                                        "\n";
    $stSql .= "      and alm.cod_almoxarifado = ". $this->getDado('cod_almoxarifado').                           "\n";
    $stSql .= "      and alm.cod_centro = ". $this->getDado('cod_centro').                                       "\n";

    $stSql .= "     order by cod_lancamento, nom_atributo                                                         \n";

    return $stSql;
}

function recuperaVerificaLancamento(&$rsRecordSet)
{
    $obConexao   = new Conexao;

    $stSql = $this->montaVerificaLancamento();
    $this->setDebug($stSql);
    $obConexao->executaSQL( $rsRecordSet, $stSql );

    return true;
}

    public function recuperaComplementoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
            return $this->executaRecupera("montaRecuperaComplemento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
   }

   public function montaRecuperaComplemento()
   {
          $stSql = "Select lancamento_material.complemento      \n";
          $stSql.= "  From almoxarifado.lancamento_material       ,  \n";
          $stSql.= "       almoxarifado.lancamento_requisicao   \n";

          return $stSql;
   }

   public function recuperaLancamentosItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
   {
            return $this->executaRecupera("montaRecuperaLancamentosItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaLancamentosItem()
    {
        $stSql = " Select count(1) as lancamentos from almoxarifado.lancamento_material \n";

        return $stSql;
    }

    # Método importado da extinta classe Lançamento Material Valor.
    public function recuperaAlmoxarifadoLancamentoMaterialValor(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAlmoxarifadoLancamentoMaterialValor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    # Método importado da extinta classe Lançamento Material Valor.
    public function montaRecuperaAlmoxarifadoLancamentoMaterialValor()
    {

        $stSql = "        SELECT tai.*                                              \n";
        $stSql.= "             , lm.*                                               \n";
        $stSql.= "             , lp.*                                               \n";
        $stSql.= "          FROM almoxarifado.transferencia_almoxarifado_item tai   \n";
        $stSql.= "          JOIN almoxarifado.lancamento_material lm                \n";
        $stSql.= "            ON tai.cod_lancamento    = lm.cod_lancamento          \n";
        $stSql.= "           AND tai.cod_item          = lm.cod_item                \n";
        $stSql.= "           AND tai.cod_centro        = lm.cod_centro              \n";
        $stSql.= "           AND tai.cod_marca         = lm.cod_marca               \n";
        $stSql.= "           AND tai.cod_almoxarifado  = lm.cod_almoxarifado        \n";
        $stSql.= "     LEFT JOIN almoxarifado.lancamento_perecivel lp               \n";
        $stSql.= "            ON lp.cod_lancamento    = lm.cod_lancamento           \n";
        $stSql.= "           AND lp.cod_item          = lm.cod_item                 \n";
        $stSql.= "           AND lp.cod_marca         = lm.cod_marca                \n";
        $stSql.= "           AND lp.cod_almoxarifado  = lm.cod_almoxarifado         \n";
        $stSql.= "           AND lp.cod_centro        = lm.cod_centro               \n";
        $stSql.= "         WHERE 1=1                                                \n";

        if ($this->getDado('cod_item') != '') {
            $stSql.= "          AND lmlv.cod_item = ".$this->getDado('cod_item')."                  \n";
        }

        if ($this->getDado('cod_marca') != '') {
            $stSql.= "            AND lmlv.cod_marca = ".$this->getDado('cod_marca')."              \n";
        }

        if ($this->getDado('cod_almoxarifado') != '') {
            $stSql.= "            AND lmlv.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')."\n";
        }

        if ($this->getDado('cod_centro') != '') {
            $stSql.= "            AND lmlv.cod_centro = ".$this->getDado('cod_centro')."            \n";
        }

        return $stSql;
    }

    # Método importado da extinta classe Lançamento Material Valor.
    public function recuperaSaldoValorUnitario(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoValorUnitario().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    # Método importado da extinta classe Lançamento Material Valor.
    public function montaRecuperaSaldoValorUnitario()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0             \n";
        $stSql .= "              THEN SUM(valor_mercado) / SUM(quantidade)  \n";
        $stSql .= "              END as valor_unitario                      \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";
        $stSql .= "       WHERE  lancamento_material.tipo_natureza = 'E'    \n";

        if ($this->getDado('cod_item') != '') {
            $stSql .= "     AND  cod_item = ".$this->getDado('cod_item')."  \n";
        }

        return $stSql;
    }

    public function recuperaSaldoValorUnitarioTruncado(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoValorUnitarioTruncado().$stFiltro.$stOrder;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaSaldoValorUnitarioTruncado()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0 THEN        \n";
        $stSql .= "                   COALESCE(TRUNC(SUM(valor_mercado) / SUM(quantidade),2),0) \n";
        $stSql .= "              ELSE 0                                     \n";
        $stSql .= "              END                                        \n";
        $stSql .= "              AS valor_unitario                          \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";
        $stSql .= "       WHERE  1=1                                        \n";

        if ($this->getDado('cod_item') != '') {
            $stSql .= "     AND  cod_item = ".$this->getDado('cod_item')."  \n";
        }

        return $stSql;
    }

    public function recuperaSaldoQuantidade(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoQuantidade().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaSaldoQuantidade()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              COALESCE(SUM(quantidade),0) as saldo_quantidade \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";
        $stSql .= "       WHERE  1=1                                        \n";

        if ($this->getDado('cod_item') != '') {
            $stSql .= "     AND  cod_item = ".$this->getDado('cod_item')."  \n";
        }

        return $stSql;
    }

    public function recuperaSaldoValor(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoValor().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaSaldoValor()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              COALESCE(SUM(valor_mercado),0) as saldo_valor \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";
        $stSql .= "       WHERE  1=1                                        \n";

        if ($this->getDado('cod_item') != '') {
            $stSql .= "     AND  cod_item = ".$this->getDado('cod_item')."  \n";
        }

        return $stSql;
    }

    public function recuperaRestoValor(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRestoValor().$stFiltro.$stOrder;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaRestoValor()
    {
        $stSql  = "      SELECT                                             \n";
        $stSql .= "              CASE WHEN SUM(quantidade) <> 0 THEN        \n";
        $stSql .= "                   SUM(valor_mercado)-TRUNC(TRUNC(SUM(valor_mercado)/SUM(quantidade),2)*SUM(quantidade),2) \n";
        $stSql .= "              ELSE 0                                     \n";
        $stSql .= "              END                                        \n";
        $stSql .= "              AS resto                                   \n";
        $stSql .= "        FROM  almoxarifado.lancamento_material           \n";
        $stSql .= "       WHERE  1=1                                        \n";

        if ($this->getDado('cod_item') != '') {
            $stSql .= "     AND  cod_item = ".$this->getDado('cod_item')."  \n";
        }

        return $stSql;
    }

}

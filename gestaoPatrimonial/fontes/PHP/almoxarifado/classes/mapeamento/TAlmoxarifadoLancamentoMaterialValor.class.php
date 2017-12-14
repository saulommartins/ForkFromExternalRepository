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
    * Classe de mapeamento da tabela ALMOXARIFADO.LANCAMENTO_MATERIAL_VALOR
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 13254 $
    $Name$
    $Author: tonismar $
    $Date: 2006-07-27 11:01:45 -0300 (Qui, 27 Jul 2006) $

    * Casos de uso: uc-03.03.06
                    uc-03.03.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.LANCAMENTO_MATERIAL_VALOR
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoLancamentoMaterialValor extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TAlmoxarifadoLancamentoMaterialValor()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.lancamento_material_valor');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_almoxarifado,cod_marca,cod_centro,cod_item,cod_lancamento');

        $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
        $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
        $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
        $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
        $this->AddCampo('cod_lancamento','integer',true,'',true,'TAlmoxarifadoLancamentoMaterial');
        $this->AddCampo('valor_mercado','numeric',true,'14.2',false,false);
    }

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

    public function montaRecuperaAlmoxarifadoLancamentoMaterialValor()
    {

        $stSql = "        SELECT tai.cod_item                                       \n";
        $stSql.= "             , tai.cod_marca                                      \n";
        $stSql.= "             , tai.cod_almoxarifado                               \n";
        $stSql.= "             , tai.cod_centro                                     \n";
        $stSql.= "             , lm.cod_lancamento                                  \n";
        $stSql.= "             , lm.quantidade                                      \n";
        $stSql.= "             , lm.exercicio_lancamento                            \n";
        $stSql.= "             , lmlv.valor_mercado                                 \n";
        $stSql.= "             , lp.lote                                            \n";
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
        $stSql.= "          JOIN almoxarifado.lancamento_material_valor lmlv        \n";
        $stSql.= "            ON lm.cod_lancamento     = lmlv.cod_lancamento        \n";
        $stSql.= "           AND lm.cod_item           = lmlv.cod_item              \n";
        $stSql.= "           AND lm.cod_centro         = lmlv.cod_centro            \n";
        $stSql.= "           AND lm.cod_marca          = lmlv.cod_marca             \n";
        $stSql.= "           AND lm.cod_almoxarifado   = lmlv.cod_almoxarifado      \n";
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

    public function montaRecuperaSaldoValorUnitario()
    {

        $stSql = "      SELECT SUM(valor_mercado) / SUM(quantidade) as valor_unitario       \n";
        $stSql.= "        FROM (SELECT CASE                                                  \n";
        $stSql.= "                WHEN  tipo_natureza = 'S' THEN valor_mercado*(-1)          \n";
        $stSql.= "                ELSE valor_mercado                                         \n";
        $stSql.= "                 END as valor_mercado                                      \n";
        $stSql.= "                   , quantidade                                            \n";
        $stSql.= "                FROM almoxarifado.lancamento_material ml                   \n";
        $stSql.= "                JOIN almoxarifado.lancamento_material_valor lmlv           \n";
        $stSql.= "                  ON ml.cod_lancamento = lmlv.cod_lancamento               \n";
        $stSql.= "                 AND ml.cod_almoxarifado = lmlv.cod_almoxarifado           \n";
        $stSql.= "                 AND ml.cod_centro = lmlv.cod_centro                       \n";
        $stSql.= "                 AND ml.cod_item = lmlv.cod_item                           \n";
        $stSql.= "               WHERE 1=1                                                   \n";

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

        $stSql.= "          ) as soma                                                               \n";
        $stSql.= "          , almoxarifado.catalogo_item                                            \n";
        $stSql.= "  WHERE 1=1                                                                       \n";

        return $stSql;
    }

}

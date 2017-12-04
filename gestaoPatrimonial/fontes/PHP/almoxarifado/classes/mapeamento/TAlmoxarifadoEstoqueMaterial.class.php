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
    * Classe de mapeamento da tabela ALMOXARIFADO.ESTOQUE_MATERIAL
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 13741 $
    $Name$
    $Author: tonismar $
    $Date: 2006-08-09 11:08:31 -0300 (Qua, 09 Ago 2006) $

    * Casos de uso: uc-03.03.02
                    uc-03.03.08
                    uc-03.03.01
                    uc-03.03.17
                    uc-03.03.16
*/

/*
$Log$
Revision 1.13  2006/08/09 14:08:31  tonismar
método de verificação de itens com saldo

Revision 1.12  2006/07/27 14:01:45  tonismar
inclusao do caso de uso

Revision 1.11  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.10  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ESTOQUE_MATERIAL
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoEstoqueMaterial extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TAlmoxarifadoEstoqueMaterial()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.estoque_material');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_item,cod_marca,cod_almoxarifado,cod_centro');

        $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoCatalogoItemMarca');
        $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoCatalogoItemMarca');
        $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoAlmoxarifado');
        $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoCentroCusto');
    }

    public function recuperaEstoqueCentroDeCusto(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEstoqueCentroDeCusto().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEstoqueCentroDeCusto()
    {
        $stSql  = " SELECT                                                                          \n";
        $stSql .= "     acc.descricao,                                                              \n";
        $stSql .= "     acc.cod_centro                                                              \n";
        $stSql .= "     FROM                                                                        \n";
        $stSql .= "     almoxarifado.almoxarifado as aa,                                            \n";
        $stSql .= "     almoxarifado.permissao_almoxarifados as apa,                                \n";
        $stSql .= "     almoxarifado.marca as am,                                                   \n";
        $stSql .= "     almoxarifado.catalogo_item as aci,                                          \n";
        $stSql .= "     almoxarifado.catalogo_item_marca as acim,                                   \n";
        $stSql .= "     almoxarifado.estoque_material as aem,                                       \n";
        $stSql .= "     almoxarifado.centro_custo as acc,                                           \n";
        $stSql .= "     almoxarifado.centro_custo_permissao as accp                                 \n";
        $stSql .= " WHERE                                                                           \n";
        $stSql .= "     aem.cod_marca = am.cod_marca               and                              \n";
        $stSql .= "     aem.cod_item  = aci.cod_item               and                              \n";
        $stSql .= "     aem.cod_almoxarifado = aa.cod_almoxarifado and                              \n";
        $stSql .= "     aem.cod_centro = acc.cod_centro            and                              \n";
        $stSql .= "     am.cod_marca = acim.cod_marca              and                              \n";
        $stSql .= "     aci.cod_item = acim.cod_item               and                              \n";
        $stSql .= "     acc.cod_centro = accp.cod_centro           and                              \n";
        $stSql .= "     aa.cod_almoxarifado = apa.cod_almoxarifado                                  \n";

        return $stSql;
    }

    public function recuperaEstoqueCentroDeCustoComSaldo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEstoqueCentroDeCustoComSaldo().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaEstoqueCentroDeCustoComSaldo()
    {
        $stSql  = " SELECT                                                                          \n";
        $stSql .= "    DISTINCT  acc.descricao,                                                     \n";
        $stSql .= "     acc.cod_centro                                                              \n";
        $stSql .= "     FROM                                                                        \n";
        $stSql .= "     ( SELECT                                       \n";
        $stSql .= "         alm.cod_item,                              \n";
        $stSql .= "         alm.cod_marca,                             \n";
        $stSql .= "         alm.cod_centro,                            \n";
        $stSql .= "         sum(alm.quantidade) as saldo               \n";
        $stSql .= "       from                                         \n";
        $stSql .= "         almoxarifado.lancamento_material as alm    \n";
        $stSql .= "       group by                                     \n";
        $stSql .= "         alm.cod_item,                              \n";
        $stSql .= "         alm.cod_marca,                             \n";
        $stSql .= "         alm.cod_centro                             \n";
        $stSql .= "     ) as spfc,                                     \n";
        $stSql .= "     almoxarifado.almoxarifado as aa,                                            \n";
        $stSql .= "     almoxarifado.permissao_almoxarifados as apa,                                \n";
        $stSql .= "     almoxarifado.marca as am,                                                   \n";
        $stSql .= "     almoxarifado.catalogo_item as aci,                                          \n";
        $stSql .= "     almoxarifado.catalogo_item_marca as acim,                                   \n";
        $stSql .= "     almoxarifado.estoque_material as aem,                                       \n";
        $stSql .= "     almoxarifado.centro_custo as acc,                                           \n";
        $stSql .= "     almoxarifado.centro_custo_permissao as accp                                 \n";
        $stSql .= " WHERE                                                                           \n";
        $stSql .= "     aem.cod_marca = am.cod_marca               and                              \n";
        $stSql .= "     aem.cod_item  = aci.cod_item               and                              \n";
        $stSql .= "     aem.cod_almoxarifado = aa.cod_almoxarifado and                              \n";
        $stSql .= "     aem.cod_centro = acc.cod_centro            and                              \n";
        $stSql .= "     am.cod_marca = acim.cod_marca              and                              \n";
        $stSql .= "     aci.cod_item = acim.cod_item               and                              \n";
        $stSql .= "     acc.cod_centro = accp.cod_centro           and                              \n";
        $stSql .= "     aa.cod_almoxarifado = apa.cod_almoxarifado and                              \n";
        $stSql .= "     acim.cod_item = spfc.cod_item and                                           \n";
        $stSql .= "     acim.cod_marca = spfc.cod_marca and                                         \n";
        $stSql .= "     acc.cod_centro = spfc.cod_centro and                                        \n";
        $stSql .= "     spfc.saldo > 0                                                              \n";

        return $stSql;
    }

    public function recuperaCentroDeCustoAlmoxarifado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stAgrupa    = " GROUP BY centro_custo.cod_centro                                           \n";
        $stAgrupa   .= "         ,centro_custo.descricao                                            \n";

        $stSql = $this->montaRecuperaCentroDeCustoAlmoxarifado().$stFiltro.$stAgrupa.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCentroDeCustoAlmoxarifado()
    {

        $stSql  = " SELECT SUM(lancamento_material.quantidade) AS quantidade                         \n";
        $stSql .= "       ,centro_custo.cod_centro                                                   \n";
        $stSql .= "       ,centro_custo.descricao                                                    \n";
        $stSql .= "   FROM almoxarifado.lancamento_material                                          \n";
        $stSql .= "       ,almoxarifado.estoque_material                                             \n";
        $stSql .= "       ,almoxarifado.centro_custo                                                 \n";
        $stSql .= "  WHERE lancamento_material.cod_item         = estoque_material.cod_item          \n";
        $stSql .= "    AND lancamento_material.cod_marca        = estoque_material.cod_marca         \n";
        $stSql .= "    AND lancamento_material.cod_almoxarifado = estoque_material.cod_almoxarifado  \n";
        $stSql .= "    AND lancamento_material.cod_centro       = estoque_material.cod_centro        \n";
        $stSql .= "    AND centro_custo.cod_centro              = estoque_material.cod_centro        \n";

        return $stSql;
    }

    public function recuperaSaldoEstoque(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSaldoEstoque().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaSaldoEstoque()
    {
        $stSql  = " SELECT                                                \n";
        $stSql .= "     coalesce(sum(alm.quantidade),0) as saldo_estoque  \n";
        $stSql .= " FROM                                                  \n";
        $stSql .= "     almoxarifado.lancamento_material as alm           \n";
        $stSql .= " LEFT JOIN                                             \n";
        $stSql .= "     almoxarifado.estoque_material as aem              \n";
        $stSql .= " ON                                                    \n";
        $stSql .= "     aem.cod_item  = alm.cod_item  and                 \n";
        $stSql .= "     aem.cod_marca = alm.cod_marca and                 \n";
        $stSql .= "     aem.cod_almoxarifado = alm.cod_almoxarifado and   \n";
        $stSql .= "     aem.cod_centro = alm.cod_centro                   \n";
        $stSql .= " WHERE 1 = 1                                           \n";

        return $stSql;

    }

    public function recuperaEstoqueMaterialItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaEstoqueMaterialItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;

    }

    public function montaRecuperaEstoqueMaterialItem()
    {
        $stSql = '       SELECT *
                                FROM almoxarifado.catalogo_item_marca
                        INNER JOIN almoxarifado.estoque_material
                                    ON catalogo_item_marca.cod_item = estoque_material.cod_item
                                  AND catalogo_item_marca.cod_marca =estoque_material.cod_marca
                              WHERE catalogo_item_marca.cod_item = '.$this->getDado('cod_item');

         return $stSql;
    }

}

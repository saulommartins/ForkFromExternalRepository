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
 * Classe de mapeamento da tabela ALMOXARIFADO.CATALOGO_ITEM_MARCA
 * Data de Criação: 26/10/2005

 * @author Analista: Diego Victoria
 * @author Desenvolvedor: Fernando Zank Correa Evangelista

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TAlmoxarifadoCatalogoItemMarca.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.03.04
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CATALOGO_ITEM_MARCA
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCatalogoItemMarca extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TAlmoxarifadoCatalogoItemMarca()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.catalogo_item_marca');

        $this->setCampoCod('cod_item');
        $this->setComplementoChave('cod_marca');

        $this->AddCampo('cod_item','integer',true,'',true,false);
        $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoMarca');

    }

    public function recuperaItemMarca(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItemMarca().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItemMarca()
    {
        $stSql  = " select                                       \n";
        $stSql .= "     am.descricao,                            \n";
        $stSql .= "     am.cod_marca                             \n";
        $stSql .= " from                                         \n";
        $stSql .= "     almoxarifado.marca as am,                \n";
        $stSql .= "     almoxarifado.catalogo_item_marca as acim \n";
        $stSql .= " where                                        \n";
        $stSql .= "     am.cod_marca = acim.cod_marca            \n";

        return $stSql;
    }

    public function recuperaItemMarcaComSaldo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItemMarcaComSaldo().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItemMarcaComSaldo()
    {
        $stSql  = " select distinct                                    \n";
        $stSql .= "     am.descricao,                                  \n";
        $stSql .= "     am.cod_marca                                   \n";
        $stSql .= " from                                               \n";
        $stSql .= "     almoxarifado.marca as am,                      \n";
        $stSql .= "     almoxarifado.catalogo_item_marca as acim,      \n";
        $stSql .= "     ( SELECT                                       \n";
        $stSql .= "         alm.cod_item,                              \n";
        $stSql .= "         alm.cod_marca,                             \n";
        $stSql .= "         sum(alm.quantidade) as saldo,              \n";
        $stSql .= "         alm.cod_almoxarifado,                      \n";
        $stSql .= "         alm.cod_centro                             \n";
        $stSql .= "       from                                         \n";
        $stSql .= "         almoxarifado.lancamento_material as alm    \n";
        $stSql .= "       group by                                     \n";
        $stSql .= "         alm.cod_item,                              \n";
        $stSql .= "         alm.cod_marca,                             \n";
        $stSql .= "         alm.cod_almoxarifado,                      \n";
        $stSql .= "         alm.cod_centro                             \n";
        $stSql .= "     ) as spfc                                      \n";
        $stSql .= " where                                              \n";
        $stSql .= "     am.cod_marca = acim.cod_marca and              \n";
        $stSql .= "     acim.cod_item = spfc.cod_item and              \n";
        $stSql .= "     acim.cod_marca = spfc.cod_marca and            \n";
        $stSql .= "     spfc.saldo > 0                                 \n";

        return $stSql;
    }
}

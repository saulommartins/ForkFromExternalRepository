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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 15/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 28331 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-04 11:56:46 -0300 (Ter, 04 Mar 2008) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.6  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:11:17  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TFrotaItem()
    {
        parent::Persistente();
        $this->setTabela('frota.item');
        $this->setCampoCod('cod_item');
        $this->setComplementoChave('');
        $this->AddCampo('cod_item','integer',true,'',true,true);
        $this->AddCampo('cod_tipo','integer',true,'',false,true);

    }

    public function recuperaItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaItem()
    {
        $stSql  = "select                                                   \n";
        $stSql .= "    catalogo_item.cod_item,                              \n";
        $stSql .= "    catalogo_item.descricao,                             \n";
        $stSql .= "    tipo_item.descricao as desc_tipo_frota,              \n";
        $stSql .= "    tipo_item_alm.descricao as desc_tipo_alm             \n";
        $stSql .= "from                                                     \n";
        $stSql .= "    frota.item,                                          \n";
        $stSql .= "    frota.tipo_item,                                     \n";
        $stSql .= "    almoxarifado.catalogo_item,                          \n";
        $stSql .= "    almoxarifado.tipo_item tipo_item_alm                 \n";
        $stSql .= "where                                                    \n";
        $stSql .= "    item.cod_item = catalogo_item.cod_item and           \n";
        $stSql .= "    item.cod_tipo = tipo_item.cod_tipo and               \n";
        $stSql .= "    catalogo_item.cod_tipo = tipo_item_alm.cod_tipo      \n";

        return $stSql;

    }

    public function recuperaCombustivelCatalogo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaCombustivelCatalogo().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaCombustivelCatalogo()
    {
      $stSql  = "               SELECT                       \n";
      $stSql .= "            ci.cod_item                     \n";
      $stSql .= "           ,ci.descricao as combustivel     \n";
      $stSql .= "       FROM                                 \n";
      $stSql .= "           frota.tipo_item as ti            \n";
      $stSql .= "          ,frota.item as i                  \n";
      $stSql .= "          ,almoxarifado.catalogo_item as ci \n";
      $stSql .= "       where                                \n";
      $stSql .= "               i.cod_tipo = ti.cod_tipo     \n";
      $stSql .= "           AND i.cod_item = ci.cod_item     \n";
      $stSql .= "           AND ti.cod_tipo = 1              \n";
      if ($this->getDado('inCodigo'))
        $stSql .= "           AND ci.cod_item = ".$this->getDado('inCodigo')."\n";
      $stSql .= "       order by                             \n";

        return $stSql;

    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT item.cod_item
                 , catalogo_item.descricao
                 , item.cod_tipo
                 , tipo_item.descricao AS nom_tipo
                 , combustivel_item.cod_combustivel
              FROM frota.item
        INNER JOIN almoxarifado.catalogo_item
                ON catalogo_item.cod_item = item.cod_item
        INNER JOIN frota.tipo_item
                ON tipo_item.cod_tipo = item.cod_tipo
         LEFT JOIN frota.combustivel_item
                ON combustivel_item.cod_item = item.cod_item
             WHERE ";
        if ( $this->getDado( 'cod_item' ) != '' ) {
            $stSql .= " item.cod_item = ".$this->getDado( 'cod_item' )." AND   ";
        }

        return substr( $stSql,0,-6 );
    }

    public function recuperaPermissaoExcluirItem(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaPermissaoExcluirItem",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaPermissaoExcluirItem()
    {
            $stSql = "Select 1 from frota.manutencao_item";

            return $stSql;
    }

    public function recuperaPermissaoAlterarItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaPermissaoAlterarItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaPermissaoAlterarItem()
    {
        $stSql = "
            SELECT CASE WHEN COUNT(*) > 0
                      THEN 'false'
                      ELSE 'true'
                   END as permissao
              FROM frota.item
             WHERE 1=1

               AND (EXISTS ( SELECT 1
                              FROM frota.manutencao_item
                             WHERE manutencao_item.cod_item = item.cod_item
                          )

                    OR EXISTS ( SELECT 1
                                FROM frota.autorizacao
                                WHERE autorizacao.cod_item = item.cod_item
                              )
                    )";

        if ( $this->getDado( 'cod_item' ) != '' ) {
            $stSql .= " AND item.cod_item = ".$this->getDado( 'cod_item' );
        }

        return $stSql;
    }

}

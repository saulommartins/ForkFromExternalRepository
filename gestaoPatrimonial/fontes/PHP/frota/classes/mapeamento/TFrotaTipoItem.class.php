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

    $Revision: 27758 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-28 07:15:55 -0200 (Seg, 28 Jan 2008) $

    Caso de uso: uc-03.02.10
**/

/*
$Log$
Revision 1.3  2006/07/06 13:57:42  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:11:17  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaTipoItem extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TFrotaTipoItem()
    {
        parent::Persistente();
        $this->setTabela('frota.tipo_item');
        $this->setCampoCod('cod_tipo');
        $this->setComplementoChave('');
        $this->AddCampo('cod_tipo','integer',true,'',true,false);
        $this->AddCampo('descricao','varchar',true,'"40"',false,false);
    }

    public function recuperaTipoItem(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTipoItem().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTipoItem()
    {
        $stSql  = "select                       \n";
        $stSql .= "    *                        \n";
        $stSql .= "from                         \n";
        $stSql .= "    frota.tipo_item          \n";

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
      $stSql .= "       order by                             \n";
      $stSql .= "           ci.descricao                     \n";

      return $stSql;

    }
}

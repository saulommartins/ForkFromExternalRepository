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
    * Classe de mapeamento da tabela ALMOXARIFADO.PERECIVEL
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 23071 $
    $Name$
    $Author: diego $
    $Date: 2007-06-04 22:47:48 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso: uc-03.03.11
*/

/*
$Log$
Revision 1.11  2007/06/05 01:47:48  diego
Bug #7873#

Revision 1.10  2007/01/15 16:31:37  leandro.zis
adicionado as chaves estrangeiras no mapeamento

Revision 1.9  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.PERECIVEL
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoPerecivel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoPerecivel()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.perecivel');

    $this->setCampoCod('');
    $this->setComplementoChave('lote,cod_item,cod_marca,cod_almoxarifado,cod_centro');

    $this->AddCampo('lote','varchar',true,'40',true,false);
    $this->AddCampo('cod_item','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_marca','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('cod_centro','integer',true,'',true,'TAlmoxarifadoEstoqueMaterial');
    $this->AddCampo('dt_fabricacao','date',true,'',false,false);
    $this->AddCampo('dt_validade','date',true,'',false,false);

}

function recuperaSaldoLote(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoLote().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSaldoLote()
{
    $stSql .= " SELECT                                                                \n";
    $stSql .= "     sum(alm.quantidade) as saldo_lote                                 \n";
    $stSql .= " FROM                                                                  \n";
    $stSql .= "     almoxarifado.lancamento_material as alm                           \n";
    $stSql .= " JOIN                                                                  \n";
    $stSql .= "     almoxarifado.lancamento_perecivel as alp                          \n";
    $stSql .= " ON (                                                                  \n";
    $stSql .= "     alm.cod_lancamento = alp.cod_lancamento and                       \n";
    $stSql .= "     alm.cod_item = alp.cod_item and                                   \n";
    $stSql .= "     alm.cod_marca = alp.cod_marca and                                 \n";
    $stSql .= "     alm.cod_almoxarifado = alp.cod_almoxarifado and                   \n";
    $stSql .= "     alm.cod_centro = alp.cod_centro )                                 \n";
    $stSql .= "LEFT JOIN almoxarifado.transferencia_almoxarifado_item tai             \n";
    $stSql .= "     ON alm.cod_lancamento = tai.cod_lancamento                        \n";
    $stSql .= "    AND alm.cod_item = tai.cod_item                                    \n";
    $stSql .= "    AND alm.cod_centro = tai.cod_centro                                \n";
    $stSql .= "    AND alm.cod_marca = tai.cod_marca                                  \n";
    $stSql .= "    AND alm.cod_almoxarifado = tai.cod_almoxarifado                    \n";
    $stSql .= " WHERE                                                                 \n";
    $stSql .= "     alm.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and \n";
    $stSql .= "     alp.lote = '".$this->getDado('lote')."' and                       \n";
    $stSql .= "     alm.cod_item  = ".$this->getDado('cod_item')." and                \n";
    $stSql .= "     alm.cod_marca = ".$this->getDado('cod_marca')." and               \n";
    $stSql .= "     alm.cod_centro = ".$this->getDado('cod_centro')."                 \n";

    return $stSql;
}

function montaRecuperaPereciveis()
{
        $stSql .= " SELECT perecivel.lote
                          ,to_char(perecivel.dt_fabricacao, 'dd/mm/yyyy') as dt_fabricacao
                          ,to_char(perecivel.dt_validade, 'dd/mm/yyyy') as dt_validade
         FROM
             almoxarifado.perecivel
     left join
    (SELECT
        sum(alm.quantidade) as saldo_lote
       ,alp.cod_almoxarifado
       ,alp.cod_item
       ,alp.cod_marca
       ,alp.cod_centro
       ,alp.lote
    FROM
        almoxarifado.lancamento_material as alm
    JOIN
        almoxarifado.lancamento_perecivel as alp
    ON
        alm.cod_lancamento = alp.cod_lancamento and
        alm.cod_item = alp.cod_item and
        alm.cod_marca = alp.cod_marca and
        alm.cod_almoxarifado = alp.cod_almoxarifado and
        alm.cod_centro = alp.cod_centro
    group by alp.cod_almoxarifado ,alp.cod_item ,alp.cod_marca,alp.cod_centro ,alp.lote
    )as saldo_lote
    on
        saldo_lote.cod_almoxarifado = perecivel.cod_almoxarifado
    and saldo_lote.cod_item = perecivel.cod_item
    and saldo_lote.cod_marca = perecivel.cod_marca
    and saldo_lote.cod_centro = perecivel.cod_centro
    and saldo_lote.lote = perecivel.lote

         WHERE
    --         saldo_lote.saldo_lote > 0 and
             perecivel.cod_almoxarifado = ".$this->getDado('cod_almoxarifado')." and
             perecivel.cod_item  = ".$this->getDado('cod_item')." and
             perecivel.cod_marca = ".$this->getDado('cod_marca')." and
             perecivel.cod_centro = ".$this->getDado('cod_centro')."
         ";

    return $stSql;
}

function recuperaPereciveis(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
   return $this->executaRecupera("montaRecuperaPereciveis",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

}

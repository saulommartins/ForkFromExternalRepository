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
    * Classe de mapeamento da tabela ALMOXARIFADO.ATRIBUTO_ESTOQUE_MATERIAL_VALOR
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 24053 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-07-17 11:50:28 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-03.03.10
                    uc-03.03.14
*/

/*
$Log$
Revision 1.11  2007/07/17 14:50:28  hboaventura
Bug#9160#

Revision 1.10  2007/05/24 14:48:37  rodrigo
Bug #9160#

Revision 1.9  2007/05/11 13:28:56  rodrigo
Bug #9160#

Revision 1.8  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );
/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ATRIBUTO_ESTOQUE_MATERIAL_VALOR
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoAtributoEstoqueMaterialValor extends Persistente{ //AtributosValores{

/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoAtributoEstoqueMaterialValor()
{
  //parent::PersistenteAtributosValores();

    parent::Persistente();
    $this->setTabela('almoxarifado.atributo_estoque_material_valor');
  //$this->setPersistenteAtributo ( new TAlmoxarifadoAtributoCatalogoItem );

    $this->setCampoCod('');
    $this->setComplementoChave('cod_modulo,cod_cadastro,cod_atributo,cod_item,cod_centro,cod_marca,cod_almoxarifado');

    $this->AddCampo('cod_modulo','integer',true,'',true,true);
    $this->AddCampo('cod_cadastro','integer',true,'',true,true);
    $this->AddCampo('cod_atributo','integer',true,'',true,true);
    $this->AddCampo('cod_item','integer',true,'',true,true);
    $this->AddCampo('cod_centro','integer',true,'',true,true);
    $this->AddCampo('cod_marca','integer',true,'',true,true);
    $this->AddCampo('cod_lancamento','integer',true,'',true,true);
    $this->AddCampo('cod_almoxarifado','integer',true,'',true,true);
    //$this->AddCampo('timestamp','timestamp',false,'',false,false);
    $this->AddCampo('valor','varchar',true,'1500',false,false);

}

function recuperaSaldo(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
{
    return $this->executaRecupera("montaRecuperaSaldo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaSaldo()
{
    $stSQL  = "
               SELECT   ates.cod_lancamento
                       ,ates.cod_item
                       ,ates.cod_marca
                       ,ates.cod_almoxarifado
                       ,ates.cod_centro
                       ,publico.concatenar_hifen(cod_atributo) as atributos
                       ,publico.concatenar_hifen(valor)        as valores
                       ,( SELECT   quantidade
                           FROM    almoxarifado.lancamento_material as lanca
                           WHERE   lanca.cod_lancamento    = ates.cod_lancamento
                           AND     lanca.cod_item          = ates.cod_item
                           AND     lanca.cod_marca         = ates.cod_marca
                           AND     lanca.cod_almoxarifado  = ates.cod_almoxarifado
                           AND     lanca.cod_centro        = ates.cod_centro
                       ) as qtd
               FROM    almoxarifado.atributo_estoque_material_valor as ates
               WHERE cod_almoxarifado = ".$this->getDado('cod_almoxarifado')."
                 AND cod_item = ".$this->getDado('cod_item')."
                 AND cod_centro = ".$this->getDado('cod_centro')."
                 AND cod_marca = ".$this->getDado('cod_marca')."
               GROUP BY ates.cod_lancamento
                       ,ates.cod_item
                       ,ates.cod_marca
                       ,ates.cod_almoxarifado
                       ,ates.cod_centro
               HAVING   publico.equal_array( publico.concatenar_array(cod_atributo)
               ,array[".$this->getDado('cod_atributos')."] )
                  AND   publico.equal_array( publico.concatenar_array(valor)
               ,array[".$this->getDado('valor_atributos')."]::varchar[] )
               ORDER BY ates.cod_lancamento
                       ,ates.cod_item
                       ,ates.cod_marca
                       ,ates.cod_almoxarifado
                       ,ates.cod_centro";

    return $stSQL;
}

function recuperaSaldoDinamico(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
{
    return $this->executaRecupera("montaRecuperaSaldoDinamico",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaSaldoDinamico()
{
    $stSQL  = "
               SELECT
                      (
                          SELECT  SUM(quantidade)
                            FROM  almoxarifado.lancamento_material
                           WHERE  lancamento_material.cod_lancamento    = atributo_estoque_material_valor.cod_lancamento
                             AND  lancamento_material.cod_item          = atributo_estoque_material_valor.cod_item
                             AND  lancamento_material.cod_marca         = atributo_estoque_material_valor.cod_marca
                             AND  lancamento_material.cod_almoxarifado  = atributo_estoque_material_valor.cod_almoxarifado
                             AND  lancamento_material.cod_centro        = atributo_estoque_material_valor.cod_centro
                      ) as qtd

              FROM  almoxarifado.atributo_estoque_material_valor

             WHERE  cod_almoxarifado = ".$this->getDado('cod_almoxarifado')."
               AND  cod_item         = ".$this->getDado('cod_item')." ";

  if ($this->getDado('cod_centro')) {
    $stSQL  .= "AND  cod_centro       = ".$this->getDado('cod_centro')." ";
  }

    $stSQL  .= "AND  cod_marca        = ".$this->getDado('cod_marca')." ";

  if ($this->getDado('cod_lancamento')) {
    $stSQL  .= "  AND  cod_lancamento IN (".$this->getDado('cod_lancamento').") ";
  }

  $stSQL  .= "GROUP BY  cod_lancamento
                  , cod_item
                  , cod_marca
                  , cod_almoxarifado
                  , cod_centro

          ORDER BY  cod_lancamento
                  , cod_item
                  , cod_marca
                  , cod_almoxarifado
                  , cod_centro";

    return $stSQL;
}

function recuperaValoresAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaValoresAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaValoresAtributo()
{
    $stSql = " SELECT  cod_atributo, valor
                 FROM  almoxarifado.atributo_estoque_material_valor ";

    if ( $this->getDado('cod_item') ) {
        $stFiltro  = " AND cod_item = ".$this->getDado('cod_item');
    }

    if ( $this->getDado('cod_atributo') ) {
        $stFiltro .= " AND cod_atributo = ".$this->getDado('cod_atributo');
    }

    if ( $this->getDado('cod_marca') ) {
        $stFiltro .= " AND cod_marca = ".$this->getDado('cod_marca');
    }

    if ( $this->getDado('cod_centro') ) {
        $stFiltro .= " AND cod_centro = ".$this->getDado('cod_centro');
    }

    if ( $this->getDado('cod_lancamento') ) {
        $stFiltro .= " AND cod_lancamento IN (".$this->getDado('cod_lancamento').")";
    }

    if ( $this->getDado('cod_almoxarifado') ) {
        $stFiltro .= " AND cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
    }

    if ( $this->getDado('outros_valores') ) {
        $stFiltro .= " AND cod_lancamento IN ( SELECT cod_lancamento
                                                from almoxarifado.atributo_estoque_material_valor";
        $stFiltroFor = "";
        $arValorAtributo = $this->getDado('outros_valores');
        for ($i=0; $i<=count($arValorAtributo) - 1; $i++ ) {
            $stFiltroFor .= " or (valor = '".$arValorAtributo[$i]['valor']."'
                              and cod_atributo = ".$arValorAtributo[$i]['cod_atributo']."
                              and cod_cadastro = 2
                              and cod_modulo = 29
                              )";
        }
        $stFiltro .= " WHERE ".substr($stFiltroFor, 3)." )";

    }
    $stFiltro = " WHERE ".substr($stFiltro, 4);

    $stGroupBy = " GROUP BY cod_atributo, valor";

    return $stSql.$stFiltro.$stGroupBy;
}

function recuperaLancamentoValoresAtributo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaLancamentoValoresAtributo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaLancamentoValoresAtributo()
{
    $stSql = " SELECT DISTINCT cod_lancamento
                 FROM almoxarifado.atributo_estoque_material_valor";

    if ( $this->getDado('cod_item') ) {
        $stFiltro  = " AND cod_item = ".$this->getDado('cod_item');
    }

    if ( $this->getDado('cod_atributo') ) {
        $stFiltro .= " AND cod_atributo IN (".$this->getDado('cod_atributo').")";
    }

    if ( $this->getDado('valor') ) {
        $stFiltro .= " AND valor IN (".$this->getDado('valor').")";
    }

    if ( $this->getDado('cod_marca') ) {
        $stFiltro .= " AND cod_marca = ".$this->getDado('cod_marca');
    }

    if ( $this->getDado('cod_centro') ) {
        $stFiltro .= " AND cod_centro = ".$this->getDado('cod_centro');
    }

    if ( $this->getDado('cod_modulo') ) {
        $stFiltro .= " AND cod_modulo = ".$this->getDado('cod_modulo');
    }

    if ( $this->getDado('cod_cadastro') ) {
        $stFiltro .= " AND cod_cadastro = ".$this->getDado('cod_cadastro');
    }

    if ( $this->getDado('cod_almoxarifado') ) {
        $stFiltro .= " AND cod_almoxarifado = ".$this->getDado('cod_almoxarifado');
    }

    $stFiltro = " where ".substr($stFiltro, 4);

    return $stSql.$stFiltro;
}

}

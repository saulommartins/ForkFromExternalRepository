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
    * Classe de mapeamento da tabela folhapagamento.valor_diversos
    * Data de Criação: 31/10/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.00.00

    $Id: TFolhaPagamentoValorDiversos.class.php 65613 2016-06-02 11:48:59Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.valor_diversos
  * Data de Criação: 31/10/2007

  * @copyright CNM Confederação Nacional de Municípios
  * @link http://www.cnm.org.br CNM Confederação Nacional de Municípios

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoValorDiversos extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoValorDiversos()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.valor_diversos");

    $this->setCampoCod('cod_valor');
    $this->setComplementoChave('timestamp');

    $this->AddCampo('cod_valor','integer'      ,true  ,''    ,true,false);
    $this->AddCampo('timestamp','timestamp_now',true  ,''    ,true,false);
    $this->AddCampo('descricao','varchar'      ,true  ,'60'  ,false,false);
    $this->AddCampo('valor'    ,'numeric'      ,true  ,'16,4',false,false);
    $this->AddCampo('ativo'    ,'boolean'      ,true  ,''    ,false,false);
    $this->AddCampo('data_vigencia','date'     ,true  ,''    ,true,false);
}

function montaRecuperaRelacionamento()
{
    $stSql.= "SELECT valor_diversos.cod_valor                                             \n";
    $stSql.= "     , valor_diversos.descricao                                             \n";
    $stSql.= "     , REPLACE((valor_diversos.valor::varchar),'.',',') as valor            \n";
    $stSql.= "     , TO_CHAR(valor_diversos.data_vigencia, 'dd/mm/yyyy') as data_vigencia \n";
    $stSql.= "  FROM folhapagamento.valor_diversos                                        \n";
    $stSql.= "     , (  SELECT cod_valor                                                  \n";    
    $stSql.= "               , max(timestamp) as timestamp                                \n";
    $stSql.= "            FROM folhapagamento.valor_diversos                              \n";
    $stSql.= "        GROUP BY cod_valor ) as max_valor_diversos                          \n";
    $stSql.= " WHERE max_valor_diversos.cod_valor = valor_diversos.cod_valor              \n";    
    $stSql.= "   AND max_valor_diversos.timestamp = valor_diversos.timestamp              \n";

    return $stSql;
}

}
?>

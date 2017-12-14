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
    * Classe de mapeamento da tabela ima.consignacao_banrisul_liquido
    * Data de Criação: 09/06/2008

    * @author Alex Cardoso

    $Id: $

    * Casos de uso: uc-04.08.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.consignacao_banrisul_liquido
  * Data de Criação: 09/10/2007

    * @author Alex Cardoso
*/
class TIMAConsignacaoBanrisulLiquido extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TIMAConsignacaoBanrisulLiquido()
{
    parent::Persistente();
    $this->setTabela("ima.consignacao_banrisul_liquido");

    $this->setCampoCod('cod_evento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_evento'        ,'integer' ,true  ,''    ,true,'TFolhaPagamentoEvento');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT consignacao_banrisul_liquido.*                               \n";
    $stSql .= "     , evento.*                                                     \n";
    $stSql .= "  FROM ima.consignacao_banrisul_liquido   \n";
    $stSql .= "     , folhapagamento.evento                                        \n";
    $stSql .= " WHERE consignacao_banrisul_liquido.cod_evento = evento.cod_evento  \n";

    return $stSql;
}

}
?>

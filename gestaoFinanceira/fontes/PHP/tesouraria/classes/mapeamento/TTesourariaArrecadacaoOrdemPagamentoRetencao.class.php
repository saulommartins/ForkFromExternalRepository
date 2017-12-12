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
    * Classe de mapeamento da tabela tesouraria.arrecadacao_ordem_pagamento_retencao
    * Data de Criação: 04/07/2007

    * @author Analista: Gelson W.
    * @author Desenvolvedor: Anderson cAko Konze

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2007-07-13 16:18:44 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.04.05
*/
/*
$Log$
Revision 1.1  2007/07/13 19:12:41  cako
Bug#9383#, Bug#9384#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaArrecadacaoOrdemPagamentoRetencao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaArrecadacaoOrdemPagamentoRetencao()
{
    parent::Persistente();
    $this->setTabela("tesouraria.arrecadacao_ordem_pagamento_retencao");

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp_arrecadacao,exercicio,cod_arrecadacao,cod_plano,cod_ordem,cod_entidade,sequencial');

    $this->AddCampo('timestamp_arrecadacao','timestamp',true  ,''   ,true,'TTesourariaArrecadacao');
    $this->AddCampo('exercicio'            ,'char'     ,true  ,'4'  ,true,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('cod_arrecadacao'      ,'integer'  ,true  ,''   ,true,'TTesourariaArrecadacao');
    $this->AddCampo('cod_plano'            ,'integer'  ,true  ,''   ,true,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('cod_ordem'            ,'integer'  ,true  ,''   ,true,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('cod_entidade'         ,'integer'  ,true  ,''   ,true,'TEmpenhoOrdemPagamentoRetencao');
    $this->AddCampo('sequencial'           ,'integer'  ,true  ,''   ,true,'TEmpenhoOrdemPagamentoRetencao');

}
}
?>

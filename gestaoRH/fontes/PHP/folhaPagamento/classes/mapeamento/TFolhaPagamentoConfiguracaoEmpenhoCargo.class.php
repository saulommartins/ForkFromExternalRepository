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
    * Classe de mapeamento da tabela folhapagamento.configuracao_empenho_cargo
    * Data de Criação: 08/08/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Evandro Melos

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31015 $
    $Name $
    $Id $    
    $Date: $

*/
include_once ( CLA_PERSISTENTE );

class TFolhaPagamentoConfiguracaoEmpenhoCargo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.configuracao_empenho_cargo");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_configuracao,exercicio,cod_sub_divisao,sequencia,timestamp,cod_cargo');

    $this->AddCampo('cod_configuracao','integer'  ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenhoSubDivisao');
    $this->AddCampo('exercicio'       ,'char'     ,true  ,'4'  ,true,'TFolhaPagamentoConfiguracaoEmpenhoSubDivisao');
    $this->AddCampo('sequencia'       ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenhoSubDivisao');
    $this->AddCampo('timestamp'       ,'timestamp',true  ,''   ,true,'TFolhaPagamentoConfiguracaoEmpenhoSubDivisao');
    $this->AddCampo('cod_sub_divisao' ,'integer'  ,true  ,''   ,true,'TPessoalSubDivisao');
    $this->AddCampo('cod_cargo'       ,'integer'  ,true  ,''   ,true,'TPessoalCargo');

}
}
?>

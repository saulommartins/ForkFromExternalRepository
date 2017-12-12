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
    * Classe de mapeamento da tabela diarias.diaria_empenho
    * Data de Criação: 03/09/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.05.62

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TDiariasDiariaEmpenho extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDiariasDiariaEmpenho()
{
    parent::Persistente();
    $this->setTabela("diarias.diaria_empenho");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_diaria,cod_contrato,timestamp');

    $this->AddCampo('cod_diaria'     ,'integer'  ,true  ,''   ,true,'TDiariasDiaria');
    $this->AddCampo('cod_contrato'   ,'integer'  ,true  ,''   ,true,'TDiariasDiaria');
    $this->AddCampo('timestamp'      ,'timestamp',true  ,''   ,true,'TDiariasDiaria');
    $this->AddCampo('exercicio'      ,'char'     ,true  ,'4'  ,false,'TEmpenhoAutorizacaoEmpenho');
    $this->AddCampo('cod_entidade'   ,'integer'  ,true  ,''   ,false,'TEmpenhoAutorizacaoEmpenho');
    $this->AddCampo('cod_autorizacao','integer'  ,true  ,''   ,false,'TEmpenhoAutorizacaoEmpenho');

}
}
?>
